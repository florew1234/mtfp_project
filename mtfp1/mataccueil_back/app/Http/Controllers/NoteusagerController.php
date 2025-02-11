<?php 
 namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Http\Controllers\AuthController;

use App\Models\Noteusager;
use App\Models\Requete;


use App\Helpers\Carbon\Carbon;
use Illuminate\Http\Request;

use DB; 
class NoteusagerController extends Controller
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
          $result = Noteusager::where('idEntite',$idEntite)->get();

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
          if (!( isset($inputArray['']) && isset($inputArray['id'])
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }   
           $codeRequete=0;
           if(isset($inputArray['codeRequete'])) $codeRequete= $inputArray['codeRequete'];
           $noteDelai=0;
           if(isset($inputArray['noteDelai'])) $noteDelai= $inputArray['noteDelai'];
           $noteResultat=0;
           if(isset($inputArray['noteResultat'])) $noteResultat= $inputArray['noteResultat'];
           $noteDisponibilite=0;
           if(isset($inputArray['noteDisponibilite'])) $noteDisponibilite= $inputArray['noteDisponibilite'];

           $noteOrganisation=0;
           if(isset($inputArray['noteOrganisation'])) $noteOrganisation= $inputArray['noteOrganisation'];

            $noteusager= new Noteusager; 
            $noteusager->codeRequete=$codeRequete;
            $noteusager->noteDelai=$noteDelai;
            $noteusager->noteResultat=$noteResultat;
            $noteusager->noteDisponibilite=$noteDisponibilite;
            $noteusager->idEntite=$request->idEntite;
            
            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            $noteusager->created_by = $userconnectdata->id;
            $noteusager->updated_by = $userconnectdata->id;
            $noteusager->save();
              return $this->index($noteusager->idEntite);

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
          if (!( isset($inputArray['']) && isset($inputArray['id'])
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }   
           $codeRequete='';
           if(isset($inputArray['codeRequete'])) $codeRequete= $inputArray['codeRequete'];
           $noteDelai='';
           if(isset($inputArray['noteDelai'])) $noteDelai= $inputArray['noteDelai'];
           $noteResultat='';
           if(isset($inputArray['noteResultat'])) $noteResultat= $inputArray['noteResultat'];
           $noteDisponibilite='';
           if(isset($inputArray['noteDisponibilite'])) $noteDisponibilite= $inputArray['noteDisponibilite'];

          $noteusager=Noteusager::find($id); 
            $noteusager->codeRequete=$codeRequete;
            $noteusager->noteDelai=$noteDelai;
            $noteusager->noteResultat=$noteResultat;
            $noteusager->noteDisponibilite=$noteDisponibilite;

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            $noteusager->created_by = $userconnectdata->id;
            $noteusager->updated_by = $userconnectdata->id;
            $noteusager->save();
              return $this->index($noteusager->idEntite);

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
       Noteusager::find($id)->delete(); 
       return array('success' => true );
   }


 }

