<?php

namespace App\Http\Controllers;
use App\Helpers\Factory\ParamsFactory;

use Illuminate\Http\Request;

//use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Collection;

use App\Http\Controllers\Controller;


use App\Http\Controllers\AuthController;
use App\Http\Controllers\RequeteController;

//use Request;

use App\Models\Requete;

use App\Models\Usager;
use App\Models\Commune;
use App\Models\Activity;

use App\Models\Service;
use App\Models\Utilisateur;
use App\Models\Profil;
use App\Models\Acteur;
use App\Models\Etape;
use App\Models\Noteusager;

use App\Models\Affectation;
use App\Models\Reponse;
use App\Models\Structure;
use App\Models\Parcoursrequete;

use App\Models\Type;
use App\Models\Etapecourrier;
use App\Models\Departement;
use App\Models\Registre;
use App\Models\Parametre;
use App\Models\EntiteAdmin;
use App\Helpers\Carbon\Carbon;

use Mail;

use DB,PDF;

use Dompdf\Dompdf;

use Tymon\JWTAuth\JWTAuth;

class AdvancedStatisticsController extends Controller
{
    function getTogetherViews(){

        $allRequtes=Requete::with('affectation')->get();
        $allNote=Noteusager::all();
        $data['all']=$allRequtes->count();
        $data['treated']=$allRequtes->where('traiteOuiNon',1)->count();
        $data['pending']=$allRequtes->where('traiteOuiNon',0)->whereNotNull('affectation')->count();
       // $data['pending']=$allRequtes->has('affectation',0)->orWhere('traiteOuiNon',0)->has('affectation','!=',0)->count();
         $data['pending_pourcent']= $data['all']==0?0: (($data['pending']/$data['all'])*100);
         $data['pending_pourcent']=number_format($data['pending_pourcent'],2)."%";
         $data['note_all']=$allNote->count();
         $totalNotes=$allNote->map(fn ($el) =>   ($el->noteDelai +$el->noteResultat)/2)->sum();
         $data['totalNotes']=$totalNotes;
         //$totalNotes=Noteusager::select(DB::raw('sum((noteDelai+noteResultat)/2) as total'))->get()[0]["total"];
          $data['moy']=number_format(($totalNotes/$data['note_all'])*10,2);
        return response()->json([
            "data"=>$data
        ]);
    }

    function getTogetherViews2(){


        $entities=EntiteAdmin::all();
        $last_sectoriels= new Collection();
        $last_pfc= new Collection();
        foreach ($entities as $value) {
            $userIds=Utilisateur::whereHas('profil_user',function($q){
                $q->whereNotIn('LibelleProfil',['Super administrateur','Administrateur']);
            })->where('idEntite',$value->id)->get()->pluck('id')->toArray();

           $result= Activity::with(['user.agent_user','user.entity'])->whereIn('id_user',$userIds)->orderby('last_login','DESC')->first();
           if($result)$last_sectoriels->push($result);


            $userPfcIds=Utilisateur::whereHas('profil_user',function($q){
                $q->where('LibelleProfil','Point focal communal');
            })->where('idEntite',$value->id)->get()->pluck('id')->toArray();

            $result= Activity::with(['user.agent_user','user.entity'])->whereIn('id_user',$userPfcIds)->orderby('last_login','DESC')->first();
            if($result)$last_pfc->push($result);
        }
       
        $last_sectoriels=$last_sectoriels->sortBy('id_log');
        $last_sectoriels=$last_sectoriels->take(5)->values()->all();

        $last_pfc=$last_pfc->sortBy('id_log');
        $last_pfc=$last_pfc->take(5)->values()->all();

        $data['last_sectoriels']=$last_sectoriels;
        $data['last_pfc']=$last_pfc;

        // $data['last_sectoriels2']=Activity::with('user.agent_user')->whereHas('user.profil_user',function($q){
        //     $q->where('LibelleProfil','!=','Administrateur');
        // })->latest()->take(5)->get();

        // $data['last_pfc2']=Activity::with('user.agent_user')->whereHas('user.profil_user',function($q){
        //     $q->where('LibelleProfil','Point focal communal');
        // })->latest()->take(5)->get();

        return response()->json([
            "data"=>$data
        ]);
    }

