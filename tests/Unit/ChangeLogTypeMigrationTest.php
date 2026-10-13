<?php

namespace Tests\Unit;

use Closure;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ChangeLogTypeMigrationTest extends TestCase
{
    public function test_migration_adds_a_string_type_with_an_empty_default(): void
    {
        Schema::shouldReceive('table')
            ->once()
            ->with('change_log', \Mockery::on(function (Closure $callback): bool {
                $blueprint = new Blueprint($this->app['db']->connection(), 'change_log');
                $callback($blueprint);
                $columns = $blueprint->getColumns();

                $this->assertCount(1, $columns);
                $this->assertSame('type', $columns[0]->name);
                $this->assertSame('string', $columns[0]->type);
                $this->assertSame(255, $columns[0]->length);
                $this->assertSame('', $columns[0]->default);

                return true;
            }));

        $migration = require database_path('migrations/2026_10_09_130000_add_type_to_change_log_table.php');
        $migration->up();
    }

    public function test_rollback_drops_the_type_column(): void
    {
        Schema::shouldReceive('table')
            ->once()
            ->with('change_log', \Mockery::on(function (Closure $callback): bool {
                $blueprint = new Blueprint($this->app['db']->connection(), 'change_log');
                $callback($blueprint);
                $commands = $blueprint->getCommands();

                $this->assertCount(1, $commands);
                $this->assertSame('dropColumn', $commands[0]->name);
                $this->assertSame(['type'], $commands[0]->columns);

                return true;
            }));

        $migration = require database_path('migrations/2026_10_09_130000_add_type_to_change_log_table.php');
        $migration->down();
    }
}
