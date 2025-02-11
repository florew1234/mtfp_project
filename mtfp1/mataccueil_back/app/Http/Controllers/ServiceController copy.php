<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;
use App\Helpers\Factory\ParamsFactory;

use Illuminate\Http\Request;

use App\Models\Service;
use App\Models\Requete;
use App\Models\Structure;
use App\Models\Institution;
use App\User;
use App\Models\Profil;
use App\Models\Acteur;
use Illuminate\Support\Facades\Storage;
use App\Models\Statthematique;
use App\Models\Pieceprestation;

use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use App\Utilities\FileStorage;
use DB,PDF,Mail,DateTime,DateTimeZone,Str;

class ServiceController extends Controller {


	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('jwt.auth',['except' =>['index','getPrestationByType','getPrestationByStructure', 'search'  , 'afficheRapport', 'afficheRapportInf','afficheRapportPf','ServiceDetailPiece']]);
    }

	public function JourDate($id){
		if($id == 1){
			return "Lundi";
		}else if($id == 2){
			return "Mardi";
		}else if($id == 3){
			return "Mercredi";
		}else if($id == 4){
			return "Jeudi";
		}else if($id == 5){
			return "Vendredi";
		}else if($id == 6){
			return "Samedi";
		}else if($id == 7){
			return "Dimanche";
		}
		return "Erreur date";
	}
	


    public function afficheRapport(Request $req){
		
		// FORMAT DE DATE
		$today = new DateTime('now', new DateTimeZone('UTC'));
		$day_of_week = $today->format('w');
		// $today->modify('- ' . (($day_of_week - 1 + 7) % 7) . 'days');
		$sunday = clone $today;
		// $sunday->modify('+ 4 days');
		$datDebu = $today->format('2022-04-11 00:00:00');
		$datFin = $sunday->format('2022-04-15 23:59:59');
		// $datDebu = $today->format('2000-01-01 00:00:00');
		// $datFin = $sunday->format('Y-m-d 23:59:59');
		$dedate = date_create($datDebu);//Y-m-d
		$fidate = date_create($datFin);
		// return response($datFin);
		//Liste des structures qui ont enregistrée une requête
		// $structures = Structure::where("idParent",0)->get();
		$structures = DB::select("SELECT DISTINCT aff.idStructure, stru.sigle, stru.idParent
									FROM outilcollecte_requete req
									LEFT JOIN outilcollecte_affectation aff ON req.id = aff.idRequete
									LEFT JOIN outilcollecte_structure stru ON stru.id = aff.idStructure
									WHERE stru.idEntite = 1 
									AND stru.idParent = 0
									AND req.plainte = 1
									AND stru.active=1
									AND aff.dateAffectation BETWEEN '$datDebu' and '$datFin';");
		//  return response()->json($structures);  
		//$2y$10$oEl7VBtvNWjyDuCxXLztUeYHO5Vhbengjlt.z39cF7N/TgHo8AmVm

		$datas=array();
		$i=0;
		foreach ($structures as $st) {
			$structure_id = $st->idStructure;
			$stats = DB::select("SELECT count(*) total,
									SUM(CASE WHEN traiteOuiNon = 1 then 1 else 0 end) totalTraite,
									SUM(CASE WHEN traiteOuiNon = 0 then 1 else 0 end) totalNTraite
									FROM outilcollecte_requete
									WHERE outilcollecte_requete.plainte = 1
									AND outilcollecte_requete.id IN 
									(
										SELECT outilcollecte_affectation.idRequete
										FROM outilcollecte_affectation
										WHERE outilcollecte_affectation.dateAffectation BETWEEN '$datDebu' and '$datFin'
										AND outilcollecte_affectation.idStructure IN 
										(
											SELECT outilcollecte_structure.id 
											FROM outilcollecte_structure
											WHERE outilcollecte_structure.idParent = $structure_id
											OR outilcollecte_structure.id = $structure_id
										)
									);");
			$Tplainte = $stats[0]->total;
			$traitesTra = $stats[0]->totalTraite;
			$traitesNTra = $stats[0]->totalNTraite;
			$pourcent = 0;
			if($Tplainte!=0 ){
				$pourcent=($traitesNTra*100)/$Tplainte;
			}

			$datas[$i]["strcuture"] = $st->sigle;
			$datas[$i]["Tplainte"]=$Tplainte;
			$datas[$i]["plainteTrai"]=$traitesTra;
			$datas[$i]["plainteNonTrai"]=$traitesNTra;
			$datas[$i]["pourcentPNT"]=round($pourcent,2);
			$i++;
		}

		//REQUÊTES ET DEMANDE D'INFORMATIONS
		$structures = Requete::join('outilcollecte_affectation','outilcollecte_affectation.idRequete','outilcollecte_requete.id')
								->join('outilcollecte_structure','outilcollecte_structure.id','outilcollecte_affectation.idStructure')
								->where('outilcollecte_structure.idParent',0)
								->where('outilcollecte_structure.idEntite',1)
								->where('outilcollecte_structure.active',1)
								->whereIn('outilcollecte_requete.plainte',[0,2])
								->whereBetween('outilcollecte_affectation.dateAffectation',[$dedate,$fidate])
								->select('outilcollecte_affectation.idStructure','outilcollecte_structure.sigle')
								->distinct()
								->get();


		
		$datasreq=array();
		$i=0;
		foreach ($structures as $st) {
			$structure_id = $st->idStructure;
			$stats = DB::select("SELECT count(*) total,
									SUM(CASE WHEN traiteOuiNon = 1 then 1 else 0 end) totalTraite,
									SUM(CASE WHEN traiteOuiNon = 0 then 1 else 0 end) totalNTraite
									FROM outilcollecte_requete
									WHERE outilcollecte_requete.plainte IN (0,2)
									AND outilcollecte_requete.id IN 
									(
										SELECT outilcollecte_affectation.idRequete
										FROM outilcollecte_affectation
										WHERE outilcollecte_affectation.dateAffectation BETWEEN '$datDebu' and '$datFin'
										AND outilcollecte_affectation.idStructure IN 
										(
											SELECT outilcollecte_structure.id 
											FROM outilcollecte_structure
											WHERE outilcollecte_structure.idParent = $structure_id
											OR outilcollecte_structure.id = $structure_id
										)
									);");

			$Treq = $stats[0]->total;
			$reqtraitesTra = $stats[0]->totalTraite;
			$reqtraitesNTra = $stats[0]->totalNTraite;
			$pourcent = 0;
			if($Treq!=0 ){
				$pourcent=($reqtraitesNTra*100)/$Treq;
			}
			$datasreq[$i]["strcuture"]=$st->sigle;
			$datasreq[$i]["Treq"]=$Treq;
			$datasreq[$i]["reqTrai"]=$reqtraitesTra;
			$datasreq[$i]["reqNonTrai"]=$reqtraitesNTra;
			$datasreq[$i]["reqpourcentPNT"]=round($pourcent,2);
			$i++;
		}

		//Point ministériel 
		$dataMin=array();
		$i=0;
		// $min = Institution::all();
		$min = Requete::join('outilcollecte_affectation','outilcollecte_affectation.idRequete','outilcollecte_requete.id')
							->join('outilcollecte_institution','outilcollecte_institution.id','outilcollecte_affectation.idEntite')
							->whereBetween('outilcollecte_affectation.dateAffectation',[$dedate,$fidate])
							->select('outilcollecte_affectation.idEntite','outilcollecte_institution.sigle')
							->distinct()
							->get();
		//HORS-DELAI : 1- pas delai fixe; 2- Traité dans les délais 3- Traité hors délai
		// foreach($min as $m){
			// $entiteId = $m->idEntite;
			// $statsDIR = DB::select("SELECT count(*) total,
			// 						SUM(CASE WHEN traiteOuiNon = 1 then 1 else 0 end) totalTraite,
			// 						SUM(CASE WHEN traiteOuiNon = 0 then 1 else 0 end) totalNTraite
			// 						FROM outilcollecte_requete
			// 						WHERE outilcollecte_requete.plainte IN (0,2)
			// 						AND outilcollecte_requete.id IN 
			// 						(
			// 							SELECT aff.idRequete
			// 							FROM outilcollecte_affectation aff
			// 							LEFT JOIN outilcollecte_structure stru ON stru.id = aff.idStructure
			// 							WHERE stru.idEntite = $entiteId
			// 							AND aff.dateAffectation BETWEEN '$datDebu' and '$datFin'
			// 						);");
			
			// $TotalDir_ = $statsDIR[0]->total;
			// $Totaldir_Tr = $statsDIR[0]->totalTraite;
			// $Totaldir_NTr = $statsDIR[0]->totalNTraite;
			// $pourcentDIR_NTr = 0;
			// if($TotalDir_!=0 ){
			// 	$pourcentDIR_NTr=($Totaldir_NTr*100)/$TotalDir_;
			// }
			// $statsPL = DB::select("SELECT count(*) total,
			// 						SUM(CASE WHEN traiteOuiNon = 1 then 1 else 0 end) totalTraite,
			// 						SUM(CASE WHEN traiteOuiNon = 0 then 1 else 0 end) totalNTraite
			// 						FROM outilcollecte_requete
			// 						WHERE outilcollecte_requete.plainte = 1
			// 						AND outilcollecte_requete.id IN 
			// 						(
			// 							SELECT aff.idRequete
			// 							FROM outilcollecte_affectation aff
			// 							LEFT JOIN outilcollecte_structure stru ON stru.id = aff.idStructure
			// 							WHERE stru.idEntite = $entiteId
			// 							AND aff.dateAffectation BETWEEN '$datDebu' and '$datFin'
			// 						);");
			// 						// return response()->json($statsPL);
			// $TotalPL_ = $statsPL[0]->total;
			// $TotalPL_Tr = $statsPL[0]->totalTraite;
			// $TotalPL_NTr = $statsPL[0]->totalNTraite;
			// $pourcentPL_NTr = 0;
			// if($TotalPL_!=0 ){
			// 	$pourcentPL_NTr=($TotalPL_NTr*100)/$TotalPL_;
			// }
			
			// $dataMin[$i]["entite"]=$m->sigle;
			// $dataMin[$i]["TotalDir_"]=$TotalDir_;
			// $dataMin[$i]["Totaldir_Tr"]=$Totaldir_Tr;
			// $dataMin[$i]["Totaldir_NTr"]=$Totaldir_NTr;
			// $dataMin[$i]["pourcentDIR_NTr"] = round($pourcentDIR_NTr,2);

			// $dataMin[$i]["TotalPL_"]=$TotalPL_;
			// $dataMin[$i]["TotalPL_Tr"]=$TotalPL_Tr;
			// $dataMin[$i]["TotalPL_NTr"]=$TotalPL_NTr;
			// $dataMin[$i]["pourcentPL_NTr"] = round($pourcentPL_NTr,2);

			// $i++;
		// }
		//Fin : Point ministériel 
		
		//Point focal communal 
		//Liste des communes qui ont enregistré des requêtes ayant pour profil "POINT FOCAL COMMUNAL "
		$reqCom = Requete::join('outilcollecte_users','outilcollecte_users.id','outilcollecte_requete.created_by')
							->join('outilcollecte_acteur','outilcollecte_acteur.id','outilcollecte_users.idagent')
							->join('outilcollecte_commune','outilcollecte_commune.id','outilcollecte_acteur.idCom')
							->join('outilcollecte_profil','outilcollecte_profil.id','outilcollecte_users.idprofil')
							->where('outilcollecte_profil.pointfocalcom',1)
							// ->where('outilcollecte_requete.plainte',[1,2])
							->select('outilcollecte_commune.*')
							->distinct()
							->get();
		$dataUser=array();
		$i=0;		
		// if($reqCom!= null){
		// 	foreach($reqCom as $com){

		// 		$statsDIR = DB::select("SELECT  count(*) total,
		// 								SUM(CASE WHEN traiteOuiNon = 1 then 1 else 0 end) totalTraite,
		// 								SUM(CASE WHEN traiteOuiNon = 0 then 1 else 0 end) totalNTraite
		// 								FROM outilcollecte_requete, outilcollecte_users,outilcollecte_acteur,outilcollecte_profil,outilcollecte_commune
		// 								WHERE outilcollecte_requete.created_by = outilcollecte_users.id
		// 								AND outilcollecte_acteur.id = outilcollecte_users.idagent
		// 								AND outilcollecte_commune.id = outilcollecte_acteur.idCom
		// 								AND outilcollecte_profil.id = outilcollecte_users.idprofil
		// 								AND outilcollecte_commune.id = $com->id
		// 								AND outilcollecte_requete.plainte IN (0,2)
		// 								AND outilcollecte_profil.pointfocalcom = 1
		// 								AND outilcollecte_requete.id IN 
		// 									(
		// 										SELECT outilcollecte_affectation.idRequete
		// 										FROM outilcollecte_affectation
		// 										WHERE outilcollecte_affectation.dateAffectation BETWEEN '$datDebu' and '$datFin'
		// 									);");
			
		// 		$TotalDir_ 		= $statsDIR[0]->total;
		// 		$Totaldir_Tr 	= $statsDIR[0]->totalTraite;
		// 		$Totaldir_NTr 	= $statsDIR[0]->totalNTraite;
		// 		$pourcentDIR_NTr = 0;
		// 		if($TotalDir_!=0 ){
		// 			$pourcentDIR_NTr=($Totaldir_NTr*100)/$TotalDir_;
		// 		}
		// 		$statsPL = DB::select("SELECT  count(*) total,
		// 								SUM(CASE WHEN traiteOuiNon = 1 then 1 else 0 end) totalTraite,
		// 								SUM(CASE WHEN traiteOuiNon = 0 then 1 else 0 end) totalNTraite
		// 								FROM outilcollecte_requete, outilcollecte_users,outilcollecte_acteur,outilcollecte_profil,outilcollecte_commune
		// 								WHERE outilcollecte_requete.created_by = outilcollecte_users.id
		// 								AND outilcollecte_acteur.id = outilcollecte_users.idagent
		// 								AND outilcollecte_commune.id = outilcollecte_acteur.idCom
		// 								AND outilcollecte_profil.id = outilcollecte_users.idprofil
		// 								AND outilcollecte_commune.id = $com->id
		// 								AND outilcollecte_requete.plainte = 1
		// 								AND outilcollecte_profil.pointfocalcom = 1
		// 								AND outilcollecte_requete.id IN 
		// 									(
		// 										SELECT outilcollecte_affectation.idRequete
		// 										FROM outilcollecte_affectation
		// 										WHERE outilcollecte_affectation.dateAffectation BETWEEN '$datDebu' and '$datFin'
		// 									);");
				
		// 		$TotalPL_ = $statsPL[0]->total;
		// 		$TotalPL_Tr = $statsPL[0]->totalTraite;
		// 		$TotalPL_NTr = $statsPL[0]->totalNTraite;

		// 		$pourcentPL_NTr = 0;
		// 		if($TotalPL_!=0 ){
		// 			$pourcentPL_NTr=($TotalPL_NTr*100)/$TotalPL_;
		// 		}

		// 		$dataUser[$i]["nomcom"]= $com->libellecom;
		// 		$dataUser[$i]["TotalDir_"]=$TotalDir_;
		// 		$dataUser[$i]["Totaldir_Tr"]=$Totaldir_Tr;
		// 		$dataUser[$i]["Totaldir_NTr"]=$Totaldir_NTr;
		// 		$dataUser[$i]["pourcentDIR_NTr"]=round($pourcentDIR_NTr,2);

		// 		$dataUser[$i]["TotalPL_"]=$TotalPL_;
		// 		$dataUser[$i]["TotalPL_Tr"]=$TotalPL_Tr;
		// 		$dataUser[$i]["TotalPL_NTr"]=$TotalPL_NTr;
		// 		$dataUser[$i]["pourcentPL_NTr"]=round($pourcentPL_NTr,2);

		// 		$i++;
		// 	}
		// }
		//Fin : Point focal communal
		$periode = self::JourDate($fidate->format('w'))." ".$fidate->format('d/m/Y');
		$title = "RAPPORT_P_R_D_RECUES_A_LA_DATE_DU_".$fidate->format('YmdHis').".pdf";

		$logo= env('ASSET_URL').'/img/logo-mtfp.svg';
        PDF::loadView("rapport",['logo'=>$logo,'datas'=>$datas,'datasreq'=>$datasreq,'datasm'=>$dataMin,'dataspf'=>$dataUser,'periode'=>$periode])->setPaper('a4','landscape')->save(Storage::path("public/rapport/").$title);
		try {
			//code...
			if(trans('auth.mode') != 'test'){
				if($req->get('giwu')){
					if($req->get('giwu') == "dsi"){
						$tra = trans('SendMail.Mails_send_me_dsi');
					}else{
						$tra = trans('SendMail.Mails_send_me'); //Envoi personnel pour checker avant l'envoi general
					}
				}else{
					$tra = trans('SendMail.Mails_receive_rapport');
				}
				foreach($tra as $mail=>$nam){
					Mail::send("mail.rapportmail", ['perio'=>$periode,'destinataire'=>$nam], function($message)use ($nam,$mail,$title){
						// $message->to($email);
						$message->from("mtfp.usager@gouv.bj", "SRU")
						->subject("Rapport suivi traitement des plaintes reçues");
						$message->attach(Storage::path("public/rapport/".$title), [
								'as' => $title,
								'mime' => 'application/pdf',
							]);
						$message->to($mail,$nam);
					});
					var_dump("Rapport plainte envoyé avec succès : ".$mail." - ".$nam." le ".date('Y-m-d-h-i-s'));
					sleep(5);
				}
			}
		} catch (\Throwable $th) {
			return response()->json($th->getMessage());
		}
		return response()->json("Rapport envoyé avec succès");
    }

    public function afficheRapportPf(Request $req){

		// FORMAT DE DATE
		$today = new DateTime('now', new DateTimeZone('UTC'));
		$day_of_week = $today->format('w');
		// $today->modify('- ' . (($day_of_week - 1 + 7) % 7) . 'days');
		$sunday = clone $today;
		
		$datDebuday = $today->format('Y-m-d 00:00:00');
		$datDebu = $today->format('2000-01-01 00:00:00');
		$datFin = $sunday->format('Y-m-d 23:59:59');
		$dedate = date_create($datDebu);//Y-m-d
		$fidate = date_create($datFin);
		
		// $premierJour = strftime("2022-04-01 00:00:00", strtotime("this week"));
		$premierJour = strftime("%Y-%m-%d 00:00:00", strtotime("this week"));
		$premdate = date_create($premierJour);
		//Point focal communal 
		//Liste des communes qui ont enregistré des requêtes ayant pour profil "POINT FOCAL COMMUNAL "
		$reqCom = Requete::join('outilcollecte_users','outilcollecte_users.id','outilcollecte_requete.created_by')
							->join('outilcollecte_acteur','outilcollecte_acteur.id','outilcollecte_users.idagent')
							->join('outilcollecte_profil','outilcollecte_profil.id','outilcollecte_users.idprofil')
							->where('outilcollecte_profil.pointfocalcom',1)
							->select('outilcollecte_acteur.*')
							->distinct()
							->get();
		$dataUserDay=array();
		$dataUser=array();
		$i=0;
		if($reqCom!= null){
			foreach($reqCom as $com){

				$statsDIR = DB::select("SELECT  count(*) total,
										SUM(CASE WHEN traiteOuiNon = 1 then 1 else 0 end) totalTraite,
										SUM(CASE WHEN traiteOuiNon = 0 then 1 else 0 end) totalNTraite
										FROM outilcollecte_requete, outilcollecte_users,outilcollecte_acteur,outilcollecte_profil
										WHERE outilcollecte_requete.created_by = outilcollecte_users.id
										AND outilcollecte_acteur.id = outilcollecte_users.idagent
										AND outilcollecte_profil.id = outilcollecte_users.idprofil
										AND outilcollecte_acteur.id = $com->id
										AND outilcollecte_requete.plainte IN (0,2)
										AND outilcollecte_profil.pointfocalcom = 1
										AND outilcollecte_requete.id IN 
											(
												SELECT outilcollecte_affectation.idRequete
												FROM outilcollecte_affectation
												WHERE outilcollecte_affectation.dateAffectation BETWEEN '$datDebuday' and '$datFin'
											);");
			
				$TotalDir_ 		= $statsDIR[0]->total;
				$Totaldir_Tr 	= $statsDIR[0]->totalTraite;
				$Totaldir_NTr 	= $statsDIR[0]->totalNTraite;
				$pourcentDIR_NTr = 0;
				if($TotalDir_!=0 ){
					$pourcentDIR_NTr=($Totaldir_NTr*100)/$TotalDir_;
				}
				$statsPL = DB::select("SELECT  count(*) total,
										SUM(CASE WHEN traiteOuiNon = 1 then 1 else 0 end) totalTraite,
										SUM(CASE WHEN traiteOuiNon = 0 then 1 else 0 end) totalNTraite
										FROM outilcollecte_requete, outilcollecte_users,outilcollecte_acteur,outilcollecte_profil
										WHERE outilcollecte_requete.created_by = outilcollecte_users.id
										AND outilcollecte_acteur.id = outilcollecte_users.idagent
										AND outilcollecte_profil.id = outilcollecte_users.idprofil
										AND outilcollecte_acteur.id = $com->id
										AND outilcollecte_requete.plainte = 1
										AND outilcollecte_profil.pointfocalcom = 1
										AND outilcollecte_requete.id IN 
											(
												SELECT outilcollecte_affectation.idRequete
												FROM outilcollecte_affectation
												WHERE outilcollecte_affectation.dateAffectation BETWEEN '$datDebuday' and '$datFin'
											);");
				
				$TotalPL_ = $statsPL[0]->total;
				$TotalPL_Tr = $statsPL[0]->totalTraite;
				$TotalPL_NTr = $statsPL[0]->totalNTraite;

				$pourcentPL_NTr = 0;
				if($TotalPL_!=0 ){
					$pourcentPL_NTr=($TotalPL_NTr*100)/$TotalPL_;
				}
				$dataUserDay[$i]["nomcom"]= $com->nomprenoms;
				$dataUserDay[$i]["TotalDir_"]=$TotalDir_;
				$dataUserDay[$i]["Totaldir_Tr"]=$Totaldir_Tr;
				$dataUserDay[$i]["Totaldir_NTr"]=$Totaldir_NTr;
				$dataUserDay[$i]["pourcentDIR_NTr"]=round($pourcentDIR_NTr,2);

				$dataUserDay[$i]["TotalPL_"]=$TotalPL_;
				$dataUserDay[$i]["TotalPL_Tr"]=$TotalPL_Tr;
				$dataUserDay[$i]["TotalPL_NTr"]=$TotalPL_NTr;
				$dataUserDay[$i]["pourcentPL_NTr"]=round($pourcentPL_NTr,2);

				$i++;
			}
			$i=0;
			// 
			foreach($reqCom as $com){

				$statsDIR = DB::select("SELECT  count(*) total,
										SUM(CASE WHEN traiteOuiNon = 1 then 1 else 0 end) totalTraite,
										SUM(CASE WHEN traiteOuiNon = 0 then 1 else 0 end) totalNTraite
										FROM outilcollecte_requete, outilcollecte_users,outilcollecte_acteur,outilcollecte_profil
										WHERE outilcollecte_requete.created_by = outilcollecte_users.id
										AND outilcollecte_acteur.id = outilcollecte_users.idagent
										AND outilcollecte_profil.id = outilcollecte_users.idprofil
										AND outilcollecte_acteur.id = $com->id
										AND outilcollecte_requete.plainte IN (0,2)
										AND outilcollecte_profil.pointfocalcom = 1
										AND outilcollecte_requete.id IN 
											(
												SELECT outilcollecte_affectation.idRequete
												FROM outilcollecte_affectation
												WHERE outilcollecte_affectation.dateAffectation BETWEEN '$datDebu' and '$datFin'
											);");
			
				$TotalDir_ 		= $statsDIR[0]->total;
				$Totaldir_Tr 	= $statsDIR[0]->totalTraite;
				$Totaldir_NTr 	= $statsDIR[0]->totalNTraite;
				$pourcentDIR_NTr = 0;
				if($TotalDir_!=0 ){
					$pourcentDIR_NTr=($Totaldir_NTr*100)/$TotalDir_;
				}
				$statsPL = DB::select("SELECT  count(*) total,
										SUM(CASE WHEN traiteOuiNon = 1 then 1 else 0 end) totalTraite,
										SUM(CASE WHEN traiteOuiNon = 0 then 1 else 0 end) totalNTraite
										FROM outilcollecte_requete, outilcollecte_users,outilcollecte_acteur,outilcollecte_profil
										WHERE outilcollecte_requete.created_by = outilcollecte_users.id
										AND outilcollecte_acteur.id = outilcollecte_users.idagent
										AND outilcollecte_profil.id = outilcollecte_users.idprofil
										AND outilcollecte_acteur.id = $com->id
										AND outilcollecte_requete.plainte = 1
										AND outilcollecte_profil.pointfocalcom = 1
										AND outilcollecte_requete.id IN 
											(
												SELECT outilcollecte_affectation.idRequete
												FROM outilcollecte_affectation
												WHERE outilcollecte_affectation.dateAffectation BETWEEN '$datDebu' and '$datFin'
											);");
				
				$TotalPL_ = $statsPL[0]->total;
				$TotalPL_Tr = $statsPL[0]->totalTraite;
				$TotalPL_NTr = $statsPL[0]->totalNTraite;

				$pourcentPL_NTr = 0;
				if($TotalPL_!=0 ){
					$pourcentPL_NTr=($TotalPL_NTr*100)/$TotalPL_;
				}

				$dataUser[$i]["nomcom"]= $com->nomprenoms;
				$dataUser[$i]["TotalDir_"]=$TotalDir_;
				$dataUser[$i]["Totaldir_Tr"]=$Totaldir_Tr;
				$dataUser[$i]["Totaldir_NTr"]=$Totaldir_NTr;
				$dataUser[$i]["pourcentDIR_NTr"]=round($pourcentDIR_NTr,2);

				$dataUser[$i]["TotalPL_"]=$TotalPL_;
				$dataUser[$i]["TotalPL_Tr"]=$TotalPL_Tr;
				$dataUser[$i]["TotalPL_NTr"]=$TotalPL_NTr;
				$dataUser[$i]["pourcentPL_NTr"]=round($pourcentPL_NTr,2);

				$i++;
			}
		}
		//Fin : Point focal communal
		// $structures = Structure::where("idParent",0)->get();
		$structures = DB::select("SELECT DISTINCT aff.idStructure, stru.sigle, stru.idParent
									FROM outilcollecte_requete req
									LEFT JOIN outilcollecte_affectation aff ON req.id = aff.idRequete
									LEFT JOIN outilcollecte_structure stru ON stru.id = aff.idStructure
									WHERE stru.idEntite = 1 
									AND stru.idParent = 0
									AND stru.active=1
									AND aff.dateAffectation BETWEEN '$premierJour' and '$datFin';
									");
		return response()->json([$premierJour,$datFin,$structures]);
		$datas=array();
		$i=0;
		foreach ($structures as $st) {
			$structure_id = $st->idStructure;
			$stats = DB::select("SELECT count(*) total
									FROM outilcollecte_requete
									WHERE outilcollecte_requete.traiteOuiNon = 0
									AND outilcollecte_requete.id IN 
									(
										SELECT outilcollecte_affectation.idRequete
										FROM outilcollecte_affectation
										WHERE outilcollecte_affectation.dateAffectation BETWEEN '$premierJour' and '$datFin'
										AND outilcollecte_affectation.idStructure IN 
										(
											SELECT outilcollecte_structure.id 
											FROM outilcollecte_structure
											WHERE outilcollecte_structure.idParent = $structure_id
											OR outilcollecte_structure.id = $structure_id
										)
									);");

			$Tplainte = $stats[0]->total;
			$datas[$i]["strcuture"] = $st->sigle;
			$datas[$i]["idStructure"] = $st->idStructure;
			$datas[$i]["Tplainte"]=$Tplainte;
			//Charger les services
			$stats_serv = DB::select("SELECT DISTINCT ser.id, ser.libelle, count(*) total
						FROM outilcollecte_requete req
						LEFT JOIN outilcollecte_service ser ON req.idPrestation = ser.id
						AND req.traiteOuiNon = 0
						AND req.id IN 
						(
							SELECT outilcollecte_affectation.idRequete
							FROM outilcollecte_affectation
							WHERE outilcollecte_affectation.dateAffectation BETWEEN '$premierJour' and '$datFin'
							AND outilcollecte_affectation.idStructure IN 
							(
								SELECT outilcollecte_structure.id 
								FROM outilcollecte_structure
								WHERE outilcollecte_structure.idParent = $structure_id
								OR outilcollecte_structure.id = $structure_id
							)
						)
						GROUP BY ser.id, ser.libelle;");
			$recu_Serv = "";
			foreach($stats_serv as $serv){
				if($serv->libelle != ""){
					$recu_Serv .= " * ".$serv->libelle." ($serv->total) <br>";
				}
			}
			$datas[$i]["serv"]=$recu_Serv;
			$i++;
		}
		// return response()->json($datas);
		$peri = self::JourDate($premdate->format('w'))." ".$premdate->format('d/m/Y')." au ".self::JourDate($fidate->format('w'))." ".$fidate->format('d/m/Y');
		$periode = self::JourDate($fidate->format('w'))." ".$fidate->format('d/m/Y');
		$title = "S_T_PF_DES_DIRP_A_LA_DATE_DU_".$fidate->format('YmdHis').".pdf";

		$logo= env('ASSET_URL').'/img/logo-mtfp.svg';
        PDF::loadView("rapportpf",['logo'=>$logo,'datas'=>$datas,'dataspf'=>$dataUser,'dataspfday'=>$dataUserDay,'periode'=>$periode,'peri'=>$peri])->setPaper('a4','landscape')->save(Storage::path("public/rapport/").$title);
		try {
			if(trans('auth.mode') != 'test'){
				if($req->get('giwu')){
					$tra = trans('SendMail.Mails_send_me'); //Envoi personnel pour checker avant l'envoi general
				}else{
					$tra = trans('SendMail.Mails_send_me_sru');
				}
				foreach($tra as $mail=>$nam){
					Mail::send("mail.rapportmailpf", ['perio'=>$periode,'destinataire'=>$nam], function($message)use ($nam,$mail,$title){
						$message->from("mtfp.usager@gouv.bj", "Service informatique MTFP")
						->subject("Rapport des DIR_P reçues des P.F.C.");
						$message->attach(Storage::path("public/rapport/".$title), [
								'as' => $title,
								'mime' => 'application/pdf',
							]);
						$message->to($mail,$nam);
					});
					var_dump("Rapport plainte pf envoyé avec succès : ".$mail." - ".$nam." le ".date('Y-m-d-h-i-s'));
					sleep(5);
				}
			}
		} catch (\Throwable $th) {
			return response()->json($th->getMessage());
		}
		return response()->json("Rapport envoyé avec succès");
    }

	public function NomAgent($id){
		$agn = Acteur::where('id',$id)->first();
		if($agn != null){
            return $agn->nomprenoms;
		}else{
			return "Acteur inconnu";
		}
	}

    public function afficheRapportInf(){

		// FORMAT DE DATE
		$today = new DateTime('now', new DateTimeZone('UTC'));
		$day_of_week = $today->format('w');
		$today->modify('- ' . (($day_of_week - 1 + 7) % 7) . 'days');
		$sunday = clone $today;
		$sunday->modify('+ 4 days');
		//$dedate = date_create($today->format('Y-m-d'));
		//$fidate = date_create($sunday->format('Y-m-d 23:59:59'));
        $ann = date('Y');
		$dedate = date_create($today->format($ann.'-01-01 00:00:00'));//Y-m-d
        $fidate = date_create($sunday->format('Y-m-d 23:59:59'));
		// FORMAT DE DATE
		
		$structures = Structure::where("idParent",0)->get();
		$datas=array();
		$i=0;
		foreach ($structures as $st) {
			$structure_id = $st->id;
			$en_cours=Requete::whereHas("affectation",function($req) use ($structure_id,$dedate,$fidate){
				return $req->where('idStructure',$structure_id)->whereBetween('dateAffectation',[$dedate,$fidate]);
			})->where('traiteOuiNon',0)->where('plainte',2)->count();

			$en_cours_hd = Requete::whereHas("affectation",function($req) use ($structure_id,$dedate,$fidate){
				return $req->where('idStructure',$structure_id)->whereBetween('dateAffectation',[$dedate,$fidate]);
			})->where('traiteOuiNon',0)->where('plainte',2)->where('horsDelai',1)->count();
			
			$pourcent_EnCourshd = 0;
			if($en_cours!=0 ){
				$pourcent_EnCourshd=($en_cours_hd*100)/$en_cours;
			}
			
			$traites=Requete::whereHas("affectation",function($req) use ($structure_id,$dedate,$fidate){
				return $req->where('idStructure',$structure_id)->whereBetween('dateAffectation',[$dedate,$fidate]);
			})->where('traiteOuiNon',1)->where('plainte',2)->count();
			
			$traites_hd = Requete::whereHas("affectation",function($req) use ($structure_id,$dedate,$fidate){
				return $req->where('idStructure',$structure_id)->whereBetween('dateAffectation',[$dedate,$fidate]);
			})->where('traiteOuiNon',1)->where('plainte',2)->where('horsDelai',1)->count();
			
			$pourcent_Traitehd = 0;
			if($traites!=0 ){
				$pourcent_Traitehd=($traites_hd*100)/$traites;
			}
			
			$total=$en_cours+$traites;
			
			$datas[$i]["structure"]=$st->sigle;
			$datas[$i]["en_cours"]=$en_cours;
			$datas[$i]["en_cours_hd"]=$en_cours_hd;
			$datas[$i]["pourcent_EnCourshd"]=$pourcent_EnCourshd;
			$datas[$i]["traites"]=$traites;
			$datas[$i]["traites_hd"]=$traites_hd;
			$datas[$i]["pourcent_Traitehd"]=$pourcent_Traitehd;
			$datas[$i]["total"]=$total;
			$i++;
		}
		$periode = self::JourDate($fidate->format('w'))." ".$fidate->format('d/m/Y');
		$title = "S_T_DES_DEMANDES_D_INFORMATIONS_A_LA_DATE_DU_".$fidate->format('YmdHis').".pdf";

		$logo= env('ASSET_URL').'/img/logo-mtfp.svg';
        PDF::loadView("rapportInf",['logo'=>$logo,'datas'=>$datas,'periode'=>$periode])->setPaper('a4','landscape')->save(Storage::path("public/rapport/").$title);
		
		try {
			//code...
			if(trans('auth.mode') != 'test'){
				foreach(trans('SendMail.Mails_receive_rapport') as $mail=>$nam){
					Mail::send("mail.rapportmailInf", ['perio'=>$periode,'destinataire'=>$nam], function($message)use ($nam,$mail,$title){
						$message->from("mtfp.usager@gouv.bj", "SRU")
						->subject("Rapport suivi traitement des demandes d'information")
						->attach(Storage::path("public/rapport/".$title), [
								'as' => $title,
								'mime' => 'application/pdf',
							]);
						$message->to($mail, $nam);
					});
					var_dump("Rapport infos envoyé avec succès : ".$mail." - ".$nam." le ".date('Y-m-d-h-i-s'));
					sleep(5);
				}
			}
		} catch (\Throwable $th) {
			return response()->json($th->getMessage());
		}
		// dd(env("MAIL_FROM_NAME"),env('MAIL_FROM_ADDRESS'),env('ASSET_URL'),env('MAIL_PASSWORD'),"aaaaaaaaaaaa");
		
		return response()->json("Rapport envoyé avec succès");
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($idEntite)
	{
		try {
			$ServiceSearch = Service::where('idEntite',$idEntite)->orderBy('libelle')->get();
			
			foreach ($ServiceSearch as $Service) {
				$Service->service_parent;
				$Service->listepieces;
			}

			return $ServiceSearch;

		} catch(\Illuminate\Database\QueryException $ex){
            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contacter l'administrateur" );
        }catch(\Exception $ex){

            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contacter l'administrateur" );
            return $error;
        }
	}

	public function ServiceDetailPiece($idServ)
	{
		$ServiceSearch = Service::with(['listepieces','service_parent'])->where('id',$idServ)->get();
		return $ServiceSearch;
	}

	public function getByStructure($idStructure)
	{
		try {
			$ServiceSearch = Service::where('idParent',$idStructure)->orderBy('libelle')->get();
			
			foreach ($ServiceSearch as $Service) {
				$Service->service_parent;
				$Service->listepieces;
			}

			return $ServiceSearch;

		} catch(\Illuminate\Database\QueryException $ex){
            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contacter l'administrateur" );
        }catch(\Exception $ex){

            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contacter l'administrateur" );
            return $error;
        }
	}
		/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getByCreator(Request $request)
	{
        try {
            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            $ServiceSearch = Service::where('created_by', $userconnectdata->id)->orderBy('libelle')->get();
            
            foreach ($ServiceSearch as $Service) {
                $Service->service_parent;
                $Service->listepieces;
            }

            return $ServiceSearch;
        } catch (\Illuminate\Database\QueryException $ex) {
            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contacter l'administrateur" );
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contacter l'administrateur" );
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
		try{
		 //recup les champs fournis
	        $inputArray =  $request->all();

			//verifie les champs fournis
          if (!( isset($inputArray['libelle']) 
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }

			$libelle=null;
			if(isset($inputArray['libelle'])){$libelle = $inputArray['libelle'];}
			
			$consiste=null;
			if(isset($inputArray['consiste'])){$consiste = $inputArray['consiste'];}
			
			$interetDemandeur=null;
			if(isset($inputArray['interetDemandeur'])){$interetDemandeur = $inputArray['interetDemandeur'];}
			
			$obligatoire=null;
			if(isset($inputArray['obligatoire'])){$obligatoire = $inputArray['obligatoire'];}
			
			$echeance=null;
			if(isset($inputArray['echeance'])){$echeance = $inputArray['echeance'];}
			
			$interetDemanderTot=null;
			if(isset($inputArray['interetDemanderTot'])){$interetDemanderTot = $inputArray['interetDemanderTot'];}
			
			$dateredac=null;
			if(isset($inputArray['dateredac'])){
				$dateredac = $inputArray['dateredac'];
				$dateredac = ParamsFactory::convertToDateTimeForSearch($dateredac, false);
				$dateredac = $dateredac->toDateTimeString();
			}
			
			$nomSousG=null;
			if(isset($inputArray['nomSousG'])){$nomSousG = $inputArray['nomSousG'];}
			
			$nomPresidentSG=null;
			if(isset($inputArray['nomPresidentSG'])){$nomPresidentSG = $inputArray['nomPresidentSG'];}
			
			$contactPresidentSG=null;
			if(isset($inputArray['contactPresidentSG'])){$contactPresidentSG = $inputArray['contactPresidentSG'];}
			
			$piecesAFournir=null;
			if(isset($inputArray['piecesAFournir'])){$piecesAFournir = $inputArray['piecesAFournir'];}
			
			$idParent=null;
			if(isset($inputArray['idParent'])){$idParent = $inputArray['idParent'];}
			
			$idType=null;
			if(isset($inputArray['idType'])){$idType = $inputArray['idType'];}
			
			$delai=null;
			if(isset($inputArray['delai'])){$delai = $inputArray['delai'];}
			
			$hide_for_public=false;
			if(isset($inputArray['hide_for_public'])){$hide_for_public = $inputArray['hide_for_public'];}
			
			$access_url=null;
			if(isset($inputArray['access_url'])){$access_url = $inputArray['access_url'];}
			
			$view_url=null;
			if(isset($inputArray['view_url'])){$view_url = $inputArray['view_url'];}


			$access_online=false;
			if(isset($inputArray['access_online'])){$access_online = $inputArray['access_online'];}
			


		
			
            $cout =0;
            if(isset($inputArray['cout']))
            {
				if(trim($inputArray['cout'])!="")
            		{
						$cout=$inputArray['cout'];
					}

			}

			$lieuDepot=null;
			if(isset($inputArray['lieuDepot'])){$lieuDepot = $inputArray['lieuDepot'];}
			

			$lieuRetrait=null;
			if(isset($inputArray['lieuRetrait'])){$lieuRetrait = $inputArray['lieuRetrait'];}
			

			$textesRegissantPrestation=null;
			if(isset($inputArray['textesRegissantPrestation'])){$textesRegissantPrestation = $inputArray['textesRegissantPrestation'];}
		
            $delaiFixe =false;
            if(isset($inputArray['delaiFixe']))
            {	$delaiFixe=$inputArray['delaiFixe'];}

            $nbreJours =0;
            if(isset($inputArray['nbreJours']))
            	{
					if(trim($inputArray['nbreJours'])!="")
            		{
						$nbreJours=$inputArray['nbreJours'];
					}

				}

			
			$userconnect = new AuthController;
			$userconnectdata = $userconnect->user_data_by_token($request->token);

		
            $Service = new Service;
			$Service->libelle = $libelle;
			$Service->consiste = $consiste;
			$Service->interetDemandeur = $interetDemandeur;
			$Service->obligatoire = $obligatoire;
			$Service->echeance = $echeance;
			$Service->interetDemanderTot = $interetDemanderTot;
			$Service->dateredac = $dateredac;
			$Service->nomSousG = $nomSousG;
			$Service->idEntite=$request->idEntite;
			$Service->hide_for_public = $hide_for_public;
			$Service->nomPresidentSG = $nomPresidentSG;
			$Service->contactPresidentSG = $contactPresidentSG;
			$Service->piecesAFournir = $piecesAFournir;
			$Service->idParent = $idParent;
			$Service->idType = $idType;
			$Service->delai = $delai;
			$Service->view_url = $view_url;
			

			$Service->cout = $cout;
			$Service->published = false;
			$Service->submited = false;
			
			$Service->lieuDepot = $lieuDepot;
			$Service->lieuRetrait = $lieuRetrait;
			$Service->textesRegissantPrestation = $textesRegissantPrestation;

			$Service->delaiFixe = $delaiFixe;
			$Service->nbreJours = $nbreJours;
			$Service->access_online = $access_online;
			$Service->access_url = $access_url;

			
            $Service->created_by = $userconnectdata->id;
            $Service->updated_by = $userconnectdata->id;
            $Service->save();

			return array("status" => "success","success" => true );
        }
        catch(\Illuminate\Database\QueryException $ex){
            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" =>"Une erreur est survenue  " .
                "au cours du traitement. Veuillez contacter l'administrateur", "error"=>$ex->getMessage() );
            //\Log::error($ex->getMessage());
            return $error;
        }
        catch(\Exception $ex){
          \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors de " .
                "votre tentative de connexion. Veuillez contacter l'administrateur" );
            //\Log::error($ex->getMessage());
            //return $error;
			return $error;
        }

	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$ServiceSearch = Service::where("id","=",$id)->get();

		if($ServiceSearch->isEmpty()){
			return array(
				"status"=>"error",
				"message"=>"Aucune étape retrouvée"
				);
		}
		else {
			$Service = $ServiceSearch->first();
			return $Service;
		}
	}

	/**
	 * update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id,Request $request)
	{
				try{
                //recup les champs fournis
                $inputArray =  $request->all();

                //verifie les champs fournis
                if (!isset($inputArray['libelle']) && !isset($inputArray['id'])) {
                	//controle d existence
                    return array("status" => "error",
                        "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
                }

            	$libelle = $inputArray['libelle'];
            	$consiste = $inputArray['consiste'];
	            $interetDemandeur = $inputArray['interetDemandeur'];
	            $obligatoire = $inputArray['obligatoire'];
	            $echeance = $inputArray['echeance'];
	            $interetDemanderTot = $inputArray['interetDemanderTot'];
	            $dateredac = $inputArray['dateredac'];
	            $dateredac = ParamsFactory::convertToDateTimeForSearch($dateredac, false);
            	//$dateredac = $dateredac->toDateTimeString();
	            $nomSousG = $inputArray['nomSousG'];
	            $nomPresidentSG = $inputArray['nomPresidentSG'];
	            $contactPresidentSG = $inputArray['contactPresidentSG'];
	            $piecesAFournir = $inputArray['piecesAFournir'];
	            $idParent = $inputArray['idParent'];
	            $idType = $inputArray['idType'];
				$delai = $inputArray['delai'];
				$hide_for_public = $inputArray['hide_for_public'];
				$published = $inputArray['published'];

	            $cout =0;
	            if(isset($inputArray['cout']))
	            	if(trim($inputArray['cout'])!="")
	            		$cout=$inputArray['cout'];

	            $delaiFixe =false;
	            if(isset($inputArray['delaiFixe']))
					$delaiFixe=$inputArray['delaiFixe'];
					
				$submited =false;
				if(isset($inputArray['submited']))
					$submited=$inputArray['submited'];

					

	            $nbreJours =0;
	            if(isset($inputArray['nbreJours']))
	            	if(trim($inputArray['nbreJours'])!="")
	            		$nbreJours=$inputArray['nbreJours'];

	            $lieuDepot = $inputArray['lieuDepot'];
	            $lieuRetrait = $inputArray['lieuRetrait'];
				$textesRegissantPrestation = $inputArray['textesRegissantPrestation'];
				

				$access_url=null;
				if(isset($inputArray['access_url'])){$access_url = $inputArray['access_url'];}
				
				$view_url=null;
				if(isset($inputArray['view_url'])){$view_url = $inputArray['view_url'];}


				$access_online=false;
				if(isset($inputArray['access_online'])){$access_online = $inputArray['access_online'];}
			


                $id = $inputArray['id'];

				$userconnect = new AuthController;
				$userconnectdata = $userconnect->user_data_by_token($request->token);
                // Récuperer lae Service
                $Service = Service::find($id);

				$Service->libelle = $libelle;
				$Service->consiste = $consiste;
				$Service->interetDemandeur = $interetDemandeur;
				$Service->obligatoire = $obligatoire;
				$Service->echeance = $echeance;
				$Service->interetDemanderTot = $interetDemanderTot;
				$Service->dateredac = $dateredac;
				$Service->nomSousG = $nomSousG;
				$Service->nomPresidentSG = $nomPresidentSG;
				$Service->contactPresidentSG = $contactPresidentSG;
				$Service->piecesAFournir = $piecesAFournir;
				$Service->idParent = $idParent;
				$Service->view_url = $view_url;
				$Service->access_online = $access_online;
				$Service->access_url = $access_url;
				$Service->idType = $idType;
				
				$Service->submited=$submited;
			
				if(($Service->published==0 || $Service->published==false) && ($published==1 || $published==true)){
					$Service->published_by = $userconnectdata->id;
					$Service->published_at = date('Y-m-d H:i:s');
				}
				$Service->published=$published;
				$Service->delai = $delai;
				$Service->hide_for_public = $hide_for_public;
				$Service->cout = $cout;
				$Service->lieuDepot = $lieuDepot;
				$Service->delaiFixe = $delaiFixe;
				$Service->nbreJours = $nbreJours;
				$Service->lieuRetrait = $lieuRetrait;
				$Service->textesRegissantPrestation = $textesRegissantPrestation;

	            $Service->created_by = $userconnectdata->id;
				$Service->updated_by = $userconnectdata->id;
				
				

	            $Service->save();

	            \DB::connection()->enableQueryLog();
				$query = \DB::getQueryLog();
				$lastQuery = end($query);

				return array("status" => "success","success" => true );
           }
            catch(\Illuminate\Database\QueryException $ex){
              \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue au cours du traitement. Veuillez reessayer plus tard.");
            return $error;
        }catch(\Exception $ex){

          \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" =>"Une erreur est survenue. Veuillez reessayer plus tard.");
            return $error;
        }

	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Service::find($id)->delete();
		return array('success' => true );
	}


	 // Liste des prestations par secteur
	public function getPrestationByStructure($structure) {

		try {

			$PrestationSearch = Service::where('idParent',"=",$structure)->get();

			foreach ($PrestationSearch as $Prestation) {
					$Prestation->service_parent;
					$Prestation->listepieces;
				}


			return $PrestationSearch;

		} catch(\Illuminate\Database\QueryException $ex){
            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
        }catch(\Exception $ex){

            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue au cours du traitement . Veuillez contactez l'administrateur" );
            return $error;
        }

	}


	// Liste des prestations par secteur
	public function getPrestationByType($thematique) {

		try {

			$PrestationSearch = Service::where('idType',"=",$thematique)->orderBy('libelle','asc')->get();

			foreach ($PrestationSearch as $Prestation) {
					$Prestation->service_type;
					$Prestation->service_parent;
					$Prestation->listepieces;
				}

			//Gérer les vues
            $statType = Statthematique::find($thematique);
    
			if($statType !== null) {
				$nbrevue=$statType->stat;
				$statType->stat=$nbrevue+1;
				$statType->save();
			}


			return $PrestationSearch;

		} catch(\Illuminate\Database\QueryException $ex){
            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
			return $error;

		}catch(\Exception $ex){

            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue au cours du traitement . Veuillez contactez l'administrateur" );
            return $error;
        }

	}



	public function getCount($table) {

		$table="outilcollecte_".$table;
		try {
			$count = DB::table($table)->count();

			return $count;
		}
		catch(\Illuminate\Database\QueryException $ex){
      \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
        }catch(\Exception $ex){

            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue au cours du traitement . Veuillez contactez l'administrateur" );
            return $error;
        }

	}



	// Liste des annonces par pays et par mot clé
    public function search($keyword) {

        try {
                $Results = Service::where('libelle',"LIKE", "%{$keyword}%")
                            ->orderBy('id', 'DESC')
                            ->get();

            foreach ($Results as $item) {
                    $item->service_parent;
            }


            return $Results;

        } catch(\Illuminate\Database\QueryException $ex){
          \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
        }catch(\Exception $ex){

            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" =>"Une erreur est survenue au cours du traitenemt de votre requête. Veuillez contactez l'administrateur" );
            return $error;
        }

    }



    public function savepiece(Request $request)
	{
		try{
		 //recup les champs fournis
	        $inputArray =  $request->all();

         //verifie les champs fournis
          if (!( isset($inputArray['listepieces']) && isset($inputArray['id'])
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }



            $listepieces = $inputArray['listepieces'];
            $id = $inputArray['id'];

			$userconnect = new AuthController;
			$userconnectdata = $userconnect->user_data_by_token($request->token);

			if(count($listepieces)>0)
			{
				Pieceprestation::where("idService","=",$id)->delete();
			}

			foreach($listepieces as $piece)
			{
	            $Piece = new Pieceprestation;
				$Piece->idService = $id;
				$Piece->libellePiece = $piece["libellePiece"];
				$Piece->created_by = $userconnectdata->id;
            	$Piece->updated_by = $userconnectdata->id;
            	$Piece->save();
			}



			
            return array("status" => "success","success" => true );
        }
        catch(\Illuminate\Database\QueryException $ex){
          \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" =>"Une erreur est survenue  " .
                "au cours du traitement. Veuillez contacter l'administrateur" );
            //\Log::error($ex->getMessage());
            return $error;
        }
        catch(\Exception $ex){
          \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors de " .
                "votre tentative de connexion. Veuillez contacter l'administrateur" );
            //\Log::error($ex->getMessage());
            //return $error;

        }

	}



}
