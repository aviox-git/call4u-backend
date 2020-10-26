<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Calendar;
use App\UploadCalendarImages;
use App\Order;
use App\AnnualEventSelection;
use App\EventDescription;
use App\Orderdetail;
use App\EventSelections;
use Log;
use Validator;
use DB;

class OrderController extends Controller
{
    

    public function placeOrder(Request $request){
  

        $response = [];
		$response['success'] = FALSE;
	    try {
	    	
	    	$a = $request->post('isPicup');
	    	$freeTemplate = $request->post('is_free_template');
	    	
	    	if($freeTemplate =='true'){
	    		$rules['free_template_id'] = 'required';
	    		$freeTemplate_id = $request->post('free_template_id');
	    	}else{
	    		$freeTemplate_id ='';
	    	}

	    	if($a == "false"){

					$rules['receiverName'] = 'required';
					$rules['receiverEmail'] = 'required';
					$rules['receiverPhone'] = 'required';
					$rules['receiverCity'] = 'required';
					$rules['receiverStreet'] = 'required';
					$rules['receiverApartment'] = 'required';
					$rules['receiverHouse'] = 'required';
					$rules['senderName'] = 'required';
					$rules['senderEmail'] = 'required';
					$rules['senderPhone'] = 'required';
					
					
    

			        $validator = Validator::make($request->all(), $rules);
			        $validatorResponse = checkValidateRequest($validator);
			            if ($validatorResponse['success'] === FALSE){
			                return $validatorResponse;
			            }

			        
			     

			        $data = [

						'name' => $request->post('receiverName'),
						'email' => $request->post('receiverEmail'),
						'phone' => $request->post('receiverPhone'),
						'city' => $request->post('receiverCity'),
						'street' => $request->post('receiverStreet'),
						'apartment' => $request->post('receiverApartment'),
						'house_number' => $request->post('receiverHouse'),
						'sender_name' => $request->post('senderName'),
						'sender_phone' => $request->post('senderEmail'),
						'sender_email' => $request->post('senderPhone'),
						'coupon_id' => $request->post('appliedCoupon'),
						'address_type' => 2,
						'is_free_template' => $freeTemplate,
						'free_template_id' => $freeTemplate_id,
				        'user_id' => $request->user()->id 
		        	];

	    	}else{
	    		// print_r("inside the else ");
	    		// die;	
	
				$rules['pickupId'] = 'required';
					
					
    

			        $validator = Validator::make($request->all(), $rules);
			        $validatorResponse = checkValidateRequest($validator);
			            if ($validatorResponse['success'] === FALSE){
			                return $validatorResponse;
			            }

	    		$data = [

						'name' => 0,
						'email' => 0,
						'phone' => 0,
						'city' => 0,
						'street' => 0,
						'apartment' => 0,
						'house_number' => 0,
						'sender_name' => 0,
						'sender_phone' => 0,
						'sender_email' => 0,
						'coupon_id' => $request->post('appliedCoupon'),
						'address_type' => 1,
					    'is_free_template' => $freeTemplate,
						'free_template_id' => $freeTemplate_id,
				        'user_id' => $request->user()->id,
				        'shop_id' =>$request->post('pickupId')
						
					];

	    	}



	        $id = DB::table('orders')->insertGetId($data);
     

            if($id){

             	foreach( $request->post('cartItems') as $key => $product)  {
     				
                 $event = [
						'hebrew' =>$product['hebrew'],
						'ent_shabbat' => $product['ent_shabbat'],
						'city_id' => $product['city_id'],
						'product_id' => $product['product_id'],
						'user_id' => $request->user()->id, 
						'template_id' => $product['template_id'],
						'order_id' => $id,
						'quantity' => $product['quantity'],
						'price' =>$product['price'],
		                'created_at' => date('Y-m-d H:i:s')
				    ];
				    $eventId = DB::table('event_selections')->insertGetId($event);
				    
				    if($eventId){
				    	
				        if(isset($_FILES['cartItems'])){
				    	if($_FILES['cartItems']['name'][$key]['images']){

		                    foreach( $_FILES['cartItems']['name'][$key]['images']as $k => $val) {
		         	
								$file = $request->file('cartItems')[$key]['images'][$k]; 
								$image_file = $request->file('cartItems')[$key]['images'][$k];
								$upload_path = '/image/calander/';
								$destinationPath = public_path() . $upload_path;
								$fileName = time() . '-' . $file->getClientOriginalName();
								$image_res = $request->file('cartItems')[$key]['images'][$k]->move($destinationPath, $fileName);
								if ($image_res) {
									$calander= [
									    'user_id' =>$request->user()->id,
									    'image' => $upload_path . $fileName,
									    'product_id'=>$product['product_id'],
									    'event_selection_id'=>$eventId,
									    'created_at' => date('Y-m-d H:i:s')
									];  
									UploadCalendarImages::insert($calander);
								}
						    }
		            	}   
		            }


				    	foreach($product['annual_date_id'] as $key => $annual)  {
					        $annualevt = [
							    'annual_date_id' =>$annual,
								'event_selection_id' => $eventId
							];
							AnnualEventSelection::insert($annualevt);    
						}
				 
				      //  $event = $request->get('event_data');
		               if(isset($product['events'])){
				        foreach($product['events'] as $ke=> $event)  {

				        	    if (isset($request->file('cartItems')[$key]['events'][$ke]['image'])) {
										$file = $request->file('cartItems')[$key]['events'][$ke]['image'];
										$image_file = $request->file('cartItems')[$key]['events'][$ke]['image'];
										$upload_path = '/image/event/';
										$destinationPath = public_path() . $upload_path;
										$fileName = time() . '-' . $file->getClientOriginalName();
										$image_res = $request->file('cartItems')[$key]['events'][$ke]['image']->move($destinationPath, $fileName);
										
										$image = $upload_path . $fileName;	
							        }else{
							        	$image='';
							        }

				               $evtdetail = [
									'category_id' =>  $event['category_id'],
									'title' =>$event['title'],
									'date' => $event['date'],
									'image' => $image,
									'event_selection_id'=>$eventId
								];
								EventDescription::insert($evtdetail);   
						} 
					}
                   }
                }
		        $response['success'] = API_SUCCESS;
		        $response['status'] = API_STATUS_OK;
		        $response['message'] = 'Record added successfully';

            }
	    }catch (\Exception $e) {
            Log::error($e->getTraceAsString());
            $response = apiResponseServerError($e);
        }
        return $response;
    }

