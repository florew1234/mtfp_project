<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Rdvjour  extends Model
{

protected $table ='outilcollecte_rdv_jour';

protected $primaryKey ='id';

public $timestamps = true;

protected $fillable = ['id','jour',  'idEntite','created_by','updated_by'];

}

