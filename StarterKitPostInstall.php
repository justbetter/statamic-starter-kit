<?php

use Statamic\Support\Str;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\search;
use function Laravel\Prompts\select;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\text;

class StarterKitPostInstall
{
    protected string $env = '';

    protected string $app = '';

    protected string $sites = '';

    public function handle(): void
    {
        $this->loadFiles();
        $this->overwriteEnvWithPresets();
        $this->writeFiles();
        $this->reloadEnvironment();
        $this->runMigrations();
        $this->finish();
    }

    protected function runMigrations(): void
    {
        $this->run(
            command: 'rm database/database.sqlite',
        );

        $this->run(
            command: 'php artisan migrate --force',
            processingMessage: 'Running migrations...',
            successMessage: 'Migrations run succesfully!',
            tty: true,
            spinner: false,
        );
    }

    protected function reloadEnvironment(): void
    {
        $this->run(
            command: 'php artisan config:clear',
            tty: true,
            spinner: false,
        );
        
        $app = app();
        $app->bootstrapWith([LoadEnvironmentVariables::class]);
    }

    protected function loadFiles(): void
    {
        $this->env = app('files')->get(base_path('.env.example'));
        $this->app = app('files')->get(base_path('config/app.php'));
        $this->sites = app('files')->get(base_path('resources/sites.yaml'));
    }

    protected function overwriteEnvWithPresets(): void
    {
        if (! confirm(label: 'Do you want overwrite your `.env` file with some presets?', default: true)) {
            return;
        }

        $this->setAppName();
        $this->setLicenseKey();
        $this->setAppUrl();
        $this->setAppKey();
        $this->setDatabaseVariables();
        $this->setLocale();
        $this->setTimezone();
        $this->useDebugbar();
        $this->useImagick();
        $this->setLocalMailer();
        $this->writeEnv();

        info('[✓] `.env` file overwritten.');
    }

    protected function setTimezone(): void
    {
        $newTimezone = search(
            label: 'What timezone should your app be in?',
            options: function (string $value) {
                if (! $value) {
                    return timezone_identifiers_list(DateTimeZone::ALL, null);
                }

                return collect(timezone_identifiers_list(DateTimeZone::ALL, null))
                    ->filter(fn (string $item) => Str::contains($item, $value, true))
                    ->values()
                    ->all();
            },
            placeholder: 'UTC',
            required: true,
        );

        $currentTimezone = config('app.timezone');

        $this->replaceInEnv("APP_TIMEZONE=\"$currentTimezone\"", "APP_TIMEZONE=\"$newTimezone\"");
    }

    protected function setDatabaseVariables(): void
    {
        $databaseName = text(
            label: 'What should the database name be?',
            placeholder: '',
            default: '',
            required: true,
        );

        $this->replaceInEnv("DB_DATABASE=laravel", "DB_DATABASE=$databaseName");

        $username = text(
            label: 'What should the database username be?',
            placeholder: 'root',
            default: 'root',
            required: true,
        );

        $this->replaceInEnv("DB_USERNAME=root", "DB_USERNAME=$username");

        $password = text(
            label: 'What should the database password be?',
            placeholder: '',
            default: '',
            required: false,
        );

        $this->replaceInEnv("DB_PASSWORD=", "DB_PASSWORD=$password");
    }

    protected function setLocale(): void
    {
        $locale = text(
            label: 'What should be the default site locale?',
            placeholder: 'nl_NL',
            default: 'nl_NL',
            required: true,
        );

        $this->replaceInSites("locale: en_US", "locale: $locale");
    }

    protected function writeEnv(): void
    {
        app('files')->put(base_path('.env'), $this->env);
    }

    protected function writeFiles(): void
    {
        app('files')->put(base_path('config/app.php'), $this->app);
        app('files')->put(base_path('resources/sites.yaml'), $this->sites);
    }

    protected function finish(): void
    {
        info('[✓] Starter kit is installed!');
    }

    protected function setAppName(): void
    {
        $appName = text(
            label: 'What should be your app name?',
            placeholder: 'Statamic',
            default: '',
            required: true,
        );

        $appName = preg_replace('/([\'|\"#])/m', '', $appName);

        $this->replaceInEnv('APP_NAME="Statamic"', "APP_NAME=\"{$appName}\"");
    }