    public function myOrder(Request $request){  
    	  $response = [];
		$response['success'] = FALSE;
	    try {
        //$order = Order::where('user_id',$request->user()->id)->get(); 
        $order = Order::where('user_id',$request->user()->id)->with('calender')->with('calender.template','calender.eventdate','calender.annualevents','calender.calenderImages')->get(); 
	        if(isset($order)){
	            $response['order'] = $order; 
	        }else{
	        	$response['message'] = 'No orders exist !';
	        }
	        $response['success'] = API_SUCCESS;
	        $response['status'] = API_STATUS_OK;

        }catch (\Exception $e) {
            Log::error($e->getTraceAsString());
            $response = apiResponseServerError($e);
        }
        return $response;
    } 

    public function reorder(Request $request){ 
    	//print_r($request->all('order_id')); die;
        $response = [];
		$response['success'] = FALSE;
	    try {
       
       
	    	$a = $request->post('isPicup');
	    	
	    	if($a == "false"){

					$rules['receiverName'] = 'required';
					$rules['receiverEmail'] = 'required';
					$rules['receiverPhone'] = 'required';
					$rules['receiverCity'] = 'required';
					$rules['receiverStreet'] = 'required';
					$rules['receiverApartment'] = 'required';
					$rules['receiverHouse'] = 'required';
					$rules['senderName'] = 'required';
					$rules['senderEmail'] = 'required';
					$rules['senderPhone'] = 'required';
					
					
    

			        $validator = Validator::make($request->all(), $rules);
			        $validatorResponse = checkValidateRequest($validator);
			            if ($validatorResponse['success'] === FALSE){
			                return $validatorResponse;
			            }

                $data = [
					'name' => $request->post('receiverName'),
					'email' => $request->post('receiverEmail'),
					'phone' => $request->post('receiverPhone'),
					'city' => $request->post('receiverCity'),
					'street' => $request->post('receiverStreet'),
					'apartment' => $request->post('receiverApartment'),
					'house_number' => $request->post('receiverHouse'),
					'sender_name' => $request->post('senderName'),
					'sender_phone' => $request->post('senderEmail'),
					'sender_email' => $request->post('senderPhone'),
					'coupon_id' => $request->post('appliedCoupon'),
					'address_type' => $request->post('address_type'),
			        'user_id' => $request->user()->id
		        ];
		     }else{

		     	$data = [

						'name' => 0,
						'email' => 0,
						'phone' => 0,
						'city' => 0,
						'street' => 0,
						'apartment' => 0,
						'house_number' => 0,
						'sender_name' => 0,
						'sender_phone' => 0,
						'sender_email' => 0,
						'coupon_id' => $request->post('appliedCoupon'),
						'address_type' => 1,
				        'user_id' => $request->user()->id,
				        'shop_id' =>$request->post('pickupId')
						
				];

		     }

	         $id = DB::table('orders')->insertGetId($data);
	         $order = Order::where('id',$request->all('order_id'))->with('calender')->with('calender.template','calender.eventdate','calender.annualevents','calender.calenderImages')->first(); 

	         
             if($id){
                     $event =EventSelections::where('id',$request->post('calender_id'))->first();

                    if($event) {

	                    $events = [
							'hebrew' =>$event['hebrew'],
							'ent_shabbat' => $event['ent_shabbat'],
							'city_id' => $event['city_id'],
							'product_id' => $event['product_id'],
							'user_id' => $request->user()->id, 
							'template_id' => $event['template_id'],
							'order_id' => $id,
							'quantity' => $request->post('quantity'),
							'price' => $request->post('price'),
			                'created_at' => date('Y-m-d H:i:s')
					    ];
					    $eventId = DB::table('event_selections')->insertGetId($events);
					}


             	    $calenderImage= UploadCalendarImages::where('event_selection_id',$request->post('calender_id'))->get();
                    
                    if($calenderImage) {
	                    foreach ($calenderImage as $key=>$calander){
		                    $calander= [
									'user_id' =>$request->user()->id,
									'image' => $calander['image'],
									'product_id'=>$calander['product_id'],
								    'event_selection_id'=>$eventId,
									'created_at' => date('Y-m-d H:i:s')
							];  
							UploadCalendarImages::insert($calander);
	                    }
                    }

                    $anualEvent= AnnualEventSelection::where('event_selection_id',$request->post('calender_id'))->get();
	                
				    if($anualEvent) {
				    	foreach($anualEvent as $key => $annual)  {
					        $annualevt = [
							    'annual_date_id' =>$annual['annual_date_id'],
								'event_selection_id' => $eventId
							];
							AnnualEventSelection::insert($annualevt);    
						}
					}

					$eventdescription= EventDescription::where('event_selection_id',$request->post('calender_id'))->get();
				    
				    if($eventdescription) {
				       foreach($eventdescription as $key => $eventdes)  {
			               $evtdetail = [
								'category_id' =>  $eventdes['category_id'],
								'title' =>$eventdes['title'],
								'date' => $eventdes['date'],
								'image' => $eventdes['image'],
								'event_selection_id'=>$eventId
							];
							EventDescription::insert($evtdetail);    
                        }
                    }
                }
            
        $response['success'] = API_SUCCESS;
        $response['status'] = API_STATUS_OK;
        $response['message'] = 'Record added successfully';

          
        }catch (\Exception $e) {
            Log::error($e->getTraceAsString());
            $response = apiResponseServerError($e);
        }
        return $response;
    }
}
