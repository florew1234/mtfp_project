<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Rdvparametre  extends Model
{

protected $table ='outilcollecte_rdv_parametre';

protected $primaryKey ='id';

public $timestamps = true;

protected $fillable = ['id','nombrePoste','dateProchainRdv',  'idEntite','created_by','updated_by'];

}

