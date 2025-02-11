<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Parcoursrequete  extends Model
{

protected $table ='outilcollecte_parcoursrequete';

protected $primaryKey ='id';

public $timestamps = false;

protected $fillable = ['id','idRequete','idEtape',  'idEntite','dateArrivee','dateDepart','sens','idStructure','typeStructure'];

	public function requete(){

		return $this->belongsTo('App\Models\Requete','idRequete','id');
}
	public function etape(){

		return $this->belongsTo('App\Models\Etapecourrier','idEtape','id');
}
	public function structure(){

		return $this->belongsTo('App\Models\Structure','idStructure','id');
}
}

