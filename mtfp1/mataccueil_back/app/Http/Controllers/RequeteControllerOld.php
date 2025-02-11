<?php
namespace App\Http\Controllers;
use App\Helpers\Factory\ParamsFactory;

use Request;
use App\Http\Requests;

;
//use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;


use App\Http\Controllers\Controller;


use App\Http\Controllers\AuthController;


//use Request;

use App\Models\Requete;

use App\Models\Usager;

use App\Models\Service;
use App\Models\Utilisateur;
use App\Models\Profil;
use App\Models\Acteur;
use App\Models\Etape;
use App\Models\Noteusager;

use App\Models\Affectation;
use App\Models\Reponse;
use App\Models\Structure;
use App\Models\Parcoursrequete;
use App\Models\Reponserapide;
use App\Models\Suggestion;

use App\Models\Type;
use App\Models\Departement;
use App\Models\Parametre;
use App\Helpers\Carbon\Carbon;

use Mail;

use DB;

use Dompdf\Dompdf;

use Tymon\JWTAuth\JWTAuth;

 class RequeteControllerOld extends Controller
{
/*
public function __construct() {
$this->middleware('jwt.auth');
    }
*/

  protected $user;


public function __construct() {
        //$this->user = JWTAuth::parseToken()->authenticate();

        $this->middleware('jwt.auth', ['except' => ['index','store', 'update',
          'transmettreRequete', 'test', 'getRequeteByCode','noterRequete','getRequeteByUsager', 'createRequestAsUsager', 'envoiFichier','destroy']]);
    }




  /**
     * Display a listing of the resource.

     *

     * @return Response

     */


    public function index(Request $request)
    {
      try {
        $input = Request::all();


        $plainte = $input['plainte'];

        if(isset($input['search'])){
            $search = $input['search'];

            $items = Requete::where("plainte","=", $plainte)
              ->where("visible","=", 1) //newly added
              ->with(['usager','service','service','service.type', 'nature','notes', 'reponse' => function ($query) { return $query->orderBy('id','ASC'); },'affectation','parcours'])->orderBy('id','desc')
              ->where("objet", "LIKE", "%{$search}%")
              ->orWhereHas('usager', function($q) use($search) {
                $q->where('email',"LIKE", "%{$search}%");
                })
              ->orWhereHas('usager', function($q) use($search) {
                $q->where('nom',"LIKE", "%{$search}%");
                })
              ->orWhereHas('usager', function($q) use($search) {
                $q->where('prenoms',"LIKE", "%{$search}%");
                })
              ->orWhereHas('usager', function($q) use($search) {
                $q->where(DB::raw("CONCAT(`nom`, ' ', `prenoms`)"),"LIKE", "%{$search}%");
                })
              ->orWhereHas('service', function($q) use($search) {
                $q->where('libelle',"LIKE", "%{$search}%");
                })
              ->orWhereHas('nature', function($q) use($search) {
                $q->where('libelle',"LIKE", "%{$search}%");
                })
              ->orWhere("codeRequete", "LIKE", "%{$search}%")
              ->paginate(10);

        }else{
          $items = Requete::where("plainte","=", $plainte)
            ->where("visible","=", 1)
            ->with(['usager','service','service.type','etape', 'nature', 'notes','reponse' => function ($query) { return $query->orderBy('id','ASC'); },'affectation','parcours'])->orderBy('id','desc')
              ->paginate(10);
        }

        return response($items);


        } catch(\Illuminate\Database\QueryException $ex){
        \Log::error($ex->getMessage());

        $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
        }catch(\Exception $ex){

        \Log::error($ex->getMessage());
        $error =  array("status" => "error", "message" => "Une erreur est survenue lors du" .
                             " chargement des connexions. Veuillez contactez l'administrateur" );
         return $error;
        }
    }
/**
     * Store a newly created resource in storage.

     *

     * @return Response

     */


public function getRequeteByCode($email,$codeRequete)

    {

        try {
          $checkusager=Usager::where("email","=",$email)->get();

          $result=array();

          if(count($checkusager)>0)
          {
            $idUsager=$checkusager[0]->id;

            $result = Requete::with(['usager','service','nature','notes', 'etape','parcours'])->orderBy('id','desc')->where("CodeRequete","=",$codeRequete)->where("idUsager","=",$idUsager)->get();
          }
          else{
            $error =
            array("status" => "error", "message" => "Aucune requête trouvée." );
             return $error;
          }


        return $result;

        } catch(\Illuminate\Database\QueryException $ex){
          \Log::error($ex->getMessage());

        $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
        }catch(\Exception $ex){

          \Log::error($ex->getMessage());
          $error =  array("status" => "error", "message" => "Une erreur est survenue lors du" .
                             " chargement des connexions. Veuillez contactez l'administrateur" );
         return $error;
        }
    }


public function getRequeteByUsager($idusager,Request $request)

