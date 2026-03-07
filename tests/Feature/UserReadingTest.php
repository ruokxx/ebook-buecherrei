<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Ebook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserReadingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_see_registration_form()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_user_can_register()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('verification.notice'));
        $this->assertDatabaseHas('users', ['email' => 'test@example.com', 'is_admin' => false]);
    }

    public function test_guest_cannot_access_admin_panel()
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }

    public function test_normal_user_cannot_access_admin_panel()
    {
        $user = User::factory()->create(['is_admin' => false]);
        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_panel()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);
    }
}
