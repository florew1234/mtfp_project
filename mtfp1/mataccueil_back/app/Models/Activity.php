<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity  extends Model
{

	protected $table ='outilcollecte_activity_logs';

	protected $primaryKey ='id_log';

	public $timestamps = true;

	protected $fillable = ['id_log','last_connect','id_user','last_login','last_logout','activity'];

    public static function SaveActivity($idUser,$log){

		$lastConne = self::where('id_user',$idUser)->where('last_logout',null)->first();

		if($lastConne){
			self::where('id_user',$idUser)->where('last_logout',null)->update([
				"activity"=> $lastConne->activity.$log.' * '
			]);
		}
    }


	function user() {
		return $this->belongsTo(Utilisateur::class,'id_user');	
	}
   
}

