<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parametre extends Model
{
    //
    protected $table = 'outilcollecte_parametre';

    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
                            'SigleStructure',
                            'LibelleStructure',
                            'LienCourrierDep',
                            'LienCourrierArr',
                            'RepServeurArrivee',
                            'RepServeurDepart',
                            'SiteUn',
                            'SiteDeux',
                            'SiteTrois',
                            'idEntite',
                            'enteteCourrier',
                            'piedPageCourrier',
                            'adresseServeur',
                            'logo',
                            'adresse',
                            'adresseServeurFichier',
                            'created_by',
                            'updated_by',
                          ];

}