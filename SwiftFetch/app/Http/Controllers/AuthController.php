<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Mockery\Exception;

class AuthController extends Controller
{
    public function CreateUser (Request $req)
    {
        try {
            $data = $req->only([
                'name',
                'password',
                'email'
            ]);
            $data['balance'] = 0;
            $data['created_at'] = now();
            User::create($data);
        } catch (\Exception $e) {
            throw $e;
        }
        return $this->showResponse(0, 'Account '.  $data['name'] . ' Successfully created ');
    }

    public function Login(Request $req){
        try{
            $data = $req->only([
                'email',
                'password'
            ]);
            $user = User::where('email','=',$data['email'])->first();
            if ($user) {
                $user = $user->toArray();
            } else {
                return $this->showResponse(1, 'No Account is Registered');
            }
            if($data['password'] !== $user['password']){
                return $this->showResponse(1, 'Wrong Password!');
            }
        } catch (Exception $e) {
            throw $e;
        }
        return [
            'error' => 0,
            'id' => $user['id'],
            'name' => $user['name'],
            'image' =>$user['photo'],
            'balance' =>$user['balance'],
            'address'=>$user['address'],
            'is_seller'=>$user['is_seller']
        ];
    }
}
