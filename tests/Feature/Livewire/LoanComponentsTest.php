<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Loans\LoanList;
use App\Livewire\Loans\LoanForm;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoanType;
use App\Models\User;
use App\Enums\LoanFrequency;
use App\Enums\LoanStatus;
use App\Enums\InterestType;
use Livewire\Livewire;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class LoanComponentsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Branch $branch;
    protected Customer $customer;
    protected LoanType $loanType;

    protected function setUp(): void
    {
        parent::setUp();

        // Create permissions
        $permissions = ['loans.view', 'loans.create', 'loans.edit', 'loans.approve', 'loans.cancel'];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create role
        $role = Role::create(['name' => 'super-admin']);
        $role->givePermissionTo($permissions);

        // Create branch
        $this->branch = Branch::create([
            'name' => 'Loan Test Branch',
            'code' => 'LTB001',
            'is_active' => true,
        ]);

        // Create admin user
        $this->admin = User::create([
            'name' => 'Loan Admin',
            'email' => 'loanadmin@test.com',
            'password' => bcrypt('password'),
            'branch_id' => $this->branch->id,
            'is_active' => true,
        ]);
        $this->admin->assignRole('super-admin');

        // Create customer
        $this->customer = Customer::create([
            'branch_id' => $this->branch->id,
            'customer_code' => 'CUS100',
            'name' => 'Loan Test Customer',
            'phone' => '+91 9999888877',
            'address' => '123 Loan Street',
            'created_by' => $this->admin->id,
            'is_active' => true,
        ]);

        // Create loan type
        $this->loanType = LoanType::create([
            'name' => 'Test Daily Loan',
            'frequency' => LoanFrequency::DAILY,
            'interest_type' => InterestType::FIXED,
            'interest_rate' => 100,
            'default_duration' => 30,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function it_can_render_loan_list_component()
    {
        $this->actingAs($this->admin);

        Livewire::test(LoanList::class)
            ->assertStatus(200);
    }

    /** @test */
    public function it_displays_loans_in_the_list()
    {
        $loan = Loan::create([
            'branch_id' => $this->branch->id,
            'loan_number' => 'LN12345678',
            'customer_id' => $this->customer->id,
            'loan_type_id' => $this->loanType->id,
            'issued_by' => $this->admin->id,
            'principal_amount' => 10000,
            'interest_amount' => 100,
            'total_amount' => 10100,
            'amount_paid' => 0,
            'amount_pending' => 10100,
            'penalty_amount' => 0,
            'total_installments' => 30,
            'paid_installments' => 0,
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'status' => LoanStatus::ACTIVE,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(LoanList::class)
            ->assertSee('LN12345678')
            ->assertSee('Loan Test Customer');
    }

    /** @test */
    public function it_can_search_loans_by_loan_number()
    {
        Loan::create([
            'branch_id' => $this->branch->id,
            'loan_number' => 'LNSEARCHME',
            'customer_id' => $this->customer->id,
            'loan_type_id' => $this->loanType->id,
            'issued_by' => $this->admin->id,
            'principal_amount' => 5000,
            'interest_amount' => 50,
            'total_amount' => 5050,
            'amount_paid' => 0,
            'amount_pending' => 5050,
            'penalty_amount' => 0,
            'total_installments' => 10,
            'paid_installments' => 0,
            'start_date' => now(),
            'end_date' => now()->addDays(10),
            'status' => LoanStatus::ACTIVE,
        ]);

        Loan::create([
            'branch_id' => $this->branch->id,
            'loan_number' => 'LNHIDDEN01',
            'customer_id' => $this->customer->id,
            'loan_type_id' => $this->loanType->id,
            'issued_by' => $this->admin->id,
            'principal_amount' => 5000,
            'interest_amount' => 50,
            'total_amount' => 5050,
            'amount_paid' => 0,
            'amount_pending' => 5050,
            'penalty_amount' => 0,
            'total_installments' => 10,
            'paid_installments' => 0,
            'start_date' => now(),
            'end_date' => now()->addDays(10),
            'status' => LoanStatus::ACTIVE,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(LoanList::class)
            ->set('search', 'SEARCHME')
            ->assertSee('LNSEARCHME')
            ->assertDontSee('LNHIDDEN01');
    }

    /** @test */
    public function it_can_filter_loans_by_status()
    {
        Loan::create([
            'branch_id' => $this->branch->id,
            'loan_number' => 'LNACTIVE01',
            'customer_id' => $this->customer->id,
            'loan_type_id' => $this->loanType->id,
            'issued_by' => $this->admin->id,
            'principal_amount' => 5000,
            'interest_amount' => 50,
            'total_amount' => 5050,
            'amount_paid' => 0,
            'amount_pending' => 5050,
            'penalty_amount' => 0,
            'total_installments' => 10,
            'paid_installments' => 0,
            'start_date' => now(),
            'end_date' => now()->addDays(10),
            'status' => LoanStatus::ACTIVE,
        ]);

        Loan::create([
            'branch_id' => $this->branch->id,
            'loan_number' => 'LNCOMPLETE',
            'customer_id' => $this->customer->id,
            'loan_type_id' => $this->loanType->id,
            'issued_by' => $this->admin->id,
            'principal_amount' => 5000,
            'interest_amount' => 50,
            'total_amount' => 5050,
            'amount_paid' => 5050,
            'amount_pending' => 0,
            'penalty_amount' => 0,
            'total_installments' => 10,
            'paid_installments' => 10,
            'start_date' => now()->subDays(20),
            'end_date' => now()->subDays(10),
            'status' => LoanStatus::COMPLETED,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(LoanList::class)
            ->set('statusFilter', 'active')
            ->assertSee('LNACTIVE01')
            ->assertDontSee('LNCOMPLETE');
    }

    /** @test */
    public function it_can_render_loan_form_component()
    {
        $this->actingAs($this->admin);

        Livewire::test(LoanForm::class)
            ->assertStatus(200);
    }

    /** @test */
    public function it_shows_loan_type_options_in_form()
    {
        $this->actingAs($this->admin);

        Livewire::test(LoanForm::class)
            ->assertSee('Test Daily Loan');
    }

    /** @test */
    public function it_shows_customer_options_in_form()
    {
        $this->actingAs($this->admin);

        Livewire::test(LoanForm::class)
            ->assertSee('Loan Test Customer');
    }

    /** @test */
    public function it_calculates_interest_when_principal_changes()
    {
        $this->actingAs($this->admin);

        $component = Livewire::test(LoanForm::class)
            ->set('loan_type_id', $this->loanType->id)
            ->set('principal_amount', 10000)
            ->set('total_installments', 30);

        // The component should calculate interest
        $this->assertEquals(100, $component->get('interest_amount'));
        $this->assertEquals(10100, $component->get('total_amount'));
    }
}
