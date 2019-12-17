<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;
use App\Team;

use App\UserRole;
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
        ];

        $rules = [
            'title' =>'required',
        ];
        $validator = Validator::make($data,$rules);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 200);
        }
        $team = Team::create($data);

        $user_role_owner = new  UserRole();
        $user_role_owner->user_id = $request->owner_id;
        $role_id_owner = Role::find(1);
        $user_role_owner->role_id = $role_id_owner->id;
        $user_role_owner->team_id = $team->id;
        $user_role_owner->save();

        $user_role_member = new  UserRole();
        $user_role_member->user_id = $request->member_id;
        $role_id_member = Role::find(2);
        $user_role_member->role_id = $role_id_member->id;
        $user_role_member->team_id = $team->id;
        $user_role_member->save();

    }
}
