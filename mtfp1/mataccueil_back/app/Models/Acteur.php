<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Acteur  extends Model
{

protected $table ='outilcollecte_acteur';

protected $primaryKey ='id'; //user_agent.id

public $timestamps = true;

protected $fillable = ['id','nomprenoms','idTypeacteur','idEntite','idStructure','idCom','created_by','updated_by'];

	public function structure(){
		return $this->belongsTo('App\Models\Structure','idStructure','id');
	}

	public function commune(){
		return $this->belongsTo('App\Models\Commune','idCom','id');
	}
	public function depart(){
		return $this->belongsTo('App\Models\Departement','idDepart','id');
	}

	public function entiteAdmin(){
		return $this->belongsTo('App\Models\EntiteAdmin','idEntite','id');
	}

	public function user_agent(){
        return $this->belongsTo('App\Models\Utilisateur','id','idagent');
    }
}

