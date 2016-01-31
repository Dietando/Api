<?php

namespace Dietando\Http\Controllers\Api;

use Dietando\Entities\AuthToken;
use Dietando\Entities\User;
use Illuminate\Http\Request;

use Dietando\Http\Requests;
use Dietando\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * AuthController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth.api', [
            'only' => 'postUser'
        ]);
    }

    /**
     * Verifica e autentica o usuário.
     *
     * @param Request $request
     * @return array
     */
    public function postLogin(Request $request)
    {
        if(!$request->has(['email', 'password'])) {
            return [
                'attempt' => false
            ];
        } else {
            $attempt = Auth::attempt($request->only(['email', 'password']));

            if(!$attempt) {
                return [
                    'attempt' => false
                ];
            } else {
                AuthToken::create([
                    'user_id' => auth()->user()->id,
                    'token' => $token = md5(str_random()."_auth_token_".time())
                ]);

                return [
                    'attempt' => true,
                    'token' => $token,
                    'user' => auth()->user()
                ];
            }
        }
    }

    /**
     * Cadastra o usuário.
     *
     * @param Request $request
     * @return array
     */
    public function postRegister(Request $request)
    {
        if(!$request->has(['name', 'email', 'password', 'password_confirmation'])) {
            return [
                'status' => 'empty_fields'
            ];
        }

        if(!hash_equals(strval($request->input('password')), strval($request->input('password_confirmation')))) {
            return [
                'status' => 'passwords_do_not_match'
            ];
        }

        if(!filter_var($request->input('email'), FILTER_VALIDATE_EMAIL)) {
            return [
                'status' => 'invalid_email'
            ];
        }

        if(User::where('email', '=', $request->input('email'))->first()) {
            return [
                'status' => 'email_exists'
            ];
        }

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt(strval($request->input('password')))
        ]);

        AuthToken::create([
            'user_id' => $user->id,
            'token' => $token = md5(str_random().'_register_token')
        ]);

        return [
            'status' => 'ok',
            'token' => $token,
            'user' => $user->toArray()
        ];
    }

    /**
     * Retorna as informações do usuário
     *
     * @return mixed
     */
    public function postUser()
    {
        return auth()->user();
    }
}
