<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Reponserapide  extends Model
{

protected $table ='outilcollecte_reponserapide';

protected $primaryKey ='id';

public $timestamps = true;

protected $fillable = ['id','emailrecevier',  'idEntite','codeRequete','emailstructure','message','complement','type','typerReceiver','receiver','fichier_joint'];

}