    {
        try {

            //$input = Request::all();
            $input = Request::get();

            //return $input;
            $result=array();
            if(isset($input['search']))     //$request->get('search'))
            {
              $search=$input['search'];
              $result = Requete::with(['usager','service', 'notes','nature','etape','parcours','affectation'])
              ->orderBy('id','desc')
              ->where("idUsager","=",$idusager)
              ->where("objet", "LIKE", "%{$search}%")
              ->orWhere("msgrequest", "LIKE", "%{$search}%")
              ->orWhere("codeRequete", "LIKE", "%{$search}%")
              ->paginate(10);
            }
            else{
              $result = Requete::with(['usager','service','notes','nature','etape','parcours','affectation'])->orderBy('id','desc')
              ->where("idUsager","=",$idusager)
              ->paginate(10);
            }


            if(count($result)>0)
            {
              return $result;
            }
            else{
              $error =
              array("status" => "error", "message" => "Aucune requête trouvée." );
               return $error;
            }


        return $result;

        } catch(\Illuminate\Database\QueryException $ex){
          \Log::error($ex->getMessage());

        $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
        }catch(\Exception $ex){

          \Log::error($ex->getMessage());

          $error = array("status" => "error", "message" => "Une erreur est survenue lors du" .
                             " chargement des connexions. Veuillez contactez l'administrateur" );
         return $error;
        }
    }



public function noterRequete()
{
        try {
          $idRequete="";
          $noteUsager="";

          $inputArray = Request::get();

          if(isset($inputArray['noteDelai'])) $noteDelai= $inputArray['noteDelai'];
          if(isset($inputArray['noteResultat'])) $noteResultat= $inputArray['noteResultat'];
          /*if(isset($inputArray['noteDisponibilite'])) $noteDisponibilite= $inputArray['noteDisponibilite'];
          if(isset($inputArray['noteOrganisation'])) $noteOrganisation= $inputArray['noteOrganisation'];
          */
  
          if(isset($inputArray['codeRequete'])) $codeRequete= $inputArray['codeRequete'];

          $commentaireNotation="";
          if(isset($inputArray['commentaireNotation'])) $commentaireNotation= $inputArray['commentaireNotation'];

          //Enregistrement note
          $check=Noteusager::where("codeReq","=",$codeRequete)->get();

          if(count($check)!=0)
            return array("status" => "success", "message" => "Vous avez déjà donné une fois votre appréciation de la prestation.");

          $note=new Noteusager;
          $note->codeReq=$codeRequete;
          $note->noteDelai=$noteDelai;
          $note->noteResultat=$noteResultat;
          
          /*$note->noteDisponibilite=$noteDisponibilite;
          $note->noteOrganisation=$noteOrganisation;
          */

          $note->commentaireNotation=$commentaireNotation;
          $note->save();

          //$noteMoy = ($noteDisponibilite+$noteResultat+$noteResultat+$noteOrganisation)/3;
          $noteMoy = ($noteResultat+$noteDelai)/2;
          $req=Requete::where('codeRequete','=',$codeRequete)->update(['noteUsager' => $noteMoy]);
         
      return array("status" => "success", "message" => "Appréciation enregistrée avec succès");


        } catch(\Illuminate\Database\QueryException $ex){
                \Log::error($ex->getMessage());

            $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );

        }catch(\Exception $ex){

          \Log::error($ex->getMessage());

          $error =  array("status" => "error", "message" => "Une erreur est survenue lors du" .
                             " chargement des connexions. Veuillez contactez l'administrateur" );
         return $error;
        }
    }


