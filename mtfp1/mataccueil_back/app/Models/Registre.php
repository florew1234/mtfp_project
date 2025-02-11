<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Registre  extends Model
{

protected $table ='outilcollecte_registre';

protected $primaryKey ='id';

public $timestamps = true;

protected $fillable = ['id','matri_telep','nom_prenom','idEntite','plainte','contenu_visite', 'satisfait','motif_non','observ_visite','idreq','created_at','created_by','sex'];

	public function entite(){
		return $this->belongsTo('App\Models\Institution','idEntite','id');
	}
	
	public function creator(){
		return $this->belongsTo('App\Models\Utilisateur','created_by','id');
	}
	
	public function requete(){
		return $this->belongsTo('App\Models\Requete','idreq','id');
	}


}

