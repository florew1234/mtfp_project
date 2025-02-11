<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RelanceParam extends Model {

    //
    protected $table = 'outilcollecte_param_relance';

    protected $primaryKey = 'id';
   // public $timestamps = true; user_agent

    protected $fillable = [
                            'id',
                            'ordre_relance',
                            'msg_relance',
                            'idEntite',
                            'id_user',
                            'apartir_de'
                          ];

    public function user_(){
        return $this->belongsTo('App\Models\Utilisateur','id_user','id');
    }

}