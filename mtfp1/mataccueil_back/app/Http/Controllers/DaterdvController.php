<?php 
 namespace App\Http\Controllers;
 use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Helpers\Factory\ParamsFactory;

use App\Http\Controllers\AuthController;

use App\Models\Daterdv;

use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use DB; 
class DaterdvController extends Controller
{

    public function __construct() {
    $this->middleware('jwt.auth',['except' => ['getDateActif']]);

} 


/**
     * Display a listing of the resource.

     *

     * @return Response

     */


    public function index($idEntite)
    {

        try { 
          $result = Daterdv::where('idEntite',$idEntite)->orderBy("dateChoisi")->get();

          return $result;

        } catch(\Illuminate\Database\QueryException $ex){
             $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" ); 
         \Log::error($ex->getMessage());
        }catch(\Exception $ex){ 
        $error =  array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur" ); 
                \Log::error($ex->getMessage());
            return $error;
        }
    }

    public function getDateActif($idEntite)
    {

        try { 
          $result = Daterdv::where('dateChoisi','>=',Now())->where('idEntite',$idEntite)->orderBy("dateChoisi")->get();

          return $result;

        } catch(\Illuminate\Database\QueryException $ex){
             $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" ); 
         \Log::error($ex->getMessage());
        }catch(\Exception $ex){ 
        $error =  array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur" ); 
                \Log::error($ex->getMessage());
            return $error;
        }
    }
/**
     * Store a newly created resource in storage.

     *

     * @return Response

     */
public function store(Request $request)
{
        try { 
          $inputArray =  $request->all();
//verifie les champs fournis
          if (!( isset($inputArray['dateChoisi'])
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }   
            
            $dateChoisi = $inputArray["dateChoisi"];
            $dateChoisi = ParamsFactory::convertToDateTimeForSearch($dateChoisi, false);
            $dateChoisi = $dateChoisi->toDateTimeString();

            $daterdv= new Daterdv; 
            $daterdv->dateChoisi=$dateChoisi;
            $daterdv->idEntite=$request->idEntite;

            $userconnect = new AuthController;
            
            $daterdv->created_by = 1;
            $daterdv->updated_by = 1;
            $daterdv->save();
              return $this->index($daterdv->idEntite);

        } catch(\Illuminate\Database\QueryException $ex){
             $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" ); 
         \Log::error($ex->getMessage());
        }catch(\Exception $ex){ 
        $error =  array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur" ); 
                \Log::error($ex->getMessage());
            return $error;
        }
    }
/**
     * update a newly created resource in storage.

     *

     * @return Response

     */
public function update($id,Request $request)
{
        try { 

          $inputArray =  $request->all();
//verifie les champs fournis
          if (!( isset($inputArray['dateChoisi']) && isset($inputArray['id'])
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }   
           
           $dateChoisi = $inputArray["dateChoisi"];
           $dateChoisi = ParamsFactory::convertToDateTimeForSearch($dateChoisi, false);
           $dateChoisi = $dateChoisi->toDateTimeString();

          $daterdv=Daterdv::find($id); 
            $daterdv->dateChoisi=$dateChoisi;

            $userconnect = new AuthController;
            $daterdv->created_by = 1;
            $daterdv->updated_by = 1;
            $daterdv->save();
              return $this->index($daterdv->idEntite);

        } catch(\Illuminate\Database\QueryException $ex){
             $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" ); 
         \Log::error($ex->getMessage());
        }catch(\Exception $ex){ 
        $error =  array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur" ); 
                \Log::error($ex->getMessage());
            return $error;
        }
 }
/**
     * Remove the specified resource from storage.
     *
     * @param  int  id
     * @return Response
     */

   public function destroy($id){
       Daterdv::find($id)->delete(); 
       return array('success' => true );
   }


 }

