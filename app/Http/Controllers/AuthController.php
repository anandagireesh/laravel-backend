<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Services\ApiResponseService;

class AuthController extends Controller
{
    protected $response;

    public function __construct(ApiResponseService $response)
    {
        $this->response = $response;
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);
     
        if($validator->fails()){
            return $this->response->error('Validation failed', $validator->errors(), 422);       
        }
     
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('passportToken')->accessToken;
        $success['name'] =  $user->name;
   
        return $this->response->success($success, 'User registered successfully.');
        // return response()->json([
        //         'user' => Auth::user(), 
        //         'token' => $success
        //     ], 200);
        // return $this->sendResponse($success, 'User register successfully.');
    }

    public function login(Request $request)
    {
        $credentials = [
            'email'    => $request->email,
            'password' => $request->password
        ];
  
        if (Auth::attempt($credentials)) 
        {
            $token = Auth::user()->createToken('passportToken')->accessToken;
            
            $success['user'] =  Auth::user();
            $success['token'] =  $token;

            return $this->response->success($success, 'User loggedin successfully.');
        }
  
        return $this->response->error('Unauthorised',[
            'error' => 'Unauthorised'
        ] , 401);
       
  
    }
}
