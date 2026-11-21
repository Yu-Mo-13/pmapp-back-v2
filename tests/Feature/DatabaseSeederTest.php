<?php

namespace Tests\Feature;

use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    public function testLocalSeedPopulatesAllSeededTables(): void
    {
        $this->app->instance('env', 'local');

        $this->artisan('db:seed')->assertExitCode(0);

        $this->assertDatabaseCount('menus', 4);
        $this->assertDatabaseCount('applications', 10);
        $this->assertDatabaseCount('accounts', 30);
        $this->assertDatabaseCount('passwords', 30);
        $this->assertDatabaseCount('preregisted_passwords', 30);
        $this->assertDatabaseCount('unregisted_passwords', 0);
        $this->assertDatabaseCount('users', 6);
        $this->assertDatabaseCount('roles', 9);
    }

    public function testSeedOutsideLocalOnlyPopulatesMenus(): void
    {
        $this->app->instance('env', 'testing');

        $this->artisan('db:seed')->assertExitCode(0);

        $this->assertDatabaseCount('menus', 4);
        $this->assertDatabaseCount('applications', 0);
        $this->assertDatabaseCount('accounts', 0);
        $this->assertDatabaseCount('passwords', 0);
        $this->assertDatabaseCount('preregisted_passwords', 0);
        $this->assertDatabaseCount('unregisted_passwords', 0);
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('roles', 0);
    }
}
