<?php
 namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Http\Controllers\AuthController;

use App\Models\Rdv;
use App\Models\Usager;
use App\Models\Service;
use App\Models\Structure;

use App\Models\Rdvcreneau;

use App\Models\Rdvparametre;


use App\Models\Requete;


use App\Helpers\Carbon\Carbon;
use Illuminate\Http\Request;

use DB;
use Mail;

class RdvController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => ['store','destroy','update','getRdvByUsager','createRdvExterne','saveStatut']]);
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
          
            if (isset($input['search'])) {
                $search = $input['search'];

                $items = Rdv::where('idEntite', $idEntite)->with(['usager','requete','daterdvs','rdvcreneau' => function ($query) {
                    return $query->orderBy('heureDebut', 'ASC');
                }])
            ->where("objet", "LIKE", "%{$search}%")
            ->orWhereHas('usager', function ($q) use ($search) {
                $q->where(DB::raw("CONCAT(`nom`, ' ', `prenoms`)"), "LIKE", "%{$search}%");
            })
            ->orWhereHas('rdvcreneau', function ($q) use ($search) {
                $q->where(DB::raw("CONCAT(`heureDebut`, ' ', `heureFin`)"), "LIKE", "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(10);
            } else {
                $items = Rdv::where('idEntite', $idEntite)->with(['usager','requete','structure', 'rdvcreneau'  => function ($query) {
                    return $query->orderBy('heureDebut', 'ASC');
                }])
            ->orderBy('id', 'desc')
            ->paginate(10);
            }

            return response($items);
        } catch (\Illuminate\Database\QueryException $ex) {
            $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" );
            \Log::error($ex->getMessage());
        } catch (\Exception $ex) {
            $error =  array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur" );
            \Log::error($ex->getMessage());
            return $error;
        }
    }




    public function getRdvByUsager($idUsager)
    {
        try {
            $items = Rdv::with(['usager','requete','structure','rdvcreneau'  => function ($query) {
                return $query->orderBy('heureDebut', 'ASC');
            }])
            ->where("idUsager", "=", $idUsager)
            ->orderBy('id', 'desc')
            ->get();

            return response($items);
        } catch (\Illuminate\Database\QueryException $ex) {
            $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" );
            \Log::error($ex->getMessage());
        } catch (\Exception $ex) {
            $error =  array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur" );
            \Log::error($ex->getMessage());
            return $error;
        }
    }
    public function getRdvByStructure($idStructure)
    {
        try {
            $items = Rdv::with(['usager','requete','structure','rdvcreneau'  => function ($query) {
                return $query->orderBy('heureDebut', 'ASC');
            }])
            ->where("idStructure", "=", $idStructure)
            ->orderBy('id', 'desc')
            ->paginate(10);

            return response($items);
        } catch (\Illuminate\Database\QueryException $ex) {
            $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" );
            \Log::error($ex->getMessage());
        } catch (\Exception $ex) {
            $error =  array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur" );
            \Log::error($ex->getMessage());
            return $error;
        }
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

    public function createRdvExterne(Request $request)
    {

            $usager=$this->createUsager($request);

            $inputArray =  $request->all();
            //verifie les champs fournis
            if (!(
                isset($inputArray['objet'])
            )) { 
                //controle d existence
               return array("status" => "error",
               "msg" => "L'objet est requis");
            }
           
            $idUsager= $usager->id;
           
            $objet= $inputArray['objet'];
           

            $idRdvCreneau= $inputArray['idRdvCreneau'];
           
            $dateRdv= $inputArray['dateRdv'];

            $statut=0;
            if (isset($inputArray['statut'])) {
                $statut= $inputArray['statut'];
            }

            $attente='';
            if (isset($inputArray['attente'])) {
                $attente= $inputArray['attente'];
            }


            $codeRequete= "0";

            $idStructure=$inputArray['idStructure'];
          

            $result = DB::Select("select count(*) as nbre from outilcollecte_rdv r where statut=0 and dateRdv=$dateRdv and idStructure=$idStructure Group by idRdvCreneau;");
            
            $RdvParametre = Rdvparametre::get();
            $nbrePoste= $RdvParametre[0]->nombrePoste;
            
            if (isset($result[0]->nbre)) {
                if ($nbrePoste<=$result[0]->nbre) {
                    
                   return array("status" => "error",
                   "msg" => "La date choisie est indisponible. Veuillez choisir une autre date");
                }
            }

            $check=Rdv::where("idUsager", "=", $idUsager)->where("dateRdv", "=", $dateRdv)->get();

            if (count($check)!=0) {
               return array("status" => "error",
               "msg" => "Vous avez déjà demandé un rendez-vous pour cette date");
            }

            $rdv= new Rdv;
            $rdv->idUsager=$idUsager;
            $rdv->objet=$objet;
            $rdv->idStructure=$idStructure;
            $rdv->idRdvCreneau=$idRdvCreneau;
            $rdv->idEntite=$request->idEntite;
            $rdv->statut=$statut;
            $rdv->attente=$attente;
            $rdv->codeRequete=$codeRequete;
            $rdv->dateRdv=$dateRdv;

            $rdv->save();
            
            //Notification à la structure
            $getstructure=Structure::find($idStructure);
            if ($getstructure !== null && $statut==1) {                       ///count($getstructure)>0)
                $emailstructure=$getstructure->contact;

                if ($emailstructure!="") {
                    RdvController::sendmail($emailstructure, "Une demande de rendez-vous vous été adressée à votre structure pour '".$objet."'. Pour y répondre rendez-vous sur la plateforme à l'adresse : https://demarchesmtfp.gouv.bj/mataccueil.", "MTFP : Service usager");
                }
            }

            return array("status" => "success",
            "msg" => "Demande de rendez-vous transmis avec succès");
        
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
            if (!(
                isset($inputArray['objet']) && isset($inputArray['idUsager'])
            )) { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }
           
            $idUsager= $inputArray['idUsager'];
           
            $objet= $inputArray['objet'];
           

            $idRdvCreneau= $inputArray['idRdvCreneau'];
           
            $dateRdv= $inputArray['dateRdv'];

            $statut=0;
            if (isset($inputArray['statut'])) {
                $statut= $inputArray['statut'];
            }

            $attente='';
            if (isset($inputArray['attente'])) {
                $attente= $inputArray['attente'];
            }


            $codeRequete= $inputArray['codeRequete'];

            if ($codeRequete!="0") {
                $requete=Requete::where('codeRequete', $codeRequete)->first();
                $service=Service::find($requete->idPrestation);
                $idStructure=$service->idParent;
            } else {
                $idStructure=$inputArray['idStructure'];
                ;
            }
          

            $result = DB::Select("select count(*) as nbre from outilcollecte_rdv r where statut=0 and dateRdv=$dateRdv and idStructure=$idStructure Group by idRdvCreneau;");
            
            $RdvParametre = Rdvparametre::get();
            $nbrePoste= $RdvParametre[0]->nombrePoste;
            
            if (isset($result[0]->nbre)) {
                if ($nbrePoste<=$result[0]->nbre) {
                    return array("status" => "error", "message" => "Plus de disponibilité pour cette date. Nous vous proposons de choisir la date suivante." );
                }
            }

            $check=Rdv::where("idUsager", "=", $idUsager)->where("dateRdv", "=", $dateRdv)->get();

            if (count($check)!=0) {
                return array("status" => "error", "message" => "Vous avez déjà pris un RDV pour le même jour." );
            }

            $rdv= new Rdv;
            $rdv->idUsager=$idUsager;
            $rdv->objet=$objet;
            $rdv->idStructure=$idStructure;
            $rdv->idRdvCreneau=$idRdvCreneau;
            $rdv->idEntite=$request->idEntite;
            $rdv->statut=$statut;
            $rdv->attente=$attente;
            $rdv->codeRequete=$codeRequete;
            $rdv->dateRdv=$dateRdv;

            $rdv->save();
            
            //Notification à la structure
            $getstructure=Structure::find($idStructure);
            if ($getstructure !== null && $statut==1) {                       ///count($getstructure)>0)
                $emailstructure=$getstructure->contact;

                if ($emailstructure!="") {
                    RdvController::sendmail($emailstructure, "Une demande de rendez-vous vous été adressée à votre structure pour '".$objet."'. Pour y répondre rendez-vous sur la plateforme à l'adresse : https://demarchesmtfp.gouv.bj/mataccueil.", "MTFP : Service usager");
                }
            }

            return array("status" => "success", "message" => "RDV enregistré avec succès." );
        } catch (\Illuminate\Database\QueryException $ex) {
            $error=array("error" =>$ex->getMessage(),"status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" );
            \Log::error($ex->getMessage());

            return $error;
        } catch (\Exception $ex) {
            $error =  array("error" =>$ex->getMessage(),"status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur" );
            \Log::error($ex->getMessage());
            return $error;
        }
    }
    /**
         * update a newly created resource in storage.

         *

         * @return Response

         */

    public function update($id, Request $request)
    {
        try {
            $inputArray =  $request->all();
            //verifie les champs fournis
            if (!(
                isset($inputArray['objet']) && isset($inputArray['id'])
            )) { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }
           
            $idUsager= $inputArray['idUsager'];
           
            $objet= $inputArray['objet'];
           
            $idRdvCreneau= $inputArray['idRdvCreneau'];
           
            $codeRequete= $inputArray['codeRequete'];
           
            $dateRdv= $inputArray['dateRdv'];

            $attente='';
            if (isset($inputArray['attente'])) {
                $attente= $inputArray['attente'];
            }

            $rdv=Rdv::find($id);
            $rdv->idUsager=$idUsager;
            $rdv->objet=$objet;
            $rdv->idRdvCreneau=$idRdvCreneau;
            $rdv->codeRequete=$codeRequete;
            $rdv->dateRdv=$dateRdv;

            $rdv->save();
            return $this->index($rdv->idEntite);
        } catch (\Illuminate\Database\QueryException $ex) {
            $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" );
            \Log::error($ex->getMessage());
        } catch (\Exception $ex) {
            $error =  array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur" );
            \Log::error($ex->getMessage());
            return $error;
        }
    }



    public function saveStatut(Request $request)
    {
        
        try {
            $inputArray =  $request->all();
            //verifie les champs fournis
            if (!(
                isset($inputArray['listerdv']) && isset($inputArray['statut'])
            )) { //controle d existence
                return array("status" => "error", "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }
           
            $listerdv= $inputArray['listerdv'];

            $statut= $inputArray['statut'];
           
            foreach ($listerdv as $oc) {
                $rdv=Rdv::find($oc);
                $rdv->statut=$statut;
                $rdv->save();

                $getstructure=Structure::find($rdv->idStructure);
                if ($getstructure !== null && $rdv->statut==1) {                       ///count($getstructure)>0)
                    $emailstructure=$getstructure->contact;
  
                    if ($emailstructure!="") {
                        RdvController::sendmail($emailstructure, "Une demande de rendez-vous vous été adressée à votre structure pour '".$objet."'. Pour y répondre rendez-vous sur la plateforme à l'adresse : https://demarchesmtfp.gouv.bj/mataccueil.", "MTFP : Service usager");
                    }
                } else {
                    $usager=Usager::find($rdv->idUsager);

                    if ($usager!=null && $rdv->statut==2) {
                        RdvController::sendmail($usager->email, "Votre demande de rendez-vous ayant pour objet '".$objet."' a été confirmée par la structure.", "MTFP : Service usager");
                    }
                    if ($usager!=null && $rdv->statut==3) {
                        RdvController::sendmail($usager->email, "Votre demande de rendez-vous ayant pour objet '".$objet."' a été rejetée par la structure.", "MTFP : Service usager");
                    }
                }
            }
            
            // if ($statut=="1") {
            // } else {
            // }
            //prévoir envoie de mail à l'utilisateur

            return array("status" => "success", "message" => "Statut mis à jour.");
        } catch (\Illuminate\Database\QueryException $ex) {
            $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" );
            \Log::error($ex->getMessage());
        } catch (\Exception $ex) {
            $error =  array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur" );
            \Log::error($ex->getMessage());
            return $error;
        }
    }
    /**
         * Remove the specified resource from storage.
         *
         * @param  int  id
         * @return Response
         */

    public function destroy($id)
    {
        Rdv::find($id)->delete();
        return array('success' => true );
    }

    public static function sendmail($email, $text="Rendez-vous usager", $sujet="PDA (MatAccueil) - Service Relations Usagers")
    {
        $email=trim($email);
        $senderEmail = 'mtfp.usager@gou.bj'; // 'travail.infos@gouv.bj';
        Mail::raw($text, function ($message) use ($email, $text, $sujet, $senderEmail) {
            $message->from($senderEmail, 'PDA');
            $message->to($email);
            $message->subject($sujet);
        });
    }
}
