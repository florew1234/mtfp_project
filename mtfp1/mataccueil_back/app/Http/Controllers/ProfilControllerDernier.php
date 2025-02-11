<?php namespace App\Http\Controllers;

use App\Helpers\Factory\ParamsFactory;


use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;

use App\Models\Utilisateur;
use Illuminate\Http\Request;

use App\Models\Profil;
use DB;

use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

class ProfilController extends Controller {

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
  public function index()
  {
    try {

      return Profil::orderBy('LibelleProfil')->get();

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


	public function getProfil($id)
	{
		try {

			return Profil::all();

		} catch(\Illuminate\Database\QueryException $ex){
      \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );

		}catch(\Exception $e){

            \Log::error($e->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du" . " chargement des connexions. Veuillez contactez l'administrateur" );
            return $error;
        }
	}


	public function store(Request $request)
	{
		try{
		 //recup les champs fournis
	        $inputArray = $request->all();

         //verifie les champs fournis
          if (!( isset($inputArray['libelle']) 
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }

            //Génération du code
                $getcode = DB::table('outilcollecte_profil')->select(DB::raw('max(CodeProfil) as code'))->get();
            $code=1;
            if(!empty($getcode))
            	$code+=$getcode[0]->code;

            $libelle = $inputArray['libelle'];

            $saisie =false;
	            if(isset($inputArray['saisie']))
	            	$saisie =$inputArray['saisie'];

	            $validation =false;
	            if(isset($inputArray['validation']))
	            	$validation =$inputArray['validation'];

	            $sgm =false;
	            if(isset($inputArray['sgm']))
	            	$sgm =$inputArray['sgm'];

	            $dc =false;
	            if(isset($inputArray['dc']))
	            	$dc =$inputArray['dc'];

	            $ministre =false;
	            if(isset($inputArray['ministre']))
	            	$ministre =$inputArray['ministre'];

	            $direction =false;
	            if(isset($inputArray['direction']))
	            	$direction =$inputArray['direction'];

	            $service =false;
	            if(isset($inputArray['service']))
	            	$service =$inputArray['service'];

	            $division =false;
	            if(isset($inputArray['division']))
	            	$division =$inputArray['division'];

	            $usersimple =false;
	            if(isset($inputArray['usersimple']))
	            	$usersimple =$inputArray['usersimple'];

	            $parametre =false;
	            if(isset($inputArray['parametre']))
	            	$parametre =$inputArray['parametre'];


			$userconnect = new AuthController;
			$userconnectdata = $userconnect->user_data_by_token($request->token);

            $profil = new Profil;
            $profil->CodeProfil = $code;
			$profil->LibelleProfil = $libelle;
			$profil->saisie = $saisie;
			$profil->validation = $validation;
			$profil->sgm = $sgm;
			$profil->dc = $dc;
			$profil->ministre = $ministre;

			$profil->direction = $direction;
			$profil->service = $service;
			$profil->division = $division;
			$profil->usersimple = $usersimple;
			$profil->parametre = $parametre;

            $profil->created_by = $userconnectdata->id;
            $profil->updated_by = $userconnectdata->id;
            $profil->save();

            return $this->index();
        }
        catch(\Illuminate\Database\QueryException $ex){
          \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors de " .
                "l'enregistrement.");
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
		$ProfilSearch = Profil::where("id","=",$id)->get();

		if($ProfilSearch->isEmpty()){
			return array(
				"status"=>"error",
				"message"=>"Aucun profil retrouvé"
				);
		}
		else {
			$profil = $ProfilSearch->first();
			return $profil;
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
                $inputArray = $request->all();

                //verifie les champs fournis
                if (!isset($inputArray['libelle']) && !isset($inputArray['id'])) {
                	//controle d existence
                    return array("status" => "error",
                        "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
                }

                $code = $inputArray['code'];
            	$libelle = $inputArray['libelle'];
                $id = $inputArray['id'];

                $saisie =false;
	            if(isset($inputArray['saisie']))
	            	$saisie =$inputArray['saisie'];

	            $validation =false;
	            if(isset($inputArray['validation']))
	            	$validation =$inputArray['validation'];

	            $sgm =false;
	            if(isset($inputArray['sgm']))
	            	$sgm =$inputArray['sgm'];

	            $dc =false;
	            if(isset($inputArray['dc']))
	            	$dc =$inputArray['dc'];

	            $ministre =false;
	            if(isset($inputArray['ministre']))
	            	$ministre =$inputArray['ministre'];

	            $direction =false;
	            if(isset($inputArray['direction']))
	            	$direction =$inputArray['direction'];

	            $service =false;
	            if(isset($inputArray['service']))
	            	$service =$inputArray['service'];

	            $division =false;
	            if(isset($inputArray['division']))
	            	$division =$inputArray['division'];

	            $usersimple =false;
	            if(isset($inputArray['usersimple']))
	            	$usersimple =$inputArray['usersimple'];

	            $parametre =false;
	            if(isset($inputArray['parametre']))
	            	$parametre =$inputArray['parametre'];

				$userconnect = new AuthController;
				$userconnectdata = $userconnect->user_data_by_token($request->token);
                // Récuperer lae profil
                $profil = Profil::find($id);

	            $profil->CodeProfil = $code;
				$profil->LibelleProfil = $libelle;
	            $profil->created_by = $userconnectdata->id;
	            $profil->updated_by = $userconnectdata->id;

	            $profil->saisie = $saisie;
				$profil->validation = $validation;
				$profil->sgm = $sgm;
				$profil->dc = $dc;
				$profil->ministre = $ministre;

				$profil->direction = $direction;
				$profil->service = $service;
				$profil->division = $division;
				$profil->usersimple = $usersimple;
				$profil->parametre = $parametre;

	            $profil->save();

                return $this->index();
           }
            catch(\Illuminate\Database\QueryException $ex){
              \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors de " .
                "votre enregistrement. Veuillez contactez l'administrateur");
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
		Profil::find($id)->delete();
		return $this->index();
	}


}
