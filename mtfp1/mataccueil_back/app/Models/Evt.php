<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evt extends Model
{
    //
    protected $table = 'outilcollecte_evt';

    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
                            'question',
                            'reponses',
                            'idEntite',
                            'created_by',
                            'updated_by',
                          ];

}