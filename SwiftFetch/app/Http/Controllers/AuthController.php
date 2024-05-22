<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function CreateUser (Request $req)
    {
        try {
            $data = $req->only([
                'name',
                'password',
                'email',
            ]);
            $data['created_at'] = now();
            $user = User::create($data);
        } catch (\Exception $e) {
            throw $e;
        }
        return $this->showResponse('Account '.  $data['name'] . ' Successfully created ');
    }

    public function Login(Request $req){
        try{
            $data = $req->only([
                'email',
                'password'
            ]);
            $user = User::where('email','=',$data['email'])->first()->toArray();
            if($user === null) {
                return $this->showResponse('No Account is Registered');
            }
            if($data['password'] !== $user['password']){
                return $this->showResponse('Wrong Password!');
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $this->showResponse('Welcome '. $user['name']);
    }
}
