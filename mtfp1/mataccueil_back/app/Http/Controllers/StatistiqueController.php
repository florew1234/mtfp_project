<?php
 namespace App\Http\Controllers;
use App\Helpers\Factory\ParamsFactory;

use Illuminate\Http\Request;

//use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;


use App\Http\Controllers\Controller;


use App\Http\Controllers\AuthController;
use App\Http\Controllers\RequeteController;

//use Request;

use App\Models\Requete;

use App\Models\Usager;
use App\Models\Commune;

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
use App\Models\Etapecourrier;
use App\Models\Departement;
use App\Models\Registre;
use App\Models\Parametre;
use App\Helpers\Carbon\Carbon;

use Illuminate\Support\Facades\DB;

use Mail;

use PDF;

use Dompdf\Dompdf;
use Illuminate\Support\Facades\DB as FacadesDB;
use Tymon\JWTAuth\JWTAuth;

class StatistiqueController extends Controller
{

  public function __construct() {
      //$this->user = JWTAuth::parseToken()->authenticate();

      $this->middleware('jwt.auth', ['except' => ['getNbrebyPrestation','getStatbyStructure','print','printregistre','printregistreStat']]);
  }

  public function print(Request $request){   

    
    $dataReq=array();
    $i=0;
    $resultat =array();
    // return response($request->all());
    $search = $request->get('se');
    $dateDebut = $request->get('db');
    $dateFin = $request->get('df');
    $idEntite = $request->get('ie');
    $user = $request->get('u');
    $plainte = $request->get('pdir');
    $statut = $request->get('s');
    $idStructure = $request->get('is');
    
    if (isset($search)) {
        $search=strtoupper(trim($input['search']));
        $query = Requete::with(['usager','entite_receive','reponses_rapide','entite','service','service.type','nature','notes', 'etape','reponse' => function ($query) {
            return $query->orderBy('id', 'DESC');
        },'affectation','parcours','parcours.etape','parcours.structure'])
        ->where("visible", "=", true)
        ->where("reponseStructure", "=", null)
    
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
    }else{
        $query = Requete::with(['usager','entite_receive','entite','reponses_rapide','service','service.type','nature','etape','notes','reponse' => function ($query) {
            return $query->orderBy('id', 'DESC');
        },'affectation','parcours','parcours.etape','parcours.structure']);
    }
    // if (isset($input['byUser'])) {
    //     $query = $query->where("created_by", "=", $idUser);
    // }
    
    $infoSta = "";
    if (isset($statut)) {
      $query=$query->where("traiteOuiNon", "=", $statut);
      if($statut == 1){
        $infoSta = "Statut : Finalisé";
      }else if($statut == 0){
        $infoSta = "Statut : En cours";
      }
    }
    
    $infoPlai = "";
    if (isset($plainte)) {
        $query=$query->where("plainte", "=", $plainte);
        if($plainte == 0){
          $infoPlai = "Requêtes";
        }else if($plainte == 1){
          $infoPlai = "Plaintes";
        }else if($plainte == 2){
          $infoPlai = "Demandes d'information";
        }
    }
    $infDate = "";
    if (isset($dateDebut) && isset($dateFin)) {
        $query=$query->whereDate('dateRequete', '>=', $dateDebut)
                ->whereDate('dateRequete', '<=', $dateFin);
        $infDate = "Du ".date('d/m/Y',strtotime($dateDebut))." au ".date('d/m/Y',strtotime($dateFin));
    }
    if (isset($idStructure)) {
        $query = $query->whereHas('affectation', function ($q) use ($idStructure) {
            $q->where('idStructure', "=", $idStructure);
        });
    }
    $query  = $query->orderBy('id', 'DESC')->get();
    // return response(count($query));
    if($query){
      foreach ($query as $que) {
        $dataReq[$i]["dateEnre"]=$que->created_at->format('d/m/Y H:i:s');
        if($que->service){
          $dataReq[$i]["prestation"]=$que->service->libelle;
        }else{
          $dataReq[$i]["prestation"]="-";
        } 
        $dataReq[$i]["objet"]=$que->objet;
        if($que->finalise == 1){
          $dataReq[$i]["statut"]= 'Finalisé';
        }else{
          $dataReq[$i]["statut"]= 'En cours';
        }
        $parcours = $que->parcours;
        $recuPars = "";
        if($parcours){
          $y=0;
          foreach($parcours as $par){
            $y++;
            if($par->etape){
              $libEtap = $par->etape->LibelleEtape;
            }else{
              $libEtap = "-";
            }
            if($par->structure){
              $libStr = ' ('.$par->structure->sigle.')';
            }else{
              $libStr = " (-)";
            }
            $recuPars .= $y."- ".$libEtap.$libStr.'<br/>';
          }
        }
        $dataReq[$i]["parcours"]= $recuPars;
				$i++;
      }
    }
    $name="Filtre-".$infoPlai."-".date('Ymdhis');
    $titre = "Liste des ".$infoPlai." ".$infDate."<br>".$infoSta;
    $data=['data'=> $dataReq,'name'=> $name,'titre'=> $titre];
    $pdf = PDF::loadView('historique', $data)->setPaper('a4', 'landscape');
    return $pdf->download($name.'.pdf');
  }

  public function printregistre(Request $request){   

    $dataReq=array();
    // return response($request->all());
    $search = $request->get('se');
    $dateDebut = $request->get('db');
    $dateFin = $request->get('df');
    $idEntite = $request->get('ie');
    $user = $request->get('u');
    // $plainte = $request->get('pdir');
    $statut = $request->get('s');
    $comm = $request->get('ic');
    
    $result=array();
    if (isset($search)) {   
        // $search=$input['search'];
        $result = Registre::with(['creator','entite','creator.agent_user'])
                          ->orderBy('id', 'asc')
                          ->where("matri_telep", "LIKE", "%{$search}%")
                          ->orWhere("nom_prenom", "LIKE", "%{$search}%")
                          ->orWhere("contenu_visite", "LIKE", "%{$search}%")
                          ->orWhere("motif_non", "LIKE", "%{$search}%")
                          ->orWhere("observ_visite", "LIKE", "%{$search}%");
    } else {
        $result = Registre::with(['creator','entite','creator.agent_user'])->orderBy('id', 'asc');
    }

      $result = $result->whereHas('creator.agent_user', function ($q) use ($comm) {
          $q->where('idCom',"=", $comm);
      });
    // return response($result->get());
    if (isset($statut)) {
        $result = $result->where("satisfait", "=", $statut);
    }
    if (isset($dateDebut)) {
        $result=$result->whereDate('created_at', '>=', $dateDebut)
                      ->whereDate('created_at', '<=', $dateFin);
    }
    
    $infDate = "";
    if (isset($dateDebut) && isset($dateFin)) {
        $result=$result->whereDate('dateRequete', '>=', $dateDebut)
                ->whereDate('dateRequete', '<=', $dateFin);
        $infDate = "Du ".date('d/m/Y',strtotime($dateDebut))." au ".date('d/m/Y',strtotime($dateFin));
    }
    $result  = $result->orderBy('id', 'DESC')->get();
    $chec = Commune::with(['departement'])->where('id',$comm)->get()->first();
    
    if($chec){
      $comm = $chec->libellecom." - ".$chec->departement->libelle;
    }
    // return response($result->first());
   
    $name="Stat-Registre-$comm-".date('Ymdhis');
    $titre = "Statistique des régistres de visite <br>".$comm;
    $data=['data'=> $result,'name'=> $name,'titre'=> $titre];
    $pdf = PDF::loadView('statRegistreVisite', $data)->setPaper('a4', 'landscape');
    return $pdf->download($name.'.pdf');
  }

