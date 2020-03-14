<?php

namespace kallbuloso\Karl\Builder\Schema;

use kallbuloso\Karl\Helpers\ProgressBar;

trait MakeSchemaTrait
{
    use ProgressBar;

    /**
     * Placeholders.
     * @var array
     */
    protected $path = null;

    /**
     * Replacements.
     * @var array
     */
    protected $replacements = null;

    protected function makeSchema()
    {
        $path = app_path('Providers\\AppServiceProvider.php');
        $setReplacement =
'<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        //
    }
}';
        if (file_exists($path)) {
            $file = file_get_contents($path);
            $file = str_replace($file, '', $file);

            file_put_contents($path, $setReplacement);
        }
    }
}
