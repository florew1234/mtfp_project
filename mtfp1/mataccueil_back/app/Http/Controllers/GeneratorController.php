<?php
    
namespace App\Http\Controllers;
use App\Helpers\Factory\ParamsFactory;

use Illuminate\Http\Request;

;
//use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;


use App\Http\Controllers\Controller;


use App\Http\Controllers\AuthController;


//use Request;

use App\Models\EntiteAdmin;

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

use App\Models\Type;
use App\Models\Departement;
use App\Models\Parametre;
use App\Helpers\Carbon\Carbon;

use Mail;

use DB;

use Dompdf\Dompdf;

use Tymon\JWTAuth\JWTAuth;

class GeneratorController extends Controller
{


    public function __construct() {
        //$this->user = JWTAuth::parseToken()->authenticate();

        $this->middleware('jwt.auth');
    }


    public function genererPDF(\Illuminate\Http\Request $request)
    {
     $inputArray =  $request->all();

        $idRequete=$inputArray["id"];

        //Récupération  de la requete
        $getrequete=Requete::with(['usager','service'])->find($idRequete);

        $dateRequete=$getrequete->created_at;

        $explodeDate=explode(' ',$dateRequete);

        $tableDate=explode('-',$explodeDate[0]);

        $date=$tableDate[2].'-'.$tableDate[1].'-'.$tableDate[0];


        $fileName =rand(1,100).'_fichierrequete'.rand(1,100).'.pdf';
        $getrequete->lien=$fileName;

        $getrequete->save();

        $pathrequete = 'FichiersRequetes/'.$idRequete.'/'.$fileName;



        //Récupération du département;
        $getdepartement=Departement::find($getrequete->usager->idDepartement);

        //Récupération de la thématique;
        $getservice=Service::with(['type','service_parent'])->find($getrequete->service->id);

        // Récupération de l
        //Détermination type requête
        $natureRequete="requête";
        if($getrequete->plainte==1)
          $natureRequete="plainte";

        /* Récupérer les élements nécessaires */
        $parametre=Parametre::find(1);

        $adresse=$parametre->adresse;
        $adresseServeur=$parametre->adresseServeur;
        $adresseServeurFichier=$parametre->adresseServeurFichier;
        $logo=$parametre->logo;

        $pathimage=$logo;

        $contenuPDF="";
        $contentPDF="";



        $divLogo="<div style='float:left;'><img src='".$pathimage."' width='250px'></div>";
        $divAdresse="<div style='float:right;text-align:right;font-family:arial;'>".$adresse."</div>";

        $divHeader="<div id='header' style='margin-bottom:30px;float:left;width:100%;'><div>".$divLogo.$divAdresse."</div></div><br><br><br><br>";

        $contenuPDF.="<div  style='text-align:center;font-weight:bold;'>« Jeudis de la Fonction Publique »</div><br><br>";
        
        $contenuPDF.="<div  style='text-align:center;font-weight:bold;'>Fiche à éditer après enregistrement de la requête ou de la plainte de l’usager</div><br><br>";
        
        $contenuPDF.="<div  style='font-weight:bold;'>
        <span>Date enregistrement:</span>".$date."</div><br><br>";
        
        
        $contenuPDF.="<div><b>Nom et prénoms de l'usager:</b> ".$getrequete->usager->nom.' '.$getrequete->usager->prenoms."</div><br><br>";

        $contenuPDF.="<div><b>Email de l'usager:</b> ".$getrequete->usager->email."</div><br><br>";

        $contenuPDF.="<div><b>Email de l'usager:</b> ".$getrequete->usager->tel."</div><br><br>";

        $contenuPDF.="<div><b>Département de l'usager:</b> ".$getdepartement->libelle."</div><br><br>";

        $contenuPDF.="<div><b>Nature de la requête:</b> ".$natureRequete."</div><br><br>";

        $contenuPDF.="<div><b>Thématique concernée:</b> ".$getservice->type->libelle."</div><br><br>";

        $contenuPDF.="<div><b>Prestation concernée:</b> ".$getrequete->service->libelle."</div><br><br>";

        $contenuPDF.="<div><b>Objet de la $natureRequete:</b> ".$getrequete->objet."</div><br><br>";

        $contenuPDF.="<div><b>Détails de la $natureRequete:</b> ".$getrequete->msgrequest."</div><br><br>";

        $contenuPDF.="<div><b>Direction concernée :</b> ".$getservice->service_parent->libelle."</div><br><br>";

        $contenuPDF.="<table style='border:0px;' width='100%'><tr>";
        $contenuPDF.="<td  style='border:0px;font-weight:bold; padding-left:30px;'>Visa de l’usager</td>";
        $contenuPDF.="<td  style='border:0px;font-weight:bold;'>Visa du réceptionnaire</td>";
        $contenuPDF.="</tr>";

        //Récupérer l'utilisateur connecté
        $userconnect = new AuthController;
        $userconnectdata = $userconnect->user_data_by_token($request->token);

        $getAgent=Acteur::find($userconnectdata->idagent);

        $contenuPDF.="<tr>";
        $contenuPDF.="<td  style='border:0px;font-weight:bold;' width='50%'><br><br><br>".$getrequete->usager->nom.' '.$getrequete->usager->prenoms."</td>";
        $contenuPDF.="<td  style='border:0px;font-weight:bold;padding-left:30px;'><br><br><br>".$getAgent->nomprenoms."</td>";
        $contenuPDF.="</tr></table>";

        $contentPDF.=$divHeader."<div id='content' style='font-family:arial;width:100% !important;max-width:100% !important;margin-right:20px !important;'><br><br>".$contenuPDF."</div>";

        
        //$dompdf = new Dompdf();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($contentPDF);

        // (Optional) Setup the paper size and orientation
        $dompdf->set_option('isRemoteEnabled', TRUE);
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
       $output = $dompdf->output(['isRemoteEnabled' => true]);
       //file_put_contents($pathcourrier, $output);

        Storage::disk('public')->put($pathrequete,$output);


return array("status" => "success", "message" => "Enregistrement effectué avec succès.","url" => $fileName );
       
    }  


