<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Cities;
use App\AnnualDates;
use App\EventSelections;
use App\AnnualEventSelection;
use App\Categories;
use Validator;
use DB;
use App\EventDescription;
use App\Shop;
use App\Contact;
class CommonController extends Controller
{
    public function getData(Request $request){  
		$response = [];
		$response['success'] = FALSE;
		try {
			$response['success'] = TRUE;
			$city =  Cities::get();
			$annualDate=AnnualDates::get();
			$category=Categories::get();
			$userEvent = EventSelections::where('user_id',$request->user()->id)->with('eventdate','annualevents')->get();
           // print_r($userEvent); die;
			$response['annualDate']=$annualDate;
			$response['city']=$city;
		    $response['category']=$category;
		    $response['user']=$userEvent;
		 
		} catch (Exceptio $e) {
            Log::error($e->getTraceAsString());
           //$response = apiResponseServerError($e);
            $response['message'] = "No data found";
        }
		return $response;
	}

	public function getShop(Request $request){ 
		$response = [];
		$response['success'] = FALSE;
		try {
			$response['success'] = TRUE;
			$shop =  Shop::get();
			$response['shop']=$shop;
		 
		} catch (Exceptio $e) {
            Log::error($e->getTraceAsString());
            $response['message'] = "No data found";
        }
		return $response;
	}


	public function addEventSelection(Request $request){ 

	    $response = [];
		$response['success'] = FALSE;
		try {
            $rules['title'] = 'required';
            $rules['date'] = 'required';
            
            $rules['category'] = 'required';
	        
	        $validator = Validator::make($request->all(), $rules);
	        $validatorResponse = checkValidateRequest($validator);
	        if ($validatorResponse['success'] === FALSE)
	            return $validatorResponse;
	          
	        if ($request->file('image') && $request->file('image') != null) {
				$file = $request->file('image');
				$image_file = $request->file('image');
				$upload_path = '/image/event/';
				$destinationPath = public_path() . $upload_path;
				$fileName = time() . '-' . $file->getClientOriginalName();
				$image_res = $request->file('image')->move($destinationPath, $fileName);
				$image = $upload_path . $fileName;	
	        }else{
	        	$image='';
	        }

           $data = [
				'category_id' =>  $request->post('category'),
				'title' =>$request->post('title'),
				'date' => $request->post('date'),
				'image' => $image,
				'user_id' => $request->user()->id, 
				'event_selection_id'=>null
						];
			EventDescription::insert($data);    
			
		    $response['event'][] =EventDescription::select('id','category_id','title','image','date')->where('user_id',$request->user()->id)->get();
	         
            $response['success'] = API_SUCCESS;
	        $response['status'] = API_STATUS_OK;
	    }catch (Exceptio $e) {
            Log::error($e->getTraceAsString());
            $response = apiResponseServerError($e);
        }
          return $response;
      }
            

		// $response = [];
		// $response['success'] = FALSE;
		// try {
  //           $rules['hebrew'] = 'required';
  //           $rules['ent_shabbat'] = 'required';
            
  //           $rules['annual_date_id'] = 'required';
  //           $rules['product_id'] = 'required';
  //           $rules['event_data.category_id'] = 'required';
  //           $rules['event_data.title'] = 'required';
  //           $rules['event_data.date'] = 'required';
  //           if($request->get('ent_shabbat')){
  //           	$request->get('ent_shabbat');
  //           }
  //           $validator = Validator::make($request->all(), $rules);
  //           $validatorResponse = checkValidateRequest($validator);
  //           if ($validatorResponse['success'] === FALSE)
  //               return $validatorResponse;


  //           //$eventData =$request->all();

  //           $data = [
		// 		'hebrew' =>$request->get('hebrew'),
		// 		'ent_shabbat' => $request->get('ent_shabbat'),
		// 		'city_id' => $request->get('city_id'),
		// 		'product_id' => $request->get('product_id'),
		// 		'user_id' => $request->user()->id, 
		// 		'order_id'=>null,
  //               'created_at' => date('Y-m-d H:i:s')
		// 	];
		// 	$id = DB::table('event_selections')->insertGetId($data);
            
