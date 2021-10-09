<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
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

    public function getUsersList(Request $request)
    {
        $header = $request->header('Authorization');
        if (empty($header)) {
            $message = "Header Authorization is missing!";
            return response()->json(['status' => false, 'message' => $message], 422);
        } else {
            if ($header == "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkRlZGUgU3lhcmlmdWRpbiIsImlhdCI6MTUxNjIzOTAyMn0.TELqbf2KLv5nsUYO2w0tjALCJPkWZHzTx2yuoCP8Iig") {
                $users = User::get();
                return response()->json(['users' => $users], 200);
            } else {
                $message = "Header Authorization is incorrect!";
                return response()->json(['status' => false, 'message' => $message], 422);
            }
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

    public function registerUser(Request $request)
    {
        if ($request->isMethod('post')) {
            $userData = $request->input();
            // echo "<pre>"; print_r($userData); die;

            // Advance Post API Validations
            $rules = [
                "name" => "required|regex:/^[\pL\s\-]+$/u",
                "email" => "required|email|unique:users",
                "password" => "required"
            ];

            $customMessage = [
                'name.required' => 'Name is required',
                'email.required' => 'Email is required',
                'email.email' => 'Valid Email is required',
                'email.unique' => 'Email already exists in database',
                'password.required' => 'Password is required'
            ];

            $validator = Validator::make($userData, $rules, $customMessage);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            // Generate Unique API / Access Token
            $apiToken = Str::random(60);

            $user = new User;
            $user->name = $userData['name'];
            $user->email = $userData['email'];
            $user->password = bcrypt($userData['password']);
            $user->api_token = $apiToken;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'User registered successfully!',
                'token' => $apiToken
            ], 201);
        }
    }

    public function loginUser(Request $request)
    {
        if ($request->isMethod('post')) {
            $userData = $request->input();
            // echo "<pre>"; print_r($userData); die;

            // Advance Post API Validations
            $rules = [
                "email" => "required|email|exists:users",
                "password" => "required"
            ];

            $customMessage = [
                'email.required' => 'Email is required',
                'email.email' => 'Valid Email is required',
                'email.unique' => 'Email does not exists in database',
                'password.required' => 'Password is required'
            ];

            $validator = Validator::make($userData, $rules, $customMessage);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            // Fetch User Details
            $userDetails = User::where('email', $userData['email'])->first();

            // Verify the password
            if (password_verify($userData['password'], $userDetails->password)) {
                // Update Token
                $apiToken = Str::random(60);

                // Update Token
                User::where('email', $userData['email'])->update(['api_token' => $apiToken]);
                return response()->json([
                    'status' => true,
                    'message' => 'User logged in successfully!',
                    'token' => $apiToken
                ], 201);
            } else {
                return response()->json(['status' => false, 'message' => 'Password in incorrect']);
            }

        }
    }

    public function logoutUser(Request $request)
    {
        $api_token = $request->header('Authorization');
        if (empty($api_token)) {
            $message = "User Token is missing in API Header";
            return response()->json(['status' => false, 'message' => $message], 422);
        } else {
            $api_token = str_replace("Bearer ", "", $api_token);
            $userCount = User::where('api_token', $api_token)->count();
            if ($userCount > 0) {
                // Update User Token to Null
                User::where('api_token', $api_token)->update(['api_token' => NULL]);
                $message = "User Logged out successfully!";
                return response()->json(['status' => true, 'message' => $message], 200);
            }
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

    // this course is pending, because i am having trouble in token & url in description
    public function updateStock(Request $request)
    {
        $header = $request->header('Authorization');
        if (empty($header)) {
            $message = "Header Authorization Token is missing in API Header!";
            return response()->json(['status' =>false, 'message' =>$message], 422);
        } else {
            if ($header == "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkFtaXQgR3VwdGEiLCJpYXQiOjE1MTYyMzkwMjJ9.cNrgi6Sso9wvs4GlJmFnA4IqJY4o2QEcKXgshJTjfNg") {
                // Update Stock API
                // if ($request->isMethod('post')) {
                //     $url = "http://127.0.0.1:8000/api/update-stock";
                //     $curl = curl_init();
                //     curl_setopt($curl, CURLOPT_URL, $url);
                //     curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                //     curl_setopt($curl, CURLOPT_HEADER, false);
                //     $data = curl_exec($curl);
                //     curl_close($curl);
                //     $data = json_decode($data, true);
                //     echo "<pre>"; print_r($data); die;
                // }
            } else {
                $message = "Header Authorization is incorrect";
                return response()->json(['status' => false, 'message' => $message], 422);
            }
        }
    }
}
