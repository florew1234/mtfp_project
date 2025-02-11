<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;

use Illuminate\Http\Request;

use App\Models\Statthematique;

use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;


use DB;

class StatthematiqueController extends Controller {

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
	public function index($idEntite)
	{
		try { 


			$StatthematiqueSearch = Statthematique::where('idEntite',$idEntite)->orderBy("stat","DESC")->get();

			foreach ($StatthematiqueSearch as $Statthematique) {
				$Statthematique->thematique_stat;
			}

			return $StatthematiqueSearch;

		} catch(\Illuminate\Database\QueryException $ex){
            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
        }catch(\Exception $e){
            $error = array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur" );
            return $error;
        }
	}


}
