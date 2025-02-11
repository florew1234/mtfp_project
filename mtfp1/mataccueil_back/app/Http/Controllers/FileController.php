<?php
 namespace App\Http\Controllers;
use App\Helpers\Factory\ParamsFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;


use App\Http\Controllers\Controller;


use App\Http\Controllers\AuthController;


use App\Models\Requete;

use App\Models\Usager;

use App\Models\Service;
use App\Models\Utilisateur;
use App\Models\Profil;
use App\Models\Acteur;
use App\Models\Etape;

use App\Models\Affectation;
use App\Models\Reponse;
use App\Models\Structure;
use App\Models\Parcoursrequete;


use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use Mail;

use DB;
class FileController extends Controller
{

    public function __construct() {

  $this->middleware('jwt.auth', ['except' => ['index','store', 'update',
    'transmettreRequete', 'test', 'getRequeteByCode','noterRequete','getRequeteByUsager', 'createRequestAsUsager', 'envoiFichier']]);

    }



  public function index(Request $request)
  {
    try {
      $input = $request->all();



      if($request->get('search')){
        $search=$request->get('search');

        $items = Requete::where("plainte","=",$request->get('plainte'))
          ->where("visible","=", 1) //newly added
          ->with(['usager','service','etape', 'nature','reponse' => function ($query) { return $query->orderBy('id','ASC'); },'affectation','parcours'])->orderBy('id','desc')
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
        $items = Requete::where("plainte","=",$request->get('plainte'))
          ->where("visible","=", 1)
          ->with(['usager','service','etape', 'nature','reponse' => function ($query) { return $query->orderBy('id','ASC'); },'affectation','parcours'])->orderBy('id','desc')
          ->paginate(10);
      }
      return response($items);


    } catch(\Illuminate\Database\QueryException $ex){
      \Log::error($ex->getMessage());

      $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
    }catch(\Exception $e){

      \Log::error($e->getMessage());
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

        $result = Requete::with(['usager','service','nature','etape','parcours'])->orderBy('id','desc')->where("CodeRequete","=",$codeRequete)->where("idUsager","=",$idUsager)->get();
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
    }catch(\Exception $e){

      \Log::error($e->getMessage());
      $error =  array("status" => "error", "message" => "Une erreur est survenue lors du" .
        " chargement des connexions. Veuillez contactez l'administrateur" );
      return $error;
    }
  }


  public function getRequeteByUsager($idusager,Request $request)

