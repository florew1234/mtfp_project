<?php
 namespace App\Http\Controllers;


use App\Http\Controllers\Controller;


use App\Http\Controllers\AuthController;


use Illuminate\Http\Request;

use App\Models\Nature;

use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use DB;
class NatureController extends Controller
{

public function __construct() {
$this->middleware('jwt.auth',['except'=>['index']]);

    }


/**
     * Display a listing of the resource.

     *

     * @return Response

     */


    public function index($idEntite)

    {

        try {
        $result = Nature::where('idEntite',$idEntite)->get();

        return $result;

        } catch(\Illuminate\Database\QueryException $ex){
                \Log::error($ex->getMessage());

        $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
        }catch(\Exception $e){

          \Log::error($e->getMessage());
          $error = array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur" );
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
          if (!( isset($inputArray['libelle']) 
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }


            $libelle='';
            if(isset($inputArray['libelle'])) $libelle= $inputArray['libelle'];

            DB::enableQueryLog();

            $nature= new Nature;

            $nature->libelle=$libelle;
            $nature->idEntite=$request->idEntite;



            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);

            $nature->created_by = $userconnectdata->id;
            $nature->updated_by = $userconnectdata->id;


            $nature->save();


            $queries = DB::getQueryLog();


            return $this->index($nature->idEntite);

} catch(\Illuminate\Database\QueryException $ex){
          \Log::error($ex->getMessage());

$error=array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" );
}catch(\Exception $e){

          \Log::error($e->getMessage());
          $error = array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur" );
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
          if (!( isset($inputArray['libelle']) && isset($inputArray['id'])
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }

$libelle='';
if(isset($inputArray['libelle'])) $libelle= $inputArray['libelle'];

$nature=Nature::find($id);
$nature->id=$id;

$nature->libelle=$libelle;

$userconnect = new AuthController;
$userconnectdata = $userconnect->user_data_by_token($request->token);
$nature->created_by = $userconnectdata->id;
$nature->updated_by = $userconnectdata->id;
$nature->save();
return $this->index($nature->idEntite);

} catch(\Illuminate\Database\QueryException $ex){
          \Log::error($ex->getMessage());

$error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
}catch(\Exception $e){

          \Log::error($e->getMessage());
          $error = array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur" );
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
            Nature::find($id)->delete();
            return array('success' => true );
        }


 }

