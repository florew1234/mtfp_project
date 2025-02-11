<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Type extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'outilcollecte_typeservice';

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
    protected $fillable = ['id', 'libelle', 'descr', 'idEntite','created_by', 'updated_by'];

    public function services() 
    {
        return $this->hasMany('App\Models\Service','idType','id');
    }

    public function getServicesCountAttribute()
    {
        return $this->services()->count();
    }
    

}
