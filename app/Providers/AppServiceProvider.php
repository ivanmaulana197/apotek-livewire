<?php
namespace App\Providers;

use App\Models\Barang\Barang;
use App\Observers\BarangObserver;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        URL::forceHttps($this->app->environment(['production', 'staging', 'local']));
        Blade::directive('nominal', function ($expression) {
            // Logika pemformatan (menggunakan helper function atau logika langsung)
            [$angka, $optional] = array_pad(explode(',', $expression), 2, null);
            if ($angka == null) {
                return "<?php echo $optional; ?>";
            }
            return "<?php echo number_format($angka, 0, ',', '.'); ?>";
        });

        Blade::directive('decimal', function ($expression) {

            [$angka, $optional] = array_pad(explode(',', $expression), 2, null);
            if ($angka == null) {
                return "<?php echo $optional; ?>";
            }
            return "<?php echo number_format($angka, 2, ',', '.'); ?>";
        });

        Barang::observe(BarangObserver::class);

    }
}
