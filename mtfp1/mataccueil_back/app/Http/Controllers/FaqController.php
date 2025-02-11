<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;

use App\Models\Faq;

use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;


use DB;

class FaqController extends Controller {

	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('jwt.auth',['except' =>['index']]);
    }


	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		try { 


			$FaqSearch = Faq::all();

			foreach ($FaqSearch as $Faq) {
				$Faq->service_parent;
			}

			return $FaqSearch;

		} catch(\Illuminate\Database\QueryException $ex){
            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez delaiez l'administrateur" );
        }catch(\Exception $e){
            $error = array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez delaiez l'administrateur" );
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
          if (!( isset($inputArray['question']) && isset($inputArray['id'])
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }		

          

            $question = $inputArray['question'];
            $reponse = $inputArray['reponse'];
            


			$userconnect = new AuthController;
			$userconnectdata = $userconnect->user_data_by_token($request->token);

            $Faq = new Faq;
			$Faq->question = $question;
			$Faq->reponse = $reponse;
            $Faq->created_by = $userconnectdata->id;
            $Faq->updated_by = $userconnectdata->id;
            $Faq->save();

            return $this->index();
        }
        catch(\Illuminate\Database\QueryException $ex){
            $error = array("status" => "error", "message" => "Une erreur inattendue est survenue !" );
            //\Log::error($ex->getMessage());
            return $error;
        }
        catch(\Exception $ex){
            $error = array("status" => "error", "message" => "Une erreur est survenue lors de " .
                "votre tentative de connexion. Veuillez delaiez l'administrateur" );
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
		$FaqSearch = Faq::where("id","=",$id)->get();

		if($FaqSearch->isEmpty()){
			return array(
				"status"=>"error",
				"message"=>"Aucune étape retrouvée"
				);
		}
		else {
			$Faq = $FaqSearch->first();
			return $Faq;
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
                if (!isset($inputArray['question']) && !isset($inputArray['id'])) { 
                	//controle d existence
                    return array("status" => "error",
                        "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
                }

            	$question = $inputArray['question'];
	            $reponse = $inputArray['reponse'];
                $id = $inputArray['id'];

				$userconnect = new AuthController;
				$userconnectdata = $userconnect->user_data_by_token($request->token);
                // Récuperer lae Faq
                $Faq = Faq::find($id);

				$Faq->question = $question;
				$Faq->reponse = $reponse;

	            $Faq->created_by = $userconnectdata->id;
	            $Faq->updated_by = $userconnectdata->id;

	            $Faq->save();

	            \DB::connection()->enableQueryLog();
				$query = \DB::getQueryLog();
				$lastQuery = end($query);

                return $this->index();
           }
            catch(\Illuminate\Database\QueryException $ex){
            $error = array("status" => "error", "message" => "Une erreur est survenue. Veuillez contacter l'administrateur.");
            return $error;
        }catch(\Exception $e){
            $error = array("status" => "error", "message" =>"Une erreur est survenue. Veuillez reessayer plus tard.");
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
		Faq::find($id)->delete();
		return $this->index();
	}


}
