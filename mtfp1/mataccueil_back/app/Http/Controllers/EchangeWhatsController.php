<?php
 namespace App\Http\Controllers;


use App\Http\Controllers\Controller;


use App\Http\Controllers\AuthController;
use Illuminate\Support\Collection;

use Illuminate\Http\Request;

use App\Models\EchangeWhat;
use Illuminate\Support\Facades\Storage;

use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use DB,DateTime;

class EchangeWhatsController extends Controller
{

public function __construct() {
    $this->middleware('jwt.auth', ['except' => ['index','store','getListDay']]);

}

    public function index(){}

    
    public function store(Request $request){
        try {

            $inputArray =  $request->all();

            $contWhatsapp=''; 
             if (isset($inputArray['contWhatsapp'])) {
                 $contWhatsapp= $inputArray['contWhatsapp'];
             }
            $discussiontxt=''; 
             if (isset($inputArray['discussiontxt'])) {
                 $discussiontxt= $inputArray['discussiontxt'];
             }
            $idUser=''; 
             if (isset($inputArray['idUser'])) {
                 $idUser= $inputArray['idUser'];
             }
            
            $echan = new EchangeWhat;
            $echan->numerowhatsapp = $contWhatsapp;
            $echan->id_user_savediscu = $idUser;
            $echan->id_userTraite = null;
            $echan->discussions = $discussiontxt;
            $echan->id_req = null;
            $echan->traite_disc = 'non';
            $echan->save();
            
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
    
    
    public function getEchangeWhatsApp(Request $request)
    {
        try {

            $input = $request->query();
            $trait = $input['traite'];
            // return response($input);
            $result=array();
            if (isset($input['search'])) {   
                $search=$input['search'];
                $result = EchangeWhat::with(['creator','creator.agent_user','userTrait','userTrait.agent_user','requete','requete.entite','creator.profil_user'])
                                    ->where("traite_disc", $trait)
                                    // ->where("numerowhatsapp", "LIKE", "%{$search}%")
                                    // ->orWhere("discussions", "LIKE", "%{$search}%")
                                    ->orderBy('id', 'asc')
                                   ->get();
            }else{
                $result = EchangeWhat::with(['creator','creator.agent_user','userTrait','userTrait.agent_user','requete','requete.entite','creator.profil_user'])
                                    ->where("traite_disc", $trait)
                                    ->orderBy('id', 'asc')
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
            $echan=EchangeWhat::find($id);

            $contWhatsapp=''; 
             if (isset($inputArray['contWhatsapp'])) {
                 $contWhatsapp= $inputArray['contWhatsapp'];
             }
            $discussiontxt=''; 
             if (isset($inputArray['discussiontxt'])) {
                 $discussiontxt= $inputArray['discussiontxt'];
             }
            $echan->numerowhatsapp = $contWhatsapp;
            $echan->discussions = $discussiontxt;
            $echan->save();

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
    public function updatereponse($id, Request $request)
    {
        try {
            $inputArray =  $request->all();
            $echan=EchangeWhat::find($id);
            
            $fileName = "";
            if ($request->file('fichier')) {
                $file = $request->file('fichier');
                $extension = $file->getClientOriginalExtension();
                $fileName = "PDA_W".$id.date('YmdHis').'.'.$extension;
                $pathName = Storage::path("public/Usager-mail/");
                $file->move($pathName, $fileName);
            }

            $reponse=''; 
            if (isset($inputArray['reponse'])) {
                $reponse= $inputArray['reponse'];
            }
            $echan->reponse_agent = $reponse;
            $echan->traite_disc = "oui";
            $echan->fichier_joint = $fileName;
            $echan->save();

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
    public function confirmerTraitement($idUser, $id, Request $request)
    {
        try {
            $ech = EchangeWhat::where('id_userTraite',$idUser)->where('id_req',null)->count();
            if($ech != 0 ){
                return array("status" => "error", "message" => "Vous aviez déjà confirmé une discussion à traiter. Prière l'ajout dans mataccueil avant de confirmer une autre.");
            }
            $ech = EchangeWhat::where('id',$id)->where('id_userTraite','<>',null)->count();
            if($ech != 0 ){
                return array("status" => "error", "message" => "Cette discusion est déjà confimée par un autre acteur. Merci de choisir une autre.");
            }

            $echan=EchangeWhat::find($id);
            $echan->id_userTraite = $idUser;
            $echan->save();

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
    EchangeWhat::find($id)->delete();
        return $this->index();
 }


 }

