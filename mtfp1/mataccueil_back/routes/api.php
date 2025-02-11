<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// Route::middleware('auth:api')->get('/user', function (Request $request) {

//     return $request->user(); 
// });
Route::get('rapportconsult','ServiceController@afficheRapport_consult');
Route::get('relanceRegistre','ServiceController@Affiche_relanceRegistre');
Route::get('/rapportGraph','ServiceController@afficheRapportGraphe');

Route::post("intra", 'IntranetController@index');
Route::get("listRequ/{author}", 'IntranetController@listRequ');
Route::get("listru", 'IntranetController@listructure');
Route::get("typeSygec/{ident}", 'IntranetController@listype')->where('type', '[0-9]+');
Route::get('serviceSygec/{type}','IntranetController@listprest')->where('type', '[0-9]+');
Route::post('getaddRequeSygec','IntranetController@AddRequete');
Route::get("/print-requete", 'StatistiqueController@print');
Route::get("/print-registre", 'StatistiqueController@printregistre');
Route::get("/print-registre-stat", 'StatistiqueController@printregistreStat');
Route::get('lispfc','UtilisateurController@listpfc');
Route::get('liscommune/{id_u}','RequeteController@listCommune')->where('id_u', '[0-9]+');
Route::get('lisuser/{idcom}','RequeteController@listUsersComm')->where('idcom', '[0-9]+');

Route::group(['prefix' => 'api-outil-collecte-test',  ], function()
{
    /* pda et mataccueil usager */
    Route::post('auth', 'AuthController@signin');
    Route::post('usager/create','MobileController@createUsager');
    Route::get('structure/{idEntite}', 'StructureController@index-old'); //-old a été ajouté. Il y a plusieurs liens relier à cette meme methode index 
    Route::get('type/{idEntite}','TypeController@index');
    Route::get('service/structure/{structure}','ServiceController@getPrestationByStructure')->where('structure', '[0-9]+');
    //Route::get('service/type/{type}','ServiceController@getPrestationByType')->where('type', '[0-9]+');
    Route::get('service/type/{type}','MobileController@getPrestationByType')->where('type', '[0-9]+');
    Route::get('service/search/{keyword}','MobileController@search');
    Route::get('evt/{idEntite}','EvtController@index');

    Route::get('service/searchdemarches/all','MobileController@getalldemarches');
    Route::get('departements','DepartementController@index');
    Route::post('authusager', 'MobileController@authusager');

    Route::get('requeteusager/getrequetebyusager/{id}','RequeteController@getRequeteByUsager');
    Route::get('requeteusager/getrequetebyusagerNT/{id}','RequeteController@getRequeteByUsagerNonTrai');
	Route::get('requeteusager/getrequetesfinaliseesbyusager/{id}','MobileController@getRequeteFinaliseesByUsager');
    Route::post('requeteusager/externe','RequeteController@createRequestAsUsager'); //

    Route::post('rdv','RdvController@store');
    Route::get('rdvcreneau/disponible','RdvcreneauController@creneauDisponible');
    Route::get('daterdv/actif','DaterdvController@getDateActif');
    Route::get('rdv/usager/{idUsager}','RdvController@getRdvByUsager')->where('id', '[0-9]+');

    /*  mataccueil usager */ 
    Route::get('auth/userdata', 'AuthController@user_data');
    Route::get('requeteusager/get/{idEntite}','RequeteController@getRequeteByUser');
    
    Route::get('statistiques/nbre/{user}/{plainte}/{idEntite}','StatistiqueController@getStatbyStructure');
    Route::get('statistiques/nbreCour/{user}/{plainte}/{idEntite}','StatistiqueController@getStatbyStructureCour');
    Route::post('statistiques/nbre/{idEntite}','StatistiqueController@getStatbyStructureR');
    Route::delete('requeteusager/{id}', 'MobileController@destroyRequete');
    Route::post('requeteusager/transmettre/externe','RequeteController@transmettreRequete');

    Route::post('requeteusager/transmettre/reponse/rapide','RequeteController@transmettreReponserapideUsager');
    Route::post('affectation/nouvelle','AffectationController@store')->where('id', '[0-9]+');
    //    Route::get('structure/get/{iduser}','StructureController@getListeStructure');

    Route::post('requeteusager/externe/{id}','RequeteController@update')->where('id', '[0-9]+');
	Route::post('noter','MobileController@noterRequete');
    Route::post('rdv/statut','RdvController@saveStatut');

});

