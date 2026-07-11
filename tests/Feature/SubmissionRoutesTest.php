<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmissionRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_open_create_submission_page(): void
    {
        $role = Role::create(['name' => 'Staff', 'slug' => 'staff']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($user)->get('/submissions/create');

        $response->assertStatus(200);
    }
}
