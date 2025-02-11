<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Requete  extends Model
{

protected $table ='outilcollecte_requete';

protected $primaryKey ='id';

public $timestamps = true;

protected $fillable = ['id','idUsager','idPrestation','objet','msgrequest','idEtape', 'interfaceRequete','traiteOuiNon','idStructureAffecte','idServiceAffecte','idDivisionAffecte','natureRequete','created_by','updated_by','dateReponse','dateRequete','codeRequete','code','horsDelai','rejete','interrompu','raisonRejet','commentaireNotation','finalise', 'visible', 'lien', 'reponseStructure',
'reponseSRUSecondaire','reponseDivision','reponseService','link_to_prestation',
  'fichier_joint','identity','matricule','entity_name',  'idEntite', 'idEntiteReceive','contact','contact_proche','email','created_at','locality','out_year','plateforme','contactUsd'];

	public function usager(){
		return $this->belongsTo('App\Models\Usager','idUsager','id');
	}
	public function nature(){
		return $this->belongsTo('App\Models\Nature','natureRequete','id');
	}
	public function service(){
			return $this->belongsTo('App\Models\Service','idPrestation','id');
	}
	public function etape(){
			return $this->belongsTo('App\Models\Etapecourrier','idEtape','id');
	}

	public function entite(){
			return $this->belongsTo('App\Models\Institution','idEntite','id');
	}
	public function entite_receive(){
		return $this->belongsTo('App\Models\Institution','idEntiteReceive','id');
	}
	public function creator(){
		return $this->belongsTo('App\Models\Utilisateur','created_by','id');
	}

	
	public function reponse(){
		return $this->hasMany('App\Models\Reponse','idRequete','id');
	}
	public function reponses_rapide(){
		return $this->hasMany('App\Models\Reponserapide','codeRequete','codeRequete');
	}

	public function notes(){
			return $this->hasMany('App\Models\Noteusager','codeReq','codeRequete');
	}

	public function affectation(){
			return $this->hasMany('App\Models\Affectation','idRequete','id')->orderBy('id','asc');
	}
	public function relance(){
			return $this->hasMany('App\Models\Relance','idRequete','id')->where('etat','e')->orderBy('id','asc');
	}

	public function lastaffectation(){
			return $this->hasOne('App\Models\Affectation','idRequete','id')->orderBy('id','desc');
	}

	public function parcours(){
			return $this->hasMany('App\Models\Parcoursrequete','idRequete','id')->orderBy('id','asc');
	}

	public function lastparcours(){
			return $this->hasOne('App\Models\Parcoursrequete','idRequete','id')->orderBy('id','desc');
	}
}

