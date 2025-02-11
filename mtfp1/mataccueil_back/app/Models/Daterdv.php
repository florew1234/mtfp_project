<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Daterdv  extends Model
{

protected $table ='outilcollecte_date_rdv';

protected $primaryKey ='id';

public $timestamps = true;

protected $fillable = ['id','dateChoisi',  'idEntite','created_by','updated_by'];

}

