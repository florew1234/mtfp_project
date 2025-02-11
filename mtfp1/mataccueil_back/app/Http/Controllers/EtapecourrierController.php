<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;

use Illuminate\Http\Request;

use App\Models\Etapecourrier;

use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use DB;

class EtapecourrierController extends Controller {

	/**
     * Create a new controller instance.
     *
     * @return void
     */
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
			//where('idEntite',$idEntite)->
			return Etapecourrier::get();

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
          if (!( isset($inputArray['LibelleEtape']) 
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }

            //Génération du code
                $getcode = DB::table('outilcollecte_etape')->where('idEntite',$request->idEntite)->select(DB::raw('max(CodeEtapeCourrier) as code'))->get();
            $code=1;
            if(!empty($getcode))
            	$code+=$getcode[0]->code;


            $libelle = $inputArray['LibelleEtape'];

			$userconnect = new AuthController;
			$userconnectdata = $userconnect->user_data_by_token($request->token);

            $Etapecourrier = new Etapecourrier;
            $Etapecourrier->CodeEtapecourrier = $code;
			$Etapecourrier->LibelleEtape = $libelle;
			//$Etapecourrier->idEntite=$request->idEntite;

            $Etapecourrier->created_by = $userconnectdata->id;
            $Etapecourrier->updated_by = $userconnectdata->id;
            $Etapecourrier->save();

            return $this->index($Etapecourrier->idEntite);
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
		$EtapecourrierSearch = Etapecourrier::where("id","=",$id)->get();

		if($EtapecourrierSearch->isEmpty()){
			return array(
				"status"=>"error",
				"message"=>"Aucune étape retrouvée"
				);
		}
		else {
			$Etapecourrier = $EtapecourrierSearch->first();
			return $Etapecourrier;
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

                $code = $inputArray['CodeEtapeCourrier'];
            	$libelle = $inputArray['LibelleEtape'];

                $id = $inputArray['id'];

				$userconnect = new AuthController;
				$userconnectdata = $userconnect->user_data_by_token($request->token);
                // Récuperer lae Etapecourrier
                $Etapecourrier = Etapecourrier::find($id);

	            $Etapecourrier->CodeEtapecourrier = $code;
				$Etapecourrier->LibelleEtape = $libelle;
	            $Etapecourrier->created_by = $userconnectdata->id;
	            $Etapecourrier->updated_by = $userconnectdata->id;

	            $Etapecourrier->save();

                return $this->index($Etapecourrier->idEntite);
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
		Etapecourrier::find($id)->delete();
		return array('success' => true );
	}


}
