<?php

namespace Dietando\Entities;

use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    /**
     * A tabela a ser utilizada pela model.
     *
     * @var string
     */
    protected $table = "meals";

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array
     */
    protected $fillable = [
        'accompaniment_id',
        'title',
        'time',
        'begin_date',
        'end_date',
        'checked',
        'checked_at'
    ];

    /**
     * Os atributos a serem mutados como data
     *
     * @var array
     */
    protected $dates = [
        'time',
        'begin_date',
        'end_date',
        'checked_at'
    ];

    /**
     * As definições de atributo.
     *
     * @var array
     */
    protected $casts = [
        'checked' => 'boolean'
    ];

    /**
     * Relacionamento com a refeição.
     *
     * @return User
     */
    public function accompaniment()
    {
        return $this->belongsTo(Accompaniment::class, 'accompaniment_id');
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
