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
    use DatabaseMigrations;

    public function testAddUser(){

        $response = $this->json('POST', '/user', [
            'name' => 'new',
            'mail' => 'new@mail.ru'
        ]);
        $response->seeJson(['created' => true]);
        $response->seeStatusCode(200);
    }
    public function testAddTeam(){
        $response = $this->json('POST', '/team', [
            'title' => 'Carolina',
            'owner_id' => '1'
        ]);
        $response->seeJson(['created' => true]);
        $response->seeStatusCode(200);
    }
    public function testGetUserWithTeam(){
        $response = $this->json('GET', '/user/1');
        $response->seeStatusCode(200);
        $response->seeJson(['created' => true]);
    }
    public function testGetTeam(){
        $response = $this->json('GET', '/team/1');
        $response->seeJson(['get_team' => true]);
        $response->seeStatusCode(200);
    }
    public function testUpdateTeam(){
        $response = $this->json('PUT', '/team/1',[
            'title'=>'asasas',
            'member_id'=>'2'
        ]);
        $response->seeJson(['update_team' => true]);
        $response->seeStatusCode(200);
    }

    public function testDeleteTeam(){
        $response = $this->json('DELETE', '/team/1');
        $response->seeJson(['Team deleted successfully' => true]);
        $response->seeStatusCode(200);
    }
    public function testDeleteUser(){
        $response = $this->json('DELETE', '/user/1');
        $response->seeJson(['User deleted successfully' => true]);
        $response->seeStatusCode(200);
    }



}
