<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CcspReport;
use App\Models\Registre;
use App\Utilities\FileStorage;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\Fpdf;
use PDF,Auth,Storage,Str,Config;
class CcspReportController extends Controller
{

    public function __construct() {
        $this->middleware('jwt.auth');
    
    }
    
    public function index(Request $request){
        $report = CcspReport::with(["transmission"])->where('user_id',Auth::id())->get();       

        // if (Auth::user()->profil_user->pointfocalcom) {
        //     $report = CcspReport::where('user_id',Auth::id())->where('type',Config('global_data.ccsp_report_types')[0])->get();       

        // }else{
        //     $report = CcspReport::where('user_id',Auth::id())->where('type',Config('global_data.ccsp_report_types')[1])->get();       

        // }
        return response()->json([
			"succes"=> true,
			"message"=> "Liste des exercies",
			"data" =>$report		 
	   ],200);
    }

    public function getPending(Request $request){
        
        if (Auth::user()->profil_user->superviseurcentrecom || Auth::user()->profil_user->validateurcentrecom) {

            $report = CcspReport::with(['transmission'])->whereHas('transmissions',function($q){
                $q->where('ua_down',Auth::user()->agent_user->structure->id)->where('is_last',true);
            })->where('type',Config('global_data.ccsp_report_types')[0])->where('status',1)->get();       

        }else{

            $report = CcspReport::with(['transmission'])->whereHas('transmissions',function($q){
                $q->where('ua_down',Auth::user()->agent_user->structure->id)->where('is_last',true);
            })->where('type',Config('global_data.ccsp_report_types')[1])->where('status',1)->get();       

        }
        
        return response()->json([
			"succes"=> true,
			"message"=> "Liste des exercies",
			"data" =>$report		 
	   ],200);
    }
   
 