    function getPerformances() {
        $data['high_pending']=DB::select("
                                        SELECT outilcollecte_affectation.idStructure, outilcollecte_structure.libelle as direction, outilcollecte_institution.libelle as entite,COUNT(*) as nontraitee 
                                        FROM `outilcollecte_requete`,outilcollecte_affectation, outilcollecte_structure,outilcollecte_institution 
                                        WHERE traiteOuiNon=0 
                                        AND outilcollecte_requete.id= outilcollecte_affectation.idRequete 
                                        AND outilcollecte_structure.id=outilcollecte_affectation.idStructure 
                                        AND outilcollecte_requete.idEntite=outilcollecte_institution.id 
                                        GROUP BY outilcollecte_affectation.idStructure,outilcollecte_institution.libelle 
                                        ORDER by nontraitee DESC 
                                        LIMIT 1 OFFSET 1;")[0];

        $data['bad_notes']=DB::select("
                            SELECT outilcollecte_structure.libelle as structure,outilcollecte_service.libelle as prestation, ((outilcollecte_note_usager.noteDelai+outilcollecte_note_usager.noteResultat)/2) as note 
                            FROM `outilcollecte_requete`,outilcollecte_note_usager,outilcollecte_service,outilcollecte_structure 
                            WHERE traiteOuiNon=1 
                            AND outilcollecte_requete.codeRequete=outilcollecte_note_usager.codeReq 
                            AND outilcollecte_requete.idPrestation=outilcollecte_service.id 
                            AND outilcollecte_service.idParent=outilcollecte_structure.id 
                            GROUP BY outilcollecte_service.libelle,outilcollecte_structure.libelle
                            HAVING note <5;");

        return response()->json([
            "data"=>$data
        ]);
    }

    function getPerformancesVisits()  {
        $preQuery=" SELECT outilcollecte_commune.libellecom, COUNT(*) as total
        FROM `outilcollecte_registre`,outilcollecte_users,outilcollecte_acteur, outilcollecte_commune 
        WHERE outilcollecte_registre.created_by=outilcollecte_users.id 
        AND outilcollecte_users.idagent=outilcollecte_acteur.id 
        AND outilcollecte_commune.id=outilcollecte_acteur.idCom "; 
        $subQuery=" GROUP BY outilcollecte_acteur.idCom";

         
       // AND outilcollecte_acteur.idTypeacteur IN (2,4)

        $tuesday =  new \DateTime();
        $tuesday->modify('last Tuesday');
        $end_date=$tuesday;
        $end_date=$end_date->format('Y-m-d 23:59:59');
        $start_date= $tuesday->modify("-7 days")->format('Y-m-d 00:00:00');
        // check if we need to go back in time one more week
        //$tuesday = date('W', $tuesday)==date('W') ? $tuesday-7*86400 : $tuesday;

       // return $preQuery." AND outilcollecte_registre.created_at between \"".$start_date."\" AND \"".$end_date."\" ".$subQuery." ORDER BY total DESC LIMIT 3 OFFSET 0";
        $data=array();
        $data[0]['name']='CCSP et GSRU comptabilisant de visite';
        $data[0]['max_last_week']=DB::select($preQuery." AND outilcollecte_registre.created_at between \"".$start_date."\" AND \"".$end_date."\" ".$subQuery." ORDER BY total DESC LIMIT 3 OFFSET 0");
        $data[0]['min_last_week']=DB::select($preQuery." AND outilcollecte_registre.created_at between \"".$start_date."\" AND \"".$end_date."\" ".$subQuery." ORDER BY total ASC LIMIT 3 OFFSET 0");
        $data[0]['max_total']=DB::select($preQuery.$subQuery." ORDER BY total DESC LIMIT 3 OFFSET 0");
        $data[0]['min_total']=DB::select($preQuery.$subQuery." ORDER BY total ASC LIMIT 3 OFFSET 0");





        $data[1]['name']='CCSP et GSRU comptabilisant de satisfaction';
        $total_last_week=DB::select($preQuery." AND outilcollecte_registre.created_at between \"".$start_date."\" AND \"".$end_date."\" ".$subQuery." ORDER BY outilcollecte_commune.id ASC");
        $total_satisfait_last_week=DB::select($preQuery." AND satisfait='oui' AND outilcollecte_registre.created_at between \"".$start_date."\" AND \"".$end_date."\" ".$subQuery." ORDER BY outilcollecte_commune.id ASC");
        
        $total_satisfait_last_week2=new Collection();
        foreach($total_satisfait_last_week as $item){
            $total_satisfait_last_week2->push((object)$item);
        }

        $lastWeekData=array();
        $i=0;
        foreach ($total_last_week as $value) {
            //$key=array_search($value->libellecom,$total_satisfait_last_week);
            $check = $total_satisfait_last_week2->where('libellecom',$value->libellecom)->first();
            if ($check !=false) {
                $taux=$value->total==0?0:($check->total/$value->total)*100;
                $lastWeekData[$i]['libellecom']= $value->libellecom;
                $lastWeekData[$i]['total']= number_format($taux,2)."%";
                $i++;
            }
         
        }
        $lastWeekData2= new Collection();
        foreach ($lastWeekData as $value) {
            $lastWeekData2->push((object)$value);
        }
       
        $data[1]['max_last_week']=$lastWeekData2->sortBy('total')->take(3)->values()->all();
        $data[1]['min_last_week']=$lastWeekData2->sortByDesc('total')->take(3)->values()->all();


        $total=DB::select($preQuery.$subQuery." ORDER BY outilcollecte_commune.id ASC");
        $total_satisfait=DB::select($preQuery." AND satisfait='oui'".$subQuery." ORDER BY outilcollecte_commune.id ASC");
        
        $total_satisfait2=new Collection();
        foreach($total_satisfait as $item){
            $total_satisfait2->push((object)$item);
        }
        
        $totalData=array();
        $i=0;
        foreach ($total as $value) {
            $check = $total_satisfait2->where('libellecom',$value->libellecom)->first();
            if ($check) {
                $taux=$value->total==0?0:($check->total/$value->total)*100;
                $totalData[$i]['libellecom']= $value->libellecom;
                $totalData[$i]['total']= number_format($taux,2)."%";
                $i++;
            }
         
        }


        $totalData2= new Collection();
        foreach ($totalData as $value) {
            $totalData2->push((object)$value);
        }
       
        $data[1]['max_total']=$totalData2->sortByDesc('total')->take(3)->values()->all();
        $data[1]['min_total']=$totalData2->sortBy('total')->take(3)->values()->all();
      
        return response()->json([
            "data"=>$data
        ]);
    }

    function printView(Request $request) {
        
        $filename=time().'.pdf';
        PDF::loadView("template",[
            'data'=> $request->data,
            'data1'=> $request->data1,
            'data2'=> $request->data2,
            'data3'=> $request->data3,
            'elements'=> $request->elements,
            ])->save(Storage::path("/public/".$filename));

        return response()->json([
            "data"=>$filename
        ]);
    }
}
