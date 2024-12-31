<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ActivityTest extends TestCase
{
    use RefreshDatabase;

    #[Test] public function it_creates_an_activity()
    {
        $activity = Activity::factory()->create([
            'title' => 'Activity 1',
            'description' => 'Description of activity 1',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('activities', [
            'title' => 'Activity 1',
            'description' => 'Description of activity 1',
            'is_active' => true,
        ]);
    }
}