  {

    try {

      $input = $request->all();

      if($request->get('search'))
      {
        $search=$request->get('search');
        $result = Requete::with(['usager','service','nature','etape','parcours','affectation'])
          ->orderBy('id','desc')
          ->where("idUsager","=",$idusager)
          ->where("objet", "LIKE", "%{$search}%")
          ->orWhere("msgrequest", "LIKE", "%{$search}%")
          ->orWhere("codeRequete", "LIKE", "%{$search}%")
          ->paginate(10);
      }
      else{
        $result = Requete::with(['usager','service','nature','etape','parcours','affectation'])->orderBy('id','desc')
          ->where("idUsager","=",$idusager)
          ->paginate(10);
      }


      return $result;

    } catch(\Illuminate\Database\QueryException $ex){
      //\Log::error($ex->getMessage());

      $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
    }catch(\Exception $e){

      //\Log::error($e->getMessage());

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

      $inputArray = $request->all();

      if(isset($inputArray['noteUsager'])) $noteUsager= $inputArray['noteUsager'];
      if(isset($inputArray['idRequete'])) $idRequete= $inputArray['idRequete'];

      $commentaireNotation="";
      if(isset($inputArray['commentaireNotation'])) $commentaireNotation= $inputArray['commentaireNotation'];

      $req=Requete::find($idRequete);
      $req->noteUsager=$noteUsager;
      $req->commentaireNotation=$commentaireNotation;
      $req->save();

    } catch(\Illuminate\Database\QueryException $ex){
      \Log::error($ex->getMessage());

      $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );

    }catch(\Exception $e){

      \Log::error($e->getMessage());

      $error =  array("status" => "error", "message" => "Une erreur est survenue lors du" .
        " chargement des connexions. Veuillez contactez l'administrateur" );
      return $error;
    }
  }


  public function store(Request $request)
  {
    try {

      $inputArray =  $request->all();
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
      $requete->visible=$visible;


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
          RequeteController::sendmail($emailstructure,"Une requête (Objet : $requete->objet) a été adressée à votre structure ($getstructure->libelle) par $nom. Pour y répondre rendez-vous sur la plateforme à l'adresse : https://mattacueil.gouv.bj/login.","MTFP : Service usager");
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

      RequeteController::sendmail($email,"Votre requête a été enregistrée. Vous recevrez notre réponse au plus tôt. Le code de votre requête est: $code . Rendez-vous sur le lien https://demarchesmtfp.gouv.bj/accesusager.php pour consulter le parcours du traitement de votre requête.","MTFP : Service usager");


    } catch(\Illuminate\Database\QueryException $ex){
      \Log::error($ex->getMessage());

      $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" );
    }catch(\Exception $e){

      \Log::error($e->getMessage());

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

      $inputArray =  $request->all();
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

      $idUser='';
      if(isset($inputArray['idUser'])) $idUser= $inputArray['idUser'];

      $natureRequete='';
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

      $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" );
    }catch(\Exception $e){

      \Log::error($e->getMessage());
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

      $inputArray =  $request->all();
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

      //enregistrer le fichier sur le serveur
      $extension  = ""; $fileName = "";
      if (Request::hasFile('file'))
      {
        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $fileName = 'PUB_'. time().'_'.mt_rand(1000, 1000000).'.'.$extension;
        $pathName = ParamsFactory::getRequestsPath($extension) .'/'. $fileName;
        Storage::disk('local')->put($pathName,  File::get($file));
      }



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
      //$requete->fichier_joint= $fileName;


      $requete->created_by = $idUser;

      // Enregistrement de l'usager s'il ne l'est pas encore
      $checkusager=Usager::where("email","=",$email)->get();


      $requete->idUsager=$checkusager[0]->id;

      $requete->save();

      return array("status" => "success", "message" => "", "data" => $requete->id);


//      // Enregistrement dans la table affectation
//      $service=Service::find($idPrestation);
//
//      $affect=new Affectation;
//      $affect->typeStructure='Direction';
//      $affect->idRequete=$requete->id;
//
//      $affect->idStructure=$service->idParent;
//      $affect->dateAffectation=date("Y-m-d h:m:i");
//      $affect->save();
//
//      //Notification à la structure
//      $getstructure=Structure::find($service->idParent);
//
//      if($plainte==1)
//        $typeRequete="plainte";
//      else $typeRequete="requête";
//
//      if($getstructure !== null)                       ///count($getstructure)>0)
//      {
//        $emailstructure=$getstructure->contact;
//
//        if($emailstructure!="")
//          RequeteController::sendmail($emailstructure,"Une requête a été adressée à votre structure par $nom. Pour y répondre rendez-vous sur la plateforme à l'adresse : https://demarchesmtfp.gouv.bj/outilcollecte.","MTFP : Service usager");
//      }
//
//      //Enregistrement parcours
//      $parcours=new Parcoursrequete;
//      $parcours->typeStructure='Direction';
//      $parcours->idRequete=$requete->id;
//      $parcours->idStructure=$service->idParent;
//      $parcours->idEtape=1;
//      $parcours->dateArrivee=date("Y-m-d h:m:i");
//      $parcours->save();
//
//
//      //Envoie mail
//
//      RequeteController::sendmail($email,"Votre requête a été enregistrée. Vous recevrez notre réponse au plus tôt. Le code de votre requête est: $code . Rendez-vous sur le lien https://demarchesmtfp.gouv.bj/accesusager.php pour consulter le parcours du traitement de votre requête.","MTFP : Service usager");


    } catch(\Illuminate\Database\QueryException $ex){
      \Log::error($ex->getMessage());

      $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" );
    }catch(\Exception $e){

      \Log::error($e->getMessage());

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


      $input = $request->all();


      $idUser=$request->get('idUser');
      $typeStructure=$request->get('typeStructure');
      $plainte=$request->get('plainte');

      $getuser=Utilisateur::find($idUser);

      $resultat =array();



      $idagent=$getuser->idagent;

      $getAgent=Acteur::find($idagent);

      $idStructure="";

      if($getAgent !== null ) //count($getAgent)>0)
      {
        $idStructure=$getAgent->idStructure;


        if($request->get('search'))
        {
          $search=$request->get('search');

          $resultat = Requete::with(['usager','service','nature','etape','reponse' => function ($query) { return $query->orderBy('id','ASC'); },'affectation','parcours'])
            ->where("traiteOuiNon","=",0)
            ->where("visible","=", true) //newly added
            ->where("plainte","=",$plainte)
            ->whereHas('affectation', function($q) use($idStructure) {
              $q->where('idStructure',"=", $idStructure);
            })
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
            ->orderBy('id','desc')
            ->paginate(10);

        }else{
          $resultat = Requete::with(['usager','service','nature','etape','reponse' => function ($query) { return $query->orderBy('id','ASC'); },'affectation','parcours'])
            ->where("traiteOuiNon","=",0)
            ->where("plainte","=",$plainte)
            ->whereHas('affectation', function($q) use($idStructure) {
              $q->where('idStructure',"=", $idStructure);
            })
            ->orderBy('id','desc')
            ->paginate(10);
        }

      }


      return response($resultat);

    } catch(\Illuminate\Database\QueryException $ex){
      \Log::error($ex->getMessage());

      $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" );
    }catch(\Exception $e){

      \Log::error($e->getMessage());

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
    }catch(\Exception $e){

      \Log::error($e->getMessage());

      $error = array("status" => "error", "message" => $e );
      return $error;
    }

  }



  public function saveReponse(Request $request)
  {
    try {

      $inputArray =  $request->all();
      //verifie les champs fournis
      if (!( isset($inputArray['idRequete'])))
      { //controle d existence
        return array("status" => "error",
          "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
      }

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

    }catch(\Exception $e){

      \Log::error($e->getMessage());
      $error = array("status" => "error", "message" =>"Une erreur est survenue au cours de l'enregistrement. Contactez l'administrateur.");
      return $error;
    }
  }



  public function transmettreReponse(Request $request)
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
            $this->sendmail($emailstructure,"Une reponse a été proposée à votre structure ($getstructure->libelle). Pour valider la réponse, rendez-vous sur la plateforme à l'adresse : https://mataccueil.gouv.bj/login.","MTFP : Service usager");
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

          $reponseUsager.="Réponse du MTFP : $req->reponseStructure";

          $reponseUsager.="\n\n Vous pouvez donner votre avis sur la réponse qui vous a été donnée en cliquant sur le lien suivant: https://demarchesmtfp.gouv.bj/outilcollecte/noter . Le code de votre requête est $req->codeRequete .\n\n";

          //if($email!="")
          // $this->sendmail($email,$reponseUsager);

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

    }catch(\Exception $e){

      \Log::error($e->getMessage());
      $error =  array("status" => "error", "message" =>"Une erreur est survenue au cours de l'enregistrement. Contactez l'administrateur.");
      return $error;
    }

  }


  //transmettre une requete de lutilisateur a la direction
  public function transmettreRequete(Request $request)
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

      $fichierJoint = "";
      if(isset($inputArray['fichier_requete'])) $fichierJoint = $inputArray['fichier_requete'];

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
      $requeteObj->fichier_joint = $fichierJoint;
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
          RequeteController::sendmail($emailstructure,"Une requête (Objet : $requeteObj->objet) a été adressée à votre structure ($getstructure->libelle) par $nom. Pour y répondre rendez-vous sur la plateforme à l'adresse : https://mataccueil.gouv.bj/login.","MTFP : Service usager");
      }

      //Enregistrement parcours
      $parcours=new Parcoursrequete;
      $parcours->typeStructure='Direction';
      $parcours->idRequete=$requeteObj->id;
      $parcours->idStructure=$service->idParent;
      $parcours->idEtape=1;
      $parcours->dateArrivee=date("Y-m-d h:m:i");
      $parcours->save();


      //Envoie mail

      RequeteController::sendmail($emailUsager,"Votre requête a été enregistrée. Vous recevrez notre réponse au plus tôt. Le code de votre requête est: $code . Rendez-vous sur le lien https://demarchesmtfp.gouv.bj/accesusager.php pour consulter le parcours du traitement de votre requête.","MTFP : Service usager");

      return array("status" => "success", "message" => "");

    } catch(\Illuminate\Database\QueryException $ex){

      \Log::error($ex->getMessage());

      $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );

    }catch(\Exception $e){

      \Log::error($e->getMessage());
      $error =  array("status" => "error", "message" =>"Une erreur est survenue au cours de l'enregistrement. Contactez l'administrateur.");
      return $error;
    }

  }//end transmettreRequete


  public function getStatbyStructure($user,$plainte)
  {
    try {
      if($user=='all')
      {
        $stats = DB::select("select stru.libelle,
                          count(*) total,
                          sum(case when traiteOuiNon = 1 then 1 else 0 end) totalTraite,
                          sum(case when (ser.delaiFixe=1 and traiteOuiNon = 1 and DATEDIFF(dateReponse,req.created_at)>ser.nbreJours) then 1 else 0 end) totalTraiteHorsDelai,
                          sum(case when traiteOuiNon = 0 then 1 else 0 end) totalEnCours,
                          sum(case when (ser.delaiFixe=1 and traiteOuiNon = 0 and DATEDIFF(CURRENT_TIMESTAMP,req.created_at)>ser.nbreJours) then 1 else 0 end) totalEnCoursHorsDelai,

                          sum(case when (rejete=1 and traiteOuiNon = 1) then 1 else 0 end) totalRejet,

                          sum(case when (interrompu=1 and traiteOuiNon = 1) then 1 else 0 end) totalInterrompu,

                          sum(case when noteUsager is not NULL then 1 else 0 end) totalRetour,
                          sum(case when noteUsager>=5 then 1 else 0 end) totalRetourPositif
                          from outilcollecte_requete req,outilcollecte_structure stru,outilcollecte_service ser 
                          where req.idPrestation=ser.id 
                          and ser.idParent=stru.id 
                          and stru.active=1 
                          and plainte=$plainte
                          group by stru.id,stru.libelle order by total desc;");
      }
      else{
        $getUser=Utilisateur::find($user);
        $getProfil=Profil::find($getUser->idprofil);


        if( ($getProfil->parametre==1) || ($getProfil->saisie==1) ||  ($getProfil->sgm==1) || ($getProfil->dc==1) || ($getProfil->ministre==1) )
        {
          $stats = DB::select("select 'Ministère du Travail et de la Fonction Publique' as libelle,
                              count(*) total,
                              sum(case when traiteOuiNon = 1 then 1 else 0 end) totalTraite,
                              sum(case when (ser.delaiFixe=1 and traiteOuiNon = 1 and DATEDIFF(dateReponse,req.created_at)>ser.nbreJours) then 1 else 0 end) totalTraiteHorsDelai,

                              sum(case when (rejete=1 and traiteOuiNon = 1) then 1 else 0 end) totalRejet,

                              sum(case when (interrompu=1 and traiteOuiNon = 1) then 1 else 0 end) totalInterrompu,

                              sum(case when traiteOuiNon = 0 then 1 else 0 end) totalEnCours,
                              sum(case when (ser.delaiFixe=1 and traiteOuiNon = 0 and DATEDIFF(CURRENT_TIMESTAMP,req.created_at)>ser.nbreJours) then 1 else 0 end) totalEnCoursHorsDelai,

                              sum(case when noteUsager is not NULL then 1 else 0 end) totalRetour,
                              sum(case when noteUsager>=5 then 1 else 0 end) totalRetourPositif
                              from outilcollecte_requete req,outilcollecte_service ser
                              where req.idPrestation=ser.id and visible=1
                              and plainte=$plainte");
        }
        else
        {
          $idagent=$getUser->idagent;
          $getAgent=Acteur::find($idagent);
          $idStructure="";

          if(count($getAgent)>0)
            $idStructure=$getAgent->idStructure;

          $stats = DB::select("select stru.libelle,
                              count(*) total,
                              sum(case when traiteOuiNon = 1 then 1 else 0 end) totalTraite,
                              sum(case when (ser.delaiFixe=1 and traiteOuiNon = 1 and DATEDIFF(dateReponse,req.created_at)>ser.nbreJours) then 1 else 0 end) totalTraiteHorsDelai,
                              sum(case when traiteOuiNon = 0 then 1 else 0 end) totalEnCours,
                              sum(case when (ser.delaiFixe=1 and traiteOuiNon = 0 and DATEDIFF(CURRENT_TIMESTAMP,req.created_at)>ser.nbreJours) then 1 else 0 end) totalEnCoursHorsDelai,

                              sum(case when (rejete=1 and traiteOuiNon = 1) then 1 else 0 end) totalRejet,
                              sum(case when (interrompu=1 and traiteOuiNon = 1) then 1 else 0 end) totalInterrompu,

                              sum(case when noteUsager is not NULL then 1 else 0 end) totalRetour,
                              sum(case when noteUsager>=5 then 1 else 0 end) totalRetourPositif
                              from outilcollecte_requete req,outilcollecte_structure stru,outilcollecte_service ser,outilcollecte_affectation aff 
                              where req.id=aff.idRequete
                              and req.idPrestation=ser.id
                              and aff.idStructure=stru.id
                              and stru.active=1
                              and stru.id=$idStructure  and visible=1
                              and plainte=0
                              group by stru.id,stru.libelle order by total desc;");
        }
      }

      return($stats);

    } catch(\Illuminate\Database\QueryException $ex){
      \Log::error($ex->getMessage());

      $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
    }catch(\Exception $e){

      \Log::error($e->getMessage());
      $error =  array("status" => "error", "message" =>"Une erreur est survenue au cours du traitement de votre requête. Contactez l'administrateur s'il vous plaît.");
      return $error;
    }
  }


  public function getStatbyType($type,$plainte)
  {
    try {
      if($type=='all')
      {
        $stats = DB::select("select ty.libelle,
                          count(*) total,
                          sum(case when traiteOuiNon = 1 then 1 else 0 end) totalTraite,
                          sum(case when (ser.delaiFixe=1 and traiteOuiNon = 1 and DATEDIFF(dateReponse,req.created_at)>ser.nbreJours) then 1 else 0 end) totalTraiteHorsDelai,

                          sum(case when (rejete=1 and traiteOuiNon = 1) then 1 else 0 end) totalRejet,
                          sum(case when (interrompu=1 and traiteOuiNon = 1) then 1 else 0 end) totalInterrompu,

                          sum(case when traiteOuiNon = 0 then 1 else 0 end) totalEnCours,
                          sum(case when (ser.delaiFixe=1 and traiteOuiNon = 0 and DATEDIFF(CURRENT_TIMESTAMP,req.created_at)>ser.nbreJours) then 1 else 0 end) totalEnCoursHorsDelai,
                          sum(case when noteUsager is not NULL then 1 else 0 end) totalRetour,
                          sum(case when noteUsager>=5 then 1 else 0 end) totalRetourPositif
                          from outilcollecte_requete req,outilcollecte_typeservice ty,outilcollecte_service ser 
                          where req.idPrestation=ser.id 
                          and ser.idType=ty.id   and visible=1
                          and plainte=$plainte
                          group by ty.id,ty.libelle;");
      }
      return($stats);

    } catch(\Illuminate\Database\QueryException $ex){
      \Log::error($ex->getMessage());

      $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
    }catch(\Exception $e){

      \Log::error($e->getMessage());
      $error =
        array("status" => "error", "message" =>$e); array("status" => "error", "message" =>"Une erreur est survenue au cours du traitement de votre requête. Contactez l'administrateur s'il vous plaît.");
      return $error;
    }
  }


  public function getNbrebyType($type,$plainte)
  {
    try {
      if($type=='all')
      {
        $stats = DB::select("select typeser.libelle,count(*) total 
            from outilcollecte_requete req,outilcollecte_typeservice typeser,outilcollecte_service ser
            where req.idPrestation=ser.id 
            and ser.idType=typeser.id 
            and plainte=$plainte  and visible=1
            group by typeser.id,typeser.libelle");
        return($stats);
      }

    } catch(\Illuminate\Database\QueryException $ex){
      \Log::error($ex->getMessage());

      $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
    }catch(\Exception $e){

      \Log::error($e->getMessage());
      $error =
        array("status" => "error", "message" =>"Une erreur est survenue au cours du traitement de votre requête. Contactez l'administrateur s'il vous plaît.");
      return $error;
    }
  }

  public function getNbrebyStructure($structure,$plainte)
  {
    try {
      if($structure=='all')
      {
        $stats = DB::select("select struct.libelle,count(*) total 
            from outilcollecte_requete req,outilcollecte_structure struct,outilcollecte_service ser
            where req.idPrestation=ser.id 
            and ser.idParent=struct.id  and visible=1
            and plainte=$plainte
            and struct.active=1
            group by struct.id,struct.libelle ORDER BY COUNT(*) DESC");
        return($stats);
      }

    } catch(\Illuminate\Database\QueryException $ex){
      \Log::error($ex->getMessage());

      $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
    }catch(\Exception $e){

      \Log::error($e->getMessage());
      $error = array("status" => "error", "message" =>"Une erreur est survenue au cours du traitement de votre requête. Contactez l'administrateur s'il vous plaît.");
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


}