Route::delete('rdv/{id}','RdvController@destroy');


Route::get('get-together-views','AdvancedStatisticsController@getTogetherViews');
Route::get('get-together-views2','AdvancedStatisticsController@getTogetherViews2');
Route::get('get-performances','AdvancedStatisticsController@getPerformances');
Route::get('get-performances-visists','AdvancedStatisticsController@getPerformancesVisits');
Route::post('print-view','AdvancedStatisticsController@printView');


Route::resource('rdv','RdvController',['only' => ['store','destroy']]);

Route::get('auth', 'AuthController@certifier');
Route::get('eservices', 'EserviceController@index');
Route::get('typestructures', 'TypeStructureController@index');
Route::get('naturecontracts', 'NatureContractController@index');
Route::get('settings2', 'SettingController@show');

//
//Route::group(['prefix' => 'api-outil-collecte'], function()
//{
 	/* Authentification */
    Route::post('auth', 'AuthController@signin');
    Route::get('user_last_logout/{id}', 'AuthController@logout_user');
    Route::post('authpfc', 'AuthController@signinpfc');
    Route::get('auth/userdata', 'AuthController@user_data');
    Route::get('auth/userdatamat', 'AuthController@user_datamat');
    Route::post('reset-password', 'AuthController@resetPassword');

    /* Utilisateurs */
    Route::post('utilisateur/profil/{update}','UtilisateurController@updateprofil');
    Route::get('utilisateur/total','UtilisateurController@getCountUtilisateurTotal');
    Route::post('utilisateur/{id}','UtilisateurController@update')->where('id', '[0-9]+');
    Route::resource('utilisateur','UtilisateurController',['only' => ['store','destroy']]);
    Route::get('utilisateur/{idEntite}','UtilisateurController@index');
    Route::get('acteurcom/{idEntite}','UtilisateurController@ListeActeurs');
    Route::get('utilisateurs/all/main','UtilisateurController@allMain');
    
    
    Route::put('utilisateur/changepassword','UtilisateurController@changePassword');
    Route::put('utilisateur/checkresetcode','UtilisateurController@checkPasswordResetCode');
    Route::put('utilisateur/changepasswordonconfirm','UtilisateurController@changePasswordOnConfirm');

    Route::post('relance','RelanceController@store');
    Route::get('relance/{idEntite}','RelanceController@index');
    
    /* Profils */
    Route::post('profilGuide/{id}','ProfilController@updateGuide')->where('id', '[0-9]+');
    Route::post('profil/{id}','ProfilController@update')->where('id', '[0-9]+');
    Route::get('profil/getprofil/{id}','ProfilController@edit')->where('id', '[0-9]+');
    Route::resource('profil','ProfilController',['only' => ['store','destroy']]);
    Route::get('profil','ProfilController@index');
    Route::get('profil/main','ProfilController@indexMain');
    Route::get('downloadFileGuide','ProfilController@DownloaFile');
    
    
    
/* Usagers */
    Route::post('usager/{id}','UsagerController@update')->where('id', '[0-9]+');
    Route::resource('usager','UsagerController',['only' => ['index','store','destroy']]);
    Route::post('authusager', 'UsagerController@authusager');
    Route::post('authuser', 'UsagerController@authusers');
    Route::put('usager/changepassword','UsagerController@changePassword');
    Route::put('usager/checkresetcode','UsagerController@checkPasswordResetCode');
    Route::put('usager/changepasswordonconfirm','UsagerController@changePasswordOnConfirm');

/* Etape courrier */
    Route::post('etape/{id}','EtapecourrierController@update')->where('id', '[0-9]+');
    Route::resource('etape','EtapecourrierController',['only' => ['store','destroy']]);
    Route::get('etape/{idEntite}','EtapecourrierController@index');

/* Commentaire */
    Route::post('comment/{id}','CommentaireController@update')->where('id', '[0-9]+');
    Route::resource('comment','CommentaireController',['only' => ['store','destroy']]);
    Route::get('comment','CommentaireController@index');
    Route::get('downloadFileCom','CommentaireController@DownloaFile');

