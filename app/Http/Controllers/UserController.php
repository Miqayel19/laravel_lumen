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


    public function show($id){

        $user = User::with('team')->where('id',$id)->get();
        return response()->json(['created' => true, 'user' =>$user],200);
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
    public function delete($id){
        if(User::find($id)->delete()){
            return response()->json(['User deleted successfully'=> true],200);
        }
        else{
            return response()->json(['User not  deleted'=> false],200);
        }
    }
}
