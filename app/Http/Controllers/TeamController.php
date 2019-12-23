<?php

namespace App\Http\Controllers;

use App\Role;
use App\Team;

use App\UserRoleTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeamController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }


    public function add(Request $request){

        $data = [
            'title' => $request['title'],
            'owner_id' => $request['owner_id'],
        ];

        $rules = [
            'title' =>'required|unique:teams',
        ];
        $validator = Validator::make($data,$rules);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 200);
        }
        $team = Team::create($data);
        $role_id_owner = Role::find(1); // Owner

        $user_role_owner = new  UserRoleTeam();
        $user_role_owner->user_id = $request->owner_id;
        $user_role_owner->role_id = $role_id_owner->id;
        $user_role_owner->team_id = $team->id;

        $user_role_owner->save();
        return response()->json(['created' => true],200);
    }
    public function update(Request $request, $id){
        $data = [
            'title' => $request['title'],
        ];

        $rules = [
            'title' => 'required|',
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 200);
        }
        Team::where('id', $id)->update($data);
        $user_role_member = new  UserRoleTeam();
        $user_role_member->user_id = $request->member_id;
        $role_id_member = Role::find(2); // Member
        $user_role_member->role_id = $role_id_member->id;
        $user_role_member->team_id = $id;
        $user_role_member->save();

        return response()->json(['update_team' => true],200);
    }
    public function index($id){
        $team =  Team::where('id',$id)->get();
        return response()->json(['get_team'=>true]);
    }
}
