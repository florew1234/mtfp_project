<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Models\Structure;
use App\Models\Requete;
use App\Models\Institution;
use App\Models\Type;
use App\Models\Service;
use App\Models\Parcoursrequete;
use App\Models\Affectation;
use App\Http\Controllers\RequeteController;
use DB;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class IntranetController extends Controller
{
    //

    public function index(){

        $headers = request()->header();
        if(array_key_exists("authorization",$headers)){
            $userAuth=explode(" ",request()->header()["authorization"][0])[1];
            $userAuthCredentials=explode(":",base64_decode($userAuth));
            $data['email'] = $userAuthCredentials[0];
            $data['password'] = $userAuthCredentials[1];
            $logChec = auth()->attempt($data);

            
            if($logChec != false){
                //Le nombre de plaintes en attente de traitement pour la structure de l'user connecté
                // $user = User::with('agent','profile','agent.uniteAdmin.entiteAdmin')->where('email',auth()->user()->email)->first();
                
                //Plaintes Du début jusqu'à la date du jour 
                $structures = Structure::where("idParent",0)->get()->first(); //A recupere dans le Auth
                $structure_id = $structures->id;

                $Plainte_Encours=Requete::whereHas("affectation",function($req) use ($structure_id){
                                        return $req->where('idStructure',$structure_id);
                                    })->where('traiteOuiNon',0)->where('horsDelai',1)->count();

                $Inform_Encours=Requete::whereHas("affectation",function($req) use ($structure_id){
                                        return $req->where('idStructure',$structure_id);
                                    })->where('traiteOuiNon',0)->count();
              //  return response()->json($user);

                
                return response()->json([
                    // $userAuth,
                    // $userAuthCredentials,
                    "Nombre de plainte : ".$Plainte_Encours,
                    "Nombre d'information : ".$Inform_Encours,
                ]);
            }
            return response()->json('Indentifiant incorrect',401);
        }

        return response()->json('Bad request',400);
        

    }

    // INTEROPERABILITE ENTRE SYGEC ET MATTACCUEIL

    public function listructure()
	{
        $giwu = self::Check_Auth();
        if($giwu == "ok"){
            $int = Institution::get();
            return response()->json($int);
        }else{
            return $giwu;
        }
	}

    public function listype($ident)
	{
        $giwu = self::Check_Auth();
        if($giwu == "ok"){
            $TypeSearch = Type::where('idEntite',$ident)->orderBy("libelle","ASC")->get();
            return response()->json($TypeSearch);
        }else{
            return $giwu;
        }
	}

    public function listprest($type){

        $giwu = self::Check_Auth();
        if($giwu == "ok"){
            $PrestationSearch = Service::where('idType',"=",$type)->select('id','libelle','nbreJours','idParent','idType')->orderBy('libelle','asc')->get();
            return response()->json($PrestationSearch);
        }else{
            return $giwu;
        }
	}
    

    public function AddRequete(Request $request)
    {
        $giwu = self::Check_Auth();
        if($giwu == "ok"){
            try {
                $inputArray = $request->all();
                //verifie les champs fournis visible
                // idEntite : Structure destinatrice 
                // idPrestation : Les prestations 
                // objet : L'objet de la requete
                // msgrequest : Zone de texte mesage
                // nom : Nom de celui qui adresse la requête 
                // nbreJours : service.nbreJours 	
                // author_sygec : User qui envoi la requete depuis sygec
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
                $idEntite=0;
                if (isset($inputArray['idEntite'])) {
                    $idEntite= $inputArray['idEntite'];
                }
                $author_sygec =0;
                if (isset($inputArray['author_sygec'])) {
                    $author_sygec= $inputArray['author_sygec'];
                }
    
                $plainte=0; //0 = Requete
    
                $interfaceRequete='SRU';
                // if (isset($inputArray['interfaceRequete'])) {
                //     $interfaceRequete= $inputArray['interfaceRequete'];
                // }
               
                $plateforme='sygec';
                // if (isset($inputArray['plateforme'])) {
                //     $plateforme= $inputArray['plateforme'];
                // }
    
                $nom='';
                if (isset($inputArray['nom'])) {
                    $nom= $inputArray['nom'];
                }
                $email='';
                if (isset($inputArray['email'])) {
                    $email= $inputArray['email'];
                }
    
                $link_to_prestation=0; //Préoccupation liée à une prestation 1 = oui et 0 = non 
                // if (isset($inputArray['link_to_prestation'])) {
                //     $link_to_prestation= $inputArray['link_to_prestation'];
                // }
                $natureRequete=13; // Sygec
                // if (isset($inputArray['natureRequete'])) {
                //     $natureRequete= $inputArray['natureRequete'];
                // }
    
                $nbreJours='';
                if (isset($inputArray['nbreJours'])) {
                    $nbreJours= $inputArray['nbreJours'];
                }
    
                $idUser=0; //Affecter 
                $idd = User::where('email','sru@gouv.bj')->first();
                if($idd){
                    $idUser=$idd->id;
                }
                $visible=1; //requeter créer et transmise 
               
    
                $fichierJoint = "";
                // if (isset($inputArray['fichier_requete'])) {
                //     $fichierJoint = $inputArray['fichier_requete'];
                // }
    
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
                $requete->email=$email;
                $requete->idEntite=$idEntite;
                $requete->fichier_joint = $fichierJoint;
                $requete->created_by = $idUser;
                $requete->author_sygec = $author_sygec;
    
                // Enregistrement de l'usager s'il ne l'est pas encore
                // $checkusager=Usager::where("email", "=", $email)->get();
    
                // if ($checkusager!=null) {
                    // $requete->idUsager=$idUsager; // idUsager	= User qui a envoyé la requete depuis sygec
                // }
    
                $requete->save();
    
    
                // Enregistrement dans la table affectation
                $service=Service::find($idPrestation);
    
                if ($visible==1 || $visible=="1") {
                    $affect=new Affectation;
                    $affect->typeStructure='Direction';
                    $affect->idRequete=$requete->id;
                    $affect->idEntite=$idEntite;
                    $affect->idStructure=$service->idParent;
                    $affect->dateAffectation=date("Y-m-d h:m:i");
                    $affect->save();
    
     
                    //Enregistrement parcours
                    $parcours=new Parcoursrequete;
                    $parcours->typeStructure='Direction';
                    $parcours->idRequete=$requete->id;
                    $parcours->idStructure=$service->idParent;
                    $parcours->idEntite=$idEntite;
                    $parcours->idEtape=1;
                    $parcours->dateArrivee=date("Y-m-d h:m:i");
                    $parcours->save();

                    //Notification à la structure
                    $getstructure=Structure::find($service->idParent);
                    if ($getstructure !== null) {                       ///count($getstructure)>0)
                        $emailstructure=$getstructure->contact;
                        if ($emailstructure!="") {
                            RequeteController::sendmail($emailstructure, "Une préoccupation (Objet : $requete->objet) a été adressée à votre structure ($getstructure->libelle) par $nom. Pour y répondre rendez-vous sur la plateforme à l'adresse : https://mataccueil.gouv.bj/login.", "MTFP : Service usager");
                        }
                    }
                }
                //retour
                return response()->json([
                    "status" => "200",
                    "message" => "Requête ajoutée avec succès",
                ]);
            } catch (\Illuminate\Database\QueryException $ex) {
                \Log::error($ex->getMessage());
                $error=array("error" =>$ex->getMessage(),"status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" );
                return response()->json($error);
            } catch (\Exception $ex) {
                \Log::error($ex->getMessage());
               // return response()->json($ex->getMessage());
                $error = array("error" =>$ex->getMessage(),"status" => "error", "message" => "Une erreur est survenue lors de l'enregistrement de votre requête. Veuillez contactez l'administrateur");
                return response()->json($error);
            }
        }else{
            return $giwu;
        }
    }

    public function listRequ($author){

        $giwu = self::Check_Auth();
        if($giwu == "ok"){
            $int = Requete::with(['entite','service'])->where('author_sygec',$author)->orderBy('id','desc')->get();
            return response()->json($int);
        }else{
            return $giwu;
        }
	}

    public function Check_Auth() {
        $headers = request()->header();
        if(array_key_exists("authorization",$headers)){
            $userAuth=explode(" ",request()->header()["authorization"][0])[1];
            $userAuthCredentials=explode(":",base64_decode($userAuth));
            $data['email'] = $userAuthCredentials[0];
            $data['password'] = $userAuthCredentials[1];
            $logChec = auth()->attempt($data);
            if($logChec != false){
                return "ok";
            }
            return response()->json('Indentifiant incorrect',401);
        }
        return response()->json('Bad request',400);
    }
}
