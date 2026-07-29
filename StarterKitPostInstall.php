<?php

use Statamic\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\search;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class StarterKitPostInstall
{
    protected string $env = '';
    protected string $app = '';
    protected string $sites = '';
    protected bool $rapidezStatamic = false;

    /**
     * Repositories that Multisite renames by site handle.
     * File drivers derive locale from the current default site;
     * Eloquent stores the handle permanently, so Multisite must run on files first.
     */
    protected array $multisiteFileDrivers = [
        'collection_trees',
        'navigation_trees',
        'global_set_variables',
    ];

    public function handle(): void
    {
        info('🚀 Setting up your JustBetter Statamic starter kit...');
        
        $this->installDependencies();
        
        if (confirm('Do you want to setup for a Rapidez Statamic project?', false)) {
            $this->setupRapidezStatamic();
        }
        
        $this->configureProject();
        $this->setupDatabase();
        $this->setupMultisite();
        $this->importEloquentContent();
        
        info('✅ Starter kit installation completed!');
    }

    protected function installDependencies(): void
    {
        $addons = $this->selectAddons();
        
        if (!empty($addons)) {
            $this->runCommand('composer require ' . implode(' ', $addons), 'Installing selected addons...');
        }
    }

    protected function selectAddons(): array
    {
        $addons = [];

        $seoAddon = select(
            'Which SEO addon do you want to use?',
            [
                'statamic/seo-pro' => 'Statamic SEO Pro',
                'none' => 'None',
            ],
            'none'
        );

        if ($seoAddon !== 'none') {
            $addons[] = $seoAddon;
            
            if ($seoAddon === 'statamic/seo-pro') {
                $this->copySeoProTemplates();
            }
        }

        $cacheAddon = select(
            'Which cache addon do you want to use?',
            [
                'justbetter/statamic-cloudflare-purge' => 'Cloudflare Purge',
                'none' => 'None',
            ],
            'none'
        );

        if ($cacheAddon !== 'none') {
            $addons[] = $cacheAddon;
        }

        $structuredDataAddon = select(
            'Which structured data addon do you want to use?',
            [
                'justbetter/statamic-structured-data' => 'Structured Data',
                'none' => 'None',
            ],
            'none'
        );

        if ($structuredDataAddon !== 'none') {
            $addons[] = $structuredDataAddon;
        }

        return $addons;
    }

    protected function copySeoProTemplates(): void
    {
        $source = base_path('vendor/justbetter/statamic-starter-kit/export/resources/views/layouts/seo');
        $destination = base_path('resources/views/layouts/seo');
        
        if (File::exists($source)) {
            File::copyDirectory($source, $destination);
            info('📁 Copied SEO Pro templates');
        }
    }

    protected function setupRapidezStatamic(): void
    {
        $this->rapidezStatamic = true;
        
        info('⚡ Setting up Rapidez Statamic...');
        
        $this->runCommand('php artisan statamic:install', 'Installing Statamic...');
        $this->updateComposerScripts();
        $this->runCommand('php artisan vendor:publish --provider=Rapidez\Statamic\RapidezStatamicServiceProvider --tag=rapidez-user-model', 'Publishing user model...');
        $this->runCommand('php artisan vendor:publish --provider=Rapidez\Statamic\RapidezStatamicServiceProvider --tag=rapidez-statamic-content', 'Publishing Rapidez content...');
        $this->runCommand('php artisan vendor:publish --provider=Rapidez\Statamic\RapidezStatamicServiceProvider --tag=views', 'Publishing views...');
    }

    protected function updateComposerScripts(): void
    {
        $composerFile = base_path('composer.json');
        
        if (!File::exists($composerFile)) {
            return;
        }

        $composer = json_decode(File::get($composerFile), true);
        
        $composer['scripts'] ??= [];
        $composer['scripts']['post-autoload-dump'] ??= [];
        
        $statamicInstallScript = '@php artisan statamic:install --ansi';
        
        if (!in_array($statamicInstallScript, $composer['scripts']['post-autoload-dump'])) {
            $composer['scripts']['post-autoload-dump'][] = $statamicInstallScript;
            File::put($composerFile, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            info('📝 Updated composer.json with Statamic install script');
        }
    }

    protected function configureProject(): void
    {
        if ($this->rapidezStatamic || !confirm('Do you want to configure your project settings?', true)) {
            return;
        }

        info('⚙️ Configuring project settings...');
        
        $this->loadConfigFiles();
        $this->configureEnvironment();
        $this->saveConfigFiles();
        $this->reloadEnvironment();
    }

    protected function loadConfigFiles(): void
    {
        $this->env = File::get(base_path('.env.example'));
        $this->app = File::get(base_path('config/app.php'));
        $this->sites = File::get(base_path('resources/sites.yaml'));
    }

    protected function configureEnvironment(): void
    {
        $this->setAppName();
        $this->setLicenseKey();
        $this->setAppUrl();
        $this->setAppKey();
        $this->setDatabaseConfig();
        $this->setLocaleConfig();
        $this->setTimezoneConfig();
        $this->setDebugbarConfig();
        $this->setImageConfig();
        $this->setMailConfig();
        
        File::put(base_path('.env'), $this->env);
        info('✅ Environment configuration updated');
    }

    protected function saveConfigFiles(): void
    {
        File::put(base_path('config/app.php'), $this->app);
        File::put(base_path('resources/sites.yaml'), $this->sites);
    }

    protected function reloadEnvironment(): void
    {
        $this->runCommand('php artisan config:clear', 'Clearing config cache...');
        app()->bootstrapWith([LoadEnvironmentVariables::class]);
    }

    protected function setupDatabase(): void
    {
        info('🗄️ Setting up database...');
        
        $dbPath = base_path('database/database.sqlite');
        if (File::exists($dbPath)) {
            File::delete($dbPath);
        }
        
        $this->runCommand('php artisan migrate', 'Running migrations...');
    }

    protected function setupMultisite(): void
    {
        if ($this->rapidezStatamic || !confirm('Would you like to configure multisite? (Requires Statamic PRO license)', false)) {
            return;
        }

        // Multisite renames the default site handle, then looks up trees by that handle.
        // File trees adapt via Site::default(); Eloquent trees keep the old handle and break.
        $this->setDrivers($this->multisiteFileDrivers, 'file');
        $this->runCommand('php artisan config:clear');
        $this->runCommand('php artisan statamic:multisite', 'Setting up multisite...');
        $this->setDrivers($this->multisiteFileDrivers, 'eloquent');
        $this->runCommand('php artisan config:clear');
    }

    protected function importEloquentContent(): void
    {
        info('📦 Importing file content into Eloquent...');

        $this->runCommand('php artisan statamic:eloquent:import-navs --force --only-nav-trees', 'Importing navigation trees...');
        $this->runCommand('php artisan statamic:eloquent:import-collections --force --only-collection-trees', 'Importing collection trees...');
        $this->runCommand('php artisan statamic:eloquent:import-globals --only-global-variables', 'Importing global variables...');
    }

    protected function setDrivers(array $repositories, string $driver): void
    {
        $path = config_path('statamic/eloquent-driver.php');
        $contents = File::get($path);

        foreach ($repositories as $repository) {
            $contents = preg_replace(
                "/('{$repository}'\s*=>\s*\[\s*'driver'\s*=>\s*)'[^']+'/",
                "$1'{$driver}'",
                $contents,
                1
            );
        }

        File::put($path, $contents);
    }

    protected function setAppName(): void
    {
        $appName = text(
            'What should be your app name?',
            placeholder: 'Statamic',
            required: true
        );

        $appName = preg_replace('/([\'|\"#])/', '', $appName);
        $this->replaceInEnv('APP_NAME="Statamic"', "APP_NAME=\"{$appName}\"");
    }

    protected function setLicenseKey(): void
    {
        $licenseKey = text(
            'Enter your Statamic License key',
            hint: 'Leave empty to skip',
            required: false
        );

        if ($licenseKey) {
            $this->replaceInEnv('STATAMIC_LICENSE_KEY=', "STATAMIC_LICENSE_KEY=\"{$licenseKey}\"");
        }
    }

    protected function setAppUrl(): void
    {
        $appUrl = env('APP_URL', 'http://localhost');
        $this->replaceInEnv('APP_URL=', "APP_URL=\"{$appUrl}\"");
    }

    protected function setAppKey(): void
    {
        $appKey = env('APP_KEY', '');
        if ($appKey) {
            $this->replaceInEnv('APP_KEY=', "APP_KEY=\"{$appKey}\"");
        }
    }

    protected function setDatabaseConfig(): void
    {
        $databaseName = text('Database name?', required: true);
        $this->replaceInEnv('DB_DATABASE=laravel', "DB_DATABASE={$databaseName}");

        $username = text('Database username?', default: 'root', required: true);
        $this->replaceInEnv('DB_USERNAME=root', "DB_USERNAME={$username}");

        $password = text('Database password?', required: false);
        $this->replaceInEnv('DB_PASSWORD=', "DB_PASSWORD={$password}");
    }

    protected function setLocaleConfig(): void
    {
        $locale = text(
            'Default site locale?',
            default: 'nl_NL',
            required: true
        );

        $this->replaceInSites('locale: en_US', "locale: {$locale}");
    }

    protected function setTimezoneConfig(): void
    {
        $timezone = search(
            'App timezone?',
            options: function (string $value) {
                $timezones = timezone_identifiers_list(\DateTimeZone::ALL);
                
                if (!$value) {
                    return $timezones;
                }

                return collect($timezones)
                    ->filter(fn (string $tz) => Str::contains($tz, $value, true))
                    ->values()
                    ->all();
            },
            placeholder: 'UTC',
            required: true
        );

        $currentTimezone = config('app.timezone', 'UTC');
        $this->replaceInEnv("APP_TIMEZONE=\"{$currentTimezone}\"", "APP_TIMEZONE=\"{$timezone}\"");
    }

    protected function setDebugbarConfig(): void
    {
        if (!confirm('Enable debugbar?', false)) {
            $this->replaceInEnv('DEBUGBAR_ENABLED=true', 'DEBUGBAR_ENABLED=false');
        }
    }

    protected function setImageConfig(): void
    {
        if (confirm('Use Imagick for image processing? (instead of GD)', true)) {
            $this->replaceInEnv('#IMAGE_MANIPULATION_DRIVER=imagick', 'IMAGE_MANIPULATION_DRIVER=imagick');
        }
    }

    protected function setMailConfig(): void
    {
        $mailer = select(
            'Local mailer preference?',
            [
                'mailpit' => 'Mailpit',
                'mailtrap' => 'Mailtrap', 
                'helo' => 'Helo',
                'herd' => 'Herd Pro',
                'log' => 'Log only',
            ],
            'mailpit'
        );

        switch ($mailer) {
            case 'helo':
            case 'herd':
                $this->replaceInEnv('MAIL_HOST=localhost', 'MAIL_HOST=127.0.0.1');
                $this->replaceInEnv('MAIL_PORT=1025', 'MAIL_PORT=2525');
                $this->replaceInEnv('MAIL_USERNAME=null', 'MAIL_USERNAME="${APP_NAME}"');
                break;
                
            case 'log':
                $this->replaceInEnv('MAIL_MAILER=smtp', 'MAIL_MAILER=log');
                break;
                
            case 'mailtrap':
                break;
        }
    }

    protected function replaceInEnv(string $search, string $replace): void
    {
        $this->env = str_replace($search, $replace, $this->env);
    }

    protected function replaceInSites(string $search, string $replace): void
    {
        $this->sites = str_replace($search, $replace, $this->sites);
    }

    protected function runCommand(string $command, string $message = ''): void
    {
        if ($message) {
            info($message);
        }

        $result = Process::forever()->tty()->run($command);

        if ($result->failed()) {
            throw new \Exception("Failed to run: {$command}\nError: " . $result->errorOutput());
        }
    }
}