<?php
 namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Http\Controllers\AuthController;

use Illuminate\Http\Request;
use App\Models\Parcoursrequete;
use App\Models\Requete;

use App\Models\Etape;

use App\Models\Structure;


use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use DB;
class ParcoursrequeteController extends Controller
{

public function __construct() {
$this->middleware('jwt.auth');

    }


/**
     * Display a listing of the resource.

     *

     * @return Response

     */


    public function index($idEntite)

    {

        try {
          $result = Parcoursrequete::where('idEntite',$idEntite)->get();

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
          if (!( isset($inputArray['libelle']) && isset($inputArray['id'])
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }
$idRequete='';
if(isset($inputArray['idRequete'])) $idRequete= $inputArray['idRequete'];
$idEtape='';
if(isset($inputArray['idEtape'])) $idEtape= $inputArray['idEtape'];
$dateArrivee='';
if(isset($inputArray['dateArrivee'])) $dateArrivee= $inputArray['dateArrivee'];
$dateDepart='';
if(isset($inputArray['dateDepart'])) $dateDepart= $inputArray['dateDepart'];
$sens='';
if(isset($inputArray['sens'])) $sens= $inputArray['sens'];
$idStructure='';
if(isset($inputArray['idStructure'])) $idStructure= $inputArray['idStructure'];
$typeStructure='';
if(isset($inputArray['typeStructure'])) $typeStructure= $inputArray['typeStructure'];

$parcoursrequete= new Parcoursrequete;
$parcoursrequete->idRequete=$idRequete;
$parcoursrequete->idEtape=$idEtape;
$parcoursrequete->dateArrivee=$dateArrivee;
$parcoursrequete->dateDepart=$dateDepart;
$parcoursrequete->sens=$sens;
$parcoursrequete->idStructure=$idStructure;
$parcoursrequete->typeStructure=$typeStructure;
$parcoursrequete->idEntite=$request->idEntite;

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            $parcoursrequete->created_by = $userconnectdata->id;
            $parcoursrequete->updated_by = $userconnectdata->id;
            $parcoursrequete->save();
       return $this->index($parcoursrequete->idEntite);

} catch(\Illuminate\Database\QueryException $ex){
          \Log::error($ex->getMessage());

         $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
}catch(\Exception $e){

          \Log::error($e->getMessage());
          $error =
                  array("status" => "error", "message" => "Une erreur est survenue lors du" .
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
$idRequete='';
if(isset($inputArray['idRequete'])) $idRequete= $inputArray['idRequete'];
$idEtape='';
if(isset($inputArray['idEtape'])) $idEtape= $inputArray['idEtape'];
$dateArrivee='';
if(isset($inputArray['dateArrivee'])) $dateArrivee= $inputArray['dateArrivee'];
$dateDepart='';
if(isset($inputArray['dateDepart'])) $dateDepart= $inputArray['dateDepart'];
$sens='';
if(isset($inputArray['sens'])) $sens= $inputArray['sens'];
$idStructure='';
if(isset($inputArray['idStructure'])) $idStructure= $inputArray['idStructure'];
$typeStructure='';
if(isset($inputArray['typeStructure'])) $typeStructure= $inputArray['typeStructure'];

     $parcoursrequete=Parcoursrequete::find($id);
$parcoursrequete->idRequete=$idRequete;
$parcoursrequete->idEtape=$idEtape;
$parcoursrequete->dateArrivee=$dateArrivee;
$parcoursrequete->dateDepart=$dateDepart;
$parcoursrequete->sens=$sens;
$parcoursrequete->idStructure=$idStructure;
$parcoursrequete->typeStructure=$typeStructure;

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            $parcoursrequete->created_by = $userconnectdata->id;
            $parcoursrequete->updated_by = $userconnectdata->id;
            $parcoursrequete->save();
       return $this->index($parcoursrequete->idEntite);

} catch(\Illuminate\Database\QueryException $ex){
          \Log::error($ex->getMessage());

         $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
}catch(\Exception $e){

          \Log::error($e->getMessage());
          $error =
                  array("status" => "error", "message" => "Une erreur est survenue lors du" .
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
       Parcoursrequete::find($id)->delete();
       return array('success' => true );
 }


 }

