<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Customers\CustomerList;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CustomerListTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        // Create permissions
        Permission::create(['name' => 'customers.view']);
        Permission::create(['name' => 'customers.create']);
        Permission::create(['name' => 'customers.edit']);
        Permission::create(['name' => 'customers.delete']);

        // Create role
        $role = Role::create(['name' => 'super-admin']);
        $role->givePermissionTo(['customers.view', 'customers.create', 'customers.edit', 'customers.delete']);

        // Create branch
        $this->branch = Branch::create([
            'name' => 'Test Branch',
            'code' => 'TB001',
            'is_active' => true,
        ]);

        // Create admin user
        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'branch_id' => $this->branch->id,
            'is_active' => true,
        ]);
        $this->admin->assignRole('super-admin');
    }

    /** @test */
    public function it_can_render_customer_list_component()
    {
        $this->actingAs($this->admin);

        Livewire::test(CustomerList::class)
            ->assertStatus(200);
    }

    /** @test */
    public function it_displays_customers_in_the_list()
    {
        Customer::create([
            'branch_id' => $this->branch->id,
            'customer_code' => 'CUS001',
            'name' => 'John Doe',
            'phone' => '+91 9999999999',
            'address' => '123 Test Street',
            'created_by' => $this->admin->id,
            'is_active' => true,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(CustomerList::class)
            ->assertSee('John Doe')
            ->assertSee('CUS001');
    }

    /** @test */
    public function it_can_search_customers_by_name()
    {
        Customer::create([
            'branch_id' => $this->branch->id,
            'customer_code' => 'CUS001',
            'name' => 'John Doe',
            'phone' => '+91 9999999991',
            'address' => '123 Test Street',
            'created_by' => $this->admin->id,
            'is_active' => true,
        ]);

        Customer::create([
            'branch_id' => $this->branch->id,
            'customer_code' => 'CUS002',
            'name' => 'Jane Smith',
            'phone' => '+91 9999999992',
            'address' => '456 Test Avenue',
            'created_by' => $this->admin->id,
            'is_active' => true,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(CustomerList::class)
            ->set('search', 'John')
            ->assertSee('John Doe')
            ->assertDontSee('Jane Smith');
    }

    /** @test */
    public function it_can_search_customers_by_phone()
    {
        Customer::create([
            'branch_id' => $this->branch->id,
            'customer_code' => 'CUS003',
            'name' => 'Phone Test',
            'phone' => '+91 8888888888',
            'address' => '789 Test Road',
            'created_by' => $this->admin->id,
            'is_active' => true,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(CustomerList::class)
            ->set('search', '8888888888')
            ->assertSee('Phone Test');
    }

    /** @test */
    public function it_can_filter_by_status()
    {
        Customer::create([
            'branch_id' => $this->branch->id,
            'customer_code' => 'CUS004',
            'name' => 'Active Customer',
            'phone' => '+91 7777777771',
            'address' => 'Active Street',
            'created_by' => $this->admin->id,
            'is_active' => true,
        ]);

        Customer::create([
            'branch_id' => $this->branch->id,
            'customer_code' => 'CUS005',
            'name' => 'Inactive Customer',
            'phone' => '+91 7777777772',
            'address' => 'Inactive Street',
            'created_by' => $this->admin->id,
            'is_active' => false,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(CustomerList::class)
            ->set('statusFilter', '1') // Active only
            ->assertSee('Active Customer')
            ->assertDontSee('Inactive Customer');
    }

    /** @test */
    public function it_can_toggle_customer_status()
    {
        $customer = Customer::create([
            'branch_id' => $this->branch->id,
            'customer_code' => 'CUS006',
            'name' => 'Toggle Customer',
            'phone' => '+91 6666666666',
            'address' => 'Toggle Street',
            'created_by' => $this->admin->id,
            'is_active' => true,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(CustomerList::class)
            ->call('toggleStatus', $customer->id);

        $this->assertFalse($customer->fresh()->is_active);
    }

    /** @test */
    public function it_can_delete_a_customer()
    {
        $customer = Customer::create([
            'branch_id' => $this->branch->id,
            'customer_code' => 'CUS007',
            'name' => 'Delete Customer',
            'phone' => '+91 5555555555',
            'address' => 'Delete Street',
            'created_by' => $this->admin->id,
            'is_active' => true,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(CustomerList::class)
            ->call('deleteCustomer', $customer->id);

        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }
}
