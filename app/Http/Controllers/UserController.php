<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
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
            $data->roles();
           User::create($data);
    }
}
