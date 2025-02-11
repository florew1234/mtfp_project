<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commentaire extends Model
{
    //
    protected $table = 'outilcollecte_commentaire';

    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
                            'id_comment',
                            'date_debut_com',
                            'date_fin_com',
                            'fichier_joint',
                            'commentaire',
                            'num_enreg',
                            'id_init',
                          ];
                         
}