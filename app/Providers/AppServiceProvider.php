<?php

namespace App\Providers;

use App\Broadcasting\FirebaseChannel;
use App\Models\Page;
use App\Models\Setting;
use App\Observers\PageObserver;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Page::observe(PageObserver::class);

        // Bind the SupportServiceManager so it can be resolved via the container
        $this->app->singleton(\App\Services\SupportServiceManager::class, function ($app) {
            return new \App\Services\SupportServiceManager;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (App::environment(['production', 'development'])) {
            URL::forceScheme('https');
            app('url')->forceScheme('https');
        }

        $this->app->make(ChannelManager::class)->extend('firebase', function ($app) {
            return new FirebaseChannel;
        });
        Schema::defaultStringLength(191);
        // Avoid database queries during composer scripts, migrations or when the
        // settings table does not exist yet (fresh install or during migrations).
        try {
            $settings = Cache::rememberForever('site_settings', function () {
                return Schema::hasTable('settings')
                    ? Setting::all()->pluck('value', 'key')->toArray()
                    : [];
            });

            View::share('settings', $settings);
        } catch (\Exception $e) {
            View::share('settings', []);
        }

        // Blade directive to format currency consistently across views.
        // Usage in Blade templates: @money($amount)
        Blade::directive('money', function ($expression) {
            return "<?php echo number_format($expression, 2); ?>";
        });

        // Safe display of values that may be string or array (e.g. JSON/translated attributes).
        // Usage: @displaySafe($model->attribute) or @displaySafe($model->attribute, 'N/A')
        Blade::directive('displaySafe', function ($expression) {
            return "<?php echo e(display_string($expression)); ?>";
        });

        // Blade directive to format a Date/DateTime as Hijri (Islamic) calendar when possible.
        // Usage: @hijri($date)
        Blade::directive('hijri', function ($expression) {
            // Use a small generated closure to isolate logic and inject the user expression
            // directly into the call. This avoids accidental variable interpolation when
            // building the returned PHP string.
            return '<?php echo (function($__dt){'.
                ' try { '.
                    ' if (! $__dt) return ""; '.
                    ' if (is_string($__dt)) { $__date = new DateTime($__dt); } '.
                    ' elseif ($__dt instanceof DateTimeInterface) { $__date = new DateTime($__dt->format("Y-m-d H:i:s")); } '.
                    ' elseif (is_object($__dt) && method_exists($__dt, "format")) { $__date = new DateTime($__dt->format("Y-m-d H:i:s")); } '.
                    ' else { $__date = new DateTime($__dt); } '.
                    ' if (class_exists("IntlDateFormatter")) { '.
                        ' $fmt = new IntlDateFormatter(app()->getLocale() === "ar" ? "ar_SA@calendar=islamic" : "en_US@calendar=islamic", IntlDateFormatter::MEDIUM, IntlDateFormatter::NONE, null, IntlDateFormatter::TRADITIONAL, "d MMM yyyy"); '.
                        ' $out = $fmt->format($__date); '.
                        ' return $out !== false ? $out : $__date->format("Y-m-d"); '.
                    ' } else { return $__date->format("Y-m-d"); } '.
                ' } catch (Exception $e) { return ""; } '.
            ' })('.$expression.'); ?>';
        });
    }
}
