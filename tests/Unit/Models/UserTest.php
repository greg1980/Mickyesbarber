<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use App\Models\Barber;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'customer'
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'customer'
        ]);
    }

    public function test_user_has_barber_relationship()
    {
        $user = User::factory()->create(['role' => 'barber']);
        $barber = Barber::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(Barber::class, $user->barber);
        $this->assertEquals($barber->id, $user->barber->id);
    }

    public function test_user_has_is_customer_method()
    {
        $customerUser = User::factory()->create(['role' => 'customer']);
        $barberUser = User::factory()->create(['role' => 'barber']);

        $this->assertTrue($customerUser->isCustomer());
        $this->assertFalse($barberUser->isCustomer());
    }

    public function test_user_has_is_barber_method()
    {
        $barberUser = User::factory()->create(['role' => 'barber']);
        $customerUser = User::factory()->create(['role' => 'customer']);

        $this->assertTrue($barberUser->isBarber());
        $this->assertFalse($customerUser->isBarber());
    }

    public function test_user_has_is_admin_method()
    {
        $adminUser = User::factory()->create(['role' => 'admin']);
        $customerUser = User::factory()->create(['role' => 'customer']);

        $this->assertTrue($adminUser->isAdmin());
        $this->assertFalse($customerUser->isAdmin());
    }

    public function test_user_can_be_soft_deleted()
    {
        $user = User::factory()->create();
        $user->delete();

        $this->assertSoftDeleted($user);
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_user_can_be_restored()
    {
        $user = User::factory()->create();
        $user->delete();
        $user->restore();

        $this->assertNotSoftDeleted($user);
    }

    public function test_user_email_is_unique()
    {
        User::factory()->create(['email' => 'test@example.com']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        User::factory()->create(['email' => 'test@example.com']);
    }

    public function test_user_has_profile_photo_url_attribute()
    {
        $user = User::factory()->create(['name' => 'John Doe']);

        $this->assertStringContainsString('John+Doe', $user->profile_photo_url);
    }

    public function test_user_has_available_roles()
    {
        $roles = User::getAvailableRoles();

        $this->assertContains('customer', $roles);
        $this->assertContains('barber', $roles);
        $this->assertContains('admin', $roles);
    }

    public function test_user_role_constants_are_defined()
    {
        $this->assertEquals('customer', User::ROLE_CUSTOMER);
        $this->assertEquals('barber', User::ROLE_BARBER);
        $this->assertEquals('admin', User::ROLE_ADMIN);
    }
}
