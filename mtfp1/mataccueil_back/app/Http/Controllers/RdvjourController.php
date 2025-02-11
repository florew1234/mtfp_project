<?php 
 namespace App\Http\Controllers;


use App\Http\Controllers\Controller;

use App\Http\Controllers\AuthController;

use Illuminate\Http\Request;
use App\Models\Rdvjour;

use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use DB; 
class RdvjourController extends Controller
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
          $result = Rdvjour::where('idEntite',$idEntite)->get();

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
          if (!( isset($inputArray['jour']) 
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }   
           $jour='';
           if(isset($inputArray['jour'])) $jour= $inputArray['jour'];

            $rdvjour= new Rdvjour; 
            $rdvjour->jour=$jour;
            $rdvjour->idEntite=$request->idEntite;

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            $rdvjour->created_by = $userconnectdata->id;
            $rdvjour->updated_by = $userconnectdata->id;
            $rdvjour->save();
              return $this->index( $rdvjour->idEntite);

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
          if (!( isset($inputArray['jour']) && isset($inputArray['id'])
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }   
           $jour='';
           if(isset($inputArray['jour'])) $jour= $inputArray['jour'];

          $rdvjour=Rdvjour::find($id); 
            $rdvjour->jour=$jour;

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            $rdvjour->created_by = $userconnectdata->id;
            $rdvjour->updated_by = $userconnectdata->id;
            $rdvjour->save();
              return $this->index( $rdvjour->idEntite);

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
       Rdvjour::find($id)->delete(); 
       return array('success' => true );
   }


 }

