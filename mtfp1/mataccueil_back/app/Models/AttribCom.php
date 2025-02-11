<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttribCom extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'outilcollecte_attribuer';

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
    protected $fillable = ['id', 'id_com','id_user'];

    public function acteur_att(){
        return $this->belongsTo('App\Models\Acteur','id_user','id');
    }

    public function commune(){
        return $this->belongsTo('App\Models\Commune','id_com','id');
    }

}
