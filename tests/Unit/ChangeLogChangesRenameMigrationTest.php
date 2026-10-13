<?php

namespace Tests\Unit;

use Closure;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ChangeLogChangesRenameMigrationTest extends TestCase
{
    public function test_migration_renames_changes_and_rollback_restores_it(): void
    {
        foreach ([
            'up' => ['changes', 'document_changes'],
            'down' => ['document_changes', 'changes'],
        ] as $method => [$from, $to]) {
            Schema::shouldReceive('table')
                ->once()
                ->with('change_log', \Mockery::on(function (Closure $callback) use ($from, $to): bool {
                    $blueprint = new Blueprint($this->app['db']->connection(), 'change_log');
                    $callback($blueprint);
                    $commands = $blueprint->getCommands();

                    $this->assertCount(1, $commands);
                    $this->assertSame('renameColumn', $commands[0]->name);
                    $this->assertSame($from, $commands[0]->from);
                    $this->assertSame($to, $commands[0]->to);

                    return true;
                }));

            $migration = require database_path('migrations/2026_10_09_140000_rename_changes_to_document_changes_on_change_log_table.php');
            $migration->{$method}();
        }
    }
}