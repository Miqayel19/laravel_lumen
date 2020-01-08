<?php


use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends Laravel\Lumen\Testing\TestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return  require __DIR__ . '/../bootstrap/app.php';

    }

    public function setUp():void
    {
        parent::setUp();
        $this->artisanMigrateRefresh();

    }

    public function artisanMigrateRefresh()
    {
        Artisan::call('migrate');
        Artisan::call('db:seed');
    }










}
