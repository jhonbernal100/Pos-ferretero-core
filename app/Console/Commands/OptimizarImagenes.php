<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Producto;
use Illuminate\Support\Facades\Storage;

class OptimizarImagenes extends Command
{
    protected $signature   = 'imagenes:optimizar {--tenant= : ID del tenant específico}';
    protected $description = 'Optimiza todas las imágenes de productos existentes a 400px y 75% calidad';

    public function handle()
    {
        $query = Producto::whereNotNull('foto')->where('activo', true);

        if ($this->option('tenant')) {
            $query->where('tenant_id', $this->option('tenant'));
        }

        $productos = $query->get();
        $total     = $productos->count();

        if ($total === 0) {
            $this->info('No hay productos con fotos para optimizar.');
            return;
        }

        $this->info("Optimizando {$total} imágenes...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $optimizadas = 0;
        $errores     = 0;
        $ahorroTotal = 0;

        foreach ($productos as $producto) {
            try {
                $path = 'public/' . $producto->foto;

                if (!Storage::exists($path)) {
                    $bar->advance();
                    continue;
                }

                $contenido = Storage::get($path);
                $pesoAntes = strlen($contenido);

                // Crear imagen desde contenido
                $img = @imagecreatefromstring($contenido);

                if ($img === false) {
                    $errores++;
                    $bar->advance();
                    continue;
                }

                $anchoOriginal = imagesx($img);
                $altoOriginal  = imagesy($img);
                $maxSize       = 400;

                if ($anchoOriginal > $maxSize || $altoOriginal > $maxSize) {
                    $ratio      = min($maxSize / $anchoOriginal, $maxSize / $altoOriginal);
                    $nuevoAncho = (int)($anchoOriginal * $ratio);
                    $nuevoAlto  = (int)($altoOriginal  * $ratio);

                    $imgRedim = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
                    $blanco   = imagecolorallocate($imgRedim, 255, 255, 255);
                    imagefill($imgRedim, 0, 0, $blanco);

                    imagecopyresampled(
                        $imgRedim, $img,
                        0, 0, 0, 0,
                        $nuevoAncho, $nuevoAlto,
                        $anchoOriginal, $altoOriginal
                    );

                    ob_start();
                    imagejpeg($imgRedim, null, 75);
                    $imagenOptimizada = ob_get_clean();

                    imagedestroy($img);
                    imagedestroy($imgRedim);
                } else {
                    // Ya es pequeña pero recomprimimos a 75%
                    ob_start();
                    imagejpeg($img, null, 75);
                    $imagenOptimizada = ob_get_clean();
                    imagedestroy($img);
                }

                $pesoDespues = strlen($imagenOptimizada);
                $ahorro      = $pesoAntes - $pesoDespues;
                $ahorroTotal += max(0, $ahorro);

                // Sobreescribir imagen optimizada
                Storage::put($path, $imagenOptimizada);

                $optimizadas++;

            } catch (\Exception $e) {
                $errores++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Completado:");
        $this->info("  Optimizadas: {$optimizadas}");
        $this->info("  Errores:     {$errores}");
        $this->info("  Ahorro:      " . round($ahorroTotal / 1024 / 1024, 2) . " MB");
    }
}