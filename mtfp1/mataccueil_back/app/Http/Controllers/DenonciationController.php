<?php
 namespace App\Http\Controllers;
use App\Http\Requests;


use App\Http\Controllers\Controller;


use App\Http\Controllers\AuthController;


use Illuminate\Http\Request;

use App\Models\Denonciation;

use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use DB;
class DenonciationController extends Controller
{
    public function __construct()
    {
    }


    /**
         * Display a listing of the resource.

         *

         * @return Response

         */


    public function index()
    {
        try {
            $result = Denonciation::get();

            return $result;
        } catch (\Illuminate\Database\QueryException $ex) {
            \Log::error($ex->getMessage());

            $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            $error = array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur" );
            return $error;
        }
    }

    public function store(Request $request)
	{
		try{
            $params= $request->all();
            $data =  Denonciation::create($params);
		
            return "yes";
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

    

}