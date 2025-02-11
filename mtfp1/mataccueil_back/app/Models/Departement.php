<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Departement  extends Model
{

protected $table ='outilcollecte_departement';

protected $primaryKey ='id';

public $timestamps = true;

protected $fillable = ['id','code','libelle','created_by','updated_by'];

function communes() : Returntype {
    
}

}