    public function genererPDFStat(\Illuminate\Http\Request $request)
    {
     $inputArray =  $request->all();

        $stats=$inputArray["stats"];
        $startDate=$inputArray["startDate"];
        $endDate=$inputArray["endDate"];


        $periodeString="";
        if($stats!='all' && $endDate!='all')
        {
            $startDate = ParamsFactory::convertToDateTimeForSearch($startDate, true);
            $startDate = $startDate->format('d-m-Y');    //->getTimestamp();

            $endDate = ParamsFactory::convertToDateTimeForSearch($endDate, false);
            $endDate = $endDate->format('d-m-Y'); //->getTimestamp();

            $periodeString=" pour la période du $startDate au $endDate";
        }


        $fileName =rand(1,100).'statreq'.rand(1,100).'.pdf';

        $pathrequete = 'statistiques/'.$fileName;



        /* Récupérer les élements nécessaires */
        $parametre=Parametre::find(1);

        $adresse=$parametre->adresse;
        $adresseServeur=$parametre->adresseServeur;
        $adresseServeurFichier=$parametre->adresseServeurFichier;
        $logo=$parametre->logo;

       // $pathimage=$logo;
        $pathimage="data:image/png;base64,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";

        $contenuPDF="";
        $contentPDF="";

        $entiteName=EntiteAdmin::find($request->entiteId)?->libelle;

        $divLogo="<div style='float:left;'><img src='".$pathimage."' width='190' height='80'></div>";
        $divAdresse="<div style='float:right;text-align:right;font-family:arial;'>".$adresse."</div>";

        $divHeader="<div id='header' style='margin-bottom:30px;float:left;width:100%;'><div>".$divLogo.$divAdresse."</div></div><br><br><br><br>";

        
        $contenuPDF.="<div  style='font-weight:bold; text-align:center'>$entiteName</div><br><br>";
        $contenuPDF.="<div  style='font-weight:bold; text-align:center'>Statistiques par prestation $periodeString</div><br><br>";
        

        $contenuPDF.="
        <style>
        td {
            padding:5px;
            border:1px solid #ccc;
        }

        .tdnumber{
            text-align:right;
        }
        </style>
        <table style='border:0px; border-collapse: collapse;' width='100%'>
        <tr>";
        $contenuPDF.="<td  style='font-weight:bold;'>Prestation</td>";
        $contenuPDF.="<td  style='font-weight:bold;'>Nombre de requêtes</td>";
        $contenuPDF.="<td  style='font-weight:bold;'>Nombre de plaintes</td>";
        $contenuPDF.="<td  style='font-weight:bold;'>Nombre de demandes d'informations</td>";
        $contenuPDF.="<td  style='font-weight:bold;'>Totale Préoccupation</td>";
        $contenuPDF.="<td  style='font-weight:bold;'>Nombre de notation</td>";
        $contenuPDF.="<td  style='font-weight:bold;'>Note totale reçue</td>";
        $contenuPDF.="<td  style='font-weight:bold;'>% de satisfaction</td>";
        $contenuPDF.="</tr>";

        //Récupérer l'utilisateur connecté
        $userconnect = new AuthController;
        $userconnectdata = $userconnect->user_data_by_token($request->token);

        $getAgent=Acteur::find($userconnectdata->idagent);

        foreach ($stats as $stat) {
            $contenuPDF.="<tr>";
            $contenuPDF.="<td>".$stat["libelle"]."</td>";
            $contenuPDF.="<td class='tdnumber'>".$stat["totalRequete"]."</td>";
            $contenuPDF.="<td class='tdnumber'>".$stat["totalPlainte"]."</td>";
            $contenuPDF.="<td class='tdnumber'>".$stat["totalInfo"]."</td>";
            $contenuPDF.="<td class='tdnumber'>".$stat["total"]."</td>";
            $contenuPDF.="<td class='tdnumber'>".$stat["notation"]."</td>";
            $contenuPDF.="<td class='tdnumber'>".$stat["noteRecu"]."</td>";
            $contenuPDF.="<td class='tdnumber'>".number_format((float)$stat["pourcent"],2)."%</td>";
            $contenuPDF.="</tr>";

        }

       
        $contenuPDF.="</table>";

        $contentPDF.=$divHeader."<div id='content' style='font-family:arial;width:100% !important;max-width:100% !important;margin-right:20px !important;'><br><br>".$contenuPDF."</div>";

        
        //$dompdf = new Dompdf();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($contentPDF);

        // (Optional) Setup the paper size and orientation
        $dompdf->set_option('isRemoteEnabled', TRUE);
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
       $output = $dompdf->output(['isRemoteEnabled' => true]);
       //file_put_contents($pathcourrier, $output);

        Storage::disk('public')->put($pathrequete,$output);


       return ["status" => "success", "message" => "Enregistrement effectué avec succès.","url" => $fileName ];
        
       
    }  


