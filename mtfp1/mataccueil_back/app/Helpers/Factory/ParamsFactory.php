<?php

namespace App\Helpers\Factory;

use App\Models\Utilisateur;
use Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Input;
use Mail;

use App\Helpers\Carbon\Carbon;



class ParamsFactory {


        public static function ClearFileName($str){

                $url = $str;

                $url = preg_replace('# #', '-', $url);
                $url = preg_replace("#'#", '-', $url);
                $url = preg_replace("/[^A-Za-z0-9-_.]/", '', $url);
                $url = preg_replace('#Ç#', 'C', $url);
                $url = preg_replace('#ç#', 'c', $url);
                $url = preg_replace('#è|é|ê|ë#', 'e', $url);
                $url = preg_replace('#È|É|Ê|Ë#', 'E', $url);
                $url = preg_replace('#à|á|â|ã|ä|å#', 'a', $url);
                $url = preg_replace('#@|À|Á|Â|Ã|Ä|Å#', 'A', $url);
                $url = preg_replace('#ì|í|î|ï#', 'i', $url);
                $url = preg_replace('#Ì|Í|Î|Ï#', 'I', $url);
                $url = preg_replace('#ð|ò|ó|ô|õ|ö#', 'o', $url);
                $url = preg_replace('#Ò|Ó|Ô|Õ|Ö#', 'O', $url);
                $url = preg_replace('#ù|ú|û|ü#', 'u', $url);
                $url = preg_replace('#Ù|Ú|Û|Ü#', 'U', $url);
                $url = preg_replace('#ý|ÿ#', 'y', $url);
                $url = preg_replace('#Ý#', 'Y', $url);

                return ($url);
        }

        public static function dateFrEn($date){
                $tab=explode('-',$date);
                $date_r=$tab[2].'-'.$tab[1].'-'.$tab[0];
                return $date_r;
        }

      //genere un code aleatoire
      public static function generateAleaCode($len) {
        $mdp = ""; $paramLen = $len; $nbAlea = "";
        $catalogue= 'abcdefghijklmnopqrstuvwxyz1234567890';
        // Initialise le générateur
        srand(((int)((double)microtime()*1000003)) );
        for ($i = 0; $i < $paramLen; $i++) {
          $nbAlea = rand(0, (strlen($catalogue) -1));
          $mdp .= $catalogue[$nbAlea] ;
        }
        $result = strtolower($mdp);
        $codeSearch = Utilisateur::where("password_reset_code", "LIKE", "$result")->get();
        if (!$codeSearch->isEmpty()) {
          generateAleaCode();
        }
        return $result;
      }//fin generateAleaCode


  //emplacement des getRequestsPath
  public static function getRequestsPath($fileExtension) {
    //fichier
    $relatedFolder = "FichiersCourriers";
    return $relatedFolder;
  }//fin getRequestsPath





  //log une exception
  public static function logException($ex, $request=null) {
    try{
      $appException = new \Exception();
      $appException = $ex;
      $message = "Message: ". $appException->getMessage() . " Trace: ". $appException->getTraceAsString().
        " Fichier: ".   $appException->getFile();
      \Log::error("");
      \Log::error($message);
      \Log::error("");

      //send by mail the error log
      ParamsFactory::sendErrorLogMail(ParamsFactory::$adminEmails, "Message d'erreur", $message);

      //ip
      if($request !== undefined && $request !== null){
        $ip = $request->ip();
        $userAgent = $request->header('User-Agent');
        $more = $request->header('User-Agent');

        $log = new LogAction();
        $log->date_action = time();
        $log->code_user = "UNKNOWN";
        $log->action = $message;
        $log->user_ip = $ip;
        $log->user_agent = $userAgent;
        $log->user_more = $message;
        $log->save();
      }

    }catch(\Exception $e){    }
  }//fin logException


  //formate une date de format (2017-10-08T23:00:00.000Z) en datetime objet Carbon
  public static function convertToDateTime($obj) {
    try{
      //get parts from dates
      $beninTimeZone = 'Africa/Porto-Novo';
      //start date
      $dateResult = new Carbon;
      if ($obj !== ""){
//                $dayDebut = substr($obj,0,2);
//                $monthDebut = substr($obj,3,2);
//                $yearDebut = substr($obj,6,4);

        $dayDebut = substr($obj,8,2);
        $monthDebut = substr($obj,5,2);
        $yearDebut = substr($obj,0,4);

        $hourDebut = substr($obj,11,2);
        $minuteDebut = substr($obj,14,2);

        $dateResult = Carbon::create($yearDebut, $monthDebut, $dayDebut, $hourDebut, $minuteDebut, 00, $beninTimeZone);

      }
      return $dateResult;
    }catch(\Exception $ex){
      \Log::error($ex->getMessage());
      return date("Y/m/d");
    }
  }//fin convertToDateTime

  public static function convertToDateTimeForSearch($obj, $isMorning) {
    try{
      //get parts from dates
      $beninTimeZone = 'Africa/Porto-Novo';
      //start date
      $dateResult = new Carbon;
      if ($obj !== ""){
        $dayDebut = substr($obj,8,2);
        $monthDebut = substr($obj,5,2);
        $yearDebut = substr($obj,0,4);

        $hourDebut = ($isMorning == true)? "00" : "23";
        $minuteDebut = ($isMorning == true)? "00" : "59";

        $dateResult = Carbon::create($yearDebut, $monthDebut, $dayDebut, $hourDebut, $minuteDebut, 00, $beninTimeZone);

      }
      return $dateResult;
    }catch(\Exception $ex){
      \Log::error($ex->getMessage());
      return date("Y/m/d");
    }
  }//fin convertToDateTime


  //formate une date en datetime
  public static function convertToDateTimeMoment($obj, $isMorning) {
    try{
      //get parts from dates
      $beninTimeZone = 'Africa/Porto-Novo';
      //start date
      $dateResult = new Carbon;
      if ($obj !== ""){
        $dayDebut = substr($obj,0,2);
        $monthDebut = substr($obj,3,2);
        $yearDebut = substr($obj,6,4);
        if($isMorning == true){
          $dateResult = Carbon::create($yearDebut, $monthDebut, $dayDebut, 00, 00, 00, $beninTimeZone);
        }else{
          $dateResult = Carbon::create($yearDebut, $monthDebut, $dayDebut, 23, 59, 59, $beninTimeZone);
        }
      }
      return $dateResult;
    }catch(\Exception $ex){
      \Log::error($ex->getMessage());
      return date("Y/m/d");
    }
  }//fin convertToDateTimePeriod

  public static function convertToDateDbTimeForSearch($obj, $isMorning) {
    try{
      //get parts from dates
      $beninTimeZone = 'Africa/Porto-Novo';
      //start date
      $dateResult = new Carbon;
      if ($obj !== ""){
        $dayDebut = substr($obj,8,2);
        $monthDebut = substr($obj,5,2);
        $yearDebut = substr($obj,0,4);

        $hourDebut = ($isMorning == true)? "00" : "23";
        $minuteDebut = ($isMorning == true)? "00" : "59";

        $dateResult = Carbon::create($yearDebut, $monthDebut, $dayDebut, $hourDebut, $minuteDebut, 00, $beninTimeZone);

        $dateResult = Carbon::createFromFormat("Y-m-d H:i:s", $dateResult);

      }
      return $dateResult;
    }catch(\Exception $ex){
      \Log::error($ex->getMessage());
      return date("Y/m/d");
    }
  }//fin convertToDateTime



}
