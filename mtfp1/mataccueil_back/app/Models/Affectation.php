<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Affectation extends Model
{

protected $table ='outilcollecte_affectation';

protected $primaryKey ='id';

public $timestamps = true;

protected $fillable = ['id','idRequete','typeStructure','idEntite','idStructure','dateAffectation','dateEnvoiReponse','textetReponseApportee','SiReponseDisponible','created_by','updated_by'];

	public function requetes(){

		return $this->belongsTo('App\Models\Requete','idRequete','id');
}
	public function structure(){

		return $this->belongsTo('App\Models\Structure','idStructure','id');
    }
    public function entiteAdmin(){
        // return $this->belongsTo('App\Models\EntiteAdmin','idEntite','id');
        return $this->belongsTo('App\Models\Institution','idEntite','id');
	}

	/*public function usager()
    {
        return $this->belongsToMany('App\Models\Usager','outilcollecte_requete','idRequete','idUsager');
    }*/


    /*
    public function prestation()
    {
        return $this->requete->store;
    }*/

}

