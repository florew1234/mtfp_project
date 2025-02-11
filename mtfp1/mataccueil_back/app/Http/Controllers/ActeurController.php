<?php
 namespace App\Http\Controllers;


use App\Http\Controllers\Controller;


use App\Http\Controllers\AuthController;


use Illuminate\Http\Request;

use App\Models\Acteur;

use App\Models\Structure;



use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use DB;
class ActeurController extends Controller
{

public function __construct() {
$this->middleware('jwt.auth');

    }


/**
     * Display a listing of the resource.

     *

     * @return Response

     */


    public function index($idEntite) {
 
        try {
            $result = Acteur::with(['structure','commune','commune.departement'])
                                ->where('idEntite',$idEntite)->get();
            
            return $result;

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

    public function All_acteur($idEntite) {
 
        try {
            // ->with(['usager', 'reponse' => function ($query) {  has('user_agent.lastConnect')->
            //     return $query->orderBy('id', 'DESC');
            // },'affectation','parcours'])
            // $result = Acteur::with(['user_agent.lastConnect' => function ($query) {
            //                             return $query->orderBy('last_login', 'desc');
            //                         },'structure','structure.sous_structure','commune','commune.departement','user_agent'])
            $result = Acteur::with(['user_agent.lastConnect','structure','structure.sous_structure','commune','commune.departement','user_agent'])
                            ->where('idEntite',$idEntite)
                            ->whereNotIn('idStructure',[154,0]) // 154 = Mairie user_agent.idprofil
                            ->where('idCom','48')
                            ->WhereHas('structure', function ($q) {
                                $q->where('libelle','<>','');
                            })
                            ->orderBy('nomprenoms','asc')
                            // ->orderBy('user_agent.updated_at','asc')
                            ->get();
                        //Charger par défaut 
                        return $result;
            } catch(\Illuminate\Database\QueryException $ex){
                    \Log::error($ex->getMessage());
            $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
        }catch(\Exception $e){
                    \Log::error($e->getMessage());
            $error = array("status" => "error", "message" => "Une erreur est survenue lors du" .
                        " chargement des connexions. Veuillez contactez l'administrateur -- ".$e->getMessage() );
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
                      if (!( isset($inputArray['nomprenoms'])  ))  { //controle d existence
                            return array("status" => "error",
                                "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
                        }
            $nomprenoms='';
            if(isset($inputArray['nomprenoms'])) $nomprenoms= $inputArray['nomprenoms'];
            $idTypeacteur='';
            if(isset($inputArray['idTypeacteur'])) $idTypeacteur= $inputArray['idTypeacteur'];
            $idStructure='';
            if(isset($inputArray['idStructure'])) $idStructure= $inputArray['idStructure'];
            $idDepart='';
            if(isset($inputArray['idDepart'])) $idDepart= $inputArray['idDepart'];
            $idComm='';
            if(isset($inputArray['idComm'])) $idComm= $inputArray['idComm'];

            $acteur= new Acteur;
            $acteur->nomprenoms=$nomprenoms;
            $acteur->idTypeacteur=$idTypeacteur;
            $acteur->idStructure=$idStructure;
            $acteur->idCom=$idComm;
            $acteur->idDepart=$idDepart;
            $acteur->idEntite=$request->idEntite;
            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            $acteur->created_by = $userconnectdata->id;
            $acteur->updated_by = $userconnectdata->id;
            $acteur->save();
            return $this->index($acteur->idEntite);

} catch(\Illuminate\Database\QueryException $ex){

          \Log::error($ex->getMessage());

$error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
}catch(\Exception $e){
          \Log::error($e->getMessage());
          $error =
array("status" => "error", "message" =>"Une erreur est survenue lors de l'enregistrement. Veuillez contacter l'administrateur." );
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
          if (!( isset($inputArray['nomprenoms']) && isset($inputArray['id'])
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }
$nomprenoms='';
if(isset($inputArray['nomprenoms'])) $nomprenoms= $inputArray['nomprenoms'];
$idTypeacteur='';
if(isset($inputArray['idTypeacteur'])) $idTypeacteur= $inputArray['idTypeacteur'];
$idStructure='';
if(isset($inputArray['idStructure'])) $idStructure= $inputArray['idStructure'];
$idComm='';
if(isset($inputArray['idComm'])) $idComm= $inputArray['idComm'];
$acteur=Acteur::find($id);
$acteur->nomprenoms=$nomprenoms;
$acteur->idTypeacteur=$idTypeacteur;
$acteur->idCom=$idComm;
$acteur->idStructure=$idStructure;

$userconnect = new AuthController;
$userconnectdata = $userconnect->user_data_by_token($request->token);
$acteur->created_by = $userconnectdata->id;
$acteur->updated_by = $userconnectdata->id;
$acteur->save();
return $this->index($acteur->idEntite);

} catch(\Illuminate\Database\QueryException $ex){

          \Log::error($ex->getMessage());

$error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
}catch(\Exception $e){
          \Log::error($e->getMessage());

          $error = array("status" => "error", "message" => "Une erreur est survenue lors du" . " chargement des connexions. Veuillez contactez l'administrateur" );
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
Acteur::find($id)->delete();
return array('success' => true );
 }


 }

