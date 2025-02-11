<?php namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;

use Illuminate\Http\Request;

use App\Models\Type;
use App\Models\Usager;
use App\Models\Service;

use App\Models\Statthematique;

use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use App\Models\Requete;
use App\Models\Noteusager;

use DB;

class MobileController extends Controller {

	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
	}
	


		// Liste des prestations par secteur
		public function getPrestationByType($thematique) {

			try {
	
				$PrestationSearch = Service::where('idType',"=",$thematique)->get();

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



	//delete requete
	public function destroyRequete($id){
		try {
		Requete::find($id)->delete();
		return array("status" => "success", "message" => "La requête a été supprimée avec succès");	

	} catch(\Illuminate\Database\QueryException $ex){
		\Log::error($ex->getMessage());
  		$error=array("status" => "error", "message" => "Une erreur est survenue lors de la suppression. Veuillez contactez l'administrateur" );
  		return $error;
	}catch(\Exception $e){
			\Log::error($e->getMessage());
			$error = array("status" => "error", "message" => $e);
			return $error;
	}
	 }//end destroyRequete


	/**
     * Store a newly created resource in storage.

     *

     * @return Response

     */
public function createUsager(Request $request)
{
        try {

$inputArray =  $request->all();
//verifie les champs fournis
          if (!( isset($inputArray['nom'] )))
          { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }
            $nom='';
            if(isset($inputArray['nom'])) $nom= $inputArray['nom'];
            $email='';
            if(isset($inputArray['email'])) $email= $inputArray['email'];

            $prenoms='';
            if(isset($inputArray['prenoms'])) $prenoms= $inputArray['prenoms'];
            $password='';
            if(isset($inputArray['password'])) $password= $inputArray['password'];

            $tel='';
            if(isset($inputArray['tel'])) $tel= $inputArray['tel'];
            $idDepartement='';
            if(isset($inputArray['idDepartement'])) $idDepartement= $inputArray['idDepartement'];

            $institu_id='';
            if(isset($inputArray['institu_id'])) $institu_id= $inputArray['institu_id'];

            // Enregistrement de l'usager s'il ne l'est pas encore
            $checkusager=Usager::where("email","=",$email)->get();

            if(count($checkusager)==0){

                //Génération du code
                $getcode = DB::table('outilcollecte_usager')->select(DB::raw('max(code) as code'))->get();
                $code=1;
                if(!empty($getcode))
                    $code+=$getcode[0]->code;

                if(($code>0) &&($code<10))
                    $codeComplet="U00000".$code;

                if(($code>=10) &&($code<1000))
                    $codeComplet="U0000".$code;

                if(($code>=1000) &&($code<10000))
                    $codeComplet="U000".$code;

                if(($code>=10000) &&($code<100000))
                    $codeComplet="U00".$code;

                if(($code>=100000) &&($code<1000000))
                    $codeComplet="U0".$code;
                if(($code>=1000000) &&($code<10000000))
                    $codeComplet="U".$code;
                    $usager= new Usager;
                    $usager->nom=$nom;
                    $usager->prenoms=$prenoms;

                    $usager->email=$email;
                    $usager->code=$code;

                    $usager->codeComplet=$codeComplet;

                    $usager->password=$password;

                    $usager->tel=$tel;
                    $usager->idDepartement=$idDepartement;
                    $usager->institu_id=$institu_id;

					$usager->save();

					return array("status" => "success", "message" => "L'usager a été créé avec succès" );
            }
            else {
                $error=array("status" => "error", "message" => "Un usager a été déjà enregistré avec cet email." );
                return $error;
            }

} catch(\Illuminate\Database\QueryException $ex){
          \Log::error($ex->getMessage());

    $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
    return $error;

}catch(\Exception $e){

          \Log::error($e->getMessage());

          $error = array("status" => "error", "message" => $e);
 return $error;
}
	}
	



	public function authusager(Request $request)
{
        try {

            $inputArray =  $request->all();

            $email='';
            if(isset($inputArray['email'])) $email= $inputArray['email'];

            $password='';
            if(isset($inputArray['password'])) $password= $inputArray['password'];

            // Enregistrement de l'usager s'il ne l'est pas encore
            $checkusager=Usager::where("email","like",$email)->where("password","like",$password)->get();

            if(sizeof($checkusager)==0){
                $error=array("status" => "error", "message" => "Email ou mot de passe incorrect",  );
                return $error;
            }
            else{
                $getuser=Usager::find($checkusager[0]->id);
                return $getuser;
            }

    } catch(\Illuminate\Database\QueryException $ex){
          \Log::error($ex->getMessage());

        $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
    }catch(\Exception $e){

          \Log::error($e->getMessage());
          $error = array("status" => "error", "message" => "Une erreur est survenue au cours de l'enregistrement. Veuillez contactez l'administrateur" );
     return $error;
    }
}




		// Liste des annonces par pays et par mot clé
		public function getalldemarches() {

			try {
					$Results = Service::orderBy('id', 'DESC')
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


	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function test()
	{
		try {
			return array("ok" => "yes");

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

			$userconnect = new AuthController;
			$userconnectdata = $userconnect->user_data_by_token($request->token);

            $Type = new Type;
			$Type->libelle = $libelle;
            $Type->idEntite=$request->idEntite;

            $Type->created_by = $userconnectdata->id;
            $Type->updated_by = $userconnectdata->id;
            $Type->save();

            //L'insérer dans la table Stat_thématique
            $StatType = new Statthematique;
            $StatType->idType=$Type->id;
            $StatType->stat=0;
            $StatType->idEntite=$request->idEntite;

            return $this->index();
        }
        catch(\Illuminate\Database\QueryException $ex){
            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Cette étape existe déjà !" );
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
		$TypeSearch = Type::where("id","=",$id)->get();

		if($TypeSearch->isEmpty()){
			return array(
				"status"=>"error",
				"message"=>"Aucune étape retrouvée"
				);
		}
		else {
			$Type = $TypeSearch->first();
			return $Type;
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

                $id = $inputArray['id'];

				$userconnect = new AuthController;
				$userconnectdata = $userconnect->user_data_by_token($request->token);
                // Récuperer lae Type
                $Type = Type::find($id);

				$Type->libelle = $libelle;
	            $Type->created_by = $userconnectdata->id;
	            $Type->updated_by = $userconnectdata->id;

	            $Type->save();

                return $this->index();
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
		Type::find($id)->delete();
		return $this->index();
	}



	// Récupérere libellé
	public function getLine($type) {

		try {

			$Type = Type::where('id',"=",$type)->get();



			return $Type;

		} catch(\Illuminate\Database\QueryException $ex){
        \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
        }catch(\Exception $e){

            \Log::error($e->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue au cours du traitement . Veuillez contactez l'administrateur" );
            return $error;
        }

	}
	
	
	
	//notation dune requete
public function noterRequete(Request $request)

{

        try {

          $idRequete="";

          $noteUsager="";
		  $noteDisponibilite = 0;  $noteOrganisation = 0;



          $inputArray =  $request->all();



          if(isset($inputArray['noteDelai'])) $noteDelai= $inputArray['noteDelai'];

          if(isset($inputArray['noteResultat'])) $noteResultat= $inputArray['noteResultat'];

          if(isset($inputArray['noteDisponibilite'])) $noteDisponibilite= $inputArray['noteDisponibilite'];

          if(isset($inputArray['noteOrganisation'])) $noteOrganisation= $inputArray['noteOrganisation'];
  

          if(isset($inputArray['codeRequete'])) $codeRequete= $inputArray['codeRequete'];



          $commentaireNotation="";

          if(isset($inputArray['commentaireNotation'])) $commentaireNotation= $inputArray['commentaireNotation'];



          //Enregistrement note

          $check=Noteusager::where("codeReq","=",$codeRequete)->get();



          if(count($check)!=0)

            return array("status" => "success", "message" => "Vous avez déjà donné une fois votre appréciation de la prestation.");



          $note=new Noteusager;

          $note->codeReq=$codeRequete;

          $note->noteDelai=$noteDelai;

          $note->noteResultat=$noteResultat;

          $note->noteDisponibilite=$noteDisponibilite;

          $note->noteOrganisation=$noteOrganisation;

          $note->commentaireNotation=$commentaireNotation;
          $note->idEntite=$request->idEntite;

          $note->save();



          //$noteMoy = ($noteDisponibilite+$noteResultat+$noteResultat+$noteOrganisation)/3;

          $noteMoy = ($noteResultat+$noteDelai)/2;

          $req=Requete::where('codeRequete','=',$codeRequete)->update(['noteUsager' => $noteMoy]);

				return array("status" => "success", "message" => "Appréciation enregistrée avec succès");


        } catch(\Illuminate\Database\QueryException $ex){

                \Log::error($ex->getMessage());



            $error=array("status" => "error", "message" => "Une erreur est survenue lors de la notation de cette requête. Veuillez contactez l'administrateur" );
			return $error;



        }catch(\Exception $ex){



          \Log::error($ex->getMessage());



          $error =  array("status" => "error", "message" => "Une erreur inattendue est survenue lors du" .

                             " de la notation de cette requête. Veuillez contactez l'administrateur" );

         return $error;

        }

    }//end noterRequete
	
	
	//get requete finalisees
public function getRequeteFinaliseesByUsager($idusager,Request $request)
    {
        try {

            //$input = Request::all();
            $input =  $request->all();

            //return $input;
            $result=array();
            if(isset($input['search']))     //$request->get('search'))
            {
              $search=$input['search'];
              $result = Requete::with(['usager','service', 'notes','nature','etape','parcours','affectation'])
              ->orderBy('id','desc')
              ->where("idUsager","=",$idusager)
              ->where("finalise","=",1)
              ->where("objet", "LIKE", "%{$search}%")
              ->orWhere("msgrequest", "LIKE", "%{$search}%")
              ->orWhere("codeRequete", "LIKE", "%{$search}%")
              ->paginate(10);
            }
            else{
              $result = Requete::with(['usager','service','notes','nature','etape','parcours','affectation'])->orderBy('id','desc')
              ->where("idUsager","=",$idusager)
              ->where("finalise","=",1)
              ->paginate(10);
            }


            if(count($result)>0)
            {
              return $result;
            }
            else{
              $error =
              array("status" => "error", "message" => "Aucune requête ou plainte trouvée." );
               return $error;
            }


        return $result;

        } catch(\Illuminate\Database\QueryException $ex){
          \Log::error($ex->getMessage());

        $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
        }catch(\Exception $ex){

          \Log::error($ex->getMessage());

          $error = array("status" => "error", "message" => "Une erreur est survenue lors du" .
                             " chargement des connexions. Veuillez contactez l'administrateur" );
         return $error;
        }
    }//end getRequeteFinaliseesByUsager




}