  //           if($id){
	 //            foreach($request->get('annual_date_id') as $key => $annual)  {
		// 	        $data1 = [
		// 			    'annual_date_id' =>$annual,
		// 				'event_selection_id' => $id,	
		// 			];
		// 			AnnualEventSelection::insert($data1);    
		// 		}
 
           
  //           //foreach($request->get('event_data') as $key => $event)  {
  //              $event = $request->get('event_data');
  //               if (isset($request->file('event_data')['image'])) {
		// 			$file = $request->file('event_data')['image'];
		// 			$image_file = $request->file('event_data')['image'];
		// 			$upload_path = '/image/event/';
		// 			$destinationPath = public_path() . $upload_path;
		// 			$fileName = time() . '-' . $file->getClientOriginalName();
		// 			$image_res = $request->file('event_data')['image']->move($destinationPath, $fileName);
					
		// 			$image = $upload_path . $fileName;	
		//         }else{
		//         	$image='';
		//         }

  //              $data3 = [
		// 			'category_id' =>  $event['category_id'],
		// 			'title' =>$event['title'],
		// 			'date' => $event['date'],
		// 			'image' => $image,
		// 			'event_selection_id'=>$id
		// 		];
		// 		EventDescription::insert($data3);    
  //           //}
  //       }
  //       //$userEvent = EventSelections::where('user_id',$request->user()->id)->with('eventdes','annualevents')->get();
  //       $userEvent = EventSelections::where('user_id',$request->user()->id)
  //                     ->whereDate('created_at',date('Y-m-d'))
  //                     ->with('eventdate','annualevents')->get();
  //       $response['user'] = $userEvent;
  //       $response['success'] = API_SUCCESS;
  //       $response['status'] = API_STATUS_OK;

		// } catch (Exceptio $e) {
  //           Log::error($e->getTraceAsString());
  //           $response = apiResponseServerError($e);
  //       }
		// return $response;
	//}
	
	public function contactUs(Request $request){  

		$response = [];
		$response['success'] = FALSE;
		try {
            $rules['phone'] = 'required';
            $rules['email'] = 'required';
            
            $rules['message'] = 'required';
         
            $validator = Validator::make($request->all(), $rules);
            $validatorResponse = checkValidateRequest($validator);
            if ($validatorResponse['success'] === FALSE)
                return $validatorResponse;

            	$data[] = [
				    'phone' =>$request->post('phone'),
				    'email'=>$request->post('email'),
				    'messages' => $request->post('message'),
				    'created_at' => date('Y-m-d H:i:s')
				];  
		    Contact::insert($data);

	        $response['success'] = API_SUCCESS;
	        $response['status'] = API_STATUS_OK;

		} catch (Exceptio $e) {
            Log::error($e->getTraceAsString());
            $response = apiResponseServerError($e);
        }
		return $response;
	}

	public function getEvent(Request $request){ 
		
		$response = [];
		$response['success'] = FALSE;
		try {

            $userEvent = EventSelections::where('user_id',$request->user()->id)
                       ->get();
            // if(isset($userEvent)){
	           //  foreach ($userEvent as $key =>$events){

		                $response['event'] =EventDescription::select('id','category_id','title','image','date')->where('user_id',$request->user()->id)->get();
	           //  }
            // }

            // $userEvent = AnnualEventSelection::select('eventdate.*')->with(['annualevents', 'eventdate'])
			// ->where('event_selectionsuser_id',$request->user()->id)
			// ->get();

			//$response['event'] = $userEvent;
			$response['success'] = API_SUCCESS;
	        $response['status'] = API_STATUS_OK;

		} catch (Exceptio $e) {
            Log::error($e->getTraceAsString());
            $response = apiResponseServerError($e);
        }
		return $response;
    }
}
