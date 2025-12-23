<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Client\Events\ConnectionFailed;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Foundation\Bus\DispatchesJobs;

class ObservabilityServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->bootSlowQueryLogging();
        $this->bootHttpClientInstrumentation();
        $this->bootQueueInstrumentation();
    }

    protected function bootSlowQueryLogging(): void
    {
        $infoThreshold = env('SLOW_QUERY_INFO_MS', 200);
        $warnThreshold = env('SLOW_QUERY_WARN_MS', 1000);

        DB::listen(function ($query) use ($infoThreshold, $warnThreshold) {
            if ($query->time >= $warnThreshold) {
                Log::warning('Slow Query (Warning)', [
                    'sql' => $query->sql,
                    'duration_ms' => $query->time,
                    // 'bindings' => $query->bindings, // Optional: careful with PII in bindings
                ]);
            } elseif ($query->time >= $infoThreshold) {
                Log::info('Slow Query (Info)', [
                    'sql' => $query->sql,
                    'duration_ms' => $query->time,
                ]);
            }
        });
    }

    protected function bootHttpClientInstrumentation(): void
    {
        // Log HTTP Client events. 
        // Note: Global middleware for Http client is available in newer Laravel versions, 
        // but Event Listeners are a solid way to hook into all requests sent via Http facade.
        
        // We will stick to simple events if possible or macro via Http::macro if we want middleware.
        // Actually, Http::globalRequestMiddleware() is available in Laravel 10/11.
        
        Http::globalRequestMiddleware(function ($request, $next) {
            $start = microtime(true);
            
            return $next($request)->then(function ($response) use ($request, $start) {
                $duration = round((microtime(true) - $start) * 1000, 2);
                
                // Only log if it failed or was slow? Or monitoring everything?
                // User asked for "medir tempo e logar falhas, timeouts, retries".
                // Let's log failures primarily, or high latency.
                // For now, let's log failures.
                
                if (!$response->successful()) {
                    Log::error('External HTTP Request Failed', [
                        'url' => (string) $request->url(),
                        'method' => $request->method(),
                        'status' => $response->status(),
                        'duration_ms' => $duration,
                    ]);
                }
                
                return $response;
            });
        });
    }

    protected function bootQueueInstrumentation(): void
    {
        Queue::before(function (JobProcessing $event) {
            $jobId = $event->job->getJobId();
            $payload = $event->job->payload();
            
            // Inject Job ID into Context
            Context::add('job_id', $jobId);
            
            // Propagate Request ID if available in payload
            // (Assuming we manually put it there when dispatching, OR rely on Laravel's context propagation in v11)
            // Laravel 11 automatically propagates Context to Queues! 
            // So 'request_id' might already be there if we used Context::add() before dispatch.
            // But let's be safe.
            
            Context::add('job_start_time', microtime(true));
            
            Log::info('Job Processing', [
                'job' => $event->job->resolveName(),
                'queue' => $event->job->getQueue(),
            ]);
        });

        Queue::after(function (JobProcessed $event) {
            $this->logJobFinish($event->job, 'Job Processed', 'info');
        });

        Queue::failing(function (JobFailed $event) {
            $this->logJobFinish($event->job, 'Job Failed', 'error', ['exception' => $event->exception?->getMessage()]);
        });
    }

    protected function logJobFinish($job, $message, $level, $extra = [])
    {
        $start = Context::get('job_start_time');
        $duration = $start ? round((microtime(true) - $start) * 1000, 2) : null;
        
        Log::log($level, $message, array_merge([
            'job' => $job->resolveName(),
            'duration_ms' => $duration,
        ], $extra));
        
        // Cleanup Context after job
        Context::forget('job_id');
        Context::forget('job_start_time');
    }
}
