<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Elementdeclservice extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'outilcollecte_elementdecl_service';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'idElementdecl',  'idEntite','idService','nameService'];

   

}
