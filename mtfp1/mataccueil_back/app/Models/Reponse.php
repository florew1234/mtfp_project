<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Reponse  extends Model
{

protected $table ='outilcollecte_reponse';

protected $primaryKey ='id';

public $timestamps = true;

protected $fillable = ['id','texteReponse','idRequete','idStructure',  'idEntite','typeStructure','siTransmis','dateTransmission','rejete','interrompu','raisonRejet','fichier_joint'];

	public function structure(){

		return $this->belongsTo('App\Models\Structure','idStructure','id');
}
}

