<?php namespace App\Http\Controllers;



use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;

use Illuminate\Http\Request;

use App\Models\Fonctionagent;

use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use DB;

class FonctionagentController extends Controller {

	/**
     * Create a new controller instance.
     *
     * @return void
     */
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

			return Fonctionagent::where('idEntite',$idEntite)->get();

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
          if (!( isset($inputArray['LibelleFonction']) 
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }

            //Génération du code
                $getcode = DB::table('sygec_fonctionagent')->select(DB::raw('max(CAST(CodeFonction AS INT)) as code'))->get();
            $code=1;
            if(!empty($getcode))
            	$code+=$getcode[0]->code;

            $libelle = $inputArray['LibelleFonction'];
            $typefonction = $inputArray['TypeFonction'];

			$userconnect = new AuthController;
			$userconnectdata = $userconnect->user_data_by_token($request->token);

            $Fonction = new Fonctionagent;
            $Fonction->CodeFonction = $code;
			$Fonction->LibelleFonction = $libelle;
			$Fonction->TypeFonction = $typefonction;
			$Fonction->idEntite=$request->idEntite;

            $Fonction->created_by = $userconnectdata->id;
            $Fonction->updated_by = $userconnectdata->id;
            $Fonction->save();

            return $this->index($Fonction->idEntite);
        }
        catch(\Illuminate\Database\QueryException $ex){
          \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue. Veuillez contactez l'administrateur." );
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
		$FonctionSearch = Fonctionagent::where("id","=",$id)->get();

		if($FonctionSearch->isEmpty()){
			return array(
				"status"=>"error",
				"message"=>"Aucun type de délai retrouvé"
				);
		}
		else {
			$Fonction = $FonctionSearch->first();
			return $Fonction;
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
                if (!isset($inputArray['LibelleFonction']) && !isset($inputArray['id'])) {
                	//controle d existence
                    return array("status" => "error",
                        "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
                }

                $code = $inputArray['CodeFonction'];
	            $libelle = $inputArray['LibelleFonction'];
	            $typefonction = $inputArray['TypeFonction'];

				$userconnect = new AuthController;
				$userconnectdata = $userconnect->user_data_by_token($request->token);


                $Fonction = Fonctionagent::find($id);

	            $Fonction->CodeFonction = $code;
				$Fonction->LibelleFonction = $libelle;
				$Fonction->TypeFonction = $typefonction;
	            $Fonction->created_by = $userconnectdata->id;
	            $Fonction->updated_by = $userconnectdata->id;

	            $Fonction->save();

                return $this->index($Fonction->idEntite);
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
		Fonctionagent::find($id)->delete();
		return array('success' => true );
	}


}
