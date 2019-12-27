<?php

use Illuminate\Support\Facades\Artisan;
use Laravel\Lumen\Testing\DatabaseTransactions;

use App\User;
use App\UserRoleTeam;
use Laravel\Lumen\Testing\DatabaseMigrations;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

//    use DatabaseTransactions;
//    use DatabaseMigrations;
    public function setUp():void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    public static $created_user;
    public static $created_team;
    public static $second_user;

    public function testAddUser()
    {

        $response = $this->json('POST', '/user', [
            'name' => 'new',
            'mail' => 'new@mail.ru'
        ]);
        $result = $response->seeJsonStructure(['user'])->response->getContent();
        $json = json_decode($result);
        self::$created_user = $json->user;

        $response->seeJsonStructure([
            'status',
            'message',
            'user'
        ]);
        $response->seeStatusCode(200);
    }

    public function testGetUser()
    {
        $user = self::$created_user;

        $id = $user->id;

        $response = $this->json('GET', '/user/'.$id);
        $response->seeJsonStructure([
            'status',
            'message',
            'user'
        ]);
        $response->seeStatusCode(200);
    }

    public function testUpdateUser()
    {

        $user = self::$created_user;
        $id = $user->id;
        $response = $this->json('PUT', '/user/'.$id , [
            'name' => 'newc',
            'mail' => 'newsdas@mail.ru'
        ]);
        $response->seeJsonStructure([
            'status',
            'message'
        ]);
        $response->seeStatusCode(200);
    }


    public function testAddOnlyTeam()
    {

        $user = self::$created_user;

        $token = $user->token;
        $response = $this->json('POST', '/team', [
            'title' => 'Test_team',
            'user_id' =>$user->id
        ],['token' =>  $token]);
        $result = $response->seeJsonStructure(['team'])->response->getContent();
        $json = json_decode($result);
        self::$created_team = $json->team;

        $response->seeJsonStructure([
            'status',
            'message',
            'team'
        ]);
        $response->seeStatusCode(200);
    }
    public function testGetTeam()
    {
        $team = self::$created_team;
        $id = $team->id;
        $response = $this->json('GET', '/team/'.$id);
        $response->seeJsonStructure([
            'status',
            'message'
        ]);
        $response->seeStatusCode(200);
    }


    public function testUpdateTeam()
    {
        $team = self::$created_team;
        $id = $team->id;
        $response = $this->json('PUT', '/team/'.$id, [
            'title' => 'asasas',
        ]);
        $response->seeJsonStructure([
            'status',
            'message'
        ]);
        $response->seeStatusCode(200);
    }
//
    public function testAddTeamMember()
    {
        $result = $this->json('POST', '/user', [
            'name' => 'new1',
            'mail' => 'new1@mail.ru'
        ]);
        $result = $result->seeJsonStructure(['user'])->response->getContent();
        $json_user = json_decode($result);
        $json_user->user= self::$second_user;
        $token = $json_user->user->token;
        $team = self::$created_team;
        $team_id = $team->id;
        $user_id = $json_user->user->id;
        $response = $this->json('POST', '/add_team_member/user/'.$user_id.'/team/'.$team_id,['token' => $token]);
        $response->seeStatusCode(200);
    }

    public function testAddTeamOwner()
    {
        $token = self::$second_user->token;
        $user_id = self::$second_user->id;
        $team = self::$created_team;
        $team_id = $team->id;
        $response = $this->json('POST', '/add_team_owner/user/'.$user_id.'/team/'.$team_id,['token'=>$token]);
        $response->seeStatusCode(200);
    }

    public function testDeleteTeamMember()
    {
        $response = $this->json('DELETE', '/delete_team_member/user/4/team/1');
        $response->seeStatusCode(200);
    }

    public function testDeleteTeamOwner()
    {
        $response = $this->json('DELETE', '/delete_team_owner/user/3/team/1');
        $response->seeStatusCode(200);
    }

    public function testDeleteUser()
    {
        $response = $this->json('DELETE', '/user/1');
        $response->seeJsonStructure([
            'status',
            'message'
        ]);
        $response->seeStatusCode(200);
    }

    public function testDeleteTeam()
    {
        $response = $this->json('DELETE', '/team/3');
        $response->seeStatusCode(200);
    }

}
