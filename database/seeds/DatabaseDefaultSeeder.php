<?php

use Illuminate\Database\Seeder;

class DatabaseDefaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $password = 'suporte';
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
        Dietando\Entities\Meal::create([
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
        Dietando\Entities\Meal::create([
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
    }
}
