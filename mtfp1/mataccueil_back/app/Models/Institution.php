<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Institution extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'outilcollecte_institution';

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
    protected $fillable = ['id', 'libelle', 'sigle','type', 'nbrjrs_relance','etat_relance','created_by', 'updated_by'];

    

}
