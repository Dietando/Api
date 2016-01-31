<?php

namespace Dietando\Http\Controllers\Api;

use Dietando\Entities\AuthToken;
use Illuminate\Http\Request;

use Dietando\Http\Requests;
use Dietando\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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
     * Retorna as informações do usuário
     *
     * @return mixed
     */
    public function postUser()
    {
        return auth()->user();
    }
}
