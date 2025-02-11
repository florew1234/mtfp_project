<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;
use App\Helpers\Factory\ParamsFactory;

use Illuminate\Http\Request;

use App\User;
use App\Models\Clotureregistre;
use App\Models\Registre;
use App\Models\Commune;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use App\Utilities\FileStorage;
use DB,PDF,Mail,DateTime,DateTimeZone,Str,File;

class ClotureregistreController extends Controller {

	
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('jwt.auth',['except' =>['index','apercuDeLimage','getListDay']]);
    }
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($iduser)
	{
		// try {
		// 	$attribu = Clotureregistre::where('id_user',$iduser)->with(['acteur_att','commune','commune.departement'])
		// 						->get();
		// 	return $attribu;

		// } catch(\Illuminate\Database\QueryException $ex){
        //     \Log::error($ex->getMessage());

        //     $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contacter l'administrateur" );
        // }catch(\Exception $ex){

        //     \Log::error($ex->getMessage());

        //     $error = array("status" => "error", "message" => "Une erreur est survenue lors du" .
        //              " chargement des connexions. Veuillez contacter l'administrateur" );
        //     return $error;
        // }
	}

	
    public function getListDay($idUser){
        try {
			$use = User::find($idUser);
            //Recuperer toutes les dates de début jusqu'a ce jour sans le week-end 
			if($use){ $date =  $use->date_start_registre; }else{ $date =  "2022-06-01"; }
			
            $date_debut = strtotime($date); 
            $date_fin = strtotime(date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d')))));
            // $date_fin = strtotime(date("Y-m-d"));
			// return response($date_fin);
            $nbreJour = round(($date_fin - $date_debut)/60/60/24,0); //le nombre de jour entre deux dates

            $req = Clotureregistre::where('id_user',$idUser)->select('date_cloture')
                                    ->pluck('date_cloture')->toArray();
            $tablgiwu=[];
            for ($i=0; $i <= $nbreJour; $i++) { 
                $date_terminee = date('Y-m-d', strtotime('+'.$i.' day', strtotime($date)));
                
                if(date_create($date_terminee)->format('w') <> 6 && date_create($date_terminee)->format('w') <> 0){ //Extrait le week-end
                    if(!in_array($date_terminee,$req)){  //Verifier si $date_terminee est dans le tableau
                        $tablgiwu[$i]['id'] = $i;
                        $tablgiwu[$i]['datec'] = $date_terminee;
                        $tablgiwu[$i]['jour'] = date_create($date_terminee)->format('w');
                    }
                }
            }
            $Resultat = new Collection($tablgiwu);

			return response($Resultat->pluck('datec'));
            
        } catch(\Illuminate\Database\QueryException $ex){

            \Log::error($ex->getMessage());
            return array("status" => "error", "message" => $ex->getMessage()."Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );

        }catch(\Exception $e){
                \Log::error($e->getMessage());
                $error = array("status" => "error", "message" => "Une erreur est survenue lors du" .
                            " chargement des connexions. Veuillez contactez l'administrateur".$e->getMessage() );
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

			$id_user=null;
			if(isset($inputArray['id_user'])){$id_user = $inputArray['id_user'];}
			
			$date_cloture=null;
			if(isset($inputArray['date_cloture'])){$date_cloture = $inputArray['date_cloture'];}
			
			$nbrvisite=0;
			if(isset($inputArray['nbrvisite'])){$nbrvisite = $inputArray['nbrvisite'];}

			//return response($inputArray);
			$dateDebut = DateTime::createFromFormat('Y-m-d H:i:s', $date_cloture.' 00:00:00');
			$dateFin = DateTime::createFromFormat('Y-m-d H:i:s', $date_cloture.' 23:59:59');
			
			// $regis = Registre::where("created_by", "=", $id_user)->where("created_at", ">=", $dateDebut)->where("created_at", "<=", $dateFin)->count();
			// if($regis != $nbrvisite ) {
			// 	return array("status" => "error","success" => false, "message" =>"Le nombre de visite renseigné n'est pas conforme à celui du sytème. Prière complèter." );
			// }
			
			$fileName = "";
			if ($request->file('fichier')) {
				$file = $request->file('fichier');
				$extension = strtolower($file->getClientOriginalExtension());
				if($extension != 'jpeg' && $extension != 'jpg' && $extension != 'png'){
					return array("status" => "error","success" => false, "message" =>"Le fichier doit être de type image (*.jpeg, *.jpg, *.png)" );
				}
				if($extension == 'png'){ $extension = 'jpeg'; }
				$fileName = $id_user."U".date('Y-m-d-His').rand(1,99999).'.'.$extension;
				// Storage::disk('public')->put('registre/'.$fileName, File::get($file));
				$pathName = Storage::path("public/registre/");
                $file->move($pathName, $fileName);
			} 
			
			// return response($regis);
			
            $att = new Clotureregistre;
			$att->id_user = $id_user;
			$att->date_cloture = $date_cloture;
			$att->nbrvisite = $nbrvisite;
			$att->fichier_cloture = $fileName;
            $att->save();

			//Charger la valeur de cloture 
			Registre::where("created_by", "=", $id_user)->where("created_at", ">=", $dateDebut)->where("created_at", "<=", $dateFin)->update([ 
				'cloture' => 1,
			]);

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
                "votre tentative de connexion. Veuillez contacter l'administrateur".$ex->getMessage() );
			return $error;
        }

	}

	public function apercuDeLimage(Request $request){   

		$date = $request->get('date');
		$iduser = $request->get('iduser');
		$idcom = $request->get('idcom');
		
		$User = User::join('outilcollecte_acteur','outilcollecte_users.idagent','outilcollecte_acteur.id')
						->where('outilcollecte_acteur.idCom',$idcom)
						->where('outilcollecte_users.id',$iduser)
						->select('outilcollecte_acteur.nomprenoms')
						->distinct()
						->first()
						->toArray();

		//recuperer la photo charger 
		$giwu['nameimg'] = '';
		if($date < "2022-05-31"){
			$giwu['infos'] = 'Cette date : '.date('d/m/Y',strtotime($date))." n'est pas inclue dans les dates à cloturer.";
		}else{
			$clotur = Clotureregistre::where('date_cloture',$date)->where('id_user',$iduser)->first();
			if(isset($clotur)){
				if($clotur->fichier_cloture != ""){
					$giwu['nameimg'] = $clotur->fichier_cloture;
				}else{
					$giwu['infos'] = 'Aucune image (Régistre physique) n\'a été associée à la date : '.date('d/m/Y',strtotime($date));
				}
			}else{
				$giwu['infos'] = 'Cette date : '.date('d/m/Y',strtotime($date))." n'a pas encore été cloturée par l'agent.";
			}
		}
		
		$com = Commune::with(['departement'])->where('id',$idcom)->first();
		$giwu['titre'] = "PFC : ".$User['nomprenoms']." - ". $com->libellecom." - ".$com->departement->libelle;
		
		$pdf = PDF::loadView('image', $giwu)->setPaper('a4', 'landscape');
		return $pdf->stream($iduser."_".$date.'.pdf');
	  }
	  
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$ServiceSearch = AttribCom::where("id","=",$id)->get();

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
	public function update(Request $request)
	{
				try{
                $inputArray =  $request->all();
				$id = $inputArray['id'];
                //verifie les champs fournis
                if (!isset($id)) {
                    return array("status" => "error",
                        "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
                }

            	$idComm = $inputArray['idComm'];
            	$id_user = $inputArray['id_user'];
				$check = AttribCom::where('id_user',$id_user)->where('id_com',$idComm)->first();
				if(isset($check)){
					if($check->id != $id){
						return array("status" => "error","success" => false );
					}
				}
				$att = AttribCom::find($inputArray['id']);
				$att->id_com = $idComm;
	            $att->save();

				return array("status" => "success","success" => true );
           }
            catch(\Illuminate\Database\QueryException $ex){
              \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue au cours du traitement. Veuillez reessayer plus tard.");
            return $error;
        }catch(\Exception $ex){

          \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" =>"Une erreur est survenue. Veuillez reessayer plus tard.".$ex->getMessage());
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


}
