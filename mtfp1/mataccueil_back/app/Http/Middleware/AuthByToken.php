<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Http;
use App\Models\Utilisateur;
use DB;

class AuthByToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $check=Utilisateur::where("access_token",$request->input('code'))->first();
           
        if(!$check){
            $curl = curl_init();
            $data = [
                'grant_type' => 'authorization_code',
                'redirect_uri' => 'https://api.mataccueil.gouv.bj',
                'code' => $request->input('code')
            ];
            
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://pprodofficial.service-public.bj/api/official/token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 20000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_HTTPHEADER => array(
                "accept: */*",
                "Authorization:  Basic  YXBwLW10ZnA6YXBwLW10ZnA=",
                "content-type: application/x-www-form-urlencoded",
            ),
        ));
    
        $response = curl_exec($curl);
        $err = curl_error($curl);
      
        curl_close($curl);
        if ($err || !isset(json_decode($response,true)['id_token'])) {
            info($err );
            return  Redirect::to("https://pprodofficial.service-public.bj/official/login?client_id=app-mtfp&redirect_uri=https:%2F%2Fapi.mataccueil.gouv.bj&scope=openid&response_type=code&authError=true");
        } else {
            
            $token=json_decode($response,true)['id_token'];
            $tokenParts = explode(".", $token);  
            $tokenHeader = base64_decode($tokenParts[0]);
            $tokenPayload = base64_decode($tokenParts[1]);
            $jwtHeader = json_decode($tokenHeader);
            $jwtPayload = json_decode($tokenPayload,true);
            $checkUser=Utilisateur::where('email',"=",$jwtPayload['sub'])->first();

          //  dd($jwtPayload['sub'],$checkUser, env('DB_DATABASE'),User::take(5)->get());

            if($checkUser){
                $checkUser->update(['access_token'=> $request->input('code')]);
                return  Redirect::to('https://mataccueil.gouv.bj/login-v2/'.$request->input('code'));
                //return  Redirect::to('http://localhost:4200/login-v2/'.$request->input('code'));
            }else{
                echo 'UTILISATREUR NON RECONNU';
            }
            return $next($request);
        }
        }else {
            return $next($request);
        }
      
}
}
