<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AuthenticateTest extends TestCase
{
    use DatabaseTransactions;

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

    public function testRegisterFail()
    {
        $fake = factory(\Dietando\Entities\User::class)->create();

        $this->post('/api/auth/register', [])
            ->seeJson(['status' => 'empty_fields']);

        $this->post('/api/auth/register', [
            'name' => 'teste',
            'email' => 'teste',
            'password' => 123,
            'password_confirmation' => 321
        ])->seeJson(['status' => 'passwords_do_not_match']);

        $this->post('/api/auth/register', [
            'name' => 'teste',
            'email' => 'teste',
            'password' => 123,
            'password_confirmation' => 123
        ])->seeJson(['status' => 'invalid_email']);

        $this->post('/api/auth/register', [
            'name' => 'teste',
            'email' => $fake->email,
            'password' => 123,
            'password_confirmation' => 123
        ])->seeJson(['status' => 'email_exists']);
    }

    public function testRegisterOk()
    {
        $password = '123456';
        $user = factory(\Dietando\Entities\User::class)->make([
            'password' => bcrypt($password)
        ]);

        $this->post('/api/auth/register', [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $password,
            'password_confirmation' => $password
        ])->seeJson([
            'status' => 'ok'
        ]);

        $response = $this->decodeResponseJson();

        $this->assertEquals($user->name, $response['user']['name']);
        $this->assertEquals($user->email, $response['user']['email']);
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
