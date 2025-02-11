<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ccsp;

class CcspController extends Controller
{
    public function index(){
        $eservice = Ccsp::all();       
        return response()->json([
			"succes"=> "true",
			"message"=> "Liste des ccsp",
			"data" =>$eservice		 
	   ],200);
    }
    public function getByState($state)
    {
        $statutgetbystate = Ccsp::where('is_active', $state)->get();
        return response([
            "success"=> "true",
            "message"=> "Liste du Ccsp",
            "data" => $statutgetbystate
        ],200);
       
    }
    public function setState(Request $request,$id,$state){


            $eservice =  Ccsp::find($id);
            $eservice->is_published = $state;    
            $eservice->save();
            if($eservice){            
                  return response([
                    "success" => "true",
                    "message" => "Le ccsp a été modifié avec succès"
                  ],200);
            }
            else{
                return response([
                    "succes" =>"false",
                    "message" => "Le ccsp n'a été pas  modifié avec succès"
                ],500);
            }
        
    }

    public function store(Request $request){
         $request->validate(
            Ccsp::$rules,
            Ccsp::$messages
            );   
               
            
            $eservice = new Ccsp();
            $eservice->title = $request->title;
            $eservice->address = $request->address;
            $eservice->email = $request->email;
            $eservice->phone = $request->phone;
            $eservice->save();
                return response([
                "success"=> "true",
                "message"=> "Le ccsp  a été crée avec succès'"
            ],200);
        

    }
    public function show($id){

        $eservice = Ccsp::find($id);
        return response([
         "success"=> "true",
         "message"=>"Détail du ccsp",
         "data" => $eservice
        ],200);
       
    }
    public function update(Request $request,$id){
        $request->validate(
            Ccsp::$rules,
            Ccsp::$messages
            );   
               
                      
        $eservice =  Ccsp::find($id);
        $eservice->title = $request->title;
        $eservice->address = $request->address;
        $eservice->email = $request->email;
        $eservice->phone = $request->phone;
        $eservice->save();
        if($eservice){            
              return response([
                "success" => "true",
                "message" => "Le ccsp a été modifié avec succès"
              ],200);
        }
    }
    public function destroy($id){
              
            $eservice = Ccsp::find($id);
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
