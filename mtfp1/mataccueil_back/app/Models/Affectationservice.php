<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Affectationservice  extends Model
{

protected $table ='outilcollecte_affectationservice';

protected $primaryKey ='id';

public $timestamps = true;

protected $fillable = ['id','idRequete','idStructure','idEntite','dateAffectation','dateEnvoiReponse','textetReponseApportee','SiReponseDisponible','created_by','updated_by'];

	public function requete(){

		return $this->belongsTo('App\Models\Requete','idRequete','id');
}
	public function structure(){

		return $this->belongsTo('App\Models\Structure','idStructure','id');
  }
  public function entiteAdmin(){
	return $this->belongsTo('App\Models\EntiteAdmin','idEntite','id');
  }
}

