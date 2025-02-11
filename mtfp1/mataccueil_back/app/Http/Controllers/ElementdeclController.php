<?php namespace App\Http\Controllers;

use App\Http\Requests;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;

use Illuminate\Http\Request;

use App\Models\Elementdecl;

use App\Models\Elementdeclservice;

use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use DB;

class ElementdeclController extends Controller {

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

			$ElementSearch = Elementdecl::where('idEntite',$idEntite)->get();

			foreach ($ElementSearch as $Element) {
				$Element->listeservices;
			}
			return $ElementSearch;

		} catch(\Illuminate\Database\QueryException $ex){
            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
        }catch(\Exception $e){
            $error = array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur" );
            return $error;
        }
	}


	public function getLine($id)
	{
		try { 
			$ElementSearch = Elementdecl::where("id","=",$id)->get();
			foreach ($ElementSearch as $Element) {
				$Element->listeservices;
			}
			return $ElementSearch;

		} catch(\Illuminate\Database\QueryException $ex){
            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
        }catch(\Exception $e){
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
	        $inputArray = $request->all();

         //verifie les champs fournis
          if (!( isset($inputArray['libelle']) && isset($inputArray['id'])
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }		

          

            $libelle = $inputArray['libelle'];

            $listedemarches = $inputArray['demarches'];

			$userconnect = new AuthController;
			$userconnectdata = $userconnect->user_data_by_token($request->token);

            $Elementdecl = new Elementdecl;
			$Elementdecl->libelle = $libelle;
			
			$Elementdecl->idEntite=$request->idEntite;
            $Elementdecl->created_by = $userconnectdata->id;
            $Elementdecl->updated_by = $userconnectdata->id;
            $Elementdecl->save();

            //print_r($listedemarches);
            foreach($listedemarches as $demarche){

            	$ElementdeclService= new Elementdeclservice;
            	$ElementdeclService->idElementdecl=$Elementdecl->id;
            	$ElementdeclService->idService=$demarche["code"];
            	$ElementdeclService->nameService=$demarche["name"];
            	
            	$ElementdeclService->save();

            }

            return $this->index($Elementdecl->idEntite);
        }
        catch(\Illuminate\Database\QueryException $ex){
            $error = array("status" => "error", "message" => "Cet élément existe déjà !" );
            //\Log::error($ex->getMessage());
            return $error;
        }
        catch(\Exception $ex){
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
		$ElementdeclSearch = Elementdecl::where("id","=",$id)->get();

		if($ElementdeclSearch->isEmpty()){
			return array(
				"status"=>"error",
				"message"=>"Aucune étape retrouvée"
				);
		}
		else {
			$Elementdecl = $ElementdeclSearch->first();
			return $Elementdecl;
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

            	$libelle = $inputArray['libelle'];
            	$listedemarches = $inputArray['demarches'];

                $id = $inputArray['id'];

				$userconnect = new AuthController;
				$userconnectdata = $userconnect->user_data_by_token($request->token);
                // Récuperer lae Elementdecl
                $Elementdecl = Elementdecl::find($id);

				$Elementdecl->libelle = $libelle;
	            $Elementdecl->created_by = $userconnectdata->id;
	            $Elementdecl->updated_by = $userconnectdata->id;

	            $Elementdecl->save();

	            DB::table('outilcollecte_elementdecl_service')->where('idElementdecl', $id)->delete();

	            foreach($listedemarches as $demarche){

	            	$ElementdeclService= new Elementdeclservice;
	            	$ElementdeclService->idElementdecl=$Elementdecl->id;
	            	$ElementdeclService->idService=$demarche["code"];
	            	$ElementdeclService->nameService=$demarche["name"];
	            	
	            	$ElementdeclService->save();

            	}

                return $this->index($Elementdecl->idEntite);
           }
            catch(\Illuminate\Database\QueryException $ex){
            $error = array("status" => "error", "message" => "Erreur de connexion à la base de données.");
            return $error;
        }catch(\Exception $e){
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
		Elementdecl::find($id)->delete();

		DB::table('outilcollecte_elementdecl_service')->where('idElementdecl', $id)->delete();

		return array('success' => true );
	}


}
