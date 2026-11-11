<?php

namespace JustBetter\StatamicStarterKit\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Statamic\Contracts\Forms\Submission as SubmissionContract;
use Statamic\Facades\Site as SiteFacade;
use Statamic\Fields\Field;
use Statamic\Fields\LabeledValue;
use Statamic\Forms\Email;
use Statamic\Forms\Submission;
use Statamic\Sites\Site;

class SendFormSubmissionEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(
        protected SubmissionContract $submission,
        protected Site $site,
        public array $config
    ) {}

    public function handle(): void
    {
        SiteFacade::setCurrent($this->site->handle());
        app()->setLocale($this->site->lang());

        $this->setMailContent();

        /** @var string|null $mailer */
        $mailer = $this->config['mailer'] ?? null;
        Mail::mailer($mailer)
            ->send(new Email($this->submission, $this->config, $this->site));
    }

    protected function setMailContent(): void
    {
        /** @var Submission $submission */
        $submission = $this->submission;
        $variables = [];
        $list = '';

        /** @var Field $field */
        foreach ($submission->blueprint()->fields()->all() as $handle => $field) {
            $value = $this->escapeAntlers($this->formatValue($submission->augmentedValue($handle)->value(), $field));
            $variables['{{ '.$handle.' }}'] = $value;

            if ($value !== '') {
                $list .= '<strong>'.e((string) $field->display()).':</strong> '.$value."<br>\n";
            }
        }

        $variables['{{ list }}'] = $list;

        $emailContent = $this->config['email_content'] ?? '';
        $this->config['email_content'] = strtr(is_string($emailContent) ? $emailContent : '', $variables);
    }

    protected function formatValue(mixed $value, Field $field): string
    {
        if ($value instanceof LabeledValue) {
            return e((string) $value->label());
        }

        if (is_array($value)) {
            return collect($value)
                ->map(fn (mixed $item): string => $this->formatValue(is_array($item) ? ($item['label'] ?? null) : $item, $field))
                ->filter()
                ->implode(', ');
        }

        if (is_bool($value)) {
            return $value
                ? __('justbetter-starter-kit::messages.yes')
                : __('justbetter-starter-kit::messages.no');
        }

        if (! is_scalar($value)) {
            return '';
        }

        if ($field->type() === 'files') {
            return e(basename((string) $value));
        }

        if ($field->get('input_type') === 'date' && $value !== '') {
            /** @var Carbon $date */
            $date = Carbon::parse((string) $value)->locale($this->site->lang());

            return $date->isoFormat('L');
        }

        return nl2br(e((string) $value));
    }

    /**
     * Submitted values end up in the email config, which Statamic parses with Antlers.
     */
    protected function escapeAntlers(string $value): string
    {
        return str_replace(['{', '}'], ['&#123;', '&#125;'], $value);
    }
}
