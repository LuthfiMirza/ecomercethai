<?php

namespace App\Providers;

use App\Support\MegaMenuBuilder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->normalizeDompdfFontPaths();

        View::composer('components.navbar', function ($view) {
            $categories = [];

            try {
                $categories = MegaMenuBuilder::build();
            } catch (Throwable $exception) {
                report($exception);
                $categories = [];
            }

            $view->with('navMegaCategories', $categories);
        });
    }

    /**
     * Dompdf stores absolute paths inside storage/fonts/installed-fonts.json.
     * When the project is moved to another machine those paths become invalid
     * which makes Thai glyphs disappear in generated PDFs. Normalize the paths
     * so Dompdf can resolve the files on every environment.
     */
    private function normalizeDompdfFontPaths(): void
    {
        $fontConfig = storage_path('fonts/installed-fonts.json');

        if (! File::exists($fontConfig)) {
            return;
        }

        $fonts = json_decode(File::get($fontConfig), true);

        if (! is_array($fonts)) {
            return;
        }

        $updated = false;

        foreach ($fonts as $family => $variants) {
            if (! is_array($variants)) {
                continue;
            }

            foreach ($variants as $variant => $path) {
                if (! is_string($path)) {
                    continue;
                }

                $basename = basename($path);
                if ($basename !== $path) {
                    $fonts[$family][$variant] = $basename;
                    $updated = true;
                }
            }
        }

        if ($updated) {
            File::put(
                $fontConfig,
                json_encode($fonts, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            );
        }
    }
}