/* Evenement declencheur */
    Route::post('evt/{id}','EvtController@update')->where('id', '[0-9]+');
    Route::resource('evt','EvtController',['only' => ['store','destroy']]);
    Route::get('evt/{idEntite}','EvtController@index');
    Route::get('evt/getLine/{id}','EvtController@getLine')->where('id', '[0-9]+');

/* Nature*/
    Route::post('nature/{id}','NatureController@update')->where('id', '[0-9]+');
    Route::resource('nature','NatureController',['only' => ['store','destroy']]);
    Route::get('nature/{idEntite}','NatureController@index');

/* Acteur*/
    Route::post('acteur/{id}','ActeurController@update')->where('id', '[0-9]+');
    Route::resource('acteur','ActeurController',['only' => ['store','destroy']]);
    Route::get('acteur/{idEntite}','ActeurController@index');
    Route::get('acteur_stat/{idEntite}','ActeurController@All_acteur');

/* Usager*/
    Route::post('usager/{id}','UsagerController@update')->where('id', '[0-9]+');
    Route::resource('usager','UsagerController',['only' => ['index','store','destroy']]);


/* Rdvcreneau*/
    Route::post('rdvcreneau/{id}','RdvcreneauController@update')->where('id', '[0-9]+');
    Route::resource('rdvcreneau','RdvcreneauController',['only' => ['store','destroy']]);
    Route::get('rdvcreneau/{idEntite}','RdvcreneauController@index');
    Route::get('rdvcreneau/disponible','RdvcreneauController@creneauDisponible');


/* Daterdv*/
    Route::post('daterdv/{id}','DaterdvController@update')->where('id', '[0-9]+');
    Route::resource('daterdv','DaterdvController',['only' => ['store','destroy']]);
    Route::get('daterdv/{idEntite}','DaterdvController@index');
    Route::get('daterdv/actif/{idEntite}','DaterdvController@getDateActif');




/* Rdvjour*/
    Route::post('rdvjour/{id}','RdvjourController@update')->where('id', '[0-9]+');
    Route::resource('rdvjour','RdvjourController',['only' => ['store','destroy']]);
    Route::get('rdvjour/{idEntite}','RdvjourController@index');

/* Rdvparametre*/
    Route::post('rdvparametre/{id}','RdvparametreController@update')->where('id', '[0-9]+');
    Route::resource('rdvparametre','RdvparametreController',['only' => ['store','destroy']]);
    Route::get('rdvparametre/{idEntite}','RdvparametreController@index');

/* Rdv*/
    Route::post('rdv/{id}','RdvController@update')->where('id', '[0-9]+');
    Route::resource('rdv','RdvController',['only' => ['store']]);
    Route::get('rdv/{idEntite}','RdvController@index');
    Route::get('rdv/byStructure/{idStructure}','RdvController@getRdvByStructure');
    Route::post('rdv/statut','RdvController@saveStatut');
    Route::get('rdv/usager/{idUsager}','RdvController@getRdvByUsager')->where('id', '[0-9]+');




/* Departement*/
    Route::post('departement/{id}','DepartementController@update')->where('id', '[0-9]+');
    Route::resource('departement','DepartementController',['only' => ['index','store','destroy']]);
    Route::get('commune/{id}','DepartementController@updateCommune')->where('id', '[0-9]+');


/* Registre*/
    Route::post('registreup/{id}','RegistreController@update')->where('id', '[0-9]+');
    Route::resource('registre','RegistreController',['only' => ['index','store','destroy']]);
    Route::resource('ccsp-reports','CcspReportController');
    Route::resource('report-transmissions','ReportTransmissionController');
    Route::get('ccsp-reports-pending','CcspReportController@getPending');
    Route::get('ccsp-reports-validation/{id}','CcspReportController@validation');
    Route::get('requeteRv/getrequetebypfcRv/{id}','RegistreController@getRequeteByPfcRv');

/* Echange Whatsapp */
    Route::post('echangeup/{id}','EchangeWhatsController@update')->where('id', '[0-9]+');
    Route::post('echangeupreponse/{id}','EchangeWhatsController@updatereponse')->where('id', '[0-9]+');
    Route::get('echangeConfi/{idUser}/{id}','EchangeWhatsController@confirmerTraitement');
    Route::resource('echange','EchangeWhatsController',['only' => ['index','store','destroy']]);
    Route::get('getEchangeWhat','EchangeWhatsController@getEchangeWhatsApp');
    
    /* Cloture registre*/
    Route::get('nbrDay/{idUser}','ClotureregistreController@getListDay')->where('idUser', '[0-9]+');;
    Route::resource('cloturerv','ClotureregistreController',['only' => ['index','store','destroy']]);
    Route::get("/apercuimage", 'ClotureregistreController@apercuDeLimage');


