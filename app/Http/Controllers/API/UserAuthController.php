<?php

namespace App\Http\Controllers\API;

use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class UserAuthController extends Controller
{
    protected $guard = 'api';
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
    public function login(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'username' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(array('msg' => 'error', 'response' => $validator->errors(), 422));
        }
        $credentials = [
            'phone_no' => $data['phone_no'],
            'password' => $data['password'],
        ];
        if ($token = auth()->attempt($credentials)) {
            $user = auth()->user();
            if ($user->is_blocked == 1) {
                auth()->logout();
                session()->flush();
                return response()->json(['msg' => 'error', 'response' => 'Your account has been blocked by admin due to violation. Please contact support team.'], 401);
            } else if ($user->status == 0) {
                auth()->logout();
                session()->flush();
                return response()->json(['msg' => 'error', 'response' => 'You have inactivated your account. Please contact support team to get it reactivated.'], 401);
            }
            // if (isset($data['device_token'])) {
            //     $user->device_token = $data['device_token'];
            //     $user->save();
            // }
            $response = 'User Logged In Successfully';
            return response()->json([
                'msg' => 'success',
                'response' => $response,
                'token' => $this->respondWithToken(JWTAuth::fromUser(auth()->user())),
                'user' => auth()->user(),
            ]);
        }
        return response()->json(['msg' => 'error', 'response' => 'Invalid credentials!'], 401);
    }
    public function register(Request $request)
    {
        // dd($request->all());
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required',
            'email' => 'email|required',
            'phone' => 'required|unique:users,phone',
            'ssn' => 'required|unique:users,ssn',
            'username' => 'required|unique:users,username',
            'password' => 'required|min:8',
            'address' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(array('msg' => 'error', 'response' => $validator->errors(), 422));
        }

        $data['otp'] = $this->generateOTP();
        $data['password_decrypt'] = $data['password'];
        $data['password'] = bcrypt($data['password']);

        $user = User::create($data);

        if ($user) {
            return response()->json(['msg' => 'success', 'response' => 'User Registered Successfully', 'user' => $user]);
            // $credentials = [
            //     'phone_no' => $data['phone_no'],
            //     'password' => $data['password_decrypt'],
            // ];
            // if (!$token = auth()->attempt($credentials)) {
            //     return response()->json(['msg' => 'error', 'response' => 'Could Not Authenticate After Account Creation!'], 401);
            // }
            // return response()->json([
            //     'msg' => 'success',
            //     'response' => 'User Registered Successfully',
            //     'token' => $this->respondWithToken(JWTAuth::fromUser(auth()->user())),
            //     'user' => $user,
            // ]);
        } else {
            return response()->json(['msg' => 'error', 'response' => 'Something went wrong! Could Not Create User.']);
        }
    }
    function generateOTP()
    {
        $otp = mt_rand(100000, 999999);

        if (User::where('otp', $otp)->exists()) {
            return $this->generateOTP();
        }

        return $otp;
    }
    public function user_profile()
    {
        $user = auth()->user();
        return response()->json(['msg' => 'success', 'response' => 'success', 'data' => $user]);
    }
    public function logout()
    {
        auth()->logout();
        return response()->json(['msg' => 'success', 'response' => 'Successfully logged out']);
    }
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Carbon::now()->addDays(5)->timestamp,
            // 'expires_in' => JWTAuth::factory()->getTTL() * 2880,
        ]);
    }
}

// Users Table:
// id (Primary Key)
// name
// email
// phone
// ssn
// username
// password
// address
// card_no

// Accounts Table:
// id (Primary Key)
// user_id (Foreign Key referencing Users Table)
// account_number
// balance

// Transactions Table:
// id (Primary Key)
// account_id (Foreign Key referencing Accounts Table)
// txn_date
// txn_type (e.g., 'deposit', 'withdrawal', 'transfer')
// amount
// sender_account_id (Foreign Key referencing Accounts Table)
// receiver_account_id (Foreign Key referencing Accounts Table)
// post_balance