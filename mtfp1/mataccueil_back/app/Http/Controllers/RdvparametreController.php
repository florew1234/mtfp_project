<?php 
namespace App\Http\Controllers;

use App\Helpers\Factory\ParamsFactory;

use App\Http\Controllers\Controller;

use App\Http\Controllers\AuthController;

use Illuminate\Http\Request;
use App\Models\Rdvparametre;

use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use DB; 
class RdvparametreController extends Controller
{

    public function __construct() {
    $this->middleware('jwt.auth', ['except' => ['index']]);

} 


/**
     * Display a listing of the resource.

     *

     * @return Response

     */


    public function index($idEntite)

    {

        try { 
          $result = Rdvparametre::where('idEntite',$idEntite)->get();

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
          if (!( isset($inputArray['nombrePoste']) 
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }   
           $nombrePoste='';
           if(isset($inputArray['nombrePoste'])) $nombrePoste= $inputArray['nombrePoste'];

            $rdvparametre= new Rdvparametre; 
            $rdvparametre->nombrePoste=$nombrePoste;
            $rdvparametre->idEntite=$request->idEntite;

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            $rdvparametre->created_by = $userconnectdata->id;
            $rdvparametre->updated_by = $userconnectdata->id;
            $rdvparametre->save();
              return $this->index( $rdvparametre->idEntite);

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
          if (!( isset($inputArray['nombrePoste']) && isset($inputArray['id'])
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }   
           $nombrePoste='';
           if(isset($inputArray['nombrePoste'])) $nombrePoste= $inputArray['nombrePoste'];

           $dateProchainRdv = "";  if(isset($inputArray["dateProchainRdv"]))  { $dateProchainRdv = $inputArray["dateProchainRdv"]; }
           $dateProchainRdv = ParamsFactory::convertToDateTimeForSearch($dateProchainRdv, false);
           $dateProchainRdv = $dateProchainRdv->toDateTimeString(); //->getTimestamp();

            $rdvparametre=Rdvparametre::find($id); 
            $rdvparametre->nombrePoste=$nombrePoste;
            $rdvparametre->dateProchainRdv=$dateProchainRdv;

            $rdvparametre->save();

              return $this->index( $rdvparametre->idEntite);

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
       Rdvparametre::find($id)->delete(); 
       return array('success' => true );
   }


 }

