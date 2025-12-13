<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Branches\BranchList;
use App\Models\Branch;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class BranchListTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create permissions
        Permission::create(['name' => 'branches.view']);
        Permission::create(['name' => 'branches.create']);
        Permission::create(['name' => 'branches.edit']);
        Permission::create(['name' => 'branches.delete']);

        // Create super-admin role
        $role = Role::create(['name' => 'super-admin']);
        $role->givePermissionTo(['branches.view', 'branches.create', 'branches.edit', 'branches.delete']);

        // Create super admin user
        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $this->superAdmin->assignRole('super-admin');
    }

    /** @test */
    public function it_can_render_branch_list_component()
    {
        $this->actingAs($this->superAdmin);

        Livewire::test(BranchList::class)
            ->assertStatus(200);
    }

    /** @test */
    public function it_displays_branches_in_the_list()
    {
        Branch::create([
            'name' => 'Test Branch One',
            'code' => 'TB001',
            'is_active' => true,
        ]);

        Branch::create([
            'name' => 'Test Branch Two',
            'code' => 'TB002',
            'is_active' => true,
        ]);

        $this->actingAs($this->superAdmin);

        Livewire::test(BranchList::class)
            ->assertSee('Test Branch One')
            ->assertSee('Test Branch Two')
            ->assertSee('TB001')
            ->assertSee('TB002');
    }

    /** @test */
    public function it_can_search_branches()
    {
        Branch::create([
            'name' => 'North Branch',
            'code' => 'NB001',
            'is_active' => true,
        ]);

        Branch::create([
            'name' => 'South Branch',
            'code' => 'SB001',
            'is_active' => true,
        ]);

        $this->actingAs($this->superAdmin);

        Livewire::test(BranchList::class)
            ->set('search', 'North')
            ->assertSee('North Branch')
            ->assertDontSee('South Branch');
    }

    /** @test */
    public function it_can_toggle_branch_status()
    {
        $branch = Branch::create([
            'name' => 'Toggle Branch',
            'code' => 'TG001',
            'is_active' => true,
        ]);

        $this->actingAs($this->superAdmin);

        Livewire::test(BranchList::class)
            ->call('toggleStatus', $branch->id);

        $this->assertFalse($branch->fresh()->is_active);
    }

    /** @test */
    public function it_can_delete_a_branch()
    {
        $branch = Branch::create([
            'name' => 'Delete Branch',
            'code' => 'DL001',
            'is_active' => true,
        ]);

        $this->actingAs($this->superAdmin);

        Livewire::test(BranchList::class)
            ->call('deleteBranch', $branch->id)
            ->assertDontSee('Delete Branch');

        $this->assertDatabaseMissing('branches', ['id' => $branch->id]);
    }

    /** @test */
    public function it_paginates_branches()
    {
        // Create 15 branches
        for ($i = 1; $i <= 15; $i++) {
            Branch::create([
                'name' => "Branch {$i}",
                'code' => "BR{$i}",
                'is_active' => true,
            ]);
        }

        $this->actingAs($this->superAdmin);

        Livewire::test(BranchList::class)
            ->assertSee('Branch 1')
            ->assertDontSee('Branch 15'); // Assuming pagination shows 10 per page
    }
}
