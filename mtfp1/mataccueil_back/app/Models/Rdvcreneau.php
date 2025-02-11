<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Rdvcreneau  extends Model
{

protected $table ='outilcollecte_rdv_creneau';

protected $primaryKey ='id';

public $timestamps = true;

protected $fillable = ['id','heureDebut','heureFin',  'idEntite','created_by','updated_by'];

}