/* Affectation */
    Route::post('affectation/{id}','AffectationController@update')->where('id', '[0-9]+');
    Route::resource('affectation','AffectationController',['only' => ['store','destroy']]);
    Route::get('affectation/get','AffectationController@getListeAffectation');
    Route::get('affectation/{idEntite}','AffectationController@index');

    Route::resources([
        'e-services' =>'EserviceController',
        'type-structures' =>'TypeStructureController',
        'nature-contracts' =>'NatureContractController',
        'ccsps' =>'CcspController'
    ]);
    Route::get('e-services/{id}/state/{state}', 'EserviceController@setState');
    Route::get('type-structures/{id}/state/{state}', 'TypeStructureController@setState');
    Route::get('nature-contracts/{id}/state/{state}', 'NatureContractController@setState');
    Route::get('ccsps/{id}/state/{state}', 'CcspController@setState');
    Route::resource('settings','SettingController');



/* Requete*/

    Route::post('question','RequeteController@transmettreQuestion');
    Route::get('suggestion','SuggestionController@index');
    Route::post('denonciation','DenonciationController@store');
    Route::get('denonciation','DenonciationController@index');

    Route::post('requeteusager/{id}','RequeteController@update')->where('id', '[0-9]+');
    Route::post('requeteusagerpfc/{id}','RequeteController@updatePfc')->where('id', '[0-9]+');
    Route::resource('requeteusager','RequeteController',['only' => ['store','destroy']]);
    Route::get('requeteusager/{idEntite}','RequeteController@index');

    Route::post('requeteusager/externe','RequeteController@createRequestAsUsager'); //
    Route::post('requeteusager/externe/{id}','RequeteController@update')->where('id', '[0-9]+');

    Route::post('requeteusager/transmettre/externe','RequeteController@transmettreRequete');
    Route::post('requeteusager/transfert/entite/{id}','RequeteController@transfertRequeteEntite');
    Route::post('requeteusager/transfert/structure/{id}','RequeteController@transfertRequeteStructure');
    
    Route::get('requeteusager/relance/{id}','RequeteController@relancerRequete');
    Route::get('requeteusager/relanceType/{id}/{idStru}/{idStruRela}','RequeteController@relancerRequeteType');
    
    Route::get('requeteusager/get/{idEntite}','RequeteController@getRequeteByUser'); 
    Route::get('requeteusager/get_stat/{idEntite}','RequeteController@getRequeteByUser_Stat'); 
    Route::get('registreusager/get/{idEntite}','RequeteController@getRegistreByUser'); 
    Route::get('download/pdf','RequeteController@downloadDataToPDF');

    Route::post('requeteusager/savereponse','RequeteController@saveReponse');
    Route::post('requeteusager/archivereque','RequeteController@ArchiverRequete');
    Route::post('requeteusager/modifierReque','RequeteController@ModifierReque');
    Route::get('downloadFile','RequeteController@DownloaFile');

    Route::post('requeteusager/transmettre/reponse','RequeteController@transmettreReponse');
    Route::post('requeteusager/transmettre/relance','RequeteController@relanceReponse');

    Route::post('requeteusager/transmettre/reponse/rapide','RequeteController@transmettreReponserapideUsager');
    Route::post('requeteusager/mail/rapide/structure','RequeteController@mailStructure');
    Route::post('requeteusager/mail/rapide/reponse/complement','RequeteController@complementReponserapide');
    Route::get('requeteusager/mail/rapide/reponse/{responsId}/get','RequeteController@findReponserapide');
    

    Route::get('requeteusager/getrequetebyusager/{id}','RequeteController@getRequeteByUsager');
    Route::get('requetepfc/getrequetebypfc/{id}','RequeteController@getRequeteByPfc');
    Route::get('requeteusager/getrequetebyusagerNT/{id}','RequeteController@getRequeteByUsagerNonTrai');
    
    
    Route::post('requeteusager/fichierjoint','RequeteController@envoiFichier');
    Route::post('requetecomment/transmettre','RequeteController@transmettreComment');


    Route::post('usagers/externe/requete_new','RequeteController@store2'); //
    Route::post('usagers/externe/rdv_new','RdvController@createRdvExterne'); //
    Route::post('usagers/externe/je_denonce','RequeteController@je_denonce'); //
    
    
    Route::post('usagers_retraites/download','RequeteController@downloadDataToPDF'); //

    
    //stats
    Route::get('statistiques/nbre/{user}/{plainte}/{idEntite}','StatistiqueController@getStatbyStructure');
    Route::get('statistiques/all-strucuture/{plainte}/{idEntite}','StatistiqueController@getStatByAllStructure');

    Route::get('statistiques/get/stat/{type}/{plainte}/{idEntite}','StatistiqueController@getStatbyType');
    Route::get('statistiques/type/{type}/{plainte}/{idEntite}','StatistiqueController@getNbrebyType');
    Route::get('stats/nbre/{structure}/{plainte}/{idEntite}','StatistiqueController@getNbrebyStructure');

    Route::get('statistiques/year/{plainte}/{year}/{idEntite}','StatistiqueController@getNbrebyYear');

    //stats reviewed
    Route::post('statistiques/nbre/{idEntite}','StatistiqueController@getStatbyStructureR');
    Route::post('statistiques/get/stat/{idEntite}','StatistiqueController@getStatbyTypeR');
    Route::post('statistiques/type/{idEntite}','StatistiqueController@getNbrebyTypeR');
    Route::post('stats/nbre/{idEntite}','StatistiqueController@getNbrebyStructureR');
    
    Route::post('statistiques/prestations/{idEntite}','StatistiqueController@getNbrebyPrestation');
    
    Route::post('statistiques/reponses/{idEntite}','StatistiqueController@getStatReponse');
    Route::get('statistiques/prestations-par-structure/{idEntite}','StatistiqueController@getStatPrestationbyStructure');
    

    Route::get('/users-set-state/{id}/state/{state}', 'UtilisateurController@setStatus');


   //ratio reviewed
    Route::post('ratio/requeteprestations/{idEntite}','StatistiqueController@getRatioByRequetePrestationTraitees');
    Route::post('ratio/requeteprestationsencours/{idEntite}','StatistiqueController@getRatioByRequetePrestationEnCours');

    Route::post('ratio/plainteprestations/{idEntite}','StatistiqueController@getRatioByPlaintePrestationTraitees');
    Route::post('ratio/plainteprestationsencours/{idEntite}','StatistiqueController@getRatioByPlaintePrestationEnCours');

    Route::post('ratio/requeteservices/{idEntite}','StatistiqueController@getRatioByRequeteServiceTraitees');
    Route::post('ratio/requeteservicesencours/{idEntite}','StatistiqueController@getRatioByRequeteServiceEnCours');

    Route::post('ratio/plainteservices/{idEntite}','StatistiqueController@getRatioByPlainteServiceTraitees');
    Route::post('ratio/plainteservicesencours/{idEntite}','StatistiqueController@getRatioByPlainteServiceEnCours');

    Route::post('ratio/demandesinfosprestations/{idEntite}','StatistiqueController@getRatioByDemandeInfosPrestationTraitees');
    Route::post('ratio/demandesinfosprestationsencours/{idEntite}','StatistiqueController@getRatioByDemandeInfosPrestationEnCours');


    Route::post('ratio/demandesinfosservices/{idEntite}','StatistiqueController@getRatioByDemandeInfosServiceTraitees');
    Route::post('ratio/demandesinfosservicesencours/{idEntite}','StatistiqueController@getRatioByDemandeInfosServiceEnCours');

