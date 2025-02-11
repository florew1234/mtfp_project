<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Statthematique extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'outilcollecte_statthematique';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'idType','stat',  'idEntite'];


    public function thematique_stat(){
        return $this->belongsTo('App\Models\Type','idType','id');
    }
    

}
