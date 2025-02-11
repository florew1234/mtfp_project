<?php
 namespace App\Http\Controllers;
use App\Http\Requests;


use App\Http\Controllers\Controller;


use App\Http\Controllers\AuthController;


use Illuminate\Http\Request;

use App\Models\Suggestion;

use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use DB;
class SuggestionController extends Controller
{
    public function __construct()
    {
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
            $result = Suggestion::get();

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

}