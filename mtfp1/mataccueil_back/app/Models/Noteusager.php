<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Noteusager  extends Model
{

protected $table ='outilcollecte_note_usager';

protected $primaryKey ='id';

public $timestamps = true;

protected $fillable = ['id','codeReq','noteDelai','noteResultat','noteDisponibilite','noteOrganisation','commentaireNotation'];

	public function requete(){
		return $this->belongsTo('App\Models\Requete','codeRequete','code');
	}
}

