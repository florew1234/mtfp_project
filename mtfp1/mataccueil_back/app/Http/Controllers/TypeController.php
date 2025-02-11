<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;

use Illuminate\Http\Request;

use App\Models\Type;

use App\Models\Statthematique;

use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use DB;

class TypeController extends Controller {

	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('jwt.auth',['except' =>['index','getOne']]);
    }


	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($idEntite)
	{
		try {
			$TypeSearch = Type::where('idEntite',$idEntite)->with('services')->orderBy("libelle","asc")->get();

			foreach($TypeSearch as $Type)
			{
			  $nb_type = $Type->services->count();
			}

			return $TypeSearch;

		} catch(\Illuminate\Database\QueryException $ex){
      \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
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
		try{
		 //recup les champs fournis
	        $inputArray =  $request->all();

         //verifie les champs fournis
          if (!( isset($inputArray['libelle'])  
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }



            $libelle = $inputArray['libelle'];
            $descrthema = $inputArray['descrthema'];

			$userconnect = new AuthController;
			$userconnectdata = $userconnect->user_data_by_token($request->token);

            $Type = new Type;
			$Type->libelle = $libelle;
			$Type->descr = $descrthema;
			$Type->idEntite=$request->idEntite;

            $Type->created_by = $userconnectdata->id;
            $Type->updated_by = $userconnectdata->id;
            $Type->save();

            //L'insérer dans la table Stat_thématique
            $StatType = new Statthematique;
			$StatType->idType=$Type->id;
			$StatType->idEntite=$request->idEntite;
            $StatType->stat=0;

            return $this->index($Type->idEntite);
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
		$TypeSearch = Type::where("id","=",$id)->get();

		if($TypeSearch->isEmpty()){
			return array(
				"status"=>"error",
				"message"=>"Aucune étape retrouvée"
				);
		}
		else {
			$Type = $TypeSearch->first();
			return $Type;
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
            	$descrthema = $inputArray['descrthema'];

                $id = $inputArray['id'];

				$userconnect = new AuthController;
				$userconnectdata = $userconnect->user_data_by_token($request->token);
                // Récuperer lae Type
                $Type = Type::find($id);

				$Type->libelle = $libelle;
				$Type->descr = $descrthema;
	            $Type->created_by = $userconnectdata->id;
	            $Type->updated_by = $userconnectdata->id;

	            $Type->save();

                return $this->index($Type->idEntite);
           }
            catch(\Illuminate\Database\QueryException $ex){
              \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Erreur de connexion à la base de données.");
            return $error;
        }catch(\Exception $e){

            \Log::error($e->getMessage());

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
		Type::find($id)->delete();
		return array('success' => true );
	}



	// Récupérere libellé
	public function getOne($type) {

		try {

			$Type = Type::where('id',"=",$type)->first();



			return $Type;

		} catch(\Illuminate\Database\QueryException $ex){
        \Log::error($ex->getMessage());

			return array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
        }catch(\Exception $e){

            \Log::error($e->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue au cours du traitement . Veuillez contactez l'administrateur" );
            return $error;
        }

	}


}
