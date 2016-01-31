<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SyncTest extends TestCase
{
    use DatabaseTransactions;

    public function testGetDataOk()
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

        $token = $this->decodeResponseJson()['token'];

        $this->get('/api/sync', [
            'auth_token' => $token
        ])->isJson();
    }

    public function testCreateFakerUsers()
    {
        $password = str_random(10);
        $users = factory(Dietando\Entities\User::class, 2)->create([
            'password' => bcrypt($password)
        ]);

        $nutritionist = $users[0];
        $client = $users[1];

        $this->assertArrayHasKey('id', $nutritionist, 'Nutritionist fake error.');
        $this->assertArrayHasKey('id', $client, 'Client fake error');
    }

    public function testGetDataList()
    {
        $password = str_random(10);
        $users = factory(Dietando\Entities\User::class, 2)->create([
            'password' => bcrypt($password)
        ]);

        $nutritionist = $users[0];
        $client = $users[1];

        $accompaniment = Dietando\Entities\Accompaniment::create([
            'user_id_nutritionist' => $nutritionist->id,
            'user_id_client' => $client->id,
            'begin_date' => Carbon\Carbon::tomorrow(),
            'end_date' => new Carbon\Carbon('6 months')
        ]);

        //----------------------------------------------------
        // Café da Manhã
        //----------------------------------------------------
        $meal = Dietando\Entities\Meal::create([
            'accompaniment_id' => $accompaniment->id,
            'title' => 'Café da Manhã',
            'time' => Carbon\Carbon::createFromTime(9, 0),
            'begin_date' => Carbon\Carbon::tomorrow(),
            'end_date' => new Carbon\Carbon('2 months')
        ]);

        // Pão
        Dietando\Entities\Item::create([
            'meal_id' => $meal->id,
            'item' => 'Pão Francês',
            'quantity' => 1,
            'unity' => 'Unidade'
        ]);

        // Leite
        Dietando\Entities\Item::create([
            'meal_id' => $meal->id,
            'item' => 'Leite',
            'quantity' => 1,
            'unity' => 'Copo 350ml'
        ]);

        // Biscoito de Sal
        Dietando\Entities\Item::create([
            'meal_id' => $meal->id,
            'item' => 'Biscoito de Sal',
            'quantity' => 1,
            'unity' => 'Unidade'
        ]);

        //----------------------------------------------------
        // Almoço
        //----------------------------------------------------
        $meal = Dietando\Entities\Meal::create([
            'accompaniment_id' => $accompaniment->id,
            'title' => 'Almoço',
            'time' => Carbon\Carbon::createFromTime(12, 0),
            'begin_date' => Carbon\Carbon::tomorrow(),
            'end_date' => new Carbon\Carbon('2 months')
        ]);

        // Arroz
        Dietando\Entities\Item::create([
            'meal_id' => $meal->id,
            'item' => 'Arroz',
            'quantity' => 3,
            'unity' => 'Colher'
        ]);

        // Feijão
        Dietando\Entities\Item::create([
            'meal_id' => $meal->id,
            'item' => 'Feijão',
            'quantity' => 2,
            'unity' => 'Colher'
        ]);

        // Bife de Frango
        Dietando\Entities\Item::create([
            'meal_id' => $meal->id,
            'item' => 'Bife de Frango',
            'quantity' => 1,
            'unity' => 'Unidade'
        ]);

        // Alface
        Dietando\Entities\Item::create([
            'meal_id' => $meal->id,
            'item' => 'Bife de Frango',
            'quantity' => 2,
            'unity' => 'Folha'
        ]);

        //----------------------------------------------------
        // Janta
        //----------------------------------------------------
        $meal = Dietando\Entities\Meal::create([
            'accompaniment_id' => $accompaniment->id,
            'title' => 'Janta',
            'time' => Carbon\Carbon::createFromTime(19, 0),
            'begin_date' => Carbon\Carbon::tomorrow(),
            'end_date' => new Carbon\Carbon('2 months')
        ]);

        // Arroz
        Dietando\Entities\Item::create([
            'meal_id' => $meal->id,
            'item' => 'Arroz',
            'quantity' => 3,
            'unity' => 'Colher'
        ]);

        // Feijão
        Dietando\Entities\Item::create([
            'meal_id' => $meal->id,
            'item' => 'Feijão',
            'quantity' => 2,
            'unity' => 'Colher'
        ]);

        // Ovo Frito
        Dietando\Entities\Item::create([
            'meal_id' => $meal->id,
            'item' => 'Ovo Frito',
            'quantity' => 1,
            'unity' => 'Unidade'
        ]);

        // Tomate
        Dietando\Entities\Item::create([
            'meal_id' => $meal->id,
            'item' => 'Bife de Frango',
            'quantity' => 3,
            'unity' => 'Unidade'
        ]);

        //----------------------------------------------------
        // Autenticação
        //----------------------------------------------------
        $this->post('/api/auth/login', [
            'email' => $client->email,
            'password' => $password
        ])->seeJson([
            'attempt' => true
        ]);

        $token = $this->decodeResponseJson()['token'];

        //----------------------------------------------------
        // Obter Lista
        //----------------------------------------------------
        $this->get('/api/sync?auth_token='.$token);

        $this->assertResponseStatus(200);
        $this->isJson();

        //----------------------------------------------------
        // Checar Lista
        //----------------------------------------------------
        $list = Dietando\Entities\Meal::with('items')->whereHas('accompaniment', function($query) use($client) {
            $query->where('user_id_client', '=', $client->id);
        })->get();

        $this->seeJsonEquals($list->toArray());
    }

    public function testUpdateItemsCheck()
    {
        $password = str_random(10);
        $users = factory(Dietando\Entities\User::class, 2)->create([
            'password' => bcrypt($password)
        ]);

        $nutritionist = $users[0];
        $client = $users[1];

        $accompaniment = Dietando\Entities\Accompaniment::create([
            'user_id_nutritionist' => $nutritionist->id,
            'user_id_client' => $client->id,
            'begin_date' => Carbon\Carbon::tomorrow(),
            'end_date' => new Carbon\Carbon('6 months')
        ]);

        $meal = Dietando\Entities\Meal::create([
            'accompaniment_id' => $accompaniment->id,
            'title' => 'Café da Manhã',
            'time' => Carbon\Carbon::createFromTime(9, 0),
            'begin_date' => Carbon\Carbon::tomorrow(),
            'end_date' => new Carbon\Carbon('2 months')
        ]);

        Dietando\Entities\Item::create([
            'meal_id' => $meal->id,
            'item' => 'Pão Francês',
            'quantity' => 1,
            'unity' => 'Unidade'
        ]);

        Dietando\Entities\Item::create([
            'meal_id' => $meal->id,
            'item' => 'Leite',
            'quantity' => 1,
            'unity' => 'Copo 350ml'
        ]);

        //----------------------------------------------------
        // Autenticação
        //----------------------------------------------------
        $this->post('/api/auth/login', [
            'email' => $client->email,
            'password' => $password
        ])->seeJson([
            'attempt' => true
        ]);

        $token = $this->decodeResponseJson()['token'];

        //----------------------------------------------------
        // Obter Itens
        //----------------------------------------------------
        $this->get('/api/sync?auth_token='.$token)
            ->isJson();

        $itemsData = $this->decodeResponseJson()[0]['items'];

        //----------------------------------------------------
        // Preparar o Check
        //----------------------------------------------------
        $itemsCheck = [];

        foreach($itemsData as $item) {
            $itemsCheck[] = [
                'id' => $item['id'],
                'check' => true,
                'checked_at' => \Carbon\Carbon::now()->toDateTimeString()
            ];
        }

        //----------------------------------------------------
        // Enviar Checks
        //----------------------------------------------------
        $this->post('/api/sync', [
            'auth_token' => $token,
            'meals' => [],
            'items' => $itemsCheck
        ])->seeJson([
            'status' => true
        ]);

        //----------------------------------------------------
        // Verificar Checks
        //----------------------------------------------------
        $this->get('/api/sync?auth_token='.$token)
            ->isJson();

        $itemsData = $this->decodeResponseJson()[0]['items'];

        foreach($itemsData as $item) {
            $this->assertTrue($item['check']);
        }
    }

    public function testUpdateMealsCheck()
    {
        $password = str_random(10);
        $users = factory(Dietando\Entities\User::class, 2)->create([
            'password' => bcrypt($password)
        ]);

        $nutritionist = $users[0];
        $client = $users[1];

        $accompaniment = Dietando\Entities\Accompaniment::create([
            'user_id_nutritionist' => $nutritionist->id,
            'user_id_client' => $client->id,
            'begin_date' => Carbon\Carbon::tomorrow(),
            'end_date' => new Carbon\Carbon('6 months')
        ]);

        Dietando\Entities\Meal::create([
            'accompaniment_id' => $accompaniment->id,
            'title' => 'Café da Manhã',
            'time' => Carbon\Carbon::createFromTime(9, 0),
            'begin_date' => Carbon\Carbon::tomorrow(),
            'end_date' => new Carbon\Carbon('2 months')
        ])->items()->create([
            'item' => 'Pão Francês',
            'quantity' => 1,
            'unity' => 'Unidade'
        ]);

        Dietando\Entities\Meal::create([
            'accompaniment_id' => $accompaniment->id,
            'title' => 'Café da Manhã',
            'time' => Carbon\Carbon::createFromTime(9, 0),
            'begin_date' => Carbon\Carbon::tomorrow(),
            'end_date' => new Carbon\Carbon('2 months')
        ])->items()->create([
            'item' => 'Pão Francês',
            'quantity' => 1,
            'unity' => 'Unidade'
        ]);

        //----------------------------------------------------
        // Autenticação
        //----------------------------------------------------
        $this->post('/api/auth/login', [
            'email' => $client->email,
            'password' => $password
        ])->seeJson([
            'attempt' => true
        ]);

        $token = $this->decodeResponseJson()['token'];

        //----------------------------------------------------
        // Obter Itens
        //----------------------------------------------------
        $this->get('/api/sync?auth_token='.$token)
            ->isJson();

        $mealsData = $this->decodeResponseJson();

        //----------------------------------------------------
        // Preparar o Check
        //----------------------------------------------------
        $mealsCheck = [];

        foreach($mealsData as $meal) {
            $mealsCheck[] = [
                'id' => $meal['id'],
                'check' => true,
                'checked_at' => \Carbon\Carbon::now()->toDateTimeString()
            ];
        }

        //----------------------------------------------------
        // Enviar Checks
        //----------------------------------------------------
        $this->post('/api/sync', [
            'auth_token' => $token,
            'meals' => $mealsCheck,
            'items' => []
        ])->seeJson([
            'status' => true
        ]);

        //----------------------------------------------------
        // Verificar Checks
        //----------------------------------------------------
        $this->get('/api/sync?auth_token='.$token)
            ->isJson();

        $mealsData = $this->decodeResponseJson();

        foreach($mealsData as $meal) {
            $this->assertTrue($meal['check']);
        }
    }

    public function testUpdateCheckAll()
    {
        $password = str_random(10);
        $users = factory(Dietando\Entities\User::class, 2)->create([
            'password' => bcrypt($password)
        ]);

        $nutritionist = $users[0];
        $client = $users[1];

        $accompaniment = Dietando\Entities\Accompaniment::create([
            'user_id_nutritionist' => $nutritionist->id,
            'user_id_client' => $client->id,
            'begin_date' => Carbon\Carbon::tomorrow(),
            'end_date' => new Carbon\Carbon('6 months')
        ]);

        Dietando\Entities\Meal::create([
            'accompaniment_id' => $accompaniment->id,
            'title' => 'Café da Manhã',
            'time' => Carbon\Carbon::createFromTime(9, 0),
            'begin_date' => Carbon\Carbon::tomorrow(),
            'end_date' => new Carbon\Carbon('2 months')
        ])->items()->create([
            'item' => 'Pão Francês',
            'quantity' => 1,
            'unity' => 'Unidade'
        ]);

        Dietando\Entities\Meal::create([
            'accompaniment_id' => $accompaniment->id,
            'title' => 'Café da Tarde',
            'time' => Carbon\Carbon::createFromTime(9, 0),
            'begin_date' => Carbon\Carbon::tomorrow(),
            'end_date' => new Carbon\Carbon('2 months')
        ])->items()->create([
            'item' => 'Biscoito de Sal',
            'quantity' => 2,
            'unity' => 'Unidade'
        ]);

        //----------------------------------------------------
        // Autenticação
        //----------------------------------------------------
        $this->post('/api/auth/login', [
            'email' => $client->email,
            'password' => $password
        ])->seeJson([
            'attempt' => true
        ]);

        $token = $this->decodeResponseJson()['token'];

        //----------------------------------------------------
        // Obter Itens
        //----------------------------------------------------
        $this->get('/api/sync?auth_token='.$token)
            ->isJson();

        $dataToCheck = $this->decodeResponseJson();

        //----------------------------------------------------
        // Preparar o Check
        //----------------------------------------------------
        $mealsCheck = [];
        $itemsCheck = [];

        foreach($dataToCheck as $meal) {
            $mealsCheck[] = [
                'id' => $meal['id'],
                'check' => true,
                'checked_at' => \Carbon\Carbon::now()->toDateTimeString()
            ];

            foreach($meal['items'] as $item) {
                $itemsCheck[] = [
                    'id' => $item['id'],
                    'check' => true,
                    'checked_at' => \Carbon\Carbon::now()->toDateString()
                ];
            }
        }

        //----------------------------------------------------
        // Enviar Checks
        //----------------------------------------------------
        $this->post('/api/sync', [
            'auth_token' => $token,
            'meals' => $mealsCheck,
            'items' => $itemsCheck
        ])->seeJson([
            'status' => true
        ]);

        //----------------------------------------------------
        // Verificar Checks
        //----------------------------------------------------
        $this->get('/api/sync?auth_token='.$token)
            ->isJson();

        $mealsData = $this->decodeResponseJson();

        foreach($mealsData as $meal) {
            $this->assertTrue($meal['check']);

            foreach($meal['items'] as $item) {
                $this->assertTrue($item['check']);
            }
        }
    }
}
