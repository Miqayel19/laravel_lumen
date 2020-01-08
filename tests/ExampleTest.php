<?php

use Illuminate\Support\Facades\Artisan;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */


    public static $created_user;
    public static $created_team;
    public  $owner;
    public  $member;



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

    public function testAddUsersToTeam($membership = null){
        $date = date_create();
        $response = $this->json('POST', '/user', [
            'name' => 'new'.date_timestamp_get($date),
            'mail' => date_timestamp_get($date).'@mail.ru'
        ]);
        $result=$response->response->getContent();
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
        $response = $this->json('POST', '/add_team_member/user/'.$member_id.'/team/'.$team_id,[],['token' => $token]);
        $response->seeJsonStructure([
            'message'
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
        $response = $this->json('POST', '/add_team_owner/user/'.$owner_id.'/team/'.$team_id,[],['token'=>$token]);
        $response->seeJsonStructure([
            'message'
        ]);
    }

    public function testDeleteTeamMember()
    {
        $user= self::$created_user;
        $token = $user->token;
        $member = $this->member;
        $member_id = $member['id'];
        $team = self::$created_team;
        $team_id = $team->id;
        $response = $this->json('DELETE', '/delete_team_member/user/'.$member_id.'/team/'.$team_id,['token' =>$token]);
        $response->seeJsonStructure([
            'message',
        ]);

    }

    public function testDeleteTeamOwner()
    {
        $user= self::$created_user;
        $token = $user->token;
        $owner =  $this->owner;
        $owner_id = $owner['id'];
        $team = self::$created_team;
        $team_id = $team->id;
        $response = $this->json('DELETE', '/delete_team_owner/user/'.$owner_id.'/team/'.$team_id,['token' => $token]);
        $response->seeJsonStructure([
            'message'
        ]);
    }

    public function testDeleteTeam()
    {
        $team = self::$created_team;
        $team_id = $team->id;
        $response = $this->json('DELETE', '/team/'.$team_id);
        $response->seeJsonStructure([
            'status',
            'message'
        ]);
    }
    public function testDeleteUser()
    {
        $user = self::$created_user;
        $user_id = $user->id;
        $response = $this->json('DELETE', '/user/'.$user_id);
        $response->seeJsonStructure([
            'status',
            'message'
        ]);
    }

        public function testResetDb(){
            Artisan::call('migrate:reset');
            $this->assertTrue(true);
        }
}
