<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Commune  extends Model
{

protected $table ='outilcollecte_commune';

protected $primaryKey ='id';

public $timestamps = true;

protected $fillable = ['id','libellecom','depart_id'];



public function departement(){
    return $this->belongsTo('App\Models\Departement','depart_id','id');
}
}

