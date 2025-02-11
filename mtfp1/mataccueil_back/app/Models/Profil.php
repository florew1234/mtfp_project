<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profil extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'outilcollecte_profil';

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
    protected $fillable = ['id', 'CodeProfil','LibelleProfil', 

    'created_by', 'updated_by','saisie','pointfocal','decisionnel_suivi','inspection','superadmin','admin_sectoriel','saisie_adjoint','validation','sgm','dc','ministre','parametre','direction','service','division','usersimple','niveauvalidation','fichier_guide'];

    //Les utilisateurs ayant ce profil
    public function utilisateurs_lies() {
       return $this->hasMany('App\Models\Utilisateur', 'idprofil', 'idprofil');
   }

}
