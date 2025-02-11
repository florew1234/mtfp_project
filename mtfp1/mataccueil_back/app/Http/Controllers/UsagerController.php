<?php
 namespace App\Http\Controllers;
use App\Helpers\Factory\ParamsFactory;
use App\Http\Requests;


use App\Http\Controllers\Controller;


use App\Http\Controllers\AuthController;


use Illuminate\Http\Request;

use App\Models\Usager;

use App\Models\Departement;
use Hash;
use App\Helpers\Carbon\Carbon;

use DB;
use Illuminate\Support\Facades\Input;

class UsagerController extends Controller
{

public function __construct() {
    $this->middleware('jwt.auth', ['except' => ['index','store','update','authusager', 'changePassword', 'checkPasswordResetCode', 'changePasswordOnConfirm']]);

}


/**
     * Display a listing of the resource.

     *

     * @return Response

     */

    public function index(Request $request)
    {
      try {
        $input = $request->all();

        if($request->get('search')){
            $search=$request->get('search');
            $items = Usager::with(['departement'])->orderBy('nom')->orderBy('prenoms')->where("nom", "LIKE", "%{$request->get('search')}%")
                ->orWhere("prenoms", "LIKE", "%{$search}%")
                ->orWhere("email", "LIKE", "%{$search}%")
                ->orWhere("tel", "LIKE", "%{$search}%")
                ->orWhere(DB::raw("CONCAT(`nom`, ' ', `prenoms`)"), "LIKE", "%{$search}%")
                ->orWhereHas('departement', function($q) use($search) {
                $q->where('libelle',"LIKE", "%{$search}%");
                })->paginate(10);
        }else{
          $items = Usager::orderBy('id','desc')->with(['departement'])->paginate(10);
        }
        return response($items);


        } catch(\Illuminate\Database\QueryException $ex){
          \Log::error($ex->getMessage());

        $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
        }catch(\Exception $e){

            \Log::error($e->getMessage());

            $error = array("status" => "error", "message" => $e );
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
            
            $prenoms='';
            if(isset($inputArray['prenoms'])) $prenoms= $inputArray['prenoms'];

            $email='';
            if(isset($inputArray['email'])){
              $email= $inputArray['email'];
            }else{
              $nomE =self::enleverCaracteresSpeciaux($nom);
              $prenomE =self::enleverCaracteresSpeciaux($prenoms);
              $email = strtolower($nomE).strtolower(substr($prenomE,0,1));
              $checkEmail=Usager::where("email","=",$email)->count();

              if($checkEmail != 0){$email = $email."1@gmail.com";}else{$email = $email."@gmail.com";}
            }

            $password='';
            if(isset($inputArray['password'])){
              $password= $inputArray['password'];
            }else{
              $password= '123';
            }
            // 
            
            // 
            $tel='';
            if(isset($inputArray['tel'])) $tel= $inputArray['tel'];
            $idDepartement='';
            if(isset($inputArray['idDepartement'])) $idDepartement= $inputArray['idDepartement'];

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

                    $usager->save();
            }else{
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
/**
     * update a newly created resource in storage.

     *

     * @return Response

     */
public function update($id,Request $request)
{
        try {
$inputArray =  $request->all();
//verifie les champs fournis
          if (!( isset($inputArray['nom']) && isset($inputArray['id'])
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }
        $nom='';
        if(isset($inputArray['nom'])) $nom= $inputArray['nom'];
        $email='';
        if(isset($inputArray['email'])) $email= $inputArray['email'];

        $prenoms='';
        if(isset($inputArray['prenoms'])) $prenoms= $inputArray['prenoms'];

        if(isset($inputArray['password'])) $password= $inputArray['password'];

        $tel='';
        if(isset($inputArray['tel'])) $tel= $inputArray['tel'];
        $idDepartement='';
        if(isset($inputArray['idDepartement'])) $idDepartement= $inputArray['idDepartement'];

        $usager=Usager::find($id);
        $usager->nom=$nom;
        $usager->email=$email;
        $usager->tel=$tel;
        $usager->idDepartement=$idDepartement;

        $usager->prenoms=$prenoms;

        if(isset($inputArray['password'])) $usager->password= $inputArray['password'];


        /*$userconnect = new AuthController;
        $userconnectdata = $userconnect->user_data_by_token($request->token);*/
        $usager->created_by = 1;
        $usager->save();


} catch(\Illuminate\Database\QueryException $ex){
          \Log::error($ex->getMessage());

          $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" );
}catch(\Exception $e){

          \Log::error($e->getMessage());

          $error = array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur" );
 return $error;
}

 }
/**
     * Remove the specified resource from storage.
     *
     * @param  int  id
     * @return Response
     */

public function destroy($id){
    Usager::find($id)->delete();
 }

  function enleverCaracteresSpeciaux($text) {
    $utf8 = array(
    '/[áàâãªä]/u' => 'a',
    '/[ÁÀÂÃÄ]/u' => 'A',
    '/[ÍÌÎÏ]/u' => 'I',
    '/[íìîï]/u' => 'i',
    '/[éèêë]/u' => 'e',
    '/[ÉÈÊË]/u' => 'E',
    '/[óòôõºö]/u' => 'o',
    '/[ÓÒÔÕÖ]/u' => 'O',
    '/[úùûü]/u' => 'u',
    '/[ÚÙÛÜ]/u' => 'U',
    '/ç/' => 'c',
    '/Ç/' => 'C',
    '/ñ/' => 'n',
    '/Ñ/' => 'N',
    );
    return str_replace('-','',str_replace("'", '', preg_replace(array_keys($utf8), array_values($utf8), $text)));
  }
 public function authusager(Request $request)
{
        try {

            $inputArray =  $request->all();
            $email='';
            if(isset($inputArray['email'])) {$email= $inputArray['email'];}

            //$password='';
            //if(isset($inputArray['password'])) $password= $inputArray['password'];

            // Enregistrement de l'usager s'il ne l'est pas encore
            $checkusager=Usager::where("email","=",$email)->get(); //->where("password","=",$password)

            if(count($checkusager)==0){

                $nom='';
                if(isset($inputArray['lastname'])) { $nom= $inputArray['lastname'];}
                $email='';
                if(isset($inputArray['email'])) {$email= $inputArray['email'];}
    
                $prenoms='';
                if(isset($inputArray['firstname'])) {$prenoms= $inputArray['firstname'];}
    
                $password= 'default';
    
                $tel='';
                if(isset($inputArray['phone'])) {$tel= $inputArray['phone'];}
                $institu_id='';
                if(isset($inputArray['institu_id'])) {$institu_id= $inputArray['institu_id'];}
                
                $idDepartement='4';
               // if(isset($inputArray['idDepartement'])) $idDepartement="0" ; //$inputArray['idDepartement']
    
                //Génération du code
                $getcode = DB::table('outilcollecte_usager')->select(DB::raw('max(code) as code'))->get();
                $code=1;
                if(!empty($getcode))
                   { $code+=$getcode[0]->code;}
    
                if(($code>0) &&($code<10))
                 {   $codeComplet="U00000".$code;}
    
                if(($code>=10) &&($code<1000))
                    {$codeComplet="U0000".$code;}
    
                if(($code>=1000) &&($code<10000))
                   { $codeComplet="U000".$code;}
    
                if(($code>=10000) &&($code<100000))
                    {$codeComplet="U00".$code;}
    
                if(($code>=100000) &&($code<1000000))
                    {$codeComplet="U0".$code;}
                if(($code>=1000000) &&($code<10000000))
                    {$codeComplet="U".$code;}
                
                
               /* Usager::create([
                    "nom"=>$nom,
                    "prenoms"=>$prenoms,
                    "email"=>$email,
                    "code"=>$code,
                    "codeComplet"=>$codeComplet,
                    "tel"=>$tel,
                    "idDepartement"=>$idDepartement
                ]);*/
                $usager= new Usager();
                $usager->nom=$nom;
                $usager->prenoms=$prenoms;

                $usager->email=$email;
                $usager->code=$code;

                $usager->codeComplet=$codeComplet;

                $usager->password=$password;

                $usager->tel=$tel;
                $usager->institu_id=$institu_id;
                $usager->idDepartement=$idDepartement;

                $usager->save();

               $getuser=Usager::where("email","=",$email)->first();
               return $getuser;
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


private function addUsagerIfNotExist(){
                   
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
      $userSearch = Usager::where("email", "like", $email)
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
      $userSearch = Usager::where("password_reset_code", "like", $code)
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
      $userSearch = Usager::where("email", "like", $email)
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
      $foundUser->password = $password1; //Hash::make($password1);
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



}

