<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Usager  extends Model
{

protected $table ='outilcollecte_usager';

protected $primaryKey ='id';

public $timestamps = true;

protected $fillable = ['id','nom','prenoms','email','password','code','codeComplet','tel','idDepartement','created_by','updated_by',  "password_reset_code", "password_reset_expiration"];

	public function departement(){
		return $this->belongsTo('App\Models\Departement','idDepartement','id');
	}
	
}

