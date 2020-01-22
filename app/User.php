<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    protected $connection = 'mysql2';
    protected $table = 'masp_museu.usuarios';
    protected $primaryKey = 'cod_usuario';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cod_usuario', 'nome', 'login', 'senha', 'status', 'data_criacao', 'email', 'cod_gestor', 'avatar'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'senha', 'remember_token',
    ];

    public $timestamps = false;

    public function menus ()
    {
        return DB::connection('mysql2')->select("
            SELECT cod_menu,
                   nome,
                   endereco,
                   parent
            FROM   i_menu i
            WHERE  i.cod_menu NOT IN (SELECT cod_menu
                                      FROM   i_menu_usuarios_bloqueados ib
                                      WHERE  ib.cod_usuario = {$this->cod_usuario})
                   AND i.cod_menu IN (SELECT cod_menu
                                      FROM   i_menu_grupos_permitidos
                                      WHERE  cod_grupo IN (SELECT cod_grupo
                                                           FROM   i_grupo_usuarios
                                                           WHERE  cod_usuario = {$this->cod_usuario}
                                                          ))
                   AND ativo = 1
            ORDER  BY parent,
                      nome;");
    }
}