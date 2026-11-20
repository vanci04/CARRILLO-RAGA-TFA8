<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class CafeTest extends TestCase
{
    use RefreshDatabase;

    private function manager(): User
    {
        return User::factory()->create(['role' => 'Manager']);
    }

    public function test_all_required_pages_and_pos_render_in_database_mode(): void
    {
        $this->seed();
        foreach (['/', '/about', '/pos'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->get('/customers')->assertRedirect('/login');
        $this->get('/users')->assertRedirect('/login');
        $this->actingAs(User::where('username', 'ella.manager')->firstOrFail());
        $this->get('/customers')->assertSee('Isabel Santos')->assertSee('isabel@example.com')->assertSee('0917 555 0101');
        $this->get('/users')->assertSee('ella.manager')->assertSee('Ella Mendoza')->assertSee('Manager');
    }

    public function test_array_mode_renders_sample_accounts_without_database_records(): void
    {
        config(['cafe.data_source' => 'arrays']);
        $this->assertSame(0, Customer::count());
        $this->assertSame(0, User::count());
        $this->actingAs($this->manager());
        $this->get('/customers')->assertOk()->assertSee('Isabel Santos')->assertSee('Ethan Lim');
        $this->get('/users')->assertOk()->assertSee('ella.manager')->assertSee('noah.cashier');
    }

    public function test_database_mode_reads_new_records_through_models(): void
    {
        config(['cafe.data_source' => 'eloquent']);
        $this->actingAs($this->manager());
        Customer::create(['full_name' => 'Database Guest', 'email' => 'guest@example.com', 'phone' => '09170000000']);
        User::create(['username' => 'database.staff', 'full_name' => 'Database Staff', 'email' => 'staff@example.com', 'password' => 'password', 'role' => 'Cashier']);
        $this->get('/customers')->assertSee('Database Guest')->assertDontSee('Isabel Santos');
        $this->get('/users')->assertSee('database.staff')->assertDontSee('Ella Mendoza');
    }

    public function test_seeder_is_repeatable_and_populates_at_least_five_each(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);
        $this->assertDatabaseCount('customers', 6);
        $this->assertDatabaseCount('users', 6);
    }

    private function orderPayload(): array
    {
        return ['checkout_token' => (string) Str::uuid(), 'service' => 'Dine in', 'cash' => 50000,
            'items' => [['id' => 1, 'quantity' => 2], ['id' => 7, 'quantity' => 1]]];
    }

    public function test_checkout_uses_server_prices_and_saves_receipt(): void
    {
        $payload = $this->orderPayload();
        $payload['items'][0]['price'] = 1;
        $payload['total'] = 1;
        $response = $this->postJson('/orders', $payload)->assertCreated()->assertJsonStructure(['receipt']);
        $order = Order::firstOrFail();
        $this->assertSame(42500, $order->total);
        $this->assertSame(16500, $order->items[0]['price']);
        $this->get($response->json('receipt'))->assertOk()->assertSee('₱425.00')->assertSee('₱75.00');
    }

    public function test_retried_checkout_does_not_create_duplicate_sale(): void
    {
        $payload = $this->orderPayload();
        $first = $this->postJson('/orders', $payload)->assertCreated();
        $retry = $this->postJson('/orders', $payload)->assertOk();
        $this->assertSame($first->json('receipt'), $retry->json('receipt'));
        $this->assertDatabaseCount('orders', 1);
    }

    public function test_checkout_rejects_invalid_payment_and_items(): void
    {
        $payload = $this->orderPayload();
        $this->postJson('/orders', [...$payload, 'cash' => 42499])->assertUnprocessable()->assertJsonValidationErrors('cash');
        $this->postJson('/orders', [...$payload, 'items' => []])->assertUnprocessable();
        $this->postJson('/orders', [...$payload, 'items' => [['id' => 999, 'quantity' => 1]]])->assertUnprocessable();
        $this->postJson('/orders', [...$payload, 'items' => [['id' => 1, 'quantity' => 0]]])->assertUnprocessable();
        $this->postJson('/orders', [...$payload, 'items' => [['id' => 1, 'quantity' => 100]]])->assertUnprocessable();
        $this->postJson('/orders', [...$payload, 'service' => 'Invalid'])->assertUnprocessable();
        $this->postJson('/orders', [...$payload, 'items' => [['id' => 1, 'quantity' => 1], ['id' => 1, 'quantity' => 1]]])->assertUnprocessable();
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_unknown_receipt_returns_not_found(): void
    {
        $this->get('/receipts/999999')->assertNotFound();
    }

    public function test_customer_create_and_edit_validate_and_persist(): void
    {
        $this->actingAs($this->manager());
        $this->post('/customers', ['full_name' => '', 'email' => 'not-email'])->assertSessionHasErrors(['full_name', 'email']);
        $this->post('/customers', ['full_name' => 'New Guest', 'email' => 'guest@example.com', 'phone' => '09170000000'])->assertRedirect('/customers');
        $customer = Customer::firstOrFail();
        $this->put('/customers/'.$customer->id, ['full_name' => 'Updated Guest', 'email' => 'updated@example.com', 'phone' => ''])->assertRedirect('/customers');
        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'full_name' => 'Updated Guest']);
    }

    public function test_user_create_edit_and_avatar_thumbnail(): void
    {
        Storage::fake('public');
        $this->actingAs($this->manager());
        $this->post('/users', ['username' => 'new.staff', 'full_name' => 'New Staff', 'email' => 'new@example.com', 'password' => 'password', 'password_confirmation' => 'password', 'role' => 'Barista'])->assertRedirect('/users');
        $user = User::where('username', 'new.staff')->firstOrFail();
        $this->post('/users', ['username' => 'new.staff', 'full_name' => 'Duplicate', 'email' => 'other@example.com', 'password' => 'password', 'password_confirmation' => 'password', 'role' => 'Cashier'])->assertSessionHasErrors('username');
        $this->put('/users/'.$user->id, ['username' => 'new.staff', 'full_name' => 'Updated Staff', 'email' => 'new@example.com', 'role' => 'Manager', 'avatar' => UploadedFile::fake()->image('avatar.png', 600, 400)])->assertRedirect('/users');
        $user->refresh();
        Storage::disk('public')->assertExists($user->avatar);
        $this->assertStringEndsWith('.jpg', $user->avatar);
    }
}
