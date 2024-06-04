<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function getProfile($userId)
    {
        try {
            $response = User::where('id', $userId)->first();

            if (!isset($response)) {
                return $this->showResponse(1, 'user not found');
            }
        } catch (\Exception $e) {
            throw $e;
        }

        return $response;
    }

    public function editProfile(Request $req)
    {
        try{
            $data = $req->only([
                'id',
                'name',
                'address',
                'gender',
                'description',
                'photo'
            ]);

            $user = User::find($data['id']);
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $user->name = $data['name'];
            $user->address = $data['address'];
            $user->gender = $data['gender'];
            $user->description = $data['description'];
            $user->photo = $data['photo'];

            $user->save();

            return $this->showResponse(0, 'Succesfully change profile');

        } catch (\Exception $e){
            throw $e;
        }
    }
}
