<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CcspReport extends Model
{
    use HasFactory;
    protected $guarded =[];
    static $rules =[];
    static $messages =[];

    function user() {
        return $this->belongsTo('App\Models\Utilisateur','user_id');

    }

    function transmissions() {
        return $this->hasMany('App\Models\CcspReportTransmission','report_id');

    }


    function transmission() {
        return $this->hasOne('App\Models\CcspReportTransmission','report_id')->latest();

    }
}
