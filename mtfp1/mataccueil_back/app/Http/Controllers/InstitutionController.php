<?php namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;

use Illuminate\Http\Request;

use App\Models\Institution;

use App\Helpers\Carbon\Carbon;


use DB;

class InstitutionController extends Controller {

	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
       $this->middleware('jwt.auth',['except' =>['index',]]);
    }


	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		try {

			return Institution::get();

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
	public function listEntite_Requete()
	{
		//Liste des entités qui ont au moins une requête non traitée
		try {
			$stats = DB::select("SELECT DISTINCT ins.id,  ins.libelle
									FROM `outilcollecte_requete` req, outilcollecte_institution ins
									WHERE req.idEntite = ins.id
									AND req.traiteOuiNon = 0
									ORDER BY ins.id ASC;");
			return $stats;

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
	public function listEntite()
	{
		//Liste des entités qui ont au moins une requête non traitée
		try {
			
			return Institution::where('etat_relance',1)->get();

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
          if (!( isset($inputArray['libelle']) 
            ))  { //controle d existence nbrjrs_relance:null,etat_relance:null}
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }



            $libelle = $inputArray['libelle'];
            $sigle = $inputArray['sigle'];
            $type = $inputArray['type'];
            $nbrjrs_relance = $inputArray['nbrjrs_relance'];
            $etat_relance = $inputArray['etat_relance'];

			$userconnect = new AuthController;
			$userconnectdata = $userconnect->user_data_by_token($request->token);

            $Institution = new Institution;
			$Institution->libelle = $libelle;
			$Institution->sigle = $sigle;
			$Institution->type = $type;
			$Institution->nbrjrs_relance = $nbrjrs_relance;
			$Institution->etat_relance = $etat_relance;
            $Institution->created_by = $userconnectdata->id;
            $Institution->updated_by = $userconnectdata->id;
            $Institution->save();

            return $this->index();
        }
        catch(\Illuminate\Database\QueryException $ex){
          \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Cette étape existe déjà !" );
            //\Log::error($ex->getMessage());
            return $error;
        }
        catch(\Exception $ex){
          \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors de " .
                "votre tentative de connexion. Veuillez contactez l'administrateur" );
            //\Log::error($ex->getMessage());
            //return $error;

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
		$InstitutionSearch = Institution::where("id","=",$id)->get();

		if($InstitutionSearch->isEmpty()){
			return array(
				"status"=>"error",
				"message"=>"Aucune étape retrouvée"
				);
		}
		else {
			$Institution = $InstitutionSearch->first();
			return $Institution;
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
                if (!isset($inputArray['libelle']) && !isset($inputArray['id'])) {
                	//controle d existence
                    return array("status" => "error",
                        "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
                }

            	$libelle = $inputArray['libelle'];
				$sigle = $inputArray['sigle'];
				$type = $inputArray['type'];
                $id = $inputArray['id'];
				$nbrjrs_relance = $inputArray['nbrjrs_relance'];
				$etat_relance = $inputArray['etat_relance'];
				
				$userconnect = new AuthController;
				$userconnectdata = $userconnect->user_data_by_token($request->token);
                // Récuperer lae Institution
                $Institution = Institution::find($id);

				$Institution->libelle = $libelle;
				$Institution->sigle = $sigle;
				$Institution->type = $type;
				$Institution->nbrjrs_relance = $nbrjrs_relance;
				$Institution->etat_relance = $etat_relance;
	            $Institution->created_by = $userconnectdata->id;
	            $Institution->updated_by = $userconnectdata->id;

	            $Institution->save();

                return $this->index();
           }
            catch(\Illuminate\Database\QueryException $ex){
              \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Erreur de connexion à la base de données.");
            return $error;
        }catch(\Exception $ex){

          \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" =>"Une erreur est survenue. Veuillez contacter l'administrateur." );
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
		Institution::find($id)->delete();
		return array('success' => true );
	}


}
