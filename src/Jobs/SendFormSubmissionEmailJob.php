<?php

namespace JustBetter\StatamicStarterKit\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Statamic\Contracts\Forms\Submission;
use Statamic\Forms\Email;
use Statamic\Sites\Site;

class SendFormSubmissionEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(
        protected Submission $submission,
        protected Site $site,
        public array $config
    ) {}

    public function handle(): void
    {
        $this->setMailContent();

        /** @var string|null $mailer */
        $mailer = $this->config['mailer'] ?? null;
        Mail::mailer($mailer)
            ->send(new Email($this->submission, $this->config, $this->site));
    }

    protected function setMailContent(): void
    {
        $data = $this->submission->data();
        /** @var Collection<int, array<string, mixed>> $fields */
        $fields = $this->submission->fields();
        $fieldHandles = $fields->pluck('handle')->all();
        $variables = [];
        $list = '';
        foreach ($fieldHandles as $field) {
            /** @var array<string, mixed>|null $fieldData */
            $fieldData = $fields->firstWhere('handle', $field);
            /** @var string $display */
            $display = isset($fieldData['display']) && is_string($fieldData['display']) ? $fieldData['display'] : '';
            /** @var string|array<int|string, mixed>|object $value */
            $value = $data[$field] ?? '';
            $list .= '<strong>'.$display.':</strong> ';
            $variableValues = $this->formatFieldValue($value, $list);
            $list .= '<br>';
            /** @var string $field */
            $variables["{{ $field }}"] = $variableValues;
        }

        $variables['{{ list }}'] = $list;

        /** @var string $emailContent */
        $emailContent = isset($this->config['email_content']) && is_string($this->config['email_content']) ? $this->config['email_content'] : '';
        $text = strtr($emailContent, $variables);
        $this->config['email_content'] = $text;
    }

    /**
     * @param  string|array<int|string, mixed>|object  $value
     */
    protected function formatFieldValue(string|array|object $value, string &$list): string
    {
        if (is_array($value)) {
            return $this->formatArrayValue($value, $list);
        }

        $label = $this->extractLabelFromItem($value);
        $list .= $label;

        return $label;
    }

    /**
     * @param  array<int|string, mixed>  $value
     */
    protected function formatArrayValue(array $value, string &$list): string
    {
        $variableValues = '';
        foreach ($value as $key => $item) {
            if ($key > 0) {
                $list .= ', ';
                $variableValues .= ', ';
            }

            $label = $this->extractLabelFromItem($item);
            $list .= $label;
            $variableValues .= $label;
        }

        return $variableValues;
    }

    protected function extractLabelFromItem(mixed $item): string
    {
        if (is_array($item) && isset($item['label'])) {
            $labelValue = $item['label'];

            return is_string($labelValue) ? $labelValue : '';
        }

        if (is_string($item)) {
            return $item;
        }

        if (is_object($item) && method_exists($item, 'label')) {
            $labelValue = $item->label();

            return is_string($labelValue) ? $labelValue : '';
        }

        return '';
    }
}
