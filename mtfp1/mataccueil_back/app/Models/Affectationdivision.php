<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Affectationdivision  extends Model
{

protected $table ='outilcollecte_affectationdivision';

protected $primaryKey ='id';

public $timestamps = true;

protected $fillable = ['id','idRequete','idEntite','idStructure','dateAffectation','dateEnvoiReponse','texteReponseApportee','SiReponseDisponible','created_by','updated_by'];

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

