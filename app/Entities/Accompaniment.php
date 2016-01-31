<?php

namespace Dietando\Entities;

use Illuminate\Database\Eloquent\Model;

class Accompaniment extends Model
{
    /**
     * Tabela a ser utilizada pela model.
     *
     * @var string
     */
    protected $table = "accompaniments";

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array
     */
    protected $fillable = [
        'user_id_nutritionist',
        'user_id_client',
        'begin_date',
        'end_date'
    ];

    /**
     * Relacionamento com o usuário nutricionista.
     *
     * @return User
     */
    public function nutritionist()
    {
        return $this->belongsTo(User::class, 'user_id_nutritionist');
    }

    /**
     * Relacionamento com o usuário cliente.
     *
     * @return User
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'user_id_client');
    }
}
