<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Relanceback extends Model
{
    //
    protected $table = 'outilcollecte_relance_back';

    protected $primaryKey = 'id';
   // public $timestamps = true;

    protected $fillable = [
                            'id',
                            'message_r',
                            'idStructure',
                            'nbre_r',
                            'datenext_r',
                            'created_at'
                          ];
}