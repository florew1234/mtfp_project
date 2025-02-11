<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    //
    protected $table = 'outilcollecte_agent';
    protected $fillable = [
                            'CodeAgent',
                            'NomPrenoms',
                            'CodeUA',
                            'CodeFonction',
                            'Activer',
                            'Matricule',
                            'created_by',
                            'idEntite',
                            'updated_by',
                          ];

    // Récupération de l'unité administrative de l'agent
    public function agent_codeUA(){
        return $this->belongsTo('App\Models\Uniteadmin','CodeUA','id');
    }

    public function getLibelleUaAttribute(){
        $listeuas = Profil::all();
        foreach ($listeuas as $ua) {
            if($this->CodeUA==$ua->id)
                return $ua->LibelleUA;
        }
    }

    // Récupération de la fonction de l'agent
    public function agent_codeFonction(){
        return $this->belongsTo('App\Models\Fonctionagent','CodeFonction','id');
    }

    public function getLibelleFonctionAttribute(){
        $listeFonction = Profil::all();
        foreach ($listeFonction as $fonction) {
            if($this->CodeFonction==$fonction->id)
                return $fonction->LibelleFonction;
        }
    }

}