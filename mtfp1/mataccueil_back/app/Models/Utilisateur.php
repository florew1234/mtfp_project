<?php
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

//use App\Models\Profil;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Utilisateur extends Model implements AuthenticatableContract, CanResetPasswordContract,JWTSubject
{
    use Authenticatable, CanResetPassword;

    public function getJWTIdentifier()

    {

    return $this->getKey();

    }

    /**

    * Return a key value array, containing any custom claims to be added to the JWT.

    *

    * @return array

    */

    public function getJWTCustomClaims() : array

    {

    return [];

    }
	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'outilcollecte_users';

    /**
     * The primary key for the model.
     *
     * @var string
     */
	protected $primaryKey = 'id';
    public $timestamps = true;

/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	//  protected $fillable = [
    //     'name', 'email', 'password','idprofil','idagent',  'idEntite','access_token','created_by','update_by', "password_reset_code", "password_reset_expiration", "password_reset_used", "typeUserOp"
    // ];

    protected $guarded=[];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password'];


// Récupération de l'unité administrative de l'agent
    public function profil_user(){
        return $this->belongsTo('App\Models\Profil','idprofil','id');
    }

    public function agent_user(){
        return $this->belongsTo('App\Models\Acteur','idagent','id');
    }

	public function attribuCom(){
		return $this->hasOne('App\Models\AttribCom','id_user','idagent');
	}

    public function email_user(){
        return $this->belongsTo('App\Models\Acteur','idagent','id');
    }
    public function entity(){
        return $this->belongsTo('App\Models\EntiteAdmin','idEntite','id');
    }

    public function lastConnect(){
        return $this->hasOne('App\Models\Activity','id_user','id')->orderBy('id_log','desc');
    }

    public function getLibelleUaAttribute(){
        $listeprofil = Profil::all();
        foreach ($listeprofil as $pr) {
            if($this->idprofil==$pr->id)
                return $pr->LibelleProfil;
        }
    }

}
