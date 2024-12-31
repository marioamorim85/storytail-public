<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\User;
use App\Models\Plan;
use App\Models\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    #[Test] public function it_creates_an_admin_user()
    {
        $adminType = UserType::factory()->create(['id' => 1, 'user_type' => 'Admin']);
        $user = User::factory()->create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'user_type_id' => $adminType->id, // Admin user type
        ]);

        $this->assertDatabaseHas('users', [
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'user_type_id' => $adminType->id,
        ]);
    }

    #[Test] public function it_creates_a_normal_user()
    {
        $normalType = UserType::factory()->create(['id' => 2, 'user_type' => 'Normal User']);
        $user = User::factory()->create([
            'first_name' => 'Pedro',
            'last_name' => 'Silva',
            'email' => 'pedro.silva@example.com',
            'user_type_id' => $normalType->id, // Normal user type
        ]);

        $this->assertDatabaseHas('users', [
            'first_name' => 'Pedro',
            'last_name' => 'Silva',
            'email' => 'pedro.silva@example.com',
            'user_type_id' => $normalType->id,
        ]);
    }

    #[Test] public function it_creates_a_user_with_default_attributes()
    {
        $userType = UserType::factory()->create(['id' => 2, 'user_type' => 'Normal User']);
        $user = User::factory()->create(['user_type_id' => $userType->id]);

        $this->assertNotNull($user->first_name);
        $this->assertNotNull($user->last_name);
        $this->assertNotNull($user->email);
        $this->assertNotNull($user->password);
        $this->assertNotNull($user->user_type_id);
    }
}
