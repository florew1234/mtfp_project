<?php
namespace App\Http\Controllers;

use App\Helpers\Factory\ParamsFactory;

use function GuzzleHttp\Promise\all;
use Illuminate\Http\Request;
use App\Http\Requests;
use DateTime;
 use Illuminate\Support\Facades\Input;
//use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;


use App\Http\Controllers\Controller;


use App\Http\Controllers\AuthController;
use App\Models\Relance;
use App\Models\AttribCom;

//use Request;

use App\Models\Requete;
use App\Models\Registre;
use App\Models\EchangeWhat;
use App\Models\Activity;
use App\User;

use App\Models\Usager;

use App\Models\Service;
use App\Models\Utilisateur;
use App\Models\Profil;
use App\Models\Acteur;
use App\Models\Etape;
use App\Models\Noteusager;
use App\Models\EntiteAdmin;

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
use PDF;
use Tymon\JWTAuth\JWTAuth;

 class RequeteController extends Controller
 {
     /*
     public function __construct() {
     $this->middleware('jwt.auth');
         }
     */

     protected $user;


     public function __construct()
     {
         //$this->user = JWTAuth::parseToken()->authenticate();

         $this->middleware('jwt.auth', ['except' => ['index','store', 'update','transmettreQuestion','je_denonce','findReponserapide','complementReponserapide',
          'transmettreRequete', 'test', 'getRequeteByCode','noterRequete','getRequeteByUsager','getRequeteByPfc','getRequeteByPfcRv', 'DownloaFile','createRequestAsUsager', 'envoiFichier','destroy','store2','downloadDataToPDF','getRequeteByUsagerNonTrai']]);
     }


     private function createUsager($request)
     {
         $inputArray=$request->only('email', 'lastname', 'firstname', 'contact');
         $checkusager=Usager::where("email", "=", $inputArray['email'])->get(); //->where("password","=",$password)

         if (count($checkusager)==0) {
             $nom='';
             if (isset($inputArray['lastname'])) {
                 $nom= $inputArray['lastname'];
             }
             $email='';
             if (isset($inputArray['email'])) {
                 $email= $inputArray['email'];
             }

             $prenoms='';
             if (isset($inputArray['firstname'])) {
                 $prenoms= $inputArray['firstname'];
             }

             $password= '123';

             $tel='';
             if (isset($inputArray['contact'])) {
                 $tel= $inputArray['contact'];
             }
            
             $idDepartement='4';
             // if(isset($inputArray['idDepartement'])) $idDepartement="0" ; //$inputArray['idDepartement']

             //Génération du code
             $getcode = DB::table('outilcollecte_usager')->select(DB::raw('max(code) as code'))->get();
             $code=1;
             if (!empty($getcode)) {
                 $code+=$getcode[0]->code;
             }

             if (($code>0) &&($code<10)) {
                 $codeComplet="U00000".$code;
             }

             if (($code>=10) &&($code<1000)) {
                 $codeComplet="U0000".$code;
             }

             if (($code>=1000) &&($code<10000)) {
                 $codeComplet="U000".$code;
             }

             if (($code>=10000) &&($code<100000)) {
                 $codeComplet="U00".$code;
             }

             if (($code>=100000) &&($code<1000000)) {
                 $codeComplet="U0".$code;
             }
             if (($code>=1000000) &&($code<10000000)) {
                 $codeComplet="U".$code;
             }
            
          
             $usager= new Usager();
             $usager->nom=$nom;
             $usager->prenoms=$prenoms;

             $usager->email=$email;
             $usager->code=$code;

             $usager->codeComplet=$codeComplet;

             $usager->password=$password;

             $usager->tel=$tel;
             $usager->idDepartement=$idDepartement;

             $usager->save();

             $getuser=Usager::where("email", "=", $email)->first();
             return $getuser;
         } else {
             $getuser=Usager::find($checkusager[0]->id);
             return $getuser;
         }
     }
     public function store2(\Illuminate\Http\Request $request)
     {
         $usager=$this->createUsager($request);

         //Génération du code
         $getcode = DB::table('outilcollecte_requete')->select(DB::raw('max(code) as code'))->get();
         $code=1;
         $codeRequete="REQ000000";
         if (!empty($getcode)) {
             $code+=$getcode[0]->code;
         }

         if (($code>0) &&($code<10)) {
             $codeRequete="REQ00000".$code;
         }

         if (($code>=10) &&($code<1000)) {
             $codeRequete="REQ0000".$code;
         }

         if (($code>=1000) &&($code<10000)) {
             $codeRequete="REQ000".$code;
         }

         if (($code>=10000) &&($code<100000)) {
             $codeRequete="REQ00".$code;
         }

         if (($code>=100000) &&($code<1000000)) {
             $codeRequete="REQ0".$code;
         }
         if (($code>=1000000) &&($code<10000000)) {
             $codeRequete="REQ".$code;
         }
         
         $check = Requete::where('matricule',$request->code)
                        ->where('idUsager',$usager->id)
                        ->where('idPrestation',$request->idPrestation)
                        ->where('created_at','>=',date('Y-m-d').' 00:00:00')
                        ->where('created_at','<=',date('Y-m-d').' 23:59:59')
                        ->where('plateforme',$request->plateforme)->get();

         if($check->count()==0){
            $requete=Requete::create([
                "idUsager"=>$usager->id,
                "idPrestation"=>$request->idPrestation,
                "identity"=>$request->lastname." ".$request->firstname,
                "matricule"=>$request->code,
                "entity_name"=>$request->entity_name,
                "contact"=>$request->contact,
                "locality"=>$request->locality,
                "out_year"=>(int)$request->out_year,
                "contact_proche"=>$request->contact_proche,
                "visible"=>1,
                // "plainte"=>2,
                "plainte"=>$request->plainte,
                "natureRequete"=>5,
                "link_to_prestation"=>1,
                
                "plateforme"=>$request->plateforme,
                "objet"=>$request->objet,
                "idEtape"=>1,
                "dureePrestation"=>2,
                "codeRequete"=>$codeRequete,
                "code"=>$code,
                "interfaceRequete"=>$request->interfaceRequete,
                "email"=>$request->email,
                "msgrequest"=>$request->msgrequest,
                "idEntite"=>$request->idEntite
            ]);
         
  
           // Enregistrement dans la table affectation
           $service=Service::find($requete->idPrestation);
  
           $affect=new Affectation;
           $affect->typeStructure='Direction';
           $affect->idRequete=$requete->id;
           $affect->idEntite=$request->idEntite;
           $affect->idStructure=$service->idParent;
           $affect->dateAffectation=date("Y-m-d h:m:i");
           $affect->save();
  
           //Notification à la structure
           $getstructure=Structure::find($service->idParent);
            
  
           if ($getstructure !== null) {                       ///count($getstructure)>0)
               $emailstructure=$getstructure->contact;
               if(trans('auth.mode') != 'test'){
                   if ($emailstructure!="") {
                       RequeteController::sendmail($emailstructure, "Une préoccupation a été adressée à votre structure ($getstructure->libelle) par $requete->identity. Pour y répondre rendez-vous sur la plateforme à l'adresse : https://mataccueil.gouv.bj/login.", "MTFP : Service usager");
                   }
               }
           }
  
           //Enregistrement parcours
           $parcours=new Parcoursrequete;
           $parcours->typeStructure='Direction';
           $parcours->idRequete=$requete->id;
           $parcours->idStructure=$service->idParent;
           $parcours->idEntite=$request->idEntite;
           $parcours->idEtape=1;
           $parcours->dateArrivee=date("Y-m-d h:m:i");
           $parcours->save();
  
  
           //Envoie mail
           if(trans('auth.mode') != 'test'){
               if ($usager->email!="") {
                   $msg="Cher usager,\n  Votre préoccupation a été bien enregistrée. Nous vous en remercions. Le code de votre préoccupation est $codeRequete . Nous veuillerons à ce que votre préoccupation soit traitée dans les plus bref délais.  \n Le Ministère à votre service.";
                   RequeteController::sendmail($usager->email, $msg, "MTFP : Service usager");
               }
           }
           
         
         }
         return ['success'=>true];
         /*$previousUrl = app('url')->previous();

         if ($request->plateforme=="PDA") {
            // return redirect()->to($previousUrl.'?'. http_build_query(['success'=>true]));
         } else {
            
         }*/
        

         //  return back()->with('message',"Votre demande a été pris en compte! Nous vous recontacterons!");
     }


     public function je_denonce(\Illuminate\Http\Request $request)
     {

        $fichierJoint = "";
        $file=null;
        if ($request->file('fichier_joint') && $request->file('fichier_joint')->isValid()) {
          $file = $request->file('fichier_joint');
          $extension = $file->getClientOriginalExtension();
          $fileName = Str::random(10) . '_' . date('His') . rand(1, 99999) . '.' . $extension;
          //$fichierJoint = $file->storeAs('uploads/pj/'.date('Y'), $fileName, 'custom');
        }
        

        $senderEmail = 'mtfp.usager@gouv.bj'; ;
        $text=$request->resume;
        Mail::raw($text, function ($message) use ($text, $senderEmail,$file) {
            $message->from($email, 'PDA');
            //$message->to('igsep@gouv.bj');
            $message->to($senderEmail);
            if($file!=null){
                $message->attach($file->getRealPath(),
                [
                    'as' => $file->getClientOriginalName(),
                    'mime' => $file->getClientMimeType(),
                ]);
            }
            $message->subject("MTFP : Service usager (Dénonciation)");
        });

        //Envoie mail
        if ($request->email !="") {

            $msg="Cher usager, Votre dénonciation a été bien enregistrée. Nous vous en remercions. Cette dénonciation sera traitée dans les plus bref délais.  \n Le Ministère à votre service.";
            RequeteController::sendmail($request->email, $msg, "MTFP : Service usager");
        }
         
         return ['success'=>true];

     }
     

     /**
        * Display a listing of the resource.

        *

        * @return Response

        */


     public function index(Request $request, $idEntite)
     {
         try {
             $input = $request->query();
             $plainte = $input['plainte'];

             if (isset($input['search'])) {
                 $search = $input['search'];

                 $items = Requete::where("plainte", "=", $plainte)
              ->where("visible", "=", 1)
              ->where("idEntite", "=", $idEntite) //newly added
              
              
              ->with(['usager','service','service','reponses_rapide','entite_receive','entite','creator','creator.agent_user','service.type','service.service_parent', 'nature','notes', 'reponse' => function ($query) {
                  return $query->orderBy('id', 'DESC');
              },'affectation','parcours'])->orderBy('id', 'DESC')
              ->where("objet", "LIKE", "%{$search}%")
              ->orWhereHas('usager', function ($q) use ($search) {
                  $q->where('email', "LIKE", "%{$search}%");
              })
              ->orWhereHas('usager', function ($q) use ($search) {
                  $q->where('nom', "LIKE", "%{$search}%");
              })
              ->orWhereHas('usager', function ($q) use ($search) {
                  $q->where('prenoms', "LIKE", "%{$search}%");
              })
              ->orWhereHas('usager', function ($q) use ($search) {
                  $q->where(DB::raw("CONCAT(`nom`, ' ', `prenoms`)"), "LIKE", "%{$search}%");
              })
              ->orWhereHas('service', function ($q) use ($search) {
                  $q->where('libelle', "LIKE", "%{$search}%");
              })
              ->orWhereHas('nature', function ($q) use ($search) {
                  $q->where('libelle', "LIKE", "%{$search}%");
              })
              ->orWhere("codeRequete", "LIKE", "%{$search}%")
              ->paginate(25);
             } else {
                 $items = Requete::where("plainte", "=", $plainte)
            ->where("visible", "=", 1)
            ->where("idEntite", "=", $idEntite)
            ->with(['usager','service','entite_receive','entite','reponses_rapide','creator','creator.agent_user','service.type','service.service_parent','etape', 'nature', 'notes','reponse' => function ($query) {
                return $query->orderBy('id', 'DESC');
            },'affectation','parcours'])->orderBy('id', 'desc')
              ->paginate(25);
             }

             return response($items);
         } catch (\Illuminate\Database\QueryException $ex) {
             \Log::error($ex->getMessage());

             $error = array("error"=>$ex->getMessage(),"status" => "errors", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" );
             return $error;
         } catch (\Exception $ex) {
             \Log::error($ex->getMessage());
             $error = array("error"=>$ex->getMessage(),"status" => "errors", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" );
             return $error;
         }
     }
     /**
          * Store a newly created resource in storage.

          *

          * @return Response

          */


     public function getRequeteByCode($email, $codeRequete)
     {
         try {
             $checkusager=Usager::where("email", "=", $email)->get();

             $result=array();

             if (count($checkusager)>0) {
                 $idUsager=$checkusager[0]->id;

                 $result = Requete::with(['usager','service','nature','notes', 'etape','parcours'])->orderBy('id', 'desc')->where("CodeRequete", "=", $codeRequete)->where("idUsager", "=", $idUsager)->get();
             } else {
                 $error =
            array("status" => "error", "message" => "Aucune requête trouvée." );
                 return $error;
             }


             return $result;
         } catch (\Illuminate\Database\QueryException $ex) {
             \Log::error($ex->getMessage());

             $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
         } catch (\Exception $ex) {
             \Log::error($ex->getMessage());
             $error =  array("status" => "error", "message" => "Une erreur est survenue lors du" .
                             " chargement des connexions. Veuillez contactez l'administrateur" );
             return $error;
         }
     }


     public function getRequeteByUsager($idusager, Request $request)
     {
         try {

            //$input = Request::all();
             $input = $request->query();

             //return $input;
             $result=array();
             if (isset($input['search'])) {     //$request->get('search'))
                 $search=$input['search'];
                 $result = Requete::with(['usager','service.type', 'notes','nature','etape','parcours','affectation','entite'])
              ->orderBy('id', 'desc')
              ->where("idUsager", "=", $idusager)
              ->where("objet", "LIKE", "%{$search}%")
              ->orWhere("msgrequest", "LIKE", "%{$search}%")
              ->orWhere("codeRequete", "LIKE", "%{$search}%")
              ->get();
             // ->paginate(10);
             } else {
                 $result = Requete::with(['usager','service.type','notes','nature','etape','parcours','affectation','entite'])->orderBy('id', 'desc')
              ->where("idUsager", "=", $idusager)
              ->get();
                 // ->paginate(10);
             }
             
             return $result;
         } catch (\Illuminate\Database\QueryException $ex) {
             \Log::error($ex->getMessage());

             $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
         } catch (\Exception $ex) {
             \Log::error($ex->getMessage());

             $error = array("status" => "error", "message" => "Une erreur est survenue lors du" .
                             " chargement des connexions. Veuillez contactez l'administrateur" );
             return $error;
         }
     }

     public function getRequeteByPfc($idusager, Request $request)
     {
         try {

            //$input = Request::all();
             $input = $request->query();

             //return $input;
             $result=array();
             if (isset($input['search'])) {     //$request->get('search'))
                 $search=$input['search'];
                 $result = Requete::with(['usager','service.type', 'notes','nature','etape','parcours','affectation','entite'])
              ->orderBy('id', 'desc')
              ->where("created_by", "=", $idusager)
              ->where("objet", "LIKE", "%{$search}%")
              ->orWhere("msgrequest", "LIKE", "%{$search}%")
              ->orWhere("codeRequete", "LIKE", "%{$search}%")
              ->get();
             // ->paginate(10);
             } else {
                 $result = Requete::with(['usager','service.type','notes','nature','etape','parcours','affectation','entite'])->orderBy('id', 'desc')
              ->where("created_by", "=", $idusager)
              ->get();
                 // ->paginate(10);
             }
             
             return $result;
         } catch (\Illuminate\Database\QueryException $ex) {
             \Log::error($ex->getMessage());

             $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
         } catch (\Exception $ex) {
             \Log::error($ex->getMessage());

             $error = array("status" => "error", "message" => "Une erreur est survenue lors du" .
                             " chargement des connexions. Veuillez contactez l'administrateur" );
             return $error;
         }
     }


     public function getRequeteByUsagerNonTrai($idusager)
     {
         
         try {

            $result = Requete::with(['usager','service', 'notes','nature','etape','parcours','affectation','entite'])
                                ->orderBy('id', 'desc')->where("idUsager", "=", $idusager)->where("traiteOuiNon",0)->get();
             return $result;
         } catch (\Illuminate\Database\QueryException $ex) {
             \Log::error($ex->getMessage());

             $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
         } catch (\Exception $ex) {
             \Log::error($ex->getMessage());

             $error = array("status" => "error", "message" => "Une erreur est survenue lors du" .
                             " chargement des connexions. Veuillez contactez l'administrateur" );
             return $error;
         }
     }


     //notation dune requete
     public function noterRequete(Request $request)
     {
         try {
             $idRequete="";
             $noteUsager="";

             $inputArray = $request->all();

             if (isset($inputArray['noteDelai'])) {
                 $noteDelai= $inputArray['noteDelai'];
             }
             if (isset($inputArray['noteResultat'])) {
                 $noteResultat= $inputArray['noteResultat'];
             }
             /*if(isset($inputArray['noteDisponibilite'])) $noteDisponibilite= $inputArray['noteDisponibilite'];
             if(isset($inputArray['noteOrganisation'])) $noteOrganisation= $inputArray['noteOrganisation'];
             */
  
             if (isset($inputArray['codeRequete'])) {
                 $codeRequete= $inputArray['codeRequete'];
             }

             $commentaireNotation="";
             if (isset($inputArray['commentaireNotation'])) {
                 $commentaireNotation= $inputArray['commentaireNotation'];
             }

             //Enregistrement note
             $check=Noteusager::where("codeReq", "=", $codeRequete)->get();

             if (count($check)!=0) {
                 return array("status" => "success", "message" => "Vous avez déjà donné une fois votre appréciation de la prestation.");
             }

             $note=new Noteusager;
             $note->codeReq=$codeRequete;
             $note->noteDelai=$noteDelai;
             $note->noteResultat=$noteResultat;
             $note->idEntite=$inputArray['idEntite'];
             /*$note->noteDisponibilite=$noteDisponibilite;
             $note->noteOrganisation=$noteOrganisation;
             */

             $note->commentaireNotation=$commentaireNotation;
             $note->save();

             //$noteMoy = ($noteDisponibilite+$noteResultat+$noteResultat+$noteOrganisation)/3;
             $noteMoy = ($noteResultat+$noteDelai)/2;
             $req=Requete::where('codeRequete', '=', $codeRequete)->update(['noteUsager' => $noteMoy]);
         
             return array("status" => "success", "message" => "Appréciation enregistrée avec succès");
         } catch (\Illuminate\Database\QueryException $ex) {
             \Log::error($ex->getMessage());

             $error=array("status" => "error", "message" => "Une erreur est survenue lors de la notation de cette requête. Veuillez contactez l'administrateur" );
             return $error;
         } catch (\Exception $ex) {
             \Log::error($ex->getMessage());

             $error =  array("status" => "error", "message" => "Une erreur inattendue est survenue lors du" .
                             " de la notation de cette requête. Veuillez contactez l'administrateur" );
             return $error;
         }
     }


     public function store(Request $request)
     {
         
         try {
             $inputArray = $request->all();
             //verifie les champs fournis visible
          if (!(isset($inputArray['objet']))) { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
          }

             $idPrestation=''; // Prestation mailPfc
             if (isset($inputArray['idPrestation'])) {
                 $idPrestation= $inputArray['idPrestation'];
             }
             $objet='';
             if (isset($inputArray['objet'])) {
                 $objet= $inputArray['objet'];
             }
             $msgrequest='';
             if (isset($inputArray['msgrequest'])) {
                 $msgrequest= $inputArray['msgrequest'];
             }
             $idEtape=1;
             if (isset($inputArray['idEtape'])) {
                 $idEtape= $inputArray['idEtape'];
             }

             $plainte=2;
             if (isset($inputArray['plainte'])) {
                 $plainte= $inputArray['plainte'];
             }

             $interfaceRequete='SRU';
             if (isset($inputArray['interfaceRequete'])) {
                 $interfaceRequete= $inputArray['interfaceRequete'];
             }
            
             $plateforme='mataccueil';
             if (isset($inputArray['plateforme'])) {
                 $plateforme= $inputArray['plateforme'];
             }

             $nom='';
             if (isset($inputArray['nom'])) {
                 $nom= $inputArray['nom'];
             }
             $email='';
             if (isset($inputArray['email'])) {
                 $email= $inputArray['email'];
             }
             $tel='';
             if (isset($inputArray['tel'])) {
                 $tel= $inputArray['tel'];
             }
             $contactUs=''; //Contact pour usage cas du PFC 
             if (isset($inputArray['contactUs'])) {
                 $contactUs= $inputArray['contactUs'];
             }
             $mailPfc=''; //E-mail pour usage cas du PFC email
             if (isset($inputArray['mailPfc'])) {
                 $mailPfc= $inputArray['mailPfc'];
             }
             $matricule=''; 
             if (isset($inputArray['matricule'])) {
                 $matricule= $inputArray['matricule'];
             }
             $idDepartement='';
             if (isset($inputArray['idDepartement'])) {
                 $idDepartement= $inputArray['idDepartement'];
             }

             $link_to_prestation=0;
             if (isset($inputArray['link_to_prestation'])) {
                 $link_to_prestation= $inputArray['link_to_prestation'];
             }
          

             $natureRequete=5; // En ligne par défaut idPrestation 12 : Whatsapp
             if (isset($inputArray['natureRequete'])) {
                 $natureRequete= $inputArray['natureRequete'];
             }

             $nbreJours='';
             if (isset($inputArray['nbreJours'])) {
                 $nbreJours= $inputArray['nbreJours'];
             }

             $idUser=0;
             if (isset($inputArray['idUser'])) {
                 $idUser= $inputArray['idUser'];
             }

             $visible=0;
             if (isset($inputArray['visible'])) {
                 $visible= $inputArray['visible'];
             }

             $fichierJoint = "";
             if (isset($inputArray['fichier_requete'])) {
                 $fichierJoint = $inputArray['fichier_requete'];
             }
             $date = date('Y-m-d');
             // Enregistrement de l'usager s'il ne l'est pas encore
              $checkusager=Usager::where("email", "=", $email)->get();
             //Vérifier si deux requetes ayant même (préoccupation + user + day) sont dejà ajouter 
            if (count($checkusager)!=0) {
                $idUs =  $checkusager[0]->id;
                //  AND idPrestation = '$idPrestation' Pour la même préoccupation vous n'aviez droit qu'à deux requêtes par jour
                $statsDIR = DB::select("SELECT count(*) as nbre FROM `outilcollecte_requete` 
                                        WHERE idUsager = '$idUs'  
                                        AND traiteOuiNon = 0 AND created_at LIKE '%$date%';");
                if($statsDIR[0]->nbre >= 3 ){
                    return array("status" => "error", "message" => "Vous n'aviez droit qu'à trois préoccupations par jour.");
                }
            }

             //Générer le CODE
             //Génération du code
             $getcode = DB::table('outilcollecte_requete')->select(DB::raw('max(code) as code'))->get();
             $code=1;
             $codeRequete="REQ000000";
             if (!empty($getcode)) {
                 $code+=$getcode[0]->code;
             }

             if (($code>0) &&($code<10)) {
                 $codeRequete="REQ00000".$code;
             }

             if (($code>=10) &&($code<1000)) {
                 $codeRequete="REQ0000".$code;
             }

             if (($code>=1000) &&($code<10000)) {
                 $codeRequete="REQ000".$code;
             }

             if (($code>=10000) &&($code<100000)) {
                 $codeRequete="REQ00".$code;
             }

             if (($code>=100000) &&($code<1000000)) {
                 $codeRequete="REQ0".$code;
             }
             if (($code>=1000000) &&($code<10000000)) {
                 $codeRequete="REQ".$code;
             }

             //Valeur par defau pour les demandes qui n'ont pas de prestation
            //  if($idPrestation == "" || $idPrestation == null){
            //     $idPrestation = '435'; //valeur par defaut prestation néant 
            //  }
             //$userconnect = new AuthController; identite
             //$userconnectdata = $userconnect->user_data_by_token($request->token);
                // return response(["----------------------presta",$idPrestation]); idUsager
             $requete= new Requete;

             $requete->idPrestation=$idPrestation;
             $requete->dureePrestation=$nbreJours;
             $requete->objet=$objet;
             $requete->link_to_prestation=$link_to_prestation;
             $requete->msgrequest=$msgrequest;
             $requete->idEtape=$idEtape;
             $requete->codeRequete=$codeRequete;
             $requete->code=$code;
             $requete->natureRequete=$natureRequete;
             $requete->interfaceRequete=$interfaceRequete;
             $requete->plainte=$plainte;
             $requete->visible=$visible;
             $requete->plateforme=$plateforme;
             $requete->idEntite=$request->idEntite;
             $requete->fichier_joint = $fichierJoint;
             $requete->created_by = $idUser;
             $requete->contact = $contactUs;
             $requete->email = $email;
             $requete->matricule = $matricule;
             
             
            if (count($checkusager)!=null) {
                $requete->idUsager=$checkusager[0]->id;
            }
            //  return response()->json($checkusager);
             $requete->save();
             
             // Enregistrement dans la table affectation
             $service = Service::find($idPrestation);

             if ($visible==1 || $visible=="1") {
                 $affect=new Affectation;
                 $affect->typeStructure='Direction';
                 $affect->idRequete=$requete->id;
                 $affect->idEntite=$request->idEntite;
                 $affect->idStructure=$service?->idParent;
                 $affect->dateAffectation=date("Y-m-d h:m:i");
                 $affect->save();

                 //Notification à la structure
                 $getstructure=Structure::find($service->idParent);

                 if ($plainte==1) {
                     $typeRequete="plainte";
                 } else {
                     $typeRequete="demande d'information";
                 }

                 
                 //Enregistrement parcours
                 $parcours=new Parcoursrequete;
                 $parcours->typeStructure='Direction';
                 $parcours->idRequete=$requete->id;
                 $parcours->idStructure=$service->idParent;
                 $parcours->idEntite=$request->idEntite;
                 $parcours->idEtape=1;
                 $parcours->dateArrivee=date("Y-m-d h:m:i");
                 $parcours->save();
  
        
                 if(trans('auth.mode') != 'test'){
                    if ($getstructure !== null) {                       ///count($getstructure)>0)
                        $emailstructure=$getstructure->contact;
                        if ($emailstructure!="") {
                            RequeteController::sendmail($emailstructure, "Une préoccupation a été adressée à votre structure($getstructure->libelle) par $nom. Pour y répondre rendez-vous sur la plateforme à l'adresse : https://mataccueil.gouv.bj/login.", "MTFP : Service usager");
                        }
                    }
                }
  
            //                  //Envoie mail
                if(trans('auth.mode') != 'test'){

                    if ($email !="") {
                        $msg="Cher usager, \n Votre préoccupation a été bien enregistrée. Nous vous en remercions.\n \n
                            Cordialement.";
                          RequeteController::sendmail($email, $msg, "MTFP : Service usager");
                    }
                }
             }
            //PDA - Controle pour les registres transferer vers mataccueil
            if($interfaceRequete == 'registre'){
                $re = Registre::where('id',$inputArray['idregistre'])->first();
                $re->idreq = $requete->id;
                $re->save();                
            }
            //Echange Whatsapp PDA - Controle pour les discussions WHatsApp transferer vers mataccueil
            if(isset($inputArray['idEchanWhat'])){
                $ech = EchangeWhat::where('id',$inputArray['idEchanWhat'])->first();
                $ech->id_req = $requete->id;
                $ech->traite_disc = 'oui';
                $ech->save();                
            }
             //
             return response([
                "status" => "success", 
                "message" => ""
            ]);
         } catch (\Illuminate\Database\QueryException $ex) {
             \Log::error($ex->getMessage());

             $error=array("error" =>$ex->getMessage(),"status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" );
         } catch (\Exception $ex) {
             \Log::error($ex->getMessage());
            // return response()->json($ex->getMessage());
             $error = array("error" =>$ex->getMessage(),"status" => "error", "message" => "Une erreur est survenue lors de l'enregistrement de votre requête. Veuillez contactez l'administrateur");
             return $error;
         }
     }

     /**
          * update a newly created resource in storage.

          *

          * @return Response

          */

     public function transfertRequeteStructure(Request $request, $id)
     {
         $inputArray = $request->all();
        
         $requete=Requete::find($id);
  
         $oldPrestation=$requete->idPrestation;
         //mise à jour de la requete
         $requete->idPrestation=$inputArray['idPrestation'];
         $requete->save();
  
         $service=Service::find($inputArray['idPrestation']);
         
         // Enregistrement dans la table affectation
         $affect=new Affectation;
         $affect->typeStructure='Direction';
         $affect->idRequete=$requete->id;
         $affect->idEntite=$inputArray['idEntite'];
         $affect->idStructure=$service->idParent;
         $affect->dateAffectation=date("Y-m-d h:m:i");
         $affect->save();
         //Notification à la structure
         $getstructure=Structure::find($service->idParent);
         
         $oldService=Service::find($oldPrestation);
         $getoldstructure=Structure::with('structure_parent')->find($oldService->idParent);
         
         
    
         //Enregistrement parcours
         $parcours=new Parcoursrequete;
         $parcours->typeStructure='Direction';
         $parcours->idRequete=$requete->id;
         $parcours->idStructure=$service->idParent;
         $parcours->idEntite=$request->idEntite;
         $parcours->idEtape=7;
         $parcours->dateArrivee=date("Y-m-d h:m:i");
         $parcours->save();

         if ($getstructure !== null) {                       ///count($getstructure)>0)
             $emailstructure=$getstructure->contact;
             $sender=$getoldstructure->libelle;
             if($getoldstructure->structure_parent!=null){
                $sender.="(".$getoldstructure->structure_parent->libelle.")";
             }
             
             if(trans('auth.mode') != 'test'){
                if ($emailstructure!="") {
                    RequeteController::sendmail($emailstructure, "Une requête (Objet : $requete->objet) a été transféré à votre structure ($getstructure->libelle) par $sender. Pour y répondre rendez-vous sur la plateforme à l'adresse : https://mataccueil.gouv.bj/login.", "MTFP : Service usager");
                }
             }
         }
  
         //ACTIVITY
         Activity::SaveActivity($inputArray['id_user'],"Transfert interne d'une préoccupation");
         return array("status" => "success", "message" => "");
     }
     public function transfertRequeteEntite(Request $request, $id)
     {
         $inputArray = $request->all();
          
         $requete=Requete::find($id);

         $oldPrestation=$requete->idPrestation;
         //mise à jour de la requete
         $requete->idPrestation=$inputArray['idPrestation'];
         $requete->idEntiteReceive=$inputArray['idEntiteReceive'];
         $requete->save();

         $service=Service::find($inputArray['idPrestation']);

         // Enregistrement dans la table affectation
         $affect=new Affectation;
         $affect->typeStructure='Direction';
         $affect->idRequete=$requete->id;
         $affect->idEntite=$inputArray['idEntiteReceive'];
         $affect->idStructure=$service->idParent;
         $affect->dateAffectation=date("Y-m-d h:m:i");
         $affect->save();

         //Notification à la structure id_user
         $getstructure=Structure::find($service->idParent);
   
         $oldService=Service::find($oldPrestation);
         $getoldstructure=Structure::with('structure_parent')->find($oldService->idParent);
         
        //  if ($plainte==1) {
        //      $typeRequete="plainte";
        //     } else {
        //         $typeRequete="requête";
        //     }
            

                //Enregistrement parcours
                $parcours=new Parcoursrequete;
                $parcours->typeStructure='Direction';
                $parcours->idRequete=$requete->id;
                $parcours->idStructure=$service->idParent;
                $parcours->idEntite=$request->idEntiteReceive;
                $parcours->idEtape=8;
                $parcours->dateArrivee=date("Y-m-d h:m:i");
                // return response($oldService);
                $parcours->save();
                
                if ($getstructure !== null && $getoldstructure !== null) {                       ///count($getstructure)>0)
                    $emailstructure=$getstructure->contact;
                    $sender=$getoldstructure->libelle;
                    if($getoldstructure->structure_parent!=null){
                        $sender.="(".$getoldstructure->structure_parent->libelle.")";
                    }
                    if(trans('auth.mode') != 'test'){
                        if ($emailstructure!="") {
                            RequeteController::sendmail($emailstructure, "Une requête (Objet : $requete->objet) a été transféré à votre structure ($getstructure->libelle) par $sender. Pour y répondre rendez-vous sur la plateforme à l'adresse : https://mataccueil.gouv.bj/login.", "MTFP : Service usager");
                        }
                    }
                }
                    
                //ACTIVITY
                Activity::SaveActivity($inputArray['id_user'],"Transfert externe d'une préoccupation");

         return array("status" => "success", "message" => "");
     }


     public function relancerRequete(Request $request, $id){
         $requete=Requete::find($id);
         $service=Service::find($requete->idPrestation);
         $getstructure=Structure::find($service->idParent);

         if ($getstructure !== null) {                       ///count($getstructure)>0)
             $emailstructure=$getstructure->contact;
             $objet=$requete->objet;
             if ($emailstructure!="") {
                $message="Vous êtes prier de traiter la requête ayant pour objet : '".$objet."' dans les plus bref délais. Pour y répondre rendez-vous sur la plateforme à l'adresse : https://mataccueil.gouv.bj/login.";
                if(trans('auth.mode') != 'test'){
                    RequeteController::sendmail($emailstructure, $message, "MTFP : Service usager (Relance)");
                }
             }
             Relance::create([
                "message"=>$message,
                "idStructure"=>$getstructure->id,
                "date_envoi"=>date("Y-m-d H:m:i"),
                "idEntite"=>$getstructure->idEntite,
                "idStructureOrdonatrice"=>'',
                "idRequete"=>$id,
                "etat"=>'e'
            ]);
         }
         return array("status" => "success", "message" => "");
     }

     public function relancerRequeteType(Request $request, $id, $idStru,$idStruRela){

        $requete=Requete::find($id);
        $getstructure=Structure::find($idStru);
        
        if ($getstructure !== null) {                       ///count($getstructure)>0)
            $emailstructure=$getstructure->contact;
            //Verifier si cest un bon mail 
        if(filter_var($emailstructure, FILTER_VALIDATE_EMAIL)){
            $objet=$requete->objet;
            if ($emailstructure!="") {
                $message="Vous êtes prier de traiter la requête ayant pour objet : '".$objet."' dans les plus bref délais. Pour y répondre rendez-vous sur la plateforme à l'adresse : https://mataccueil.gouv.bj/login.";
                if(trans('auth.mode') != 'test'){
                    RequeteController::sendmail($emailstructure, $message, "MTFP : Service usager (Relance)");
                }
            }
            Relance::create([
                "message"=>$message,
                "idStructure"=>$getstructure->id,
                "date_envoi"=>date("Y-m-d H:m:i"),
                "idEntite"=>$getstructure->idEntite,
                "idStructureOrdonatrice"=>$idStruRela,
                "idRequete"=>$id,
                "etat"=>'e'
            ]);
            // //ACTIVITY
            // Activity::SaveActivity($userconnectdata->id,"Affectaction d'une reqête");

        }else{
            return array("status" => "error", "message" => "Le mail ($emailstructure) de cette structure n'est pas correcte.");
        }
        return array("status" => "success", "message" => $emailstructure);
        }
     }

     public function update($id, Request $request) {
         try {
             $inputArray = $request->all();
          
             $requete=Requete::find($id);
          
             //verifie les champs fournis email
             if (!(
                 isset($inputArray['objet']) && isset($inputArray['id'])
             )) { //controle d existence
                 return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
             }

             $idPrestation='';
             if (isset($inputArray['idPrestation'])) {
                 $idPrestation= $inputArray['idPrestation'];
             }
             $objet='';
             if (isset($inputArray['objet'])) {
                 $objet= $inputArray['objet'];
             }
             $msgrequest='';
             if (isset($inputArray['msgrequest'])) {
                 $msgrequest= $inputArray['msgrequest'];
             }
    
             $link_to_prestation=0;
             if (isset($inputArray['link_to_prestation'])) {
                 $link_to_prestation= $inputArray['link_to_prestation'];
             }
             $contactUs=''; //Contact pour usage cas du PFC 
             if (isset($inputArray['contactUs'])) {
                 $contactUs= $inputArray['contactUs'];
             }
             $mailPfc=''; //E-mail pour usage cas du PFC email
             if (isset($inputArray['mailPfc'])) {
                 $mailPfc= $inputArray['mailPfc'];
             }

             $plainte=2;
             if (isset($inputArray['plainte'])) {
                 $plainte= $inputArray['plainte'];
             }

             $idUser=1;
             if (isset($inputArray['idUser'])) {
                 $idUser= $inputArray['idUser'];
             }

             $natureRequete=5; // En ligne par défaut
             if (isset($inputArray['natureRequete'])) {
                 $natureRequete= $inputArray['natureRequete'];
             }
             $matricule=''; 
             if (isset($inputArray['matricule'])) {
                 $matricule= $inputArray['matricule'];
             }

             $requete->idPrestation=$idPrestation;
             $requete->objet=$objet;
             $requete->link_to_prestation=$link_to_prestation;
             $requete->natureRequete=$natureRequete;
             $requete->msgrequest=$msgrequest;
             $requete->plainte=$plainte;
             $requete->updated_by = $idUser;
             $requete->contact = $contactUs;
             $requete->matricule = $matricule;
             $requete->save();

             return array("status" => "success", "message" => "");
         } catch (\Illuminate\Database\QueryException $ex) {
             \Log::error($ex->getMessage());

             return array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
            
         } catch (\Exception $ex) {
             \Log::error($ex->getMessage());
             $error = array("status" => "error", "message" => "Une erreur est survenue lors du" .
                                " chargement des connexions. Veuillez contactez l'administrateur" );
                        return $error;
                    }
     }

     public function updatePfc($id, Request $request) {
         try {
             $inputArray = $request->all();
        //   return response($inputArray);
             $requete=Requete::find($id);
          
             //verifie les champs fournis contactUs
             if (!(isset($inputArray['objet']) && isset($inputArray['id']))) { //controle d existence 
                 return array("status" => "error", "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
             }

             $idPrestation='';
             if (isset($inputArray['idPrestation'])) {
                 $idPrestation= $inputArray['idPrestation'];
             }
             $objet='';
             if (isset($inputArray['objet'])) {
                 $objet= $inputArray['objet'];
             }
             $contactUs=''; //Contact pour usage cas du PFC 
             if (isset($inputArray['contactUs'])) {
                 $contactUs= $inputArray['contactUs'];
             }
             $msgrequest='';
             if (isset($inputArray['msgrequest'])) {
                 $msgrequest= $inputArray['msgrequest'];
             }
    
             $link_to_prestation=0;
             if (isset($inputArray['link_to_prestation'])) {
                 $link_to_prestation= $inputArray['link_to_prestation'];
             }
          
             $nbreJours='';
             if (isset($inputArray['nbreJours'])) {
                 $nbreJours= $inputArray['nbreJours'];
             }

             $plainte=2;
             if (isset($inputArray['plainte'])) {
                 $plainte= $inputArray['plainte'];
             }

             $idUser=1;
             if (isset($inputArray['idUser'])) {
                 $idUser= $inputArray['idUser'];
             }
             $email='';
             if (isset($inputArray['email'])) {
                 $email= $inputArray['email'];
             }
             $matricule=''; 
             if (isset($inputArray['matricule'])) {
                 $matricule= $inputArray['matricule'];
             }
             $natureRequete=5; // En ligne par défaut
             if (isset($inputArray['natureRequete'])) {
                 $natureRequete= $inputArray['natureRequete'];
             }


             $requete->idPrestation=$idPrestation;
             $requete->dureePrestation=$nbreJours;
             $requete->objet=$objet;
             $requete->link_to_prestation=$link_to_prestation;
             $requete->natureRequete=$natureRequete;
             $requete->msgrequest=$msgrequest;
             $requete->plainte=$plainte;
             $requete->updated_by = $idUser;
             $requete->contact = $contactUs;
             $requete->email = $email;
             $requete->matricule = $matricule;
             $requete->save();

             return array("status" => "success", "message" => "");
         } catch (\Illuminate\Database\QueryException $ex) {
             \Log::error($ex->getMessage());

             return array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
            
         } catch (\Exception $ex) {
             \Log::error($ex->getMessage());
             $error = array("status" => "error", "message" => "Une erreur est survenue lors du" .
                                " chargement des connexions. Veuillez contactez l'administrateur" );
                        return $error;
                    }
     }



     ///create a request as usager
     public function createRequestAsUsager(Request $request)
     {
         try {
             $inputArray = $request->all();
             //verifie les champs fournis
      if (!(isset($inputArray['objet']))) { //controle d existence
        return array("status" => "error",
          "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
      }

             $idPrestation='';
             if (isset($inputArray['idPrestation'])) {
                 $idPrestation= $inputArray['idPrestation'];
             }
             $objet='';
             if (isset($inputArray['objet'])) {
                 $objet= $inputArray['objet'];
             }
             $msgrequest='';
             if (isset($inputArray['msgrequest'])) {
                 $msgrequest= $inputArray['msgrequest'];
             }
             $idEtape=1;
             if (isset($inputArray['idEtape'])) {
                 $idEtape= $inputArray['idEtape'];
             }

             $link_to_prestation=0;
             if (isset($inputArray['link_to_prestation'])) {
                 $link_to_prestation= $inputArray['link_to_prestation'];
             }
    
             $plainte=2;
             if (isset($inputArray['plainte'])) {
                 $plainte= $inputArray['plainte'];
             }

             $interfaceRequete='SRU';
             if (isset($inputArray['interfaceRequete'])) {
                 $interfaceRequete= $inputArray['interfaceRequete'];
             }

             $nom='';
             if (isset($inputArray['nom'])) {
                 $nom= $inputArray['nom'];
             }
             $email='';
             if (isset($inputArray['email'])) {
                 $email= $inputArray['email'];
             }
             $tel='';
             if (isset($inputArray['tel'])) {
                 $tel= $inputArray['tel'];
             }
             $idDepartement='';
             if (isset($inputArray['idDepartement'])) {
                 $idDepartement= $inputArray['idDepartement'];
             }

             $natureRequete=5; // En ligne par défaut
             if (isset($inputArray['natureRequete'])) {
                 $natureRequete= $inputArray['natureRequete'];
             }

             $nbreJours='';
             if (isset($inputArray['nbreJours'])) {
                 $nbreJours= $inputArray['nbreJours'];
             }

             $idUser=0;
             if (isset($inputArray['idUser'])) {
                 $idUser= $inputArray['idUser'];
             }

             $visible=0;
             if (isset($inputArray['visible'])) {
                 $visible= $inputArray['visible'];
             }

             //Générer le CODE
             //Génération du code
             $getcode = DB::table('outilcollecte_requete')->select(DB::raw('max(code) as code'))->get();
             $code=1;
             $codeRequete="REQ000000";
             if (!empty($getcode)) {
                 $code+=$getcode[0]->code;
             }

             if (($code>0) &&($code<10)) {
                 $codeRequete="REQ00000".$code;
             }

             if (($code>=10) &&($code<1000)) {
                 $codeRequete="REQ0000".$code;
             }

             if (($code>=1000) &&($code<10000)) {
                 $codeRequete="REQ000".$code;
             }

             if (($code>=10000) &&($code<100000)) {
                 $codeRequete="REQ00".$code;
             }

             if (($code>=100000) &&($code<1000000)) {
                 $codeRequete="REQ0".$code;
             }
             if (($code>=1000000) &&($code<10000000)) {
                 $codeRequete="REQ".$code;
             }


             //$userconnect = new AuthController;
             //$userconnectdata = $userconnect->user_data_by_token($request->token);

             $requete= new Requete;

             $requete->idPrestation=$idPrestation;
             $requete->dureePrestation=$nbreJours;
             $requete->link_to_prestation=$link_to_prestation;
             $requete->objet=$objet;
             $requete->msgrequest=$msgrequest;
             $requete->idEtape=$idEtape;
             $requete->codeRequete=$codeRequete;
             $requete->code=$code;
             $requete->natureRequete=$natureRequete;
             $requete->interfaceRequete=$interfaceRequete;
             $requete->plainte=$plainte;
             $requete->visible= $visible;
             $requete->fichier_joint= "";
             $requete->idEntite=$request->idEntite;

             $requete->created_by = $idUser;

             // Enregistrement de l'usager s'il ne l'est pas encore
             $checkusager=Usager::where("email", "=", $email)->get();


             $requete->idUsager=$checkusager[0]->id;

             $requete->save();

             return array("status" => "success", "message" => "","data" => $requete->id);
         } catch (\Illuminate\Database\QueryException $ex) {
             \Log::error($ex->getMessage());

             $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" );
             return $error;
         } catch (\Exception $ex) {
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

     public function destroy($id)
     {
         Requete::find($id)->delete();
     }

     

     // Liste des courriers
     public static function getRequeteByUser(Request $request, $idEntite)
     {
         try {
            $input = $request->query();
            $idUser= $input['idUser'];

            $getUser=Utilisateur::find($idUser);
            $getProfil=Profil::find($getUser->idprofil);

            $resultat =array();

            // ${idEntite}?traiteOuiNon=${traiteOuiNon}&idUser=${idUser}&structure=${structure}&plainte=${plainte}&search=${search}&page=${page}
           
            if (isset($input['search'])) {
                // return response('ssss');
                $search=strtoupper(trim($input['search']));
                //where('idEntite',$idEntite)->
                $query = Requete::with(['usager','entite_receive','reponses_rapide','entite','service','service.type','nature','notes', 'etape','reponse' => function ($query) {
                    return $query->orderBy('id', 'DESC');
                },'affectation','affectation.structure','parcours','parcours.structure','lastparcours','lastaffectation','lastaffectation.structure','lastaffectation.entiteAdmin'])
                // ->where("visible", "=", true)
                // ->where("reponseStructure", "=", null) structure
            
                ->where(function ($query) use ($search){
                $query->orwhere("objet", "LIKE", "%{$search}%");
                $query->orWhere("msgrequest", "LIKE", "%{$search}%");
                $query->orWhere("codeRequete", "LIKE", "%{$search}%");
                $query->orWhereHas('usager', function ($q) use ($search) {
                    $q->where('email', "LIKE", "%{$search}%");
                });
                $query->orWhereHas('usager', function ($q) use ($search) {
                    $q->where('nom', "LIKE", "%{$search}%");
                });
                $query->orWhereHas('usager', function ($q) use ($search) {
                    $q->where('prenoms', "LIKE", "%{$search}%");
                });
                $query->orWhereHas('usager', function ($q) use ($search) {
                    $q->where(DB::raw("CONCAT(`nom`, ' ', `prenoms`)"), "LIKE", "%{$search}%");
                });
                $query->orWhereHas('service', function ($q) use ($search) {
                    $q->where('libelle', "LIKE", "%{$search}%");
                });
                $query->orWhereHas('service.type', function ($q) use ($search) {
                    $q->where('libelle', "LIKE", "%{$search}%");
                });
            });
            } else {
                // return response($query->get());
                // where('idEntite',$idEntite)->
                $query = Requete::with(['usager','entite_receive','entite','reponses_rapide','service','service.type','nature','etape','notes','reponse' => function ($query) {
                            return $query->orderBy('id', 'DESC');
                        },'affectation','affectation.structure','parcours','parcours.structure','lastparcours','lastaffectation','lastaffectation.structure','lastaffectation.entiteAdmin']
                );
            }
            
            // $query = $query->whereHas('affectation.structure', function ($q) {
            //     $q->where('type_s',"=", 'dt')->orWhere('type_s',"=", 'dc');
            // });
            /*if( ($getProfil->parametre!=1) && ($getProfil->saisie!=1) &&  ($getProfil->sgm!=1) && ($getProfil->dc!=1) && ($getProfil->ministre!=1) )
            {
            $idagent=$getUser->idagent;

            $getAgent=Acteur::find($idagent);

            $idStructure="";

            if($getAgent !== null ) //count($getAgent)>0) idUser
            {
                $idStructure=$getAgent->idStructure;

                $query = $query->whereHas('affectation', function($q) use($idStructure) {
                    $q->where('idStructure',"=", $idStructure);
                    });
            }
            }*/
            
            if (isset($input['byUser'])) {
                $query = $query->where("created_by", "=", $idUser);
            }
            if (isset($input['traiteOuiNon'])) {
                $query=$query->where("traiteOuiNon", "=", $input['traiteOuiNon']);
            }
            
            if (isset($input['plainte'])) {
                $query=$query->where("plainte", "=", $input['plainte']);
            }
            
            if (isset($input['type'])) {
                $query=$query->where("plainte", "=", $input['plainte']);
            }
            
            
            if (isset($input['startDate'])) {
                
                $startDate =$input["startDate"];
                $endDate = $input["endDate"];
                $query=$query->whereDate('dateRequete', '>=', $startDate)
                        ->whereDate('dateRequete', '<=', $endDate);
            }
            
            
            if (isset($input['structure'])) {
                
                $idStructure=$input['structure'];
                $query = $query->whereHas('affectation', function ($q) use ($idStructure) {
                    $q->where('idStructure',"=", $idStructure);
                });
            }
            
            if (isset($input['type'])) {
                
                $typeThe=$input['type'];
                $query = $query->whereHas('service.type', function ($q) use ($typeThe) {
                    $q->where('id',"=", $typeThe);
                });
            }
            // $rec = "";
            // foreach( $query->get() as $ok){
            //     $rec .= $ok->id.',';
            // }
            // return  $rec;
            $resultat = $query->orderBy('id', 'DESC')->paginate(250);

            if (isset($input['parc'])) {
                //ACTIVITY
                Activity::SaveActivity($idUser,"Consultation d'un parcours requête");
            }
             return response($resultat);
         } catch (\Illuminate\Database\QueryException $ex) {
             \Log::error($ex->getMessage());

             $error = array("error"=>$ex->getMessage(),"status" => "errors", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" );
            
             return $error;
         } catch (\Exception $ex) {
             \Log::error($ex->getMessage());
            
             $error = array("error"=>$ex->getMessage(),"status" => "errors", "message" => "Une erreur est survenue lors du chargement des requêtes. Veuillez contactez l'administrateur" );
             return $error;
         }
     }
     

     // Liste des courriers
     public static function getRequeteByUser_Stat(Request $request, $idEntite)
     {
         try {
            $input = $request->query();
            $idUser= $input['idUser'];
            $id_connUse = $input['id_connUse'];
            // relance 
            $resultat = array();

            $query = Requete::with(['affectation','lastparcours','relance'])->where("traiteOuiNon", "=", 0);

            // if (isset($input['plainte'])) {
            //     $query=$query->where("plainte", "=", $input['plainte']);
            // }

            if (isset($input['structure'])) {
                $idStructure=$input['structure'];
                $query = $query->whereHas('affectation', function ($q) use ($idStructure) {
                    $q->where('idStructure',"=", $idStructure);
                });
            }
            
            $resultat = $query->orderBy('id', 'DESC')->paginate(1000);
            //ACTIVITY
            $use = User::where('id',$idUser)->first();
            $act = Acteur::where('id',$use->idagent)->first();
            if($act){
                $act = $act->nomprenoms;
            }else{
                $act = '--';
            }
            Activity::SaveActivity($id_connUse,"Consultation de l'historique des connectivités de ".$act);

             return response($resultat);
         } catch (\Illuminate\Database\QueryException $ex) {
             \Log::error($ex->getMessage());

             $error = array("error"=>$ex->getMessage(),"status" => "errors", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" );
            
             return $error;
         } catch (\Exception $ex) {
             \Log::error($ex->getMessage());
            
             $error = array("error"=>$ex->getMessage(),"status" => "errors", "message" => "Une erreur est survenue lors du chargement des requêtes. Veuillez contactez l'administrateur" );
             return $error;
         }
     }

     
     public static function listCommune($id_u) {
        if($id_u == '147' || $id_u == '149'){ //sru@gouv.bj ou oatakpame@gouv.bj
            $reqCom = Registre::join('outilcollecte_users','outilcollecte_users.id','outilcollecte_registre.created_by')
            					->join('outilcollecte_acteur','outilcollecte_acteur.id','outilcollecte_users.idagent')
            					->join('outilcollecte_commune','outilcollecte_acteur.idCom','outilcollecte_commune.id')
            					->join('outilcollecte_departement','outilcollecte_departement.id','outilcollecte_commune.depart_id')
            					->select('outilcollecte_commune.*','outilcollecte_departement.libelle')
            					->orderby('outilcollecte_commune.libellecom','asc')
            					->distinct()
            					->get();
        }else{
            $reqCom = AttribCom::join('outilcollecte_commune','outilcollecte_attribuer.id_com','outilcollecte_commune.id')
                                ->join('outilcollecte_departement','outilcollecte_departement.id','outilcollecte_commune.depart_id')
                                ->select('outilcollecte_commune.*','outilcollecte_departement.libelle')
                                ->where('outilcollecte_attribuer.id_user',$id_u)
                                ->orderby('outilcollecte_commune.libellecom','asc')
                                ->distinct()
                                ->get();
        }

        return $reqCom;
     }
     public static function listCommuneTrRequMat() {

        $reqCom = Registre::join('outilcollecte_users','outilcollecte_users.id','outilcollecte_registre.created_by')
                            ->join('outilcollecte_acteur','outilcollecte_acteur.id','outilcollecte_users.idagent')
                            ->join('outilcollecte_commune','outilcollecte_acteur.idCom','outilcollecte_commune.id')
                            ->join('outilcollecte_departement','outilcollecte_departement.id','outilcollecte_commune.depart_id')
                            ->where('outilcollecte_registre.idreq','<>',0)
                            ->select('outilcollecte_commune.*','outilcollecte_departement.libelle')
                            ->orderby('outilcollecte_commune.libellecom','asc')
                            ->distinct()
                            ->get();
        return $reqCom;
     }

     public function listUsersComm($idcom) {

         $reqCom = User::join('outilcollecte_acteur','outilcollecte_users.idagent','outilcollecte_acteur.id')
                            ->where('outilcollecte_acteur.idCom',$idcom)
                            ->select('outilcollecte_acteur.nomprenoms','outilcollecte_users.id')
                            ->orderby('outilcollecte_acteur.nomprenoms','asc')
                            ->distinct()
                            ->get();

        return $reqCom;
     }

     // Liste des courriers
     public static function getRegistreByUser(Request $request, $idEntite)
     {
         try {
            
            $input = $request->query();
            $result=array();
            if (isset($input['search'])) {   
                $search=$input['search'];
                // creator.agent_user communue
                $query = Registre::with(['creator','entite','creator.agent_user'])
                                   ->orderBy('id', 'desc')
                                   ->where("matri_telep", "LIKE", "%{$search}%")
                                   ->orWhere("nom_prenom", "LIKE", "%{$search}%")
                                   ->orWhere("contenu_visite", "LIKE", "%{$search}%")
                                   ->orWhere("motif_non", "LIKE", "%{$search}%")
                                   ->orWhere("observ_visite", "LIKE", "%{$search}%");
            } else {
                $query = Registre::with(['creator','entite','creator.agent_user'])->orderBy('id', 'desc');
            }
            
            $commu = $input['communue'];
            // if (isset($commu)) {
                $query = $query->whereHas('creator.agent_user', function ($q) use ($commu) {
                    $q->where('idCom',"=", $commu);
                });
            // }
            if (isset($input['statut'])) {
                $query = $query->where("satisfait", "=", $input['statut']);
            }
            if (isset($input['iduserCom'])) {
                $query = $query->where("created_by", "=", $input['iduserCom']);
            }

            if (isset($input['startDate'])) {
                $startDate =$input["startDate"];
                $endDate = $input["endDate"];
                $query=$query->whereDate('created_at', '>=', $startDate)
                        ->whereDate('created_at', '<=', $endDate);
            }
            $resultat = $query->orderBy('id', 'asc')
                    ->paginate(25);
             return response($resultat);

         } catch (\Illuminate\Database\QueryException $ex) {
             \Log::error($ex->getMessage());

             $error = array("error"=>$ex->getMessage(),"status" => "errors", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" );
            
             return $error;
         } catch (\Exception $ex) {
             \Log::error($ex->getMessage());
            
             $error = array("error"=>$ex->getMessage(),"status" => "errors", "message" => "Une erreur est survenue lors du chargement des requêtes. Veuillez contactez l'administrateur" );
             return $error;
         }
     }


     // Liste des courriers
     public static function getCountRequeteByUser($idUser)
     {
         try {
             $getuser=Utilisateur::find($idUser);

             $result =0;



             $idagent=$getuser->idagent;

             $getAgent=Acteur::find($idagent);

             $idStructure="";

             if (count($getAgent)>0) {
                 $idStructure=$getAgent->idStructure;

                 $result=Affectation::where("idStructure", "=", $idStructure)->count();
             }


             return $result;
         } catch (\Illuminate\Database\QueryException $ex) {
             \Log::error($ex->getMessage());

             $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" );
         } catch (\Exception $ex) {
             \Log::error($ex->getMessage());

             $error = array("status" => "error", "message" => "Une erreur est survenue" );
             return $error;
         }
     }



     public function saveReponse(\Illuminate\Http\Request $request)
     {
         try {
            $inputArray = $request->all();
            //  return response($inputArray);
             
             //verifie les champs fournis
          if (!(isset($inputArray['idRequete']))) { //controle d existence texteReponseApportee  texteReponse
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
             if ($getAgent !== null) {               //count($getAgent)>0)
                 $idStructure=$getAgent->idStructure;
             }



             $idRequete=0;
             if (isset($inputArray['idRequete'])) {
                 $idRequete= $inputArray['idRequete'];
             }

             $raisonRejet="";
             if (isset($inputArray['raisonRejet'])) {
                 $raisonRejet= $inputArray['raisonRejet'];
             }

             $interrompu=false;
             if (isset($inputArray['interrompu'])) {
                 $interrompu= $inputArray['interrompu'];
             }

             $rejete=false;
             if (isset($inputArray['rejete'])) {
                 $rejet= $inputArray['rejete'];
             }


             $texteReponse="";
             if (isset($inputArray['texteReponse'])) {
                 $texteReponse= $inputArray['texteReponse'];
             }

             $typeStructure='Division';
             if (isset($inputArray['typeStructure'])) {
                 $typeStructure= $inputArray['typeStructure'];
             }


             if (isset($inputArray['idEtape'])) {
                 $idEtape= $inputArray['idEtape'];
             }

             //hasFile
            //  return response($request->hasFile('fichier'));

                $pj = "";
                $fileName = "";
                if ($request->file('fichier')) {
                    $file = $request->file('fichier');
                    $extension = $file->getClientOriginalExtension();
                    $fileName = $idRequete.date('YmdH').'.'.$extension;
                    $pathName = Storage::path("public/Usager-mail/");
                    $file->move($pathName, $fileName);
                    $pj = $pathName.$fileName;
                }
             //Check if reponse exist
             $checkreponse=Reponse::where("idStructure", "=", $idStructure)->where("typeStructure", "=", $typeStructure)->where("idRequete", "=", $idRequete)->get();
             
             if (count($checkreponse)==0) {
                 $reponse=new Reponse;
                $reponse->texteReponse=$texteReponse;
                $reponse->fichier_joint=$fileName;
                $reponse->idStructure=$idStructure;
                $reponse->idRequete=$idRequete;
                $reponse->typeStructure=$typeStructure;
                $reponse->siTransmis=0;
                $reponse->interrompu=$interrompu;
                $reponse->rejete=$rejete;
                $reponse->idEntite=$request->idEntite;
                $reponse->raisonRejet=$raisonRejet;
                $reponse->save();
            }else{
                $reponse=Reponse::find($checkreponse[0]->id);
                $reponse->texteReponse=$texteReponse;
                $reponse->fichier_joint=$fileName;
                $reponse->interrompu=$interrompu;
                $reponse->rejete=$rejete;
                $reponse->raisonRejet=$raisonRejet;
                $reponse->save();
             }

            //ACTIVITY
            Activity::SaveActivity($userconnectdata->id,"Enregistrement d'une réponse");

            //  print_r($reponse);
             $req=Requete::find($idRequete);
             $req->interrompu=$interrompu;
             $req->rejete=$rejete;
             $req->raisonRejet=$raisonRejet;
             $req->fichier_joint=$fileName;
             $req->save();

            //Code deplacer vers la methode transmettrereponse
            // Seulement les reponses transmises

             if ($typeStructure=='Direction' || $typeStructure=='SRU') {
                 $req1=Requete::find($idRequete);
                 $req1->reponseStructure=$texteReponse;

                 $req1->save();
             }
             if ($typeStructure=='Division') {
                 $req1=Requete::find($idRequete);
                 $req1->reponseDivision=$texteReponse;

                 $req1->save();
             }

             if ($typeStructure=='Service') {
                 $req1=Requete::find($idRequete);
                 $req1->reponseService=$texteReponse;

                 $req1->save();
             }
             if ($typeStructure=='SRU Secondaire') {
                 $req1=Requete::find($idRequete);
                 $req1->reponseSRUSecondaire=$texteReponse;

                 $req1->save();
             }
             //Relance

             return ['success'=> true];
         } catch (\Illuminate\Database\QueryException $ex) {
             \Log::error($ex->getMessage());

             $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
         } catch (\Exception $ex) {
             \Log::error($ex->getMessage());
             $error = array("status" => "error", "message" =>"Une erreur est survenue au cours de l'enregistrement. Contactez l'administrateur.");
             return $error;
         }
     }

     public function ArchiverRequete(\Illuminate\Http\Request $request)
     {
         try {
            $inputArray = $request->all();
             //verifie les champs fournis
          if (!(isset($inputArray['idRequete']))) { //controle d existence texteReponseApportee  texteReponse
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
          }
          
             //return json_encode($request->token); traiteOuiNon typeStructure

             $userconnect = new AuthController;
             $userconnectdata = $userconnect->user_data_by_token($request->token);

             $getuser=Utilisateur::find($userconnectdata->id);
             $idagent=$getuser->idagent;
             $getAgent=Acteur::find($idagent);

             $idStructure="";
             if ($getAgent !== null) {               //count($getAgent)>0)
                 $idStructure=$getAgent->idStructure;
             }

             $idRequete=0;
             if (isset($inputArray['idRequete'])) {
                 $idRequete= $inputArray['idRequete'];
             }
             $texteReponse="";
             if (isset($inputArray['texteReponse'])) {
                 $texteReponse= $inputArray['texteReponse'];
             }
            $req=Requete::find($idRequete);
            $req->archiver=1;
            $req->motif_archive=$texteReponse;
            $req->traiteOuiNon=1;
            $req->dateReponse=date("Y-m-d H:m:i");
            $req->finalise=1;
            $req->save();
            
            Activity::SaveActivity($userconnectdata->id,"Archiver une préoccupation");
             return ['success'=> true];
         } catch (\Illuminate\Database\QueryException $ex) {
             \Log::error($ex->getMessage());

             $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
         } catch (\Exception $ex) {
             \Log::error($ex->getMessage());
             $error = array("status" => "error", "message" =>"Une erreur est survenue au cours de l'enregistrement. Contactez l'administrateur.");
             return $error;
         }
     }

     public function ModifierReque(\Illuminate\Http\Request $request)
     {
         try {
            $inputArray = $request->all();
             //verifie les champs fournis
          if (!(isset($inputArray['idRequete']))) { //controle d existence texteReponseApportee  texteReponse
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
          }
          
             //return json_encode($request->token); traiteOuiNon typeStructure

             $userconnect = new AuthController;
             $userconnectdata = $userconnect->user_data_by_token($request->token);

             $getuser=Utilisateur::find($userconnectdata->id);
             $idagent=$getuser->idagent;
             $getAgent=Acteur::find($idagent);

             $idRequete=0;
             if (isset($inputArray['idRequete'])) {
                 $idRequete= $inputArray['idRequete'];
             }
             $plainte=2;
             if (isset($inputArray['plainte'])) {
                 $plainte= $inputArray['plainte'];
             }
            $req=Requete::find($idRequete);
            $req->plainte=$plainte;
            $req->save();
            
             return ['success'=> true];
         } catch (\Illuminate\Database\QueryException $ex) {
             \Log::error($ex->getMessage());

             $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
         } catch (\Exception $ex) {
             \Log::error($ex->getMessage());
             $error = array("status" => "error", "message" =>"Une erreur est survenue au cours de l'enregistrement. Contactez l'administrateur.");
             return $error;
         }
     }



     public function transmettreReponse(\Illuminate\Http\Request $request)
     {
         
         try {
             $inputArray =$request->all();
             //verifie les champs fournis
          if (!(isset($inputArray['idRequete']))) { //controle d existence reponseStructure
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
          }
             $idRequete=0;
             if (isset($inputArray['idRequete'])) {
                 $idRequete= $inputArray['idRequete'];
             }

             if (isset($inputArray['idEtape'])) {
                 $idEtape= $inputArray['idEtape'];
             }

             $typeStructure='Division'; 

             if (isset($inputArray['typeStructure'])) {
                 $typeStructure= $inputArray['typeStructure'];
             }

             if (isset($inputArray['typeSuperieur'])) {
                 $typeSuperieur= $inputArray['typeSuperieur'];
             }


             $userconnect = new AuthController;
             $userconnectdata = $userconnect->user_data_by_token($request->token);

             $getuser=Utilisateur::find($userconnectdata->id);

             $idagent=$getuser->idagent;

             $getAgent=Acteur::find($idagent);

             $idStructure="";

             if ($getAgent !== null) {     ///count($getAgent)>0) traiteOuiNon
                $idStructure=$getAgent->idStructure;
             }

             $req=Requete::find($idRequete);

             //modification du statut de transmission de la réponse courante
             $getreponse=Reponse::where("idRequete", '=', $idRequete)->where("idStructure", '=', $idStructure)->first();
             $getreponse->siTransmis=1;
             $getreponse->save();
             //ACTIVITY
            Activity::SaveActivity($userconnectdata->id,"Transmission d'une réponse");

            //  print_r($typeStructure);

             if ($typeStructure!='Direction' && $typeStructure!='SRU') {  
                 //Get Reponse

                 $reponse=new Reponse;

                 $reponse->texteReponse=$getreponse->texteReponse;

                 //Récupérer l'ID parent
                 $structure=Structure::find($idStructure);

                 $reponse->idStructure=$structure->idParent;
                 $reponse->idEntite=$request->idEntite;
                 $reponse->idRequete=$idRequete;

                 if ($typeStructure=='Division') {
                     $reponse->typeStructure='Service';
                 }elseif ($typeStructure=='Service') {
                     $reponse->typeStructure='Direction';
                 }elseif ($typeStructure=='SRU Secondaire') {
                     $reponse->typeStructure='SRU';
                 }else{
                    $reponse->typeStructure = $typeStructure;
                 }

                 $reponse->dateTransmission  = date("Y-m-d H:m:i");

                 $reponse->save();


                 //Enregistrement parcours
                 $parcours=new Parcoursrequete;
                 $parcours->typeStructure=$typeSuperieur;
                 $parcours->idRequete=$req->id;
                 $parcours->idEntite=$request->idEntite;
                 $parcours->idStructure=$structure->idParent;
                 $parcours->idEtape=$idEtape;
                 $parcours->dateArrivee=date("Y-m-d h:m:i");
                 $parcours->save();


                 //Notification à la structure
                 $getstructure=Structure::find($structure->idParent);

                 if ($getstructure !== null) {               //count($getstructure)>0)
                     $emailstructure=$getstructure->contact;

                     if ($emailstructure!="") {
                         $this->sendmail($emailstructure, "Une réponse a été proposée à votre structure ($getstructure->libelle). Pour valider la réponse, rendez-vous sur la plateforme à l'adresse : https://mataccueil.gouv.bj/login.", "MTFP : Service usager");
                     }
                 }
             }
                    



            if ($typeStructure=='Direction' || $typeStructure=='SRU') {

                

                $req->dateReponse=date("Y-m-d H:m:i");
                $req->traiteOuiNon=1;

                if ($req->interrompu==1) {
                    $req->finalise=0;
                } else {
                    $req->finalise=1;
                }
                $req->horsDelai=1; // cas où la requête n'a pas un délai fixe.
                //Vérifier si c'est hors délai
                if ($req->dureePrestation>0) {
                    $dateReponse=new DateTime($req->dateReponse);
                    $dateEnreg=new DateTime($req->created_at);
            
                    $dif=$dateReponse->diff($dateEnreg)->format("%a");
                    if ($dif<=$req->dureePrestation) {
                        $req->horsDelai=2;
                    } // Cas où la requête a été traitée dans les délais.
                    else {
                        $req->horsDelai=3;
                    } // Cas où la requête a été traitée hors délais.
                }
                //Affecter la reponse à l'usager
                $req->reponseStructure=$getreponse->texteReponse;
                $req->save();

                //Enregistrement parcours
                $parcours=new Parcoursrequete;
                $parcours->typeStructure='USAGER';
                $parcours->idRequete=$req->id;
                $parcours->idStructure=0;
                $parcours->idEntite=$request->idEntite;
                $parcours->idEtape=$idEtape;
                $parcours->dateArrivee=date("Y-m-d h:m:i");
                $parcours->save();
                //Relance Update etat 
                Relance::where('idRequete', '=', $req->id)->update(['etat' => 'a']); //Achevé
                //Send reponse à l'usager email
                $getUsager=Usager::where("id", "=", $req->idUsager)->get();
                if (count($getUsager)>0) {
                    $mail=$getUsager[0]->email;
                    $reponseUsager="Service Relations Usagers du Ministère du Travail et de la Fonction Publique : <br/>";
                    $reponseUsager.="Objet de votre requête : $req->objet <br/>";
                    $reponseUsager.="Réponse du MTFP : $getreponse->texteReponse <br/><br/>";
                    $reponseUsager.="Êtes-vous satisfait de la prestation ? Faites-le nous savoir en donnant une note.<br/>";
                }else{
                    $mail = $req->email;
                    $reponseUsager="Service Relations Usagers du Ministère du Travail et de la Fonction Publique : <br/>";
                    $reponseUsager.="Objet de votre requête : $req->objet <br/>";
                }

                if($req->fichier_joint != null || $req->fichier_joint != "" ){

                    $path = Storage::path("public/Usager-mail/".$req->fichier_joint);
                    if (file_exists($path)) {
                        //Send le mail !
                        if(filter_var($mail, FILTER_VALIDATE_EMAIL)){ //Vérifier si c'est un bon mail...
                            $title = $req->fichier_joint;
                            Mail::send("mail.reponsemail", ['reponse'=>$reponseUsager], function($message)use ($mail,$title){
                                $message->from("mtfp.usager@gouv.bj", "SRU")
                                        ->subject("Réponse à votre requête");
                                $message->attach(Storage::path("public/Usager-mail/".$title), [
                                        'as' => $title,
                                        'mime' => 'application/pdf',
                                    ]);
                                $message->to($mail,"USAGER");
                            });
                        }
                    }   
                }else{
                    if ($mail!="") {
                        $this->sendmail($mail, $reponseUsager);
                    }
                }
            }
            return ['success'=> true];
         } catch (\Illuminate\Database\QueryException $ex) {
             \Log::error($ex->getMessage());

             $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
         } catch (\Exception $ex) {
             \Log::error($ex->getMessage());
             $error =  array("error" => $ex->getMessage(),"status" => "error", "message" =>"Une erreur est survenue au cours de l'enregistrement. Contactez l'administrateur.");
             return $error;
         }
     }

     public function relanceReponse(\Illuminate\Http\Request $request){

        try {
            $inputArray =$request->all();
            //verifie les champs fournis
            
            if (!(isset($inputArray['idRequete']))) { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }

            $idRequete=0;
            if (isset($inputArray['idRequete'])) {
                $idRequete= $inputArray['idRequete'];
            }

            if (isset($inputArray['idStructure'])) {
                $idStructure = $inputArray['idStructure'];
            }
            
            $req=Requete::find($idRequete);

            //Notification à la structure
            $getstructure=Structure::find($idStructure);

            if ($getstructure !== null) {               //count($getstructure)>0)
                $emailstructure=$getstructure->contact;

                if ($emailstructure!="") {
                    $this->sendmail($emailstructure, "Par ce présent nous vous relançons sur cette requête : ".
                    $req->id." Objet : ".$req->objet." \n Message : ".$req->msgrequest." \nPrestation : ".Service::find($idPrestation)
                    , "RELANCE REQUÊTE");
                }
            }     

            return ['success'=> true];
        } catch (\Illuminate\Database\QueryException $ex) {
            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage());
            $error =  array("error" => $ex->getMessage(),"status" => "error", "message" =>"Une erreur est survenue au cours de la relance. Contactez l'administrateur.");
            return $error;
        }
     }








     public function transmettreReponserapideUsager(\Illuminate\Http\Request $request)
     {
        
         try {
             //verifie les champs fournis
            if (!(isset($request->codeRequete))) { //controle d existence emailusager
                    return array("status" => "error",
                        "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }
            // return response($request->all());
             $emailusager= $request->emailusager;
             if(filter_var($emailusager, FILTER_VALIDATE_EMAIL)){
                // Mail est bon 
                $nomprenomsusager= $request->nomprenomsusager;
                $emailstructure= $request->emailstructure;
                $message= $request->message;
                $codeRequete= $request->codeRequete;
                $type= $request->type; // SIMPLE ou DEMANDE D'INFORMATION
                
                //$emailusager="gildas.zinkpe@gmail.com";

                //$emailstructure="gildas.zinkpe@gmail.com";
                $pj = "";
                $fileName = "";
                if ($request->file('fichier')) {
                    $file = $request->file('fichier');
                    $extension = $file->getClientOriginalExtension();
                    $fileName = date('His').rand(1,99999).'.'.$extension;
                    $pathName = Storage::path("public/Usager-mail/");
                    $file->move($pathName, $fileName);
                    $pj = $pathName.$fileName;
                } 

                $reponserapide=new Reponserapide;
                $reponserapide->emailstructure=$emailstructure;
                $reponserapide->fichier_joint=$fileName;
                $reponserapide->emailrecevier=$emailusager;
                $reponserapide->receiver=$nomprenomsusager;
                $reponserapide->message=$message;
                $reponserapide->typerReceiver="USAGER";
                $reponserapide->type=$type;
                $reponserapide->idEntite=$request->idEntite;
                $reponserapide->codeRequete=$codeRequete;
                $reponserapide->save();
                
                
                //ACTIVITY
                $usercon = User::where('email',$emailstructure)->first();
                Activity::SaveActivity($usercon->id,"Envoi d'une réponse rapide à usager (Mail)");

                $reponseUsager="Service Relations Usagers du Ministère du Travail et de la Fonction Publique : \n\n";
                $reponseUsager.="Message urgent du MTFP suite à votre requête : \n\n";
                $reponseUsager.="$message \n \n";
                //  if ($type!="SIMPLE") {
                //      $reponseUsager.="Rendez-vous sur :  https://mataccueil.bj/requete-usager/complement-information/$reponserapide->id/$codeRequete  pour fournir les renseignements demandés";
                //  }
                $CopiereponseUsager="Plateforme de gestion des préoccupations des usagers du Ministère du Travail et de la Fonction Publique : \n\n";
                $CopiereponseUsager.="Copie de la réponse urgente envoyée à l'adresse $emailusager de l'usager du MTFP $nomprenomsusager \n\n";
                $CopiereponseUsager.="$message \n \n";
                
                if(trans('auth.mode') != 'test'){
                    if ($emailusager!="") {
                        $this->sendmail($emailusager, $reponseUsager);
                    }
                    if ($emailstructure!="") {
                        $this->sendmail($emailstructure, $CopiereponseUsager);
                    }
                }
                return array("status" => "success", "message" => $emailusager);
            }else{
                return array("status" => "error", "message" => "Le mail ($emailusager) de cet usage n'est pas correcte.");
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage());
            $error =  array("status" => "error", "message" =>"Une erreur est survenue au cours de l'enregistrement. Contactez l'administrateur.");
            return $error;
        }
     }
     public function mailStructure(\Illuminate\Http\Request $request)
     {
         try {
             $inputArray = $request->all();
             //verifie les champs fournis
            if (!(isset($inputArray['codeRequete']))) { //controle d existence
                return array("status" => "error","message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }
            $emailstructure = $inputArray['emailstructure'];
            $receiverId= $inputArray['receiverId'];
            $message= $inputArray['message'];
            $codeRequete= $inputArray['codeRequete'];
            $type= $inputArray['type']; // SIMPLE ou DEMANDE D'INFORMATION

            $structure_receive=Structure::find($receiverId);
            $emailreceiver="";
            $receiver="";

            if ($structure_receive!=null) {
                $emailreceiver= $structure_receive->contact;
                $receiver= $structure_receive->libelle;
            }
            //    ---
            //Vérifier si cest un bon mail 
            if(filter_var($emailreceiver, FILTER_VALIDATE_EMAIL)){
                // Mail est bon 
                $reponserapide=new Reponserapide;
                $reponserapide->emailstructure=$emailstructure;
                $reponserapide->emailrecevier=$emailreceiver;
                $reponserapide->receiver=$receiver;
                $reponserapide->message=$message;
                $reponserapide->typerReceiver="STRUCTURE";
                $reponserapide->type=$type;
                $reponserapide->idEntite=$request->idEntite;
                $reponserapide->codeRequete=$codeRequete;
                $reponserapide->save();
                
                
                //ACTIVITY
                $usercon = User::where('email',$emailstructure)->first();
                Activity::SaveActivity($usercon->id,"Envoi de mail à une structure");

                if(trans('auth.mode') != 'test'){
                    if ($emailreceiver!="") {
                        $reponseReceiver="Service Relations Usagers du Ministère du Travail et de la Fonction Publique : \n\n";
                        $reponseReceiver.="Message urgent du $receiver suite à une requête usager : \n\n";
                        $reponseReceiver.="$message \n \n";
                        //  if ($type!="SIMPLE") {
                        //      $reponseReceiver.="Rendez-vous sur :  http://mataccueil_new.hebergeappli.bj/requete-usager/complement-information/$reponserapide->id/$codeRequete  pour fournir les renseignements demandés";
                        //  }
                        $this->sendmail($emailreceiver, $reponseReceiver);
                    }
                    if ($emailstructure!="") {
                        $CopiereponseReceiver="Plateforme de gestion des préoccupations des usagers du Ministère du Travail et de la Fonction Publique : \n\n";
                        $CopiereponseReceiver.="Copie du mail urgent envoyé à l'adresse $emailreceiver de la structure $receiver du MTFP \n\n";
                        $CopiereponseReceiver.="$message \n \n";
                        $this->sendmail($emailstructure, $CopiereponseReceiver);
                    }
                }
                // Mail est bon 
                return array("status" => "success", "message" => $emailreceiver);
            }else{
                return array("status" => "error", "message" => "Le mail ($emailreceiver) de cette structure n'est pas correcte.");
            }
            
            // return array("status" => "success", "message" => $emailstructure);
         } catch (\Illuminate\Database\QueryException $ex) {
             \Log::error($ex->getMessage());

             $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
         } catch (\Exception $ex) {
             \Log::error($ex->getMessage());
             $error =  array("status" => "error", "message" =>"Une erreur est survenue au cours de l'enregistrement. Contactez l'administrateur.");
             return $error;
         }
     }
     public function complementReponserapide(\Illuminate\Http\Request $request)
     {
         try {
             $inputArray = $request->all();
           
           
             $complement= $inputArray['complement'];



             $reponsesrapide=Reponserapide::find($inputArray['id']);


             if ($reponsesrapide!=null) {
                 $reponsesrapide->complement=$complement;
                 $reponsesrapide->save();

                 $message="Response suite au mail adressé à $reponsesrapide->receiver \n \n";
                 $message.="Message de départ :  $reponsesrapide->message \n \n";
                 $message.="Code requête :  $reponsesrapide->codeRequete \n \n";
                 $message.="Réponse / Complément d'information :  $complement \n \n";

                 $emailstructure=$reponsesrapide->emailstructure;
                 $this->sendmail($emailstructure, $message);

                 return ['success'=> true,'data'=> $reponsesrapide];
             }
         } catch (\Illuminate\Database\QueryException $ex) {
             \Log::error($ex->getMessage());

             $error = array("error" => $ex->getMessage(),"status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
         } catch (\Exception $ex) {
             \Log::error($ex->getMessage());
             $error =  array("error" => $ex->getMessage(),"status" => "error", "message" =>"Une erreur est survenue au cours de l'enregistrement. Contactez l'administrateur.");
             return $error;
         }
     }
     public function findReponserapide(\Illuminate\Http\Request $request, $responsId)
     {
         try {
             $reponsesrapide=Reponserapide::find($responsId);

             return ['success'=> true,'data'=> $reponsesrapide];
         } catch (\Illuminate\Database\QueryException $ex) {
             \Log::error($ex->getMessage());

             $error = array("error" => $ex->getMessage(),"status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
         } catch (\Exception $ex) {
             \Log::error($ex->getMessage());
             $error =  array("error" => $ex->getMessage(),"status" => "error", "message" =>"Une erreur est survenue au cours de l'enregistrement. Contactez l'administrateur.");
             return $error;
         }
     }

     public function transmettreComment(\Illuminate\Http\Request $request)
     {
        try {
            $inputArray = $request->all();
            //verifie les champs fournis
        if (!(isset($inputArray['commentaire']) && isset($inputArray['email']) && isset($inputArray['name']))) { //controle d existence
            return array("status" => "error",
                "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
        }
            $name= $inputArray['name'];
            $email= $inputArray['email'];
            $structure= $inputArray['structure'];
            $message= $inputArray['commentaire'];
        
        
        
            $parametre=Parametre::find($request->idEntite);

            $emailRecepteur=$parametre->emailSuggestion;
            $reponseUsager="Service Relations Usagers du Ministère du Travail et de la Fonction Publique : \n\n";
            $reponseUsager.="$message \n \n";
            $reponseUsager.="De la part de $name : $email / $structure \n \n";
        
            if(trans('auth.mode') != 'test'){
                if ($emailRecepteur!="") {
                    $this->sendmail($emailRecepteur, $reponseUsager);
                }
            }

            $suggestion=new Suggestion;
            $suggestion->message=$message;
            $suggestion->nomEmetteur=$name;
            $suggestion->plateforme="Mataccueil";
            $suggestion->emailEmetteur=$email;
            $suggestion->idEntite=$request->idEntite;
            $suggestion->structureEmetteur=$structure;
            $suggestion->emailRecepteur=$emailRecepteur;
            $suggestion->save();
        } catch (\Illuminate\Database\QueryException $ex) {
            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
            return $error;
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage());
            $error =  array("status" => "error", "message" =>"Une erreur est survenue au cours de l'enregistrement. Contactez l'administrateur.");
            return $error;
        }
     }




     //transmettre une requete de lutilisateur a la direction
     public function transmettreRequete(Request $request)
     {
         try {
             $inputArray = $request->all();
             //verifie les champs fournis
      if (!(isset($inputArray['idRequete']))) { //controle d existence
        return array("status" => "error",
          "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
      }
             $idRequete=0;
             if (isset($inputArray['idRequete'])) {
                 $idRequete= $inputArray['idRequete'];
             }

             //if(isset($inputArray['idEtape'])) $idEtape= $inputArray['idEtape'];

             $typeStructure='Direction';

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
             if ($requeteObj === null) {
                 return array("status" => "error", "message" => "Requête n'est existe pas");
             }

             //get fields for requete
             $idPrestation = $requeteObj->idPrestation;
             $plainte = $requeteObj->plainte;
             $code = $requeteObj->code;

             //rendre visible la requete a present
             $requeteObj->visible = 1;

             $requeteObj->save();

             //email usager
             if($requeteObj->idUsager == Null || !$requeteObj->idUsager){
                 $emailUsager = $requeteObj->email;
                 $nom = $requeteObj->contact;
             }else{
                 $emailUsager = $requeteObj->usager->email;
                 $nom = $requeteObj->usager->nom . " " . $requeteObj->usager->prenoms;
             }


             // Enregistrement dans la table affectation
             $service=Service::find($idPrestation);
             if ($service->hide_for_public==1) {
                 $typeStructure="SRU";
             }
             $affect=new Affectation;
             $affect->typeStructure=$typeStructure;
             $affect->idRequete=$requeteObj->id;
             $affect->idEntite=$request->idEntite;
             $affect->idStructure = $service->idParent;
             $affect->dateAffectation=date("Y-m-d h:m:i");
             $affect->save();

             //Notification à la structure
             $getstructure=Structure::find($service->idParent);

             if ($plainte==1) {
                 $typeRequete="plainte";
             } else {
                 $typeRequete="demande d'information";
             }

             if ($getstructure !== null) {                       ///count($getstructure)>0)
                 $emailstructure=$getstructure->contact;

                 if ($emailstructure!="") {
                     RequeteController::sendmail($emailstructure, "Une préoccupation a été adressée à votre structure ($getstructure->libelle) par $nom. Pour y répondre rendez-vous sur la plateforme à l'adresse : https://mataccueil.gouv.bj/login.", "MTFP : Service usager");
                 }
             }
             //Enregistrement parcours
             $check=Parcoursrequete::where("typeStructure", "=", $typeStructure)->where("idRequete", "=", $requeteObj->id)->get();

             if (count($check)==0) {
                 $parcours=new Parcoursrequete;
                 $parcours->typeStructure=$typeStructure;
                 $parcours->idRequete=$requeteObj->id;
                 $parcours->idEntite=$request->idEntite;
                 $parcours->idStructure=$service->idParent;
                 $parcours->idEtape=1;
                 $parcours->dateArrivee=date("Y-m-d h:m:i");
                 $parcours->save();
             }
      

             //Envoie mail
            if ($emailUsager!="") {
                RequeteController::sendmail($emailUsager, "Votre préoccupation a été enregistrée. Vous recevrez notre réponse au plus tôt. Le code de votre préoccupation est: $code . Rendez-vous sur le lien https://demarchesmtfp.gouv.bj/ pour consulter le parcours du traitement de votre requête.", "MTFP : Service usager");
            }

             return array("status" => "success", "message" => "");
         } catch (\Illuminate\Database\QueryException $ex) {
             \Log::error($ex->getMessage());

             $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
         } catch (\Exception $ex) {
             \Log::error($ex->getMessage());
             $error =  array("status" => "error", "message" =>"Une erreur est survenue au cours de l'enregistrement. Contactez l'administrateur.");
             return $error;
         }
     }//end transmettreRequete

     // questions / suggestions de l'usager
     public function DownloaFile(Request $request){
        //  return response()->json($request->get('file'));
        //  dd(Storage::path("public/Usager-mail/"));
        return response()->download(Storage::path("public/Usager-mail/").$request->get('file'));
     }
     public function transmettreQuestion(\Illuminate\Http\Request $request)
     {
         try {
             $inputArray =$request->all();
            
             //verifie les champs fournis
          if (!(isset($inputArray['objet']) && isset($inputArray['nom']) && isset($inputArray['prenoms']) && isset($inputArray['email']) && isset($inputArray['questions']))) { //controle d existence
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
        "\n"."Prénoms : "."".$prenoms."\n Email : "."".$email.
        "\n Objet : "."".$objet.
        "\n"."Questions / Suggestions : "."".$questions."\n";


             if ($emailRecepteur!="") {
                 $this->sendmail($emailRecepteur, $mailtosend, "PDA (Question / Suggestion) - Service Relations Usagers");
             }

          
             $suggestion=new Suggestion;
             $suggestion->message=$questions;
             $suggestion->nomEmetteur= $prenoms." ".$nom;
             $suggestion->plateforme="PDA";
             $suggestion->emailEmetteur=$email;
             $suggestion->idEntite=1;
             $suggestion->structureEmetteur="IHM PDA";
             $suggestion->emailRecepteur=$emailRecepteur;
             $suggestion->save();
  

             return array("success" => true);

         } catch (\Illuminate\Database\QueryException $ex) {
             \Log::error($ex->getMessage());

             $error = array("error"=>$ex->getMessage(),"status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
             return $error;
         } catch (\Exception $ex) {
             \Log::error($ex->getMessage());
             $error =  array("error"=>$ex->getMessage(),"status" => "error", "message" =>"Une erreur est survenue au cours de l'enregistrement. Contactez l'administrateur.");
             return $error;
         }
     }



     public static function sendmail($email, $text="Enregistrement de votre requête", $sujet="PDA (MatAccueil) - Service Relations Usagers")
     {
         $senderEmail = 'mtfp.usager@gouv.bj'; // 'travail.infos@gouv.bj';
         $email=trim($email);
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){ //Vérifier si c'est un bon mail...
            
            Mail::raw($text, function ($message) use ($email, $text, $sujet, $senderEmail) {
                $message->from($senderEmail, 'PDA');
                $message->to($email);
                $message->subject($sujet);
            });
        }
     }

    public static function sendmailPieceJointe($email, $text="Enregistrement de votre requête", $sujet="PDA (MatAccueil) - Service Relations Usagers",$pj="") {

        $email=trim($email);
        $senderEmail = 'mtfp.usager@gouv.bj'; // 'travail.infos@gouv.bj';
        Mail::raw($text, function ($message) use ($email, $sujet,$pj,$senderEmail) {
            $message->from($senderEmail, 'PDA');
           // if($pj){
                $message->attach($pj, [
                    'as' => "Pieces_Jointes",
                    //'mime' => 'application/octet-stream',
                ]);
           //}
            $message->to($email);
            $message->subject($sujet);
        });
    }
     /*public static function sendmail($email,$text="Enregistrement de votre requête",$sujet="PDA (MatAccueil) - Service Relations Usagers"){

       $senderEmail = 'travail.infos@gouv.bj';
       Mail::raw($text, function ($message) use ($email,$text,$sujet, $senderEmail) {
         $message->from($senderEmail, 'PDA');
         $message->to($email);
         $message->subject($sujet);
       });
     }*/


     //send file for upload
     public static function envoiFichier()
     {
         try {
             //enregistrer le fichier sur le serveur
             $extension  = "";
             $fileName = "";
             if (Input::hasFile('file')) {
                 $file =$request->file('file');
                 $extension = $file->getClientOriginalExtension();
                 $fileName = 'REQ_'. time().'_'.mt_rand(1000, 1000000).'.'.$extension;
                 $pathName = ParamsFactory::getRequestsPath($extension) .'/'. $fileName;
                 Storage::disk('local')->put($pathName, File::get($file));
             }
             $input = $request->all();

             $requeteObj = Requete::find($input["id"]);
             $requeteObj->fichier_joint = $fileName;
             $requeteObj->save();

             return array("status" => "success", "message" => "", "data" => $fileName);
         } catch (\Illuminate\Database\QueryException $ex) {
             $error = array("status" => "error", "message" => "Une erreur est survenue lors du chargement du fichier de publication. Veuillez contactez l'administrateur" );
             \Log::error($ex->getMessage());
             return $error;
         } catch (\Exception $ex) {
             $error = array("status" => "error", "message" => "Une erreur est survenue lors du chargement du fichier de publication. Veuillez contactez l'administrateur" );
             \Log::error($ex->getMessage());
             return $error;
         }
     }//end envoiFichier


     public function downloadDataToPDF(\Illuminate\Http\Request $request)
     {
         if ($request->period==0) {
             $last_date=date_create($request->date_start)->modify("-1 day")->format("Y-m-d");
             $requetes=Requete::where("idUsager", "91586")->whereBetween("created_at", [$last_date." 19:00:00",$request->date_start." 13:29:59"])->orderBy("id", "desc")->get();
         } elseif ($request->period==1) {
             $requetes=Requete::where("idUsager", "91586")->whereBetween("created_at", [$request->date_start." 13:30:00",$request->date_start." 18:59:59"])->orderBy("id", "desc")->get();
         }

         $time=$request->period==0?"13h30":"19h";
         $data=array();
         $data["date_start"]=$request->date_start;
         $data["data"]=$requetes;

         view()->share('data', $data);
         $pdf = PDF::loadView('download', $requetes);
         $pdf->setPaper('A4', 'landscape');
//        return $pdf->download('point_doléances_'.$request->date_start."_".$time.'.pdf');
         return  $pdf->stream('point_doléances_'.$request->date_start."_".$time.'.pdf');
     }
 }
