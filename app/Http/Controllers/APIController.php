<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class APIController extends Controller
{
    public function getUser()
    {
        $users = User::get();
        return $users;
    }

    public function getUserId($id = null)
    {
        if (empty($id)) {
            $users = User::get();
            return response()->json(["users" => $users], 200);
        } else {
            $users = User::find($id);
            return response()->json(["users" => $users], 200);
        }
    }

    public function addUsers(Request $request) {
        if($request->isMethod('post')) {
            $userData = $request->input();
            // echo "<pre>"; print_r($userData); die;

            // Simple Post API Validations
            // Check User Details
            if ( empty($userData['name']) || empty($userData['email']) || empty($userData['password']) ) {
                echo "cek true";
                $error_message = "Please enter complete user details!";
                return response()->json([
                    "status"    => false,
                    "message"   => $error_message
                ], 422);
            }

            // // Check if validate email
            if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
                $error_message = "Please enter valid email";
                return response()->json([
                    "status"    => false,
                    "message"   => $error_message
                ], 422);
            }

            // // Check if User Email Already Exists
            $userCount = User::where('email', $userData['email'])->count();
            if ($userCount > 0) {
                $error_message = "Email Already Exists";
                return response()->json([
                    "status"    => false,
                    "message"   => $error_message
                ], 422);
            }

            // dd(isset($error_message));
            if (isset($error_message) && !empty($error_message)) {
                return response()->json([
                    "status"    => false,
                    "message"   => $error_message
                ], 422);
            }


            // Advance Post API Validations
            // $rules = [
            //     "name" => "required|regex:/^[\pL\s\-]+$/u",
            //     "email" => "required|email|unique:users",
            //     "password" => "required"
            // ];

            // $customMessage = [
            //     'name.required' => 'Name is required',
            //     'email.required' => 'Email is required',
            //     'email.email' => 'Valid Email is required',
            //     'email.unique' => 'Email already exists in database',
            //     'password.required' => 'Password is required'
            // ];

            // $validator = Validator::make($userData, $rules, $customMessage);

            // if ($validator->fails()) {
            //     return response()->json($validator->errors(), 422);
            // }

            $user = new User;
            $user->name = $userData['name'];
            $user->email = $userData['email'];
            $user->password = bcrypt($userData['password']);
            $user->save();
            return response()->json(['message' => 'User added successfully!'], 201);
        }
    }

    public function addMultipleUsers(Request $request) {
        if($request->isMethod('post')) {
            $userData = $request->input();
            // echo "<pre>"; print_r($userData); die;

            // Advance Post API Validations Multiple
            $rules = [
                "users.*.name" => "required|regex:/^[\pL\s\-]+$/u",
                "users.*.email" => "required|email|unique:users",
                "users.*.password" => "required"
            ];

            $customMessage = [
                'users.*.name.required' => 'Name is required',
                'users.*.email.required' => 'Email is required',
                'users.*.email.email' => 'Valid Email is required',
                'users.*.email.unique' => 'Email already exists in database',
                'users.*.password.required' => 'Password is required'
            ];

            $validator = Validator::make($userData, $rules);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            foreach($userData['users'] as $key => $value) {
                $user = new User;
                $user->name = $value['name'];
                $user->email = $value['email'];
                $user->password = bcrypt($value['password']);
                $user->save();
            }
            return response()->json(['message' => 'User added successfully'], 201);
        }
    }

    public function updateUserDetails(Request $request, $id)
    {
        if($request->isMethod('put')) {
            $userData = $request->input();
            // echo "<pre>"; print_r($userData); die;

            $rules = [
                "users.*.name" => "required|regex:/^[\pL\s\-]+$/u",
                "users.*.password" => "required"
            ];

            $customMessage = [
                'users.*.name.required' => 'Name is required',
                'users.*.password.required' => 'Password is required'
            ];

            $validator = Validator::make($userData, $rules, $customMessage);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            User::where('id', $id)->update([
                'name' => $userData['name'],
                'password' => bcrypt($userData['password']),
            ]);
            return response()->json(['message' => 'User details updated successfully'], 202);

        }
    }

    public function updateUsername(Request $request, $id)
    {
        if($request->isMethod('patch')) {
            $userData = $request->input();
            // echo "<pre>"; print_r($userData); die;

            $rules = [
                "name" => "required|regex:/^[\pL\s\-]+$/u",
            ];

            $customMessage = [
                'name.required' => 'Name is required',
            ];

            $validator = Validator::make($userData, $rules, $customMessage);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            User::where('id', $id)->update([
                'name' => $userData['name']
            ]);
            return response()->json(['message' => 'User details updated successfully'], 202);
        }
    }

    public function deleteUser($id)
    {
        User::where('id', $id)->delete();
        return response()->json(['message' => 'User deleted successfully'], 202);
    }

    public function deleteUserWithJson(Request $request)
    {
        if($request->isMethod('delete')) {
            $userData = $request->all();
            // echo "<pre>"; print_r($userData); die;
            User::where('id', $userData['id'])->delete();
            return response()->json(['message' => 'User deleted successfully'], 202);
        }
    }

    public function deleteMultipleUsers($ids)
    {
        $ids = explode(", ", $ids);
        User::whereIn('id', $ids)->delete();
        return response()->json(['message' => 'User deleted successfully'], 202);
    }

    public function deleteMultipleUsersWithJson(Request $request)
    {
        if($request->isMethod('delete')) {
            $userData = $request->all();
            // echo "<pre>"; print_r($userData); die;
            // foreach ($userData as $key => $value) {
            //     echo $value;
            //     User::whereIn('id', $value)->delete();
            // }
            User::whereIn('id', $userData['ids'])->delete();
            return response()->json(['message' => 'User deleted successfully'], 202);
        }
    }
}
