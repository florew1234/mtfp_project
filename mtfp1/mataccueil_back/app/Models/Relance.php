<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Relance extends Model
{
    //
    protected $table = 'outilcollecte_relance';

    protected $primaryKey = 'id';
   // public $timestamps = true;

    protected $fillable = [
                            'id',
                            'date_envoi',
                            'message',
                            'idEntite',
                            'idStructure',
                            'idStructureOrdonatrice',
                            'idRequete',
                            'etat'
                          ];
    public function structure(){
      return $this->belongsTo('App\Models\Structure','idStructure','id');
    }

}