    protected function setLicenseKey(): void
    {
        $licenseKey = text(
            label: 'Enter your Statamic License key',
            hint: 'Leave empty to skip',
            default: '',
            required: false,
        );

        $this->replaceInEnv('STATAMIC_LICENSE_KEY=', "STATAMIC_LICENSE_KEY=\"{$licenseKey}\"");
    }

    protected function setAppUrl(): void
    {
        $appUrl = env('APP_URL');

        $this->replaceInEnv('APP_URL=', "APP_URL=\"{$appUrl}\"");
    }

    protected function setAppKey(): void
    {
        $appKey = env('APP_KEY');

        $this->replaceInEnv('APP_KEY=', "APP_KEY=\"{$appKey}\"");
    }

    protected function useDebugbar(): void
    {
        if (confirm(label: 'Do you want to use the debugbar?', default: false)) {
            return;
        }

        $this->replaceInEnv('DEBUGBAR_ENABLED=true', 'DEBUGBAR_ENABLED=false');
    }

    protected function useImagick(): void
    {
        if (! confirm(label: 'Do you want use Imagick as an image processor instead of GD?', default: true)) {
            return;
        }

        $this->replaceInEnv('#IMAGE_MANIPULATION_DRIVER=imagick', 'IMAGE_MANIPULATION_DRIVER=imagick');
    }

    protected function setLocalMailer(): void
    {
        $localMailer = select(
            label: 'Which local mailer do you use?',
            options: [
                'helo' => 'Helo',
                'herd' => 'Herd Pro',
                'log' => 'Log',
                'mailpit' => 'Mailpit',
                'mailtrap' => 'Mailtrap',
            ],
            default: 'mailtrap',
            scroll: 10
        );

        if ($localMailer === 'mailpit') {
            return;
        }

        if ($localMailer === 'helo' || $localMailer === 'herd') {
            $this->replaceInEnv('MAIL_HOST=localhost', 'MAIL_HOST=127.0.0.1');
            $this->replaceInEnv('MAIL_PORT=1025', 'MAIL_PORT=2525');
            $this->replaceInEnv('MAIL_USERNAME=null', 'MAIL_USERNAME="${APP_NAME}"');
        }

        if ($localMailer === 'mailhog') {
            $this->replaceInEnv('MAIL_HOST=localhost', 'MAIL_HOST=127.0.0.1');
            $this->replaceInEnv('MAIL_PORT=1025', 'MAIL_PORT=8025');
        }

        if ($localMailer === 'log') {
            $this->replaceInEnv('MAIL_MAILER=smtp', 'MAIL_MAILER=log');
            echo 'log';
        }
    }

    protected function run(string $command, string $processingMessage = '', string $successMessage = '', ?string $errorMessage = null, bool $tty = false, bool $spinner = true, int $timeout = 120): bool
    {
        $process = new Process(explode(' ', $command));
        $process->setTimeout($timeout);

        if ($tty) {
            $process->setTty(true);
        }

        try {
            $spinner ?
                $this->withSpinner(
                    fn () => $process->mustRun(),
                    $processingMessage,
                    $successMessage
                ) :
                $this->withoutSpinner(
                    fn () => $process->mustRun(),
                    $successMessage
                );

            return true;
        } catch (ProcessFailedException $exception) {
            error($errorMessage ?? $exception->getMessage());

            return false;
        }
    }

    protected function replaceInSites(string $search, string $replace): void
    {
        $this->sites = str_replace($search, $replace, $this->sites);
    }

    protected function replaceInEnv(string $search, string $replace): void
    {
        $this->env = str_replace($search, $replace, $this->env);
    }

    protected function withSpinner(callable $callback, string $processingMessage = '', string $successMessage = ''): void
    {
        spin($callback, $processingMessage);

        if ($successMessage) {
            info("[✓] $successMessage");
        }
    }

    protected function withoutSpinner(callable $callback, string $successMessage = ''): void
    {
        $callback();

        if ($successMessage) {
            info("[✓] $successMessage");
        }
    }
}