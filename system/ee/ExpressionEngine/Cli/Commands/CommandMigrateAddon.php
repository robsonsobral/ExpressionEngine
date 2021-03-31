<?php

namespace ExpressionEngine\Cli\Commands;

use ExpressionEngine\Cli\Cli;

/**
 * Run migrations
 */
class CommandMigrateAddon extends Cli
{
    /**
     * name of command
     * @var string
     */
    public $name = 'Migrate Addon';

    /**
     * signature of command
     * @var string
     */
    public $signature = 'migrate:addon';

    /**
     * How to use command
     * @var string
     */
    public $usage = 'php eecli.php migrate:addon';

    /**
     * options available for use in command
     * @var array
     */
    public $commandOptions = [
        'steps,s:' => 'Specify the number of migrations to run',
        'everything,e' => 'Run all migrations. Core runs first, all add-on migrations, one at a time.',
        'all' => 'Run all migrations. Alias for --everything',
        'addon,a:' => 'Run migration only for specified addon.',
    ];

    /**
     * Command can run without EE Core
     * @var boolean
     */
    public $standalone = false;

    /**
     * Run the command
     * @return mixed
     */
    public function handle()
    {
        // Specify the number of migrations to run
        $steps = $this->option('-s', -1);

        $all = $this->option('--everything', false) || $this->option('--all', false);
        $addon = $this->option('--addon', false);

        // Lets figure out the location we're migrating
        $location = null;

        // If all is set, it means location is all addons
        if ($all) {
            $location = 'addons';
        } elseif ($addon) {
            $location = $addon;
        }

        // No location set. Lets ask and default to all
        if (is_null($location)) {
            $availableMigrationLocations = ee('Migration')->getAddonsWithMigrations();
            if (count($availableMigrationLocations) === 0) {
                $this->complete('command_migrate_addon_all_migrations_ran');
            }
            $location = $this->ask(lang('command_migrate_addon_ask_location_of_migration') . ' [all, ' . implode(', ', $availableMigrationLocations) . ']', 'addons');
        }

        // No location set, even after
        if (! $location) {
            $this->fail('command_migrate_addon_error_no_location_set');
        }

        // Location all means all addons
        if ($location == 'all') {
            $location = 'addons';
        }

        $migrationGroup = ee('Migration')->getNextMigrationGroup();
        $ran = ee('Migration')->migrateAllByType($location, $migrationGroup, $steps);

        foreach ($ran as $ranMigration) {
            $this->info(lang('command_migrate_addon_migrated') . $ranMigration);
        }

        $this->complete('command_migrate_addon_all_migrations_completed');
    }
}