    public function store(Request $request){
         $request->validate(
            CcspReport::$rules,
            CcspReport::$messages
            );

            $start_date_format=date('01-'.$request->month.'-Y');
            $end_date_format=date('t-'.$request->month.'-Y');

            $start_date=date_create($start_date_format." 00:00:00");
            $end_date=date_create($end_date_format." 23:59:59");

            $dataArr= $request->all();

       

            $title="Rapport du ".$start_date_format." au ".$end_date_format."-".time();
            $path=Str::slug($title).".pdf";
            if (Auth::user()->profil_user->pointfocalcom) {
                $query=Registre::query();
                $query->whereBetween('created_at',[$start_date,$end_date]);
                if($request->sex!="undefined" && $request->sex!="") $query->where('sex',$request->sex);
                $data=$query->get();

                if ( $data->count()==0) {
                    return response([
                        "success"=> false,
                        "data"=> [],
                        "message"=> "Aucun rapport disponible pour cette période"
                    ],200);
                }
                $pdf=PDF::loadView("ccsp_pf_report",[
                    "customer_recieved"=> $data->count(),
                    "customer_satisfied"=> $data->where('satisfait','oui')->count(),
                    "unsatified_reason"=> $request->unsatified_reason,
                    "difficult"=> $request->difficult,
                    "solution"=> $request->solution,
                    "structure"=>Auth::user()->agent_user->structure->libelle,
                    "observation"=> $request->observation
               ])->save(Storage::disk('public')->path($path));
               $dataArr['user_id']=Auth::id();
               $dataArr['customer_recieved']=$data->count();
               $dataArr['customer_satisfied']=$data->where('satisfait','oui')->count();
               $dataArr['status']=0;
               $dataArr['title']= $title;
               $dataArr['filename']=$path;
               $dataArr['start_date']=date_create($start_date_format);
               $dataArr['end_date']=date_create($end_date_format);
            }else if(Auth::user()->profil_user->validateurcentrecom){
                $query=CcspReport::where('start_date',date_create($start_date_format))->where('end_date',date_create($end_date_format))->where('type',Config("global_data.ccsp_report_types")[0]);
                if($request->sex!="undefined" && $request->sex!="") $query->where('sex',$request->sex);
                 $data=$query->where('status',2)->get();
                
                 if ( $data->count()==0) {
                    return response([
                        "success"=> false,
                        "data"=> [],
                        "message"=> "Aucun rapport disponible pour cette période"
                    ],200);
                }

                 $pdf=PDF::loadView("ccsp_ddtfp_report",[
                     "data"=> $data,
                     "month"=> date("F", mktime(0, 0, 0, $request->month, 10)),
                     "structure"=>Auth::user()->agent_user->structure->libelle,
                     "summary"=> $request->summary
                ])->save(Storage::disk('public')->path($path));
                $dataArr['user_id']=Auth::id();
                $dataArr['customer_recieved']=$query->sum('customer_recieved')[0];
                $dataArr['customer_satisfied']=$query->sum('customer_satisfied')[0];
                $dataArr['status']=0;
                $dataArr['title']= $title;
                $dataArr['filename']=$path;
                $dataArr['type']=Config("global_data.ccsp_report_types")[1];
                $dataArr['start_date']=date_create($start_date_format);
                $dataArr['end_date']=date_create($end_date_format);
            }
            else{
                $query=CcspReport::where('start_date',date_create($start_date_format))->where('end_date',date_create($end_date_format))->where('type',Config("global_data.ccsp_report_types")[1]);
               // if($request->sex!="undefined") $query->where('sex',$request->sex);
                $data=$query->get();
               

                if (sizeof($data)==0) {
                    return response([
                        "success"=> false,
                        "data"=> [],
                        "message"=> "Aucun rapport disponible pour cette période"
                    ],200);
                }
                $files=$query->where('status',2)->get()->pluck(['filename'])->toArray();
             
                $pdf=new Fpdi();
                foreach ($files as $file) {
                    $pageCount=$pdf->setSourceFile(Storage::disk('public')->path($file));
                    for ($i=0; $i < $pageCount; $i++) { 
                        $pdf->AddPage();
                        $tplId=$pdf->importPage($i+1);
                        $pdf->useTemplate($tplId);
                    }
                }
                $pdf->Output(Storage::disk('public')->path($path),'F');
                
            
               $dataArr['user_id']=Auth::id();
               $dataArr['status']=2;
               $dataArr['customer_recieved']=$query->sum('customer_recieved')[0];
               $dataArr['customer_satisfied']=$query->sum('customer_satisfied')[0];
               $dataArr['title']= $title;
               $dataArr['filename']=$path;
               $dataArr['type']=Config("global_data.ccsp_report_types")[2];
               $dataArr['start_date']=date_create($start_date_format);
               $dataArr['end_date']=date_create($end_date_format);
            }
           
            unset($dataArr['month']);
            unset($dataArr['sex']);
            $report = CcspReport::create($dataArr);
            
            return response([
                "success"=> true,
                "data"=> $path,
                "message"=> "L' exercice  a été crée avec succès'"
            ],200);
        

    }
    public function show($id){

        $report = CcspReport::find($id);
        return response([
         "success"=> true,
         "message"=>"Détail de l'exercice",
         "data" => [
            'data' =>$report      
           ]
        ],200);
       
    }
    public function update(Request $request,$id){
        $request->validate(
            CcspReport::$rules,
            CcspReport::$messages
            ); 
                  
            $report =  CcspReport::find($id);
            $dataArr= $request->all();

            if ($report) {
                FileStorage::deleteFile('public',$report->filename,"");
            }

            if (Auth::user()->profil_user->pointfocalcom) {

                $pdf=PDF::loadView("ccsp_pf_report",[
                    "customer_recieved"=> $report->customer_recieved,
                    "customer_satisfied"=> $customer_recieved->customer_satisfied,
                    "unsatified_reason"=> $request->unsatified_reason,
                    "difficult"=> $request->difficult,
                    "solution"=> $request->solution,
                    "structure"=>Auth::user()->agent_user->structure->libelle,
                    "observation"=> $request->observation
               ])->save(Storage::disk('public')->path($report->filename));
             
            }else{
                $start_date_format=date('01-'.$request->month.'-Y');
                $end_date_format=date('t-'.$request->month.'-Y');
    
                $start_date=date_create($start_date_format." 00:00:00");
                $end_date=date_create($end_date_format." 23:59:59");
    
                $query=CcspReport::where('start_date',date_create($start_date_format))->where('end_date',date_create($end_date_format))->where('type',Config("global_data.ccsp_report_types")[0]);
               // if($request->sex!="undefined") $query->where('sex',$request->sex);
                $data=$query->get();
               
                $pdf=PDF::loadView("ccsp_ddtfp_report",[
                    "data"=> $data,
                    "month"=> date("F", mktime(0, 0, 0, $request->month, 10)),
                    "structure"=>Auth::user()->agent_user->structure->libelle,
                    "summary"=> $request->summary
               ])
               ->setPaper('a4', 'landscape')
               ->save(Storage::disk('public')->path($report->filename));
              
            }

            unset($dataArr['month']);
            $report2 =  $report->update($dataArr);
          

            if($report2){            
                return response([
                    "success" => true,
                    "data"=> CcspReport::find($id)->filename,
                    "message" => "L' exercice a été modifié avec succès"
                ],200);
            }
    }
    public function destroy($id){
              
            $report = CcspReport::find($id);
            if ($report) {
                FileStorage::deleteFile('public',$report->filename,"");
                $report->delete();
            }
            if($report){
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

   function validation($id) {
    $report=CcspReport::find($id);
    $report->transmissions->last()->update(['is_last'=>false]);
    $report->update(['status'=>2]);

    return response([
        "success" => true,
        "message" => "Rapport validé avec succès"
     ],200);
   }
}