  public static function printregistreStat(Request $request){   

    
    // if(this.user) url+="&u="+this.user.id //id_user
    // if(this.select_date_start) url+="&db="+this.select_date_start // Date debut 
    // if(this.select_date_end) url+="&df="+this.select_date_end  //Date fin 
    // if(this.selected_idcom) url+="&ic="+this.selected_idcom //
    
    
    $dateDebut = $request->get('db');
    $dateFin = $request->get('df');
    $idEntite = $request->get('ie');
    $user = $request->get('u');
    $dateDebutGen = $dateDebut;

    if (!isset($dateDebut)) {
        $dateDebut = date('2022-06-01 00:00:00');
        $dateDebutGen = date('2019-01-01 00:00:00');
    }
    if (!isset($dateFin)) {
        $dateFin = date('Y-m-d 23:59:59');
    }

    $infDate = "";
    if (isset($dateDebut) && isset($dateFin)) {
        $infDate = "Période du ".date('d/m/Y',strtotime($dateDebut))." au ".date('d/m/Y',strtotime($dateFin));
    }
    $infDateGen = "";
    if (isset($dateDebutGen) && isset($dateFin)) {
        $infDateGen = "Période du ".date('d/m/Y',strtotime($dateDebutGen))." au ".date('d/m/Y',strtotime($dateFin));
    }

    $listCom = RequeteController::listCommune($user);
    // dd($listCom,$user);
    $datas=array();
    $i=0;
    foreach ($listCom as $Comm) {
      $stats = DB::select("SELECT count(*) total,
                        SUM(CASE WHEN plainte = 0 then 1 else 0 end) rns,
                        SUM(CASE WHEN plainte = 1 then 1 else 0 end) pns,
                        SUM(CASE WHEN plainte = 2 then 1 else 0 end) dns
                        FROM outilcollecte_registre reg,outilcollecte_users user,outilcollecte_acteur act
                        WHERE user.id = reg.created_by
                        AND  act.id = user.idagent
                        AND act.idCom = $Comm->id
                        AND reg.satisfait ='non'
                        AND reg.created_at BETWEEN '$dateDebut' and '$dateFin';");
                        
        $datas[$i]["Commune"] = $Comm->libellecom." - ".$Comm->libelle;
        $datas[$i]["rns"]= intval($stats[0]->rns);
        $datas[$i]["pns"]= intval($stats[0]->pns);
        $datas[$i]["dns"]= intval($stats[0]->dns);
        $datas[$i]["total"] = intval($stats[0]->total);
        $i++;
    }
    // 
    // Liste des communes qui ont transferé leur requete vers matacccueil 
    $i=0;
    $datasPresRegi = array();

    $reqCom = DB::select("SELECT DISTINCT comm.*, dep.libelle
                            FROM outilcollecte_registre reg,outilcollecte_users user,outilcollecte_acteur act,outilcollecte_commune comm,outilcollecte_departement dep
                            WHERE user.id = reg.created_by
                            AND  act.id = user.idagent AND  comm.id = act.idCom
                            AND  comm.depart_id = dep.id AND reg.idreq <> 0
                            AND reg.satisfait ='non' AND reg.created_at BETWEEN '$dateDebutGen' and '$dateFin'
                            ORDER BY comm.libellecom ASC;");

    foreach ($reqCom as $Comm) {

			$stats = DB::select("SELECT count(requ.id) total
									FROM outilcollecte_requete requ, outilcollecte_users user,outilcollecte_registre reg,outilcollecte_acteur act
									WHERE requ.id = reg.idreq
                  AND reg.created_by = user.id
                  AND act.id = user.idagent 
                  AND act.idCom = $Comm->id 
									AND requ.traiteOuiNon = 0 
									AND reg.idreq <> 0
                  AND reg.created_at BETWEEN '$dateDebutGen' and '$dateFin';");

			$Tplainte = $stats[0]->total;
			$datasPresRegi[$i]["commune_re"] = $Comm->libellecom." - ".$Comm->libelle;;
			$datasPresRegi[$i]["Tplainte"]=$Tplainte;
			//Charger les services
			$stats_serv = DB::select("SELECT DISTINCT ser.id, ser.libelle, count(*) total
						FROM outilcollecte_requete requ, outilcollecte_service ser, outilcollecte_users user,outilcollecte_registre reg,outilcollecte_acteur act
						WHERE requ.idPrestation = ser.id
            AND requ.id = reg.idreq
            AND user.id = reg.created_by 
            AND act.id = user.idagent 
            AND act.idCom = $Comm->id 
						AND requ.traiteOuiNon = 0
						AND  ser.id is not null
            AND reg.created_at BETWEEN '$dateDebutGen' and '$dateFin'
						GROUP BY ser.id, ser.libelle
						ORDER BY total DESC
						LIMIT 6;");
			$recu_Serv = "";
			foreach($stats_serv as $serv){
				if($serv->libelle != ""){
					$recu_Serv .= " * ".$serv->libelle." ($serv->total) <br>";
				}
			}
			$datasPresRegi[$i]["serv"]=$recu_Serv;
			$i++;
    }

    // 
		$datasPresStr=array();
		$i=0;
		
		// $structures = Structure::where("idParent",0)->get();
		$structures = DB::select("SELECT DISTINCT aff.idStructure, stru.sigle, stru.idParent
									FROM outilcollecte_requete req
									LEFT JOIN outilcollecte_affectation aff ON req.id = aff.idRequete
									LEFT JOIN outilcollecte_structure stru ON stru.id = aff.idStructure
									WHERE stru.idEntite = 1 
									AND stru.idParent = 0
                  AND stru.active=1
									AND req.traiteOuiNon = 0
									AND aff.dateAffectation BETWEEN '$dateDebutGen' and '$dateFin'
									ORDER BY stru.sigle ASC ;");

		foreach ($structures as $st) {
			$structure_id = $st->idStructure;
			$stats = DB::select("SELECT count(*) total
									FROM outilcollecte_requete
									WHERE outilcollecte_requete.traiteOuiNon = 0
									AND outilcollecte_requete.id IN 
									(
										SELECT DISTINCT outilcollecte_affectation.idRequete
										FROM outilcollecte_affectation
										WHERE outilcollecte_affectation.dateAffectation BETWEEN '$dateDebutGen' and '$dateFin'
										AND outilcollecte_affectation.idStructure = $st->idStructure
									);");

			$Tplainte = $stats[0]->total;
			$datasPresStr[$i]["strcuture"] = $st->sigle;
			$datasPresStr[$i]["idStructure"] = $st->idStructure;
			$datasPresStr[$i]["Tplainte"]=$Tplainte;
			//Charger les services
			$stats_seStr = DB::select("SELECT DISTINCT ser.id, ser.libelle, count(*) total
						FROM outilcollecte_requete req, outilcollecte_service ser 
						WHERE req.idPrestation = ser.id
						AND req.traiteOuiNon = 0
						AND  ser.id is not null
						AND req.id IN 
						(
							SELECT DISTINCT outilcollecte_affectation.idRequete
							FROM outilcollecte_affectation
							WHERE outilcollecte_affectation.dateAffectation BETWEEN '$dateDebutGen' and '$dateFin'
							AND outilcollecte_affectation.idStructure = $st->idStructure
						)
						GROUP BY ser.id, ser.libelle
						ORDER BY total DESC
						LIMIT 6;");
			$recu_Serv = "";
			foreach($stats_seStr as $ser){
				if($ser->libelle != ""){
					$recu_Serv .= " * ".$ser->libelle." ($ser->total) <br>";
				}
			}
			$datasPresStr[$i]["servStr"]=$recu_Serv;
			$i++;
		}
		// dd($datasPresStr);
    $titre = "Statistiques sur les préoccupations non satisfaites <br> au niveau des points focaux CCSP et GSRU <br>".$infDate;
    $titreGen = "Statistiques sur les préoccupations non traitées par structure<br/> avec les 5 premières prestations concernées <br>".$infDateGen;
    $titreGenReg = "Statistiques sur les préoccupations remontées et non traitées <br/> par les PFC avec les 5 premières prestations concernées <br>".$infDateGen;

    $data = ['datas'=> $datas,'titre'=> $titre,'titreGen'=> $titreGen,'titreGenReg'=> $titreGenReg,'datasPresStr'=> $datasPresStr,'datasPresRegi'=> $datasPresRegi];
    $pdf = PDF::loadView('PointStatRegistreVisite', $data)->setPaper('a4', 'landscape');
    return $pdf->download(date('Y-m-d-hist').'.pdf');
  }
  
  public function getStatByAllStructure($plainte,$idEntite){
    try{
       
      if($plainte==0){
        $delai=2;
       }
       if($plainte==1){
        $delai=3;
       }
       if($plainte==2){
        $delai=1;
       }
      //
      if($plainte == '-1'){  // Tous les types de plainte
          $stats = DB::select("SELECT  stru.id, stru.libelle, stru.idParent,
                            count(*) total,
                            sum(case when traiteOuiNon = 1 then 1 else 0 end) totalTraite,
                            sum(case when (ser.delaiFixe=1 and traiteOuiNon = 1 and DATEDIFF(dateReponse,req.created_at)>ser.nbreJours) then 1 else 0 end) totalTraiteHorsDelai,
                            sum(case when traiteOuiNon = 0 then 1 else 0 end) totalEnCours,
                            sum(case when (ser.delaiFixe=1 and traiteOuiNon = 0 and DATEDIFF(CURRENT_TIMESTAMP,req.created_at)>ser.nbreJours) then 1 else 0 end) totalEnCoursHorsDelai,
                            sum(case when (ser.delaiFixe=1 and traiteOuiNon = 0 and (DATEDIFF(CURRENT_TIMESTAMP,req.created_at) - ser.nbreJours) >= 0) then 1 else 0 end) totalEnCoursDelaiDans24H,
                            sum(case when (rejete=1 and traiteOuiNon = 1) then 1 else 0 end) totalRejet,
                            sum(case when (interrompu=1 and traiteOuiNon = 1) then 1 else 0 end) totalInterrompu,

                            sum(case when noteUsager is not NULL then 1 else 0 end) totalRetour,
                            sum(case when noteUsager>=5 then 1 else 0 end) totalRetourPositif
                            from outilcollecte_requete req
                            LEFT JOIN outilcollecte_affectation aff  ON req.id=aff.idRequete
                            LEFT JOIN outilcollecte_structure stru ON stru.id = aff.idStructure
                            LEFT JOIN outilcollecte_service ser ON ser.id = req.idPrestation
                            where visible=1
                            and req.idEntite=$idEntite
                            and stru.idParent!=0
                            and stru.active=1
                            group by stru.id,stru.libelle, stru.idParent order by total desc
                          ;");

      }else{
          $stats = DB::select("SELECT  stru.id, stru.libelle, stru.idParent,
                              count(*) total,
                              sum(case when traiteOuiNon = 1 then 1 else 0 end) totalTraite,
                              sum(case when (ser.delaiFixe=1 and traiteOuiNon = 1 and DATEDIFF(dateReponse,req.created_at)>ser.nbreJours) then 1 else 0 end) totalTraiteHorsDelai,
                              sum(case when traiteOuiNon = 0 then 1 else 0 end) totalEnCours,
                              sum(case when (ser.delaiFixe=1 and traiteOuiNon = 0 and DATEDIFF(CURRENT_TIMESTAMP,req.created_at)>ser.nbreJours) then 1 else 0 end) totalEnCoursHorsDelai,
                              sum(case when (ser.delaiFixe=1 and traiteOuiNon = 0 and (DATEDIFF(CURRENT_TIMESTAMP,req.created_at) - ser.nbreJours) >= 0 and  (DATEDIFF(CURRENT_TIMESTAMP,req.created_at) - ser.nbreJours) <= $delai) then 1 else 0 end) totalEnCoursDelaiDans24H,
                              sum(case when (rejete=1 and traiteOuiNon = 1) then 1 else 0 end) totalRejet,
                              sum(case when (interrompu=1 and traiteOuiNon = 1) then 1 else 0 end) totalInterrompu,

                              sum(case when noteUsager is not NULL then 1 else 0 end) totalRetour,
                              sum(case when noteUsager>=5 then 1 else 0 end) totalRetourPositif
                              from outilcollecte_requete req
                              LEFT JOIN outilcollecte_affectation aff  ON req.id=aff.idRequete
                              LEFT JOIN outilcollecte_structure stru ON stru.id = aff.idStructure
                              LEFT JOIN outilcollecte_service ser ON ser.id = req.idPrestation
                              where visible=1
                              and req.idEntite=$idEntite
                              and stru.idParent!=0
                              and plainte=$plainte
                              and stru.active=1
                              group by stru.id,stru.libelle, stru.idParent order by total desc
                            ;");
      }
        return($stats);

      } catch(\Illuminate\Database\QueryException $ex){
        \Log::error($ex->getMessage());

        $error=array("error" => $ex->getMessage(),"status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
       
        return $error;
        }catch(\Exception $ex){

        \Log::error($ex->getMessage());
        $error =  array("error" => $ex->getMessage(),"status" => "error", "message" =>"Une erreur est survenue au cours du traitement de votre requête. Contactez l'administrateur s'il vous plaît.");
        return $error;
      }
    
  } 
  public function getStatPrestationbyStructure($idEntite)
    {
       try {
        $stats = DB::select("select stru.id, stru.libelle,stru.idParent,
          count(*) total,
          sum(case when ser.published = 1 then 1 else 0 end) totalPublish,
          sum(case when ser.published = 0 then 1 else 0 end) totalNotPublish

          from outilcollecte_service ser,outilcollecte_structure stru 
          where ser.idParent=stru.id 
          and ser.idEntite=$idEntite
          and stru.idEntite=$idEntite
          and stru.active=1
          group by stru.id,stru.libelle,stru.idParent order by total desc;");

          return ($stats);

      } catch(\Illuminate\Database\QueryException $ex){
         \Log::error($ex->getMessage());

        $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
        return $error;

      }catch(\Exception $ex){

         \Log::error($ex->getMessage());
         $error =  array("status" => "error", "message" =>"Une erreur est survenue au cours du traitement de votre requête. Contactez l'administrateur s'il vous plaît.");
         return $error;
        }
  }
  public function getStatbyStructure($user,$plainte,$idEntite) {
      try {
      if($plainte==0){
        $delai=2;
      }
      if($plainte==1){
        $delai=3;
      }
      if($plainte==2){
        $delai=1;
      }

			// $Tplainte = $stats[0]->total;
          if($user=='all'){
            if($plainte == '-1'){  // Tous les types de plainte
              $stats = DB::select("SELECT stru.id, stru.libelle,stru.idParent,
                            count(*) total,
                            sum(case when traiteOuiNon = 1 then 1 else 0 end) totalTraite,
                            sum(case when (ser.delaiFixe=1 and traiteOuiNon = 1 and DATEDIFF(dateReponse,req.created_at)>ser.nbreJours) then 1 else 0 end) totalTraiteHorsDelai,
                            sum(case when traiteOuiNon = 0 then 1 else 0 end) totalEnCours,
                            sum(case when (ser.delaiFixe=1 and traiteOuiNon = 0 and DATEDIFF(CURRENT_TIMESTAMP,req.created_at)>ser.nbreJours) then 1 else 0 end) totalEnCoursHorsDelai,
                            sum(case when (ser.delaiFixe=1 and traiteOuiNon = 0 and (DATEDIFF(CURRENT_TIMESTAMP,req.created_at) - ser.nbreJours) >= 0) then 1 else 0 end) totalEnCoursDelaiDans24H,
                            sum(case when (rejete=1 and traiteOuiNon = 1) then 1 else 0 end) totalRejet,

                            sum(case when (interrompu=1 and traiteOuiNon = 1) then 1 else 0 end) totalInterrompu,

                            sum(case when noteUsager is not NULL then 1 else 0 end) totalRetour,
                            sum(case when noteUsager>=5 then 1 else 0 end) totalRetourPositif
                            from outilcollecte_requete req
                            LEFT JOIN outilcollecte_service ser ON req.idPrestation = ser.id 
                            LEFT JOIN outilcollecte_structure stru ON stru.id = ser.idParent
                            where visible=1
                            and req.idEntite=$idEntite
                            and stru.active=1
                            group by stru.id,stru.libelle,stru.idParent order by total desc;");
            }else{
              $stats = DB::select("SELECT stru.id, stru.libelle,stru.idParent,
                            count(*) total,
                            sum(case when traiteOuiNon = 1 then 1 else 0 end) totalTraite,
                            sum(case when (ser.delaiFixe=1 and traiteOuiNon = 1 and DATEDIFF(dateReponse,req.created_at)>ser.nbreJours) then 1 else 0 end) totalTraiteHorsDelai,
                            sum(case when traiteOuiNon = 0 then 1 else 0 end) totalEnCours,
                            sum(case when (ser.delaiFixe=1 and traiteOuiNon = 0 and DATEDIFF(CURRENT_TIMESTAMP,req.created_at)>ser.nbreJours) then 1 else 0 end) totalEnCoursHorsDelai,
                            sum(case when (ser.delaiFixe=1 and traiteOuiNon = 0 and (DATEDIFF(CURRENT_TIMESTAMP,req.created_at) - ser.nbreJours) >= 0 and  (DATEDIFF(CURRENT_TIMESTAMP,req.created_at) - ser.nbreJours) <=  $delai) then 1 else 0 end) totalEnCoursDelaiDans24H,
                            sum(case when (rejete=1 and traiteOuiNon = 1) then 1 else 0 end) totalRejet,

                            sum(case when (interrompu=1 and traiteOuiNon = 1) then 1 else 0 end) totalInterrompu,

                            sum(case when noteUsager is not NULL then 1 else 0 end) totalRetour,
                            sum(case when noteUsager>=5 then 1 else 0 end) totalRetourPositif
                            from outilcollecte_requete req
                            LEFT JOIN outilcollecte_service ser ON req.idPrestation = ser.id 
                            LEFT JOIN outilcollecte_structure stru ON stru.id = ser.idParent
                            where visible=1
                            and plainte=$plainte
                            and req.idEntite=$idEntite
                            and stru.active=1
                            group by stru.id,stru.libelle,stru.idParent order by total desc;");

            }
          }else{
            $getUser=Utilisateur::find($user);
            $getProfil=Profil::find($getUser->idprofil);
            

            if( ($getProfil->parametre==1) || ($getProfil->saisie==1) ||  ($getProfil->sgm==1) || ($getProfil->dc==1) || ($getProfil->ministre==1) ){
              
              $stats = DB::select("SELECT 'Statistiques globales' as libelle,
                              count(*) total,
                              sum(case when traiteOuiNon = 1 then 1 else 0 end) totalTraite,
                              sum(case when (ser.delaiFixe=1 and traiteOuiNon = 1 and DATEDIFF(dateReponse,req.created_at)>ser.nbreJours) then 1 else 0 end) totalTraiteHorsDelai,
                              sum(case when (ser.delaiFixe=1 and traiteOuiNon = 0 and (DATEDIFF(CURRENT_TIMESTAMP,req.created_at) - ser.nbreJours) >= 0 and  (DATEDIFF(CURRENT_TIMESTAMP,req.created_at) - ser.nbreJours) <= $delai) then 1 else 0 end) totalEnCoursDelaiDans24H,
                              sum(case when (rejete=1 and traiteOuiNon = 1) then 1 else 0 end) totalRejet,
                              sum(case when (interrompu=1 and traiteOuiNon = 1) then 1 else 0 end) totalInterrompu,
                              sum(case when traiteOuiNon = 0 then 1 else 0 end) totalEnCours,
                              sum(case when (ser.delaiFixe=1 and traiteOuiNon = 0 and DATEDIFF(CURRENT_TIMESTAMP,req.created_at)>ser.nbreJours) then 1 else 0 end) totalEnCoursHorsDelai,
                              sum(case when noteUsager is not NULL then 1 else 0 end) totalRetour,
                              sum(case when noteUsager>=5 then 1 else 0 end) totalRetourPositif
                              from outilcollecte_requete req
                              LEFT JOIN outilcollecte_service ser ON req.idPrestation = ser.id
                              where visible=1
                              and req.idEntite=$idEntite
                              and plainte=$plainte");
            }else{
                $idagent=$getUser->idagent;
                $getAgent=Acteur::find($idagent);
                $idStructure="";
                
                if($getAgent !== null)             //count($getAgent)>0)
                  $idStructure=$getAgent->idStructure;
                  //stru.id, stru.libelle, totalEnCoursHorsDelai
                  $stats = DB::select("SELECT count(*) total,
                                        SUM(CASE WHEN traiteOuiNon = 1 then 1 else 0 end) totalTraite,
                                        SUM(CASE WHEN traiteOuiNon = 0 then 1 else 0 end) totalNTraite,
                                        SUM(CASE WHEN horsDelai = 3 then 1 else 0 end) totalTraiteHorsDelai,
                                        SUM(case when traiteOuiNon = 0 then 1 else 0 end) totalEnCours,
                                        SUM(case when (interrompu=1 and traiteOuiNon = 1) then 1 else 0 end) totalInterrompu,
                                        SUM(case when (ser.delaiFixe=1 and traiteOuiNon = 0 and DATEDIFF(CURRENT_TIMESTAMP,req.created_at)>ser.nbreJours) then 1 else 0 end) totalEnCoursHorsDelai,
                                        SUM(case when (ser.delaiFixe=1 and traiteOuiNon = 0 and (DATEDIFF(CURRENT_TIMESTAMP,req.created_at) - ser.nbreJours) >= 0 and  (DATEDIFF(CURRENT_TIMESTAMP,req.created_at) - ser.nbreJours) <= $delai) then 1 else 0 end) totalEnCoursDelaiDans24H,
                                        SUM(case when (rejete=1 and traiteOuiNon = 1) then 1 else 0 end) totalRejet,
                                        SUM(case when noteUsager is not NULL then 1 else 0 end) totalRetour,
                                        SUM(case when noteUsager>=5 then 1 else 0 end) totalRetourPositif
                                        FROM outilcollecte_requete req
                                        LEFT JOIN outilcollecte_service ser ON req.idPrestation = ser.id
                                        WHERE req.plainte = $plainte
                                        AND req.id IN 
                                        (
                                          SELECT DISTINCT aff.idRequete
                                          FROM outilcollecte_affectation aff
                                          INNER JOIN
                                          (
                                            SELECT DISTINCT idRequete, MAX(`id`) AS max_aff
                                            FROM outilcollecte_affectation
                                            WHERE outilcollecte_affectation.typeStructure = 'Direction'
                                            GROUP BY idRequete
                                          ) tmp
                                            ON tmp.idRequete = aff.idRequete
                                            AND tmp.max_aff = aff.`id`
                                            AND aff.idStructure = $idStructure
                                          ORDER BY aff.`id` ASC
                                        );");

                  // $stats = DB::select("SELECT count(*) total,
                  //                     SUM(case when traiteOuiNon = 1 then 1 else 0 end) totalTraite,
                  //                     SUM(case when (ser.delaiFixe=1 and traiteOuiNon = 1 and DATEDIFF(dateReponse,req.created_at)>ser.nbreJours) then 1 else 0 end) totalTraiteHorsDelai,
                  //                     SUM(case when traiteOuiNon = 0 then 1 else 0 end) totalEnCours,
                  //                     SUM(case when (ser.delaiFixe=1 and traiteOuiNon = 0 and DATEDIFF(CURRENT_TIMESTAMP,req.created_at)>ser.nbreJours) then 1 else 0 end) totalEnCoursHorsDelai,
                  //                     SUM(case when (ser.delaiFixe=1 and traiteOuiNon = 0 and (DATEDIFF(CURRENT_TIMESTAMP,req.created_at) - ser.nbreJours) >= 0 and  (DATEDIFF(CURRENT_TIMESTAMP,req.created_at) - ser.nbreJours) <= $delai) then 1 else 0 end) totalEnCoursDelaiDans24H,
                  //                     SUM(case when (rejete=1 and traiteOuiNon = 1) then 1 else 0 end) totalRejet,
                  //                     SUM(case when (interrompu=1 and traiteOuiNon = 1) then 1 else 0 end) totalInterrompu,

                  //                     SUM(case when noteUsager is not NULL then 1 else 0 end) totalRetour,
                  //                     SUM(case when noteUsager>=5 then 1 else 0 end) totalRetourPositif
                  //                     from outilcollecte_requete req,outilcollecte_structure stru,outilcollecte_service ser,outilcollecte_affectation aff 
                  //                     where req.id=aff.idRequete
                  //                     and req.idPrestation=ser.id
                  //                     and aff.idStructure=stru.id
                  //                     and stru.id=$idStructure  
                  //                     and visible=1
                  //                     and req.idEntite=$idEntite
                  //                     and plainte=$plainte
                  //             ;");
                              // group by stru.id,stru.libelle order by total desc
            } 
          }

          return response($stats);

      } catch(\Illuminate\Database\QueryException $ex){
         \Log::error($ex->getMessage());

        $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
        }catch(\Exception $ex){

         \Log::error($ex->getMessage());
         $error =  array("status" => "error", "message" =>"Une erreur est survenue au cours du traitement de votre requête. Contactez l'administrateur s'il vous plaît.");
         return $error;
        }
  }
  public function getStatbyStructureCour($user,$plainte,$idEntite) {
      try {
        if($plainte==0){
          $delai=2;
        }
        if($plainte==1){
          $delai=3;
        }
        if($plainte==2){
          $delai=1;
        }

          if($user == 'all'){
              $stats = DB::select("SELECT stru.id, stru.libelle,stru.idParent,
                            count(*) total,
                            sum(case when traiteOuiNon = 1 then 1 else 0 end) totalTraite,
                            sum(case when (ser.delaiFixe=1 and traiteOuiNon = 1 and DATEDIFF(dateReponse,req.created_at)>ser.nbreJours) then 1 else 0 end) totalTraiteHorsDelai,
                            sum(case when traiteOuiNon = 0 then 1 else 0 end) totalEnCours,
                            sum(case when (ser.delaiFixe=1 and traiteOuiNon = 0 and DATEDIFF(CURRENT_TIMESTAMP,req.created_at)>ser.nbreJours) then 1 else 0 end) totalEnCoursHorsDelai,
                            sum(case when (ser.delaiFixe=1 and traiteOuiNon = 0 and (DATEDIFF(CURRENT_TIMESTAMP,req.created_at) - ser.nbreJours) >= 0 and  (DATEDIFF(CURRENT_TIMESTAMP,req.created_at) - ser.nbreJours) <=  $delai) then 1 else 0 end) totalEnCoursDelaiDans24H,
                            sum(case when (rejete=1 and traiteOuiNon = 1) then 1 else 0 end) totalRejet,

                            sum(case when (interrompu=1 and traiteOuiNon = 1) then 1 else 0 end) totalInterrompu,

                            sum(case when noteUsager is not NULL then 1 else 0 end) totalRetour,
                            sum(case when noteUsager>=5 then 1 else 0 end) totalRetourPositif
                            from outilcollecte_requete req
                            LEFT JOIN outilcollecte_service ser ON req.idPrestation = ser.id 
                            LEFT JOIN outilcollecte_structure stru ON stru.id = ser.idParent
                            where visible=1
                            and plateforme ='sygec'
                            and plainte=$plainte
                            and req.idEntite=$idEntite
                            and stru.active=1
                            group by stru.id,stru.libelle,stru.idParent order by total desc;");
          }else{
            $getUser=Utilisateur::find($user);
            $getProfil=Profil::find($getUser->idprofil);
            

            if( ($getProfil->parametre==1) || ($getProfil->saisie==1) ||  ($getProfil->sgm==1) || ($getProfil->dc==1) || ($getProfil->ministre==1) ){
              
              $stats = DB::select("SELECT 'Statistiques globales' as libelle,
                              count(*) total,
                              sum(case when traiteOuiNon = 1 then 1 else 0 end) totalTraite,
                              sum(case when (ser.delaiFixe=1 and traiteOuiNon = 1 and DATEDIFF(dateReponse,req.created_at)>ser.nbreJours) then 1 else 0 end) totalTraiteHorsDelai,
                              sum(case when (ser.delaiFixe=1 and traiteOuiNon = 0 and (DATEDIFF(CURRENT_TIMESTAMP,req.created_at) - ser.nbreJours) >= 0 and  (DATEDIFF(CURRENT_TIMESTAMP,req.created_at) - ser.nbreJours) <= $delai) then 1 else 0 end) totalEnCoursDelaiDans24H,
                              sum(case when (rejete=1 and traiteOuiNon = 1) then 1 else 0 end) totalRejet,
                              sum(case when (interrompu=1 and traiteOuiNon = 1) then 1 else 0 end) totalInterrompu,
                              sum(case when traiteOuiNon = 0 then 1 else 0 end) totalEnCours,
                              sum(case when (ser.delaiFixe=1 and traiteOuiNon = 0 and DATEDIFF(CURRENT_TIMESTAMP,req.created_at)>ser.nbreJours) then 1 else 0 end) totalEnCoursHorsDelai,
                              sum(case when noteUsager is not NULL then 1 else 0 end) totalRetour,
                              sum(case when noteUsager>=5 then 1 else 0 end) totalRetourPositif
                              from outilcollecte_requete req
                              LEFT JOIN outilcollecte_service ser ON req.idPrestation = ser.id
                              where visible=1
                              and req.plateforme ='sygec'
                              and req.idEntite=$idEntite
                              and plainte=$plainte");
            }else{
                $idagent=$getUser->idagent;
                $getAgent=Acteur::find($idagent);
                $idStructure="";
                
                if($getAgent !== null)             //count($getAgent)>0)
                  $idStructure=$getAgent->idStructure;
                  //stru.id, stru.libelle, totalEnCoursHorsDelai
                  $stats = DB::select("SELECT count(*) total,
                                        SUM(CASE WHEN traiteOuiNon = 1 then 1 else 0 end) totalTraite,
                                        SUM(CASE WHEN traiteOuiNon = 0 then 1 else 0 end) totalNTraite,
                                        SUM(CASE WHEN horsDelai = 3 then 1 else 0 end) totalTraiteHorsDelai,
                                        SUM(case when traiteOuiNon = 0 then 1 else 0 end) totalEnCours,
                                        SUM(case when (interrompu=1 and traiteOuiNon = 1) then 1 else 0 end) totalInterrompu,
                                        SUM(case when (ser.delaiFixe=1 and traiteOuiNon = 0 and DATEDIFF(CURRENT_TIMESTAMP,req.created_at)>ser.nbreJours) then 1 else 0 end) totalEnCoursHorsDelai,
                                        SUM(case when (ser.delaiFixe=1 and traiteOuiNon = 0 and (DATEDIFF(CURRENT_TIMESTAMP,req.created_at) - ser.nbreJours) >= 0 and  (DATEDIFF(CURRENT_TIMESTAMP,req.created_at) - ser.nbreJours) <= $delai) then 1 else 0 end) totalEnCoursDelaiDans24H,
                                        SUM(case when (rejete=1 and traiteOuiNon = 1) then 1 else 0 end) totalRejet,
                                        SUM(case when noteUsager is not NULL then 1 else 0 end) totalRetour,
                                        SUM(case when noteUsager>=5 then 1 else 0 end) totalRetourPositif
                                        FROM outilcollecte_requete req
                                        LEFT JOIN outilcollecte_service ser ON req.idPrestation = ser.id
                                        WHERE req.plainte = $plainte
                                        AND req.plateforme = 'sygec'
                                        AND req.id IN 
                                        (
                                          SELECT DISTINCT outilcollecte_affectation.idRequete
                                          FROM outilcollecte_affectation
                                          WHERE outilcollecte_affectation.idStructure = $idStructure 
                                        );");
            } 
          }

          return response($stats);

      } catch(\Illuminate\Database\QueryException $ex){
         \Log::error($ex->getMessage());

        $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
        }catch(\Exception $ex){

         \Log::error($ex->getMessage());
         $error =  array("status" => "error", "message" =>"Une erreur est survenue au cours du traitement de votre requête. Contactez l'administrateur s'il vous plaît.");
         return $error;
        }
  }


    public function getStatbyType($type,$plainte,$idEntite)
    {
       try {
        if($plainte==0){
          $delai=2;
         }
         if($plainte==1){
          $delai=3;
         }
         if($plainte==2){
          $delai=1;
         }
          if($type=='all'){
            if($plainte == '-1'){  // Tous les types de plainte
              $stats = DB::select("select ty.libelle,
                            count(*) total,
                            sum(case when traiteOuiNon = 1 then 1 else 0 end) totalTraite,
                            sum(case when (ser.delaiFixe=1 and traiteOuiNon = 1 and DATEDIFF(dateReponse,req.created_at)>ser.nbreJours) then 1 else 0 end) totalTraiteHorsDelai,
  
                            sum(case when (rejete=1 and traiteOuiNon = 1) then 1 else 0 end) totalRejet,
                            sum(case when (interrompu=1 and traiteOuiNon = 1) then 1 else 0 end) totalInterrompu,
  
                            sum(case when traiteOuiNon = 0 then 1 else 0 end) totalEnCours,
                            sum(case when (ser.delaiFixe=1 and traiteOuiNon = 0 and DATEDIFF(CURRENT_TIMESTAMP,req.created_at)>ser.nbreJours) then 1 else 0 end) totalEnCoursHorsDelai,
                            sum(case when (ser.delaiFixe=1 and traiteOuiNon = 0 and (DATEDIFF(CURRENT_TIMESTAMP,req.created_at) - ser.nbreJours) >= 0) then 1 else 0 end) totalEnCoursDelaiDans24H,
                            sum(case when noteUsager is not NULL then 1 else 0 end) totalRetour,
                            sum(case when noteUsager>=5 then 1 else 0 end) totalRetourPositif
                            from outilcollecte_requete req,outilcollecte_typeservice ty,outilcollecte_service ser 
                            where req.idPrestation=ser.id 
                            and ser.idType=ty.id   and visible=1
                            and ser.idEntite=$idEntite
                            and req.idEntite=$idEntite
                            and ty.idEntite=$idEntite
                            group by ty.id,ty.libelle;");
                            
            }else{
                $stats = DB::select("select ty.libelle,
                              count(*) total,
                              sum(case when traiteOuiNon = 1 then 1 else 0 end) totalTraite,
                              sum(case when (ser.delaiFixe=1 and traiteOuiNon = 1 and DATEDIFF(dateReponse,req.created_at)>ser.nbreJours) then 1 else 0 end) totalTraiteHorsDelai,
    
                              sum(case when (rejete=1 and traiteOuiNon = 1) then 1 else 0 end) totalRejet,
                              sum(case when (interrompu=1 and traiteOuiNon = 1) then 1 else 0 end) totalInterrompu,
    
                              sum(case when traiteOuiNon = 0 then 1 else 0 end) totalEnCours,
                              sum(case when (ser.delaiFixe=1 and traiteOuiNon = 0 and DATEDIFF(CURRENT_TIMESTAMP,req.created_at)>ser.nbreJours) then 1 else 0 end) totalEnCoursHorsDelai,
                              sum(case when (ser.delaiFixe=1 and traiteOuiNon = 0 and (DATEDIFF(CURRENT_TIMESTAMP,req.created_at) - ser.nbreJours) >= 0 and  (DATEDIFF(CURRENT_TIMESTAMP,req.created_at) - ser.nbreJours) <=  $delai) then 1 else 0 end) totalEnCoursDelaiDans24H,
                              sum(case when noteUsager is not NULL then 1 else 0 end) totalRetour,
                              sum(case when noteUsager>=5 then 1 else 0 end) totalRetourPositif
                              from outilcollecte_requete req,outilcollecte_typeservice ty,outilcollecte_service ser 
                              where req.idPrestation=ser.id 
                              and ser.idType=ty.id   and visible=1
                              and plainte=$plainte
                              and ser.idEntite=$idEntite
                              and req.idEntite=$idEntite
                              and ty.idEntite=$idEntite
                            
                              group by ty.id,ty.libelle;");

            }
          }
          return($stats);

      } catch(\Illuminate\Database\QueryException $ex){
         \Log::error($ex->getMessage());

        $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
        }catch(\Exception $ex){

         \Log::error($ex->getMessage());
         $error =
        array("status" => "error", "message" =>$ex); array("status" => "error", "message" =>"Une erreur est survenue au cours du traitement de votre requête. Contactez l'administrateur s'il vous plaît.");
         return $error;
        }
    }


    public function getNbrebyType($type,$plainte,$idEntite)
    {
       try {
        if($type=='all') {

          if($plainte == '-1'){ // Tous les types de plainte
            $stats = DB::select("select typeser.libelle,count(*) total 
                        from outilcollecte_requete req,outilcollecte_typeservice typeser,outilcollecte_service ser
                        where req.idPrestation=ser.id 
                        and ser.idType=typeser.id 
                        and ser.idEntite=$idEntite
                        and req.idEntite=$idEntite
                        and typeser.idEntite=$idEntite
                        and visible=1
                        group by typeser.id,typeser.libelle");
          }else{
            $stats = DB::select("select typeser.libelle,count(*) total 
                      from outilcollecte_requete req,outilcollecte_typeservice typeser,outilcollecte_service ser
                      where req.idPrestation=ser.id 
                      and ser.idType=typeser.id 
                      and ser.idEntite=$idEntite
                      and req.idEntite=$idEntite
                      and typeser.idEntite=$idEntite
                    
                      and plainte=$plainte  and visible=1
                      group by typeser.id,typeser.libelle");

          }
            return($stats);
        }

      } catch(\Illuminate\Database\QueryException $ex){
         \Log::error($ex->getMessage());

        $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
        }catch(\Exception $ex){

         \Log::error($ex->getMessage());
         $error =
        array("status" => "error", "message" =>"Une erreur est survenue au cours du traitement de votre requête. Contactez l'administrateur s'il vous plaît.");
         return $error;
        }
    }

    public function getNbrebyStructure($structure,$plainte,$idEntite)
    {
       try {
        if($structure=='all') {
          if($plainte == '-1'){
            $stats = DB::select("select struct.libelle,count(*) total 
                                from outilcollecte_requete req,outilcollecte_structure struct,outilcollecte_service ser
                                where req.idPrestation=ser.id 
                                and ser.idParent=struct.id  and visible=1
                                and ser.idEntite=$idEntite
                                and req.idEntite=$idEntite
                                and struct.idEntite=$idEntite
                                and struct.active=1
                                group by struct.id,struct.libelle ORDER BY COUNT(*) DESC");
          }else{
            $stats = DB::select("select struct.libelle,count(*) total 
                                from outilcollecte_requete req,outilcollecte_structure struct,outilcollecte_service ser
                                where req.idPrestation=ser.id 
                                and ser.idParent=struct.id  and visible=1
                                and plainte=$plainte
                                and ser.idEntite=$idEntite
                                and req.idEntite=$idEntite
                                and struct.idEntite=$idEntite
                                and struct.active=1
                                group by struct.id,struct.libelle ORDER BY COUNT(*) DESC");
          }
            return($stats);
        }

      } catch(\Illuminate\Database\QueryException $ex){
         \Log::error($ex->getMessage());

        $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
        }catch(\Exception $ex){

         \Log::error($ex->getMessage());
         $error = array("status" => "error", "message" =>"Une erreur est survenue au cours du traitement de votre requête. Contactez l'administrateur s'il vous plaît.");
         return $error;
        }
    }


    //getStatbyStructureR
   public function getStatbyStructureR(Request $request,$idEntite)
   {
   //  try {


       $inputArray =$request->all();
       $user = "";  if(!isset($inputArray["user"])) { $user = $inputArray["user"]; } else {
         return array("status" => "error", "message" => "Vous ne pouvez pas accéder à cette fonctionnalité" );
       }
       $plainte = "";
       if(!isset($inputArray["plainte"])) { $plainte = $inputArray["plainte"]; } else {
         return array("status" => "error", "message" => "Vous ne pouvez pas accéder à cette fonctionnalité" );
       }

       $delai=1;
       if((int)$plainte==0){
        $delai=2;
       }
       if((int)$plainte==1){
        $delai=3;
       }
       if((int)$plainte==2){
        $delai=1;
       }

       $startDate = "";  if(isset($inputArray["startDate"]))  { $startDate = $inputArray["startDate"]; }
       $startDate = ParamsFactory::convertToDateTimeForSearch($startDate, true);
       $startDate = $startDate->toDateTimeString();    //->getTimestamp();

       $endDate = "";  if(isset($inputArray["endDate"]))  { $endDate = $inputArray["endDate"]; }
       $endDate = ParamsFactory::convertToDateTimeForSearch($endDate, false);
       $endDate = $endDate->toDateTimeString(); //->getTimestamp();


       if($user=='all')
       {
        var_dump($startDate,$endDate);
        $stats=Requete::join('outilcollecte_service','outilcollecte_service.id','outilcollecte_requete.idPrestation')
        ->join('outilcollecte_structure','outilcollecte_structure.id','outilcollecte_service.idParent')
        ->whereBetween('dateRequete',[$startDate,$endDate])
        ->where('plainte',$plainte)
        ->whereHas('affectation.structure',function($q)use($idEntite){
         $q->where('idEntite',$idEntite)->where('active',1);
       })
       ->select(DB::raw("
       count(*) total,
       sum(case when traiteOuiNon = 1 then 1 else 0 end) totalTraite,
       sum(case when (outilcollecte_service.delaiFixe=1 and traiteOuiNon = 1 and DATEDIFF(dateReponse,outilcollecte_requete.created_at)>outilcollecte_service.nbreJours) then 1 else 0 end) totalTraiteHorsDelai,
       sum(case when traiteOuiNon = 0 then 1 else 0 end) totalEnCours,
       sum(case when (outilcollecte_service.delaiFixe=1 and traiteOuiNon = 0 and DATEDIFF(CURRENT_TIMESTAMP,outilcollecte_requete.created_at)>outilcollecte_service.nbreJours) then 1 else 0 end) totalEnCoursHorsDelai,
       sum(case when (outilcollecte_service.delaiFixe=1 and traiteOuiNon = 0 and (DATEDIFF(CURRENT_TIMESTAMP,outilcollecte_requete.created_at) - outilcollecte_service.nbreJours) >= 0 and  (DATEDIFF(CURRENT_TIMESTAMP,outilcollecte_requete.created_at) - outilcollecte_service.nbreJours) <= $delai) then 1 else 0 end) totalEnCoursDelaiDans24H,
       sum(case when (rejete=1 and traiteOuiNon = 1) then 1 else 0 end) totalRejet,
       sum(case when (interrompu=1 and traiteOuiNon = 1) then 1 else 0 end) totalInterrompu,
       sum(case when noteUsager is not NULL then 1 else 0 end) totalRetour,
       sum(case when noteUsager>=5 then 1 else 0 end) totalRetourPositif
       "))
       ->groupBy('outilcollecte_structure.libelle')
       ->orderBy('total','desc')
       ->get();

        //  $stats = DB::select("select stru.id, stru.libelle,stru.idParent,
        //                   count(*) total,
        //                   sum(case when traiteOuiNon = 1 then 1 else 0 end) totalTraite,
        //                   sum(case when (ser.delaiFixe=1 and traiteOuiNon = 1 and DATEDIFF(dateReponse,req.created_at)>ser.nbreJours) then 1 else 0 end) totalTraiteHorsDelai,
        //                   sum(case when traiteOuiNon = 0 then 1 else 0 end) totalEnCours,
        //                   sum(case when (ser.delaiFixe=1 and traiteOuiNon = 0 and DATEDIFF(CURRENT_TIMESTAMP,req.created_at)>ser.nbreJours) then 1 else 0 end) totalEnCoursHorsDelai,
        //                   sum(case when (ser.delaiFixe=1 and traiteOuiNon = 0 and (DATEDIFF(CURRENT_TIMESTAMP,req.created_at) - ser.nbreJours) >= 0 and  (DATEDIFF(CURRENT_TIMESTAMP,req.created_at) - ser.nbreJours) <= $delai) then 1 else 0 end) totalEnCoursDelaiDans24H,
        //                   sum(case when (rejete=1 and traiteOuiNon = 1) then 1 else 0 end) totalRejet,
        //                   sum(case when (interrompu=1 and traiteOuiNon = 1) then 1 else 0 end) totalInterrompu,
        //                   sum(case when noteUsager is not NULL then 1 else 0 end) totalRetour,
        //                   sum(case when noteUsager>=5 then 1 else 0 end) totalRetourPositif
        //                   from outilcollecte_requete req,outilcollecte_structure stru,outilcollecte_service ser 
        //                   where req.idPrestation=ser.id 
        //                   and ser.idParent=stru.id 
        //                   and req.dateRequete between '$startDate' and '$endDate'
        //                   and plainte=$plainte
        //                   and stru.idEntite=$idEntite
        //                   and stru.active=1
                         
        //                   group by stru.libelle order by total desc;");
       }
       else{
         $getUser=Utilisateur::find($user);
         if(!$getUser === null){
           return array("status" => "error", "message" => "Cet utilisateur n'est pas valide" );
         }

         $getProfil=Profil::find($getUser->idprofil);
         if($getProfil === null){
           return array("status" => "error", "message" => "Ce profil d'utilisateur n'est pas valide" );
         }


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
                              sum(case when (ser.delaiFixe=1 and traiteOuiNon = 0 and (DATEDIFF(CURRENT_TIMESTAMP,req.created_at) - ser.nbreJours) >= 0 and  (DATEDIFF(CURRENT_TIMESTAMP,req.created_at) - ser.nbreJours) <= $delai) then 1 else 0 end) totalEnCoursDelaiDans24H,
                              sum(case when noteUsager is not NULL then 1 else 0 end) totalRetour,
                              sum(case when noteUsager>=5 then 1 else 0 end) totalRetourPositif
                              from outilcollecte_requete req,outilcollecte_service ser
                              where req.idPrestation=ser.id and visible=1
                              and ser.idEntite=$idEntite
                              and req.idEntite=$idEntite
                          
                              and plainte=$plainte");
         }
         else
         {
           $idagent=$getUser->idagent;
           $getAgent=Acteur::find($idagent);
           $idStructure="";

           if($getAgent !== null)             //count($getAgent)>0)


             $idStructure=$getAgent->idStructure;

           $stats = DB::select("select stru.id, stru.libelle,stru.idParent,
                              count(*) total,
                              sum(case when traiteOuiNon = 1 then 1 else 0 end) totalTraite,
                              sum(case when (ser.delaiFixe=1 and traiteOuiNon = 1 and DATEDIFF(dateReponse,req.created_at)>ser.nbreJours) then 1 else 0 end) totalTraiteHorsDelai,
                              sum(case when traiteOuiNon = 0 then 1 else 0 end) totalEnCours,
                              sum(case when (ser.delaiFixe=1 and traiteOuiNon = 0 and DATEDIFF(CURRENT_TIMESTAMP,req.created_at)>ser.nbreJours) then 1 else 0 end) totalEnCoursHorsDelai,
                              sum(case when (ser.delaiFixe=1 and traiteOuiNon = 0 and (DATEDIFF(CURRENT_TIMESTAMP,req.created_at) - ser.nbreJours) >= 0 and  (DATEDIFF(CURRENT_TIMESTAMP,req.created_at) - ser.nbreJours) <= $delai) then 1 else 0 end) totalEnCoursDelaiDans24H,
                              sum(case when (rejete=1 and traiteOuiNon = 1) then 1 else 0 end) totalRejet,
                              sum(case when (interrompu=1 and traiteOuiNon = 1) then 1 else 0 end) totalInterrompu,

                              sum(case when noteUsager is not NULL then 1 else 0 end) totalRetour,
                              sum(case when noteUsager>=5 then 1 else 0 end) totalRetourPositif
                              from outilcollecte_requete req,outilcollecte_structure stru,outilcollecte_service ser,outilcollecte_affectation aff 
                              where req.id=aff.idRequete
                              and req.idPrestation=ser.id
                              and aff.idStructure=stru.id
                              and stru.id=$idStructure  and visible=1
                              and plainte=0
                              and ser.idEntite=$idEntite
                              and req.idEntite=$idEntite
                              and stru.idEntite=$idEntite
                              and aff.idEntite=$idEntite
                              and stru.active=1
                              group by stru.id,stru.libelle, stru.idParent order by total desc;");
         }
       }
       //return(DB::select("select count(*) from outilcollecte_requete where plainte=2  and dateRequete between '$startDate' and '$endDate'"));

       return($stats);

    //  } catch(\Illuminate\Database\QueryException $ex){
    //    \Log::error($ex->getMessage());

    //    $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
    //  }catch(\Exception $ex){

    //    \Log::error($ex->getMessage());
    //    $error =  array("status" => "error", "message" =>"Une erreur est survenue au cours du traitement de votre requête. Contactez l'administrateur s'il vous plaît.");
    //    return $error;
    //  }
   }

   public function getStatbyTypeR(Request $request,$idEntite)
   {
     try {
       $inputArray = $request->all();
       $plainte = "";  if(isset($inputArray["plainte"])) { $plainte = $inputArray["plainte"]; } else {
         return array("status" => "error", "message" => "Vous ne pouvez pas accéder à cette fonctionnalité" );
       }
       $type = "";  if(isset($inputArray["type"])) { $type = $inputArray["type"]; } else {
         return array("status" => "error", "message" => "Vous ne pouvez pas accéder à cette fonctionnalité" );
       }

       $startDate = "";  if(isset($inputArray["startDate"])) $startDate = $inputArray["startDate"];
       $startDate = ParamsFactory::convertToDateTimeForSearch($startDate, true);
       $startDate = $startDate->toDateTimeString();    //->getTimestamp();
       //$startDate = $startDate;    //->getTimestamp();

       $endDate = "";  if(isset($inputArray["endDate"])) $endDate = $inputArray["endDate"];
       $endDate = ParamsFactory::convertToDateTimeForSearch($endDate, false);
       $endDate = $endDate->toDateTimeString(); //->getTimestamp();
       //$endDate = $endDate; //->getTimestamp();

       //var_dump($startDate,$endDate);

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
                          sum(case when (ser.delaiFixe=1 and traiteOuiNon = 0 and (DATEDIFF(CURRENT_TIMESTAMP,req.created_at) - ser.nbreJours) >= 0 and  (DATEDIFF(CURRENT_TIMESTAMP,req.created_at) - ser.nbreJours) <= 1) then 1 else 0 end) totalEnCoursDelaiDans24H,
                          sum(case when noteUsager is not NULL then 1 else 0 end) totalRetour,
                          sum(case when noteUsager>=5 then 1 else 0 end) totalRetourPositif
                          from outilcollecte_requete req,outilcollecte_typeservice ty,outilcollecte_service ser 
                          where req.idPrestation=ser.id    
                          and req.dateRequete > '$startDate' and req.dateRequete < '$endDate'
                          and ser.idType=ty.id   and req.visible=1
                          and plainte=$plainte
                          and ser.idEntite=$idEntite
                          and req.idEntite=$idEntite
                          and ty.idEntite=$idEntite
                         
                          group by ty.id,ty.libelle;");
       }
       return($stats);

     } catch(\Illuminate\Database\QueryException $ex){
       \Log::error($ex->getMessage());

       $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
       return $error;
     }catch(\Exception $ex){

       \Log::error($ex->getMessage());
       $error =
         array("status" => "error", "message" =>$ex); array("status" => "error", "message" =>"Une erreur est survenue au cours du traitement de votre requête. Contactez l'administrateur s'il vous plaît.");
       return $error;
     }
   }


   //type
   public function getNbrebyTypeR(Request $request,$idEntite)
   {
     try {
      // return $request->all();
       $inputArray = $request->all();
       $plainte = "";  if(isset($inputArray["plainte"])) { $plainte = $inputArray["plainte"]; } else {
         return array("status" => "error", "message" => "Vous ne pouvez pas accéder à cette fonctionnalité" );
       }
       $type = "";  if(isset($inputArray["type"])) { $type = $inputArray["type"]; } else {
         return array("status" => "error", "message" => "Vous ne pouvez pas accéder à cette fonctionnalité" );
       }

       $startDate = "";  if(isset($inputArray["startDate"]))  { $startDate = $inputArray["startDate"]; } else {
         return array("status" => "error", "message" => "Vous ne pouvez pas accéder à cette fonctionnalité" );
       }
       $startDate = ParamsFactory::convertToDateTimeForSearch($startDate, true);
       $startDate = $startDate->toDateTimeString();    //->getTimestamp();

       $endDate = "";  if(isset($inputArray["endDate"]))  { $endDate = $inputArray["endDate"]; }
       else {
         return array("status" => "error", "message" => "Vous ne pouvez pas accéder à cette fonctionnalité" );
       }
       $endDate = ParamsFactory::convertToDateTimeForSearch($endDate, false);
       $endDate = $endDate->toDateTimeString(); //->getTimestamp();


       if($type=='all') {

        if($plainte == '-1'){
          $stats = DB::select("select typeser.libelle,count(*) total 
             from outilcollecte_requete req,outilcollecte_typeservice typeser,outilcollecte_service ser
             where req.idPrestation=ser.id 
             and ser.idType=typeser.id 
             and visible=1
             and ser.idEntite=$idEntite
             and req.idEntite=$idEntite
             and typeser.idEntite=$idEntite
             and req.dateRequete > '$startDate' and req.dateRequete < '$endDate'
             group by typeser.id,typeser.libelle");
        }else{
          $stats = DB::select("select typeser.libelle,count(*) total 
             from outilcollecte_requete req,outilcollecte_typeservice typeser,outilcollecte_service ser
             where req.idPrestation=ser.id 
             and ser.idType=typeser.id 
             and plainte=$plainte  and visible=1
             and ser.idEntite=$idEntite
             and req.idEntite=$idEntite
             and typeser.idEntite=$idEntite
           
             and req.dateRequete > '$startDate' and req.dateRequete < '$endDate'
             group by typeser.id,typeser.libelle");

        }
         return($stats);
       }

     } catch(\Illuminate\Database\QueryException $ex){
       \Log::error($ex->getMessage());

       $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
     }catch(\Exception $ex){

       \Log::error($ex->getMessage());
       $error =
         array("status" => "error", "message" =>"Une erreur est survenue au cours du traitement de votre requête. Contactez l'administrateur s'il vous plaît.");
       return $error;
     }
   }//end getNbrebyTypeR


   public function getNbrebyStructureR(Request $request,$idEntite)
   {
     try {
       $inputArray = $request->all();
       $structure = "";  if(isset($inputArray["structure"])) { $structure = $inputArray["structure"]; } else {
         return array("status" => "error", "message" => "Vous ne pouvez pas accéder à cette fonctionnalité" );
       }
       $plainte = "";
       if(isset($inputArray["plainte"])) { $plainte = $inputArray["plainte"]; } else {
         return array("status" => "error", "message" => "Vous ne pouvez pas accéder à cette fonctionnalité" );
       }

       $startDate = "";  if(isset($inputArray["startDate"]))  { $startDate = $inputArray["startDate"]; } else {
         return array("status" => "error", "message" => "Vous ne pouvez pas accéder à cette fonctionnalité" );
       }
       $startDate = ParamsFactory::convertToDateTimeForSearch($startDate, true);
       $startDate = $startDate->toDateTimeString();    //->getTimestamp();

       $endDate = "";  if(isset($inputArray["endDate"]))  { $endDate = $inputArray["endDate"]; }
       else {
         return array("status" => "error", "message" => "Vous ne pouvez pas accéder à cette fonctionnalité" );
       }
       $endDate = ParamsFactory::convertToDateTimeForSearch($endDate, false);
       $endDate = $endDate->toDateTimeString(); //->getTimestamp();


       if($structure=='all'){

        if($plainte == '-1'){ // tous les types de plainte
          $stats = DB::select("select struct.libelle,count(*) total 
             from outilcollecte_requete req,outilcollecte_structure struct,outilcollecte_service ser
             where req.idPrestation=ser.id 
             and ser.idParent=struct.id  and visible=1
             and ser.idEntite=$idEntite
             and req.idEntite=$idEntite
             and struct.idEntite=$idEntite
             and struct.active=1
             and req.dateRequete > '$startDate' and req.dateRequete < '$endDate'
             group by struct.id,struct.libelle ORDER BY COUNT(*) DESC");

        }else{

          $stats = DB::select("select struct.libelle,count(*) total 
             from outilcollecte_requete req,outilcollecte_structure struct,outilcollecte_service ser
             where req.idPrestation=ser.id 
             and ser.idParent=struct.id  and visible=1
             and plainte=$plainte
             and ser.idEntite=$idEntite
             and req.idEntite=$idEntite
             and struct.idEntite=$idEntite
             and struct.active=1
             and req.dateRequete > '$startDate' and req.dateRequete < '$endDate'
             group by struct.id,struct.libelle ORDER BY COUNT(*) DESC");

        }
         return($stats);
       }

     } catch(\Illuminate\Database\QueryException $ex){
       \Log::error($ex->getMessage());

       $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
     }catch(\Exception $ex){

       \Log::error($ex->getMessage());
       $error = array("status" => "error", "message" =>"Une erreur est survenue au cours du traitement de votre requête. Contactez l'administrateur s'il vous plaît.");
       return $error;
     }
   }//end getNbrebyStructureR



  
   public function getNbrebyYear(Request $request,$plainte,$year,$idEntite)
   {
     try {
       $inputArray = $request->query();

       if(isset($inputArray["year"])) 
          $year = $inputArray["year"]; 
      
      if($year=='all')
      {
        $stats = DB::select("SELECT YEAR(created_at) as periode,count(*) as nbre FROM `outilcollecte_requete` where idEntite=$idEntite and plainte=$plainte group by YEAR(created_at) order by YEAR(created_at) ");
         
         return($stats);
      }
      else
      {
        setlocale(LC_ALL, 'fr_FR'); 

        $stats = DB::select("SELECT MONTHNAME(created_at) as periode,count(*) as nbre FROM `outilcollecte_requete` where  idEntite=$idEntite and plainte=$plainte and YEAR(created_at)=$year group by MONTHNAME(created_at),MONTH(created_at)  order by MONTH(created_at)");
         
         return($stats);
      }
       

     } catch(\Illuminate\Database\QueryException $ex){
       \Log::error($ex->getMessage());

       $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
     }catch(\Exception $ex){

       \Log::error($ex->getMessage());
       $error = array("status" => "error", "message" =>"Une erreur est survenue au cours du traitement de votre requête. Contactez l'administrateur s'il vous plaît.");
       return $error;
     }
   }//end getNbrebyStructureR 





    public function getNbrebyPrestation(Request $request,$idEntite)
    {
       try {

        $inputArray = $request->all();

        $startDate = "";  if(isset($inputArray["startDate"]))  { $startDate = $inputArray["startDate"]; }
        $endDate = "";  if(isset($inputArray["endDate"]))  { $endDate = $inputArray["endDate"]; }

        $querydate="";
        if(!($startDate=='all' && $endDate=='all'))
        {
        	$startDate = date("Y-m-d",strtotime($inputArray["startDate"]));               
          $endDate =  date("Y-m-d",strtotime($inputArray["endDate"])); 
         
        	$querydate="and DATE(req.dateRequete) between '$startDate' and '$endDate'";
        }

        $getUser=Utilisateur::find($inputArray['idUser']);
		     $getProfil=Profil::find($getUser->idprofil);


        if( ($getProfil->parametre==1) || ($getProfil->saisie==1) ||  ($getProfil->sgm==1) || ($getProfil->dc==1) || ($getProfil->ministre==1) )
        {
            $suitequery=" from outilcollecte_requete req,outilcollecte_service ser
                where ser.id=req.idPrestation
                and visible=1
                and req.idEntite=$idEntite
                and ser.idEntite=$idEntite
                ".$querydate.
                "group by ser.libelle
                order by total desc;";
        }
        else
        {
          $idStructure="";

          $idagent=$getUser->idagent;
          $getAgent=Acteur::find($idagent);

                if($getAgent !== null)             //count($getAgent)>0)
                  $idStructure=$getAgent->idStructure;

            $suitequery=" from outilcollecte_requete req,outilcollecte_structure stru,outilcollecte_service ser,outilcollecte_affectation aff 
                                      where req.id=aff.idRequete
                                      and req.idPrestation=ser.id
                                      and aff.idStructure=stru.id
                                      and stru.id=$idStructure  
                                      and req.idEntite=$idEntite
                                      and stru.idEntite=$idEntite
                                      and stru.active=1
                                      and ser.idEntite=$idEntite
                                      and aff.idEntite=$idEntite
                                      and visible=1 
                                      ".$querydate."
                                      group by stru.id,stru.libelle,ser.libelle order by total desc;";
        }

        

        $query="select ser.libelle as libelle,count(*) total,
            sum(case when plainte=0 then 1 else 0 end) totalRequete,
            sum(case when plainte=2 then 1 else 0 end) totalInfo,
            sum(case when plainte=1 then 1 else 0 end) totalPlainte".$suitequery;
         
       // var_dump($query);
        $stats = DB::select($query);
        //var_dump($stats);

        foreach ($stats  as $key => $value) {
          $idPrestation=DB::select("select id from outilcollecte_service where libelle=\"".$value->libelle."\" and idEntite=$idEntite ")[0]->id;
          $suitequeryNote=" from outilcollecte_note_usager, outilcollecte_requete
          where outilcollecte_note_usager.codeReq=outilcollecte_requete.codeRequete
          AND outilcollecte_requete.idPrestation=$idPrestation";

          $notation=DB::select("select COUNT(*) as res ".$suitequeryNote)[0]->res;
          $noteRecu=DB::select("select sum(noteResultat) as res ".$suitequeryNote)[0]->res;

          $noteAttendu=10*$notation;
          $stats[$key]->notation=$notation;
          $stats[$key]->noteRecu=$noteRecu;
          $stats[$key]->noteAttendu=$noteAttendu;
          $stats[$key]->pourcent= $noteAttendu==0?"-":( $noteRecu/$noteAttendu)*100;
        }

        $noteTotal=DB::select("select sum(noteResultat) as res from outilcollecte_note_usager")[0]->res;
        $noteDonne=DB::select("select COUNT(*) as res from outilcollecte_note_usager")[0]->res;

        $pourcent=($noteTotal / ($noteDonne*10))*100;
        return response()->json([
          "stats"=> $stats,
          "pourcent"=>$pourcent
        ]);


      } catch(\Illuminate\Database\QueryException $ex){
         \Log::error($ex->getMessage());

        $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
        }catch(\Exception $ex){

         \Log::error($ex->getMessage());
         $error =
        array("status" => "error", "message" =>"Une erreur est survenue au cours du traitement de votre requête. Contactez l'administrateur s'il vous plaît.");
         return $error;
        }
    }



    public function getStatReponse(Request $request,$idEntite)
    {
       try {

        $inputArray = $request->query();

        $startDate = "";  if(isset($inputArray["startDate"]))  { $startDate = $inputArray["startDate"]; }
        $endDate = "";  if(isset($inputArray["endDate"]))  { $endDate = $inputArray["endDate"]; }

        if($startDate=='all' && $endDate=='all')
        {
            $stats = DB::select("select struct.libelle as libelle,
			count(*) total,
			sum(case when traiteOuiNon = 1 then 1 else 0 end) totalTraite,
			sum(case when noteUsager is not NULL then 1 else 0 end) totalAyantNote,
			sum(noteUsager) as sumNoteMoy
            from outilcollecte_requete req,outilcollecte_structure struct,outilcollecte_service ser
            where req.idPrestation=ser.id 
            and ser.idEntite=$idEntite
            and req.idEntite=$idEntite
            and struct.idEntite=$idEntite
            and struct.active=1
            and ser.idParent=struct.id  and visible=1
            group by struct.id,struct.libelle ORDER BY COUNT(*) DESC;");
        }
        else
        {

           $startDate = ParamsFactory::convertToDateTimeForSearch($startDate, true);
           $startDate = $startDate->toDateTimeString();    //->getTimestamp();

           $endDate = ParamsFactory::convertToDateTimeForSearch($endDate, false);
           $endDate = $endDate->toDateTimeString(); //->getTimestamp();

           $stats = DB::select("select struct.libelle,
            count(*) total,
            sum(case when traiteOuiNon = 1 then 1 else 0 end) totalTraite,
            sum(case when noteUsager is not NULL then 1 else 0 end) totalAyantNote,
            sum(noteUsager) as sumNoteMoy
            from outilcollecte_requete req,outilcollecte_structure struct,outilcollecte_service ser
            where req.idPrestation=ser.id 
            and ser.idEntite=$idEntite
            and req.idEntite=$idEntite
            and struct.idEntite=$idEntite
            and struct.active=1
            and ser.idParent=struct.id  and visible=1
            and req.dateRequete > '$startDate' and req.dateRequete < '$endDate'
            group by struct.id,struct.libelle ORDER BY COUNT(*) DESC;");
        }

            return($stats);


      } catch(\Illuminate\Database\QueryException $ex){
         \Log::error($ex->getMessage());

        $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
        }catch(\Exception $ex){

         \Log::error($ex->getMessage());
         $error =
        array("status" => "error", "message" =>"Une erreur est survenue au cours du traitement de votre requête. Contactez l'administrateur s'il vous plaît.");
         return $error;
        }
    }

public function getRatioByRequetePrestationTraitees(Request $request,$idEntite)
    {
       try {

        $inputArray = $request->all();

        $startDate = "";  if(isset($inputArray["startDate"]))  { $startDate = $inputArray["startDate"]; }
        $endDate = "";  if(isset($inputArray["endDate"]))  { $endDate = $inputArray["endDate"]; }

        $query = DB::select("select ser.libelle, sum(1+DATEDIFF(dateReponse, dateRequete))/sum(nbreJours) as ratio
                from outilcollecte_service ser, outilcollecte_requete req
                where req.idPrestation=ser.id
                and req.plainte=0
                and ser.idEntite=$idEntite
                and req.idEntite=$idEntite
              
                and dateReponse is not null
                and ser.delaiFixe=1
                group by ser.id,ser.libelle;");

        return ($query);


      } catch(\Illuminate\Database\QueryException $ex){
         \Log::error($ex->getMessage());

        $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requ�te. Veuillez contactez l'administrateur" );
        }catch(\Exception $ex){

         \Log::error($ex->getMessage());
         $error =
        array("status" => "error", "message" =>"Une erreur est survenue au cours du traitement de votre requ�te. Contactez l'administrateur s'il vous pla�t.");
           return $error;
        }
    }

    public function getRatioByRequetePrestationEnCours(Request $request,$idEntite)
    {
       try {

        $inputArray = $request->all();

        $startDate = "";  if(isset($inputArray["startDate"]))  { $startDate = $inputArray["startDate"]; }
        $endDate = "";  if(isset($inputArray["endDate"]))  { $endDate = $inputArray["endDate"]; }

        $query = DB::select("select ser.libelle, sum(1+DATEDIFF(NOW(), dateRequete))/sum(nbreJours) as ratio
              from outilcollecte_service ser, outilcollecte_requete req
              where req.idPrestation=ser.id
              and req.plainte=0
              and ser.idEntite=$idEntite
              and req.idEntite=$idEntite
             
              and dateReponse is null
              and ser.delaiFixe=1
              group by ser.id,ser.libelle;");

        return ($query);


      } catch(\Illuminate\Database\QueryException $ex){
         \Log::error($ex->getMessage());

        $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requ�te. Veuillez contactez l'administrateur" );
        }catch(\Exception $ex){

         \Log::error($ex->getMessage());
         $error =
        array("status" => "error", "message" =>"Une erreur est survenue au cours du traitement de votre requ�te. Contactez l'administrateur s'il vous pla�t.");
         return $error;
        }
    }


    public function getRatioByPlaintePrestationTraitees(Request $request,$idEntite)
    {
       try {

        $inputArray = $request->all();

        $startDate = "";  if(isset($inputArray["startDate"]))  { $startDate = $inputArray["startDate"]; }
        $endDate = "";  if(isset($inputArray["endDate"]))  { $endDate = $inputArray["endDate"]; }

        $query = DB::select("select ser.libelle, sum(1+DATEDIFF(dateReponse, dateRequete))/sum(nbreJours) as ratio
                from outilcollecte_service ser, outilcollecte_requete req
                where req.idPrestation=ser.id
                and req.plainte=1
                and ser.idEntite=$idEntite
                and req.idEntite=$idEntite
                and dateReponse is not null
                and ser.delaiFixe=1
                group by ser.id,ser.libelle;");

        return ($query);


      } catch(\Illuminate\Database\QueryException $ex){
         \Log::error($ex->getMessage());

        $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requ�te. Veuillez contactez l'administrateur" );
        }catch(\Exception $ex){

         \Log::error($ex->getMessage());
         $error =
        array("status" => "error", "message" =>"Une erreur est survenue au cours du traitement de votre requ�te. Contactez l'administrateur s'il vous pla�t.");
         return $error;
        }
    }

    public function getRatioByPlaintePrestationEnCours(Request $request,$idEntite)
    {
       try {

        $inputArray =$request->all();

        $startDate = "";  if(isset($inputArray["startDate"]))  { $startDate = $inputArray["startDate"]; }
        $endDate = "";  if(isset($inputArray["endDate"]))  { $endDate = $inputArray["endDate"]; }

        $query = DB::select("select ser.libelle, sum(1+DATEDIFF(NOW(), dateRequete))/sum(nbreJours) as ratio
              from outilcollecte_service ser, outilcollecte_requete req
              where req.idPrestation=ser.id
              and req.plainte=1
              and dateReponse is null
              and ser.delaiFixe=1
              and ser.idEntite=$idEntite
              and req.idEntite=$idEntite
  
              group by ser.id,ser.libelle;");

        return ($query);


      } catch(\Illuminate\Database\QueryException $ex){
         \Log::error($ex->getMessage());

        $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requ�te. Veuillez contactez l'administrateur" );
        }catch(\Exception $ex){

         \Log::error($ex->getMessage());
         $error =
        array("status" => "error", "message" =>"Une erreur est survenue au cours du traitement de votre requ�te. Contactez l'administrateur s'il vous pla�t.");
         return $error;
        }
    }



    public function getRatioByRequeteServiceTraitees(Request $request,$idEntite)
    {
       try {

        $inputArray = $request->all();

        //delay: delai de reponse /sejour moyen des request par structures
        // nbrOutdelay: nombre de requetes hors delais

        $startDate = "";  if(isset($inputArray["startDate"]))  { $startDate = $inputArray["startDate"]; }
        $endDate = "";  if(isset($inputArray["endDate"]))  { $endDate = $inputArray["endDate"]; }

        $query = DB::select("select struct.libelle, sum(1+DATEDIFF(dateReponse, dateRequete))/sum(nbreJours) as ratio,
          sum(DATEDIFF(dateReponse, dateRequete))/sum(case when traiteOuiNon = 1 then 1 else 0 end) as delay, 
          sum(case when (DATEDIFF(dateReponse, dateRequete)) > nbreJours then 1 else 0 end) as nbrOutdelay
          from outilcollecte_structure struct,outilcollecte_service ser, outilcollecte_requete req
          where struct.id=ser.idParent
          and req.idPrestation=ser.id
          and req.plainte=0
          and ser.idEntite=$idEntite
          and req.idEntite=$idEntite
          and struct.idEntite=$idEntite
          and struct.active=1
          and dateReponse is not null
          and ser.delaiFixe=1
          group by struct.id,struct.libelle;");

        return ($query);


      } catch(\Illuminate\Database\QueryException $ex){
         \Log::error($ex->getMessage());

        $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requ�te. Veuillez contactez l'administrateur" );
        }catch(\Exception $ex){

         \Log::error($ex->getMessage());
         $error =
        array("status" => "error", "message" =>"Une erreur est survenue au cours du traitement de votre requ�te. Contactez l'administrateur s'il vous pla�t.");
         return $error;
        }
    }

    public function getRatioByRequeteServiceEnCours(Request $request,$idEntite)
    {
       try {

        $inputArray = $request->all();

        $startDate = "";  if(isset($inputArray["startDate"]))  { $startDate = $inputArray["startDate"]; }
        $endDate = "";  if(isset($inputArray["endDate"]))  { $endDate = $inputArray["endDate"]; }

        $query = DB::select("select struct.libelle, sum(1+DATEDIFF(NOW(), dateRequete))/sum(nbreJours) as ratio,
        sum(DATEDIFF(dateReponse, dateRequete))/sum(case when traiteOuiNon = 1 then 1 else 0 end) as delay, 
        sum(case when (DATEDIFF(dateReponse, dateRequete)) > nbreJours then 1 else 0 end) as nbrOutdelay
          from outilcollecte_structure struct,outilcollecte_service ser, outilcollecte_requete req
          where struct.id=ser.idParent
          and req.idPrestation=ser.id
          and req.plainte=0
          and ser.idEntite=$idEntite
          and req.idEntite=$idEntite
          and struct.idEntite=$idEntite
          and struct.active=1
          and dateReponse is null
          and ser.delaiFixe=1
          group by struct.id,struct.libelle;");

        return ($query);


      } catch(\Illuminate\Database\QueryException $ex){
         \Log::error($ex->getMessage());

        $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requ�te. Veuillez contactez l'administrateur" );
        }catch(\Exception $ex){

         \Log::error($ex->getMessage());
         $error =
        array("status" => "error", "message" =>"Une erreur est survenue au cours du traitement de votre requ�te. Contactez l'administrateur s'il vous pla�t.");
         return $error;
        }
    }



    public function getRatioByPlainteServiceTraitees(Request $request,$idEntite)
    {
       try {

        $inputArray = $request->all();

        $startDate = "";  if(isset($inputArray["startDate"]))  { $startDate = $inputArray["startDate"]; }
        $endDate = "";  if(isset($inputArray["endDate"]))  { $endDate = $inputArray["endDate"]; }

        $query = DB::select("select struct.libelle, sum(1+DATEDIFF(dateReponse, dateRequete))/sum(nbreJours) as ratio,
        sum(DATEDIFF(dateReponse, dateRequete))/sum(case when traiteOuiNon = 1 then 1 else 0 end) as delay, 
        sum(case when (DATEDIFF(dateReponse, dateRequete)) > nbreJours then 1 else 0 end) as nbrOutdelay 
        from outilcollecte_structure struct,outilcollecte_service ser, outilcollecte_requete req
          where struct.id=ser.idParent
          and req.idPrestation=ser.id
          and req.plainte=1
          and ser.idEntite=$idEntite
          and req.idEntite=$idEntite
          and struct.idEntite=$idEntite
          and struct.active=1
          and dateReponse is not null
          and ser.delaiFixe=1
          group by struct.id,struct.libelle;");

        return ($query);


      } catch(\Illuminate\Database\QueryException $ex){
         \Log::error($ex->getMessage());

        $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
        }catch(\Exception $ex){

         \Log::error($ex->getMessage());
         $error =
        array("status" => "error", "message" =>"Une erreur est survenue au cours du traitement de votre requête. Contactez l'administrateur s'il vous pla�t.");
         return $error;
        }
    }

    public function getRatioByPlainteServiceEnCours(Request $request,$idEntite)
    {
      
       try {

        $inputArray = $request->all();

        $startDate = "";  if(isset($inputArray["startDate"]))  { $startDate = $inputArray["startDate"]; }
        $endDate = "";  if(isset($inputArray["endDate"]))  { $endDate = $inputArray["endDate"]; }

        $query = DB::select("select struct.libelle, sum(1+DATEDIFF(NOW(), dateRequete))/sum(nbreJours) as ratio,
        sum(DATEDIFF(dateReponse, dateRequete))/sum(case when traiteOuiNon = 1 then 1 else 0 end) as delay, 
        sum(case when (DATEDIFF(dateReponse, dateRequete)) > nbreJours then 1 else 0 end) as nbrOutdelay  
        from outilcollecte_structure struct,outilcollecte_service ser, outilcollecte_requete req
          where struct.id=ser.idParent
          and req.idPrestation=ser.id
          and req.plainte=1
          and ser.idEntite=$idEntite
          and req.idEntite=$idEntite
          and struct.idEntite=$idEntite
          and struct.active=1
          and dateReponse is null
          and ser.delaiFixe=1
          group by struct.id,struct.libelle;");

        return ($query);


      } catch(\Illuminate\Database\QueryException $ex){
         \Log::error($ex->getMessage());

        $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requ�te. Veuillez contactez l'administrateur" );
        }catch(\Exception $ex){

         \Log::error($ex->getMessage());
         $error =
        array("status" => "error", "message" =>"Une erreur est survenue au cours du traitement de votre requ�te. Contactez l'administrateur s'il vous pla�t.");
         return $error;
        }
    }



    public function getRatioByDemandeInfosServiceTraitees(Request $request,$idEntite)
    {
       try {

        $inputArray = $request->all();

        $startDate = "";  if(isset($inputArray["startDate"]))  { $startDate = $inputArray["startDate"]; }
        $endDate = "";  if(isset($inputArray["endDate"]))  { $endDate = $inputArray["endDate"]; }

        $query = DB::select("select struct.libelle, sum(1+DATEDIFF(dateReponse, dateRequete))/sum(nbreJours) as ratio,
        sum(DATEDIFF(dateReponse, dateRequete))/sum(case when traiteOuiNon = 1 then 1 else 0 end) as delay, 
        sum(case when (DATEDIFF(dateReponse, dateRequete)) > nbreJours then 1 else 0 end) as nbrOutdelay  
        from outilcollecte_structure struct,outilcollecte_service ser, outilcollecte_requete req
          where struct.id=ser.idParent
          and req.idPrestation=ser.id
          and req.plainte=2
          and ser.idEntite=$idEntite
          and req.idEntite=$idEntite
          and struct.idEntite=$idEntite
          and struct.active=1
          and dateReponse is not null
          and ser.delaiFixe=1
          group by struct.id,struct.libelle;");

        return ($query);


      } catch(\Illuminate\Database\QueryException $ex){
         \Log::error($ex->getMessage());

        $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requ�te. Veuillez contactez l'administrateur" );
        }catch(\Exception $ex){

         \Log::error($ex->getMessage());
         $error =
        array("status" => "error", "message" =>"Une erreur est survenue au cours du traitement de votre requ�te. Contactez l'administrateur s'il vous pla�t.");
         return $error;
        }
    }

    public function getRatioByDemandeInfosServiceEnCours(Request $request,$idEntite)
    {
      
       try {

        $inputArray = $request->all();

        $startDate = "";  if(isset($inputArray["startDate"]))  { $startDate = $inputArray["startDate"]; }
        $endDate = "";  if(isset($inputArray["endDate"]))  { $endDate = $inputArray["endDate"]; }

        $query = DB::select("select struct.libelle, sum(1+DATEDIFF(NOW(), dateRequete))/sum(nbreJours) as ratio,
        sum(DATEDIFF(dateReponse, dateRequete))/sum(case when traiteOuiNon = 1 then 1 else 0 end) as delay, 
        sum(case when (DATEDIFF(dateReponse, dateRequete)) > nbreJours then 1 else 0 end) as nbrOutdelay  
        from outilcollecte_structure struct,outilcollecte_service ser, outilcollecte_requete req
          where struct.id=ser.idParent
          and req.idPrestation=ser.id
          and req.plainte=2
          and ser.idEntite=$idEntite
          and req.idEntite=$idEntite
          and struct.idEntite=$idEntite
          and struct.active=1
          and dateReponse is null
          and ser.delaiFixe=1
          group by struct.id,struct.libelle;");

        return ($query);


      } catch(\Illuminate\Database\QueryException $ex){
         \Log::error($ex->getMessage());

        $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requ�te. Veuillez contactez l'administrateur" );
        }catch(\Exception $ex){

         \Log::error($ex->getMessage());
         $error =
        array("status" => "error", "message" =>"Une erreur est survenue au cours du traitement de votre requ�te. Contactez l'administrateur s'il vous pla�t.");
         return $error;
        }
    }


    public function getRatioByDemandeInfosPrestationTraitees(Request $request,$idEntite)
    {
       try {

        $inputArray =$request->all();

        $startDate = "";  if(isset($inputArray["startDate"]))  { $startDate = $inputArray["startDate"]; }
        $endDate = "";  if(isset($inputArray["endDate"]))  { $endDate = $inputArray["endDate"]; }

        $query = DB::select("select ser.libelle, sum(1+DATEDIFF(dateReponse, dateRequete))/sum(nbreJours) as ratio
                from outilcollecte_service ser, outilcollecte_requete req
                where req.idPrestation=ser.id
                and req.plainte=2
                and dateReponse is not null
                and ser.idEntite=$idEntite
                and req.idEntite=$idEntite
              
                and ser.delaiFixe=1
                group by ser.id,ser.libelle;");

        return ($query);


      } catch(\Illuminate\Database\QueryException $ex){
         \Log::error($ex->getMessage());

        $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requ�te. Veuillez contactez l'administrateur" );
        }catch(\Exception $ex){

         \Log::error($ex->getMessage());
         $error =
        array("status" => "error", "message" =>"Une erreur est survenue au cours du traitement de votre requ�te. Contactez l'administrateur s'il vous pla�t.");
         return $error;
        }
    }

    public function getRatioByDemandeInfosPrestationEnCours(Request $request,$idEntite)
    {
       try {

        $inputArray = $request->all();

        $startDate = "";  if(isset($inputArray["startDate"]))  { $startDate = $inputArray["startDate"]; }
        $endDate = "";  if(isset($inputArray["endDate"]))  { $endDate = $inputArray["endDate"]; }

        $query = DB::select("select ser.libelle, sum(1+DATEDIFF(NOW(), dateRequete))/sum(nbreJours) as ratio
              from outilcollecte_service ser, outilcollecte_requete req
              where req.idPrestation=ser.id
              and req.plainte=2
              and dateReponse is null
              and ser.idEntite=$idEntite
              and req.idEntite=$idEntite
              and ser.delaiFixe=1
              group by ser.id,ser.libelle;");

        return ($query);


      } catch(\Illuminate\Database\QueryException $ex){
         \Log::error($ex->getMessage());

        $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requ�te. Veuillez contactez l'administrateur" );
        }catch(\Exception $ex){

         \Log::error($ex->getMessage());
         $error =
        array("status" => "error", "message" =>"Une erreur est survenue au cours du traitement de votre requ�te. Contactez l'administrateur s'il vous pla�t.");
         return $error;
        }
    }


} //End Class