<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Console\Input\Input;

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

    public function add(Request $request){

        $data = [
            'name' => $request['name'],
            'mail' => $request['mail']
        ];
        $rules = [
            'name' =>'required',
            'mail' => 'required|email|unique:users'
        ];
        $validator = Validator::make($data,$rules);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 200);
        }
//        dd($roles);
        $user = new User();
        $user['name'] = $data['name'];
        $user['mail'] = $data['mail'];
//        $roles = Role::all();
//        dd($roles);
        $user->save();
    }
}
