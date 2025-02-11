<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;

use Illuminate\Http\Request;

use App\Models\Typeua;

use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use DB;

class TypeuaController extends Controller {

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

			return Typeua::where('idEntite',$idEntite)->orderBy('LibelleTypeUA')->get();

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
          if (!( isset($inputArray['LibelleTypeUA'])  
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }

            //Génération du code
                $getcode = DB::table('sygec_typeuniteadmin')->select(DB::raw('max(CAST(CodeTypeUA AS INT)) as code'))->get();
            $code=1;
            if(!empty($getcode))
            	$code+=$getcode[0]->code;

            $libelle = $inputArray['LibelleTypeUA'];

			$userconnect = new AuthController;
			$userconnectdata = $userconnect->user_data_by_token($request->token);

            $Typeua = new Typeua;
            $Typeua->CodeTypeua = $code;
			$Typeua->LibelleTypeUA = $libelle;
			$Typeua->idEntite=$request->idEntite;

            $Typeua->created_by = $userconnectdata->id;
            $Typeua->updated_by = $userconnectdata->id;
            $Typeua->save();
			
            return $this->index($request->idEntite);
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
		$TypeuaSearch = Typeua::where("id","=",$id)->get();

		if($TypeuaSearch->isEmpty()){
			return array(
				"status"=>"error",
				"message"=>"Aucune unité administrative retrouvée"
				);
		}
		else {
			$Typeua = $TypeuaSearch->first();
			return $Typeua;
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
                if (!isset($inputArray['LibelleTypeUA']) && !isset($inputArray['id'])) {
                	//controle d existence
                    return array("status" => "error",
                        "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
                }

                $code = $inputArray['CodeTypeUA'];
            	$libelle = $inputArray['LibelleTypeUA'];

                $id = $inputArray['id'];

				$userconnect = new AuthController;
				$userconnectdata = $userconnect->user_data_by_token($request->token);
                // Récuperer lae Typeua
                $Typeua = Typeua::find($id);

	            $Typeua->CodeTypeua = $code;
				$Typeua->LibelleTypeUA = $libelle;
	            $Typeua->created_by = $userconnectdata->id;
	            $Typeua->updated_by = $userconnectdata->id;

	            $Typeua->save();

                return $this->index($Typeua->idEntite);
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
		Typeua::find($id)->delete();
		return array('success' => true );
	}


}
