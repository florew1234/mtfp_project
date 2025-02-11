<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Eservice;

class EserviceController extends Controller
{
    public function index(){
        $eservice = Eservice::all();       
        return response()->json([
			"succes"=> "true",
			"message"=> "Liste des e-services",
			"data" =>$eservice		 
	   ],200);
    }
    public function getByState($state)
    {
        $statutgetbystate = Eservice::where('is_active', $state)->get();
        return response([
            "success"=> "true",
            "message"=> "Liste du e-service",
            "data" => $statutgetbystate
        ],200);
       
    }
    public function setState(Request $request,$id,$state){


            $eservice =  Eservice::find($id);
            $eservice->is_published = $state;    
            $eservice->save();
            if($eservice){            
                  return response([
                    "success" => "true",
                    "message" => "Le e-service a été modifié avec succès"
                  ],200);
            }
            else{
                return response([
                    "succes" =>"false",
                    "message" => "Le e-service n'a été pas  modifié avec succès"
                ],500);
            }
        
    }

    public function store(Request $request){
         $request->validate(
            Eservice::$rules,
            Eservice::$messages
            );   
               
            
            $eservice = new Eservice();
            $eservice->title = $request->title;
            $eservice->resume = "";
            $eservice->link = $request->link;
            $eservice->code = $request->code;
            $eservice->is_external_service = $request->is_external_service;
            $eservice->desc_link = $request->desc_link;
            $eservice->save();
                return response([
                "success"=> "true",
                "message"=> "Le e-service  a été crée avec succès'"
            ],200);
        

    }
    public function show($id){

        $eservice = Eservice::find($id);
        return response([
         "success"=> "true",
         "message"=>"Détail du e-service",
         "data" => $eservice
        ],200);
       
    }
    public function update(Request $request,$id){
        $request->validate(
            Eservice::$rules,
            Eservice::$messages
            );   
               
                      
        $eservice =  Eservice::find($id);
        $eservice->title = $request->title;
        $eservice->resume = "";
        $eservice->link = $request->link;
        $eservice->code = $request->code;
        $eservice->is_external_service = $request->is_external_service;
        $eservice->desc_link = $request->desc_link;
        $eservice->save();
        if($eservice){            
              return response([
                "success" => "true",
                "message" => "Le e-service a été modifié avec succès"
              ],200);
        }
    }
    public function destroy($id){
              
            $eservice = Eservice::find($id);
            $eservice->delete();
            if($eservice){
             return response([
                "success" => "true",
                "message" => "Le benefice a été supprimé"
             ],200);
            }
            else{
                return response([
                   "succes" => "false",
                   "message" => "Le benefice n'a pas été supprimé."
                ],500);
            }
   } 
}