/* Services */
    Route::post('service/{id}','ServiceController@update')->where('id', '[0-9]+');
    Route::resource('service','ServiceController',['only' => ['store','destroy']]);
    Route::get('service/{idEntite}','ServiceController@index');
    Route::get('service/byCreator','ServiceController@getByCreator');
    Route::get('service/byStructure/{idStructure}','ServiceController@getByStructure');
    Route::get('servicePiece/{idSer}','ServiceController@ServiceDetailPiece');
    Route::get('/relreq','ServiceController@relanceArchierat');
/* Attribution des comm */
    Route::post('attri/{id}','AttribuController@update')->where('id', '[0-9]+');
    Route::resource('attri','AttribuController',['only' => ['store','destroy']]);
    Route::get('attri/{iduser}','AttribuController@index');
    

    Route::get('service/structure/{structure}','ServiceController@getPrestationByStructure')->where('structure', '[0-9]+');

    Route::get('service/type/{type}','ServiceController@getPrestationByType')->where('type', '[0-9]+');

    Route::get('service/data/{table}','ServiceController@getCount')->where('table', '[a-z]+');

    Route::get('service/search/{keyword}','ServiceController@search');

    /* Piece */
    Route::post('service/savepiece','ServiceController@savePiece');

    /* Institution */
    Route::resource('institution','InstitutionController',['only' => ['store','index','destroy']]);
    Route::post('institution/{id}','InstitutionController@update')->where('id', '[0-9]+');
    Route::get('ministere','InstitutionController@listEntite_Requete');
    Route::get('allministere','InstitutionController@listEntite');

    /* Relance */
    Route::resource('relanceconfig','RelanceConfigController',['only' => ['store','destroy']]);
    Route::post('relanceconfig/{id}','RelanceConfigController@update')->where('id', '[0-9]+');
    Route::get('relanceconfig/{id}','RelanceConfigController@index')->where('id', '[0-9]+');
    Route::get('lisuserRelance/{identite}','RelanceConfigController@listUsers')->where('identite', '[0-9]+');

    /* Statistique par thématique */
    Route::post('statthematique/{id}','StatthematiqueController@update')->where('id', '[0-9]+');
    Route::resource('statthematique','StatthematiqueController',['only' => ['store','destroy']]);
    Route::get('statthematique/{idEntite}','StatthematiqueController@index');

    /* ElementdeclserviceController par thématique */
    Route::post('elementdeclservice/{id}','ElementdeclserviceController@update')->where('id', '[0-9]+');
    Route::resource('elementdeclservice','ElementdeclserviceController',['only' => ['store','destroy']]);
    Route::get('elementdeclservice/{idEntite}','ElementdeclserviceController@index');

    Route::post('elementdecl/{id}','ElementdeclController@update')->where('id', '[0-9]+');
    Route::get('elementdecl/getLine/{id}','ElementdeclController@getLine')->where('id', '[0-9]+');
    Route::resource('elementdecl','ElementdeclController',['only' => ['store','destroy']]);
    Route::get('elementdecl/{idEntite}','ElementdeclController@index');


    
