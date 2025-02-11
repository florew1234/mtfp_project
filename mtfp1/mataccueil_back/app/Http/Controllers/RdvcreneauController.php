<?php 
 namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Http\Controllers\AuthController;

use Illuminate\Http\Request;
use App\Models\Rdvcreneau;

use App\Models\Rdvparametre;


use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use DB; 
class RdvcreneauController extends Controller
{

    public function __construct() {
    $this->middleware('jwt.auth', ['except' => ['index','creneauDisponible']]);

} 


/**
     * Display a listing of the resource.

     *

     * @return Response

     */


    public function index($idEntite)
    {
        try { 
          $result = Rdvcreneau::where('idEntite',$idEntite)->orderBy('heureDebut','desc')->get();

          return $result;

        } catch(\Illuminate\Database\QueryException $ex){
             $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" ); 
         \Log::error($ex->getMessage());
        }catch(\Exception $ex){ 
        $error =  array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur" ); 
                \Log::error($ex->getMessage());
            return $error;
        }
    }


    public function creneauDisponible($idEntite)
    {

        try { 
          $RdvParametre = Rdvparametre::where('idEntite',$idEntite)->get();
          $nbrePoste= $RdvParametre[0]->nombrePoste;

          $dateProchainRDV= $RdvParametre[0]->dateProchainRDV;

          $result = DB::Select("select * from outilcollecte_rdv_creneau where id not in (select idRdvCreneau from outilcollecte_rdv r where statut=0 Group by idRdvCreneau HAVING  count(idRdvCreneau)>=$nbrePoste)");

          return $result;

        } catch(\Illuminate\Database\QueryException $ex){
             $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" ); 
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
          if (!( isset($inputArray['heureDebut']) 
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }   
           $heureDebut='';
           if(isset($inputArray['heureDebut'])) $heureDebut= $inputArray['heureDebut'];
           $heureFin='';
           if(isset($inputArray['heureFin'])) $heureFin= $inputArray['heureFin'];

            $rdvcreneau= new Rdvcreneau; 
            $rdvcreneau->heureDebut=$heureDebut;
            $rdvcreneau->heureFin=$heureFin;
            $rdvcreneau->idEntite=$request->idEntite;
            
            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            $rdvcreneau->created_by = $userconnectdata->id;
            $rdvcreneau->updated_by = $userconnectdata->id;
            $rdvcreneau->save();
              return $this->index($rdvcreneau->idEntite);

        } catch(\Illuminate\Database\QueryException $ex){
             $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" ); 
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
          if (!( isset($inputArray['heureDebut']) && isset($inputArray['id'])
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }   
           $heureDebut='';
           if(isset($inputArray['heureDebut'])) $heureDebut= $inputArray['heureDebut'];
           $heureFin='';
           if(isset($inputArray['heureFin'])) $heureFin= $inputArray['heureFin'];

          $rdvcreneau=Rdvcreneau::find($id); 
            $rdvcreneau->heureDebut=$heureDebut;
            $rdvcreneau->heureFin=$heureFin;

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            $rdvcreneau->created_by = $userconnectdata->id;
            $rdvcreneau->updated_by = $userconnectdata->id;
            $rdvcreneau->save();
              return $this->index($rdvcreneau->idEntite);

        } catch(\Illuminate\Database\QueryException $ex){
             $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" ); 
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
       Rdvcreneau::find($id)->delete(); 
       return array('success' => true );
   }


 }

