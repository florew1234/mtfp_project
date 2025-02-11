<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Denonciation  extends Model
{

protected $table ='outilcollecte_denonciation';

protected $primaryKey ='id';

public $timestamps = true;

protected $fillable = ['id','resume','nom','prenoms',  'pj','email','phone','entity_name','created_at','updated_at'];

}

