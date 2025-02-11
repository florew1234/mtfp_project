<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;
use App\Helpers\Factory\ParamsFactory;

use Illuminate\Http\Request;

use App\User;
use App\Models\AttribCom;
use App\Models\Acteur;
use Illuminate\Support\Facades\Storage;

use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use App\Utilities\FileStorage;
use DB,PDF,Mail,DateTime,DateTimeZone,Str;

class AttribuController extends Controller {


	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('jwt.auth',['except' =>['index']]);
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($iduser)
	{
		try {
			$attribu = AttribCom::where('id_user',$iduser)->with(['acteur_att','commune','commune.departement'])
								->get();
			return $attribu;

		} catch(\Illuminate\Database\QueryException $ex){
            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contacter l'administrateur" );
        }catch(\Exception $ex){

            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contacter l'administrateur" );
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
			
			$idComm=null;
			if(isset($inputArray['idComm'])){$idComm = $inputArray['idComm'];}
			
			$id_user=null;
			if(isset($inputArray['id_user'])){$id_user = $inputArray['id_user'];}
			
			if(AttribCom::where('id_com',$idComm)->where('id_user',$id_user)->count() != 0 ) {
				return array("status" => "error","success" => false );
			}
			
            $att = new AttribCom;
			$att->id_com = $idComm;
			$att->id_user = $id_user;
            $att->save();

			return array("status" => "success","success" => true );
        }
        catch(\Illuminate\Database\QueryException $ex){
            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" =>"Une erreur est survenue  " .
                "au cours du traitement. Veuillez contacter l'administrateur", "error"=>$ex->getMessage() );
            //\Log::error($ex->getMessage());
            return $error;
        }
        catch(\Exception $ex){
          \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors de " .
                "votre tentative de connexion. Veuillez contacter l'administrateur" );
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
		$ServiceSearch = AttribCom::where("id","=",$id)->get();

		if($ServiceSearch->isEmpty()){
			return array(
				"status"=>"error",
				"message"=>"Aucune étape retrouvée"
				);
		}
		else {
			$Service = $ServiceSearch->first();
			return $Service;
		}
	}

	/**
	 * update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Request $request)
	{
				try{
                $inputArray =  $request->all();
				$id = $inputArray['id'];
                //verifie les champs fournis
                if (!isset($id)) {
                    return array("status" => "error",
                        "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
                }

            	$idComm = $inputArray['idComm'];
            	$id_user = $inputArray['id_user'];
				$check = AttribCom::where('id_user',$id_user)->where('id_com',$idComm)->first();
				if(isset($check)){
					if($check->id != $id){
						return array("status" => "error","success" => false );
					}
				}
				$att = AttribCom::find($inputArray['id']);
				$att->id_com = $idComm;
	            $att->save();

				return array("status" => "success","success" => true );
           }
            catch(\Illuminate\Database\QueryException $ex){
              \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue au cours du traitement. Veuillez reessayer plus tard.");
            return $error;
        }catch(\Exception $ex){

          \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" =>"Une erreur est survenue. Veuillez reessayer plus tard.".$ex->getMessage());
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
		Service::find($id)->delete();
		return array('success' => true );
	}



}
