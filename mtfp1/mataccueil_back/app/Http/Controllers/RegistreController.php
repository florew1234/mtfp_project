<?php
 namespace App\Http\Controllers;


use App\Http\Controllers\Controller;


use App\Http\Controllers\AuthController;
use Illuminate\Support\Collection;

use Illuminate\Http\Request;

use App\Models\Registre;
use App\Models\Clotureregistre;

use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use DB,DateTime;
class RegistreController extends Controller
{

public function __construct() {
    $this->middleware('jwt.auth', ['except' => ['index','store','getListDay']]);

}


/**
     * Display a listing of the resource.

     *

     * @return Response

     */


    public function index(Request $request){

        $start_date=date_create($request->startDate." 00:00:00");
        $end_date=date_create($request->endDate." 23:59:59");
        $query=Registre::query();
        $query->whereBetween('created_at',[$start_date,$end_date]);
        if($request->sex!="undefined" && $request->sex!="") $query->where('sex',$request->sex);
        $data=$query->get();

        return response()->json(array("success" => true, "message" => "","data" => $data),200);
    }


/**
     * Store a newly created resource in storage.

     *

     * @return Response

     */
    public function store(Request $request){
        try {

            $inputArray =  $request->all();

            // dd($inputArray);

            $contactMat=''; 
             if (isset($inputArray['contactMatri'])) {
                 $contactMat= $inputArray['contactMatri'];
             }
            $idEnti=''; 
             if (isset($inputArray['idEntite'])) {
                 $idEnti= $inputArray['idEntite'];
             }
            $idUser=''; 
             if (isset($inputArray['idUser'])) {
                 $idUser= $inputArray['idUser'];
             }
            $nom_pre_=''; 
             if (isset($inputArray['nom_pre_rv'])) {
                 $nom_pre_= $inputArray['nom_pre_rv'];
             }
            $observa=''; 
             if (isset($inputArray['observarv'])) {
                 $observa= $inputArray['observarv'];
             }
            $plainte=''; 
             if (isset($inputArray['plainterv'])) {
                 $plainte= $inputArray['plainterv'];
             }
            $preoccu=''; 
             if (isset($inputArray['preoccurv'])) {
                 $preoccu= $inputArray['preoccurv'];
             }
            $satisfait=''; 
             if (isset($inputArray['satisfaitrv'])) {
                 $satisfait= $inputArray['satisfaitrv'];
             }

             $created_at = "" ;
             if(isset($inputArray['created_at'])){
                $created_at = $inputArray['created_at'];
            }

             $registre = new Registre;
             $registre->matri_telep = $contactMat;
             $registre->nom_prenom = $nom_pre_;
             $registre->idEntite = $idEnti;
             $registre->plainte = $plainte;
             $registre->contenu_visite = $preoccu;
             $registre->satisfait = $satisfait;
             $registre->sex = $request->sex;
             $registre->motif_non = "";
             $registre->observ_visite = $observa;
             $registre->created_by = $idUser;
             $registre->created_at = $created_at;
             $registre->save();
             
             return array("status" => "success", "message" => "");

            } catch(\Illuminate\Database\QueryException $ex){
                    \Log::error($ex->getMessage());
                return array("status" => "error", "message" => $ex->getMessage()."Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
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
    
    public function getRequeteByPfcRv($idusager, Request $request)
    {
        try {

            $input = $request->query();
            
            $result=array();
            if (isset($input['search'])) {   
                $search=$input['search'];
                $result = Registre::with(['creator','entite','requete','requete.entite','creator.profil_user'])
                                   ->orderBy('id', 'desc')
                                   ->where("cloture", "=", 0)
                                   ->where("created_by", "=", $idusager)
                                   ->where("matri_telep", "LIKE", "%{$search}%")
                                   ->orWhere("nom_prenom", "LIKE", "%{$search}%")
                                   ->orWhere("contenu_visite", "LIKE", "%{$search}%")
                                   ->orWhere("motif_non", "LIKE", "%{$search}%")
                                   ->orWhere("observ_visite", "LIKE", "%{$search}%")
                                   ->get();
            } else {
                $result = Registre::with(['creator','entite','requete','requete.entite','creator.profil_user'])->orderBy('id', 'desc')
                                   ->where("created_by", "=", $idusager)
                                   ->where("cloture", "=", 0)
                                   ->get();
            }
            
            return $result;
        } catch (\Illuminate\Database\QueryException $ex) {
            \Log::error($ex->getMessage());

            $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du" .
                            " chargement des connexions. Veuillez contactez l'administrateur" );
            return $error;
        }
    }
public function update($id, Request $request)
{
        try {
            $inputArray =  $request->all();
            $registre=Registre::find($id);

            $contactMat=''; 
             if (isset($inputArray['contactMatri'])) {
                 $contactMat= $inputArray['contactMatri'];
             }
            $idEnti=''; 
             if (isset($inputArray['idEntite'])) {
                 $idEnti= $inputArray['idEntite'];
             }
            $idUser=''; 
             if (isset($inputArray['idUser'])) {
                 $idUser= $inputArray['idUser'];
             }
            $nom_pre_=''; 
             if (isset($inputArray['nom_pre_rv'])) {
                 $nom_pre_= $inputArray['nom_pre_rv'];
             }
            $observa=''; 
             if (isset($inputArray['observarv'])) {
                 $observa= $inputArray['observarv'];
             }
            $plainte=''; 
             if (isset($inputArray['plainterv'])) {
                 $plainte= $inputArray['plainterv'];
             }
            $preoccu=''; 
             if (isset($inputArray['preoccurv'])) {
                 $preoccu= $inputArray['preoccurv'];
             }
            $satisfait=''; 
             if (isset($inputArray['satisfaitrv'])) {
                 $satisfait= $inputArray['satisfaitrv'];
             }

             $registre->matri_telep = $contactMat;
             $registre->nom_prenom = $nom_pre_;
             $registre->idEntite = $idEnti;
             $registre->plainte = $plainte;
             $registre->contenu_visite = $preoccu;
             $registre->satisfait = $satisfait;
             $registre->sex = $request->sex;
             $registre->motif_non = "";
             $registre->observ_visite = $observa;
             $registre->created_by = $idUser;
             
             $registre->save();
             
             return array("status" => "success", "message" => "");
        } catch(\Illuminate\Database\QueryException $ex){
                \Log::error($ex->getMessage());

            return array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );

        }catch(\Exception $e){

                \Log::error($e->getMessage());
            return array("status" => "error", "message" => "Une erreur est survenue lors du" .
                            " chargement des connexions. Veuillez contactez l'administrateur" );
        }

 }
/**
     * Remove the specified resource from storage.
     *
     * @param  int  id
     * @return Response
     */

public function destroy($id){
    Registre::find($id)->delete();
        return $this->index();
 }


 }

