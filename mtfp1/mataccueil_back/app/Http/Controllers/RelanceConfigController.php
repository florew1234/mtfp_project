<?php namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;

use Illuminate\Http\Request;

use App\Models\RelanceParam;
use App\User;

use App\Helpers\Carbon\Carbon;


use DB;

class RelanceConfigController extends Controller {

	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
       $this->middleware('jwt.auth',['except' =>['index','listUsers']]);
    }


	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($id)
	{
		try {
			return RelanceParam::with(['user_.agent_user'])->orderBy('apartir_de','asc')->where('idEntite',$id)->get();
		} catch(\Illuminate\Database\QueryException $ex){
      \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
        }catch(\Exception $ex){
      \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur" );
            return $error;
        }
	}

	public function listUsers($identite) {
		//Liste des utilisateurs ayant le profil Directeur
		$reqCom = User::join('outilcollecte_acteur','outilcollecte_users.idagent','outilcollecte_acteur.id')
						   ->join('outilcollecte_profil','outilcollecte_users.idprofil','outilcollecte_profil.id')
						   ->where('outilcollecte_users.idEntite',$identite)
						   ->whereIn('outilcollecte_users.idprofil',[19,20,21])
						   ->select(DB::raw("CONCAT(outilcollecte_acteur.nomprenoms,' : ',outilcollecte_profil.LibelleProfil) AS nomprenom"),'outilcollecte_users.id')
						   ->orderby('nomprenom','asc')
						   ->distinct()
						   ->get();
	   return $reqCom;
	}
	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		try{
		 //recup les champs fournis
	        $inputArray =  $request->all();
			
         //verifie les champs fournis
          if (!( isset($inputArray['msg_relance']) 
            ))  { //controle d existence nbrjrs_relance:null,etat_relance:null}
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }
			
			$msg_relance = $inputArray['msg_relance'];
            $idEntite = $inputArray['idEntite'];
            $id_user = $inputArray['listuser'];
            $apartir_de = $inputArray['apartir_de'];
			
			
            $Relance = new RelanceParam;
			$Relance->ordre_relance = 0;
			$Relance->msg_relance = $msg_relance;
			$Relance->idEntite = $idEntite;
			$Relance->id_user = $id_user;
			$Relance->apartir_de = $apartir_de;
            $Relance->save();
			
            return $this->index($idEntite);
        }
        catch(\Illuminate\Database\QueryException $ex){
          \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Error" );
            //\Log::error($ex->getMessage());
            return $error;
        }
        catch(\Exception $ex){
          \Log::error($ex->getMessage());
			// return response($ex->getMessage());
            $error = array("status" => "error", "message" => "Une erreur est survenue lors de " .
                "votre tentative de connexion. Veuillez contactez l'administrateur" );
				return $error;
        }

	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$InstitutionSearch = RelanceParam::where("id","=",$id)->get();

		if($InstitutionSearch->isEmpty()){
			return array(
				"status"=>"error",
				"message"=>"Aucune étape retrouvée"
				);
		}
		else {
			$RelanceParam = $InstitutionSearch->first();
			return $RelanceParam;
		}
	}

	/**
	 * update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id,Request $request)
	{
				try{
                //recup les champs fournis
                $inputArray =  $request->all();

                //verifie les champs fournis
                if (!isset($inputArray['msg_relance']) && !isset($inputArray['id'])) {
                	//controle d existence
                    return array("status" => "error",
                        "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
                }
					
				$msg_relance = $inputArray['msg_relance'];
				$idEntite = $inputArray['idEntite'];
				$id_user = $inputArray['listuser'];
				$id = $inputArray['id'];
				$apartir_de = $inputArray['apartir_de'];
				
				
				$Relance = RelanceParam::find($id);
				$Relance->ordre_relance = 0;
				$Relance->msg_relance = $msg_relance;
				$Relance->idEntite = $idEntite;
				$Relance->id_user = $id_user;
				$Relance->apartir_de = $apartir_de;
				$Relance->save();
				
				return $this->index($idEntite);
            	
           }
            catch(\Illuminate\Database\QueryException $ex){
              \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Erreur de connexion à la base de données.");
            return $error;
        }catch(\Exception $ex){

          \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" =>"Une erreur est survenue. Veuillez contacter l'administrateur.".$ex->getMessage() );
            return $error;
        }

	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		RelanceParam::find($id)->delete();
		return array('success' => true );
	}


}
