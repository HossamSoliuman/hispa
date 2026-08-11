<?php

namespace App\Providers;

use App\Broadcasting\FirebaseChannel;
use App\Models\Page;
use App\Models\Setting;
use App\Observers\PageObserver;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Page::observe(PageObserver::class);

        $this->app->scoped('platform.settings', function (): array {
            try {
                return Schema::hasTable('settings')
                    ? Setting::all()->pluck('value', 'key')->toArray()
                    : [];
            } catch (\Exception) {
                return [];
            }
        });

        $this->app->scoped('platform.branding', function (): array {
            return $this->resolveBranding(app('platform.settings'));
        });

        // Bind the SupportServiceManager so it can be resolved via the container
        $this->app->singleton(\App\Services\SupportServiceManager::class, function ($app) {
            return new \App\Services\SupportServiceManager;
        });
    }

    /**
     * Resolve every platform branding asset once per request.
     *
     * The three assets are managed separately because they sit on different
     * surfaces: `logo` on light backgrounds, `logo_dark` on the blue/navy app
     * headers, and `favicon` in the browser tab. The dark variant falls back to
     * the main logo so a single upload still brands the whole application.
     *
     * @param  array<string, mixed>  $settings
     * @return array{platformLogoUrl: string, platformLogoOnDarkUrl: string, platformFaviconUrl: string, platformAppleTouchIconUrl: string, platformLogoSeoPath: string}
     */
    protected function resolveBranding(array $settings): array
    {
        $path = fn (string $key): ?string => filled($settings[$key] ?? null)
            ? (string) $settings[$key]
            : null;

        $url = fn (?string $value): ?string => $value === null
            ? null
            : (Str::startsWith($value, ['http://', 'https://', '//']) ? $value : asset($value));

        $logo = $path('logo');
        $logoDark = $path('logo_dark');
        $favicon = $path('favicon');

        return [
            'platformLogoUrl' => $url($logo) ?? asset('site/assets/hisbah-huwat-logo.png'),
            'platformLogoOnDarkUrl' => $url($logoDark) ?? $url($logo) ?? asset('site/assets/hisbah-huwat-logo-white.png'),
            'platformFaviconUrl' => $url($favicon) ?? asset('site/assets/hisbah-huwat-favicon.png'),
            'platformAppleTouchIconUrl' => $url($favicon) ?? asset('site/assets/hisbah-huwat-apple-touch-icon.png'),
            'platformLogoSeoPath' => $logo ?? config('seo.default_image_path'),
        ];
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

        View::composer('*', function ($view): void {
            $data = $view->getData();

            if (! array_key_exists('settings', $data)) {
                $view->with('settings', app('platform.settings'));
            }

            foreach (app('platform.branding') as $key => $value) {
                if (! array_key_exists($key, $data)) {
                    $view->with($key, $value);
                }
            }
        });

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
