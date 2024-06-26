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
            $data['photo'] = "https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png";

            $exist = User::where('email','=',$data['email'])->get()->toArray();
            if(count($exist) > 0){
                return $this->showResponse(1,'Account Already Exist');
            }

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
            'photo' => $user['photo'],
            'balance' =>$user['balance'],
            'address'=>$user['address'],
            'is_seller'=>$user['is_seller'],
            'password'=>$user['password']
        ];
    }

    public function changePassword(Request $req) {
        try {
            $param = $req->only(
                'password',
                'user_id'
            );
            $user = User::find($param['user_id']);
            if($user) {
                $user->password = $param['password'];
                $user->save();
            } else {
                return $this->showResponse(1,'Change Password Failed');
            }

        } catch  (Exception $e) {
            throw $e;
        }
        return $this->showResponse(0,'Password Successfully Changed');
    }
    public function topUpBalance(Request $req) {
        try {
            $param = $req->only(
                'amount',
                'user_id'
            );

            $user = User::find($param['user_id']);
            if($user){
                $user->balance = $user->balance + $param['amount'];
                $user->save();
            } else {
                return $this->showResponse(1,'Top Up Failed');
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $this->showResponse(0,'Top Up Successfully');
    }
}
