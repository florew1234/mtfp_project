<?php 
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;
use App\Helpers\Factory\ParamsFactory;

use Illuminate\Http\Request;

use App\Models\Service;
use App\Models\Requete;
use App\Models\Structure;
use App\Models\Relanceback;
use App\Models\Institution;
use App\Models\Registre;
use App\User;
use App\Models\Profil;
use App\Models\Relance;
use App\Models\Acteur;
use Illuminate\Support\Facades\Storage;
use App\Models\Statthematique;
use App\Models\Pieceprestation;
use App\Models\Commentaire;
use App\Models\RelanceParam;
use App\Models\Clotureregistre;

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
		$this->middleware('jwt.auth',['except' =>['index','getPrestationByType','getPrestationByStructure', 'search'  , 'afficheRapport', 
									'afficheRapportGraphe', 'afficheRapportDpaf','afficheRapportInf','afficheRapportPf','ServiceDetailPiece',
									'relanceArchierat','afficheRapport_consult','Affiche_relanceRegistre']]);
    }

	public function relanceArchierat(){

		try {
			$msg = '';
			//Liste des institutions dont l'etat est = Activé! 
			$get_inst = Institution::where('etat_relance',1)->get();
			foreach($get_inst as $insti){
				$temps_rel = intval($insti->nbrjrs_relance);
				$idEnti = $insti->id;

				//Ajouter une ligne dans la table RelanceParam Pour le contrôle des directions de chaque entité
				$rel = RelanceParam::where('idEntite',$idEnti)->where('id_user','-1')->count();
				if($rel == 0){
					
					$message = "Bonjour Mr/Mme. <br> ";
					$message .= "La structure dont vous êtes responsable a enregistré des préoccupations provenant de nos usagers-clients. <br> ";
					$message .= "var_nbreRelance <br> ";
					$message .= "var_containt <br> ";
					
					$message .= "<a href='https://mataccueil.gouv.bj/listrequeteparcours/plaintes' target='_blank' >Cliquer sur ce lien pour consulter le détail de ces préoccupations.</a><br> <br> ";
					$message .= "Le Centre de Services sollicite votre concours pour le traitement diligent de ces préoccupations. <br> <br> ";
					$message .= "Le MTFP en marche pour une administration intelligente, moderne et dynamique. <br> ";

					RelanceParam::create([
						"ordre_relance" => 1,
						"idEntite" => $idEnti,
						"id_user" => '-1',
						"msg_relance"=>$message,
						"apartir_de"=>1,
					]);

					if($idEnti == 1 ){ //A supprimer 
						//SGM Et SGAM  = id_user = 80
						$message = "Bonjour Mr/Mme.<br> ";
						$message .= "Plusieurs structures ont enregistré des préoccupations venant des usagers.  Bien que le délai retenu pour leur traitement ait expiré, aucun retour à date n'a été fait. <br> ";
						$message .= "var_nbreRelance <br> ";
						$message .= "var_containt <br> ";
						
						$message .= "<a href='https://mataccueil.gouv.bj/listrequeteparcours/plaintes' target='_blank' >Cliquer sur ce lien pour consulter le détail de ces préoccupations.</a><br> <br> ";
						$message .= "Le Centre de Services sollicite votre concours pour le traitement diligent de ces préoccupations. <br> <br> ";
						$message .= "Le MTFP en marche pour une administration intelligente, moderne et dynamique. <br> ";

						RelanceParam::create([
							"ordre_relance" => 2,
							"idEntite" => $idEnti,
							"id_user" => 80,
							"msg_relance"=>$message,
							"apartir_de"=>3,
						]);	
						//DC et DAC  = id_user = 77
						$message = "Bonjour Mr/Mme.<br> ";
						$message .= "Plusieurs structures comptabilisent des préoccupations venant des usagers-clients. Bien que le délai retenu pour leur traitement ait expiré, aucun retour à date n'a été fait. <br> ";
						$message .= "var_nbreRelance <br> ";
						$message .= "var_containt <br> ";
						
						$message .= "<a href='https://mataccueil.gouv.bj/listrequeteparcours/plaintes' target='_blank' >Cliquer sur ce lien pour consulter le détail de ces préoccupations.</a><br> <br> ";
						$message .= "Le Centre de Services sollicite votre concours pour le traitement diligent de ces préoccupations. <br> <br> ";
						$message .= "Le MTFP en marche pour une administration intelligente, moderne et dynamique. <br> ";

						RelanceParam::create([
							"ordre_relance" => 3,
							"idEntite" => $idEnti,
							"id_user" => 77,
							"msg_relance"=>$message,
							"apartir_de"=>5,
						]);	

						//DC et DAC  = id_user = 75
						$message = "Bonjour Mr/Mme le Ministre.<br> ";
						$message .= "Plusieurs structures comptabilisent des préoccupations exprimées par les usagers-clients, mais sans retour à date. Ceci constitue la 7ème relance à leur endroit. <br> ";
						$message .= "var_nbreRelance <br> ";
						$message .= "var_containt <br> ";
						
						$message .= "<a href='https://mataccueil.gouv.bj/listrequeteparcours/plaintes' target='_blank' >Cliquer sur ce lien pour consulter le détail de ces préoccupations.</a><br> <br> ";
						$message .= "Le Centre de Services sollicite votre concours pour le traitement diligent de ces préoccupations. <br> <br> ";
						$message .= "Le MTFP en marche pour une administration intelligente, moderne et dynamique. <br> ";

						RelanceParam::create([
							"ordre_relance" => 4,
							"idEntite" => $idEnti,
							"id_user" => 75,
							"msg_relance"=>$message,
							"apartir_de"=>7,
						]);	
					}
				}
				// return response()->json([
				// 	'structure' => $relance,
				// ]);
				//Send mail 
				$today = new DateTime('now', new DateTimeZone('UTC'));
				
				$sunday = clone $today;
	
				$datDebu = $today->format('2010-01-01 00:00:00');
				$datFin = $sunday->format('Y-m-d 23:59:59');
				$dedate = date_create($datDebu); //Y-m-d
				$fidate = date_create($datFin);
				
				//Récuperer toutes les requêtes non traité ::  AND user.idprofil = 23 Directeur
				$structures = DB::select("SELECT DISTINCT aff.idStructure, stru.sigle, stru.idParent, user.email, stru.libelle, stru.active 
												FROM outilcollecte_requete req
												LEFT JOIN outilcollecte_affectation aff ON req.id = aff.idRequete
												LEFT JOIN outilcollecte_structure stru ON stru.id = aff.idStructure
												LEFT JOIN outilcollecte_acteur act ON stru.id = act.idStructure
												LEFT JOIN outilcollecte_users user ON user.idagent = act.id
												WHERE stru.idEntite = $idEnti
												AND stru.type_s = 'dt'
                                                OR stru.type_s = 'dc'
												AND stru.active=1
												AND user.idprofil = 23
												AND req.traiteOuiNon = 0
												AND aff.dateAffectation BETWEEN '$datDebu' and '$datFin';");
				// return response()->json([
				// 	'structure' => $structures,
				// ]);
	
				$datas=array();
				$i=0;
				$msgExtern = "";
				$nbr_Relance_globale = 0;
				$total_non_trait = 0;
				$total_struc = 0;
				foreach ($structures as $st) {
					if($st->active == 1){ //Important 
						
						$structure_id = $st->idStructure;
						$ListReq = DB::select("SELECT count(*) as total,
												SUM(case when (req.traiteOuiNon = 0 and DATEDIFF(CURRENT_TIMESTAMP,req.created_at) <= ser.nbreJours) then 1 else 0 end) totalEnCoursDansLesDelai,
												SUM(case when (req.traiteOuiNon = 0 and DATEDIFF(CURRENT_TIMESTAMP,req.created_at) > ser.nbreJours) then 1 else 0 end) totalEnCoursHorsDelai
												FROM outilcollecte_requete req
												LEFT JOIN outilcollecte_service ser ON req.idPrestation = ser.id
												WHERE req.traiteOuiNon = 0
												AND req.dateRequete BETWEEN '$datDebu' and '$datFin'
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
														AND aff.idStructure = $structure_id
													ORDER BY aff.`id` ASC
												);");
						//Ajouter dans la base les infos sur la relance 
						if ($ListReq[0]->total != 0){
							//Compte 
	
							$sendMail = false;
							$nbreRelan = 1;
							$relance = Relanceback::where('idStructure',$structure_id)->first();
							if(!isset($relance)){
								//Ajouter la structure
								$rel = new Relanceback();
								$rel->message_r = "-";
								$rel->idStructure = $structure_id;
								$rel->nbre_r = 1;
								$rel->datenext_r = date('Y-m-d', strtotime(date('Y-m-d').' + '.$temps_rel.' days'));
								$rel->save();
		
								$sendMail = true;
							}else if($relance->datenext_r <= date('Y-m-d')){
		
								$nbreRelan = intval($relance->nbre_r + 1);
								$rel = Relanceback::where('idStructure',$structure_id)->update([
									'nbre_r' => $nbreRelan,
									'datenext_r' => date('Y-m-d', strtotime(date('Y-m-d').' + '.$temps_rel.' days'))
								]);
								$sendMail = true;
							}
							//Récuperer le nombre de relance maxi 
							if($nbreRelan >= $nbr_Relance_globale){
								$nbr_Relance_globale = $nbreRelan;
							}
	
							if($sendMail == true){
	
								$total_non_trait += $ListReq[0]->total;
								$total_struc += 1;
								
	
								//Send Mail Relance
								//Recupérer la liste de ceux qui devrait recevoir les mails de relance (Ordre de relance)
								$rel = RelanceParam::where('idEntite',$idEnti)->where('id_user','-1')->first();
	
								if(isset($rel)){
									// $con = "Total : ".$ListReq[0]->total." <br>";
									$con  = "** Préoccupations en attente de traitement dont les délais ne sont pas échus : ".$ListReq[0]->totalEnCoursDansLesDelai." <br>";
									$con .= "** Préoccupations en attente de traitement et hors délais : ".$ListReq[0]->totalEnCoursHorsDelai;
	
									$msg = str_replace("var_containt", $con, $rel->msg_relance);
	
									if($nbreRelan == 1){
										$con = "Ceci est la première relance.";
									}else{
										$con = "Ceci est la ".$nbreRelan."ième relance.";
									}
									$msg = str_replace("var_nbreRelance", $con, $msg);
	
									//Faire une sauvegarde des relances 
									Relance::create([
										"message"=>$msg,
										"idStructure"=>$structure_id,
										"date_envoi"=>date("Y-m-d H:m:i"),
										"idEntite"=>$idEnti,
										"idStructureOrdonatrice"=>'-1',
										"idRequete"=>null,
										"etat"=>'e'
									]);
								}
								// Envoyer Mail à la structure  
								self::SendMail_Giwu($msg,$st->sigle,$st->email);
								self::SendMail_Giwu_Copi($msg,$st->sigle);
							}
						}else{
							// Supprimer la ligne de la structure des relances 
							Relanceback::where('idStructure',$structure_id)->delete();
						}
					}
				}
				if($total_struc != 0){
					// Send Mail aux supérieurs ajouté 
					$relance = RelanceParam::with(['user_','user_.agent_user'])->where('idEntite',$idEnti)
											->where('id_user','<>','-1')
											->where('apartir_de','<=',$nbr_Relance_globale)
											->orderBy('apartir_de','asc')
											->get();
					if(isset($relance)){
						
						foreach($relance as $rel){
							
							$con  = "** Nombre total de préoccupations en attente de traitement et hors délais : ".$total_non_trait." <br>";
							$con .= "** Nombre de structures concernées : ".$total_struc." ";
	
							$msg = str_replace("var_containt", $con, $rel->msg_relance);
	
							if($nbreRelan == 1){
								$con = "Ceci est la première relance.";
							}else{
								$con = "Ceci est la ".$nbr_Relance_globale."ième relance.";
							}
							$msg = str_replace("var_nbreRelance", $con, $msg);
							$mail = "";
							if($rel->acteur != null && $rel->acteur->user_agent != null){
								$mail = $rel->acteur->user_agent->email;
							}
							Relance::create([
								"message"=>$msg,
								"idStructure"=>'-'.$rel->id_user,
								"date_envoi"=>date("Y-m-d H:m:i"),
								"idEntite"=>$idEnti,
								"idStructureOrdonatrice"=>'-1',
								"idRequete"=>null,
								"etat"=>'e'
							]);
							self::SendMail_Giwu($msg,"",$mail);
							self::SendMail_Giwu_Copi($msg,"");
						}
					}
				}
			}
			return response()->json("Rapport envoyé avec succès");
		} catch (\Throwable $th) {
			return response()->json($th->getMessage());
		}
		
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
		}else if($id == 0){
			return "Dimanche";
		}
		return "Erreur date";
	}

	public function SendMail_Giwu($msg,$nam,$mail){
		// $mail = 'ritohon@gouv.bj';
		if(trans('auth.mode') != 'test'){
			if(filter_var($mail, FILTER_VALIDATE_EMAIL)){ //Vérifier si c'est un bon mail...

				Mail::send("mail.relancemail", ['message_send'=>$msg], function($mes)use ($nam,$mail){
					$mes->from("mtfp.usager@gouv.bj", "RELANCE MatAccueil")
							->subject("Relance MatAccueil ".$nam);
					$mes->to($mail,$nam);
				});
			}
		}
	}

	public function Ctr_Date_Lundi($id){
		// date_create(date('Y-m-d', strtotime(date('Y-m-d').' + 2 days')))->format('w');
		if($id == 6 || $id == 7 ){
			return 1;
		}
		return $id;
	}
	// Statistique 2021 -- Temporaire
    public function afficheRapportDpaf(Request $req){
		

		// FORMAT DE DATE
		$today = new DateTime('now', new DateTimeZone('UTC'));
		$day_of_week = $today->format('w');
		// $today->modify('- ' . (($day_of_week - 1 + 7) % 7) . 'days');
		$sunday = clone $today;
		// $sunday->modify('+ 4 days');
		$datDebu = $today->format('2021-01-01 00:00:00');
		// $datFin = $sunday->format('Y-m-d 23:59:59');
		$datFin = $sunday->format('2021-12-31 23:59:59');
		$dedate = date_create($datDebu); //Y-m-d
		$fidate = date_create($datFin);
		//
		//Liste des structures qui ont enregistrée une requête
		// ------------STATISTIQUE DU DEBUT 2000 ------------------------------------------------------------------------------------------
		
			$structures = DB::select("SELECT DISTINCT aff.idStructure, stru.sigle, stru.idParent
										FROM outilcollecte_requete req
										LEFT JOIN outilcollecte_affectation aff ON req.id = aff.idRequete
										LEFT JOIN outilcollecte_structure stru ON stru.id = aff.idStructure
										WHERE stru.idEntite = 1 
										AND stru.idParent = 0
										AND stru.type_s = 'dt'
                                    	OR stru.type_s = 'dc'
										AND stru.active=1
										AND req.plainte = 1
										AND aff.dateAffectation BETWEEN '$datDebu' and '$datFin';");
			$datas=array();
			$dataUserRegi=array();
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
											SELECT DISTINCT outilcollecte_affectation.idRequete
											FROM outilcollecte_affectation
											WHERE outilcollecte_affectation.dateAffectation BETWEEN '$datDebu' and '$datFin'
											AND outilcollecte_affectation.idStructure = $structure_id
												
										);");
				$Tplainte = $stats[0]->total;
				$traitesTra = $stats[0]->totalTraite;
				$traitesNTra = $stats[0]->totalNTraite;
				$pourcent = 0;
				if($Tplainte!=0 ){
					$pourcent=($traitesNTra*100)/$Tplainte;
				}
				// if($structure_id == '58'){
				// 	dd($statsTra);
				// }
				$datas[$i]["strcuture"] = $st->sigle;
				$datas[$i]["Tplainte"]=$Tplainte;
				$datas[$i]["plainteTrai"]=$traitesTra;
				$datas[$i]["plainteNonTrai"]=intval($traitesNTra);
				$datas[$i]["nbrTrans"] = "";
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
										SUM(CASE WHEN traiteOuiNon = 0 then 1 else 0 end) totalNTraite,
										SUM(CASE WHEN horsDelai = 3 and traiteOuiNon = 1 then 1 else 0 end) totalTraiteDansLesDelais,
										SUM(case when ser.nbreJours = 0 then 0 else DATEDIFF(req.dateReponse,req.created_at) end) totalTempTraitement
										FROM outilcollecte_requete req
										LEFT JOIN outilcollecte_service ser ON req.idPrestation = ser.id
										WHERE req.plainte IN (0,2)
										AND req.id IN 
										(
											SELECT DISTINCT outilcollecte_affectation.idRequete
											FROM outilcollecte_affectation
											WHERE outilcollecte_affectation.dateAffectation BETWEEN '$datDebu' and '$datFin'
											AND outilcollecte_affectation.idStructure = $structure_id 
										);");

				$Treq = $stats[0]->total;
				$totalTempTraitement = $stats[0]->totalTempTraitement;
				$reqtraitesTra = $stats[0]->totalTraite;
				$reqtraitesNTra = $stats[0]->totalNTraite;
				$reqtraitesTraDDelai = $stats[0]->totalTraiteDansLesDelais;
				
				$pourcent = 0;
				if($Treq!=0 ){
					$pourcent=($reqtraitesNTra*100)/$Treq;
				}
				$pourcentDDelai = 0;
				if($Treq!=0 ){
					$pourcentDDelai=($reqtraitesTraDDelai*100)/$Treq;
				}
				$datasreq[$i]["strcuture"]=$st->sigle;
				$datasreq[$i]["Treq"]=$Treq;
				$datasreq[$i]["totalTempTraitement"] = $totalTempTraitement;
				$datasreq[$i]["reqTrai"]=$reqtraitesTra;
				$datasreq[$i]["reqNonTrai"]=intval($reqtraitesNTra);
				$datasreq[$i]["reqpourcentPNT"]=round($pourcent,2);
				$datasreq[$i]["reqpourcentPDelaiT"]=round($pourcentDDelai,2);
				// EffeServ
				$statsDMoy = DB::select("SELECT DISTINCT ser.id,req.traiteOuiNon,ser.nbreJours, count(*) total,
											SUM(case when ser.nbreJours = 0 then 0 else DATEDIFF(req.dateReponse,req.created_at) end) totalTempTraitement
											FROM outilcollecte_requete req
											LEFT JOIN outilcollecte_service ser ON req.idPrestation = ser.id
											WHERE req.plainte IN (0,2)
											AND req.traiteOuiNon = 1
											AND req.id IN 
											(
												SELECT DISTINCT outilcollecte_affectation.idRequete
												FROM outilcollecte_affectation
												WHERE outilcollecte_affectation.dateAffectation BETWEEN '$datDebu' and '$datFin'
												AND outilcollecte_affectation.idStructure = $structure_id
											)
											GROUP BY ser.id;");
				$moyen = 0;
				foreach($statsDMoy as $sta){
					if($sta->total != 0){
						$moyen += round($sta->totalTempTraitement) / $sta->total;
					}
				}	
				$datasreq[$i]["moyen"]= round($moyen,2);		
				$i++;
			} 
		// 
		// Listes des communes qui ont enregistre des registres 
		$periode = self::JourDate($fidate->format('w'))." ".$fidate->format('d/m/Y');
		$title = "RAPPORT_DPAF_2021.pdf";
		// $logo= env('ASSET_URL').'/img/logo-mtfp.svg';
        PDF::loadView("rapportDpaf",['datas'=>$datas,'datasreq'=>$datasreq,'periode'=>$periode,'labels' => $label, 'prices' => $price])
						->setPaper('a4','landscape')->save(Storage::path("public/rapport/".$title));
        // return view('rapportDpaf',['datas'=>$datas,'datasreq'=>$datasreq,'periode'=>$periode,'labels' => $label, 'prices' => $price]);
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
	
    public static function afficheRapport(Request $req,$compte){
		
		// $tabRecu = self::tab_Data_Rapport($req);
		
		// $dateC = $tabRecu[0];
		// $fidate = $tabRecu[1];
		// $datas = $tabRecu[2];
		// $datasreq = $tabRecu[3];
		// $dataUserRegi = $tabRecu[4];
		// $dataUserRegiRemon = $tabRecu[5];
		// $datasDem = $tabRecu[8];

		// $datasStrTop = $tabRecu[7];

		// $periode = self::JourDate($fidate->format('w'))." ".$fidate->format('d/m/Y');
		// $period_pres = self::JourDate($Thursdaydat->format('w'))." ".$Thursdaydat->format('d/m/Y')." au ".self::JourDate($fidate->format('w'))." ".$fidate->format('d/m/Y');
		$title = "RAPPORT_P_R_D_RECUES_A_LA_DATE_DU_".date('Y-m-d').".pdf";
		
		//Ajouter dans la base de commentaire... date('Y-m-d', strtotime(''.' - 5 days'))
		$com = Commentaire::orderBy('id_comment','desc')->first();
		if(isset($com)){
			$dateFin = $com->date_fin_com;
		}else{
			$dateFin = date('Y-m-d', strtotime(date('Y-m-d').' - 7 days'));
		}
		
		if($dateFin != date('Y-m-d')){
			$Commentaire = new Commentaire;
			$Commentaire->num_enreg = '2'.date('YmdHis');
			$Commentaire->date_debut_com = $dateFin;
			$Commentaire->date_fin_com = date('Y-m-d');
			$Commentaire->fichier_joint = "";
			$Commentaire->commentaire = "";
			$Commentaire->id_init = 2; //Administrateur
			$Commentaire->save();
		}

		// $logo= env('ASSET_URL').'/img/logo-mtfp.svg';
        // PDF::loadView("rapport",['datas'=>$datas,'datasStrTop'=>$datasStrTop,'datasreq'=>$datasreq,'datasDem'=>$datasDem,'periode'=>$periode,'dataspfrevi'=>$dataUserRegi,'dataspfreviRemon'=>$dataUserRegiRemon])
		// 				->setPaper('a4','landscape')->save(Storage::path("public/rapport/".$title));
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
				// $text = "";
				// $text .= "Mesdames et Messieurs,\n";
				// $text .= "Le point statistique hebdomadaire relatif à la prise en charge des préoccupations des usagers-clients du MTFP est disponible.\n";
				// $text .= "Pour le consulter, connectez-vous à partir de <a href='https://mataccueil.gouv.bj' target='_blank' >https://mataccueil.gouv.bj</a>. \n";
				// $text .= "Après connexion, prière de cliquer sur le menu 'Gestion des rapports'. \n ";
				// $text .= "Pour toute préoccupation, vous pouvez nous contacter \n";
				// $text .= "** Via WhatsApp au 52 16 00 00 \n";
				// $text .= "** Par mail : mtfp.usager@gouv.bj \n";
				// $text .= "Le Centre de Services du MTFP à votre service. \n";
				// dd($text);
				// $sujet = "Rapport suivi traitement des plaintes reçues";
				// $senderEmail = 'mtfp.usager@gouv.bj';
				// dd($req->get('compte'));
				//$compte = intval($req->get('compte'));
				
				$nbre = 0;
				if($compte == 0){
					foreach($tra as $mail=>$nam){
						Mail::send("mail.rapportmail", [], function($message)use ($nam,$mail){
							$message->from("mtfp.usager@gouv.bj", "SRU")
									->subject("Rapport suivi traitement des plaintes reçues");
							$message->to($mail,$nam);
						});
						var_dump("Rapport plainte envoyé avec succès : ".$mail." - ".$nam." le ".date('Y-m-d-h-i-s')." -- ".$nbre."<br>");
					}
				}else{
					var_dump("compte ".$compte);
					foreach($tra as $mail=>$nam){
						$nbre++;
						if($nbre >= $compte){
							Mail::send("mail.rapportmail", [], function($message)use ($nam,$mail){
								$message->from("mtfp.usager@gouv.bj", "SRU")
										->subject("Rapport suivi traitement des plaintes reçues");
								$message->to($mail,$nam);
							});
							
							var_dump("Rapport--- plainte envoyé avec succès : ".$mail." - ".$nam." le ".date('Y-m-d-h-i-s')." -- ".$nbre."<br>");
							if($nbre == $compte+4){
								exit;
							}
						}
					}

				}
			}
		} catch (\Throwable $th) {
			return response()->json($th->getMessage());
		}
		return response()->json("Rapport envoyé avec succès");
    }
	
	public function SendMail_Giwu_Copi($msg,$nam){
		$mail = 'ritohon@gouv.bj';
		
		if(trans('auth.mode') != 'test'){
			Mail::send("mail.relancemail", ['message_send'=>$msg], function($mes)use ($nam,$mail){
				$mes->from("mtfp.usager@gouv.bj", "TEST : RELANCE MatAccueil")
						->subject("TEST : RELANCE MatAccueil (".$nam.")");
				$mes->to($mail,$nam);
			});
			sleep(2);
		}
	}
	
    public static function afficheRapport_consult(Request $req){

		$tabRecu = self::tab_Data_Rapport($req);
		//return $tabRecu;
		//dd($tabRecu);
		$dateC = date_create($tabRecu[0]);
		$fidate = $tabRecu[1];
		$datas = $tabRecu[2];
		$datasreq = $tabRecu[3];
		$dataUserRegi = $tabRecu[4];
		$dataUserRegiRemon = $tabRecu[5];
		$datasDem = $tabRecu[8];

		$datasStrTop = $tabRecu[7];

		$datF = $req->get('datef');
		if(isset($datF)){
			$periode = " DU ".$dateC->format('d/m/Y')." AU ".$fidate->format('d/m/Y');
		}else{
			$periode = " à la date du ".$fidate->format('d/m/Y');
		}
		$title = "RAPPORT_P_R_D_RECUES_A_LA_DATE_DU_".$fidate->format('YmdHis').".pdf";

		// $logo= env('ASSET_URL').'/img/logo-mtfp.svg';
        $pdf = PDF::loadView("rapport",['datas'=>$datas,'datasStrTop'=>$datasStrTop,'datasreq'=>$datasreq,'datasDem'=>$datasDem,'periode'=>$periode,'dataspfrevi'=>$dataUserRegi,'dataspfreviRemon'=>$dataUserRegiRemon])
						->setPaper('a4','landscape');
		return $pdf->download($title);
    }
	
	
    public function Affiche_relanceRegistre(Request $req){
		
		//Recuperer la liste des mails qui doivent recevoir le mail
		$ListEmail = Acteur::join('outilcollecte_users','outilcollecte_users.idagent','outilcollecte_acteur.id')
                      ->where('outilcollecte_users.typeUserOp','p')
                      ->select('outilcollecte_acteur.nomprenoms','outilcollecte_users.id','outilcollecte_users.email')
					  ->get();
		//Recuperer le nombre de visite non  cloturee par users
		foreach($ListEmail as $mail){
			// return response($mail);
			$use = User::find($mail->id);
			//Recuperer toutes les dates de début jusqu'a ce jour sans le week-end 
			if($use){ $date =  $use->date_start_registre; }else{ $date =  "2022-06-01"; }
			
			$date_debut = strtotime($date); 
			$date_fin = strtotime(date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d')))));
			// $date_fin = strtotime(date("Y-m-d"));
			$nbreJour = round(($date_fin - $date_debut)/60/60/24,0); //le nombre de jour entre deux dates
			$req = Clotureregistre::where('id_user',$mail->id)->select('date_cloture')
												->pluck('date_cloture')->toArray();
			$nbreJourCpte = 0;
			for ($i=0; $i <= $nbreJour; $i++) { 
				$date_terminee = date('Y-m-d', strtotime('+'.$i.' day', strtotime($date)));
				
				if(date_create($date_terminee)->format('w') <> 6 && date_create($date_terminee)->format('w') <> 0){ //Extrait le week-end
					if(!in_array($date_terminee,$req)){  //Verifier si $date_terminee est dans le tableau
						$nbreJourCpte++;
					}
				}
			}

			if($nbreJourCpte != 0){
				if(filter_var($mail->email, FILTER_VALIDATE_EMAIL)){ //Vérifier si c'est un bon mail...
					$nam = $mail->nomprenoms;
					$MailRegis = $mail->email;
					Mail::send("mail.rapportRelanceRegistre", ['nbre_date'=>$nbreJourCpte,'destinataire'=>$nam], function($mes)use ($nam,$MailRegis){
						$mes->from("mtfp.usager@gouv.bj", "RELANCE Registre")
								->subject("Relance Registre ".$nam);
						$mes->to($MailRegis,$nam);
					});
					sleep(5);
				}
			}
		}
    }
	
    public function afficheRapportGraphe(Request $req){

		$tabRecu = self::tab_Data_Rapport($req);

		$giwu['date'] = date_create($tabRecu[0]);
		$giwu['fidate'] = $tabRecu[1];
		$giwu['datas'] = $tabRecu[2];
		$giwu['datasreq'] = $tabRecu[3];
		$giwu['datasDem'] = $tabRecu[8];
		// $giwu['dataUserRegi'] = $tabRecu[4];
		// $giwu['dataUserRegiRemon'] = $tabRecu[5];
		$giwu['datasThem'] = $tabRecu[6];

		$datF = $req->get('datef');
		if(isset($datF)){
			$periode = " période du ".$giwu['date']->format('d/m/Y')." AU ".$giwu['fidate']->format('d/m/Y');
		}else{
			$periode = " à la date du ".$giwu['fidate']->format('d/m/Y');
		}
		// $periode = self::JourDate($giwu['fidate']->format('w'))." ".$giwu['fidate']->format('d/m/Y');
		$giwu['periode'] = $periode;
		$giwu['infos'] = 'Appuyez les touches "Ctrl + P" pour exporter en PDF ou imprimer les graphes.';
		//dd($giwu);
		return view('rapportGraphe')->with($giwu);
    }

	static public  function tab_Data_Rapport(Request $req){
		$tab = [];

		$idEntite=$req->get('idEntite');
		// FORMAT DE DATE
		$dat = $req->get('date');
		if(isset($dat)){
			$today = date_create($dat);
		}else{
			$today = new DateTime('now', new DateTimeZone('UTC'));
		}

		array_push($tab, $dat);

		$datF = $req->get('datef');
		if(isset($datF)){
			$datDebu = $today->format('Y-m-d 00:00:00');
			$dedate = date_create($datDebu); //Y-m-d
			$datFin = $datF.' 23:59:59';
			$fidate = date_create($datF); 
		}else{

			$day_of_week = $today->format('w');
			$sunday = clone $today;

			$datDebu = $today->format('2000-01-01 00:00:00');

			$dedate = date_create($datDebu); //Y-m-d
			$datFin = $sunday->format('Y-m-d 23:59:59');
			$fidate = date_create($datFin);
		}
		array_push($tab, $fidate);

		// $premierJour = strftime("2022-04-01 00:00:00", strtotime("this week"));
		$premierJour = strftime("%Y-%m-%d 00:00:00", strtotime("this week"));
		$premdate = date_create($premierJour);
		// 	
		$Thursday = strftime("%Y-%m-%d 00:00:00", strtotime('last Thursday'));
		$Thursdaydat = date_create($Thursday);
		//Liste des structures qui ont enregistrée une requête
		// ------------STATISTIQUE DU DEBUT 2000 ------------------------------------------------------------------------------------------
		// https://www.developpez.net/forums/d1313474/bases-donnees/mysql/requetes/group-by-dernier-enregistrement/ -- Transferer
		//dd($idEntite );

		$structures = DB::select("SELECT DISTINCT aff.idStructure, stru.sigle, stru.idParent,stru.active 
									FROM outilcollecte_requete req
									LEFT JOIN outilcollecte_affectation aff ON req.id = aff.idRequete
									LEFT JOIN outilcollecte_structure stru ON stru.id = aff.idStructure
									WHERE stru.idEntite = '$idEntite' 
									AND ( stru.type_s = 'dt'
									OR stru.type_s = 'dc')
									AND stru.active=1
									AND req.plainte = 1
									AND req.dateRequete BETWEEN '$datDebu' and '$datFin'
									order by stru.sigle asc ;");
		
		$datas=array();
		$dataUserRegi=array();
		$i=0;
		foreach ($structures as $st) {
			
			if($st->active == 1){
				
				$structure_id = $st->idStructure;
				$stats = DB::select("SELECT count(*) total,
										SUM(CASE WHEN traiteOuiNon = 1 then 1 else 0 end) totalTraite,
										SUM(CASE WHEN archiver = 1 then 1 else 0 end) totalArcive,
										SUM(CASE WHEN traiteOuiNon = 0 then 1 else 0 end) totalNTraite
										FROM outilcollecte_requete
										WHERE outilcollecte_requete.plainte = 1
										AND outilcollecte_requete.dateRequete BETWEEN '$datDebu' and '$datFin'
										AND outilcollecte_requete.id IN 
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
												AND aff.idStructure = $structure_id
											ORDER BY aff.`id` ASC
										);");
				$Tplainte = $stats[0]->total;
				$traitesTra = $stats[0]->totalTraite;
				$traitesArchiv = $stats[0]->totalArcive;
				$traitesNTra = $stats[0]->totalNTraite;
				$pourcent = 0;
				if($Tplainte!=0 ){
					$pourcent=($traitesNTra*100)/$Tplainte;
				}
				// if($structure_id == '58'){
				// 	dd($statsTra);
				// }
				$datas[$i]["strcuture"] = $st->sigle;
				$datas[$i]["Tplainte"]=$Tplainte;
				$datas[$i]["plainteTrai"]=$traitesTra;
				$datas[$i]["plainteNonTrai"]=intval($traitesNTra);
				$datas[$i]["archiveReq"]=intval($traitesArchiv);
				$datas[$i]["nbrTrans"] = "";
				$datas[$i]["pourcentPNT"]=round($pourcent,2);
				$i++;
			}
		}
		
		array_push($tab, $datas);
		//return $tab;
		//REQUÊTES
		$structures = Requete::join('outilcollecte_affectation','outilcollecte_affectation.idRequete','outilcollecte_requete.id')
								->join('outilcollecte_structure','outilcollecte_structure.id','outilcollecte_affectation.idStructure')
								->where('outilcollecte_structure.idParent',0)
								->where('outilcollecte_structure.idEntite',$idEntite)
								->where('outilcollecte_structure.active',1)
								->where('outilcollecte_requete.plainte',0)
								->whereIn('outilcollecte_structure.type_s',['dt','dc'])
								->whereBetween('outilcollecte_requete.dateRequete',[$dedate,$fidate])
								->select('outilcollecte_affectation.idStructure','outilcollecte_structure.sigle')
								->orderBy('outilcollecte_structure.sigle','asc')
								->distinct() ;
								//->get();
		
		$datasreq=array();
		//dd($structures->toSql(),$dedate,$fidate,$structures->getBindings());
		$i=0;
		foreach ($structures as $st) {
			// if($st->active == 1){
				$structure_id = $st->idStructure;
				$stats = DB::select("SELECT count(*) total, 
										SUM(CASE WHEN traiteOuiNon = 1 then 1 else 0 end) totalTraite,
										SUM(CASE WHEN traiteOuiNon = 0 then 1 else 0 end) totalNTraite,
										SUM(CASE WHEN archiver = 1 then 1 else 0 end) totalArcive,
										SUM(CASE WHEN horsDelai = 3 and traiteOuiNon = 1 then 1 else 0 end) totalTraiteDansLesDelais,
										SUM(case when ser.nbreJours = 0 then 0 else DATEDIFF(req.dateReponse,req.created_at) end) totalTempTraitement
										FROM outilcollecte_requete req
										LEFT JOIN outilcollecte_service ser ON req.idPrestation = ser.id
										WHERE req.plainte = 0
										AND req.dateRequete BETWEEN '$datDebu' and '$datFin'
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
												AND aff.idStructure = $structure_id
											ORDER BY aff.`id` ASC 
										);");

				$Treq = $stats[0]->total;
				$totalTempTraitement = $stats[0]->totalTempTraitement;
				$reqtraitesTra = $stats[0]->totalTraite;
				$reqtraitesNTra = $stats[0]->totalNTraite;
				$reqtraitesArchive = $stats[0]->totalArcive;
				$reqtraitesTraDDelai = $stats[0]->totalTraiteDansLesDelais;
				
				$pourcent = 0;
				if($Treq!=0 ){
					$pourcent=($reqtraitesNTra*100)/$Treq;
				}
				$pourcentDDelai = 0;
				if($Treq!=0 ){
					$pourcentDDelai=($reqtraitesTraDDelai*100)/$Treq;
				}
				$datasreq[$i]["strcuture"]=$st->sigle;
				$datasreq[$i]["Treq"]=$Treq;
				$datasreq[$i]["totalTempTraitement"] = $totalTempTraitement;
				$datasreq[$i]["reqTrai"]=$reqtraitesTra;
				$datasreq[$i]["reqNonTrai"]=intval($reqtraitesNTra);
				$datasreq[$i]["archiveReq"]=intval($reqtraitesArchive);
				$datasreq[$i]["reqpourcentPNT"]=round($pourcent,2);
				$datasreq[$i]["reqpourcentPDelaiT"]=round($pourcentDDelai,2);
				// EffeServ
				$statsDMoy = DB::select("SELECT DISTINCT ser.id,req.traiteOuiNon,ser.nbreJours, count(*) total,
											SUM(case when ser.nbreJours = 0 then 0 else DATEDIFF(req.dateReponse,req.created_at) end) totalTempTraitement
											FROM outilcollecte_requete req
											LEFT JOIN outilcollecte_service ser ON req.idPrestation = ser.id
											WHERE req.plainte = 0
											AND req.traiteOuiNon = 1
											AND req.dateRequete BETWEEN '$datDebu' and '$datFin'
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
													AND aff.idStructure = $structure_id
												ORDER BY aff.`id` ASC
											)
											GROUP BY ser.id;");
				$moyen = 0;
				foreach($statsDMoy as $sta){
					if($sta->total != 0){
						$moyen += round($sta->totalTempTraitement) / $sta->total;
					}
				}	
				$datasreq[$i]["moyen"]= round($moyen,2);		
				$i++;
			// }
		} 
		array_push($tab, $datasreq);
		//Liste des prestations --------------------------Thursday------------------------------


		$datasPresD=array();
		$i=0;
		//Liste des prestations --- Fin 

		// Listes des communes qui ont enregistre des registres 
		$reqCom = Registre::join('outilcollecte_users','outilcollecte_users.id','outilcollecte_registre.created_by')
								->join('outilcollecte_acteur','outilcollecte_acteur.id','outilcollecte_users.idagent')
								->join('outilcollecte_commune','outilcollecte_acteur.idCom','outilcollecte_commune.id')
								->join('outilcollecte_departement','outilcollecte_departement.id','outilcollecte_commune.depart_id')
								->select('outilcollecte_commune.*','outilcollecte_departement.libelle')
								->orderBy('outilcollecte_commune.libellecom','asc')
								->whereBetween('outilcollecte_registre.created_at',[$dedate,$fidate])
								->distinct()
								->get();
		$i=0;
		// 
		foreach($reqCom as $com){

			$statsDIR = DB::select("SELECT  count(*) total,
									SUM(CASE WHEN satisfait = 'oui' then 1 else 0 end) totalTraite,
									SUM(CASE WHEN satisfait = 'non' then 1 else 0 end) totalNTraite
									FROM outilcollecte_registre, outilcollecte_users,outilcollecte_acteur
									WHERE outilcollecte_registre.created_by = outilcollecte_users.id
									AND outilcollecte_acteur.id = outilcollecte_users.idagent
									AND outilcollecte_acteur.idCom = $com->id
									AND outilcollecte_registre.created_at BETWEEN '$datDebu' and '$datFin'
									AND outilcollecte_registre.plainte IN (0,2)");
			$TotalDir_ 		= $statsDIR[0]->total;
			$Totaldir_Tr 	= $statsDIR[0]->totalTraite;
			$Totaldir_NTr 	= $statsDIR[0]->totalNTraite;
			$pourcentDIR_NTr = 0;
			if($TotalDir_!=0 ){
				$pourcentDIR_NTr=($Totaldir_NTr*100)/$TotalDir_;
			}
			$statsPL = DB::select("SELECT  count(*) total,
									SUM(CASE WHEN satisfait = 'oui' then 1 else 0 end) totalTraite,
									SUM(CASE WHEN satisfait = 'non' then 1 else 0 end) totalNTraite
									FROM outilcollecte_registre, outilcollecte_users,outilcollecte_acteur
									WHERE outilcollecte_registre.created_by = outilcollecte_users.id
									AND outilcollecte_acteur.id = outilcollecte_users.idagent
									AND outilcollecte_acteur.idCom = $com->id
									AND outilcollecte_registre.created_at BETWEEN '$datDebu' and '$datFin'
									AND outilcollecte_registre.plainte = 1");

			$TotalPL_ = $statsPL[0]->total;
			$TotalPL_Tr = $statsPL[0]->totalTraite;
			$TotalPL_NTr = $statsPL[0]->totalNTraite;

			$pourcentPL_NTr = 0;
			if($TotalPL_!=0 ){
				$pourcentPL_NTr=($TotalPL_NTr*100)/$TotalPL_;
			}
			$dataUserRegi[$i]["nomcomr"]= $com->libellecom;
			$dataUserRegi[$i]["TotalDir_r"]=$TotalDir_;
			$dataUserRegi[$i]["Totaldir_Trr"]=$Totaldir_Tr;
			$dataUserRegi[$i]["Totaldir_NTrr"]=$Totaldir_NTr;
			$dataUserRegi[$i]["pourcentDIR_NTrr"]=round($pourcentDIR_NTr,2);

			$dataUserRegi[$i]["TotalPL_r"]=$TotalPL_;
			$dataUserRegi[$i]["TotalPL_Trr"]=$TotalPL_Tr;
			$dataUserRegi[$i]["TotalPL_NTrr"]=$TotalPL_NTr;
			$dataUserRegi[$i]["pourcentPL_NTrr"]=round($pourcentPL_NTr,2);

			$i++;
		}
		array_push($tab, $dataUserRegi);
		// Listes des communes qui ont enregistre des registres 

		// Listes des communes qui ont remonté les préoccupations non satisfait
		$reqComRem = DB::select("SELECT DISTINCT com.*
								FROM `outilcollecte_requete` req, outilcollecte_users u, outilcollecte_acteur act, outilcollecte_commune com
								WHERE req.created_by = u.id
								AND act.id = u.idagent
								AND act.idCom = com.id
								AND req.interfaceRequete = 'registre'
								ORDER BY com.libellecom DESC;");
		$i=0;
		$dataUserRegiRemon=array();
		// 
		foreach($reqComRem as $com){
			$statsDIR = DB::select("SELECT count(*) total,
								SUM(CASE WHEN traiteOuiNon = 1 then 1 else 0 end) totalTraite,
								SUM(CASE WHEN traiteOuiNon = 0 then 1 else 0 end) totalNTraite
								FROM outilcollecte_requete, outilcollecte_users, outilcollecte_acteur
								WHERE outilcollecte_requete.created_by = outilcollecte_users.id
								AND outilcollecte_acteur.id = outilcollecte_users.idagent
								AND outilcollecte_requete.plainte IN (0,2)
								AND outilcollecte_acteur.idCom = $com->id
								AND outilcollecte_requete.interfaceRequete = 'registre';");

			$TotalDir_ 		= $statsDIR[0]->total;
			$Totaldir_Tr 	= $statsDIR[0]->totalTraite;
			$Totaldir_NTr 	= $statsDIR[0]->totalNTraite;
			$pourcentDIR_NTr = 0;
			if($TotalDir_!=0 ){
				$pourcentDIR_NTr=($Totaldir_NTr*100)/$TotalDir_;
			}
			$statsPL = DB::select("SELECT count(*) total,
									SUM(CASE WHEN traiteOuiNon = 1 then 1 else 0 end) totalTraite,
									SUM(CASE WHEN traiteOuiNon = 0 then 1 else 0 end) totalNTraite
									FROM outilcollecte_requete, outilcollecte_users, outilcollecte_acteur
									WHERE outilcollecte_requete.created_by = outilcollecte_users.id
									AND outilcollecte_acteur.id = outilcollecte_users.idagent
									AND outilcollecte_requete.plainte = 1
									AND outilcollecte_acteur.idCom = $com->id
									AND outilcollecte_requete.interfaceRequete = 'registre';");
			$TotalPL_ = $statsPL[0]->total;
			$TotalPL_Tr = $statsPL[0]->totalTraite;
			$TotalPL_NTr = $statsPL[0]->totalNTraite;

			$pourcentPL_NTr = 0;
			if($TotalPL_!=0 ){
				$pourcentPL_NTr=($TotalPL_NTr*100)/$TotalPL_;
			}
			$dataUserRegiRemon[$i]["nomcomr"]= $com->libellecom;
			$dataUserRegiRemon[$i]["TotalDir_r"]=$TotalDir_;
			$dataUserRegiRemon[$i]["Totaldir_Trr"]=$Totaldir_Tr;
			$dataUserRegiRemon[$i]["Totaldir_NTrr"]=$Totaldir_NTr;
			$dataUserRegiRemon[$i]["pourcentDIR_NTrr"]=round($pourcentDIR_NTr,2);

			$dataUserRegiRemon[$i]["TotalPL_r"]=$TotalPL_;
			$dataUserRegiRemon[$i]["TotalPL_Trr"]=$TotalPL_Tr;
			$dataUserRegiRemon[$i]["TotalPL_NTrr"]=$TotalPL_NTr;
			$dataUserRegiRemon[$i]["pourcentPL_NTrr"]=round($pourcentPL_NTr,2);

			$i++;
		}
		array_push($tab, $dataUserRegiRemon);
		// Listes des communes qui ont enregistre des registres 
		//Statistique par thématique
		$themati = DB::select("SELECT * FROM `outilcollecte_typeservice` where idEntite = 1 order By id asc;");
		$i = 0;
		$datasThem=array();
		foreach($themati as $them){

			$stats = DB::select("SELECT SUM(CASE WHEN r.traiteOuiNon = 1 then 1 else 0 end) totalTraite, 
										SUM(CASE WHEN r.traiteOuiNon = 0 then 1 else 0 end) totalNTraite, 
										count(*) total
								FROM outilcollecte_service s, outilcollecte_requete r
								WHERE s.id = r.idPrestation
								AND r.idEntite = 1
								AND r.dateRequete BETWEEN '$datDebu' and '$datFin'
								AND s.idType = $them->id;");
			
			$Total = $stats[0]->total;
			$traitesTra = $stats[0]->totalTraite;
			$traitesNTra = $stats[0]->totalNTraite;
			// if($structure_id == '58'){
			// 	dd($statsTra);
			// }
			$datasThem[$i]["theme"] = $them->libelle;
			$datasThem[$i]["totalReq"]=intval($Total);
			$datasThem[$i]["ReqTr"]=intval($traitesTra);
			$datasThem[$i]["ReqNonTrai"]=intval($traitesNTra);
			$i++;
		}
		array_push($tab, $datasThem);	
		
		
		$datasPres=array();
		$i=0;
		
		// $structures = Structure::where("idParent",0)->get();
		$structures = DB::select("SELECT DISTINCT aff.idStructure, stru.sigle, stru.idParent,stru.active 
									FROM outilcollecte_requete req
									LEFT JOIN outilcollecte_affectation aff ON req.id = aff.idRequete
									LEFT JOIN outilcollecte_structure stru ON stru.id = aff.idStructure
									WHERE stru.idEntite = 1 
									AND stru.type_s = 'dt'
                                    OR stru.type_s = 'dc'
									AND stru.active=1
									AND req.traiteOuiNon = 0
									AND req.dateRequete BETWEEN '$datDebu' and '$datFin'
									ORDER BY stru.sigle ASC ;");

		foreach ($structures as $st) {
			if($st->active == 1){
				
				$structure_id = $st->idStructure;
				$stats = DB::select("SELECT count(*) total
										FROM outilcollecte_requete
										WHERE outilcollecte_requete.traiteOuiNon = 0
										AND outilcollecte_requete.dateRequete BETWEEN '$datDebu' and '$datFin'
										AND outilcollecte_requete.id IN 
										(
											SELECT DISTINCT outilcollecte_affectation.idRequete
											FROM outilcollecte_affectation
											WHERE outilcollecte_affectation.idStructure = $structure_id
										);");
	
				$Tplainte = $stats[0]->total;
				$datasPres[$i]["strcuture"] = $st->sigle;
				$datasPres[$i]["idStructure"] = $st->idStructure;
				$datasPres[$i]["Tplainte"]=$Tplainte;
				//Charger les services
				$stats_serv = DB::select("SELECT DISTINCT ser.id, ser.libelle, count(*) total
							FROM outilcollecte_requete req
							LEFT JOIN outilcollecte_service ser ON req.idPrestation = ser.id
							AND req.traiteOuiNon = 0
							AND  ser.id is not null
							AND  req.dateRequete BETWEEN '$datDebu' and '$datFin'
							AND req.id IN 
							(
								SELECT DISTINCT outilcollecte_affectation.idRequete
								FROM outilcollecte_affectation
								WHERE outilcollecte_affectation.idStructure = $structure_id
							)
							GROUP BY ser.id, ser.libelle
							ORDER BY total DESC
							LIMIT 6;");
				$recu_Serv = "";
				foreach($stats_serv as $serv){
					if($serv->libelle != ""){
						$recu_Serv .= " * ".$serv->libelle." ($serv->total) <br>";
					}
				}
				$datasPres[$i]["serv"]=$recu_Serv;
				$i++;
			}
		}
		
		array_push($tab, $datasPres);	
		
		//DEMANDE D'INFORMATION
		// $structures = Requete::join('outilcollecte_affectation','outilcollecte_affectation.idRequete','outilcollecte_requete.id')
		// 						->join('outilcollecte_structure','outilcollecte_structure.id','outilcollecte_affectation.idStructure')
		// 						->where('outilcollecte_structure.idParent',0)
		// 						->where('outilcollecte_structure.idEntite',1)
		// 						->where('outilcollecte_structure.active',1)
		// 						->where('outilcollecte_requete.plainte',2)
		// 						->whereIn('outilcollecte_structure.type_s',['dt','dc'])
		// 						->whereBetween('outilcollecte_requete.dateRequete',[$dedate,$fidate])
		// 						->select('outilcollecte_affectation.idStructure','outilcollecte_structure.sigle')
		// 						->orderBy('outilcollecte_structure.sigle','asc')
		// 						->distinct()
		// 						->get();
		$structures = Requete::where('outilcollecte_structure.idParent',0)
								->join('outilcollecte_service','outilcollecte_service.id','outilcollecte_requete.idPrestation')
								->join('outilcollecte_structure','outilcollecte_structure.id','outilcollecte_service.idParent')
								->where('outilcollecte_structure.idEntite',1)
								->where('outilcollecte_structure.active',1)
								->where('outilcollecte_requete.plainte',2)
								->whereIn('outilcollecte_structure.type_s',['dt','dc'])
								->whereBetween('outilcollecte_requete.dateRequete',[$dedate,$fidate])
								->select('outilcollecte_structure.id','outilcollecte_structure.sigle')
								->orderBy('outilcollecte_structure.sigle','asc')
								->distinct()
								->get();
								//dd($structures->toArray());
		$datasreq=array();
		$i=0;
		foreach ($structures as $st) {
			// if($st->active == 1){
				$structure_id = $st->id;
				$stats = DB::select("SELECT count(*) total, 
										SUM(CASE WHEN traiteOuiNon = 1 then 1 else 0 end) totalTraite,
										SUM(CASE WHEN traiteOuiNon = 0 then 1 else 0 end) totalNTraite,
										SUM(CASE WHEN archiver = 1 then 1 else 0 end) totalArcive,
										SUM(CASE WHEN horsDelai = 3 and traiteOuiNon = 1 then 1 else 0 end) totalTraiteDansLesDelais,
										SUM(case when ser.nbreJours = 0 then 0 else DATEDIFF(req.dateReponse,req.created_at) end) totalTempTraitement
										FROM outilcollecte_requete req
										LEFT JOIN outilcollecte_service ser ON req.idPrestation = ser.id
										WHERE req.plainte = 2
										AND req.dateRequete BETWEEN '$datDebu' and '$datFin'
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
												AND aff.idStructure = $structure_id
												);");

				$Treq = $stats[0]->total;
				$totalTempTraitement = $stats[0]->totalTempTraitement;
				$reqtraitesTra = $stats[0]->totalTraite;
				$reqtraitesNTra = $stats[0]->totalNTraite;
				$reqtraitesArchive = $stats[0]->totalArcive;
				$reqtraitesTraDDelai = $stats[0]->totalTraiteDansLesDelais;
				
				$pourcent = 0;
				if($Treq!=0 ){
					$pourcent=($reqtraitesNTra*100)/$Treq;
				}
				$pourcentDDelai = 0;
				if($Treq!=0 ){
					$pourcentDDelai=($reqtraitesTraDDelai*100)/$Treq;
				}
				$datasreq[$i]["strcuture"]=$st->sigle;
				$datasreq[$i]["Treq"]=$Treq;
				$datasreq[$i]["totalTempTraitement"] = $totalTempTraitement;
				$datasreq[$i]["reqTrai"]=$reqtraitesTra;
				$datasreq[$i]["reqNonTrai"]=intval($reqtraitesNTra);
				$datasreq[$i]["archiveReq"]=intval($reqtraitesArchive);
				$datasreq[$i]["reqpourcentPNT"]=round($pourcent,2);
				$datasreq[$i]["reqpourcentPDelaiT"]=round($pourcentDDelai,2);
				// EffeServ
				$statsDMoy = DB::select("SELECT DISTINCT ser.id,req.traiteOuiNon,ser.nbreJours, count(*) total,
											SUM(case when ser.nbreJours = 0 then 0 else DATEDIFF(req.dateReponse,req.created_at) end) totalTempTraitement
											FROM outilcollecte_requete req
											LEFT JOIN outilcollecte_service ser ON req.idPrestation = ser.id
											WHERE req.plainte = 2
											AND req.traiteOuiNon = 1
											AND req.dateRequete BETWEEN '$datDebu' and '$datFin'
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
													AND aff.idStructure = $structure_id
												ORDER BY aff.`id` ASC 
											)
											GROUP BY ser.id;");
				$moyen = 0;
				foreach($statsDMoy as $sta){
					if($sta->total != 0){
						$moyen += round($sta->totalTempTraitement) / $sta->total;
					}
				}	
				$datasreq[$i]["moyen"]= round($moyen,2);		
				$i++;
			// }
		} 
		array_push($tab, $datasreq);
		
		
		return $tab;
	}

    public function afficheRapportPf(Request $req){

		
		// FORMAT DE DATE
		$today = new DateTime('now', new DateTimeZone('UTC'));
		$day_of_week = $today->format('w');
		// $today->modify('- ' . (($day_of_week - 1 + 7) % 7) . 'days');
		$sunday = clone $today;
		
		$datDebuday = $today->format('Y-m-d 00:00:00');
		$datDebu = $today->format('2018-01-01 00:00:00');
		$datFin = $sunday->format('Y-m-d 23:59:59');
		$dedate = date_create($datDebu);//Y-m-d
		$fidate = date_create($datFin);
		
		// $premierJour = strftime("2022-04-10 00:00:00", strtotime("this week"));
		$premierJour = strftime("%Y-%m-%d 00:00:00", strtotime("this week"));
		$premdate = date_create($premierJour);
		//Point focal communal 
		
		//Liste des communes qui ont enregistré des requêtes ayant pour profil "POINT FOCAL COMMUNAL "
		$reqCom = Requete::join('outilcollecte_users','outilcollecte_users.id','outilcollecte_requete.created_by')
							->join('outilcollecte_acteur','outilcollecte_acteur.id','outilcollecte_users.idagent')
							->join('outilcollecte_commune','outilcollecte_acteur.idCom','outilcollecte_commune.id')
							->join('outilcollecte_profil','outilcollecte_profil.id','outilcollecte_users.idprofil')
							->where('outilcollecte_profil.pointfocalcom',1)
							->select('outilcollecte_acteur.*','outilcollecte_commune.libellecom')
							->orderby('outilcollecte_acteur.nomprenoms','asc')
							->distinct()
							->get();
							
		$dataUserDay=array();
		$dataUser=array();
		$dataUserRegi=array();
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
												SELECT DISTINCT outilcollecte_affectation.idRequete
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
												SELECT DISTINCT outilcollecte_affectation.idRequete
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

				$dataUserDay[$i]["idcom"]= $com->id;
				$dataUserDay[$i]["commune"]= $com->libellecom;
				$dataUserDay[$i]["nomcom"]= $com->nomprenoms;
				$dataUserDay[$i]["TotalDir_"]=$TotalDir_;
				$dataUserDay[$i]["Totaldir_Tr"]=$Totaldir_Tr;
				$dataUserDay[$i]["Totaldir_NTr"]=$Totaldir_NTr;
				$dataUserDay[$i]["pourcentDIR_NTr"]=round($pourcentDIR_NTr,2);

				$dataUserDay[$i]["TotalPL_"]=$TotalPL_;
				$dataUserDay[$i]["TotalPL_Tr"]=$TotalPL_Tr;
				$dataUserDay[$i]["TotalPL_NTr"]=$TotalPL_NTr;
				$dataUserDay[$i]["pourcentPL_NTr"]=round($pourcentPL_NTr,2);

				//Detail des requetes de chaque utilisateur  
					
				//Detail des requetes de chaque utilisateur 
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
												SELECT DISTINCT outilcollecte_affectation.idRequete
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
												SELECT DISTINCT outilcollecte_affectation.idRequete
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
		// Listes des communes qui ont enregistre des registres 
		$reqCom = Registre::join('outilcollecte_users','outilcollecte_users.id','outilcollecte_registre.created_by')
							->join('outilcollecte_acteur','outilcollecte_acteur.id','outilcollecte_users.idagent')
							->join('outilcollecte_commune','outilcollecte_acteur.idCom','outilcollecte_commune.id')
							->join('outilcollecte_departement','outilcollecte_departement.id','outilcollecte_commune.depart_id')
							->select('outilcollecte_commune.*','outilcollecte_departement.libelle')
							->orderby('outilcollecte_commune.libellecom','asc')
							->distinct()
							->get();
		$i=0;
		// 
		foreach($reqCom as $com){

			$statsDIR = DB::select("SELECT  count(*) total,
									SUM(CASE WHEN satisfait = 'oui' then 1 else 0 end) totalTraite,
									SUM(CASE WHEN satisfait = 'non' then 1 else 0 end) totalNTraite
									FROM outilcollecte_registre, outilcollecte_users,outilcollecte_acteur
									WHERE outilcollecte_registre.created_by = outilcollecte_users.id
									AND outilcollecte_acteur.id = outilcollecte_users.idagent
									AND outilcollecte_acteur.idCom = $com->id
									AND outilcollecte_registre.plainte IN (0,2)");
			$TotalDir_ 		= $statsDIR[0]->total;
			$Totaldir_Tr 	= $statsDIR[0]->totalTraite;
			$Totaldir_NTr 	= $statsDIR[0]->totalNTraite;
			$pourcentDIR_NTr = 0;
			if($TotalDir_!=0 ){
				$pourcentDIR_NTr=($Totaldir_NTr*100)/$TotalDir_;
			}
			$statsPL = DB::select("SELECT  count(*) total,
									SUM(CASE WHEN satisfait = 'oui' then 1 else 0 end) totalTraite,
									SUM(CASE WHEN satisfait = 'non' then 1 else 0 end) totalNTraite
									FROM outilcollecte_registre, outilcollecte_users,outilcollecte_acteur
									WHERE outilcollecte_registre.created_by = outilcollecte_users.id
									AND outilcollecte_acteur.id = outilcollecte_users.idagent
									AND outilcollecte_acteur.idCom = $com->id
									AND outilcollecte_registre.plainte = 1");
			
			$TotalPL_ = $statsPL[0]->total;
			$TotalPL_Tr = $statsPL[0]->totalTraite;
			$TotalPL_NTr = $statsPL[0]->totalNTraite;

			$pourcentPL_NTr = 0;
			if($TotalPL_!=0 ){
				$pourcentPL_NTr=($TotalPL_NTr*100)/$TotalPL_;
			}
			$dataUserRegi[$i]["nomcomr"]= $com->libellecom;
			$dataUserRegi[$i]["TotalDir_r"]=$TotalDir_;
			$dataUserRegi[$i]["Totaldir_Trr"]=$Totaldir_Tr;
			$dataUserRegi[$i]["Totaldir_NTrr"]=$Totaldir_NTr;
			$dataUserRegi[$i]["pourcentDIR_NTrr"]=round($pourcentDIR_NTr,2);

			$dataUserRegi[$i]["TotalPL_r"]=$TotalPL_;
			$dataUserRegi[$i]["TotalPL_Trr"]=$TotalPL_Tr;
			$dataUserRegi[$i]["TotalPL_NTrr"]=$TotalPL_NTr;
			$dataUserRegi[$i]["pourcentPL_NTrr"]=round($pourcentPL_NTr,2);

			$i++;
		}
		// Listes des communes qui ont enregistre des registres 

		// $structures = Structure::where("idParent",0)->get();
		$structures = DB::select("SELECT DISTINCT aff.idStructure, stru.sigle, stru.idParent
									FROM outilcollecte_requete req
									LEFT JOIN outilcollecte_affectation aff ON req.id = aff.idRequete
									LEFT JOIN outilcollecte_structure stru ON stru.id = aff.idStructure
									WHERE stru.idEntite = 1 
									AND stru.idParent = 0
									AND stru.type_s = 'dt'
                                    OR stru.type_s = 'dc'
									AND stru.active=1
									AND req.traiteOuiNon = 0
									AND aff.dateAffectation BETWEEN '$premierJour' and '$datFin';");
		$datas=array();
		$i=0;
		foreach ($structures as $st) {
			$structure_id = $st->idStructure;
			$stats = DB::select("SELECT count(*) total
									FROM outilcollecte_requete
									WHERE outilcollecte_requete.traiteOuiNon = 0
									AND outilcollecte_requete.id IN 
									(
										SELECT DISTINCT outilcollecte_affectation.idRequete
										FROM outilcollecte_affectation
										WHERE outilcollecte_affectation.dateAffectation BETWEEN '$premierJour' and '$datFin'
										AND outilcollecte_affectation.idStructure = $structure_id
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
						AND  ser.id is not null
						AND req.id IN 
						(
							SELECT DISTINCT outilcollecte_affectation.idRequete
							FROM outilcollecte_affectation
							WHERE outilcollecte_affectation.dateAffectation BETWEEN '$premierJour' and '$datFin'
							AND outilcollecte_affectation.idStructure = $structure_id
						)
						GROUP BY ser.id, ser.libelle
						ORDER BY total DESC
						LIMIT 5;");
			$recu_Serv = "";
			foreach($stats_serv as $serv){
				if($serv->libelle != ""){
					$recu_Serv .= " * ".$serv->libelle." ($serv->total) <br>";
				}
			}
			$datas[$i]["serv"]=$recu_Serv;
			$i++;
		}
		// ------------STATISTIQUE DE LA SEMAINE------------------------------------------------------------------------------------------
			$structures = DB::select("SELECT DISTINCT aff.idStructure, stru.sigle, stru.idParent
										FROM outilcollecte_requete req
										LEFT JOIN outilcollecte_affectation aff ON req.id = aff.idRequete
										LEFT JOIN outilcollecte_structure stru ON stru.id = aff.idStructure
										WHERE stru.idEntite = 1 
										AND stru.idParent = 0
										AND stru.active=1
										AND stru.type_s = 'dt'
                                    	OR stru.type_s = 'dc'
										AND req.plainte = 1
										AND aff.dateAffectation BETWEEN '$premierJour' and '$datFin';");
			$datas_we=array();
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
											SELECT DISTINCT outilcollecte_affectation.idRequete
											FROM outilcollecte_affectation
											WHERE outilcollecte_affectation.dateAffectation BETWEEN '$premierJour' and '$datFin'
											AND outilcollecte_affectation.idStructure = $structure_id
										);");
				$Tplainte = $stats[0]->total;
				$traitesTra = $stats[0]->totalTraite;
				$traitesNTra = $stats[0]->totalNTraite;
				$pourcent = 0;
				if($Tplainte!=0 ){
					$pourcent=($traitesNTra*100)/$Tplainte;
				}

				$datas_we[$i]["strcuture"] = $st->sigle;
				$datas_we[$i]["Tplainte"]=$Tplainte;
				$datas_we[$i]["plainteTrai"]=$traitesTra;
				$datas_we[$i]["plainteNonTrai"]=intval($traitesNTra);
				$datas_we[$i]["pourcentPNT"]=round($pourcent,2);
				$i++;
			}
			//REQUÊTES ET DEMANDE D'INFORMATIONS
			$structures = Requete::join('outilcollecte_affectation','outilcollecte_affectation.idRequete','outilcollecte_requete.id')
									->join('outilcollecte_structure','outilcollecte_structure.id','outilcollecte_affectation.idStructure')
									->where('outilcollecte_structure.idParent',0)
									->where('outilcollecte_structure.idEntite',1)
									->where('outilcollecte_structure.active',1)
									->whereIn('outilcollecte_requete.plainte',[0,2])
									->whereBetween('outilcollecte_affectation.dateAffectation',[$premdate,$fidate])
									->select('outilcollecte_affectation.idStructure','outilcollecte_structure.sigle')
									->distinct()
									->get();
			
			$datasreq_we=array();
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
											SELECT DISTINCT outilcollecte_affectation.idRequete
											FROM outilcollecte_affectation
											WHERE outilcollecte_affectation.dateAffectation BETWEEN '$premierJour' and '$datFin'
											AND outilcollecte_affectation.idStructure = $structure_id
										);");

				$Treq = $stats[0]->total;
				$reqtraitesTra = $stats[0]->totalTraite;
				$reqtraitesNTra = $stats[0]->totalNTraite;
				$pourcent = 0;
				if($Treq!=0 ){
					$pourcent=($reqtraitesNTra*100)/$Treq;
				}
				$datasreq_we[$i]["strcuture"]=$st->sigle;
				$datasreq_we[$i]["Treq"]=$Treq;
				$datasreq_we[$i]["reqTrai"]=$reqtraitesTra;
				$datasreq_we[$i]["reqNonTrai"]=intval($reqtraitesNTra);
				$datasreq_we[$i]["reqpourcentPNT"]=round($pourcent,2);
				$i++;
			}

		$peri = self::JourDate($premdate->format('w'))." ".$premdate->format('d/m/Y')." au ".self::JourDate($fidate->format('w'))." ".$fidate->format('d/m/Y');
		$periode = self::JourDate($fidate->format('w'))." ".$fidate->format('d/m/Y');
		$title = "S_T_PF_DES_DIRP_A_LA_DATE_DU_".$fidate->format('YmdHis').".pdf";

		// $logo= env('ASSET_URL').'/img/logo-mtfp.svg';
        PDF::loadView("rapportpf",['datas'=>$datas,'datas_we'=>$datas_we,'datasreq_we'=>$datasreq_we,'dataspf'=>$dataUser,
												'dataspfrevi'=>$dataUserRegi,'dataspfday'=>$dataUserDay,'periode'=>$periode,'peri'=>$peri])
												->setPaper('a4','landscape')->save(Storage::path("public/rapport/".$title));
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

	public static function Detail_Requete($id) {
		$today = new DateTime('now', new DateTimeZone('UTC'));
		$sunday = clone $today;
		$datFin = $sunday->format('Y-m-d 23:59:59');
		// $premierJour = strftime("2022-04-25 00:00:00", strtotime("this week"));
		$premierJour = strftime("%Y-%m-%d 00:00:00", strtotime("this week"));
		
		$statsDIR = DB::select("SELECT DISTINCT req.idPrestation, req.created_at, req.objet, req.plainte
								FROM outilcollecte_requete req, outilcollecte_users,outilcollecte_acteur,outilcollecte_profil
								-- LEFT JOIN outilcollecte_service ser ON req.idPrestation = ser.id
								WHERE req.created_by = outilcollecte_users.id
								-- AND req.idPrestation = ser.id
								AND outilcollecte_acteur.id = outilcollecte_users.idagent
								AND outilcollecte_profil.id = outilcollecte_users.idprofil
								AND outilcollecte_acteur.id = $id
								AND outilcollecte_profil.pointfocalcom = 1
								AND req.id IN 
									(
										SELECT outilcollecte_affectation.idRequete
										FROM outilcollecte_affectation
										WHERE outilcollecte_affectation.dateAffectation BETWEEN '$premierJour' and '$datFin'
									);");
		
		return $statsDIR;
	}

	public static function LibelleService($id) {
		$ser = Service::find($id);
		if($ser){
			return $ser->libelle;
		}else{
			return "-";
		}
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
			$ServiceSearch = Service::with(['service_parent'])->where('idEntite',$idEntite)->orderBy('libelle')->get();
			
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

			$PrestationSearch = Service::where('idType',"=",$thematique)->where('published',1)->orderBy('libelle','asc')->get();

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
