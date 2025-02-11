<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Pieceprestation  extends Model
{

protected $table ='outilcollecte_pieceprestation';

protected $primaryKey ='id';

public $timestamps = true;

protected $fillable = ['id','idService','libellePiece','created_by','updated_by'];

	public function Service(){

		return $this->belongsTo('App\Models\Service','idService','id');
}
}