public function store(Request $request)
{
        try {

          $inputArray = Request::get();
//verifie les champs fournis
          if (!( isset($inputArray['objet']) ) )  { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }

            $idPrestation='';
            if(isset($inputArray['idPrestation'])) $idPrestation= $inputArray['idPrestation'];
            $objet='';
            if(isset($inputArray['objet'])) $objet= $inputArray['objet'];
            $msgrequest='';
            if(isset($inputArray['msgrequest'])) $msgrequest= $inputArray['msgrequest'];
            $idEtape=1;
            if(isset($inputArray['idEtape'])) $idEtape= $inputArray['idEtape'];

            $plainte=0;
            if(isset($inputArray['plainte'])) $plainte= $inputArray['plainte'];

            $interfaceRequete='SRU';
            if(isset($inputArray['interfaceRequete'])) $interfaceRequete= $inputArray['interfaceRequete'];

            $nom='';
            if(isset($inputArray['nom'])) $nom= $inputArray['nom'];
            $email='';
            if(isset($inputArray['email'])) $email= $inputArray['email'];
            $tel='';
            if(isset($inputArray['tel'])) $tel= $inputArray['tel'];
            $idDepartement='';
            if(isset($inputArray['idDepartement'])) $idDepartement= $inputArray['idDepartement'];

            $natureRequete=5; // En ligne par défaut
            if(isset($inputArray['natureRequete'])) $natureRequete= $inputArray['natureRequete'];

            $nbreJours='';
            if(isset($inputArray['nbreJours'])) $nbreJours= $inputArray['nbreJours'];

            $idUser=0;
            if(isset($inputArray['idUser'])) $idUser= $inputArray['idUser'];

            $visible=0;
            if(isset($inputArray['visible'])) $visible= $inputArray['visible'];

          $fichierJoint = "";
          if(isset($inputArray['fichier_requete'])) $fichierJoint = $inputArray['fichier_requete'];

            //Générer le CODE
            //Génération du code
                $getcode = DB::table('outilcollecte_requete')->select(DB::raw('max(code) as code'))->get();
                $code=1;
                $codeRequete="REQ000000";
                if(!empty($getcode))
                    $code+=$getcode[0]->code;

                if(($code>0) &&($code<10))
                    $codeRequete="REQ00000".$code;

                if(($code>=10) &&($code<1000))
                    $codeRequete="REQ0000".$code;

                if(($code>=1000) &&($code<10000))
                    $codeRequete="REQ000".$code;

                if(($code>=10000) &&($code<100000))
                    $codeRequete="REQ00".$code;

                if(($code>=100000) &&($code<1000000))
                    $codeRequete="REQ0".$code;
                if(($code>=1000000) &&($code<10000000))
                    $codeRequete="REQ".$code;



            //$userconnect = new AuthController;
            //$userconnectdata = $userconnect->user_data_by_token($request->token);

            $requete= new Requete;

            $requete->idPrestation=$idPrestation;
            $requete->dureePrestation=$nbreJours;
            $requete->objet=$objet;
            $requete->msgrequest=$msgrequest;
            $requete->idEtape=$idEtape;
            $requete->idEtape=$idEtape;
            $requete->codeRequete=$codeRequete;
            $requete->code=$code;
            $requete->natureRequete=$natureRequete;
            $requete->interfaceRequete=$interfaceRequete;
            $requete->plainte=$plainte;
            $requete->visible=$visible;

          $requete->fichier_joint = $fichierJoint;


            $requete->created_by = $idUser;

            // Enregistrement de l'usager s'il ne l'est pas encore
            $checkusager=Usager::where("email","=",$email)->get();


            $requete->idUsager=$checkusager[0]->id;

            $requete->save();


            // Enregistrement dans la table affectation
            $service=Service::find($idPrestation);

            $affect=new Affectation;
            $affect->typeStructure='Direction';
            $affect->idRequete=$requete->id;

            $affect->idStructure=$service->idParent;
            $affect->dateAffectation=date("Y-m-d h:m:i");
            $affect->save();

            //Notification à la structure
            $getstructure=Structure::find($service->idParent);

            if($plainte==1)
              $typeRequete="plainte";
            else $typeRequete="requête";

            if($getstructure !== null)                       ///count($getstructure)>0)
            {
              $emailstructure=$getstructure->contact;

              if($emailstructure!="")
                RequeteController::sendmail($emailstructure,"Une requête (Objet : $requete->objet) a été adressée à votre structure ($getstructure->libelle) par $nom. Pour y répondre rendez-vous sur la plateforme à l'adresse : https://mataccueil.gouv.bj/login.","MTFP : Service usager");
              }

            //Enregistrement parcours
            $parcours=new Parcoursrequete;
            $parcours->typeStructure='Direction';
            $parcours->idRequete=$requete->id;
            $parcours->idStructure=$service->idParent;
            $parcours->idEtape=1;
            $parcours->dateArrivee=date("Y-m-d h:m:i");
            $parcours->save();


            //Envoie mail
              $msg="Cher usager,
Votre requête a été bien enregistrée. Nous vous en remercions. Le code de votre requête est $codeRequete . Vous voudriez bien vous rendre sur les liens \n
- https://.demarchesmtfp.gouv.bj pour connaitre des procédures de délivrance pour toute prestation sollicitée ; \n
-  https://.demarchesmtfp.gouv.bj/main : Connectez-vous pour le suivi en ligne le traitement de votre requête.  \n Le Ministère à votre service.";

              RequeteController::sendmail($email,$msg,"MTFP : Service usager");

              //retour
          return array("status" => "success", "message" => "");


} catch(\Illuminate\Database\QueryException $ex){
          \Log::error($ex->getMessage());

$error=array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" );
}catch(\Exception $ex){

          \Log::error($ex->getMessage());

          $error = array("status" => "error", "message" => "Une erreur est survenue lors de l'enregistrement de votre requête. Veuillez contactez l'administrateur"  );
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

          $inputArray = Request::get();
          //verifie les champs fournis
          if (!( isset($inputArray['objet']) && isset($inputArray['id'])
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }

            $idPrestation='';
            if(isset($inputArray['idPrestation'])) $idPrestation= $inputArray['idPrestation'];
            $objet='';
            if(isset($inputArray['objet'])) $objet= $inputArray['objet'];
            $msgrequest='';
            if(isset($inputArray['msgrequest'])) $msgrequest= $inputArray['msgrequest'];

            $plainte=0;
            if(isset($inputArray['plainte'])) $plainte= $inputArray['plainte'];

            $idUser=1;
            if(isset($inputArray['idUser'])) $idUser= $inputArray['idUser'];

            $natureRequete=5; // En ligne par défaut
            if(isset($inputArray['natureRequete'])) $natureRequete= $inputArray['natureRequete'];

            $requete=Requete::find($id);

            $requete->idPrestation=$idPrestation;
            $requete->objet=$objet;
            $requete->natureRequete=$natureRequete;
            $requete->msgrequest=$msgrequest;
            $requete->plainte=$plainte;

            $requete->updated_by = $idUser;
            $requete->save();

            return array("status" => "success", "message" => "");

} catch(\Illuminate\Database\QueryException $ex){
          \Log::error($ex->getMessage());

$error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
}catch(\Exception $ex){

          \Log::error($ex->getMessage());
          $error =
array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur" );
 return $error;
}

 }



  ///create a request as usager
  public function createRequestAsUsager(Request $request)
  {
    try {

      $inputArray = Request::get();
//verifie les champs fournis
      if (!( isset($inputArray['objet']) ) )  { //controle d existence
        return array("status" => "error",
          "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
      }

      $idPrestation='';
      if(isset($inputArray['idPrestation'])) $idPrestation= $inputArray['idPrestation'];
      $objet='';
      if(isset($inputArray['objet'])) $objet= $inputArray['objet'];
      $msgrequest='';
      if(isset($inputArray['msgrequest'])) $msgrequest= $inputArray['msgrequest'];
      $idEtape=1;
      if(isset($inputArray['idEtape'])) $idEtape= $inputArray['idEtape'];

      $plainte=0;
      if(isset($inputArray['plainte'])) $plainte= $inputArray['plainte'];

      $interfaceRequete='SRU';
      if(isset($inputArray['interfaceRequete'])) $interfaceRequete= $inputArray['interfaceRequete'];

      $nom='';
      if(isset($inputArray['nom'])) $nom= $inputArray['nom'];
      $email='';
      if(isset($inputArray['email'])) $email= $inputArray['email'];
      $tel='';
      if(isset($inputArray['tel'])) $tel= $inputArray['tel'];
      $idDepartement='';
      if(isset($inputArray['idDepartement'])) $idDepartement= $inputArray['idDepartement'];

      $natureRequete=5; // En ligne par défaut
      if(isset($inputArray['natureRequete'])) $natureRequete= $inputArray['natureRequete'];

      $nbreJours='';
      if(isset($inputArray['nbreJours'])) $nbreJours= $inputArray['nbreJours'];

      $idUser=0;
      if(isset($inputArray['idUser'])) $idUser= $inputArray['idUser'];

      $visible=0;
      if(isset($inputArray['visible'])) $visible= $inputArray['visible'];

      //Générer le CODE
      //Génération du code
      $getcode = DB::table('outilcollecte_requete')->select(DB::raw('max(code) as code'))->get();
      $code=1;
      $codeRequete="REQ000000";
      if(!empty($getcode))
        $code+=$getcode[0]->code;

      if(($code>0) &&($code<10))
        $codeRequete="REQ00000".$code;

      if(($code>=10) &&($code<1000))
        $codeRequete="REQ0000".$code;

      if(($code>=1000) &&($code<10000))
        $codeRequete="REQ000".$code;

      if(($code>=10000) &&($code<100000))
        $codeRequete="REQ00".$code;

      if(($code>=100000) &&($code<1000000))
        $codeRequete="REQ0".$code;
      if(($code>=1000000) &&($code<10000000))
        $codeRequete="REQ".$code;


      //$userconnect = new AuthController;
      //$userconnectdata = $userconnect->user_data_by_token($request->token);

      $requete= new Requete;

      $requete->idPrestation=$idPrestation;
      $requete->dureePrestation=$nbreJours;
      $requete->objet=$objet;
      $requete->msgrequest=$msgrequest;
      $requete->idEtape=$idEtape;
      $requete->idEtape=$idEtape;
      $requete->codeRequete=$codeRequete;
      $requete->code=$code;
      $requete->natureRequete=$natureRequete;
      $requete->interfaceRequete=$interfaceRequete;
      $requete->plainte=$plainte;
      $requete->visible= $visible;
      $requete->fichier_joint= "";


      $requete->created_by = $idUser;

      // Enregistrement de l'usager s'il ne l'est pas encore
      $checkusager=Usager::where("email","=",$email)->get();


      $requete->idUsager=$checkusager[0]->id;

      $requete->save();

      return array("status" => "success", "message" => "","data" => $requete->id);



    } catch(\Illuminate\Database\QueryException $ex){
      \Log::error($ex->getMessage());

      $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" );
      return $error;
    }catch(\Exception $ex){

      \Log::error($ex->getMessage());

      $error = array("status" => "error", "message" => "Une erreur est survenue lors de l'enregistrement de votre requête. Veuillez contactez l'administrateur"  );
      return $error;
    }
  }//end createRequestAsUsager



  /**
     * Remove the specified resource from storage.
     *
     * @param  int  id
     * @return Response
     */

public function destroy($id){
    Requete::find($id)->delete();
 }


 // Liste des courriers
  public static function getRequeteByUser(Request $request) {
    try {

        $input = Request::all();


        $idUser= $input['idUser'];
        

        $getUser=Utilisateur::find($idUser);
        $getProfil=Profil::find($getUser->idprofil);


        $resultat =array();

    

            if(isset($input['search']))
            {
              $search=$input['search'];

              $query = Requete::with(['usager','service','service.type','nature','notes', 'etape','reponse' => function ($query) { return $query->orderBy('id','ASC'); },'affectation','parcours'])
                ->where("visible","=", true)
              ->where("objet", "LIKE", "%{$search}%")
              ->orWhere("msgrequest", "LIKE", "%{$search}%")
              ->orWhere("codeRequete", "LIKE", "%{$search}%")
              ->orWhereHas('usager', function($q) use($search) {
                $q->where('email',"LIKE", "%{$search}%");
                })
              ->orWhereHas('usager', function($q) use($search) {
                $q->where('nom',"LIKE", "%{$search}%");
                })
              ->orWhereHas('usager', function($q) use($search) {
                $q->where('prenoms',"LIKE", "%{$search}%");
                })
              ->orWhereHas('usager', function($q) use($search) {
                $q->where(DB::raw("CONCAT(`nom`, ' ', `prenoms`)"),"LIKE", "%{$search}%");
                })
              ->orWhereHas('service', function($q) use($search) {
                $q->where('libelle',"LIKE", "%{$search}%");
                })
              ->orWhereHas('service.type', function($q) use($search) {
                $q->where('libelle',"LIKE", "%{$search}%");
                });

              

            }else{
                  $query = Requete::
                  with(['usager','service','service.type','nature','etape','notes',
                    'reponse' => function ($query) 
                    { return $query->orderBy('id','ASC'); },'affectation','parcours']
                  );
                  
                  
            }

            if( ($getProfil->parametre!=1) && ($getProfil->saisie!=1) &&  ($getProfil->sgm!=1) && ($getProfil->dc!=1) && ($getProfil->ministre!=1) )
            {
              $idagent=$getUser->idagent;

              $getAgent=Acteur::find($idagent);

              $idStructure="";

              if($getAgent !== null ) //count($getAgent)>0)
              {
                $idStructure=$getAgent->idStructure;

                $query = $query->whereHas('affectation', function($q) use($idStructure) {
                    $q->where('idStructure',"=", $idStructure);
                    });
              }
            }

            if(isset($input['traiteOuiNon']))
                $query=$query->where("traiteOuiNon","=",$input['traiteOuiNon']);

            if(isset($input['plainte']))
                $query=$query->where("plainte","=",$input['plainte']);

            if(isset($input['startDate'])){
               $startDate =$input["startDate"];               
               $endDate = $input["endDate"]; 

               $query=$query->whereDate('dateRequete', '>=', $startDate)
                            ->whereDate('dateRequete', '<=', $endDate);
            }

            $resultat = $query->orderBy('id','desc')
                        ->paginate(10);

          


        return response($resultat);

    } catch(\Illuminate\Database\QueryException $ex){
            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" );
        }catch(\Exception $ex){

            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du chargement des requêtes. Veuillez contactez l'administrateur" );
            return $error;
        }

  }


 // Liste des courriers
  public static function getCountRequeteByUser($idUser) {
    try {
        $getuser=Utilisateur::find($idUser);

        $result =0;



          $idagent=$getuser->idagent;

          $getAgent=Acteur::find($idagent);

          $idStructure="";

          if(count($getAgent)>0)
          {
            $idStructure=$getAgent->idStructure;

            $result=Affectation::where("idStructure","=",$idStructure)->count();

          }


        return $result;

    } catch(\Illuminate\Database\QueryException $ex){
        \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" );
        }catch(\Exception $ex){

            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue" );
            return $error;
        }

  }



public function saveReponse(\Illuminate\Http\Request $request)
{
        try {

            $inputArray = Request::get();
            //verifie les champs fournis
          if (!( isset($inputArray['idRequete'])))
            { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }

            //return json_encode($request->token);

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);

            $getuser=Utilisateur::find($userconnectdata->id);
            $idagent=$getuser->idagent;
            $getAgent=Acteur::find($idagent);

            $idStructure="";
            if($getAgent !== null)               //count($getAgent)>0)
              $idStructure=$getAgent->idStructure;




            $idRequete=0;
            if(isset($inputArray['idRequete'])) $idRequete= $inputArray['idRequete'];

            $raisonRejet="";
            if(isset($inputArray['raisonRejet'])) $raisonRejet= $inputArray['raisonRejet'];

            $interrompu=false;
            if(isset($inputArray['interrompu'])) $interrompu= $inputArray['interrompu'];

            $rejete=false;
            if(isset($inputArray['rejete'])) $rejet= $inputArray['rejete'];


            $texteReponse="";
            if(isset($inputArray['texteReponse'])) $texteReponse= $inputArray['texteReponse'];

            $typeStructure='Division';
            if(isset($inputArray['typeStructure'])) $typeStructure= $inputArray['typeStructure'];


            if(isset($inputArray['idEtape'])) $idEtape= $inputArray['idEtape'];




            //Check if reponse exist
            $checkreponse=Reponse::where("idStructure","=",$idStructure)->where("typeStructure","=",$typeStructure)->where("idRequete","=",$idRequete)->get();
            if(count($checkreponse)==0)
            {
              $reponse=new Reponse;

              $reponse->texteReponse=$texteReponse;
              $reponse->idStructure=$idStructure;
              $reponse->idRequete=$idRequete;
              $reponse->typeStructure=$typeStructure;
              $reponse->interrompu=$interrompu;
              $reponse->rejete=$rejete;
              $reponse->raisonRejet=$raisonRejet;
              $reponse->save();
            }
            else
            {
              $reponse=Reponse::find($checkreponse[0]->id);
              $reponse->texteReponse=$texteReponse;
              $reponse->interrompu=$interrompu;
              $reponse->rejete=$rejete;
              $reponse->raisonRejet=$raisonRejet;
              $reponse->save();
            }

            $req=Requete::find($idRequete);
            $req->interrompu=$interrompu;
            $req->rejete=$rejete;
            $req->raisonRejet=$raisonRejet;
            $req->save();

            if($typeStructure=='Direction')
            {
                $req1=Requete::find($idRequete);
                $req1->reponseStructure=$texteReponse;

                $req1->save();
            }

        } catch(\Illuminate\Database\QueryException $ex){

          \Log::error($ex->getMessage());

        $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );

        }catch(\Exception $ex){

          \Log::error($ex->getMessage());
          $error = array("status" => "error", "message" =>"Une erreur est survenue au cours de l'enregistrement. Contactez l'administrateur.");
          return $error;
        }
    }



