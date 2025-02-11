<?php namespace App\Http\Controllers;

use App\Helpers\Factory\ParamsFactory;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;

use App\Models\Usager;
use App\Models\Acteur;
use App\Models\Institution;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Input;

use Hash;
use DB;
use App\Helpers\Carbon\Carbon;

class UtilisateurController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('jwt.auth', ['except' => ['changePassword', 'checkPasswordResetCode', 'changePasswordOnConfirm','listpfc'  ]]);
    }


	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($idEntite)
	{
		try {

			$UtilisateurSearch = Utilisateur::where('idEntite',$idEntite)
                    ->with('profil_user','agent_user','entity')
                    ->orderBy('id','desc')
                    ->get();
			foreach ($UtilisateurSearch as $Utilisateur) {
				$Utilisateur->profil_user;
              $Utilisateur->agent_user;
			}

			return $UtilisateurSearch;


		} catch(\Illuminate\Database\QueryException $ex){

            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
        }catch(\Exception $e){
            $error = array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur".$e->getMessage() );
            return $error;
        }
	}
	public function ListeActeurs($idEntite)
	{
		try {

			$acteur = Acteur::where('idEntite',$idEntite)
                    ->orderBy('nomprenoms','asc')
                    ->whereIn('idTypeacteur',["2","3","4"])
                    ->get();
			return $acteur;
		} catch(\Illuminate\Database\QueryException $ex){

            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
        }catch(\Exception $e){
            $error = array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur".$e->getMessage() );
            return $error;
        }
	}

  public function allMain()
	{
		try {

			$UtilisateurSearch = Utilisateur::with('profil_user','agent_user','agent_user.structure','attribuCom')->get();
      $response=[];
      $i=0;
      foreach ($UtilisateurSearch as $Utilisateur) {
          if($Utilisateur->profil_user!=null && ($Utilisateur->profil_user->saisie==1 || $Utilisateur->profil_user->admin_sectoriel==1)){
            $response[$i]=$Utilisateur;
            $response[$i]['entite']=Institution::find($Utilisateur->idEntite);
            $i++;
          }
			}
      // dd($response);
			return $response;

    } catch(\Illuminate\Database\QueryException $ex){

            \Log::error($ex->getMessage());
            return [$ex->getMessage()];

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
      }catch(\Exception $e){
          $error = array("status" => "error", "message" => "Une erreur est survenue lors du" .
                    " chargement des connexions. Veuillez contactez l'administrateur" );
          return $error;
      }
	}


	public function listpfc()
	{
      $point = Acteur::join('outilcollecte_users','outilcollecte_users.idagent','outilcollecte_acteur.id')
                      ->join('outilcollecte_commune','outilcollecte_commune.id','outilcollecte_acteur.idCom')
                      ->orderby('outilcollecte_acteur.nomprenoms',"asc")
                      ->where('outilcollecte_users.typeUserOp','p') //point focal communal
                      // ->pluck('outilcollecte_acteur.nomprenoms','outilcollecte_acteur.id');
                      ->select('outilcollecte_acteur.nomprenoms','outilcollecte_users.id','outilcollecte_commune.libellecom')
                      ->get();
			return $point;
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
          if (!( isset($inputArray['email'])
           && isset($inputArray['password']) && isset($inputArray['statut'])
            && isset($inputArray['profil'])
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }

            $profil = $inputArray['profil'];
            $email = $inputArray['email'];
            $password = $inputArray['password'];
            $statut = false;
            if(isset($inputArray['statut']))
                $statut=$inputArray['statut'];

          
            $checkuserexist = Utilisateur::where("email","=",$email)->get();

            if(!$checkuserexist->isEmpty())
            	return array("status" => "error", "message" => "Cet utilisateur existe déjà !" );

            $userconnect = new AuthController;
		      	$userconnectdata = $userconnect->user_data_by_token($request->token);

            $utilisateur = new Utilisateur;
            $utilisateur->idprofil = $profil;
            if($profil == 34){ //Point focal communal
              $utilisateur->typeUserOp = 'p'; //Privilégier
            }else{
              $utilisateur->typeUserOp = 'o'; //Ordinaire
            }
            $utilisateur->email = $email;
            $utilisateur->password = Hash::make($password);
            $utilisateur->statut = $statut;
            $utilisateur->idEntite=$request->idEntite;
            if(isset($inputArray['idagent'])){
              $utilisateur->idagent = $inputArray['idagent'];
            }
            $utilisateur->date_start_registre = date('Y-m-d');
            $utilisateur->created_by = $userconnectdata->id;
            $utilisateur->updated_by = $userconnectdata->id;
            $utilisateur->save();

            return $this->index($request->idEntite);
        }
        catch(\Illuminate\Database\QueryException $ex){
          \Log::error($ex->getMessage());

            $error = array("status" => $ex, "message" => "Une erreur est survenue lors de l'enregistrement. Veuillez contacter l'administrateur.");
            //\Log::error($ex->getMessage());
            return $error;
        }
        catch(\Exception $ex){
          \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenur lors de l'exécution de la requête. Veuillez contacter l'administrateur." );
            //\Log::error($ex->getMessage());
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
          if (!( isset($inputArray['id']) && isset($inputArray['email'])
           && isset($inputArray['password']) && isset($inputArray['statut'])
           && isset($inputArray['profil'])
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }

            $profil = $inputArray['profil'];
            $email = $inputArray['email'];
            $password = $inputArray['password'];
            $statut = $inputArray['statut'];
            
            /*    
                        $checkuserexist  = Utilisateur::where("idagent","=",$idagent)->where("id","<>",$id)->get();
                        if(!$checkuserexist->isEmpty())
                          return array("status" => "error", "message" => "Un autre utilisateur ayant les mêmes informations existe déjà !" );
                        // Récuperer l'utilisateur'
            */
            $userconnect = new AuthController;
			$userconnectdata = $userconnect->user_data_by_token($request->token);

             $utilisateur = Utilisateur::find($id);
                $utilisateur->idprofil = $profil;
                $utilisateur->email = $email;
                if($password!='')
                    $utilisateur->password = Hash::make($password);
                $utilisateur->statut = $statut;
                if(isset($inputArray['idagent'])){
                  $utilisateur->idagent = $inputArray['idagent'];
                }
                if($profil == 34){ //Point focal communal
                  $utilisateur->typeUserOp = 'p'; //Privilégier
                }else{
                  $utilisateur->typeUserOp = 'o'; //Ordinaire
                }
                $utilisateur->updated_by = $userconnectdata->id;
                $utilisateur->save();

                return $this->index($request->idEntite);
        }
        catch(\Illuminate\Database\QueryException $ex){
          \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Cet utilisateur existe déjà !" );
            //\Log::error($ex->getMessage());
            return $error;
        }
        catch(\Exception $ex){
          \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors de " .
                "votre tentative de connexion. Veuillez contactez l'administrateur" );
            //\Log::error($ex->getMessage());
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
    $user=Utilisateur::find($id);
    $idEntite=$user->idEntite;
    $user->delete();
		return $this->index($idEntite);
	}

    public function updateprofil(Request $request){

        try{
        $UtilisateurSearch = Utilisateur::findOrFail($request->IdUtilisateur);

        if(!empty($UtilisateurSearch)) {
            if($request->newemail !="")
                $UtilisateurSearch->email = $request->newemail;

        if($request->newpassword !="")
                $UtilisateurSearch->password = Hash::make($request->newpassword);
        $UtilisateurSearch->save();
        return array("status" =>"success",
            "message" =>"Profil mise à jour");
        }
        return array("status" =>"error",
            "message" =>"Cet utilisateur n'existe pas");

        }
         catch(\Illuminate\Database\QueryException $ex){
           \Log::error($ex->getMessage());

           $error = array("status" => "error", "message" => "Cet utilisateur existe déjà !" );
            //\Log::error($ex->getMessage());
            return $error;
        }
        catch(\Exception $ex){
          \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors de " .
                "votre tentative de connexion. Veuillez contactez l'administrateur" );
            //\Log::error($ex->getMessage());
            return $error;
        }
    }

    public function getCountUtilisateurTotal(Request $request) {

		try {

			$countusers = DB::table('users')->count();
			return $countusers;
		}
		catch(\Illuminate\Database\QueryException $ex){
          \Log::error($ex->getMessage());

          $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
        }catch(\Exception $e){

            \Log::error($e->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue au cours du traitement . Veuillez contactez l'administrateur" );
            return $error;
        }
	}

  /**
   * Change user password.
   *
   * @return Response
   */
  public function changePassword(Request $request)
  {
    try {

      //recup les champs fournis
      $inputArray =  $request->all();

      //verifie les champs fournis
      if (!( isset($inputArray['email'])
      ))  { //controle d existence
        return array("status" => "error",
          "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
      }
      $email = $inputArray['email'];

      //get the user
      $userSearch = Utilisateur::where("email", "like", $email)
        ->get();
      if($userSearch->isEmpty()){
        return array("status" => "error",
          "message" => "Cet utilisateur n'est pas valide");
      }
      $foundUser = $userSearch->first();

      //generer un code de 40 chiffres
      $codeGenerated = ParamsFactory::generateAleaCode(100);
      $expDate = Carbon::now()->addHour()->addMinute(10);

      //mettre à jour la table concernee
      $foundUser->password_reset_code = $codeGenerated;
      $foundUser->password_reset_expiration = $expDate;
      $foundUser->save();

      //envoyer le mail
      $passwordChangeLink = "https://demarchesmtfp.gouv.bj/mataccueil/connexion/#/usagerchgpwdconfirm/". $codeGenerated;
      $texte = "Veuillez cliquer sur le lien ci-dessous pour modifier votre mot de passe"; $sujet = "Changement de votre mot de passe";
      $texte = $texte. '<a href="'. $passwordChangeLink. '"> Cliquez ici pour modifier votre mot de passe </a>';

      RequeteController::sendmail($foundUser->email, $texte, $sujet);

      return array("status" => true, "message" => "Un mail a été envoyé dans votre boîte électronique");

    } catch(\Illuminate\Database\QueryException $ex){
      \Log::error($ex->getMessage());

      $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
      return $error;

    }catch(\Exception $e){

      \Log::error($e->getMessage());

      $error = array("status" => "error", "message" => "Une erreur est survenue lors du" . " chargement des connexions. Veuillez contactez l'administrateur" );
      return $error;
    }
  }//end changePassword

  /**
   * Check password reset code
   *
   * @return Response
   */
  public function checkPasswordResetCode(Request $request)
  {
    try {

      //recup les champs fournis
      $inputArray =  $request->all();

      //verifie les champs fournis
      if (!( isset($inputArray['code'])
      ))  { //controle d existence
        return array("status" => "error",
          "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
      }
      $code = $inputArray['code'];

      //get the user
      $userSearch = Utilisateur::where("password_reset_code", "like", $code)
        ->get();
      if($userSearch->isEmpty()){
        return array("status" => "error",
          "message" => "Cet utilisateur n'est pas valide");
      }
      $foundUser = $userSearch->first();

      //verifier
      if($foundUser->password_reset_used === true){
        return array("status" => "error",
          "message" => "Votre demande de modification de mot de passe ne peut aboutir. Veuillez réessayer.");
      }
      $user = array("email" => $foundUser->email);

      return array("status" => true, "message" => "", "data" => $user );

    } catch(\Illuminate\Database\QueryException $ex){
      \Log::error($ex->getMessage());

      $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" );
      return $error;

    }catch(\Exception $e){

      \Log::error($e->getMessage());

      $error = array("status" => "error", "message" => "Une erreur est survenue lors du" . " chargement des connexions. Veuillez contactez l'administrateur" );
      return $error;
    }
  }//end checkPasswordResetCode


  /**
   * Change password on confirmation
   *
   * @return Response
   */
  public function changePasswordOnConfirm(Request $request)
  {
    try {

      //recup les champs fournis
      $inputArray =  $request->all();

      //verifie les champs fournis
      if (!( isset($inputArray['password1']) && isset($inputArray['password2']) && isset($inputArray['email'])
      ))  { //controle d existence
        return array("status" => "error",
          "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
      }
      $email = $inputArray['email'];
      $password1 = $inputArray['password1'];
      $password2 = $inputArray['password2'];

      //get the user
      $userSearch = Utilisateur::where("email", "like", $email)
        ->get();
      if($userSearch->isEmpty()){
        return array("status" => "error",
          "message" => "Cet utilisateur n'est pas valide");
      }
      $foundUser = $userSearch->first();

      //check password
      if($password1 !== $password2){
        return array("status" => "error",
          "message" => "Le mot de passe et la confirmation ne sont pas identiques");
      }

      //check expiration
      $currentDate = Carbon::now()->addHour();
      $expDate = Carbon::createFromFormat("Y-m-d H:i:s", $foundUser->password_reset_expiration) ;

      if($currentDate > $expDate ){
        return array("status" => "error",
          "message" => "Le changement du mot de passe a expiré. Veuillez effectuer une nouvelle requête de changement.");
      }

      //update password
      $foundUser->password = Hash::make($password1);
      $foundUser->save();

      return array("status" => true, "message" => "", "data" => "" );

    } catch(\Illuminate\Database\QueryException $ex){
      \Log::error($ex->getMessage());

      $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" );
      return $error;

    }catch(\Exception $e){

      \Log::error($e->getMessage());

      $error = array("status" => "error", "message" => "Une erreur est survenue lors du" . " chargement des connexions. Veuillez contactez l'administrateur" );
      return $error;
    }
  }//end changePasswordOnConfirm


  public function setStatus($id,$status)
  {
      $user=Utilisateur::find($id);
      $user->update(['is_active' =>$status]);
      return response()->json([
          "success"=>true,
          "message"=>"Status mis à jour avec succès",
          "data"=>null
      ],200);
  }

}
