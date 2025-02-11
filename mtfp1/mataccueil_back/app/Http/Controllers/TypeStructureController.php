<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TypeStructure;

class TypeStructureController extends Controller
{
    public function index(){
        $eservice = TypeStructure::all();       
        return response()->json([
			"succes"=> "true",
			"message"=> "Liste des e-services",
			"data" =>$eservice		 
	   ],200);
    }
    public function getByState($state)
    {
        $statutgetbystate = TypeStructure::where('is_active', $state)->get();
        return response([
            "success"=> "true",
            "message"=> "Liste du e-service",
            "data" => $statutgetbystate
        ],200);
       
    }
    public function setState(Request $request,$id,$state){


            $eservice =  TypeStructure::find($id);
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
            TypeStructure::$rules,
            TypeStructure::$messages
            );   
               
            
            $eservice = new TypeStructure();
            $eservice->name = $request->name;
            $eservice->save();
                return response([
                "success"=> "true",
                "message"=> "Le e-service  a été crée avec succès'"
            ],200);
        

    }
    public function show($id){

        $eservice = TypeStructure::find($id);
        return response([
         "success"=> "true",
         "message"=>"Détail du e-service",
         "data" => $eservice
        ],200);
       
    }
    public function update(Request $request,$id){
        $request->validate(
            TypeStructure::$rules,
            TypeStructure::$messages
            );   
               
                      
        $eservice =  TypeStructure::find($id);            
        $eservice->name = $request->name;

        $eservice->save();
        if($eservice){            
              return response([
                "success" => "true",
                "message" => "Le e-service a été modifié avec succès"
              ],200);
        }
    }
    public function destroy($id){
              
            $eservice = TypeStructure::find($id);
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
