<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clotureregistre extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'outilcollecte_cloture_registre';

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
    protected $fillable = ['id','id_user', 'date_cloture','fichier_cloture'];

    public function acteur_att(){
        return $this->belongsTo('App\Models\Acteur','id_user','id');
    }

}
