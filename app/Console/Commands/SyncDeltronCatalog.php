<?php

namespace App\Console\Commands;

use App\Models\Categoria;
use App\Models\DeltronProducto;
use App\Models\Empresa;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

#[Signature('deltron:sync-catalog')]
#[Description('Sincroniza el catálogo de Deltron usando el porcentaje configurado en Empresa')]
class SyncDeltronCatalog extends Command
{
    public function handle(): int
    {
        ini_set('memory_limit', '512M');
        set_time_limit(1800);

        $baseUrl = config('services.deltron.base_url');
        $apiKey  = config('services.deltron.key');
        $secret  = config('services.deltron.secret');

        // 1. Leer configuración dinámica desde la tabla empresas
        $empresa = Empresa::first();
        $porcentaje = $empresa?->porcentaje_deltron ?? 25.00;
        $margenGanancia = $porcentaje / 100;

        $this->info("1. Autenticando con Deltron (Margen aplicado: {$porcentaje}%)...");

        try {
            $token = Cache::remember('deltron_bearer_token', 82800, function () use ($baseUrl, $apiKey, $secret) {
                $authResponse = Http::post($baseUrl . '/api/auth/token', [
                    'api_key'    => $apiKey,
                    'api_secret' => $secret,
                ]);

                if ($authResponse->failed() || ($authResponse->json('status') !== 'ok')) {
                    throw new \Exception('Error de autenticación con Deltron. HTTP: ' . $authResponse->status());
                }

                $result = $authResponse->json('result');
                if (isset($result['client_ip'])) {
                    $this->info('IP detectada por Deltron: ' . $result['client_ip']);
                }

                return $result['token'];
            });
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return 1;
        }

        $this->info('2. Descargando catálogo masivo (/api/catalogo)...');

        $catalogResponse = Http::timeout(300)
            ->withToken($token)
            ->get($baseUrl . '/api/catalogo');

        if ($catalogResponse->status() === 401) {
            Cache::forget('deltron_bearer_token');
            $this->error('Token rechazado (401). Caché limpiada, vuelve a ejecutar el comando.');
            return 1;
        }

        if ($catalogResponse->failed() || ($catalogResponse->json('status') !== 'ok')) {
            $this->error('Error al descargar el catálogo. Código HTTP: ' . $catalogResponse->status());
            return 1;
        }

        $data = $catalogResponse->json('result');
        $tipoCambio = $data['currency_change_val'] ?? 3.80;
        $items = $data['items'] ?? [];

        $this->info('3. Procesando ' . count($items) . ' productos...');

        foreach ($items as $item) {
            $nombreCat = trim($item['category_name'] ?? 'General');
            $categoria = Categoria::firstOrCreate(
                ['nombreCategoria' => $nombreCat],
                ['slug' => Str::slug($nombreCat) . '-' . Str::random(4)]
            );

            $stockTotal = 0;
            if (isset($item['stock']['almacenes']) && is_array($item['stock']['almacenes'])) {
                foreach ($item['stock']['almacenes'] as $alm) {
                    $stockTotal += $alm['cantidad'] ?? 0;
                }
            }

            $tienePromo = !empty($item['promocion_activa']) && !empty($item['price_promo']);
            $precioBaseUSD = $tienePromo ? $item['price_promo'] : ($item['price'] ?? 0);

            $precioConIgvUSD = $precioBaseUSD * 1.18;
            $precioCostoSoles = round($precioConIgvUSD * $tipoCambio, 2);
            $precioVentaFinal = round($precioCostoSoles * (1 + $margenGanancia), 2);

            $nombreProducto = $item['name'] ?? 'Producto sin nombre';

            DeltronProducto::updateOrCreate(
                ['sku' => $item['sku']],
                [
                    'nombre'                => $nombreProducto,
                    'slug'                  => Str::slug($nombreProducto . '-' . $item['sku']),
                    'precio'                => $precioVentaFinal,
                    'precio_costo_soles'    => $precioCostoSoles,
                    'precio_costo_usd'      => $precioBaseUSD,
                    'stock'                 => $stockTotal,
                    'imagen'                => $item['images']['image_listado'] ?? null,
                    'categoria_id'          => $categoria->id,
                    'marca'                 => $item['brand_name'] ?? null,
                    'especificaciones_html' => $item['specs']['specs_all_html'] ?? null,
                    'is_active'             => ($item['status'] ?? 'ACTIVO') === 'ACTIVO',
                ]
            );
        }

        $this->info('¡Sincronización completada con éxito!');
        return 0;
    }
}