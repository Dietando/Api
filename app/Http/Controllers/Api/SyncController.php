<?php

namespace Dietando\Http\Controllers\Api;

use Carbon\Carbon;
use Dietando\Entities\Item;
use Dietando\Entities\Meal;
use Illuminate\Http\Request;

use Dietando\Http\Requests;
use Dietando\Http\Controllers\Controller;

class SyncController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Meal::with('items')->whereHas('accompaniment', function($query) {
            $query->where('user_id_client', '=', auth()->user()->getAuthIdentifier());
        })->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!$request->has(['meals', 'items'])) {
            return [
                'status' => false
            ];
        } else {
            $meals = $request->get('meals');
            $items = $request->get('items');

            if(count($meals) > 0) {
                foreach($meals as $meal) {
                    $model = Meal::whereHas('accompaniment', function($query) {
                        $query->where('user_id_client', '=', auth()->user()->getAuthIdentifier());
                    })->find($meal['id']);

                    if($model) {
                        $model->fill([
                            'check' => $meal['check'],
                            //'checked_at' => new Carbon($meal['checked_at'])
                        ])->save();
                    }
                }
            }

            if(count($items) > 0) {
                foreach($items as $item) {
                    $model = Item::whereHas('meal.accompaniment', function($query) {
                        $query->where('user_id_client', '=', auth()->user()->getAuthIdentifier());
                    })->find($item['id']);

                    if($model) {
                        $model->fill([
                            'check' => $item['check'],
                            //'checked_at' => new Carbon($item['checked_at'])
                        ])->save();
                    }
                }
            }

            return [
                'status' => true
            ];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
