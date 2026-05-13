<?php

namespace App\Jobs;

use App\Models\JobMetric;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DemoJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $metricId,
        public int $workDurationMs = 500,
    ) {}

    public function handle(): void
    {
        $workerId = gethostname().':'.getmypid();

        JobMetric::where('id', $this->metricId)->update([
            'picked_up_at' => microtime(true),
            'worker_id' => $workerId,
        ]);

        $this->sleepFully($this->workDurationMs * 1000);

        JobMetric::where('id', $this->metricId)->update([
            'completed_at' => microtime(true),
        ]);
    }

    private function sleepFully(int $microseconds): void
    {
        $deadline = microtime(true) + ($microseconds / 1_000_000);

        while (($remaining = $deadline - microtime(true)) > 0) {
            usleep((int) ($remaining * 1_000_000));
        }
    }
}
