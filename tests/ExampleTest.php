<?php

use Illuminate\Support\Facades\Artisan;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */


    public static  $created_user,$created_team;
    public  $owner,$member;



    public function testAddUser()
    {
        $response = $this->json('POST', '/users', [
            'name' => 'new',
            'mail' => 'fzc@mail.ru'
        ]);
        $result = $response->response->getContent();
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
        $response = $this->json('GET', '/users/'.$id);
        $response->seeJsonStructure([
            'status',
            'message',
            'user'
        ]);
        $response->seeStatusCode(200);
    }
    public function testGetUsers()
    {
        $response = $this->json('GET', '/users/');
        $response->seeJsonStructure([
            'status',
            'message',
            'users'
        ]);
        $response->seeStatusCode(200);
    }


    public function testUpdateUser()
    {

        $user = self::$created_user;
        $id = $user->id;
        $response = $this->json('PUT', '/users/'.$id , [
            'name' => 'newc',
            'mail' => 'nesdsaaaaaaaadszxwsdas@mail.ru'
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
        $response = $this->json('POST', '/teams', [
            'title' => 'Tesdsssdsddteam',
        ],['token' =>  $token]);
        $result = $response->response->getContent();
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
        $response = $this->json('GET', '/teams/'.$id);
        $response->seeJsonStructure([
            'status',
            'message'
        ]);
        $response->seeStatusCode(200);
    }
    public function testGetTeams()

    {
        $response = $this->json('GET', '/teams/');
        $response->seeJsonStructure([
            'status',
            'message',
            'teams'
        ]);
        $response->seeStatusCode(200);
    }
    public function testUpdateTeam()
    {
        $user = self::$created_user;
        $token = $user->token;
        $team = self::$created_team;
        $id = $team->id;
        $response = $this->json('PUT', '/teams/'.$id, [
            'title' => 'asasas',
        ],['token' => $token]);
        $response->seeJsonStructure([
            'status',
            'message'
        ]);
        $response->seeStatusCode(200);
    }
    public function testAddUsersToTeam($membership = null){
        $date = date_create();
        $response = $this->json('POST', '/users', [
            'name' => 'new'.date_timestamp_get($date),
            'mail' => date_timestamp_get($date).'@mail.ru'
        ]);
        $result = $response->response->getContent();
        $json = json_decode($result);
        if($membership == 'owner'){
             $json->user = $this->owner ;
        }elseif($membership == 'member'){
            $json->user = $this->member ;
        }

        $response->seeStatusCode(200);

    }
    public function testAddTeamMember()
    {

        $user = self::$created_user;
        $token = $user->token;
        $team = self::$created_team;
        $team_id = $team->id;
        $this->testAddUsersToTeam('member');
        $member = $this->member;
        $member_id = $member['id'];
        $response = $this->json('POST', '/add_team_member/users/'.$member_id.'/teams/'.$team_id,[],['token' => $token]);
        $response->seeJsonStructure([
            'message',
        ]);

    }
    public function testAddTeamOwner()
    {
        $user= self::$created_user;
        $token = $user->token;
        $this->testAddUsersToTeam('owner');
        $owner =  $this->owner;
        $owner_id = $owner['id'];
        $team = self::$created_team;
        $team_id = $team->id;
        $response = $this->json('POST', '/add_team_owner/users/'.$owner_id.'/teams/'.$team_id,[],['token'=>$token]);
        $response->seeJsonStructure([
            'message'
        ]);
    }
//
    public function testDeleteTeamMember()
    {
        $user= self::$created_user;
        $token = $user->token;
        $member = $this->member;
        $member_id = $member['id'];
        $team = self::$created_team;
        $team_id = $team->id;
        $response = $this->json('DELETE', '/delete_team_member/users/'.$member_id.'/teams/'.$team_id,['token' =>$token]);
        $response->seeJsonStructure([
            'message',
        ]);

    }
//
    public function testDeleteTeamOwner()
    {
        $user= self::$created_user;
        $token = $user->token;
        $owner =  $this->owner;
        $owner_id = $owner['id'];
        $team = self::$created_team;
        $team_id = $team->id;
        $response = $this->json('DELETE', '/delete_team_owner/users/'.$owner_id.'/teams/'.$team_id,['token' => $token]);
        $response->seeJsonStructure([
            'message'
        ]);
    }
//
    public function testDeleteTeam()
    {
        $team = self::$created_team;
        $team_id = $team->id;
        $response = $this->json('DELETE', '/teams/'.$team_id);
        $response->seeJsonStructure([
            'message'
        ]);
    }
    public function testDeleteUser()
    {
        $user = self::$created_user;
        $user_id = $user->id;
        $response = $this->json('DELETE', '/users/'.$user_id);
        $response->seeJsonStructure([
            'message'
        ]);
    }
//
    public function testResetDb(){
            Artisan::call('migrate:reset');
            $this->assertTrue(true);
        }
}
