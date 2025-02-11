<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Rdv  extends Model
{

protected $table ='outilcollecte_rdv';

protected $primaryKey ='id';

public $timestamps = true;

protected $fillable = ['id','idUsager','objet','idRdvCreneau','codeRequete',  'idEntite','dateRdv','statut','attente','idStructure'];
	public function usager(){
		return $this->belongsTo('App\Models\Usager','idUsager','id');
	}
	public function rdvcreneau(){
		return $this->belongsTo('App\Models\Rdvcreneau','idRdvCreneau','id');
	}
	public function requete(){
		return $this->belongsTo('App\Models\Requete','codeRequete','codeRequete');
	}

	public function structure(){
		return $this->belongsTo('App\Models\Structure','idStructure','id');
	}

}

