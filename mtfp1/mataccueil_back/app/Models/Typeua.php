<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeUA extends Model
{
    //
    protected $table = 'outilcollecte_typeuniteadmin';

    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
                            'CodeTypeUA',
                            'LibelleTypeUA',
                            'created_by',
                            'idEntite',
                            'updated_by',
                          ];

}