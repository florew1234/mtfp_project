<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\Utilisateur;
use App\Models\Activity;
use App\Models\Requete;
use Hash,DateTime;

class AuthController extends Controller {
    /*
      |--------------------------------------------------------------------------
      | Home Controller
      |--------------------------------------------------------------------------
      |
      | This controller renders your application's "dashboard" for users that
      | are authenticated. Of course, you are free to change or remove the
      | controller as you wish. It is just here to get your app started!
      |
     */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        // Apply the jwt.auth middleware to all methods in this controller
        // except for the authenticate method. We don't want to prevent
        // the user from retrieving their token if they don't already have it
        $this->middleware('jwt.auth', ['except' => ['signin','signin2','signinpfc','logout_user']]);
    }

    public function certifier() {
        return array('statuts' =>'success');
    }

    public function user_data(Request $request) {
        $user = JWTAuth::toUser($request->token);
        $user->profil_user;
        $user->agent_user;
        $user->attribuCom;
        $user->entity;
        if(isset($user->agent_user))
        $user->agent_user->structure;
        return $user;
    }

    public function user_datamat(Request $request) {
        $user = JWTAuth::toUser($request->token);
        // return response('');
        $user->profil_user;
        $user->agent_user;
        $user->attribuCom;
        if(isset($user->agent_user))
        $user->agent_user->structure;
        return response()->json($user);
    }

    public function user_data_by_token($token) {
        $user = JWTAuth::toUser($token);
        return $user;
    }
    //authentifie un utilisateur
    public function signin(Request $request) {

        $credentials = $request->only('email', 'password');
        try {
            // verify the credentials and create a token for the user
           
            if (!  $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
            //Save les traces de l'utilisateur
            $idUser = Utilisateur::with(['agent_user','entity'])->where('email',$request->email)->first()->id;
            //Déconnecter les autres comptes de l'utilisateur avant l'ouverture d'un nouveau 
            Activity::where('id_user',$idUser)->where('last_logout',null)->update([
                'last_logout' => date("Y-m-d H:i:s")
            ]);
            $lastConne = Activity::where('id_user',$idUser)->orderBy('last_logout','desc')->first();
            // return response($lastConne);
            if($lastConne){
                $dateLast_con = $lastConne->last_logout;
            }else{
                $dateLast_con = '2023-01-01 00:00:00';
            }
            //Determiner la dernière connexion
            $lastConne = '';
            $debut = date_create($dateLast_con); 
            $fin = date_create(date('Y-m-d H:i:s'));
            //Différence entre deux dates 
            $intvl = $debut->diff($fin);
            
            // return response($intvl->y." y ".$intvl->m." m ".$intvl->d." d ".$intvl->h." h ".$intvl->i." i ");
            if($intvl->y != 0){
                $lastConne = $intvl->y ." an(s) ";
            }
            if($intvl->m != 0){
                $lastConne .= $intvl->m ." mois ";
            }
            if($intvl->d != 0){
                $lastConne .= $intvl->d ." jours ";
            }
            if($intvl->h != 0){
                $lastConne .= $intvl->h ." Heures ";
            }
            if($intvl->i != 0){
                $lastConne .= $intvl->i ." min ";
            }
            if($intvl->s != 0){
                $lastConne .= $intvl->s ." sec ";
            }

            //Nombre d'instance 
            
            Activity::create([
                "last_connect"=> $lastConne,
                "id_user"=>$idUser,
                "last_login"=>date("Y-m-d H:i:s"),
                // "last_logout"=>date("Y-m-d H:i:s"), // Doit être vide
                "activity"=>''
            ]);
        } catch (JWTException $e) {

          \Log::error($e->getMessage());
            // something went wrong
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        return response()->json(compact('token'));

        // if no errors are encountered we can return a JWT  invalid_credentials
    }
    //authentifie un utilisateur
    public function logout_user($id) {

        try {
            //Déconnecter les autres comptes de l'utilisateur avant l'ouverture d'un nouveau 
            Activity::where('id_user',$id)->where('last_logout',null)->update([
                'last_logout' => date("Y-m-d H:i:s")
            ]);
            return response()->json(["success" => true]);
        } catch (JWTException $e) {
            \Log::error($e->getMessage());
            return response()->json(["success" => false,'error' => 'could_not_create_token'], 500);
        }
    }
    
    //authentifie un utilisateur
    public function signinpfc(Request $request) {
        
        
        $credentials = $request->only('email', 'password');
        try {
            // verify the credentials and create a token for the user
           
            if (!  $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
            if (!  $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {

          \Log::error($e->getMessage());
            // something went wrong
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        return response()->json(compact('token'));

        // if no errors are encountered we can return a JWT
    }

    public function signin2(Request $request)
    {
        $check=Utilisateur::whereAccessToken($request->code)->first();
        $credentials = ["email"=>$check->email,"password"=>"123"];
        try {
            // verify the credentials and create a token for the user
            if (!  $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {

          \Log::error($e->getMessage());
            // something went wrong
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
      //  $check->update(['access_token'=>null]);
       // $user=User::with("roles.permissions","agent.uniteAdmin",'userprestation.prestation')->where("id",Auth::id())->first();
        
       return response()->json(compact('token'));


        // if no errors are encountered we can return a JWT
        

    }
    

  public function resetPassword(Request $request){
    
      
      $user=Utilisateur::with(['agent_user'])->find($request->id);
      $ancien_pass=$request->last_password;
      $pass=$request->new_password;
      $confirm=$request->confirm_password;
      if(strlen($pass) < 6){
        return response()->json([
            "success" => false,
            "message" =>'Le nouveau mot de passe doit être plus de 6 caractères'
        ],400);
      }else if (Hash::check($ancien_pass,$user->password)){
            // return response("tetete");
          if($confirm==$pass){
            $user->update([
                "password"=>bcrypt($pass)
            ]);
            return response()->json([
                "success" => true,
                "message" =>'Votre nouveau mot de passe a bien été pris en compte'
            ],200);
          }else{
            return response()->json([
                "success" => false,
                "message" =>'Le nouveau mot de passe et le mot de passe de confirmation sont incorrect'
            ],400);
          }
        }else{
            return response()->json([
                "success" => false,
                "message" =>'Ancien mot de passe incorrect'
            ],400);
        }
        return response()->json([
            "success" => false
        ],400);
  }

  }
