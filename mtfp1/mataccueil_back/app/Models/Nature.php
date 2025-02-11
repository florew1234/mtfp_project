<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Nature  extends Model
{

protected $table ='outilcollecte_nature';

protected $primaryKey ='id';

public $timestamps = true;

protected $fillable = ['id','CodeNature','libelle',  'idEntite','created_by','updated_by'];

}

