<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Elementdecl extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'outilcollecte_elementdecl';

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
    protected $fillable = ['id', 'libelle',  'idEntite','created_by', 'updated_by'];

    public function listeservices(){
        return $this->hasMany('App\Models\Elementdeclservice','idElementdecl','id');
    }   

}
