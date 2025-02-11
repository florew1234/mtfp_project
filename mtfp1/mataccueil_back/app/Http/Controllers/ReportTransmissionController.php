<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utilisateur;
use App\Models\CcspReport;
use App\Models\CcspReportTransmission;
use App\Models\Registre;
use App\Utilities\FileStorage;
use PDF,Auth,Storage,Str;

class ReportTransmissionController extends Controller
{
    
    public function __construct() {
        $this->middleware('jwt.auth');
    
    }
    
    public function index(Request $request){
        $report = CcspReportTransmission::all();       
        return response()->json([
			"succes"=> true,
			"message"=> "Liste des exercies",
			"data" =>$report		 
	   ],200);
    }
   
 

    public function store(Request $request){
         $request->validate(
            CcspReportTransmission::$rules,
            CcspReportTransmission::$messages
            );
            $data= $request->all();
            $report2 =    CcspReport::find($request->report_id);


            if ($request->sens==1) {
                if (Auth::user()->profil_user->pointfocalcom) {

                    if (Auth::user()->agent_user->idCom) {
                        $userDown=Utilisateur::whereHas('agent_user', function($q){
                            $q->where('idCom',Auth::user()->agent_user->idCom);
                        })
                        ->whereHas('profil_user', function($q){
                            $q->where('superviseurcentrecom',true);
                        })
                        ->first();
                    } else {
                        $userDown=Utilisateur::whereHas('profil_user', function($q){
                            $q->where('coordonnateurcentrecom',1);
                        })->first();
                    }
                    
                 
                }else if (Auth::user()->profil_user->pointfocal || Auth::user()->profil_user->superviseurcentrecom) {
                    $userDown=Utilisateur::whereHas('agent_user', function($q){
                        $q->where('idDepart',Auth::user()->agent_user->commune->depart_id);
                    })
                    ->whereHas('profil_user', function($q){
                        $q->where('validateurcentrecom',1);
                    })
                    ->first();
    
                  
                } else if (Auth::user()->profil_user->validateurcentrecom){
                    $userDown=Utilisateur::whereHas('profil_user', function($q){
                        $q->where('coordonnateurcentrecom',1);
                    })->first();
                }
            }else{
                $userDown= Utilisateur::find($report2->transmissions->last()?->user_up);

                $data['instruction']=$request->instruction;
                $data['delay']=$request->delay;
            }
         

            
            $data['sens']=$request->sens;
            $data['is_last']=1;
            $data['user_up']=Auth::id();
            $data['ua_up']=Auth::user()->agent_user->structure->id;
            $data['ua_down']=$userDown->agent_user->structure->id;
            $data['user_down']=$userDown->id;

            $report2->transmissions->last()?->update(['is_last'=>false]);

            $report = CcspReportTransmission::create($data);
            
        

            $report2->status=1;
            $report2->save();

            return response([
                "success"=> true,
                "data"=> $report,
                "message"=> "L' exercice  a été crée avec succès'"
            ],200);
        

    }
    public function show($id){

        $report = CcspReportTransmission::find($id);
        return response([
         "success"=> true,
         "message"=>"Détail de l'exercice",
         "data" =>$report
        ],200);
       
    }
    public function update(Request $request,$id){
        $request->validate(
            CcspReportTransmission::$rules,
            CcspReportTransmission::$messages
            ); 
                  
            $report =  CcspReportTransmission::find($id);
            $data= $request->all();
            $report->update($data);
            
            if($report){            
                return response([
                    "success" => true,
                    "data"=> $path,
                    "message" => "L' exercice a été modifié avec succès"
                ],200);
            }
    }
    public function destroy($id){
              
            $report = CcspReportTransmission::find($id);
          
            if($report){
                $report->delete();
             return response([
                "success" => true,
                "message" => "L' exercice a été supprimé"
             ],200);
            }
            else{
                return response([
                   "succes" => "false",
                   "message" => "L' exercice n'a pas été supprimé."
                ],500);
            }
   } 
}
