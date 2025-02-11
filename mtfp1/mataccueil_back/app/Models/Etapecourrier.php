<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Etapecourrier extends Model
{
    //
    protected $table = 'outilcollecte_etape';

    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
                            'CodeEtapeCourrier',
                            'LibelleEtape',
                            'LibelleEtapeUs',
                            'idEntite',
                            'created_by',
                            'updated_by',
                          ];

}