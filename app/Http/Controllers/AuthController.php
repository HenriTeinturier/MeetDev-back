<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Users;
use App\Models\Developers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['register', 'registerDev', 'registerRecrut', 'login', 'refresh', 'logout']]);
    }


    /*public function register(Request $request)
    {
        $this->validate($request, [
            'email_address' => 'required|unique:users,email_address,1,id',
            'password' => 'required|confirmed'
        ]);

        $email_address = $request->email_address;
        $password = Hash::make($request->password);

        $user = User::create(['email_address' => $email_address, 'password' => $password]);

        return $user;
        return response()->json(['status' => 'success', 'operation' => 'created']);
    }*/

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only(['email_address', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }



    public function register(Request $request)
    {
        /*$this->validate($request, [
            'email_address' => 'required|unique:users,email_address,1,id',
            'password' => 'required|confirmed'
        ]);*/

        //check if user email address exists in DB, if not proceed to creation
        if (Users::where('email_address', '=', $request->email_address)->exists()) {
            return response()->json(['status' => 'error', 'message' => 'email address already existing in database'], 400);
        } else {
            try {
                $user = new User();
                $user->lastname = $request->lastname;
                $user->firstname = $request->firstname;
                $user->city = $request->city;
                $user->department = $request->department;
                $user->zip_code = $request->zip_code;
                $user->email_address = $request->email_address;
                $hashedPassword = Hash::make($request->password);
                $user->password = $hashedPassword;
                $user->phone = $request->phone;
                $user->subscribe_to_push_notif = $request->subscribe_to_push_notif;
                $user->profile_picture = $request->profile_picture;


                if ($user->save()) {
                    try {
                        $developer = new Developers();
                        $developer->label = $request->label;
                        $developer->description = $request->description;
                        $developer->available_for_recruiters = $request->available_for_recruiters;
                        $developer->available_for_developers = $request-> available_for_developers;
                        $developer->minimum_salary_requested = $request->minimum_salary_requested;
                        $developer->maximum_salary_requested = $request->maximum_salary_requested;
                        $developer->age = $request->age;
                        $developer->languages = $request->languages;
                        $developer->years_of_experience = $request->years_of_experience;
                        $developer->english_spoken = $request->english_spoken;
                        $developer->github_link = $request->github_link;
                        $developer->portfolio_link = $request->portfolio_link;
                        $developer->other_link = $request->other_link;

                        if ($developer->save()) {
                            $devId = $developer->id;
                            $user->dev_id = $devId;

                            if ($user->save()) {
                                $token = auth()->login($user);

                                return response()->json(['status' => 'success', 'message' =>'Developer user created successfully and language saved', 'general' => $user, 'spec' => $developer, 'token' => $this->respondWithToken($token)]);
                            } else {
                                return response()->json(['status' => 'error', 'message' => 'Language not saved'], 400);
                            }

                            if ($user->save()) {
                                return response()->json(['status' => 'success', 'message' =>'Developer user created successfully']);
                            }
                        }
                    } catch (\Exception $e) {
                        $user->delete();
                        return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
                    }
                }
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
            }
        }
    }
}



    // /**
    //  * Get a JWT via given credentials.
    //  *
    //  * @param  Request  $request
    //  * @return Response
    //  */
    // public function login(Request $request)
    // {

    //     $this->validate($request, [
    //         'email' => 'required|string',
    //         'password' => 'required|string',
    //     ]);

    //     $credentials = $request->only(['email', 'password']);

    //     if (! $token = Auth::attempt($credentials)) {
    //         return response()->json(['message' => 'Unauthorized'], 401);
    //     }

    //     return $this->respondWithToken($token);
    // }

    //  /**
    //  * Get the authenticated User.
    //  *
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function me()
    // {
    //     return response()->json(auth()->user());
    // }

    // /**
    //  * Log the user out (Invalidate the token).
    //  *
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function logout()
    // {
    //     auth()->logout();

    //     return response()->json(['message' => 'Successfully logged out']);
    // }

    // /**
    //  * Refresh a token.
    //  *
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function refresh()
    // {
    //     return $this->respondWithToken(auth()->refresh());
    // }

    // /**
    //  * Get the token array structure.
    //  *
    //  * @param  string $token
    //  *
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // protected function respondWithToken($token)
    // {
    //     return response()->json([
    //         'access_token' => $token,
    //         'token_type' => 'bearer',
    //         'user' => auth()->user(),
    //         'expires_in' => auth()->factory()->getTTL() * 60 * 24
    //     ]);
    // }