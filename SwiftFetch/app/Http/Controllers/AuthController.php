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
            'user_id' => $user['id'],
            'name' => $user['name']
        ];
    }
    public function RegisterAsSeller(Request $req) {
        try {
            DB::beginTransaction();
            $data = $req->only([
                'user_id',
                'subscribe'
            ]);
            $user = User::find($data['user_id']);
            $userData = $user->first();
            if (!isset($userData)){
                $this->showResponse(1,'No User Found');
            }
            if ($data['subscribe'] === true && isset($userData)){
               $user->is_seller = 1;
            } else if ($data['subscribe'] === false && isset($userData)) {
                $user->is_seller = 0;
            }
            $user->save();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
        return $this->showResponse(0,'Successfully Registered as Seller');
    }
}