    public function genererPDFStatHebdo(\Illuminate\Http\Request $request)
    {
        try {
            $inputArray =  $request->all();

            $stats=$inputArray["stats"];
            $typeRequete=$inputArray["typeRequete"];
            $typeStat=$inputArray["typeStat"];
            $startDate=$inputArray["startDate"];
            $endDate=$inputArray["endDate"];
            $sended=$inputArray["sended"];
    
    
            $periodeString="";
            if($stats!='all' && $endDate!='all')
            {
                $startDate = ParamsFactory::convertToDateTimeForSearch($startDate, true);
                $startDate = $startDate->format('d-m-Y');    //->getTimestamp();
    
                $endDate = ParamsFactory::convertToDateTimeForSearch($endDate, false);
                $endDate = $endDate->format('d-m-Y'); //->getTimestamp();
    
                $periodeString=" pour la période du $startDate au $endDate";
            }
    
    
            $fileName =rand(1,100).'stat_hebdo_'.$typeRequete.'_'.rand(1,100).'.pdf';
    
            $pathrequete = 'statistiques/'.$fileName;
    
    
    
            /* Récupérer les élements nécessaires */
            $parametre=Parametre::find(1);
    
            $adresse=$parametre->adresse;
            $adresseServeur=$parametre->adresseServeur;
            $adresseServeurFichier=$parametre->adresseServeurFichier;
            $logo=$parametre->logo;
    
            $pathimage=$logo;
    
            $contenuPDF="";
            $contentPDF="";
    
    
    
            $divLogo="<div style='float:left;'><img src='".$pathimage."' width='250px'></div>";
            $divAdresse="<div style='float:right;text-align:right;font-family:arial;'>".$adresse."</div>";
    
            $divHeader="<div id='header' style='margin-bottom:30px;float:left;width:100%;'><div>".$divLogo.$divAdresse."</div></div><br><br><br><br>";
    
            
            $contenuPDF.="<div  style='font-weight:bold; align='center'>Statistiques ".$typeRequete." par ".$typeStat."s $periodeString</div><br><br>";
            
    
            $contenuPDF.="
            <style>
            td {
                padding:10px;
                border:1px solid #ccc;
            }
    
            .tdnumber{
                text-align:right;
            }
            </style>
            <table style='border:0px; border-collapse: collapse;' width='100%'>
            <tr>";
            $contenuPDF.="<td  style='font-weight:bold;'>".$typeStat."</td>";
            $contenuPDF.="<td  style='font-weight:bold;'>".$typeRequete." reçues</td>";
            $contenuPDF.="<td  style='font-weight:bold;'>".$typeRequete." en cours</td>";
            $contenuPDF.="<td  style='font-weight:bold;'>".$typeRequete." en cours hors délai</td>";
            $contenuPDF.="<td  style='font-weight:bold;'>".$typeRequete." traitées</td>";
            $contenuPDF.="<td  style='font-weight:bold;'>".$typeRequete." traitées hors délai</td>";
            $contenuPDF.="<td  style='font-weight:bold;'>".$typeRequete." à traitées (- 24H)</td>";
            $contenuPDF.="<td  style='font-weight:bold;'>Avis positif</td>";
            $contenuPDF.="</tr>";
    
            //Récupérer l'utilisateur connecté
            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
    
            
            $getAgent=Acteur::find($userconnectdata->idagent);
    
            $total=0;
            $totalEnCours=0;
            $totalEnCoursHorsDelai=0;
            $totalTraite=0;
            $totalTraiteHorsDelai=0;
            $totalEnCoursDelaiDans24H=0;
            foreach ($stats as $stat) {
                
                if(is_int($stat["total"])){
                    $total+=(int)$stat["total"];
                }
                if(is_int((int)$stat["totalEnCours"])){
                    $totalEnCours+=(int)$stat["totalEnCours"];
                }
                if(is_int((int)$stat["totalEnCoursHorsDelai"])){
                    $totalEnCoursHorsDelai+=(int)$stat["totalEnCoursHorsDelai"];
                }
                if(is_int((int)$stat["totalTraite"])){
                    $totalTraite+=(int)$stat["totalTraite"];
                }
                if(is_int((int)$stat["totalTraiteHorsDelai"])){
                    $totalTraiteHorsDelai+=(int)$stat["totalTraiteHorsDelai"];
                }
                if(is_int((int)$stat["totalEnCoursDelaiDans24H"])){
                    $totalEnCoursDelaiDans24H+=(int)$stat["totalEnCoursDelaiDans24H"];
                }
                $contenuPDF.="<tr>";
                $contenuPDF.="<td>".$stat["libelle"]."</td>";
                $contenuPDF.="<td class='tdnumber'>".$stat["total"]."</td>";
                $contenuPDF.="<td class='tdnumber'>".$stat["totalEnCours"]."</td>";
                $contenuPDF.="<td class='tdnumber'>".$stat["totalEnCoursHorsDelai"]."</td>";
                $contenuPDF.="<td class='tdnumber'>".$stat["totalTraite"]."</td>";
                $contenuPDF.="<td class='tdnumber'>".$stat["totalTraiteHorsDelai"]."</td>";
                $contenuPDF.="<td class='tdnumber'>".$stat["totalEnCoursDelaiDans24H"]."</td>";
                if($stat['totalRetour']>0){
                    $contenuPDF.="<td class='tdnumber'>".($stat["totalRetourPositif"]/$stat['totalRetour'])."</td>";
                }else{
                    $contenuPDF.="<td class='tdnumber'></td>";
                }
                $contenuPDF.="</tr>";
            }
            $contenuPDF.="<tr>";
            $contenuPDF.="<td  style='font-weight:bold;'>Total </td>";
            $contenuPDF.="<td  style='font-weight:bold;'>".$total."</td>";
            $contenuPDF.="<td  style='font-weight:bold;'>".$totalEnCours."</td>";
            $contenuPDF.="<td  style='font-weight:bold;'>".$totalEnCoursHorsDelai."</td>";
            $contenuPDF.="<td  style='font-weight:bold;'>".$totalTraite."</td>";
            $contenuPDF.="<td  style='font-weight:bold;'>".$totalTraiteHorsDelai."</td>";
            $contenuPDF.="<td  style='font-weight:bold;'>".$totalEnCoursDelaiDans24H." </td>";
            $contenuPDF.="<td  style='font-weight:bold;'>-</td>";
            $contenuPDF.="</tr>";
           
            $contenuPDF.="</table>";
    
            $contentPDF.=$divHeader."<div id='content' style='font-family:arial;width:100% !important;max-width:100% !important;margin-right:20px !important;'><br><br>".$contenuPDF."</div>";
    
           
            
            //$dompdf = new Dompdf();
            $dompdf = new Dompdf();
            $dompdf->loadHtml($contentPDF);
    
            // (Optional) Setup the paper size and orientation
            $dompdf->set_option('isRemoteEnabled', TRUE);
            $dompdf->setPaper('A4', 'landscape');
    
            // Render the HTML as PDF
            $dompdf->render();
    
            // Output the generated PDF to Browser
           $output = $dompdf->output(['isRemoteEnabled' => true]);
           //file_put_contents($pathcourrier, $output);
    

            Storage::disk('public')->put($pathrequete,$output);
            
            if($sended==1 || $sended==true){
                $strutures = Structure::all();
                foreach ($strutures as $item) {
                    GeneratorController::sendmail($item->contact,"Bilan des ".$typeRequete." adressée à votre structure par les usagers. Votre structure est donc priée de traiter toutes les ".$typeRequete." en instance dont vous avez la charge. Nous vous prions de ne pas répondre à ce mail .","MTFP : Service usager | Bilan Hebdo",
                        $pathrequete);
    
                }
    
            }
            
            return ["status" => "success", "message" => "Enregistrement effectué avec succès.","url" => $fileName ];
            
           
        } catch(\Illuminate\Database\QueryException $ex){
            \Log::error($ex->getMessage());
  
                $error=["error"=>$ex->getMessage(),"status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" ];
                return $error;
        }
        
    }  

    public static function sendmail($email,$text="Enregistrement de votre requête",$sujet="PDA (MatAccueil) - Service Relations Usagers",$pathToFile){

        $senderEmail = 'travail.infos@gouv.bj';
        Mail::raw($text, function ($message) use ($email,$text,$sujet, $senderEmail,$pathToFile) {
          $message->from($senderEmail, 'PDA');
          $message->to($email);
          $message->subject($sujet);
          $message->attach($pathToFile);
      });
    }
 }