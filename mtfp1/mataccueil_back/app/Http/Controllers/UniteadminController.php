<?php namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;

use Illuminate\Http\Request;

use App\Models\Uniteadmin;

use App\Models\Agent;

use App\Models\Utilisateur;

use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use DB;

class UniteadminController extends Controller {

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

			$UniteadminSearch = Uniteadmin::where('idEntite',$idEntite)->orderBy('LibelleUA')->get();
			foreach ($UniteadminSearch as $Uniteadmin) {
				$Uniteadmin->ua_typeua;
			}

			return $UniteadminSearch;

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


	// Liste des courriers
	public static function getListeUA($idUser,$idEntite) {
		try {
				$getuser=Utilisateur::find($idUser);

				$UASearch =array();



				if(is_null($getuser->profil_user->niveauValidation))
					$niveauUser=-1;
				else
					$niveauUser=$getuser->profil_user->niveauValidation;

				if($niveauUser>3)
				{
					$UASearch = Uniteadmin::where('idEntite',$idEntite)->with(['ua_typeua'])->orderBy('LibelleUA')->get();
				}
				else
				{
					$idagent=$getuser->idagent;

					$getAgent=Agent::find($idagent);

					$codeUA="";

					if(count($getAgent)>0)
					{
						$codeUA=$getAgent->CodeUA;

						$UASearch = Uniteadmin::where('idEntite',$idEntite)->with(['ua_typeua'])->where('UAParent',$codeUA)->orderBy('LibelleUA')->get();

					}
				}




				return $UASearch;

		} catch(\Illuminate\Database\QueryException $ex){
        \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" );
        }catch(\Exception $e){

            \Log::error($e->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue au cours du traitement . Veuillez contactez l'administrateur" );
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
          if (!( isset($inputArray['LibelleUA'])  
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }

            //Génération du code
                $getcode = DB::table('sygec_uniteadmin')->select(DB::raw('max( CAST(CodeUA AS INT)) as code'))->get();
            $code=1;
            if(!empty($getcode))
            	$code+=$getcode[0]->code;


            $LibelleUA = $inputArray['LibelleUA'];
            $CodeTypeUA = $inputArray['CodeTypeUA'];

            $email ="";
            if(isset($inputArray['email']))
            	$email=$inputArray['email'];

            $SigleUA = $inputArray['SigleUA'];

            $UAParent = "";
	        if (isset($inputArray['UAParent']))
	            $UAParent = $inputArray['UAParent'];

			$userconnect = new AuthController;
			$userconnectdata = $userconnect->user_data_by_token($request->token);

            $uniteadmin = new Uniteadmin;
            $uniteadmin->CodeUA = $code;
			$uniteadmin->LibelleUA = $LibelleUA;
			$uniteadmin->CodeTypeUA = $CodeTypeUA;
			$uniteadmin->SigleUA = $SigleUA;
			$uniteadmin->UAParent = $UAParent;
			$uniteadmin->email = $email;
			$uniteadmin->idEntite=$request->idEntite;

            $uniteadmin->created_by = $userconnectdata->id;
            $uniteadmin->updated_by = $userconnectdata->id;
            $uniteadmin->save();

            return $this->index($request->idEntite);
        }
        catch(\Illuminate\Database\QueryException $ex){
            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Erreur lors de l'exécution de la requête. Veuillez contacter l'administrateur." );
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
		$UniteadminSearch = Uniteadmin::where("id","=",$id)->get();

		if($UniteadminSearch->isEmpty()){
			return array(
				"status"=>"error",
				"message"=>"Aucun unité administrative retrouvée"
				);
		}
		else {
			$uniteadmin = $UniteadminSearch->first();
			return $uniteadmin;
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
                if (!isset($inputArray['LibelleUA']) && !isset($inputArray['id'])) {
                	//controle d existence
                    return array("status" => "error",
                        "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
                }

                $code = $inputArray['CodeUA'];
	            $LibelleUA = $inputArray['LibelleUA'];

	            $CodeTypeUA = "";
	            if (isset($inputArray['CodeTypeUA']))
	            	$CodeTypeUA = $inputArray['CodeTypeUA'];

	            $SigleUA = $inputArray['SigleUA'];

	            $UAParent = "";
	            if (isset($inputArray['UAParent']))
	            	$UAParent = $inputArray['UAParent'];

	            $email ="";
            	if(isset($inputArray['email']))
            		$email=$inputArray['email'];

				$userconnect = new AuthController;
				$userconnectdata = $userconnect->user_data_by_token($request->token);
                // Récuperer lae uniteadmin
                $uniteadmin = Uniteadmin::find($id);

	            $uniteadmin->CodeUA = $code;
				$uniteadmin->LibelleUA = $LibelleUA;
	            $uniteadmin->created_by = $userconnectdata->id;
	            $uniteadmin->updated_by = $userconnectdata->id;

				$uniteadmin->CodeTypeUA = $CodeTypeUA;
				$uniteadmin->SigleUA = $SigleUA;
				$uniteadmin->UAParent = $UAParent;

				$uniteadmin->email = $email;


	            $uniteadmin->save();

                return $this->index($request->idEntite);
           }
            catch(\Illuminate\Database\QueryException $ex){
              \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Erreur de connexion à la base de données.");
            return $error;
        }catch(\Exception $e){

            \Log::error($e->getMessage());

            $error = array("status" => "error", "message" => $e );
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
		Uniteadmin::find($id)->delete();
		return array('success' => true );
	}


}
