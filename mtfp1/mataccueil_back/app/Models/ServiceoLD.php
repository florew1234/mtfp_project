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
    protected $fillable = ['id', 'libelle','description','idType', 'idParent','piecesAFournir','delai','delaiFixe','nbreJours','cout', 'lieuDepot','lieuRetrait','textesRegissantPrestation','created_by', 'updated_by'];

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
