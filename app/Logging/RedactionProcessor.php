<?php

namespace App\Logging;

use Monolog\LogRecord;
use Illuminate\Support\Str;

class RedactionProcessor
{
    /**
     * The keys that should be redacted.
     *
     * @var array
     */
    protected $sensitiveKeys = [
        'password',
        'password_confirmation',
        'token',
        'access_token',
        'refresh_token',
        'api_key',
        'authorization',
        'cookie',
        'set-cookie',
        'secret',
        'client_secret',
        'private_key',
        'credit_card',
        'cc',
        'cvv',
    ];

    /**
     * Invoke the processor.
     *
     * @param  \Monolog\LogRecord  $record
     * @return \Monolog\LogRecord
     */
    public function __invoke(LogRecord $record): LogRecord
    {
        $context = $record->context;

        $record->context = $this->redact($context);

        return $record;
    }

    /**
     * Redact sensitive information from the given array.
     *
     * @param  array  $data
     * @return array
     */
    protected function redact(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->redact($value);
                continue;
            }

            if (!is_string($key)) {
                continue;
            }

            // Check if key contains any sensitive keyword
            foreach ($this->sensitiveKeys as $sensitiveKey) {
                if (Str::contains(strtolower($key), $sensitiveKey)) {
                    $data[$key] = '[REDACTED]';
                    break; 
                }
            }
        }

        return $data;
    }
}
