<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{

    public function index(){

        $setting = Setting::first();
        return response([
         "success"=> "true",
         "message"=>"Paramètre",
         "data" =>$setting
        ],200);
       
    }
    public function show(){

        $setting = Setting::first();
        return response([
         "success"=> "true",
         "message"=>"Paramètre",
         "data" =>$setting
        ],200);
       
    }
    public function store(Request $request){
         $request->validate(
            Setting::$rules,
            Setting::$messages
            );   
               
            
            $setting = new Setting();
            $setting->header_text = $request->header_text;
            $setting->save();
                return response([
                "success"=> "true",
                "message"=> "Le bénéfice  a été crée avec succès'",
                "data"=>$setting
            ],200);
        

    }
    public function update(Request $request,$id){
        $request->validate(
            Setting::$rules,
            Setting::$messages
            );   
               
                      
        $setting =  Setting::find($id);
        $setting->header_text= $request->header_text;
        $setting->save();
        if($setting){            
              return response([
                "success" => "true",
                "message" => "Le bénéfice a été modifié avec succès"
              ],200);
        }
    }
}
