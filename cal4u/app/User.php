<?php

namespace App;

use App\Mail\ForgotPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\HasApiTokens;
use Mail;
use Str;
use Validator;
use Hash;
use Auth;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','otp','is_verified'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    public static function checkUser($data)
    {
        $result = [];
        $result['success'] = FALSE;
        if (array_key_exists('username', $data)) {
            if (\Auth::validate(array('username' => $data['username'], 'password' => $data['password']))) {

                $user = User::where('username', $data['username'])->first();
                $result['user'] = $user;
                $result['success'] = TRUE;
            } else {
                $result['message'] = 'Incorrect username or password';
            }
        } elseif (array_key_exists('email', $data)) {
            if (\Auth::validate(array('email' => $data['email'], 'password' => $data['password']))) {

                $user = User::where('email', $data['email'])->first();
                $result['user'] = $user;
                $result['success'] = TRUE;
            } else {
                $result['message'] = 'Incorrect email or password';
            }
        } else {
            $result['message'] = 'Email or username is required';
        }
        return $result;
    }

    public static function login($request)
    {
        $response = [];
        $response['success'] = FALSE;

        $rules['mobile'] = 'required|min:10|max:14';
        $rules['password'] = 'required';

        $validator = Validator::make($request->all(), $rules);
        $validatorResponse = checkValidateRequest($validator);
            if ($validatorResponse['success'] === FALSE){
                return $validatorResponse;
            }else {

        $phone = $request->get('mobile');
       
            $mobile =$phone;
      
       
            $password =$request->get('password');

            $query = User::where('mobile',$mobile)->first();

              if($query){
            
                if(Hash::check($password, $query->password)) {
                 
                    if($query->is_verified == '1'){
                    
                        $user_id = $query->id;
                        $user = Auth::loginUsingId($user_id)->toArray();
                        $response['data'] =$user;

                        $user = User::find($user_id);

                        $token = $user->createToken($user_id . ' token ')->accessToken;
                        
                        $response['access_token'] = $token;
                        $response['success'] = TRUE;
                        $response['message'] = "Login Successfully";
                    }else{
                        $response['message'] = "Please Verify your number by filling the OTP you get in your number ";
                    }
                }else{
                    $response['message'] = "Please check enter passsword";
                }

              }else{

                $response['message'] = "Wrong details";
              }

            }
        
        return $response;
    }

    public static function updateMobile($request)
    {
        $response = [];
        $response['status'] = False;
        try {
            $userId = $request->user()->id;
            $rules = ['mobile' => 'required|min:10|max:14'];
            $validator = Validator::make($request->all(), $rules);
            $validatorResponse = checkValidateRequest($validator);
            if ($validatorResponse['success'] === FALSE)
                return $validatorResponse;
          
            
            $phone = $request->get('mobile');
             
                    $mobile =$phone;
                

            $user = User::where('id', $userId)->update(['mobile' => $mobile ]);
            if($user){
                $response['status'] = True;
                $response['message'] = "successfully update!";
                $response['data'] = $user;

            }else{

                 $response['message'] = "Mobile number is required!";
            }
        } catch (Exceptio $e) {
            Log::error($e->getTraceAsString());
            $response = apiResponseServerError($e);
        }
        return $response;
    }
    



    public static function signup($request)
    {
        $response = [];
        try {
            $rules['name'] = 'required';
            $rules['email'] = 'required|email|max:255|unique:users';
            $rules['mobile'] = 'required|string|min:10|max:14';
            $rules['password'] = 'required|min:6';

            $validator = Validator::make($request->all(), $rules);
            $validatorResponse = checkValidateRequest($validator);
            if ($validatorResponse['success'] === FALSE)
                return $validatorResponse;


            $user = new User;
            $user->name = $request->get('name');
            $user->email = $request->get('email');
            $user->password = bcrypt($request->get('password'));
         
            $phone = $request->get('mobile');
            $mobile =$phone;

            $exist =User::where('mobile', $mobile)->first(); 
            
            if($exist){
              
              $response['errors'] = 'The mobile has already been taken';
              $response['success'] = false;
              $response['status'] = 400;

            }else{

                $user->mobile = $mobile;

                if ($request->hasFile('image') && $request->file('image')->isValid()) {
                    $file = $request->image;
                    $image_file = $request->file('image');
                    $upload_path = '/uploads/images/profile_pic';
                    $destinationPath = public_path() . $upload_path;
                    $fileName = time() . '-' . $file->getClientOriginalName();
                    $image_res = $request->file('image')->move($destinationPath, $fileName);
                    if ($image_res) {
                        $image = $upload_path . "/" . $fileName;
                        $user->profile_pic = $image;
                    }
                }

                $user->save();

                $response['message'] = 'User successfully registerd';
              
                $user_id = $user['id'];

                $user = User::where('id', $user_id)->first();

                $response['user'] = $user;
                $token = $user->createToken($user->id . ' token ')->accessToken;
                $response['access_token'] = $token;
                $response['user'] = $user->toArray();
                $response['success'] = API_SUCCESS;
                $response['status'] = API_STATUS_OK;
            }
        } catch (\Exception $e) {
            Log::error($e->getTraceAsString());
            $response = apiResponseServerError($e);
        }
        return $response;
    }

    public static function updateLatLng($request)
    {
        $response = [];
        try {
            $requestData = $request->all();
            $userId = $request->user()->id;

            $userObj = User::find($userId);
            if(!$userObj)
                return apiResponseUnauthorized();

            $userObj->lat = $requestData['lat'];
            $userObj->lng = $requestData['lng'];
            $userObj->save();

            $message = "Lat Lng updated successfully";
            $response = apiResponseCreateUpdate($message);
        } catch (\Exception $e) {
            Log::error($e->getTraceAsString());
            $response = apiResponseServerError($e);
        }
        return $response;
    }


    public static function forgotPassword($request) {
        $response = [];
        $response['success'] = False;
        try {
            $rules = [
                'otp' => 'required',
                'password' => 'required',
                'mobile' => 'required|min:10|max:14'
            ];
            $validator = Validator::make($request->all(), $rules);
            $validatorResponse = checkValidateRequest($validator);
            if ($validatorResponse['success'] === FALSE)
                return $validatorResponse;
            
            $otp = $request->get('otp');
            $phone = $request->get('mobile');
                $mobile =$phone;
                
           
            $user =User::where('mobile', $mobile)->first();

            if($user){
                if($user->otp == $otp){
                    $data= array(
                        'is_verified' => '1',
                        'password'    => bcrypt($request->get('password'))
                    );
                    User::where('mobile', $mobile)->update($data);
                    $response['success'] = True;
                    $response['message'] = "Password Updated";
                    
                }else{
                   
                    $response['message'] = "Otp didn't match";
                  
                }
            }else{

                $response['message'] = "Number/User  didn't exist"; 
            }

        } catch (\Exception $e) {
            Log::error($e->getTraceAsString());
            $response = apiResponseServerError($e);
        }
        return $response;
    }



    // public static function forgotPassword($request) {
    //     $response = [];

    //     try {
    //         $rules = [
    //             'email' => 'required|email|max:255'
    //         ];

    //         $validator = Validator::make($request->all(), $rules);
    //         $validatorResponse = checkValidateRequest($validator);
    //         if ($validatorResponse['success'] === FALSE)
    //             return $validatorResponse;

    //         $userObj = User::where('email', $request->get('email'))->first();
    //         if(!$userObj)
    //             return apiResponseUnauthorized();

    //         $randomPassword = Str::random(8);
    //         $userObj->password = bcrypt($randomPassword);
    //         $userObj->save();
    //         $sendTo = $userObj->email;
    //         Mail::to($sendTo)->send(new ForgotPassword($userObj, $randomPassword));
    //         $response['message'] = 'Please check your email for new password';
    //         $response['success'] = API_SUCCESS;
    //         $response['status'] = API_STATUS_OK;
    //     } catch (\Exception $e) {
    //         Log::error($e->getTraceAsString());
    //         $response = apiResponseServerError($e);
    //     }
    //     return $response;
    // }


    public static function socialLogin($request) {
        try {
            $post_data = $request->all();

            $rules['email'] = 'required';
            // $rules['name'] = 'required';
            //$rules['username'] = 'required';
            $rules['type'] = 'required';

            if ($request->get('type') == 1) {
                $rules['facebook_id'] = 'required';

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    $result['errors'] = array_merge($validator->errors()->toArray());
                    return $result;
                }

                $check_email_exist = User::where('email', $post_data['email'])->count();

                if ($check_email_exist) {
                    $userobj = User::where('email', $post_data['email'])->first();
                    $check_facebook_id_exist = User::where('email', $post_data['email'])->where('facebook_id', $post_data['facebook_id'])->count();

                    if (!$check_facebook_id_exist) {
                        $userobj->facebook_id = $post_data['facebook_id'];
                        $userobj->name = $post_data['name'];
                        if (isset($post_data['username'])) {
                            if ($post_data['username'] != NULL && $post_data['username'] != '') {
                                $userobj->username = $post_data['username'];
                            }
                        }

                        $userobj->save();
                    }
                } else {
                    $userobj = new User;
                    $userobj->facebook_id = $post_data['facebook_id'];
                    $userobj->name = $post_data['name'];
                    $userobj->email = $post_data['email'];
                    if (isset($post_data['username'])) {
                        if ($post_data['username'] != NULL && $post_data['username'] != '') {
                            $userobj->username = $post_data['username'];
                        }
                    }
                    $userobj->password = bcrypt(rand(999999, 6));
                    $userobj->save();
                }
            } elseif ($request->get('type') == 2) {
                $rules['googleplus_id'] = 'required';

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    $result['errors'] = array_merge($validator->errors()->toArray());
                    return $result;
                }

                $check_email_exist = User::where('email', $post_data['email'])->count();

                if ($check_email_exist) {
                    $userobj = User::where('email', $post_data['email'])->first();
                    $check_googleplus_id_exist = User::where('email', $post_data['email'])->where('googleplus_id', $post_data['googleplus_id'])->count();

                    if (!$check_googleplus_id_exist) {
                        $userobj->googleplus_id = $post_data['googleplus_id'];
                        $userobj->name = $post_data['name'];
                        if (isset($post_data['username'])) {
                            if ($post_data['username'] != NULL && $post_data['username'] != '') {
                                $userobj->username = $post_data['username'];
                            }
                        }

                        $userobj->save();
                    }
                } else {
                    $userobj = new User;
                    $userobj->googleplus_id = $post_data['googleplus_id'];
                    $userobj->name = $post_data['name'];
                    $userobj->email = $post_data['email'];
                    if (isset($post_data['username'])) {
                        if ($post_data['username'] != NULL && $post_data['username'] != '') {
                            $userobj->username = $post_data['username'];
                        }
                    }
                    $userobj->password = bcrypt(rand(999999, 6));
                    $userobj->save();
                }
            } elseif ($request->get('type') == 3) {
                $rules['twitter_id'] = 'required';

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    $result['errors'] = array_merge($validator->errors()->toArray());
                    return $result;
                }

                $check_email_exist = User::where('email', $post_data['email'])->count();

                if ($check_email_exist) {
                    $userobj = User::where('email', $post_data['email'])->first();
                    $check_twitter_id_exist = User::where('email', $post_data['email'])->where('twitter_id', $post_data['twitter_id'])->count();

                    if (!$check_twitter_id_exist) {
                        $userobj->twitter_id = $post_data['twitter_id'];
                        $userobj->name = $post_data['name'];
                        if (isset($post_data['username'])) {
                            if ($post_data['username'] != NULL && $post_data['username'] != '') {
                                $userobj->username = $post_data['username'];
                            }
                        }

                        $userobj->save();
                    }
                } else {
                    $userobj = new User;
                    $userobj->twitter_id = $post_data['twitter_id'];
                    $userobj->name = $post_data['name'];
                    $userobj->email = $post_data['email'];
                    if (isset($post_data['username'])) {
                        if ($post_data['username'] != NULL && $post_data['username'] != '') {
                            $userobj->username = $post_data['username'];
                        }
                    }
                    $userobj->password = bcrypt(rand(999999, 6));
                    $userobj->save();
                }
            } elseif ($request->get('type') == 4) {
                $rules['instagram_id'] = 'required';

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    $result['errors'] = array_merge($validator->errors()->toArray());
                    return $result;
                }

                $check_email_exist = User::where('email', $post_data['email'])->count();

                if ($check_email_exist) {
                    $userobj = User::where('email', $post_data['email'])->first();
                    $check_instagram_id_exist = User::where('email', $post_data['email'])->where('instagram_id', $post_data['instagram_id'])->count();

                    if (!$check_instagram_id_exist) {
                        $userobj->instagram_id = $post_data['instagram_id'];
                        $userobj->name = $post_data['name'];
                        if (isset($post_data['username'])) {
                            if ($post_data['username'] != NULL && $post_data['username'] != '') {
                                $userobj->username = $post_data['username'];
                            }
                        }

                        $userobj->save();
                    }
                } else {
                    $userobj = new User;
                    $userobj->instagram_id = $post_data['instagram_id'];
                    $userobj->name = $post_data['name'];
                    $userobj->email = $post_data['email'];
                    if (isset($post_data['username'])) {
                        if ($post_data['username'] != NULL && $post_data['username'] != '') {
                            $userobj->username = $post_data['username'];
                        }
                    }
                    $userobj->password = bcrypt(rand(999999, 6));
                    $userobj->save();
                }
            }

            $user_id = $userobj->id;
            $token = $userobj->createToken($user_id . ' token ')->accessToken;
            $result['access_token'] = $token;
            $result['user'] = $userobj;

            $result['success'] = TRUE;
        } catch (\Exception $e) {
            $result = [
                'error' => $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile()
            ];
            Log::error($e->getTraceAsString());
            $result['success'] = FALSE;
        }

        return $result;
    }

}