/* Faq service */
    Route::post('faq/{id}','FaqController@update')->where('id', '[0-9]+');
    Route::resource('faq','FaqController',['only' => ['index','store','destroy']]);
    Route::get('faq/getLine/{faq}','FaqController@getLine')->where('faq', '[0-9]+');


    /* structure */
    Route::post('structure/{id}','StructureController@update')->where('id', '[0-9]+');
    Route::resource('structure','StructureController',['only' => ['store','destroy']]);
    Route::get('structure/{onlyDirection}/{idEntite}','StructureController@index');
    Route::get('structure/{idEntite}','StructureController@index_new');
    Route::get('structure/{idEntite}','StructureController@taux_digita');
    Route::get('structurethema/{idtype}','StructureController@ListStrucThem');
    Route::get('structurePreocc/{idEntite}','StructureController@ListStrucPreocc');
    

    Route::get('structure/getLine/{structure}','StructureController@getOne')->where('structure', '[0-9]+');
    Route::get('structure/get/sub/{iduser}','StructureController@getListeSubStructure');
    //Route::get('structure/direction/{onlyDirection}/{idEntite}','StructureController@index-Direc');


    /* Type service */
    Route::post('type/{id}','TypeController@update')->where('id', '[0-9]+');
    Route::resource('type','TypeController',['only' => ['store','destroy']]);
    Route::get('type/{idEntite}','TypeController@index');

    Route::get('type/getLine/{type}','TypeController@getOne')->where('type', '[0-9]+');

    Route::get('usagers/parcours/{email}/{codeRequete}','RequeteController@getRequeteByCode');

    Route::post('noter','RequeteController@noterRequete');

    Route::get('home/countrequest/{iduser}','RequeteController@getCountRequeteByUser');


    Route::post('genererpdf','GeneratorController@genererPDF');

    Route::post('genererpdfstat','GeneratorController@genererPDFStat');
    Route::post('genererpdfstathebdo','GeneratorController@genererPDFStatHebdo');
    Route::post('/auth-me', 'AuthController@signin2');



//}
//);


