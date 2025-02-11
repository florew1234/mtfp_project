<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class EchangeWhat  extends Model
{

protected $table ='outilcollecte_save_whatsapp';

protected $primaryKey ='id';

public $timestamps = true;

protected $fillable = ['id','numerowhatsapp','id_user_savediscu','id_userTraite','discussions','id_req', 'traite_disc','reponse_agent', 'fichier_joint','created_at','updated_at'];
	
	public function creator(){
		return $this->belongsTo('App\Models\Utilisateur','id_user_savediscu','id');
	}

	public function userTrait(){
		return $this->belongsTo('App\Models\Utilisateur','id_userTraite','id');
	}
	
	public function requete(){
		return $this->belongsTo('App\Models\Requete','id_req','id');
	}


}

