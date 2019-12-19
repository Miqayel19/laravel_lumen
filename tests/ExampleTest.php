<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
//    public function testExample()
//    {
//        $this->get('/');
//
//        $this->assertEquals(
//            $this->app->version(), $this->response->getContent()
//        );
//    }
    public function testBasicExample()
    {
        $response = $this->json('POST', '/user', [
            'name' => 'Sallydfdsf',
            'mail' => 'asdasd@km.ru'
        ]);
        $response->seeJson(['created' => true]);


    }
}
