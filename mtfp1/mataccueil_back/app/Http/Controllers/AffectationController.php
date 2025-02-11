<?php
 namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Http\Controllers\AuthController;

use Illuminate\Http\Request;

use App\Models\Affectation;
use App\Models\Requete;

use App\Models\Structure;

use App\Models\Utilisateur;
use App\Models\Acteur;
use App\Models\Parcoursrequete;
use App\Models\Activity;

use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Mail;
use DB;
class AffectationController extends Controller
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
            $result = Affectation::with(['requetes','structure','requetes.usager','requetes.service'])
            ->where('idEntite',$idEntite)->get();

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

            $inputArray =  $request->all();
            //verifie les champs fournis
          if (!( isset($inputArray['idRequete'])))
            { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }
            $idRequete=0;
            if(isset($inputArray['idRequete'])) $idRequete= $inputArray['idRequete'];

            $idStructure=0;
            if(isset($inputArray['idStructure'])) $idStructure= $inputArray['idStructure'];

            $idEtape=1;
            if(isset($inputArray['idEtape'])) $idEtape= $inputArray['idEtape'];

            $typeStructure='Direction';
            if(isset($inputArray['typeStructure'])) $typeStructure= $inputArray['typeStructure'];


            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);

            $checkaffect=Affectation::where("idStructure","=",$idStructure)->where("typeStructure","=",$typeStructure)->where("idRequete","=",$idRequete)->get();

            if(count($checkaffect)>0)
            {
              $error=array("status" => "error", "message" => "Cette requête a été déjà affectée à la même structure." );
              return $error;
            }

            $affectation= new Affectation;

            $affectation->idRequete =  $idRequete;
            $affectation->idStructure = $idStructure;
            $affectation->typeStructure = $typeStructure;
            $affectation->idEntite=$request->idEntite;
            $affectation->created_by = $userconnectdata->id;
            $affectation->updated_by = $userconnectdata->id;
            $affectation->save();

            $requete=Requete::find($idRequete);
            $requete->idServiceAffecte=$idStructure;
            $requete->save();


            //Enregistrement parcours
            $check=Parcoursrequete::where("idStructure","=",$idStructure)->where("idRequete","=",$requete->id)->get();

            if(count($check)==0)
            {
              $parcours=new Parcoursrequete;
              $parcours->typeStructure=$typeStructure;
              $parcours->idRequete=$requete->id;
              $parcours->idStructure=$idStructure;
              $parcours->idEtape=$idEtape;
              $parcours->idEntite=$request->idEntite;
              $parcours->dateArrivee=date("Y-m-d h:m:i");
              $parcours->save();
            }

            //ACTIVITY
            Activity::SaveActivity($userconnectdata->id,"Affectaction d'une reqête");

            //Notification à la structure
            $getstructure=Structure::find($idStructure);
            $getActeur=Acteur::where("idStructure","=",$getstructure->id)->first();
            $getUser=Utilisateur::where("idAgent","=",$getActeur->id)->first();

            if($getUser !== null)                   //count($getstructure)>0)
            {
              $email=$getUser->email;
              if($email!="")
              {
                $this->sendmail($email,"Une requête (Objet : ".$requete->objet.") a été affectée à votre structure (".$getstructure->libelle."). Pour la traiter, rendez-vous sur la plateforme : https://mataccueil.gouv.bj/login.");
              }
            }

            //return $this->getListeAffectation($userconnectdata->id,$typeStructure);

        } catch(\Illuminate\Database\QueryException $ex){
          \Log::error($ex->getMessage());

        $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );

        }catch(\Exception $e){
          \Log::error($e->getMessage());

          $error = array("status" => "error", "message" => "Une erreur est survenue lors du" .
                             " chargement des connexions. Veuillez contactez l'administrateur" );
         return $error;
        }
    }
/**
     * update a newly created resource in storage.

     *

     * @return Response

     */
public function update($id,Request $request)
{
        try {

            $inputArray =  $request->all();
            //verifie les champs fournis
          if (!( isset($inputArray['idRequete'])))
            { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }

            $idRequete='';
            if(isset($inputArray['idRequete'])) $idRequete= $inputArray['idRequete'];
            $idStructure='';
            if(isset($inputArray['idStructure'])) $idStructure= $inputArray['idStructure'];
            $dateAffectation='';
            if(isset($inputArray['dateAffectation'])) $dateAffectation= $inputArray['dateAffectation'];
            $dateEnvoiReponse='';
            if(isset($inputArray['dateEnvoiReponse'])) $dateEnvoiReponse= $inputArray['dateEnvoiReponse'];
            $texteReponseApportee='';
            if(isset($inputArray['texteReponseApportee'])) $texteReponseApportee= $inputArray['texteReponseApportee'];
            $SiReponseDisponible='';
            if(isset($inputArray['SiReponseDisponible'])) $SiReponseDisponible= $inputArray['SiReponseDisponible'];

            $affectation=Affectation::find($id);
            $affectation->idRequete=$idRequete;
            $affectation->idStructure=$idStructure;
            $affectation->dateAffectation=$dateAffectation;
            $affectation->dateEnvoiReponse=$dateEnvoiReponse;
            $affectation->texteReponseApportee=$texteReponseApportee;
            $affectation->SiReponseDisponible=$SiReponseDisponible;

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            $affectation->created_by = $userconnectdata->id;
            $affectation->updated_by = $userconnectdata->id;
            $affectation->save();
            return $this->index($parcours->idEntite);

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
               * Remove the specified resource from storage.
               *
               * @param  int  id
               * @return Response
               */

          public function destroy($id){
          Affectation::find($id)->delete();
          return array('success' => true );
 }



 // Liste des courriers
    public static function getListeAffectation(Request $request) {
        try {
                $input = $request->query();

                $idUser=$request->get('idUser');
                $typeStructure=$request->get('typeStructure');
                $plainte=$request->get('plainte');

                $getuser=Utilisateur::find($idUser);

                $result =array();

                $idagent=$getuser->idagent;

                $getAgent=Acteur::find($idagent);

                $idStructure="";

                if( $getAgent !== null ) //count($getAgent)>0)
                {
                    $idStructure=$getAgent->idStructure;

                    $listeStructure=Structure::where('idParent','=',$idStructure)->select('id')->get()->toArray();
                    // return response($listeStructure);
                    
                    $result = Affectation::WhereIn('idStructure',$listeStructure)
                    ->with(['structure','requetes','requetes.usager','requetes.service','requetes.parcours'])
                    ->whereHas('requetes', function($q) use($plainte) { $q->where('plainte', $plainte); })
                    ->where('typeStructure','=',$typeStructure)->orderBy('id','desc')
                    ->paginate(10);
                }
                
              // // where('idEntite',$idEntite)->
              // $query = Requete::with(['usager','entite_receive','entite','reponses_rapide','service','service.type','nature','etape','notes','reponse' => function ($query) {
              //   return $query->orderBy('id', 'DESC');
              // },'affectation','parcours']
              // );
                return response($result);

        } catch(\Illuminate\Database\QueryException $ex){
          \Log::error($ex->getMessage());
            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" );
        }catch(\Exception $e){

          \Log::error($e->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue au cours du traitement . Veuillez contactez l'administrateur" );
            return $error;
        }

    }



    public static function sendmail($email,$text="Enregistrement de votre requête",$sujet="PDA (MatAccueil) - Service Relations Usagers"){

      Mail::raw($text, function ($message) use ($email,$text,$sujet) {
        $message->from('travail.infos@gouv.bj', 'PDA');
        $message->to($email);
        $message->subject($sujet);
    });


   }


 }

