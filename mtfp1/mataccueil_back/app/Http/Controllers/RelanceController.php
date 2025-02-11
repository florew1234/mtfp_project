<?php
 namespace App\Http\Controllers;


use App\Http\Controllers\Controller;


use App\Http\Controllers\AuthController;


use Illuminate\Http\Request;

use App\Models\Relance;

use App\Models\Structure;
use App\Models\Utilisateur;
use App\Models\Acteur;




use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use Mail;
use DB;
class RelanceController extends Controller
{

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
            $result = Relance::with(['structure'])->where('idEntite',$idEntite)->get();

            return $result;

            } catch(\Illuminate\Database\QueryException $ex){

                    \Log::error($ex->getMessage());

            $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
        }catch(\Exception $e){
                    \Log::error($e->getMessage());

          $error =
        array("status" => "error", "message" => "Une erreur est survenue lors du" .
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
    try {
            $param=$request->all();
            $param['date_envoi']=date("Y-m-d H:m:i");
            $relance= new Relance;
            $relance->message=$param['message'];
            $relance->idStructure=$param['idStructure'];
            $relance->idEntite=$param['idEntite'];
            $relance->date_envoi=$param['date_envoi'];
            $relance->etat= 'e';
            $relance->save();

            $text=$request->message;
            $sujet="PDA (MatAccueil) - Service Relations Usagers";
          
            $acteur=Acteur::where('idStructure',$request->idStructure)->first();
            $getUser=Utilisateur::where("idAgent","=",$acteur->id)->first();
            $email=$getUser->email;
           
            Mail::raw($text, function ($message) use ($email,$text,$sujet) {
                $message->from('travail.infos@gouv.bj', 'PDA');
                $message->to($email);
                $message->subject($sujet);
            });

            return $this->index($relance->idEntite);

            } catch(\Illuminate\Database\QueryException $ex){

                    \Log::error($ex->getMessage());

            $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
            }catch(\Exception $e){
                    \Log::error($e->getMessage());
                    $error =
            array("status" => "error", "message" =>"Une erreur est survenue lors de l'enregistrement. Veuillez contacter l'administrateur." );
            return $error;
            }
    }

 }

