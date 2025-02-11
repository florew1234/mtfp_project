<?php
 namespace App\Http\Controllers;


use App\Http\Controllers\Controller;


use App\Http\Controllers\AuthController;


use Illuminate\Http\Request;

use App\Models\Departement;
use App\Models\Commune;

use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use DB;
class DepartementController extends Controller
{

public function __construct() {


}


/**
     * Display a listing of the resource.

     *

     * @return Response

     */


    public function index()

    {

        try {
            $result = Departement::get();

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

    public function updateCommune($id)

    {

        try {
            $result = Commune::where('depart_id',$id)->get();
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
$code='';
if(isset($inputArray['code'])) $code= $inputArray['code'];
$libelle='';
if(isset($inputArray['libelle'])) $libelle= $inputArray['libelle'];

$departement= new Departement;
$departement->code=$code;
$departement->libelle=$libelle;

$userconnect = new AuthController;
$userconnectdata = $userconnect->user_data_by_token($request->token);
$departement->created_by = $userconnectdata->id;
$departement->updated_by = $userconnectdata->id;
$departement->save();
return $this->index();

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
$code='';
if(isset($inputArray['code'])) $code= $inputArray['code'];
$libelle='';
if(isset($inputArray['libelle'])) $libelle= $inputArray['libelle'];

$departement=Departement::find($id);
$departement->code=$code;
$departement->libelle=$libelle;

$userconnect = new AuthController;
$userconnectdata = $userconnect->user_data_by_token($request->token);
$departement->created_by = $userconnectdata->id;
$departement->updated_by = $userconnectdata->id;
$departement->save();
return $this->index();

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
Departement::find($id)->delete();
return $this->index();
 }


 }

