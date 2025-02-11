<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Structure extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'outilcollecte_structure';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.  structure.sous_structure.sigle
     *
     * @var array
     */
    protected $fillable = ['id', 'libelle','sigle', 'idEntite','contact','idParent','created_by', 'updated_by','active','type_s'];

    
    public function structure_parent(){
        return $this->belongsTo('App\Models\Institution','idEntite','id');
    }

    public function parent(){
        return $this->belongsTo('App\Models\Structure','idParent','id');
    }

    public function agent(){
        return $this->hasOne('App\Models\Acteur','idStructure');
    }

    public function services() 
    {
        return $this->hasMany('App\Models\Service','idParent','id');
    }

    public function sous_structure() 
    {
        return $this->belongsTo('App\Models\Structure','idParent','id');
    }


}
