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
    public static $owner_role_id, $member_role_id;

    public function __construct()
    {
        //
    }

    public function index($id)
    {
        $team = Team::with('users')->where('id', $id)->first();
        if (!empty($team)) {
            return response()->json(['status' => 'success', 'message' => 'Team', 'team' => $team], 200);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Team not found'], 200);
        }
    }

    public function show()
    {
        $teams = Team::all();
        if (!empty($teams)) {
            return response()->json(['status' => 'success', 'message' => 'All Teams', 'teams' => $teams], 200);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Teams not found'], 200);
        }
    }

    public function add(Request $request)
    {
        $token = $request->headers->all()['token'][0];
        $user = User::where('token', $token)->first();
        if ($token && $user) {
            $user_id = $user->id;
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
            self::$owner_role_id = Role::where('status', 'Owner')->first()->id;
            $owner_role_id = self::$owner_role_id;

            UserRoleTeam::create([
                'user_id' => $user_id,
                'team_id' => $team->id,
                'role_id' => $owner_role_id,
            ]);

            return response()->json(['status' => 'success', 'message' => 'Team created', 'team' => $team], 200);
        } else {

            return response()->json(['status' => 'failed', 'message' => 'Invalid token or invalid user']);
        }

    }

    public function update(Request $request, $id)
    {
        $token = $request->headers->all()['token'][0];
        $user = User::where('token', $token)->first();
        $team_owner = UserRoleTeam::where([['user_id', $user->id], ['team_id', $id]])->first();
        if ($token && $user) {

            $data = [
                'title' => $request['title'],
            ];
            $rules = [
                'title' => 'required|unique:teams',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return response()->json($validator->messages(), 200);
            } else {
                if ($team_owner) {
                    Team::where('id', $id)->update($data);
                    return response()->json(['status' => 'success', 'message' => 'Team updated'], 200);
                } else {
                    return response()->json(['status' => 'failed', 'message' => 'You dont have an access to update the team'], 200);
                }

            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Invalid token or Invalid user']);
        }

    }


    public function delete(Request $request, $id)
    {
        $token = $request->headers->all()['token'][0];
        $user = User::where('token', $token)->first();
        $team_owner = UserRoleTeam::where([['user_id', $user->id], ['team_id', $id]])->first();
        if ($token && $user) {

            $team = Team::where('id', $id)->first();
            if (!empty($team)) {
                if ($team_owner) {
                    $team->delete();
                    return response()->json(['status' => 'success', 'message' => 'Team successfully deleted'], 200);
                } else {
                    return response()->json(['status' => 'success', 'message' => 'You dont have an access to delete the team'], 200);
                }
            } else {
                return response()->json(['status' => 'failed', 'message' => 'Team not deleted'], 200);
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Invalid token or Invalid user']);
        }

    }

    public function addTeamMember(Request $request, $member_id, $team_id)
    {
        self::$member_role_id = Role::where('status', 'Member')->first()->id;
        $member_role_id = self::$member_role_id;
        $token = $request->headers->all()['token'][0];
        $user = User::where('token', $token)->first();
        $team_owner = UserRoleTeam::where([['user_id', $user->id], ['team_id', $team_id]])->first();
        if ($token && $user) {
            if ($team_owner) {
                $team_member_info = UserRoleTeam::where([['user_id', $member_id], ['team_id', $team_id]])->first();
                if (!empty($team_member_info)) {
                    UserRoleTeam::where('user_id', $member_id)->update(['role_id' => $member_role_id]);
                    return response()->json(['status' => 'success', 'message' => 'Team member  updated'], 200);
                } else {
                    UserRoleTeam::create([
                        'user_id' => $member_id,
                        'team_id' => $team_id,
                        'role_id' => $member_role_id,
                    ]);

                }
                return response()->json(['status' => 'success', 'message' => 'Team member added'], 200);
            } else {
                return response()->json(['status' => 'success', 'message' => 'You dont have an access to delete the team'], 200);
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Invalid token or Invalid user'], 200);
        }

    }


    public function addTeamOwner(Request $request, $owner_id, $team_id)
    {
        $owner_role_id = self::$owner_role_id;
        $token = $request->headers->all()['token'][0];
        $user = User::where('token', $token)->first();
        $team_owner = UserRoleTeam::where([['user_id', $user->id], ['team_id', $team_id]])->first();
        if ($token && $user) {
            if ($team_owner) {
                $team_owner_info = UserRoleTeam::where([['user_id', $owner_id], ['team_id', $team_id]])->first();
                if (!empty($team_owner_info)) {
                    UserRoleTeam::where('user_id', $owner_id)->update(['role_id' => $owner_role_id]);
                    return response()->json(['status' => 'success', 'message' => 'Team owner updated'], 200);
                } else {
                    UserRoleTeam::create([
                        'user_id' => $owner_id,
                        'team_id' => $team_id,
                        'role_id' => $owner_role_id,
                    ]);

                    return response()->json(['status' => 'success', 'message' => 'Team owner added'], 200);
                }

            } else {
                return response()->json(['status' => 'success', 'message' => 'You dont have an access to delete the team'], 200);
            }

        } else {
            return response()->json(['status' => 'failed', 'message' => 'Invalid token or Invalid user'], 200);
        }

    }

    public function deleteTeamMember(Request $request, $team_id, $member_id)
    {
        $token = $request->headers->all()['token'][0];
        $user = User::where('token', $token)->first();
        $team_owner = UserRoleTeam::where([['user_id', $user->id], ['team_id', $team_id]])->first();
        if ($token && $user) {
            if ($team_owner) {
                if ($team_id) {
                    if ($member_id) {
                        $member = UserRoleTeam::where('user_id', $member_id)->first();
                        if (!empty($member)) {
                            UserRoleTeam::where('user_id', $member_id)->delete();
                            return response()->json(['status' => 'success', 'message' => 'Member deleted'], 200);
                        } else {
                            return response()->json(['status' => 'failed', 'message' => 'Member not found'], 200);
                        }
                    }
                } else {
                    return response()->json(['status' => 'failed', 'message' => 'Team not found'], 200);
                }
            } else {
                return response()->json(['status' => 'success', 'message' => 'You dont have an access to delete the team'], 200);
            }

        } else {
            return response()->json(['status' => 'failed', 'message' => 'Invalid token or Invalid user'], 200);
        }


    }

    public function deleteTeamOwner(Request $request, $team_id, $owner_id)
    {
        $token = $request->headers->all()['token'][0];
        $user = User::where('token', $token)->first();
        $team_owner = UserRoleTeam::where([['user_id', $user->id], ['team_id', $team_id]])->first();
        if ($token && $user) {
            if ($team_owner) {
                if ($team_id) {
                    if ($owner_id) {
                        $owner = UserRoleTeam::where('user_id', $owner_id)->first();
                        if (!empty($owner)) {
                            UserRoleTeam::where('user_id', $owner_id)->delete();
                            return response()->json(['status' => 'success', 'message' => 'Owner deleted'], 200);
                        } else {
                            return response()->json(['status' => 'failed', 'message' => 'Owner not found'], 200);
                        }
                    } else {
                        return response()->json(['status' => 'failed', 'message' => 'Team not found'], 200);
                    }
                }

            } else {
                return response()->json(['status' => 'success', 'message' => 'You dont have an access to delete the team'], 200);

            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Invalid token or Invalid user']);
        }

    }
}
