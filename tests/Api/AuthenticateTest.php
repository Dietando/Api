<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AuthenticateTest extends TestCase
{
    public function testUnauthorizedWithoutAuthToken()
    {
        $response = $this->call('POST', '/api/teste');

        $this->assertResponseStatus(401, $response->status());
    }

    public function testUnauthorizedInvalidToken()
    {
        $response = $this->call('POST', '/api/teste', [
            'auth_token' => md5(str_random(4))
        ]);

        $this->assertResponseStatus(401, $response->status());
        $this->assertEquals('Unauthorized', $response->getContent());
    }

    public function testAuthorized()
    {
        $token = Dietando\Entities\AuthToken::firstOrNew([]);

        $this->post('/api/teste', [
            'auth_token' => $token->token,
        ])->seeJsonEquals([
            'status' => 'ok'
        ]);
    }

    public function testLoginFail()
    {
        $user = factory(Dietando\Entities\User::class)->make();

        $this->post('/api/auth/login', [
            'email' => $user->email,
            'password' => $user->password
        ])->seeJson([
            'attempt' => false
        ]);
    }

    public function testLoginOk()
    {
        $password = str_random(10);
        $user = factory(Dietando\Entities\User::class)->create([
            'password' => bcrypt($password)
        ]);

        $this->post('/api/auth/login', [
            'email' => $user->email,
            'password' => $password
        ])->seeJson([
            'attempt' => true
        ]);
    }

    public function testGetUser()
    {
        $password = str_random(10);
        $user = factory(Dietando\Entities\User::class)->create([
            'password' => bcrypt($password)
        ]);

        $this->post('/api/auth/login', [
            'email' => $user->email,
            'password' => $password
        ])->seeJson([
            'attempt' => true
        ]);

        $this->post('/api/auth/user', [
            'auth_token' => json_decode($this->response->getContent())->token
        ])->seeJsonEquals([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at->toDateTimeString(),
            'updated_at' => $user->updated_at->toDateTimeString()
        ]);
    }
}
