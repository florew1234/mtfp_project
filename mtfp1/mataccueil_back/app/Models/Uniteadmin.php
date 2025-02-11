<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class Uniteadmin extends Model
{
    //
    protected $table = 'outilcollecte_uniteadmin';
    protected $fillable = [
                            'CodeUA',
                            'LibelleUA',
                            'SigleUA',
                            'CodeTypeUA',
                            'UAParent',
                            'idEntite',
                            'email',
                            'created_by',
                            'updated_by',
                          ];

                          // Récupération du type de l'UA
    public function ua_typeua(){
        return $this->belongsTo('App\Models\Typeua','CodeTypeUA','id');
    }

    public function getLibelleUaAttribute(){
        $listetypeuas = Typeua::all();
        foreach ($listetypeuas as $typeua) {
            if($this->CodeTypeUA==$typeua->id)
                return $typeua->LibelleTypeUA;
        }
    }

}