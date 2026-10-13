<?php

namespace Tests\Unit;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Mockery;
use PHPUnit\Framework\TestCase;

class CreatorAndApproverGroupsMigrationTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        DB::clearResolvedInstances();

        parent::tearDown();
    }

    public function test_up_inserts_creator_and_approver_groups(): void
    {
        $query = Mockery::mock(Builder::class);
        DB::shouldReceive('table')->once()->with('groups')->andReturn($query);
        $query->shouldReceive('insert')->once()->with([
            [
                'name' => 'creadores_de_reglamentaciones',
                'description' => 'Creadores de reglamentaciones',
            ],
            [
                'name' => 'aprobadores_de_reglamentaciones',
                'description' => 'Aprobadores de reglamentaciones',
            ],
        ])->andReturn(true);

        $migration = require __DIR__.'/../../database/migrations/2026_10_09_122647_add_creator_and_approver_groups.php';
        $migration->up();

        $this->addToAssertionCount(1);
    }

    public function test_down_deletes_only_creator_and_approver_groups(): void
    {
        $query = Mockery::mock(Builder::class);
        DB::shouldReceive('table')->once()->with('groups')->andReturn($query);
        $query->shouldReceive('whereIn')->once()->with('name', [
            'creadores_de_reglamentaciones',
            'aprobadores_de_reglamentaciones',
        ])->andReturnSelf();
        $query->shouldReceive('delete')->once()->andReturn(2);

        $migration = require __DIR__.'/../../database/migrations/2026_10_09_122647_add_creator_and_approver_groups.php';
        $migration->down();

        $this->addToAssertionCount(1);
    }
}
