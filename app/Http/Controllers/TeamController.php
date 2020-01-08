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


    public function add(Request $request)
    {

        $data = [
            'title' => $request['title'],
        ];

        $rules = [
            'title' => 'required|unique:teams',
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 200);
        }
        $team = Team::create($data);
        $token = $request->headers->all()['token'][0];

        $user_id = User::where('token', $token)->first()['id'];

        $owner_role_id = Role::find(1); // Owner

        $user_role_owner = new  UserRoleTeam();
        $user_role_owner->user_id = $user_id;
        $user_role_owner->role_id = $owner_role_id->id;
        $user_role_owner->team_id = $team->id;

        $user_role_owner->save();
        return response()->json(['status' => 'success', 'message' => 'Team created','team' => $team], 200);
    }

    public function update(Request $request, $id)
    {
        $data = [
            'title' => $request['title'],
        ];
        if (!empty($data['title'])) {
            Team::where('id', $id)->update($data);
            return response()->json(['status' => 'success', 'message' => 'Team updated'], 200);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Team does not updated'], 200);
        }
    }

    public function show($id)
    {
        $team = Team::with('users')->where('id', $id)->get();
        if ($team) {
            return response()->json(['status' => 'success', 'message' => 'Team found', 'team' => $team], 200);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Team not found'], 200);
        }
    }

    public function delete($id)
    {
        $team = Team::where('id', $id)->get();
        if (!empty($team)){
            Team::find($id)->delete();
            return response()->json(['status' => 'success', 'message' => 'Team successfully deleted'], 200);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Team not deleted'], 200);
        }
    }

    public function addTeamMember(Request $request, $member_id, $team_id)
    {
        $member_role_id = Role::find(2); // Member
        $token = $request->headers->all()['token'][0];
        $user_id = User::where('token', $token)->first()['id'];
        if($user_id){
            $team_member_info = UserRoleTeam::where([['user_id', $member_id], ['team_id', $team_id]])->first();
            if (!empty($team_member_info)) {
                UserRoleTeam::where('user_id', $member_id)->update(['role_id' => $member_role_id->id]);
                return response()->json(['status' =>'success','message' => 'Team member  updated'], 200);
            } else {
                $team_member = new  UserRoleTeam();
                $team_member->user_id = $member_id;
                $team_member->role_id = $member_role_id->id;
                $team_member->team_id = $team_id;
                $team_member->save();
            }
            return response()->json(['status' =>'success','message' => 'Team member added'], 200);
        }
        else{
            return response()->json(['status' =>'failed','message' => 'You are not the creator of this team'], 200);
        }
    }

    public function addTeamOwner(Request $request, $owner_id, $team_id)
    {
        $owner_role_id = Role::find(1); // Owner
        $token = $request->headers->all()['token'][0];
        $user_id = User::where('token', $token)->first()['id'];
        if($user_id){
            $team_owner_info = UserRoleTeam::where([['user_id', $owner_id], ['team_id', $team_id]])->first();
            if (!empty($team_owner_info)) {
                UserRoleTeam::where('user_id', $owner_id)->update(['role_id' => $owner_role_id->id]);
                return response()->json(['status' =>'success','message' =>'Team owner updated'], 200);
            } else {
                $team_owner = new  UserRoleTeam();
                $team_owner->user_id = $owner_id;
                $team_owner->role_id = $owner_role_id->id;
                $team_owner->team_id = $team_id;
                $team_owner->save();
                return response()->json(['status' =>'success','message' =>'Team owner added'], 200);
            }
        }
        else{
            return response()->json(['status' => 'failed', 'message' => 'You are not the creator of this team'], 200);
        }

    }

    public function deleteTeamMember(Request $request,$team_id, $member_id)
    {
        $token = $request->headers->all()['token'][0];
        $user_id = User::where('token', $token)->first()['id'];
        if($user_id){
            if ($team_id) {
                if ($member_id) {
                    $member = UserRoleTeam::where('user_id',$member_id)->first();
                    if(!empty($member)){
                        UserRoleTeam::where('user_id', $member_id)->delete();
                        return response()->json(['status' =>'success','message' =>'Member deleted'], 200);
                    }
                    else {
                        return response()->json(['status' =>'failed','message' =>'Member not found'], 200);
                    }
                }
            } else {
                return response()->json(['status' =>'failed','message' =>'Team not found'], 200);
            }
        }
        else{
            return response()->json(['status' =>'failed','message' =>'You are not the creator of this team'], 200);
        }


    }

    public function deleteTeamOwner(Request $request,$team_id, $owner_id)
    {
        $token = $request->headers->all()['token'][0];
        $user_id = User::where('token', $token)->first()['id'];
        if($user_id) {
            if ($team_id) {
                if ($owner_id) {
                    $owner = UserRoleTeam::where('user_id', $owner_id)->first();
                    if(!empty($owner)){
                        UserRoleTeam::where('user_id', $owner_id)->delete();
                        return response()->json(['status' => 'success', 'message' => 'Owner deleted'], 200);
                    } else {
                        return response()->json(['status' => 'failed', 'message' => 'Owner not found'], 200);
                    }
            } else {
                return response()->json(['status' => 'failed', 'message' => 'Team not found'], 200);
            }
        }
            else{
                return response()->json(['status' =>'failed','message' =>'You are not the creator of this team'], 200);
            }
        }

    }
}
