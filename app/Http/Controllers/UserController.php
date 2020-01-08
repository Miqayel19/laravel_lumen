<?php

namespace App\Http\Controllers;

use App\User;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
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


    public function show($id)
    {
        $user = User::with('team')->where('id', $id)->get();
        if (empty($user)) {
            return response()->json(['status' => 'failed', 'message' => 'User not found'], 200);
        } else {
            return response()->json(['status' => 'success', 'message' => 'User', 'user' => $user], 200);
        }

    }

    public function add(Request $request)
    {
        $data = [
            'name' => $request['name'],
            'mail' => $request['mail'],
        ];

        $rules = [
            'name' => 'required',
            'mail' => 'required|email|unique:users'
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 200);
        }
        $token = Str::random(5);

        $user = new User();
        $user['name'] = $data['name'];
        $user['mail'] = $data['mail'];
        $user['token'] = $token;
        $user->save();
        return response()->json(['status' => 'success', 'message' => 'User created', 'user' => $user], 200);
    }

    public function delete($id)
    {
        $user = User::where('id', $id)->get();
        if (empty($user)) {
            return response()->json(['status' => 'failed', 'message' => 'User not deleted'], 200);
        } else {
            User::find($id)->delete();
            return response()->json(['status' => 'success', 'message' => 'User deleted'], 200);
        }
    }

    public function update(Request $request, $id)
    {
        $data = [
            'name' => $request['name'],
            'mail' => $request['mail'],
        ];
        $rules = [
            'name' => 'required',
            'mail' => 'required|email|unique:users,mail,' . $id . ',id',
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 200);
        } else {
            User::where('id', $id)->update($data);
            return response()->json(['status' => 'success', 'message' => 'User updated'], 200);
        }


    }

}
