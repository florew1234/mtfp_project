<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'outilcollecte_service';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['id', 'libelle','consiste',  'idEntite','interetDemandeur','obligatoire','echeance','interetDemanderTot'
    ,'dateredac','nomSousG','nomPresidentSG','contactPresidentSG','idType','hide_for_public', 'idParent','piecesAFournir','delai',
    'published_by','published_at','access_online','access_url','submited','view_url',
    'delaiFixe','nbreJours','cout', 'lieuDepot','lieuRetrait','textesRegissantPrestation','created_by','published', 'updated_by'];

    public function service_parent(){
        return $this->belongsTo('App\Models\Structure','idParent','id');
    }

    public function type(){
        return $this->belongsTo('App\Models\Type','idType','id');
    }

    public function listepieces(){
        return $this->hasMany('App\Models\Pieceprestation','idService','id');
    }

}
