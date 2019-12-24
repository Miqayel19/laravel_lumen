<?php

namespace App\Http\Controllers;

use App\Role;
use App\Team;

use App\User;
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
        ];

        $rules = [
            'title' =>'required|unique:teams',
        ];
        $validator = Validator::make($data,$rules);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 200);
        }
        $token = $request->headers->all()['token'][0];
        $user = User::where('token',$token)->first();
        $user_id = $user['id'];
//        dd($user_id['id']);
        $team = Team::create($data);
        $role_id_owner = Role::find(1); // Owner
        $user_role_owner = new  UserRoleTeam();
        $user_role_owner->user_id = $user_id;
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
        $token = $request->headers->all()['token'][0];
        if($token) {
            $user_create_team_id = User::where('token', $token)->first()->value('id');
            $user_id = UserRoleTeam::where('team_id', $id)->first()['id'];
            $role_id_other_owner = Role::find(1); // Owner
            $role_id_member = Role::find(2); // Member

//            if (isset($user_create_team_id) && $user_create_team_id == $user_id) {

                Team::where('id', $id)->update($data);

                $user_role_member_info = UserRoleTeam::where([['user_id',$request->member_id],['team_id',$id]])->first();
                if(isset($user_role_member_info)){
                    if($user_role_member_info['role_id']){
                        UserRoleTeam::where('user_id',$request->member_id)->update(['role_id' => $role_id_member->id]);
                    }
                } else{
                    $user_role_member = new  UserRoleTeam();
                    $user_role_member->user_id = $request->member_id;
                    $user_role_member->role_id = $role_id_member->id;
                    $user_role_member->team_id = $id;
                    $user_role_member->save();
                }

                $user_role_new_owner_info = UserRoleTeam::where([['user_id',$request->other_owner_id],['team_id',$id]])->first();

                if(isset($user_role_new_owner_info)){
                    if($user_role_new_owner_info['role_id']){
                        UserRoleTeam::where('user_id',$request->other_owner_id)->update(['role_id' => $role_id_other_owner->id]);
                    }
                } else{
                    $user_role_other_owner = new UserRoleTeam();
//                    if($request->other_owner_id == $user_create_team_id ){
//                        UserRoleTeam::where('user_id', $request->other_owner_id)->delete();
//                    }
                    $user_role_other_owner->user_id = $request->other_owner_id;
                    $user_role_other_owner->role_id = $role_id_other_owner->id;
                    $user_role_other_owner->team_id = $id;
                    $user_role_other_owner->save();
                }

                return response()->json(['update_team' => true], 200);

            }
//        }
        return response()->json(['error' => false],404);
    }
    public function index($id){
        $team =  Team::where('id',$id)->get();
        return response()->json(['get_team'=>true]);
    }
}
