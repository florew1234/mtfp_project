<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Suggestion  extends Model
{

protected $table ='outilcollecte_suggestion';

protected $primaryKey ='id';

public $timestamps = true;

protected $fillable = ['id','message','nomEmetteur','plateforme',  'idEntite','emailEmetteur','emailRecepteur','created_at','updated_at'];
}