public function transmettreReponse(\Illuminate\Http\Request $request)
{

  try {
            $inputArray = Request::get();
            //verifie les champs fournis
          if (!( isset($inputArray['idRequete'])))
            { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }
            $idRequete=0;
            if(isset($inputArray['idRequete'])) $idRequete= $inputArray['idRequete'];

            if(isset($inputArray['idEtape'])) $idEtape= $inputArray['idEtape'];

            $typeStructure='Division';

            if(isset($inputArray['typeStructure'])) $typeStructure= $inputArray['typeStructure'];

            if(isset($inputArray['typeSuperieur'])) $typeSuperieur= $inputArray['typeSuperieur'];


            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);

            $getuser=Utilisateur::find($userconnectdata->id);

            $idagent=$getuser->idagent;

            $getAgent=Acteur::find($idagent);

            $idStructure="";

            if( $getAgent !== null )     ///count($getAgent)>0)
              $idStructure=$getAgent->idStructure;


            $req=Requete::find($idRequete);

            if($typeStructure!='Direction')
            {
              //Get Reponse
              $getreponse=Reponse::where("idRequete",'=',$idRequete)->where("idStructure",'=',$idStructure)->get();

              $reponse=new Reponse;

              $reponse->texteReponse=$getreponse[0]->texteReponse;

              //Récupérer l'ID parent
              $structure=Structure::find($idStructure);

              $reponse->idStructure=$structure->idParent;

              $reponse->idRequete=$idRequete;

              if($typeStructure=='Division')
                $reponse->typeStructure='Service';

              if($typeStructure=='Service')
                $reponse->typeStructure='Direction';

              $reponse->dateTransmission  = date("Y-m-d H:m:i");

              $reponse->save();


              //Enregistrement parcours
              $parcours=new Parcoursrequete;
              $parcours->typeStructure=$typeSuperieur;
              $parcours->idRequete=$req->id;
              $parcours->idStructure=$structure->idParent;
              $parcours->idEtape=$idEtape;
              $parcours->dateArrivee=date("Y-m-d h:m:i");
              $parcours->save();


              //Notification à la structure
              $getstructure=Structure::find($structure->idParent);

              if($getstructure !== null)               //count($getstructure)>0)
              {
                $emailstructure=$getstructure->contact;

                if($emailstructure!="")
                  $this->sendmail($emailstructure,"Une réponse ($reponse->texteReponse) a été proposée à votre structure ($getstructure->libelle). Pour valider la réponse, rendez-vous sur la plateforme à l'adresse : https://mataccueil.gouv.bj/login","MTFP : Service usager");
              }
            }




            if($typeStructure=='Direction')
            {

                $getUsager=Usager::where("id","=",$req->idUsager)->get();

                if(count($getUsager)>0)
                {
                  $email=$getUsager[0]->email;

                  $reponseUsager="Service Relations Usagers du Ministère du Travail et de la Fonction Publique : \n\n";

                  $reponseUsager.="Objet de votre votre requête : $req->objet\n\n";

                  $reponseUsager.="Réponse du MTFP : $req->reponseStructure \n \n";

                  //if (!(empty($req->raisonRejet)))
                  //  $reponseUsager.="$req->raisonRejet \n\n";

                  $reponseUsager.="\n\n Etes-vous satisfait de la prestation ? Faites-le nous savoir en donnant une note sur votre espace de requête. Le code de votre requête est $req->codeRequete .\n\n";

                  if($email!="")
                   $this->sendmail($email,$reponseUsager);

                  $req->dateReponse=date("Y-m-d H:m:i");
                  $req->traiteOuiNon=1;

                  if($req->interrompu==1)
                    $req->finalise=0;
                  else
                    $req->finalise=1;


                  $req->horsDelai=1; // cas où la requête n'a pas un délai fixe.

                  //Vérifier si c'est hors délai
                  if($req->dureePrestation>0)
                  {
                    $dateReponse=$req->dateReponse;
                    $dateEnreg=$req->created_at;

                    $dif=$dateReponse->diff($dateEnreg)->format("%a");

                    if($dif<=$req->dureePrestation)
                      $req->horsDelai=2; // Cas où la requête a été traitée dans les délais.
                    else
                      $req->horsDelai=3; // Cas où la requête a été traitée hors délais.

                  }

                  $req->save();

                  //Enregistrement parcours
                  $parcours=new Parcoursrequete;
                  $parcours->typeStructure='USAGER';
                  $parcours->idRequete=$req->id;
                  $parcours->idStructure=0;
                  $parcours->idEtape=$idEtape;
                  $parcours->dateArrivee=date("Y-m-d h:m:i");

                  $parcours->save();
                }

            }
          } catch(\Illuminate\Database\QueryException $ex){

            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );

          }catch(\Exception $ex){

          \Log::error($ex->getMessage());
          $error =  array("status" => "error", "message" =>"Une erreur est survenue au cours de l'enregistrement. Contactez l'administrateur.");
            return $error;
          }

    }








