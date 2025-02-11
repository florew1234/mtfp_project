<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;

use Illuminate\Http\Request;

use App\Models\Commentaire;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use DB;

class CommentaireController extends Controller {

	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('jwt.auth', ['except' => ['index','DownloaFile']]);
    }


	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */


	public function DownloaFile(Request $request){

        return response()->download(Storage::path("public/rapport/").$request->get('file'));
     }

	public function index()
	{
		try {
			
			return Commentaire::orderBy('date_fin_com','desc')->get();

		} catch(\Illuminate\Database\QueryException $ex){
      \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur".$ex->getMessage() );
        }catch(\Exception $e){

            \Log::error($e->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur".$e->getMessage() );
            return $error;
        }
	}

     // questions / suggestions de l'usager


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
            //Génération du code
			$pj = "";
			if ($request->file('fichier')) {
				//Vérifier les dates 
				if($inputArray['date_debut_com'] > $inputArray['date_fin_com'] ){
					return array("status" => "error", "message" => "Les dates sont incorrectes" );
				}
				$file = $request->file('fichier');
				$extension = $file->getClientOriginalExtension();
				if($extension != "pdf"){
					return array("status" => "error", "message" => "Le fichier doit être de type pdf" );
				}
				//Déplacer le fichier
				$fileName = $inputArray['date_debut_com'].'-'.$inputArray['date_fin_com'].'.'.$extension;
				$pathName = Storage::path("/public/rapport/");
				$file->move($pathName, $fileName);
				$pj = $pathName.$fileName;

				//Ajouter dans la base...
				$Commentaire = new Commentaire;
				$Commentaire->num_enreg = $inputArray['id_init'].date('YmdHis');
				$Commentaire->date_debut_com = $inputArray['date_debut_com'];
				$Commentaire->date_fin_com = $inputArray['date_fin_com'];
				$Commentaire->fichier_joint = $fileName;
				$Commentaire->commentaire = $inputArray['commentaire'];
				$Commentaire->id_init = $inputArray['id_init'];
				$Commentaire->save();
				return array("status" => "succes", "message" => "Ajout effectué avec succès");

			}else{
				return array("status" => "error", "message" => "Aucun fichier n'est importé");
			}
			
        }catch(\Illuminate\Database\QueryException $ex){
          \Log::error($ex->getMessage());
            $error = array("status" => "error", "message" => "Cette étape existe déjà !".$ex->getMessage() );
            return $error;
        }
        catch(\Exception $ex){
          \Log::error($ex->getMessage());
            $error = array("status" => "error", "message" => "Une erreur est survenue lors de votre tentative de connexion. Veuillez contactez l'administrateur" );
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
		// $Comm = Commentaire::where("id",$id)->first();

		
	}

	/**
	 * update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Request $request,$id)
	{
		try{
			//recup les champs fournis
			$inputArray = $request->all();

			// $id = $inputArray['id'];
			// return response($inputArray);
			// Récuperer lae Commentaire
			$req = Commentaire::where('id_comment',$inputArray['id_comment'])
						->update([
							'date_debut_com' => $inputArray['date_debut_com'],
							'date_fin_com' => $inputArray['date_fin_com'],
							'commentaire' => $inputArray['commentaire']
						]);

			return array("status" => "succes", "message" => "Modification effectuée avec succès");
		}
		catch(\Illuminate\Database\QueryException $ex){
			\Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Erreur de connexion à la base de données.".$ex->getMessage());
            return $error;
        }catch(\Exception $e){

          \Log::error($e->getMessage());

            $error = array("status" => "error", "message" =>"Une erreur est survenue. Veuillez contacter l'administrateur.".$e->getMessage() );
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
		Commentaire::where('id_comment',$id)->delete();
		return array('success' => true );
	}


}
