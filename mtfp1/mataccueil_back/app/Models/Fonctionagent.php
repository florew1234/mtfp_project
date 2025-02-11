<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fonctionagent extends Model
{
    //
    protected $table = 'sygec_fonctionagent';

    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
                            'CodeFonction',
                            'LibelleFonction',
                            'TypeFonction',
                            'idEntite',
                            'created_by',
                            'updated_by',
                          ];

}