<?php namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;

use Illuminate\Http\Request;

use App\Models\Structure;

use App\Models\Acteur;

use App\Models\Utilisateur;

use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use DB,DateTime,DateTimeZone,PDF;

class StructureController extends Controller {

	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('jwt.auth',['except' =>['index','index_new','getOne','taux_digita']]);
    }


	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	public function index($onlyDirection=0,$idEntite=0)
	{
		
		try {
			// return response($onlyDirection);
			if($onlyDirection==1)
				$StructureSearch = Structure::where('idEntite',$idEntite)->where("idParent","=","0")->orderBy("libelle","asc")->get();

			
			else $StructureSearch = Structure::where('idEntite',$idEntite)->orderBy("libelle","asc")->get();

			foreach ($StructureSearch as $Structure) {
				$Structure->structure_parent;
				$Structure->services;
			}

			return $StructureSearch;

		} catch(\Illuminate\Database\QueryException $ex){
            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );

		  }catch(\Exception $e){

            \Log::error($e->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur" );
            return $error;
      }
	}

	public function index_new($idEntite=0)
	{
		
		try {
			return Structure::where('idEntite',$idEntite)->where("type_s","dt")->orderBy("libelle","asc")->get();

		} catch(\Illuminate\Database\QueryException $ex){
            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );

		  }catch(\Exception $e){

            \Log::error($e->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur" );
            return $error;
      }
	}

	public function taux_digita($idEntite, Request $request)
	{
		try {
			$structures = DB::select("SELECT stru.*
										FROM outilcollecte_structure stru
										WHERE stru.active=1
										AND stru.type_s = 'dt';");
			$datas=array();
			$i=0;
			foreach ($structures as $st) {
				$structure_id = $st->id;
				$stats = DB::select("SELECT count(*) total,
										SUM(CASE WHEN access_online = 1 then 1 else 0 end) totalOnline
										FROM outilcollecte_service serv
										WHERE serv.idParent = $structure_id ;");
				$total_ = $stats[0]->total;
				$totalOnline = intval($stats[0]->totalOnline);
				$pourcent = 0;
				if($total_!=0 ){
					$pourcent=($totalOnline*100)/$total_;
				}
				$datas[$i]["id"] = $st->id;
				$datas[$i]["libelle"] = $st->libelle;
				$datas[$i]["sigle"] = $st->sigle;
				$datas[$i]["total"] = $total_;
				$datas[$i]["taux"] = round($pourcent,2);
				$datas[$i]["totalOnline"] = intval($stats[0]->totalOnline);
				
				$i++;
			}
			// 

			$search = $request->get('imp');

			if (isset($search)) {
				$name="Taux-Digit-".date('Ymdhist');
				$titre = "Taux de digitilisation par structure";
				$data=['data'=> $datas,'name'=> $name,'titre'=> $titre];
				$pdf = PDF::loadView('tauxdigit', $data)->setPaper('a4', 'landscape');
				return $pdf->download($name.'.pdf');
			}
			return response()->json($datas);

		} catch(\Illuminate\Database\QueryException $ex){
            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );

		  }catch(\Exception $e){

            \Log::error($e->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur".$e->getMessage() );
            return $error;
      }
	}

	public function ListStrucThem($idtype)
	{
		
		try {
			$stats = DB::select("SELECT DISTINCT str.id, str.libelle
									FROM `outilcollecte_service` ser, outilcollecte_structure str
									WHERE ser.idParent = str.id
									AND str.idParent = 0
									AND str.active=1
									AND str.type_s = 'dt'
                                    OR str.type_s = 'dc'
									AND ser.idType = '$idtype'
									ORDER BY str.libelle;");
			return $stats;

		} catch(\Illuminate\Database\QueryException $ex){
            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );

		  }catch(\Exception $e){

            \Log::error($e->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur" );
            return $error;
      }
	}

	public function ListStrucPreocc($idEntite)
	{
		
		try {
			$today = new DateTime('now', new DateTimeZone('UTC'));
			
			$sunday = clone $today;

			$datDebu = $today->format('2010-01-01 00:00:00');
			$datFin = $sunday->format('Y-m-d 23:59:59');
			
			//Récuperer toutes les requêtes non traité ::  AND user.idprofil = 23 Directeur
			$structures = DB::select("SELECT DISTINCT aff.idStructure, stru.sigle, stru.idParent, user.email, stru.libelle 
											FROM outilcollecte_requete req
											LEFT JOIN outilcollecte_affectation aff ON req.id = aff.idRequete
											LEFT JOIN outilcollecte_structure stru ON stru.id = aff.idStructure
											LEFT JOIN outilcollecte_acteur act ON stru.id = act.idStructure
											LEFT JOIN outilcollecte_users user ON user.idagent = act.id
											WHERE stru.idEntite = $idEntite 
											AND stru.idParent = 0
											AND user.idprofil = 23
											AND req.traiteOuiNon = 0
											AND stru.active=1
											AND aff.dateAffectation BETWEEN '$datDebu' and '$datFin';");
			return $structures;

		} catch(\Illuminate\Database\QueryException $ex){
            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );

		  }catch(\Exception $e){

            \Log::error($e->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur" );
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



            $libelle = $inputArray['libelle'];
            $sigle = $inputArray['sigle'];
            $contact = $inputArray['contact'];
            $active = $inputArray['active'];
            $type_s = $inputArray['type_s'];

            $idParent=0;
	            if(isset($inputArray['idParent']))
	            	$idParent = $inputArray['idParent'];


			$userconnect = new AuthController;
			$userconnectdata = $userconnect->user_data_by_token($request->token);

            $Structure = new Structure;
			$Structure->libelle = $libelle;
			$Structure->sigle = $sigle;
			$Structure->contact = $contact;
			$Structure->idParent = $idParent;
			$Structure->active = $active;
			$Structure->type_s = $type_s;

			$Structure->idEntite=$request->idEntite;
            $Structure->created_by = $userconnectdata->id;
            $Structure->updated_by = $userconnectdata->id;
            $Structure->save();

            return $this->index(0,$Structure->idEntite);
        }
        catch(\Illuminate\Database\QueryException $ex){
            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur inattendue est survenue !" );
            //\Log::error($ex->getMessage());
            return $error;
        }
        catch(\Exception $ex){
          \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors de " .
                "votre tentative de connexion. Veuillez contactez l'administrateur" );
            //\Log::error($ex->getMessage());
            //return $error;

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
		$StructureSearch = Structure::where("id","=",$id)->get();

		if($StructureSearch->isEmpty()){
			return array(
				"status"=>"error",
				"message"=>"Aucune étape retrouvée"
				);
		}
		else {
			$Structure = $StructureSearch->first();
			return $Structure;
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
	            $sigle = $inputArray['sigle'];
	            $contact = $inputArray['contact'];
	            $active = $inputArray['active'];
	            $type_s = $inputArray['type_s'];

	            $idParent=0;
	            if(isset($inputArray['idParent']))
	            	$idParent = $inputArray['idParent'];

                $id = $inputArray['id'];

				$userconnect = new AuthController;
				$userconnectdata = $userconnect->user_data_by_token($request->token);
                // Récuperer lae Structure
                $Structure = Structure::find($id);

				$Structure->libelle = $libelle;
				$Structure->sigle = $sigle;
				$Structure->contact = $contact;
				$Structure->active = $active;
				$Structure->type_s = $type_s;
				$Structure->idParent = $idParent;
	            $Structure->created_by = $userconnectdata->id;
	            $Structure->updated_by = $userconnectdata->id;

	            $Structure->save();

                return $this->index(0,$Structure->idEntite);
           }
            catch(\Illuminate\Database\QueryException $ex){
              \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Erreur de connexion à la base de données.");
            return $error;
        }catch(\Exception $e){

            \Log::error($e->getMessage());

            $error = array("status" => "error", "message" =>"Une erreur est survenue. Veuillez contacter l'administrateur." );
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
		Structure::find($id)->delete();
		return array('success' => true );
	}


	// Récupérere libellé
	public function getOne($structure) {

		try {

			$Structure = Structure::where('id',"=",$structure)->first();

			return $Structure;

		} catch(\Illuminate\Database\QueryException $ex){
          \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
        }catch(\Exception $e){

            \Log::error($e->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue au cours du traitement . Veuillez contactez l'administrateur" );
            return $error;
        }

	}


	// Liste des courriers
	public function getListeSubStructure($idUser) {
		try {
			$getuser=Utilisateur::find($idUser);
			
			$result =array();
			
			$idagent=$getuser->idagent;
			
			$getAgent=Acteur::find($idagent);
			
			$idStructure="";
			
			if($getAgent !== null)           //if(count($getAgent)>0)
			{
				$idStructure=$getAgent->idStructure;
				
				$result = Structure::with(['structure_parent'])->where('idParent',$idStructure)->orderBy('libelle')->get();
				// return response([$idStructure,$result]);
				}


				return $result;

		} catch(\Illuminate\Database\QueryException $ex){

		      \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" );
        }catch(\Exception $e){

          \Log::error($e->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue au cours du traitement . Veuillez contactez l'administrateur" );
            return $error;
        }

	}


}