public function transmettreReponserapide(\Illuminate\Http\Request $request)
{

  try {
            $inputArray = Request::get();
            //verifie les champs fournis
          if (!( isset($inputArray['codeRequete'])))
            { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }
           
            $emailusager= $inputArray['emailusager'];
            $nomprenomsusager= $inputArray['nomprenomsusager'];
            $emailstructure= $inputArray['emailstructure'];
            $message= $inputArray['message'];
            $codeRequete= $inputArray['codeRequete'];


            //$emailusager="gildas.zinkpe@gmail.com";

            //$emailstructure="gildas.zinkpe@gmail.com";


            $reponseUsager="Service Relations Usagers du Ministère du Travail et de la Fonction Publique : \n\n";

            $reponseUsager.="Message urgent du MTFP suite à votre requête : \n\n";

            $reponseUsager.="$message \n \n";


            $CopiereponseUsager="Plateforme de gestion des requêtes et plaintes des usagers du Ministère du Travail et de la Fonction Publique : \n\n";

            $CopiereponseUsager.="Copie de la réponse urgente envoyée à l'adresse $emailusager de l'usager du MTFP $nomprenomsusager \n\n";

            $CopiereponseUsager.="$message \n \n";
                  

            if($emailusager!="")
              $this->sendmail($emailusager,$reponseUsager);

            if($emailstructure!="")
              $this->sendmail($emailstructure,$CopiereponseUsager);


            $reponserapide=new Reponserapide;
            $reponserapide->emailstructure=$emailstructure;
            $reponserapide->emailusager=$emailusager;
            $reponserapide->message=$message;
            $reponserapide->codeRequete=$codeRequete;
            $reponserapide->save();

               
          } catch(\Illuminate\Database\QueryException $ex){

            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );

          }catch(\Exception $ex){

          \Log::error($ex->getMessage());
          $error =  array("status" => "error", "message" =>"Une erreur est survenue au cours de l'enregistrement. Contactez l'administrateur.");
            return $error;
          }

    }

  public function transmettreComment(\Illuminate\Http\Request $request)
{

  try {

            $inputArray = Request::get();
            //verifie les champs fournis
          if (!( isset($inputArray['commentaire']) && isset($inputArray['email']) && isset($inputArray['name'])))
            { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }
            $name= $inputArray['name'];
            $email= $inputArray['email'];
            $structure= $inputArray['structure'];
            $message= $inputArray['commentaire'];
           
            $parametre=Parametre::find(1);
            $emailRecepteur=$parametre->emailSuggestion;
            $reponseUsager="Service Relations Usagers du Ministère du Travail et de la Fonction Publique : \n\n";
            $reponseUsager.="$message \n \n";
            $reponseUsager.="De la part de $name : $email / $structure \n \n";
            

            if($emailRecepteur!="")
              $this->sendmail($emailRecepteur,$reponseUsager);

            $suggestion=new Suggestion;
            $suggestion->message=$message;
            $suggestion->nomEmetteur=$name;
            $suggestion->emailEmetteur=$email;
            $suggestion->structureEmetteur=$structure;
            $suggestion->emailRecepteur=$emailRecepteur;
            $suggestion->save();

               
          } catch(\Illuminate\Database\QueryException $ex){

            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
            return $error;
          }catch(\Exception $ex){

          \Log::error($ex->getMessage());
          $error =  array("status" => "error", "message" =>"Une erreur est survenue au cours de l'enregistrement. Contactez l'administrateur.");
            return $error;
          }

    }




  //transmettre une requete de lutilisateur a la direction
  public function transmettreRequete(Request $request)
  {

    try {
      $inputArray = Request::get();
      //verifie les champs fournis
      if (!( isset($inputArray['idRequete'])))
      { //controle d existence
        return array("status" => "error",
          "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
      }
      $idRequete=0;
      if(isset($inputArray['idRequete'])) $idRequete= $inputArray['idRequete'];

      //if(isset($inputArray['idEtape'])) $idEtape= $inputArray['idEtape'];

      $typeStructure='Division';

      //if(isset($inputArray['typeStructure'])) $typeStructure= $inputArray['typeStructure'];

      //if(isset($inputArray['typeSuperieur'])) $typeSuperieur= $inputArray['typeSuperieur'];

//      $userconnect = new AuthController;
//      $userconnectdata = $userconnect->user_data_by_token($request->token);
//
//      $getuser=Utilisateur::find($userconnectdata->id);
//
//      $idagent=$getuser->idagent;
//
//      $getAgent=Acteur::find($idagent);
//
//      $idStructure="";
//
//      if( $getAgent !== null )     ///count($getAgent)>0)
//        $idStructure=$getAgent->idStructure;


      $requeteObj = Requete::find($idRequete);
      if($requeteObj === null){
        return array("status" => "error", "message" => "");
      }

      //get fields for requete
      $idPrestation = $requeteObj->idPrestation;
      $plainte = $requeteObj->plainte;
      $code = $requeteObj->code;

      //rendre visible la requete a present
      $requeteObj->visible = 1;

      $requeteObj->save();

      //email usager
      $emailUsager = $requeteObj->usager->email;
      $nom = $requeteObj->usager->nom . " " . $requeteObj->usager->prenoms;


      // Enregistrement dans la table affectation
      $service=Service::find($idPrestation);

      $affect=new Affectation;
      $affect->typeStructure='Direction';
      $affect->idRequete=$requeteObj->id;

      $affect->idStructure=$service->idParent;
      $affect->dateAffectation=date("Y-m-d h:m:i");
      $affect->save();

      //Notification à la structure
      $getstructure=Structure::find($service->idParent);

      if($plainte==1)
        $typeRequete="plainte";
      else $typeRequete="requête";

      if($getstructure !== null)                       ///count($getstructure)>0)
      {
        $emailstructure=$getstructure->contact;

        if($emailstructure!="")
          RequeteController::sendmail($emailstructure,"Une requête (Objet : $requeteObj->objet) a été adressée à votre structure($getstructure->libelle) par $nom. Pour y répondre rendez-vous sur la plateforme à l'adresse : https://mataccueil.gouv.bj/login","MTFP : Service usager");
      }

      //Enregistrement parcours
      $check=Parcoursrequete::where("typeStructure","=",'Direction')->where("idRequete","=",$requeteObj->id)->get();

      if(count($check)==0)
      {
        $parcours=new Parcoursrequete;
        $parcours->typeStructure='Direction';
        $parcours->idRequete=$requeteObj->id;
        $parcours->idStructure=$service->idParent;
        $parcours->idEtape=1;
        $parcours->dateArrivee=date("Y-m-d h:m:i");
        $parcours->save();

      }
      

      //Envoie mail

      RequeteController::sendmail($emailUsager,"Votre requête a été enregistrée. Vous recevrez notre réponse au plus tôt. Le code de votre requête est: $code . Rendez-vous sur le lien https://demarchesmtfp.gouv.bj/ pour consulter le parcours du traitement de votre requête.","MTFP : Service usager");

        return array("status" => "success", "message" => "");

    } catch(\Illuminate\Database\QueryException $ex){

      \Log::error($ex->getMessage());

      $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );

    }catch(\Exception $ex){

      \Log::error($ex->getMessage());
      $error =  array("status" => "error", "message" =>"Une erreur est survenue au cours de l'enregistrement. Contactez l'administrateur.");
      return $error;
    }

  }//end transmettreRequete

  // questions / suggestions de l'usager 

   public function transmettreQuestion(\Illuminate\Http\Request $request)
{

  try {

            $inputArray = Request::get();
            
            //verifie les champs fournis
          if (!( isset($inputArray['objet']) && isset($inputArray['nom']) && isset($inputArray['prenoms']) && isset($inputArray['email']) && isset($inputArray['questions'])))
            { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }
            $objet= $inputArray['objet'];
            $nom= $inputArray['nom'];
            $prenoms= $inputArray['prenoms'];
            $email= $inputArray['email'];
            $questions= $inputArray['questions'];
           
            $parametre=Parametre::find(1);
            $emailRecepteur=$parametre->emailSuggestion;
            $mailtosend="Nom : "."".$nom.
        "\n\n"."Prénoms : "."".$prenoms."\n\n Email : "."".$email.
        "\n\n Objet : "."".$objet.
        "\n\n"."Questions / Suggestions : "."".$questions."\n\n";            

            if($emailRecepteur!="")
              $this->sendmail($emailRecepteur,$mailtosend);
            $result = "yes";
            return $result;
          } catch(\Illuminate\Database\QueryException $ex){

            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
            return $error;
          }catch(\Exception $ex){

          \Log::error($ex->getMessage());
          $error =  array("status" => "error", "message" =>"Une erreur est survenue au cours de l'enregistrement. Contactez l'administrateur.");
            return $error;
          }

    }



    public static function sendmail($email,$text="Enregistrement de votre requête",$sujet="PDA (MatAccueil) - Service Relations Usagers"){

      $senderEmail = 'travail.infos@gouv.bj';
      Mail::raw($text, function ($message) use ($email,$text,$sujet, $senderEmail) {
        $message->from($senderEmail, 'PDA');
        $message->to($email);
        $message->subject($sujet);
    });
    }


      //send file for upload
      public static function envoiFichier() {


        try{
          //enregistrer le fichier sur le serveur
          $extension  = ""; $fileName = "";
          if (Request::hasFile('file'))
          {
            $file = Request::file('file');
            $extension = $file->getClientOriginalExtension();
            $fileName = 'REQ_'. time().'_'.mt_rand(1000, 1000000).'.'.$extension;
            $pathName = ParamsFactory::getRequestsPath($extension) .'/'. $fileName;
            Storage::disk('local')->put($pathName,  File::get($file));
          }
          $input = Request::all();

          $requeteObj = Requete::find($input["id"]);
          $requeteObj->fichier_joint = $fileName;
          $requeteObj->save();        

          return array("status" => "success", "message" => "", "data" => $fileName);

        }catch(\Illuminate\Database\QueryException $ex){
          $error = array("status" => "error", "message" => "Une erreur est survenue lors du chargement du fichier de publication. Veuillez contactez l'administrateur" );
          \Log::error($ex->getMessage());
          return $error;
        }catch(\Exception $ex){
          $error = array("status" => "error", "message" => "Une erreur est survenue lors du chargement du fichier de publication. Veuillez contactez l'administrateur" );
          \Log::error($ex->getMessage());
          return $error;
        }

      }//end envoiFichier



 }



