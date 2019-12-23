<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;
use App\Token;

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


    public function show($id){

        $user = User::with('team')->where('id',$id)->get();
        dd($user);
        return response()->json(['created' => true],200);
    }

    public function add(Request $request){

        $data = [
            'name' => $request['name'],
            'mail' => $request['mail'],
        ];

        $rules = [
            'name' =>'required',
            'mail' => 'required|email|unique:users'
        ];
        $validator = Validator::make($data,$rules);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 200);
        }
        $token = Str::random(5);

        $user = new User();
        $user['name'] = $data['name'];
        $user['mail'] = $data['mail'];
        $user['token'] = $token;
         $user->save();
         return view('token')->with('token', $token);
    }
}
