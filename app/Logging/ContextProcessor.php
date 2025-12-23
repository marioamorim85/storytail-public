<?php

namespace App\Logging;

use Illuminate\Support\Facades\Context;
use Monolog\LogRecord;

class ContextProcessor
{
    /**
     * Invoke the processor.
     *
     * @param  \Monolog\LogRecord  $record
     * @return \Monolog\LogRecord
     */
    public function __invoke(LogRecord $record): LogRecord
    {
        // Add all context data to the record
        $context = Context::all();

        // Inject global app info if not already present
        if (!isset($context['app_env'])) {
            $context['app_env'] = config('app.env');
        }

        // Merge contexts. Note: We use 'extra' or allow it to be top-level keys 
        // depending on formatter. For JSON/structured, merging into context/extra is typical.
        // Monolog 3 (Laravel 10+) LogRecord is immutable-ish but has array access for some parts or channel specific.
        // However, __invoke in monolog usually returns modified record or modifies array if using older versions.
        // Laravel 11 uses Monolog 3.
        
        $record->extra = array_merge($record->extra, $context);

        return $record;
    }
}
