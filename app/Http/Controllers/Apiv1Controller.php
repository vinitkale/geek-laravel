<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use JWTAuth;
use Auth;
use Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Dingo\Api\Exception\StoreResourceFailedException as store_exception;
use Dingo\Api\Exception\UpdateResourceFailedException as update_exception;
use App\User;
use App\Event;
use App\Blog;
use App\Image;
use App\Category;
use App\Country;
use App\State;
use App\City;
use App\Organization;
use App\Location;
use App\Review;
use App\Rating;
use App\Reply;
use App\Visit;
use App\Favorites;
use App\Banner;
use DB;
use App\ForgotToken;
use App\ContactUs;
use App\Attendance;
use App\BlogCategory;
use App\Email;
use Illuminate\Support\Facades\Mail;

class Apiv1Controller extends Controller {

    public function __construct() {

        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Range, Content-Disposition, Content-Type, Authorization');
        header('Access-Control-Allow-Credentials: true');
    }

    public function registration() {
        $credentials = json_decode(file_get_contents("php://input"), true);
        $rules = array(
            'email' => 'required|email|max:255|unique:users',
            'username' => 'required|max:255|unique:users',
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required'
        );

        $validator = app('validator')->make($credentials, $rules);
        if ($validator->fails()) {
            throw new store_exception('Could not create new user.', $validator->errors());
        } else {
            $credentials['password'] = Hash::make($credentials['password']);
            $user = User::create($credentials);
            if (!empty($user)) {
                $template = Email::find(1);
                if ($template->status == 1) {
                    $hrf = 'activate-now/' . $user->user_id;

                    // Set Body
                    if ($template->body != '') {
                        $content = str_replace('%fullname%', $user->first_name . ' ' . $user->last_name, $template->body);
                    } else {
                        $content = "<b>Hii, $user_detail->first_name $user_detail->last_name! </b><br>Thank ypu for registration, please click Active Now Botton for activate your account.<br>";
                    }
                    $data = array(
                        'content' => $content,
                        'link' => $hrf,
                        'btntitle' => 'Activate Now'
                    );

                    // Set Subject
                    if ($template->subject != '') {
                        $subject = $template->subject;
                    } else {
                        $subject = 'Welcome To GeekMeet';
                    }


                    // Set From
                    if ($template->from_name != '') {
                        $from = $template->from_name;
                    } else {
                        $from = 'GeekMeet';
                    }

                    //  return view('email.email_view',$data);   
//                    $email_id = $inputs['email'];
                    $email_id = $user->email;
                    $status = Mail::send('email.email_view', $data, function($message) use($email_id, $subject, $from) {
                                $message->to($email_id)->subject($subject);
                                $message->from('support@geekmeet.com', $from);
                            });

                    if ($status) {
                       $token = JWTAuth::fromUser($user);
                       return response()->json(['error' => '', 'message' => 'User Registered Successfully Please Check Your Email To Activate Your Account', 'data' => compact('token')], 200);
                    } else {
                        return response()->json(['error' => '', 'message' => 'some thing went wrong'], 200);
                    }
               } else {
                    return response()->json(['error' => '', 'message' => 'some thing went wrong'], 200);
                }
            }

            
        }
    }

    public function get_popular_event() {
        $perameter = json_decode(file_get_contents("php://input"), true);
        $query = Event::select(['event_id', 'event_title', 'event_description', 'start_date', 'end_date', 'start_time', 'end_time', 'category_id', 'image_id'])->where('start_date', '>=', date('Y-m-d', time()));

        if (isset($perameter['category']) && $perameter['category'] != '') {
            $query->where('category_id', 'LIKE', '%' . $perameter['category'] . '%');
        }
        $query->orderBy('start_date', 'asc');
        $event = $query->paginate(6)->toArray();
        
                
        if (!empty($event['data'])) {
            foreach ($event['data'] as $key => $val) {
                $date_obj = date_create($val['start_date']);
                $event['data'][$key]['start_date'] = date_format($date_obj, 'd M');
                $date_obj = date_create($val['end_date']);
                $event['data'][$key]['end_date'] = date_format($date_obj, 'd M');
                $event['data'][$key]['start_time'] = date('h:i A', strtotime($val['start_time']));
                $event['data'][$key]['end_time'] = date('h:i A', strtotime($val['end_time']));
                $event['data'][$key]['event_description'] = html_entity_decode($val['event_description']);

                if ($val['image_id'] != '') {
                    if (strpos($val['image_id'], ',') !== false) {
                        $img_arr = explode(',', $val['image_id']);
                    } else {
                        $img_arr = array($val['image_id']);
                    }
                    $featurd_arr = array();
                    foreach ($img_arr as $img_val) {
                        $image_data = Image::where('image_id', $img_val)->where('featured_image', 1)->get()->take(1)->toArray();
                        if (!empty($image_data)) {
                            $featurd_arr = $image_data;
                        }
                    }

                    if (count($featurd_arr) > 0) {
                        $event['data'][$key]['featured_image'] = asset('geekmeet/uploads/events/' . $featurd_arr[0]['name']);
                        $event['data'][$key]['title'] = $featurd_arr[0]['title'];
                    } else {
                        $event['data'][$key]['featured_image'] = asset('geekmeet/uploads/no-image-available.jpg');
                        $event['data'][$key]['title'] = 'default.jpg';
                    }
                } else {
                    $event['data'][$key]['featured_image'] = asset('geekmeet/uploads/no-image-available.jpg');
                    $event['data'][$key]['title'] = 'no-image-available.jpg';
                }

                $event['data'][$key]['rating'] = $this->eventRating($val['event_id']);
                $event['data'][$key]['comment_count'] = Review::where('event_id', $val['event_id'])->count();
            }
          
            return response()->json(['error' => '', 'message' => '', 'data' => $event], 200);
        } else {
            return response()->json(['error' => '', 'message' => 'No Data Found', 'data' => $event], 200);
        }
    }

    public function detailed_popular_event() {
        $perameter = json_decode(file_get_contents("php://input"), true);
        $query = Event::select(['event_id', 'contact_info', 'website', 'audience', 'contact_info', 'event_title', 'event_description', 'start_date', 'end_date', 'start_time', 'image_id', 'end_time', 'gm_venue.address as location', 'gm_venue.venue_name', 'category_id'])->leftJoin('gm_venue', 'gm_events.location', '=', 'gm_venue.venue_id')->where('start_date', '>=', date('Y-m-d', time()))->orderBy('start_date', 'asc');


        if (isset($perameter['category']) && $perameter['category'] != '') {
            $query->where('category_id', 'LIKE', '%' . $perameter['category'] . '%');
        }

        if (isset($perameter['all']) && $perameter['all'] == 'true') {
            $event = $query->paginate(6)->toArray();
        } else {
            $event = $query->paginate(6)->toArray();
        }
        if (!empty($event['data'])) {
            foreach ($event['data'] as $key => $val) {
                $date_obj = date_create($val['start_date']);
                $event['data'][$key]['start_date'] = date_format($date_obj, 'd M');
                $date_obj = date_create($val['end_date']);
                $event['data'][$key]['end_date'] = date_format($date_obj, 'd M');
                $event['data'][$key]['start_time'] = date('h:i A', strtotime($val['start_time']));
                $event['data'][$key]['end_time'] = date('h:i A', strtotime($val['end_time']));
                $event['data'][$key]['event_description'] = html_entity_decode($val['event_description']);

                if ($val['image_id'] != '') {
                    if (strpos($val['image_id'], ',') !== false) {
                        $img_arr = explode(',', $val['image_id']);
                    } else {
                        $img_arr = array($val['image_id']);
                    }
                    $featurd_arr = array();
                    foreach ($img_arr as $img_val) {
                        $image_data = Image::where('image_id', $img_val)->where('featured_image', 1)->get()->take(1)->toArray();
                        if (!empty($image_data)) {
                            $featurd_arr = $image_data;
                        }
                    }

                    if (count($featurd_arr) > 0) {
                        $event['data'][$key]['featured_image'] = asset('geekmeet/uploads/events/' . $featurd_arr[0]['name']);
                        $event['data'][$key]['title'] = $featurd_arr[0]['title'];
                    } else {
                        $event['data'][$key]['featured_image'] = asset('geekmeet/uploads/no-image-available.jpg');
                        $event['data'][$key]['title'] = 'default.jpg';
                    }
                } else {
                    $event['data'][$key]['featured_image'] = asset('geekmeet/uploads/no-image-available.jpg');
                    $event['data'][$key]['title'] = 'no-image-available.jpg';
                }

                $event['data'][$key]['rating'] = $this->eventRating($val['event_id']);
                $event['data'][$key]['comment_count'] = Review::where('event_id', $val['event_id'])->count();
                $event['data'][$key]['favorite'] = $this->get_favorite($val['event_id']);
            }
           

            return response()->json(['error' => '', 'message' => '', 'data' => $event], 200);
        } else {
            return response()->json(['error' => '', 'message' => 'No Data Found', 'data' => $event], 200);
        }
    }

    public function get_event() {
        $perameter = json_decode(file_get_contents("php://input"), true);
        $query = DB::table('gm_events')->select(DB::raw('event_id,event_title,event_description,start_date,end_date,start_time,image_id,end_time,category_id'));

        if (isset($perameter['search']) && $perameter['search'] != '') {
            $location = $this->getLnt($perameter['search']);
            if (!empty($location)) {
                $query = DB::table('gm_events')->select(DB::raw('event_id,event_title,event_description,start_date,end_date,start_time,image_id,end_time,category_id,( 3959 * acos( cos( radians("' . $location['lat'] . '") ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians("' . $location['lng'] . '") ) + sin( radians("' . $location['lat'] . '") ) * sin( radians(latitude) ) ) ) AS distance'))->leftJoin('gm_venue', 'gm_events.location', '=', 'gm_venue.venue_id');
            }
        }
        if (isset($perameter['category']) && $perameter['category'] != '') {
            $query->where('category_id', 'LIKE', '%' . $perameter['category'] . '%');
        }
        if (isset($perameter['type']) && $perameter['type'] != '') {
            switch ($perameter['type']) {
                case 'past':
                    $query->where('start_date', '<', date('Y-m-d', time()));
                    break;
                case 'upcoming':
                    $query->where('start_date', '>=', date('Y-m-d', time()));
                    break;
                case 'current':
                    $query->where('start_date', '=', date('Y-m-d', time()));
                    break;
                default:
            }
        }
        if (isset($perameter['sort']) && $perameter['sort'] != '') {
            switch ($perameter['sort']) {
                case 'title_asc':
                    $query->orderBy('event_title', 'asc');
                    break;
                case 'title_desc':
                    $query->orderBy('event_title', 'desc');
                    break;
                case 'stdate_low_high':
                    $query->orderBy('start_date', 'asc');
                    break;
                case 'stdate_high_low':
                    $query->orderBy('start_date', 'desc');
                    break;
                default:
            }
        }

        if (isset($perameter['search']) && $perameter['search'] != '') {
            $query->orderBy('distance');
        }

        $event = $query->paginate(6)->toArray();

        if (!empty($event['data'])) {

            foreach ($event['data'] as $key => $val) {

                $date_obj = date_create($val->start_date);
                $event['data'][$key]->start_date = date_format($date_obj, 'd M');
                $date_obj = date_create($val->end_date);
                $event['data'][$key]->end_date = date_format($date_obj, 'd M');
                $event['data'][$key]->start_time = date('h:i A', strtotime($val->start_time));
                $event['data'][$key]->end_time = date('h:i A', strtotime($val->end_time));
                $event['data'][$key]->readmore = 0;
                $string = strip_tags($val->event_description);
                if (strlen($string) > 200) {

                    // truncate string
                    $stringCut = substr($string, 0, 200);

                    // make sure it ends in a word so assassinate doesn't become ass...
                    $string = substr($stringCut, 0, strrpos($stringCut, ' '));

                    $event['data'][$key]->readmore = 1;
                }

                $event['data'][$key]->event_description = $string;



                if ($val->image_id != '') {
                    if (strpos($val->image_id, ',') !== false) {
                        $img_arr = explode(',', $val->image_id);
                    } else {
                        $img_arr = array($val->image_id);
                    }
                    $featurd_arr = array();
                    foreach ($img_arr as $img_val) {
                        $image_data = Image::where('image_id', $img_val)->where('featured_image', 1)->get()->take(1)->toArray();
                        if (!empty($image_data)) {
                            $featurd_arr = $image_data;
                        }
                    }

                    if (count($featurd_arr) > 0) {
                        $event['data'][$key]->featured_image = asset('geekmeet/uploads/events/' . $featurd_arr[0]['name']);
                        $event['data'][$key]->title = $featurd_arr[0]['title'];
                    } else {
                        $event['data'][$key]->featured_image = asset('geekmeet/uploads/no-image-available.jpg');
                        $event['data'][$key]->title = 'default.jpg';
                    }
                } else {
                    $event['data'][$key]->featured_image = asset('geekmeet/uploads/no-image-available.jpg');
                    $event['data'][$key]->title = 'no-image-available.jpg';
                }

                $event['data'][$key]->rating = $this->eventRating($val->event_id);
                $event['data'][$key]->comment_count = Review::where('event_id', $val->event_id)->count();
            }

            if (isset($perameter['sort']) && $perameter['sort'] == 'rating') {
                foreach ($event['data'] as $key => $row) {
                    $rating[$key] = $row->rating;
                }

                array_multisort($rating, SORT_DESC, $event['data']);
            }
            if (isset($perameter['sort']) && $perameter['sort'] == 'reviews') {
                foreach ($event['data'] as $key => $row) {
                    $comment[$key] = $row->comment_count;
                }

                array_multisort($comment, SORT_DESC, $event['data']);
            }
            return response()->json(['error' => '', 'message' => '', 'data' => $event], 200);
        } else {
            return response()->json(['error' => '', 'message' => 'No Data Found', 'data' => $event], 200);
        }
    }

    public function detailed_event() {
        $perameter = json_decode(file_get_contents("php://input"), true);
        $query = DB::table('gm_events')->select(DB::raw('event_id,   contact_info,website,audience,contact_info,event_title,event_description,start_date,end_date,start_time,image_id,end_time,gm_venue.address as location,gm_venue.venue_name,category_id'))->leftJoin('gm_venue', 'gm_events.location', '=', 'gm_venue.venue_id');

        if (isset($perameter['search']) && $perameter['search'] != '') {
            $location = $this->getLnt($perameter['search']);
            if (!empty($location)) {
                $query = DB::table('gm_events')->select(DB::raw('event_id,   contact_info,website,audience,contact_info,event_title,event_description,start_date,end_date,start_time,image_id,end_time,gm_venue.address as location,gm_venue.venue_name,category_id,( 3959 * acos( cos( radians("' . $location['lat'] . '") ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians("' . $location['lng'] . '") ) + sin( radians("' . $location['lat'] . '") ) * sin( radians(latitude) ) ) ) AS distance'))->leftJoin('gm_venue', 'gm_events.location', '=', 'gm_venue.venue_id');
            }
        }



        if (isset($perameter['my-event']) && $perameter['my-event'] == TRUE) {
            $user = JWTAuth::parseToken()->authenticate();
            $user_id = $user->user_id;
            $query->where('event_organize_by', $user_id);
        }

        if (isset($perameter['category']) && $perameter['category'] != '') {
            $query->where('category_id', 'LIKE', '%' . $perameter['category'] . '%');
        }
        if (isset($perameter['type']) && $perameter['type'] != '') {
            switch ($perameter['type']) {
                case 'past':
                    $query->where('start_date', '<', date('Y-m-d', time()));
                    break;
                case 'upcoming':
                    $query->where('start_date', '>=', date('Y-m-d', time()));
                    break;
                case 'current':
                    $query->where('start_date', '=', date('Y-m-d', time()));
                    break;
                default:
            }
        }
        if (isset($perameter['sort']) && $perameter['sort'] != '') {
            switch ($perameter['sort']) {
                case 'title_asc':
                    $query->orderBy('event_title', 'asc');
                    break;
                case 'title_desc':
                    $query->orderBy('event_title', 'desc');
                    break;
                case 'stdate_low_high':
                    $query->orderBy('start_date', 'asc');
                    break;
                case 'stdate_high_low':
                    $query->orderBy('start_date', 'desc');
                    break;
                default:
            }
        }

        if (isset($perameter['search']) && $perameter['search'] != '') {
//            $query->having('distance', '>', 100)
            $query->orderBy('distance');
        }


        $event = $query->paginate(6)->toArray();
        
        
        if (!empty($event)) {

            foreach ($event['data'] as $key => $val) {

                $date_obj = date_create($val->start_date);
                $event['data'][$key]->start_date = date_format($date_obj, 'd M');
                $date_obj = date_create($val->end_date);
                $event['data'][$key]->end_date = date_format($date_obj, 'd M');
                $event['data'][$key]->start_time = date('h:i A', strtotime($val->start_time));
                $event['data'][$key]->end_time = date('h:i A', strtotime($val->end_time));
                $event['data'][$key]->readmore = 0;
                $string = strip_tags($val->event_description);
                if (strlen($string) > 200) {

                    // truncate string
                    $stringCut = substr($string, 0, 200);

                    // make sure it ends in a word so assassinate doesn't become ass...
                    $string = substr($stringCut, 0, strrpos($stringCut, ' '));

                    $event['data'][$key]->readmore = 1;
                }

                $event['data'][$key]->event_description = $string;



                if ($val->image_id != '') {
                    if (strpos($val->image_id, ',') !== false) {
                        $img_arr = explode(',', $val->image_id);
                    } else {
                        $img_arr = array($val->image_id);
                    }
                    $featurd_arr = array();
                    foreach ($img_arr as $img_val) {
                        $image_data = Image::where('image_id', $img_val)->where('featured_image', 1)->get()->take(1)->toArray();
                        if (!empty($image_data)) {
                            $featurd_arr = $image_data;
                        }
                    }

                    if (count($featurd_arr) > 0) {
                        $event['data'][$key]->featured_image = asset('geekmeet/uploads/events/' . $featurd_arr[0]['name']);
                        $event['data'][$key]->title = $featurd_arr[0]['title'];
                    } else {
                        $event['data'][$key]->featured_image = asset('geekmeet/uploads/no-image-available.jpg');
                        $event['data'][$key]->title = 'default.jpg';
                    }
                } else {
                    $event['data'][$key]->featured_image = asset('geekmeet/uploads/no-image-available.jpg');
                    $event['data'][$key]->title = 'no-image-available.jpg';
                }

                $event['data'][$key]->rating = $this->eventRating($val->event_id);
                $event['data'][$key]->comment_count = Review::where('event_id', $val->event_id)->count();
            }

            if (isset($perameter['sort']) && $perameter['sort'] == 'rating') {
                foreach ($event['data'] as $key => $row) {
                    $rating[$key] = $row->rating;
                }

                array_multisort($rating, SORT_DESC, $event['data']);
            }
            if (isset($perameter['sort']) && $perameter['sort'] == 'reviews') {
                foreach ($event['data'] as $key => $row) {
                    $comment[$key] = $row->comment_count;
                }

                array_multisort($comment, SORT_DESC, $event['data']);
            }
            return response()->json(['error' => '', 'message' => '', 'data' => $event], 200);
        } else {
            return response()->json(['error' => '', 'message' => 'No Data Found', 'data' => $event], 200);
        }
    }

    public function get_upcoming_event() {
        $perameter = json_decode(file_get_contents("php://input"), true);
        $query = Event::select(['event_id', 'event_title', 'event_description', 'start_date', 'end_date', 'start_time', 'end_time', 'category_id', 'image_id'])->where('start_date', '>=', date('Y-m-d', time()))->orderBy('start_date', 'asc');

        if (isset($perameter['category']) && $perameter['category'] != '') {
            $query->where('category_id', 'LIKE', '%' . $perameter['category'] . '%');
        }

        if (isset($perameter['all']) && $perameter['all'] == 'true') {
            $event = $query->paginate(6);
        } else {
            $event = $query->paginate(6);
        }
        if ($event->count() > 0) {
            foreach ($event as $key => $val) {
                $date_obj = date_create($val->start_date);
                $event[$key]->start_date = date_format($date_obj, 'd M');
                $date_obj = date_create($val->end_date);
                $event[$key]->end_date = date_format($date_obj, 'd M');
                $string = strip_tags($val->event_description);
                $event[$key]->start_time = date('h:i A', strtotime($val->start_time));
                $event[$key]->end_time = date('h:i A', strtotime($val->end_time));
                $event[$key]->read_more = 0;
                if (strlen($string) > 200) {

                    // truncate string
                    $stringCut = substr($string, 0, 200);

                    // make sure it ends in a word so assassinate doesn't become ass...
                    $string = substr($stringCut, 0, strrpos($stringCut, ' '));
                    $event[$key]->read_more = 1;
                }

                $event[$key]->event_description = $string;

                if ($val->image_id != '') {
                    if (strpos($val->image_id, ',') !== false) {
                        $img_arr = explode(',', $val->image_id);
                    } else {
                        $img_arr = array($val->image_id);
                    }
                    $featurd_arr = array();
                    foreach ($img_arr as $img_val) {
                        $image_data = Image::where('image_id', $img_val)->where('featured_image', 1)->get()->take(1)->toArray();
                        if (!empty($image_data)) {
                            $featurd_arr = $image_data;
                        }
                    }

                    if (count($featurd_arr) > 0) {
                        $event[$key]->featured_image = asset('geekmeet/uploads/events/' . $featurd_arr[0]['name']);
                        $event[$key]->title = $featurd_arr[0]['title'];
                    } else {
                        $event[$key]->featured_image = asset('geekmeet/uploads/no-image-available.jpg');
                        $event[$key]->title = 'default.jpg';
                    }
                } else {
                    $event[$key]->featured_image = asset('geekmeet/uploads/no-image-available.jpg');
                    $event[$key]->title = 'no-image-available.jpg';
                }
                $event[$key]->rating = $this->eventRating($val->event_id);
                $event[$key]->comment_count = Review::where('event_id', $val->event_id)->count();
            }
            return response()->json(['error' => '', 'message' => '', 'data' => $event], 200);
        } else {
            return response()->json(['error' => '', 'message' => 'No Data Found', 'data' => $event], 200);
        }
    }

    public function detailed_upcoming_event() {
        $perameter = json_decode(file_get_contents("php://input"), true);
        $query = Event::select(['event_id', 'contact_info', 'website', 'audience', 'contact_info', 'event_title', 'event_description', 'start_date', 'end_date', 'start_time', 'image_id', 'end_time', 'gm_venue.address as location', 'gm_venue.venue_name', 'category_id'])->leftJoin('gm_venue', 'gm_events.location', '=', 'gm_venue.venue_id')->where('start_date', '>=', date('Y-m-d', time()))->orderBy('start_date', 'asc');

        if (isset($perameter['category']) && $perameter['category'] != '') {
            $query->where('category_id', 'LIKE', '%' . $perameter['category'] . '%');
        }

        if (isset($perameter['all']) && $perameter['all'] == 'true') {
            $event = $query->paginate(6);
        } else {
            $event = $query->paginate(6);
        }

        if ($event->count() > 0) {
            foreach ($event as $key => $val) {
                $date_obj = date_create($val->start_date);
                $event[$key]->start_date = date_format($date_obj, 'd M');
                $date_obj = date_create($val->end_date);
                $event[$key]->end_date = date_format($date_obj, 'd M');
                $string = strip_tags($val->event_description);
                $event[$key]->start_time = date('h:i A', strtotime($val->start_time));
                $event[$key]->end_time = date('h:i A', strtotime($val->end_time));
                $event[$key]->read_more = 0;
                if (strlen($string) > 200) {

                    // truncate string
                    $stringCut = substr($string, 0, 200);

                    // make sure it ends in a word so assassinate doesn't become ass...
                    $string = substr($stringCut, 0, strrpos($stringCut, ' '));
                    $event[$key]->read_more = 1;
                }

                $event[$key]->event_description = $string;

                if ($val->image_id != '') {
                    if (strpos($val->image_id, ',') !== false) {
                        $img_arr = explode(',', $val->image_id);
                    } else {
                        $img_arr = array($val->image_id);
                    }
                    $featurd_arr = array();
                    foreach ($img_arr as $img_val) {
                        $image_data = Image::where('image_id', $img_val)->where('featured_image', 1)->get()->take(1)->toArray();
                        if (!empty($image_data)) {
                            $featurd_arr = $image_data;
                        }
                    }

                    if (count($featurd_arr) > 0) {
                        $event[$key]->featured_image = asset('geekmeet/uploads/events/' . $featurd_arr[0]['name']);
                        $event[$key]->title = $featurd_arr[0]['title'];
                    } else {
                        $event[$key]->featured_image = asset('geekmeet/uploads/no-image-available.jpg');
                        $event[$key]->title = 'default.jpg';
                    }
                } else {
                    $event[$key]->featured_image = asset('geekmeet/uploads/no-image-available.jpg');
                    $event[$key]->title = 'no-image-available.jpg';
                }
                $event[$key]->rating = $this->eventRating($val->event_id);
                $event[$key]->comment_count = Review::where('event_id', $val->event_id)->count();
                $event[$key]->favorite = $this->get_favorite($val->event_id);
            }
            return response()->json(['error' => '', 'message' => '', 'data' => $event], 200);
        } else {
            return response()->json(['error' => '', 'message' => 'No Data Found', 'data' => $event], 200);
        }
    }

    public function get_blog() {
        $perameter = json_decode(file_get_contents("php://input"), true);        
        $query = Blog::select(['blog_id','blog_category','blog_title', 'blog_content', 'gm_blogs.status', 'gm_blogs.published_date as create_date', 'gm_images.name as thumb', 'users.username', 'title'])->leftJoin('users', 'gm_blogs.added_by', '=', 'users.user_id');
        
        if (isset($perameter['category']) && $perameter['category'] != '') {
            $query->where('blog_category', 'LIKE', '%' . $perameter['category'] . '%');
        }
        
        $blog = $query->leftJoin('gm_images', 'gm_blogs.thumb', '=', 'gm_images.image_id')->orderby('published_date', 'desc')->paginate(4);

        if ($blog->count() > 0) {
            foreach ($blog as $key => $val) {
                $date_obj = date_create($val->create_date);
                $blog[$key]->create_date = date_format($date_obj, 'F j, Y');
                $blog[$key]->blog_content = $val->blog_content;
                $blog[$key]->read_more = 0;

                $string = strip_tags($val->blog_content);

                if (strlen($string) > 200) {

                    // truncate string
                    $blog[$key]->read_more = 1;
                    $stringCut = substr($string, 0, 200);

                    // make sure it ends in a word so assassinate doesn't become ass...
                    $string = substr($stringCut, 0, strrpos($stringCut, ' '));
                }

                $blog[$key]->blog_content = $string;
                if ($val->thumb != '') {
                    $blog[$key]->thumb = asset('geekmeet/uploads/blog/' . $val->thumb);
                } else {
                    $blog[$key]->thumb = asset('geekmeet/uploads/no-image-available.jpg');
                    $blog[$key]->title = 'no-image-available.jpg';
                }
            }
            return response()->json(['error' => '', 'message' => '', 'data' => $blog], 200);
        } else {
            return response()->json(['error' => '', 'message' => 'No Data Found', 'data' => $blog], 200);
        }
    }

    public function get_category_menu() {

        $category = Category::select(['category_id', 'category_name', 'category_status'])->where('category_status', 1)->take(5)->get();
        if ($category->count() > 0) {
            return response()->json(['error' => '', 'message' => '', 'data' => $category], 200);
        } else {
            return response()->json(['error' => '', 'message' => 'No Data Found', 'data' => $category], 200);
        }
    }
    public function get_blog_category() {

        $category = BlogCategory::select(['blog_category_id', 'blog_category_name', 'blog_category_status'])->where('blog_category_status', 1)->take(5)->get();
        if ($category->count() > 0) {
            return response()->json(['error' => '', 'message' => '', 'data' => $category], 200);
        } else {
            return response()->json(['error' => '', 'message' => 'No Data Found', 'data' => $category], 200);
        }
    }

    public function event_detail($id) {

        $event = Event::select(['event_id', 'website', 'audience', 'event_title', 'event_description', 'start_date', 'end_date', 'start_time', 'gm_events.image_id as event_images', 'end_time', 'category_id', 'users.username', 'users.first_name', 'users.last_name', 'gm_organizations.organization_name as org_name', 'organization_location', 'organization_email', 'organization_website', 'organization_description', 'gm_images.name as organization_logo', 'gm_images.title as organization_logo_title', 'organization_contact'])->leftJoin('users', 'gm_events.event_organize_by', '=', 'users.user_id')->leftJoin('gm_organizations', 'gm_events.organizers', '=', 'gm_organizations.organization_id')->leftJoin('gm_images', 'gm_organizations.organization_logo', '=', 'gm_images.image_id')->where('event_id', $id)->get();

        if ($event->count() > 0) {
            $event = $event[0];

            $date_obj = date_create($event->start_date);
            $event->start_date = date_format($date_obj, 'd M ,Y');
            $event->start_time = date('h:i A', strtotime($event->start_time));
            $event->end_time = date('h:i A', strtotime($event->end_time));
            $date_obj = date_create($event->end_date);
            $event->end_date = date_format($date_obj, 'd M ,Y');
            $string = strip_tags($event->event_description);
            if ($event->org_name != '') {
                $event->organizer = $event->org_name;
            } else {
                $event->organizer = $event->first_name . ' ' . $event->last_name;
            }
            if ($event->audience != '') {
                $event->audience = 'All';
            }

            if ($event->organization_logo != '') {
                $event->organization_logo = asset('geekmeet/uploads/user/' . $event->organization_logo);
            } else {
                $event->organization_logo = $event->username;
                $event->organization_logo = asset('geekmeet/uploads/no-image-available.jpg');
                $event->organization_logo_title = 'no-image-available.jpg';
            }


            $event->event_description = $string;
            $image_arr = array();
            if ($event->event_images != '') {

                $img_arr = explode(',', $event->event_images);


                foreach ($img_arr as $img_val) {
                    $image_data = Image::where('image_id', $img_val)->where('featured_image', '1')->get()->toArray();
                    if (!empty($image_data)) {

                        $image_arr[] = array(
                            'image_url' => asset("geekmeet/uploads/events/" . $image_data[0]['name']),
                            'image_title' => $image_data[0]['title']
                        );
                    }
                }
            } else {
                $image_arr[] = array(
                    'image_url' => asset('geekmeet/uploads/no-image-available.jpg'),
                    'image_title' => 'no-image-available.jpg'
                );
            }

            if (empty($image_arr)) {
                $image_arr[] = array(
                    'image_url' => asset('geekmeet/uploads/no-image-available.jpg'),
                    'image_title' => 'no-image-available.jpg'
                );
            }
            $event->slider_image = $image_arr;
            $event->rating = $this->eventRating($event->event_id);
            $event->visit = Visit::where('event_id', $event->event_id)->count();
            $event->today_visit = Visit::where('event_id', $event->event_id)->whereDate('created_at', '=', date('Y-m-d', time()))->count();
            // get related event
            $event_id = $event->event_id;
            $category_id = $event->category_id;
            $event_title = $event->event_title;
            $event_description = $event->event_description;

            $event->related_event = $this->getRelatedEvent($event_id, $category_id, $event_title, $event_description);
            $event->total_attendance = Attendance::where('event_id', $event->event_id)->count();
            return response()->json(['error' => '', 'message' => '', 'data' => $event], 200);
        } else {
            return response()->json(['error' => '', 'message' => 'No Data Found', 'data' => $event], 200);
        }
    }

    public function user_event_detail($id) {
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->user_id;
        $event = Event::select(['event_id', 'contact_info', 'website', 'audience', 'contact_info', 'event_title', 'event_description', 'start_date', 'end_date', 'start_time', 'gm_events.image_id as event_images', 'end_time', 'gm_venue.address as location', 'category_id', 'users.username', 'users.first_name', 'users.last_name', 'gm_organizations.organization_name as org_name', 'organization_location', 'gm_venue.venue_name', 'organization_email', 'organization_website', 'organization_description', 'gm_images.name as organization_logo', 'gm_images.title as organization_logo_title', 'organization_contact'])->leftJoin('gm_venue', 'gm_events.location', '=', 'gm_venue.venue_id')->leftJoin('users', 'gm_events.event_organize_by', '=', 'users.user_id')->leftJoin('gm_organizations', 'gm_events.organizers', '=', 'gm_organizations.organization_id')->leftJoin('gm_images', 'gm_organizations.organization_logo', '=', 'gm_images.image_id')->where('event_id', $id)->get();

        if ($event->count() > 0) {
            $event = $event[0];
            $startinputDate = "$event->start_date $event->start_time";
//          
            $startdatetime = date('Y-m-d\TH:i:sP', strtotime($startinputDate));
            $endinputDate = "$event->end_date $event->end_time";
            $enddatetime = date("Y-m-d\TH:i:sP", strtotime($endinputDate));
            $event->start_datetime = $startdatetime;
            $event->end_datetime = $enddatetime;

            $date_obj = date_create($event->start_date);
            $event->start_date = date_format($date_obj, 'd M ,Y');
            $event->start_time = date('h:i A', strtotime($event->start_time));
            $event->end_time = date('h:i A', strtotime($event->end_time));
            $date_obj = date_create($event->end_date);
            $event->end_date = date_format($date_obj, 'd M ,Y');


            $string = strip_tags($event->event_description);
            if ($event->org_name != '') {
                $event->organizer = $event->org_name;
            } else {
                $event->organizer = $event->first_name . ' ' . $event->last_name;
            }
            if ($event->audience != '') {
                $event->audience = 'All';
            }

            if ($event->organization_logo != '') {
                $event->organization_logo = asset('geekmeet/uploads/user/' . $event->organization_logo);
            } else {
                $event->organization_logo = $event->username;
                $event->organization_logo = asset('geekmeet/uploads/no-image-available.jpg');
                $event->organization_logo_title = 'no-image-available.jpg';
            }


            $event->event_description = $string;
            $image_arr = array();
            if ($event->event_images != '') {

                $img_arr = explode(',', $event->event_images);


                foreach ($img_arr as $img_val) {
                    $image_data = Image::where('image_id', $img_val)->where('featured_image', '1')->get()->toArray();
                    if (!empty($image_data)) {

                        $image_arr[] = array(
                            'image_url' => asset("geekmeet/uploads/events/" . $image_data[0]['name']),
                            'image_title' => $image_data[0]['title']
                        );
                    }
                }
            } else {
                $image_arr[] = array(
                    'image_url' => asset('geekmeet/uploads/no-image-available.jpg'),
                    'image_title' => 'no-image-available.jpg'
                );
            }

            if (empty($image_arr)) {
                $image_arr[] = array(
                    'image_url' => asset('geekmeet/uploads/no-image-available.jpg'),
                    'image_title' => 'no-image-available.jpg'
                );
            }
            $event->slider_image = $image_arr;
            $event->rating = $this->eventRating($event->event_id);
            $event->visit = Visit::where('event_id', $event->event_id)->count();
            $event->today_visit = Visit::where('event_id', $event->event_id)->whereDate('created_at', '=', date('Y-m-d', time()))->count();

            // get related event
            $event_id = $event->event_id;
            $category_id = $event->category_id;
            $event_title = $event->event_title;
            $event_description = $event->event_description;

            $event->related_event = $this->getRelatedEvent($event_id, $category_id, $event_title, $event_description);
            $event->favorite = $this->get_favorite($event->event_id);
            $event->total_attendance = Attendance::where('event_id', $event->event_id)->count();
            $event->own_status = Attendance::where('user_id', $user_id)->where('event_id', $event->event_id)->count();
            return response()->json(['error' => '', 'message' => '', 'data' => $event], 200);
        } else {
            return response()->json(['error' => '', 'message' => 'No Data Found', 'data' => $event], 200);
        }
    }

    public function blog_detail($id) {

        $blog = Blog::select(['blog_id', 'blog_title', 'blog_content', 'blog_category', 'gm_blogs.status', 'gm_blogs.published_date as create_date', 'gm_images.name as thumb', 'users.username', 'title'])->leftJoin('users', 'gm_blogs.added_by', '=', 'users.user_id')->leftJoin('gm_images', 'gm_blogs.thumb', '=', 'gm_images.image_id')->orderby('published_date', 'desc')->where('blog_id', $id)->get();

        $previous = Blog::where('blog_id', '<', $id)->max('blog_id');

        // get next user id
        $next = Blog::where('blog_id', '>', $id)->min('blog_id');

        if ($blog->count() > 0) {
            foreach ($blog as $key => $val) {
                $date_obj = date_create($val->create_date);
                $blog[$key]->create_date = date_format($date_obj, 'F j, Y');
                $blog[$key]->blog_content = $val->blog_content;


                $string = strip_tags($val->blog_content);



                $blog[$key]->blog_content = $string;
                if ($val->thumb != '') {
                    $blog[$key]->thumb = asset('geekmeet/uploads/blog/' . $val->thumb);
                } else {
                    $blog[$key]->thumb = asset('geekmeet/uploads/no-image-available.jpg');
                    $blog[$key]->title = 'no-image-available.jpg';
                }
            }
            return response()->json(['error' => '', 'message' => '', 'data' => $blog, 'next' => $next, 'previous' => $previous], 200);
        } else {
            return response()->json(['error' => '', 'message' => 'No Data Found', 'data' => $blog], 200);
        }
    }

    public function social_login() {
        $inputs = json_decode(file_get_contents("php://input"), true);
        if ($inputs['token'] != '') {
            $user_detail = User::where('socail_token', $inputs['token'])->get()->toArray();


            if (!count($user_detail)) {
                $data = array(
                    'username' => $inputs['first_name'],
                    'first_name' => $inputs['first_name'],
                    'last_name' => $inputs['last_name'],
                    'socail_token' => $inputs['token'],
                    'created_at' => date("Y-m-d H:i:s")
                );

                if (array_key_exists('image', $inputs)) {
                    $path = base_path() . '/uploads/user/';
                    $db_path = 'user_' . time();
                    copy($inputs['image'], $path . '//' . $db_path . '.jpeg');
                    $imageName = $db_path . '.jpeg';
                    $image_data = array(
                        'name' => $imageName,
                        'type' => 'user',
                    );
                    $image = Image::create($image_data);
                    $data['profile_image'] = $image->image_id;
                }
                $user = User::create($data);
                $token = JWTAuth::fromUser($user);
                return response()->json(['error' => '', 'message' => 'User Login Successfully', 'data' => compact('token')], 200);
            } else {
                $user = User::where('socail_token', $inputs['token'])->first();
                $token = JWTAuth::fromUser($user);
                return response()->json(['error' => '', 'message' => 'User Login Successfully', 'data' => compact('token')], 200);
            }
        }
    }

    // to get authenticate user data


    public function get_profile() {
        $user = JWTAuth::parseToken()->authenticate();
        $userId = $user->user_id;

        $user_detail = User::select(['user_id', 'first_name', 'country', 'state', 'users.city', 'last_name', 'email', 'address', 'gender', 'gender', 'dob', 'birth_place', 'about_me', 'zip_code', 'phone', 'favorite_category', 'gm_images.name as image', 'gm_images.title as title', 'gm_cities.name as city_name'])->where('user_id', $userId)->leftJoin('gm_cities', 'users.city', '=', 'gm_cities.city_id')->leftJoin('gm_images', 'users.profile_image', '=', 'gm_images.image_id')->get();
        if ($user_detail->count() > 0) {
            $user_detail = $user_detail[0];

            if ($user_detail->favorite_category != '') {
                $user_detail->favorite_category = explode(',', $user_detail->favorite_category);
            }
            if ($user_detail->image != '') {
                $user_detail->image_url = asset('geekmeet/uploads/user/' . $user_detail->image);
            } else {
                $user_detail->image_url = asset('geekmeet/uploads/user/user-avatar.png');
                $user_detail->title = 'user-avatar';
            }
            return response()->json(['error' => '', 'message' => '', 'data' => $user_detail], 200);
        } else {
            return response()->json(['error' => '', 'message' => 'No Data Found', 'data' => $user_detail], 200);
        }
    }

    public function update_profile() {

        $inputs = json_decode(file_get_contents("php://input"), true);
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->user_id;
        $user_detail = User::find($user_id);

        $rules = array(
            'first_name' => 'required',
            'last_name' => 'required',
            'dob' => 'required',
            'birth_place' => 'required',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'zip_code' => 'required',
            'gender' => 'required',
            'address' => 'required'
        );

        if ($inputs['email'] == $user_detail['email']) {
            $rules['email'] = 'required|email|max:255';
        } else {
            $rules['email'] = 'required|email|max:255|unique:users';
        }
//            $messages = ['required' => 'this field is required'];

        $validator = app('validator')->make($inputs, $rules);
        if ($validator->fails()) {
            throw new update_exception('Could not update profile.', $validator->errors());
        } else {

            if ($inputs['image'] != '') {
                $strFileName = 'user_' . time() . '.jpg';

                $path = base_path() . '/uploads/user/' . $strFileName;
                $image_file = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $inputs['image']));

                if (file_put_contents($path, $image_file)) {
                    $image_data = array(
                        'name' => $strFileName,
                        'type' => 'user',
                    );
                    $image = Image::create($image_data);
                    if (!empty($image)) {
                        $inputs['profile_image'] = $image->image_id;
                        $this->delete_images($user_detail['profile_image'], 'user');
                    }
                }
            } else {
                $inputs['profile_image'] = $user_detail['profile_image'];
            }



            $inputs['dob'] = date('Y-m-d', strtotime($inputs['dob']));
            if ($inputs['favorite_category'] != '') {
                $interest_topic = explode(',', $inputs['favorite_category']);
                foreach ($interest_topic as $cat) {
                    $cate_res = Category::where('category_name', $cat)->get();


                    if ($cate_res->isEmpty()) {
                        $category = new Category;
                        $category->category_name = $cat;
                        $category->category_status = 1;
                        $category->save();
                    }
                }
                $inputs['favorite_category'] = $inputs['favorite_category'];
            }

            unset($inputs['contact_info']);
            unset($inputs['image']);
            $res = User::where('user_id', $user_id)
                    ->update($inputs);
            if ($res != FALSE) {
                return response()->json(['error' => '', 'message' => 'Profile update successfully'], 200);
            } else {
                return response()->json(['error' => '', 'message' => 'Could not update profile.'], 422);
            }
        }
    }

    public function delete_images($image_id, $type) {

        if ($image_id != '') {
            if (strpos($image_id, ',') > 0) {
                $ids = explode(',', $image_id);
                foreach ($ids as $val) {
                    $image_del = Image::find($val);
                    $path = base_path() . "/uploads/$type/";
                    if (!empty($image_del)) {
                        if (file_exists($path . $image_del->name)) {
                            unlink($path . $image_del->name);
                        }
                        $image_del->delete();
                    }
                }
            } else {
                $image_del = Image::find($image_id);
                $path = base_path() . "/uploads/$type/";
                if (!empty($image_del)) {
                    if (file_exists($path . $image_del->name)) {
                        unlink($path . $image_del->name);
                    }
                    $image_del->delete();
                }
            }
            return true;
        }
    }

    public function get_country() {
        $country = Country::all();
        return response()->json(['error' => '', 'message' => '', 'data' => $country], 200);
    }

    public function get_state($country_id) {
        $state = State::where('country_id', $country_id)->get();
        if ($state->count() > 0) {
            return response()->json(['error' => '', 'message' => '', 'data' => $state], 200);
        } else {
            return response()->json(['error' => '', 'message' => 'No Data Found', 'data' => $state], 200);
        }
    }

    public function get_city($state_id) {
        $city = City::where('state_id', $state_id)->get();
        if ($city->count() > 0) {
            return response()->json(['error' => '', 'message' => '', 'data' => $city], 200);
        } else {
            return response()->json(['error' => '', 'message' => 'No Data Found', 'data' => $city], 200);
        }
    }

    public function get_venue() {
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->user_id;
        $location = Location::where('user_id', $user_id)->orderBy('venue_id', 'desc')->get();
        if ($location->count() > 0) {
            return response()->json(['error' => '', 'message' => '', 'data' => $location], 200);
        } else {
            return response()->json(['error' => '', 'message' => 'No Data Found', 'data' => $location], 200);
        }
    }

    public function get_organization() {
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->user_id;
        $organization = Organization::select(['organization_id', 'gm_organizations.user_id', 'organization_name', 'organization_description', 'organization_location', 'organization_email', 'organization_website', 'organization_contact', 'gm_images.name as organization_logo', 'gm_images.title as title'])->where('user_id', $user_id)->leftJoin('gm_images', 'gm_organizations.organization_logo', '=', 'gm_images.image_id')->orderBy('organization_id', 'desc')->paginate(4);
        if ($organization->count() > 0) {
            foreach ($organization as $key => $val) {
                if ($val->organization_logo != '') {
                    $organization[$key]->organization_logo = asset('geekmeet/uploads/user/' . $val->organization_logo);
                } else {
                    $organization[$key]->organization_logo = asset('geekmeet/uploads/no-image-available.jpg');
                    $organization[$key]->title = 'organization_logo';
                }
            }

            return response()->json(['error' => '', 'message' => '', 'data' => $organization], 200);
        } else {
            return response()->json(['error' => '', 'message' => 'No Data Found', 'data' => $organization], 200);
        }
    }

    public function category_list() {

        $category = Category::select(['category_id', 'category_name', 'category_status'])->where('category_status', 1)->get();
        if (!empty($category)) {
            return response()->json(['error' => '', 'message' => '', 'data' => $category], 200);
        } else {
            return response()->json(['error' => '', 'message' => 'No Data Found', 'data' => $category], 200);
        }
    }

    public function add_event() {

        $inputs = json_decode(file_get_contents("php://input"), true);

        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->user_id;

        $rules = array(
            'event_title' => 'required|max:255',
            'event_description' => 'required',
            'start_date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'website' => 'required',
            'location' => 'required',
            'contact_info' => 'required',
        );
        if ($inputs['start_date'] != date('d-m-Y', time())) {
            $rules['end_date'] = "required";
        }
        $validator = app('validator')->make($inputs, $rules);
        if ($validator->fails()) {
            throw new store_exception('Could not add event.', $validator->errors());
        } else {
            if ($inputs['keyword'] != '') {
                $keyword = explode(',', $inputs['keyword']);
                foreach ($keyword as $cat) {
                    $cate_res = Category::where('category_name', $cat)->get();


                    if ($cate_res->isEmpty()) {
                        $category = new Category;
                        $category->category_name = $cat;
                        $category->category_status = 1;
                        $category->save();
                    }
                }
                $inputs['category_id'] = $inputs['keyword'];
            }
            $inputs['event_organize_by'] = $user_id;



            $inputs['start_date'] = date('Y-m-d', strtotime($inputs['start_date']));
            if ($inputs['start_date'] != date('d-m-Y', time())) {
                $inputs['end_date'] = date('Y-m-d', strtotime($inputs['end_date']));
            } else {
                $inputs['end_date'] = date('Y-m-d', time());
            }




            unset($inputs['images']);
            $res = Event::create($inputs);
            if ($res != FALSE) {
                return response()->json(['error' => '', 'message' => 'Event add successfully'], 200);
            } else {
                return response()->json(['error' => '', 'message' => 'Could not add event'], 422);
            }
        }
    }

    public function add_review() {
        $inputs = json_decode(file_get_contents("php://input"), true);
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->user_id;
        $inputs['review_given_by'] = $user_id;
        if ($inputs['event_review'] != '') {
            $res = Review::create($inputs);
        }
        if ($inputs['rating'] != '') {
            $rating_arr = array(
                'rating' => $inputs['rating'],
                'event_id' => $inputs['event_id'],
                'user_id' => $user_id
            );
            $res = Rating::create($rating_arr);
        }
        if ($res != FALSE) {
            return response()->json(['error' => '', 'message' => 'Review add successfully'], 200);
        } else {
            return response()->json(['error' => '', 'message' => 'Could not add review'], 422);
        }
    }

    public function edit_review() {
        $inputs = json_decode(file_get_contents("php://input"), true);
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->user_id;
        $rules = array(
            'review_id' => 'required',
            'event_review' => 'required'
        );
        $validator = app('validator')->make($inputs, $rules);
        if ($validator->fails()) {
            throw new store_exception('Could not add reply.', $validator->errors());
        } else {
            $updata['event_review'] = $inputs['event_review'];

            $res = Review::where('event_review_id', $inputs['review_id'])->where('review_given_by', $user_id)
                    ->update($updata);
            if ($res != FALSE) {
                return response()->json(['error' => '', 'message' => 'Review updated successfully'], 200);
            } else {
                return response()->json(['error' => '', 'message' => 'Could not update review'], 422);
            }
        }
    }

    public function edit_reply() {
        $inputs = json_decode(file_get_contents("php://input"), true);
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->user_id;
        $rules = array(
            'review_reply_id' => 'required',
            'reply' => 'required'
        );
        $validator = app('validator')->make($inputs, $rules);
        if ($validator->fails()) {
            throw new store_exception('Could not update reply.', $validator->errors());
        } else {
            $updata['reply'] = $inputs['reply'];

            $res = Reply::where('review_reply_id', $inputs['review_reply_id'])->where('reply_by', $user_id)
                    ->update($updata);
            if ($res != FALSE) {
                return response()->json(['error' => '', 'message' => 'Reply updated successfully'], 200);
            } else {
                return response()->json(['error' => '', 'message' => 'Could not update Reply'], 422);
            }
        }
    }

    public function add_review_reply() {
        $inputs = json_decode(file_get_contents("php://input"), true);
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->user_id;
        $rules = array(
            'review_id' => 'required',
            'reply' => 'required'
        );

        $validator = app('validator')->make($inputs, $rules);
        if ($validator->fails()) {
            throw new store_exception('Could not add reply.', $validator->errors());
        } else {
            $inputs['reply_by'] = $user_id;
        }

        $res = Reply::create($inputs);
        if ($res != FALSE) {
            return response()->json(['error' => '', 'message' => 'Reply add successfully'], 200);
        } else {
            return response()->json(['error' => '', 'message' => 'Could not add review'], 422);
        }
    }

    public function add_rating() {
        $inputs = json_decode(file_get_contents("php://input"), true);
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->user_id;
        $rules = array(
            'event_id' => 'required',
            'rating' => 'required'
        );

        $validator = app('validator')->make($inputs, $rules);
        if ($validator->fails()) {
            throw new store_exception('Could not add rating.', $validator->errors());
        } else {
            $inputs['user_id'] = $user_id;
        }

        $res = Rating::create($inputs);
        if ($res != FALSE) {
            return response()->json(['error' => '', 'message' => 'Rating add successfully'], 200);
        } else {
            return response()->json(['error' => '', 'message' => 'Could not add rating'], 422);
        }
    }

    public function getUserDetail($userId) {

        $user_detail = User::select(['first_name', 'last_name', 'gm_images.name as image', 'gm_images.title as title'])->where('user_id', $userId)->leftJoin('gm_images', 'users.profile_image', '=', 'gm_images.image_id')->get();
        if ($user_detail->count() > 0) {
            $user_detail = $user_detail[0];
            if ($user_detail->image != '') {
                $user_detail->image_url = asset('geekmeet/uploads/user/' . $user_detail->image);
            } else {
                $user_detail->image_url = asset('geekmeet/uploads/user/user-avatar.png');
                $user_detail->title = 'user-avatar';
            }
            if (!empty($user_detail)) {
                return $user_detail;
            } else {
                return $user_detail;
            }
        } else {
            return $user_detail;
        }
    }

    public function getReview($event_id) {
        $review_arr = Review::where('event_id', $event_id)->orderBy('created_at', 'desc')->get();
        if (!empty($review_arr)) {
            foreach ($review_arr as $review_key => $review_val) {
                $review_arr[$review_key]->user_detail = $this->getUserDetail($review_val->review_given_by);
                $review_arr[$review_key]->reply = $this->getReply($review_val->event_review_id);
                $date_obj = date_create($review_val->created_at);
                $review_arr[$review_key]->date = date_format($date_obj, 'M j, Y');
                $review_arr[$review_key]->time = date_format($date_obj, 'h:i A');
            }
            $data = array(
                'review' => $review_arr,
                'review_count' => count($review_arr),
            );
            return response()->json(['error' => '', 'message' => '', 'data' => $data], 200);
        } else {
            return response()->json(['error' => '', 'message' => 'No Data Found', 'data' => $data], 200);
        }
    }

    public function getReply($review_id) {
        $replyarr = Reply::where('review_id', $review_id)->orderBy('created_at', 'asc')->get();
        foreach ($replyarr as $reply_key => $reply_val) {
            $replyarr[$reply_key]->user_detail = $this->getUserDetail($reply_val->reply_by);
            $date_obj = date_create($reply_val->created_at);
            $replyarr[$reply_key]->date = date_format($date_obj, 'M j, Y');
            $replyarr[$reply_key]->time = date_format($date_obj, 'h:i A');
        }
        return $replyarr;
    }

    public function organization_detail($org_id) {
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->user_id;
        $organization = Organization::select(['organization_id', 'gm_organizations.user_id', 'organization_name', 'organization_description', 'organization_location', 'organization_email', 'city', 'state', 'country', 'organization_website', 'organization_contact', 'gm_images.name as organization_logo', 'gm_images.title as title'])->where('user_id', $user_id)->where('organization_id', $org_id)->leftJoin('gm_images', 'gm_organizations.organization_logo', '=', 'gm_images.image_id')->orderBy('organization_id', 'desc')->get();
        if ($organization->count() > 0) {
            $organization = $organization[0];
            if ($organization->organization_logo != '') {
                $organization->organization_logo = asset('geekmeet/uploads/user/' . $organization->organization_logo);
            } else {
                $organization->organization_logo = asset('geekmeet/uploads/no-image-available.jpg');
                $organization->title = 'organization_logo';
            }

            if (!empty($organization)) {
                return response()->json(['error' => '', 'message' => '', 'data' => $organization], 200);
            } else {
                return response()->json(['error' => '', 'message' => 'No Data Found', 'data' => $organization], 200);
            }
        } else {
            return response()->json(['error' => '', 'message' => 'No Data Found', 'data' => $organization], 200);
        }
    }

    public function organization_list() {
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->user_id;
        $organization = Organization::select(['organization_id', 'organization_name'])->where('user_id', $user_id)->orderBy('organization_id', 'desc')->get();
        if (!empty($organization)) {
            return response()->json(['error' => '', 'message' => '', 'data' => $organization], 200);
        } else {
            return response()->json(['error' => '', 'message' => 'No Data Found'], 200);
        }
    }

    public function storeOrganization() {
        $inputs = json_decode(file_get_contents("php://input"), true);
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->user_id;

        $rules = array(
            'organization_name' => 'required',
            'organization_description' => 'required',
        );

        $validator = app('validator')->make($inputs, $rules);
        if ($validator->fails()) {
            throw new store_exception('Could not add organization.', $validator->errors());
        } else {

            if ($inputs['image'] != '') {
                $strFileName = 'organization_' . time() . '.jpg';
                $path = base_path() . '/uploads/user/' . $strFileName;
                $image_file = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $inputs['image']));

                if (file_put_contents($path, $image_file)) {
                    $image_data = array(
                        'name' => $strFileName,
                        'type' => 'user',
                    );
                    $image = Image::create($image_data);
                    if (!empty($image)) {
                        $inputs['organization_logo'] = $image->image_id;
                    }
                }
            }
            $inputs['user_id'] = $user_id;
            $res = Organization::create($inputs);
            if ($res != FALSE) {
                return response()->json(['error' => '', 'message' => 'Organization add successfully'], 200);
            } else {
                return response()->json(['error' => '', 'message' => 'Could not add organization'], 422);
            }
        }
    }

    public function updateOrganization() {

        $inputs = json_decode(file_get_contents("php://input"), true);

        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->user_id;
        $organization_id = $inputs['organization_id'];
        $organization_detail = Organization::find($organization_id);
        $rules = array(
            'organization_name' => 'required',
            'organization_description' => 'required',
            'organization_id' => 'required',
        );

        $validator = app('validator')->make($inputs, $rules);
        if ($validator->fails()) {
            throw new update_exception('Could not update organization.', $validator->errors());
        } else {

            if ($inputs['image'] != '') {
                $strFileName = 'organization_' . time() . '.jpg';

                $path = base_path() . '/uploads/user/' . $strFileName;
                $image_file = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $inputs['image']));

                if (file_put_contents($path, $image_file)) {
                    $image_data = array(
                        'name' => $strFileName,
                        'type' => 'user',
                    );
                    $image = Image::create($image_data);
                    if (!empty($image)) {
                        $inputs['organization_logo'] = $image->image_id;
                        $this->delete_images($organization_detail['organization_logo'], 'user');
                    }
                }
            } else {
                $inputs['organization_logo'] = $organization_detail['organization_logo'];
            }
            unset($inputs['organization_id']);
            unset($inputs['image']);
            $res = Organization::where('organization_id', $organization_id)->where('user_id', $user_id)
                    ->update($inputs);
            if ($res != FALSE) {
                return response()->json(['error' => '', 'message' => 'Organization update successfully'], 200);
            } else {
                return response()->json(['error' => '', 'message' => 'Could not update organization'], 422);
            }
        }
    }

    public function deleteOrganization($id) {
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->user_id;
        $organization = Organization::find($id);
        if ($organization->organization_logo != '') {
            $image_del = Image::find($organization->organization_logo);
            $path = base_path() . '/uploads/user/';
            if (!empty($image_del)) {
                if (file_exists($path . $image_del->name)) {
                    unlink($path . $image_del->name);
                }
                $image_del->delete();
            }
        }
        if ($organization->delete()) {
            return response()->json(['error' => '', 'message' => 'Organization delete successfully'], 200);
        } else {
            return response()->json(['error' => '', 'message' => 'Could not delete organization'], 422);
        }
    }

    public function venue_list() {
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->user_id;
        $location = Location::select(['venue_id', 'venue_name', 'gm_venue.venue_image', 'gm_venue.user_id', 'gm_cities.name as city', 'users.username as username', 'gm_states.name as state', 'gm_countries.name as country', 'gm_venue.address', 'gm_venue.longitude', 'gm_venue.latitude', 'gm_venue.venue_description'])->leftJoin('gm_cities', 'gm_venue.city', '=', 'gm_cities.city_id')->leftJoin('gm_states', 'gm_venue.state', '=', 'gm_states.state_id')->leftJoin('gm_countries', 'gm_venue.country', '=', 'gm_countries.id')->leftJoin('users', 'gm_venue.user_id', '=', 'users.user_id')->where('gm_venue.user_id', $user_id)->paginate(5);

        if ($location->count() > 0) {
            foreach ($location as $key => $val) {
                $image_arr = array();
                if ($val->venue_image != '') {

                    $img_arr = explode(',', $val->venue_image);


                    foreach ($img_arr as $img_val) {
                        $image_data = Image::where('image_id', $img_val)->get()->toArray();
                        if (!empty($image_data)) {

                            $image_arr[] = array(
                                'image_url' => asset("geekmeet/uploads/events/" . $image_data[0]['name']),
                                'image_title' => $image_data[0]['title']
                            );
                        } else {
                            $image_arr[] = array(
                                'image_url' => asset('geekmeet/uploads/no-image-available.jpg'),
                                'image_title' => 'no-image-available.jpg'
                            );
                        }
                    }
                } else {
                    $image_arr[] = array(
                        'image_url' => asset('geekmeet/uploads/no-image-available.jpg'),
                        'image_title' => 'no-image-available.jpg'
                    );
                }

                $location[$key]['images'] = $image_arr;
            }
            return response()->json(['error' => '', 'message' => '', 'data' => $location], 200);
        } else {
            return response()->json(['error' => '', 'message' => 'No Data Found', 'data' => $location], 200);
        }
    }

    public function venue_detail($venue_id) {
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->user_id;
        $location = Location::select(['venue_id', 'venue_name', 'gm_venue.venue_image', 'gm_venue.user_id', 'gm_cities.name as city_name', 'users.username as username', 'gm_states.name as state_name', 'gm_countries.name as country_name', 'gm_venue.address', 'gm_venue.longitude', 'gm_venue.latitude', 'gm_venue.venue_description', 'gm_venue.country', 'gm_venue.state', 'gm_venue.city'])->leftJoin('gm_cities', 'gm_venue.city', '=', 'gm_cities.city_id')->leftJoin('gm_states', 'gm_venue.state', '=', 'gm_states.state_id')->leftJoin('gm_countries', 'gm_venue.country', '=', 'gm_countries.id')->leftJoin('users', 'gm_venue.user_id', '=', 'users.user_id')->where('gm_venue.user_id', $user_id)->where('gm_venue.venue_id', $venue_id)->get();

        if ($location->count() > 0) {
            $location = $location[0];

            $image_arr = array();
            if ($location->venue_image != '') {

                $img_arr = explode(',', $location->venue_image);


                foreach ($img_arr as $img_val) {
                    $image_data = Image::where('image_id', $img_val)->get()->toArray();
                    if (!empty($image_data)) {

                        $image_arr[] = array(
                            'image_url' => asset("geekmeet/uploads/events/" . $image_data[0]['name']),
                            'image_title' => $image_data[0]['title']
                        );
                    }
                }
            } else {
                $image_arr[] = array(
                    'image_url' => asset('geekmeet/uploads/no-image-available.jpg'),
                    'image_title' => 'no-image-available.jpg'
                );
            }

            $location['images'] = $image_arr;

            return response()->json(['error' => '', 'message' => '', 'data' => $location], 200);
        } else {
            return response()->json(['error' => '', 'message' => 'No Data Found', 'data' => $location], 200);
        }
    }

    public function storeVenue() {

        $inputs = json_decode(file_get_contents("php://input"), true);
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->user_id;
        $rules = array(
            'country' => 'required',
            'state' => 'required',
            'address' => 'required',
            'venue_name' => 'required'
        );

        $validator = app('validator')->make($inputs, $rules);
        if ($validator->fails()) {
            throw new store_exception('Could not add venue.', $validator->errors());
        } else {
            $inputs['user_id'] = $user_id;

            if (!empty($inputs['images'])) {
                $img_arr = array();
                foreach ($inputs['images'] as $img_val) {
                    $strFileName = 'venue_' . time() . '.jpg';
                    $path = base_path() . '/uploads/events/' . $strFileName;
                    $image_file = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $img_val));

                    if (file_put_contents($path, $image_file)) {
                        $image_data = array(
                            'name' => $strFileName,
                            'type' => 'user',
                        );
                        $image = Image::create($image_data);
                        if (!empty($image)) {
                            $img_arr[] = $image->image_id;
                        }
                    }
                }
                if (!empty($img_arr)) {
                    $inputs['venue_image'] = implode(',', $img_arr);
                } else {
                    $inputs['venue_image'] = '';
                }
            }
            unset($inputs['images']);
            $res = Location::create($inputs);
            if ($res != FALSE) {
                return response()->json(['error' => '', 'message' => 'Venue add successfully'], 200);
            } else {
                return response()->json(['error' => '', 'message' => 'Could not add venue'], 422);
            }
        }
    }

    public function updateVenue() {
        $inputs = json_decode(file_get_contents("php://input"), true);
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->user_id;
        $location_id = $inputs['vanue_id'];
        $rules = array(
            'country' => 'required',
            'state' => 'required',
            'address' => 'required',
            'venue_name' => 'required'
        );

        $validator = app('validator')->make($inputs, $rules);
        if ($validator->fails()) {
            throw new update_exception('Could not update venue.', $validator->errors());
        } else {
            unset($inputs['vanue_id']);
            unset($inputs['images']);

            $res = Location::where('venue_id', $location_id)->where('user_id', $user_id)
                    ->update($inputs);
            if ($res != FALSE) {
                return response()->json(['error' => '', 'message' => 'Venue update successfully'], 200);
            } else {
                return response()->json(['error' => '', 'message' => 'Could not update venue'], 422);
            }
        }
    }

    public function deleteVenue($id) {
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->user_id;
        $location = Location::find($id);

        if ($location->venue_image != '') {
            $this->delete_images($location->venue_image, 'events');
        }
        if ($location->delete()) {
            return response()->json(['error' => '', 'message' => 'Venue delete successfully'], 200);
        } else {
            return response()->json(['error' => '', 'message' => 'Could not update venue'], 422);
        }
    }

    public function deleteComment() {
        $inputs = json_decode(file_get_contents("php://input"), true);
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->user_id;
        if ($inputs['type'] == "review") {
            $review = Review::select(['*'])->where('event_review_id', $inputs['delete_id'])->where('review_given_by', $user_id)->get();
            if (!empty($review)) {
                Reply::where('review_id', $review[0]->event_review_id)
                        ->delete();
                Review::where('event_review_id', $inputs['delete_id'])->delete();
                return response()->json(['error' => '', 'message' => 'Review delete successfully'], 200);
            } else {
                return response()->json(['error' => '', 'message' => 'Could not delete review'], 422);
            }
        } elseif ($inputs['type'] == "reply") {
            $res = Reply::where('review_reply_id', $inputs['delete_id'])->where('reply_by', $user_id)
                    ->delete();
            if ($res) {
                return response()->json(['error' => '', 'message' => 'Reply delete successfully'], 200);
            } else {
                return response()->json(['error' => '', 'message' => 'Could not delete reply'], 422);
            }
        } else {
            return response()->json(['error' => '', 'message' => 'something went wrong'], 422);
        }
    }

    public function eventRating($event_id) {
        $rating = Rating::where('event_id', $event_id)->avg('rating');
        return $rating;
    }

    public function visitCount() {
        $inputs = json_decode(file_get_contents("php://input"), true);
        Visit::create($inputs);
        return response()->json(['error' => '', 'message' => ''], 200);
    }

    public function userVisitCount() {
        $inputs = json_decode(file_get_contents("php://input"), true);
        $user = JWTAuth::parseToken()->authenticate();
        $inputs['user_id'] = $user->user_id;
        Visit::create($inputs);
        return response()->json(['error' => '', 'message' => ''], 200);
    }

    public function favorite() {
        $inputs = json_decode(file_get_contents("php://input"), true);
        $user = JWTAuth::parseToken()->authenticate();
        $inputs['user_id'] = $user->user_id;
        $detail = Favorites::where('event_id', $inputs['event_id'])->where('user_id', $user->user_id)->count();
        if ($detail > 0) {
            Favorites::where('event_id', $inputs['event_id'])->where('user_id', $user->user_id)->update(array('favorite' => $inputs['favorite']));
        } else {
            Favorites::create($inputs);
        }
        if ($inputs['favorite'] == 1) {
            return response()->json(['error' => '', 'message' => 'event add to favorite successfully'], 200);
        } else {
            return response()->json(['error' => '', 'message' => 'event remove from favorite successfully'], 200);
        }
    }

    public function get_favorite($event_id) {
        $user = JWTAuth::parseToken()->authenticate();
        $userId = $user->user_id;
        $favorites = Favorites::where('event_id', $event_id)->where('user_id', $userId)->get()->toArray();
        if (!empty($favorites)) {
            return $favorites[0]['favorite'];
        } else {
            return 0;
        }
    }

    public function get_banner($title) {
        $banner = Banner::where('title', $title)->get();
        if (!empty($banner)) {
            return response()->json(['error' => '', 'message' => '', 'data' => $banner], 200);
        } else {
            return response()->json(['error' => '', 'message' => 'No Data Found', 'data' => $banner], 200);
        }
    }

    public function getRelatedEvent($event_id, $category_id, $event_title, $event_description) {
        $event = Event::select(['event_id', 'event_title', 'event_description', 'start_date', 'end_date', 'start_time', 'end_time', 'category_id', 'image_id'])
                        ->where('start_date', '>=', date('Y-m-d', time()))
                        ->where('event_id', '!=', $event_id)
                        ->where(function ($query) use ($category_id, $event_title, $event_description) {
                            $query->orWhere('category_id', 'LIKE', '%' . $category_id . '%');
                            $query->orWhere('event_title', 'LIKE', '%' . $event_title . '%');
                            $query->orWhere('event_description', 'LIKE', '%' . $event_description . '%');
                        })
                        ->orderBy('start_date', 'asc')->paginate(6);



        if ($event->count() > 0) {
            foreach ($event as $key => $val) {
                $date_obj = date_create($val->start_date);
                $event[$key]->start_date = date_format($date_obj, 'd M');
                $date_obj = date_create($val->end_date);
                $event[$key]->end_date = date_format($date_obj, 'd M');
                $string = strip_tags($val->event_description);
                $event[$key]->start_time = date('h:i A', strtotime($val->start_time));
                $event[$key]->end_time = date('h:i A', strtotime($val->end_time));
                $event[$key]->read_more = 0;
                if (strlen($string) > 200) {

                    // truncate string
                    $stringCut = substr($string, 0, 200);

                    // make sure it ends in a word so assassinate doesn't become ass...
                    $string = substr($stringCut, 0, strrpos($stringCut, ' '));
                    $event[$key]->read_more = 1;
                }

                $event[$key]->event_description = $string;

                if ($val->image_id != '') {
                    if (strpos($val->image_id, ',') !== false) {
                        $img_arr = explode(',', $val->image_id);
                    } else {
                        $img_arr = array($val->image_id);
                    }
                    $featurd_arr = array();
                    foreach ($img_arr as $img_val) {
                        $image_data = Image::where('image_id', $img_val)->where('featured_image', 1)->get()->take(1)->toArray();
                        if (!empty($image_data)) {
                            $featurd_arr = $image_data;
                        }
                    }
                    if (count($featurd_arr) > 0) {
                        $event[$key]->featured_image = asset('geekmeet/uploads/events/' . $featurd_arr[0]['name']);
                        $event[$key]->title = $featurd_arr[0]['title'];
                    } else {
                        $event[$key]->featured_image = asset('geekmeet/uploads/no-image-available.jpg');
                        $event[$key]->title = 'default.jpg';
                    }
                } else {
                    $event[$key]->featured_image = asset('geekmeet/uploads/no-image-available.jpg');
                    $event[$key]->title = 'no-image-available.jpg';
                }


                $event[$key]->rating = $this->eventRating($val->event_id);

                $event[$key]->comment_count = Review::where('event_id', $val->event_id)->count();
            }

            return $event;
        } else {
            return $event;
        }
    }

    public function changePassword() {
        $inputs = json_decode(file_get_contents("php://input"), true);
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->user_id;
        $user_data = User::find($user_id);
        $hashed_password = $user_data->password;
        $rules = array(
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
            'old_password' => "required|password_hash_check:$hashed_password",
        );
        $messages = [
            'old_password.password_hash_check' => 'Password did not match to the current password.',
        ];
        $validator = app('validator')->make($inputs, $rules, $messages);

        if ($validator->fails()) {
            throw new update_exception('Could not update paasword.', $validator->errors());
        } else {
            $password = Hash::make($inputs['password']);
            $res = User::where('user_id', $user_id)->update(array('password' => $password));
            if (!empty($res)) {
                return response()->json(['error' => '', 'message' => 'password change successfully'], 200);
            } else {
                return response()->json(['error' => '', 'message' => 'some thing went wrong'], 422);
            }
        }
    }

    public function forgotPassword() {

        $inputs = json_decode(file_get_contents("php://input"), true);
        $rules = array(
            'email' => 'required|email|exists:users'
        );

        $messages = [
            'email.exists' => 'This email is invalid.',
        ];
        $validator = app('validator')->make($inputs, $rules, $messages);

        if ($validator->fails()) {
            throw new update_exception('Could not reset password.', $validator->errors());
        } else {
            $user_detail = User::select('*')->where('email', $inputs['email'])->get();
            $user_detail = $user_detail[0];

            if (!empty($user_detail)) {
                $forget_data = array(
                    'user_id' => $user_detail->user_id,
                    'token' => str_random(20)
                );
                $forget = ForgotToken::insert($forget_data);

                if (!empty($forget)) {
                    $forgot_template = Email::find(2);
                    if ($forgot_template->status == 1) {
                        $hrf = 'reset-password/' . $forget_data['token'];

                        // Set Body
                        if ($forgot_template->body != '') {
                            $content = str_replace('%fullname%', $user_detail->first_name . ' ' . $user_detail->last_name, $forgot_template->body);
                        } else {
                            $content = "<b>Dear, $user_detail->first_name $user_detail->last_name! </b><br>There was recently a request to change the password for your account.<br>if you requested this password change, please click Reset Now Botton.<br>";
                        }
                        $data = array(
                            'content' => $content,
                            'link' => $hrf,
                            'btntitle' => 'Reset Now'
                        );

                        // Set Subject
                        if ($forgot_template->subject != '') {
                            $subject = $forgot_template->subject;
                        } else {
                            $subject = 'Password Reset Request';
                        }


                        // Set From
                        if ($forgot_template->from_name != '') {
                            $from = $forgot_template->from_name;
                        } else {
                            $from = 'GeekMeet';
                        }

                        //  return view('email.email_view',$data);   
//                    $email_id = $inputs['email'];
                        $email_id = 'prateek.jain@ignisitsolutions.com';
                        $status = Mail::send('email.email_view', $data, function($message) use($email_id, $subject, $from) {
                                    $message->to($email_id)->subject($subject);
                                    $message->from('support@geekmeet.com', $from);
                                });

                        if ($status) {
                            return response()->json(['error' => '', 'message' => 'Reset password link send you email.'], 200);
                        } else {
                            return response()->json(['error' => '', 'message' => 'some thing went wrong'], 200);
                        }
                    } else {
                        return response()->json(['error' => '', 'message' => 'some thing went wrong'], 200);
                    }
                }
            }
        }
    }

    public function checkForgotToken($token) {
        if ($token != '') {
            $forget = ForgotToken::select('*')->where('token', $token)->get()->toArray();
            if (!empty($forget)) {
                $forget = $forget[0];
                $time2 = $forget['created_at'];
                $time1 = date("Y-m-d H:i:s");
                $diff = strtotime($time1) - strtotime($time2);
                $diff_in_hrs = round($diff / 3600);
                if ($diff_in_hrs <= 12) {
                    return response()->json(['error' => '', 'message' => ''], 200);
                } else {
                    return response()->json(['error' => '', 'message' => 'Reset password link has been expired'], 422);
                }
            } else {
                return response()->json(['error' => '', 'message' => 'Reset password link has been expired'], 422);
            }
        }
    }

    public function resetPassword() {
        $inputs = json_decode(file_get_contents("php://input"), true);
        $rules = array(
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
            'token' => 'required',
        );
        $validator = app('validator')->make($inputs, $rules);

        if ($validator->fails()) {
            throw new update_exception('Could not reset paasword.', $validator->errors());
        } else {
            $forget = ForgotToken::select('*')->where('token', $inputs['token'])->get();
            if (!empty($forget)) {
                $user_id = $forget[0]->user_id;
                $password = Hash::make($inputs['password']);

                $res = User::where('user_id', $user_id)->update(array('password' => $password));
                if (!empty($res)) {
                    ForgotToken::where('token', $inputs['token'])->delete();
                    return response()->json(['error' => '', 'message' => 'password reset successfully'], 200);
                } else {
                    return response()->json(['error' => '', 'message' => 'some thing went wrong'], 422);
                }
            } else {
                return response()->json(['error' => '', 'message' => 'Reset password link has been expired'], 422);
            }
        }
    }

    public function contact_us() {
        $inputs = json_decode(file_get_contents("php://input"), true);
        $rules = array(
            'name' => 'required',
            'email' => 'required|email|max:255',
            'subject' => 'required',
            'message' => 'required|min:100'
        );

        $validator = app('validator')->make($inputs, $rules);
        if ($validator->fails()) {
            throw new store_exception('Could not add information.', $validator->errors());
        } else {
            $res = ContactUs::create($inputs);
            if ($res != FALSE) {
                return response()->json(['error' => '', 'message' => 'Information send successfully'], 200);
            } else {
                return response()->json(['error' => '', 'message' => 'Could not add information'], 422);
            }
        }
    }

    public function upload_image(Request $request) {

        if ($request->file('images') != NULL) {
            $imageTempName = $request->file('images')->getPathname();
            $imageName = str_random(10) . '_' . $request->file('images')->getClientOriginalName();
            $path = base_path() . "/uploads/events/";
            $request->file('images')->move($path, $imageName);
            $image_data = array(
                'title' => $request->file('images')->getClientOriginalName(),
                'name' => $imageName,
                'type' => 'event',
                'extension' => $request->file('images')->getClientOriginalExtension(),
                'content_type' => $request->file('images')->getClientMimeType()
            );

            $image = Image::create($image_data);
            echo $image->image_id;
            die;
        }
    }

    public function remove_image(Request $request) {

        if ($request->input('id') != NULL) {
            if (strpos($request->input('id'), ',') > 0) {
                $ids = explode(',', $request->input('id'));
                foreach ($ids as $val) {
                    $image_del = Image::find($val);
                    $path = base_path() . '/uploads/events/';
                    if (!empty($image_del)) {
                        if (file_exists($path . $image_del->name)) {
                            unlink($path . $image_del->name);
                        }
                        $image_del->delete();
                    }
                }
            } else {
                $image_del = Image::find($request->input('id'));
                $path = base_path() . '/uploads/events/';
                if (!empty($image_del)) {
                    if (file_exists($path . $image_del->name)) {
                        unlink($path . $image_del->name);
                    }
                    $image_del->delete();
                }
            }
            echo json_encode(array("status" => 1));
        }
    }

    public function edit_event_detail($id) {
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->user_id;
        $event = Event::select(['*', 'gm_venue.address as location_address', 'gm_venue.venue_id as location_id'])->leftJoin('gm_venue', 'gm_events.location', '=', 'gm_venue.venue_id')->where('event_id', $id)->where('event_organize_by', $user_id)->get();

        if ($event->count() > 0) {
            $event = $event[0];
            $event->images = array();

            $event->attachment = array();
            if ($event->image_id != '') {
                $image_data = array();
                $images = explode(',', $event->image_id);
                $attch_info = array();

                foreach ($images as $image_val) {
                    $images_array = Image::find($image_val);
                    if (!empty($images_array)) {
                        $src = asset('geekmeet/uploads/events/' . $images_array->name);
                        $title = $images_array->title;
                        $alt = $images_array->title;
                        $image_data[] = '<img img_id = "' . $images_array->image_id . '" width="200px" featured_image="' . $images_array->featured_image . '" height="200px" src="' . $src . '" class="img-responsive" alt="' . $alt . '" title="' . $title . '">';
                        $attch_info[] = array(
                            'url' => 'http://2016.geekmeet.com/admin/v1/delete_per_image',
                            'caption' => $title,
                            'key' => $images_array->image_id,
                        );
                    }
                }


                $event->images = $image_data;

                $event->attachment = json_encode($attch_info);
            }

            if ($event->audience != '') {
                $event->audience = explode(',', $event->audience);
            }
            if ($event->category_id != '') {
                $event->category_id = explode(',', $event->category_id);
            }
            return response()->json(['error' => '', 'message' => '', 'data' => $event], 200);
        } else {
            return response()->json(['error' => '', 'message' => 'event not exist'], 422);
        }
    }

    public function update_event() {
        $inputs = json_decode(file_get_contents("php://input"), true);
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->user_id;
        $event_id = $inputs['event_id'];
        unset($inputs['image']);
        $rules = array(
            'event_title' => 'required|max:255',
            'event_description' => 'required',
            'start_date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'website' => 'required',
            'location' => 'required',
        );
        if ($inputs['start_date'] != date('d-m-Y', time())) {
            $rules['end_date'] = "required";
        }
        $validator = app('validator')->make($inputs, $rules);
        if ($validator->fails()) {
            throw new update_exception('Could not update event.', $validator->errors());
        } else {
            if ($inputs['keyword'] != '') {
                $keyword = explode(',', $inputs['keyword']);
                foreach ($keyword as $cat) {
                    $cate_res = Category::where('category_name', $cat)->get();


                    if ($cate_res->isEmpty()) {
                        $category = new Category;
                        $category->category_name = $cat;
                        $category->category_status = 1;
                        $category->save();
                    }
                }
                $inputs['category_id'] = $inputs['keyword'];
            }

            $inputs['start_date'] = date('Y-m-d', strtotime($inputs['start_date']));
            if ($inputs['start_date'] != date('d-m-Y', time())) {
                $inputs['end_date'] = date('Y-m-d', strtotime($inputs['end_date']));
            } else {
                $inputs['end_date'] = date('Y-m-d', time());
            }
            unset($inputs['keyword']);
            unset($inputs['images']);
            $res = Event::where('event_id', $event_id)->where('event_organize_by', $user_id)
                    ->update($inputs);
            if ($res) {
                return response()->json(['error' => '', 'message' => 'Event update successfully'], 200);
            } else {
                return response()->json(['error' => '', 'message' => 'Could not update event detail'], 422);
            }
        }
    }

    public function delete_event($id) {
        $event = Event::find($id);
        if ($event->image_id != '') {
            $this->delete_images($event->image_id, 'events');
        }
        if ($event->delete()) {
            return response()->json(['error' => '', 'message' => 'Event delete successfully'], 200);
        } else {
            return response()->json(['error' => '', 'message' => 'Could not delete this event'], 422);
        }
    }

    public function sendToFriend() {
        $inputs = json_decode(file_get_contents("php://input"), true);
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->user_id;
        $rules = array(
            'sender_email' => 'required|email'
        );

        $validator = app('validator')->make($inputs, $rules);

        if ($validator->fails()) {
            throw new store_exception('Could not send event.', $validator->errors());
        } else {
            $user_detail = User::find($user_id);
            $event = Event::select(['*', 'gm_venue.address as location_address', 'gm_venue.venue_id as location_id'])->leftJoin('gm_venue', 'gm_events.location', '=', 'gm_venue.venue_id')->leftJoin('users', 'gm_events.event_organize_by', '=', 'users.user_id')->where('event_id', $inputs['event_id'])->get();
            if ($event->count() > 0) {
                $event = $event[0];
                $date_obj = date_create($event->start_date);
                $event->start_date = date_format($date_obj, 'd M ,Y');
                $event->start_time = date('h:i A', strtotime($event->start_time));
                $event->end_time = date('h:i A', strtotime($event->end_time));
                $date_obj = date_create($event->end_date);
                $event->end_date = date_format($date_obj, 'd M ,Y');
                if ($event->org_name != '') {
                    $event->organizer = $event->org_name;
                } else {
                    $event->organizer = $event->first_name . ' ' . $event->last_name;
                }
                if ($event->audience != '') {
                    $event->audience = 'All';
                }
                $image_arr = array();
                if ($event->image_id != '') {
                    if (strpos($event->image_id, ',') !== false) {
                        $img_arr = explode(',', $event->image_id);
                    } else {
                        $img_arr = array($event->image_id);
                    }
                    $featurd_arr = array();
                    foreach ($img_arr as $img_val) {
                        $image_data = Image::where('image_id', $img_val)->where('featured_image', 1)->get()->take(1)->toArray();
                        if (!empty($image_data)) {
                            $featurd_arr = $image_data;
                        }
                    }

                    if (count($featurd_arr) > 0) {
                        $event->featured_image = asset('geekmeet/uploads/events/' . $featurd_arr[0]['name']);
                        $event->title = $featurd_arr[0]['title'];
                    } else {
                        $event->featured_image = asset('geekmeet/uploads/no-image-available.jpg');
                        $event->title = 'default.jpg';
                    }
                } else {
                    $event->featured_image = asset('geekmeet/uploads/no-image-available.jpg');
                    $event->title = 'no-image-available.jpg';
                }

                $email_id = $inputs['sender_email'];
                $username = $user_detail->first_name . ' ' . $user_detail->last_name;
                $event_title = $event->event_title;
                $data['event'] = $event;
                $status = Mail::send('email.event_template', $data, function($message) use($email_id, $username, $event_title) {
                            $message->to($email_id)->subject("$username invite you to $event_title event");
                            $message->from('support@geekmeet.com', 'GeekMeet');
                        });

                if ($status) {
                    return response()->json(['error' => '', 'message' => 'Event sent successfully.'], 200);
                } else {
                    return response()->json(['error' => '', 'message' => 'some thing went wrong'], 200);
                }
            } else {
                return response()->json(['error' => '', 'message' => 'some thing went wrong'], 200);
            }
        }
    }

    public function add_attendance() {
        $inputs = json_decode(file_get_contents("php://input"), true);
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->user_id;
        $data = array(
            'user_id' => $user_id,
            'event_id' => $inputs['event_id'],
        );

        $res = Attendance::create($data);
        if ($res) {
            return response()->json(['error' => '', 'message' => ''], 200);
        } else {
            return response()->json(['error' => '', 'message' => 'some thing went wrong'], 200);
        }
    }

    public function addCalender($id) {
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->user_id;
        $event = Event::select(['event_id', 'contact_info', 'website', 'audience', 'contact_info', 'event_title', 'event_description', 'start_date', 'end_date', 'start_time', 'gm_events.image_id as event_images', 'end_time', 'gm_venue.address as location', 'category_id', 'users.username', 'users.first_name', 'users.last_name', 'users.email', 'gm_venue.venue_name'])->leftJoin('gm_venue', 'gm_events.location', '=', 'gm_venue.venue_id')->leftJoin('users', 'gm_events.event_organize_by', '=', 'users.user_id')->where('event_id', $id)->get();

        if ($event->count() > 0) {
            $event = $event[0];
            $startinputDate = "$event->start_date $event->start_time";
//          
            $startdatetime = date('Y-m-d\TH:i:sP', strtotime($startinputDate));
            $endinputDate = "$event->end_date $event->end_time";
            $enddatetime = date("Y-m-d\TH:i:sP", strtotime($endinputDate));
            $event->start_datetime = $startdatetime;
            $event->end_datetime = $enddatetime;
            $date_obj = date_create($event->start_date);
            $event->start_date = date_format($date_obj, 'd M ,Y');
            $event->start_time = date('h:i A', strtotime($event->start_time));
            $event->end_time = date('h:i A', strtotime($event->end_time));
            $date_obj = date_create($event->end_date);
            $event->end_date = date_format($date_obj, 'd M ,Y');
            $event->create_date = date("Y-m-d\TH:i:sP", time());

            $string = strip_tags($event->event_description);
            if ($event->org_name != '') {
                $event->organizer = $event->org_name;
            } else {
                $event->organizer = $event->first_name . ' ' . $event->last_name;
            }
        }
        $user_detail = User::find($user_id);
        $email = $user_detail->email;
//        $email = "dheerendrachouhan@gmail.com";
        $cal_uid = date('Ymd') . 'T' . date('His') . "-" . rand() . "@mydomain.com";


        $subject = "$event->event_title add to your calender";
        $headers = "";
        $headers .= "MIME-Version: 1.0\n";
        $headers .= "Content-Type: text/calendar; method=REQUEST;\n";
        $headers .= '        charset="UTF-8"';
        $headers .= "\n";
        $headers .= "Content-Transfer-Encoding: 7bit";

        //Create Email Body (HTML)
        $message = '';
//        $message .= 'BEGIN:VCALENDAR
//PRODID:-//Microsoft Corporation//Outlook 15.0 MIMEDIR//EN
//VERSION:2.0
//METHOD:REQUEST
//BEGIN:VTIMEZONE
//TZID:India Standard Time
//BEGIN:STANDARD
//DTSTART:'.$event->start_datetime.'
//TZOFFSETFROM:+0530
//TZOFFSETTO:+0530
//END:STANDARD
//END:VTIMEZONE
//BEGIN:VEVENT
//CLASS:PUBLIC
//CREATED:'.$event->create_date.'
//DESCRIPTION:Test
//DTEND;TZID="India Standard Time":'.$event->end_datetime.'
//DTSTART;TZID="India Standard Time":'.$event->start_datetime.'
//LOCATION:'.$event->location.'
//ORGANIZER;CN="Prateek Jain":mailto:prateek.jain@ignisitsolutions.com
//PRIORITY:5
//SEQUENCE:0
//SUMMARY;LANGUAGE=en-us:'.$event->event_title.'
//TRANSP:OPAQUE
//UID:'.$cal_uid.'
//X-MICROSOFT-CDO-BUSYSTATUS:TENTATIVE
//X-MICROSOFT-CDO-IMPORTANCE:1
//X-MICROSOFT-CDO-INTENDEDSTATUS:BUSY
//X-MICROSOFT-DISALLOW-COUNTER:FALSE
//X-MS-OLK-AUTOSTARTCHECK:FALSE
//X-MS-OLK-CONFTYPE:0
//BEGIN:VALARM
//TRIGGER:-PT15M
//ACTION:DISPLAY
//DESCRIPTION:Reminder
//END:VALARM
//END:VEVENT
//END:VCALENDAR';



        $description = str_replace("'", "", $event->event_description);
        $description = html_entity_decode($description, ENT_QUOTES, 'UTF-8');
        $subject = str_replace("'", "", $subject);
        $subject = html_entity_decode($subject, ENT_QUOTES, 'UTF-8');
        $message = 'BEGIN:VCALENDAR
PRODID:-//Microsoft Corporation//Outlook 14.0 MIMEDIR//EN
VERSION:2.0
METHOD:PUBLISH
BEGIN:VEVENT
DESCRIPTION:' . $description . '
DTEND:' . $event->end_datetime . '
DTSTART:' . $event->start_datetime . '
LOCATION:' . $event->location . '
ORGANIZER;CN="' . $event->first_name . ' ' . $event->last_name . '":mailto:' . $event->email . '
PRIORITY:5
SUMMARY;LANGUAGE=en-us:' . $event->event_title . '
UID:' . $cal_uid . '
X-ALT-DESC;FMTTYPE=text/html:<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2//E
N"><html>asdasdads</html>\n
BEGIN:VALARM
TRIGGER:-PT15M
ACTION:DISPLAY
DESCRIPTION:Reminder
END:VALARM
END:VEVENT
END:VCALENDAR';
        $mail_sent = mail($email, $subject, $message, $headers);
        if ($mail_sent) {
            return response()->json(['error' => '', 'message' => 'Event successfully add to your calender'], 200);
        } else {
            return response()->json(['error' => '', 'message' => 'some thing went wrong'], 200);
        }
    }

    public function print_event($id) {
        $event = Event::select(['*', 'gm_venue.address as location_address', 'gm_venue.venue_id as location_id'])->leftJoin('gm_venue', 'gm_events.location', '=', 'gm_venue.venue_id')->leftJoin('users', 'gm_events.event_organize_by', '=', 'users.user_id')->where('event_id', $id)->get();
        if ($event->count() > 0) {
            $event = $event[0];
            $date_obj = date_create($event->start_date);
            $event->start_date = date_format($date_obj, 'd M ,Y');
            $event->start_time = date('h:i A', strtotime($event->start_time));
            $event->end_time = date('h:i A', strtotime($event->end_time));
            $date_obj = date_create($event->end_date);
            $event->end_date = date_format($date_obj, 'd M ,Y');
            if ($event->org_name != '') {
                $event->organizer = $event->org_name;
            } else {
                $event->organizer = $event->first_name . ' ' . $event->last_name;
            }
            if ($event->audience != '') {
                $event->audience = 'All';
            }
            $image_arr = array();
            if ($event->image_id != '') {
                if (strpos($event->image_id, ',') !== false) {
                    $img_arr = explode(',', $event->image_id);
                } else {
                    $img_arr = array($event->image_id);
                }
                $featurd_arr = array();
                foreach ($img_arr as $img_val) {
                    $image_data = Image::where('image_id', $img_val)->where('featured_image', 1)->get()->take(1)->toArray();
                    if (!empty($image_data)) {
                        $featurd_arr = $image_data;
                    }
                }

                if (count($featurd_arr) > 0) {
                    $event->featured_image = asset('geekmeet/uploads/events/' . $featurd_arr[0]['name']);
                    $event->title = $featurd_arr[0]['title'];
                } else {
                    $event->featured_image = asset('geekmeet/uploads/no-image-available.jpg');
                    $event->title = 'default.jpg';
                }
            } else {
                $event->featured_image = asset('geekmeet/uploads/no-image-available.jpg');
                $event->title = 'no-image-available.jpg';
            }

            $data['event'] = $event;
            $html = view('email.event_template', $data);
            $contents = $this->array_utf8_encode($html->render());

            if ($contents != '') {
                return response()->json(['error' => '', 'message' => '', 'data' => $contents], 200);
            } else {
                return response()->json(['error' => '', 'message' => 'some thing went wrong'], 200);
            }
        } else {
            return response()->json(['error' => '', 'message' => 'some thing went wrong'], 200);
        }
    }

    public function array_utf8_encode($dat) {
        if (is_string($dat))
            return utf8_encode($dat);
        if (!is_array($dat))
            return $dat;
        $ret = array();
        foreach ($dat as $i => $d)
            $ret[$i] = self::array_utf8_encode($d);
        return $ret;
    }

    function getLnt($zip) {
        $url = "http://maps.googleapis.com/maps/api/geocode/json?address=
" . urlencode($zip) . "&sensor=false";
        $result_string = file_get_contents($url);
        $result = json_decode($result_string, true);
        $result1[] = $result['results'][0];
        $result2[] = $result1[0]['geometry'];
        $result3[] = $result2[0]['location'];
        return $result3[0];
    }

    public function edit_venue_detail($location_id) {

        $location = Location::find($location_id);

        $attch_data = array();
        $attch_info = '';
        $location->images = array();
        $location->attachment = array();
        if ($location->venue_image != '') {
            $image_data = array();

            $images = explode(',', $location->venue_image);

            $attch_info = array();
            foreach ($images as $image_val) {
                $images_array = Image::find($image_val);
//                    var_dump($images_array);

                if (!empty($images_array)) {

                    $path = base_path() . '/uploads/events/' . $images_array->name;

                    if (file_exists($path)) {

                        $type = explode('/', $images_array->content_type);

                        switch ($type[0]) {
                            case 'image':
                                $src = asset('geekmeet/uploads/events/' . $images_array->name);
                                $title = $images_array->title;
                                $alt = $images_array->title;
                                $attch_data[] = '<img img_id = "' . $images_array->image_id . '" width="200px" featured_image="' . $images_array->featured_image . '" height="200px" src="' . $src . '" class="img-responsive" alt="' . $alt . '" title="' . $title . '">';
                                $attch_info[] = array(
                                    'url' => 'http://2016.geekmeet.com/admin/v1/delete_per_image',
                                    'caption' => $images_array->title,
                                    'key' => $images_array->image_id,
                                );
                                break;

                            case 'video':
                                $src = asset('geekmeet/uploads/events/' . $images_array->name);
                                $attch_data[] = '<video width="auto" height="160" controls> <source src="' . $src . '"></video>';
                                $attch_info[] = array(
                                    'url' => 'http://2016.geekmeet.com/admin/v1/delete_per_image',
                                    'caption' => $images_array->title,
                                    'key' => $images_array->image_id,
                                );
                                break;
                            case 'audio':
                                $src = asset('geekmeet/uploads/events/' . $images_array->name);
                                $attch_data[] = '<audio controls width="auto"> <source src="' . $src . '"></audio>';
                                $attch_info[] = array(
                                    'url' => 'http://2016.geekmeet.com/admin/v1/delete_per_image',
                                    'caption' => $images_array->title,
                                    'key' => $images_array->image_id,
                                );
                                break;
                        }
                    }
                }


                $location->images = $attch_data;
                $location->attachment = json_encode($attch_info);
            }
        }

        if ($location != '') {
            return response()->json(['error' => '', 'message' => '', 'data' => $location], 200);
        } else {
            return response()->json(['error' => '', 'message' => 'venue not found'], 200);
        }
    }

    public function search() {
        $inputs = json_decode(file_get_contents("php://input"), true);
        $search = $inputs['search'];

        if ($search != '') {

            $event = DB::table('gm_events')->select(DB::raw('event_id,event_title,event_description,start_date,end_date,start_time,image_id,end_time,category_id'))
                            ->where(function ($query) use ($search) {
                                $query->orWhere('category_id', 'LIKE', '%' . $search . '%');
                                $query->orWhere('event_title', 'LIKE', '%' . $search . '%');
                                return $query->orWhere('event_description', 'LIKE', '%' . $search . '%');
                            })
                            ->orderBy('start_date', 'asc')->get();


            if (!empty($event)) {
                foreach ($event as $key => $val) {
                    $date_obj = date_create($val->start_date);
                    $event[$key]->start_date = date_format($date_obj, 'd M');
                    $date_obj = date_create($val->end_date);
                    $event[$key]->end_date = date_format($date_obj, 'd M');
                    $event[$key]->start_time = date('h:i A', strtotime($val->start_time));
                    $event[$key]->end_time = date('h:i A', strtotime($val->end_time));
                    $event[$key]->readmore = 0;
                    $string = strip_tags($val->event_description);
                    if (strlen($string) > 200) {

                        // truncate string
                        $stringCut = substr($string, 0, 200);

                        // make sure it ends in a word so assassinate doesn't become ass...
                        $string = substr($stringCut, 0, strrpos($stringCut, ' '));

                        $event[$key]->readmore = 1;
                    }

                    $event[$key]->event_description = $string;
                    $event[$key]->owner = 0;



                    if ($val->image_id != '') {
                        if (strpos($val->image_id, ',') !== false) {
                            $img_arr = explode(',', $val->image_id);
                        } else {
                            $img_arr = array($val->image_id);
                        }
                        $featurd_arr = array();
                        foreach ($img_arr as $img_val) {
                            $image_data = Image::where('image_id', $img_val)->where('featured_image', 1)->get()->take(1)->toArray();
                            if (!empty($image_data)) {
                                $featurd_arr = $image_data;
                            }
                        }

                        if (count($featurd_arr) > 0) {
                            $event[$key]->featured_image = asset('geekmeet/uploads/events/' . $featurd_arr[0]['name']);
                            $event[$key]->title = $featurd_arr[0]['title'];
                        } else {
                            $event[$key]->featured_image = asset('geekmeet/uploads/no-image-available.jpg');
                            $event[$key]->title = 'default.jpg';
                        }
                    } else {
                        $event[$key]->featured_image = asset('geekmeet/uploads/no-image-available.jpg');
                        $event[$key]->title = 'no-image-available.jpg';
                    }

                    $event[$key]->rating = $this->eventRating($val->event_id);
                    $event[$key]->comment_count = Review::where('event_id', $val->event_id)->count();
                }
            }





            $blog = DB::table('gm_blogs')->select(DB::raw('blog_id,blog_title,blog_content,gm_blogs.status,gm_blogs.published_date as create_date,gm_images.name as thumb,users.username,title'))->leftJoin('users', 'gm_blogs.added_by', '=', 'users.user_id')->leftJoin('gm_images', 'gm_blogs.thumb', '=', 'gm_images.image_id')
                            ->where(function ($query) use ($search) {
                                $query->orWhere('blog_category', 'LIKE', '%' . $search . '%');
                                $query->orWhere('blog_title', 'LIKE', '%' . $search . '%');
                                return $query->orWhere('blog_content', 'LIKE', '%' . $search . '%');
                            })
                            ->orderBy('published_date', 'asc')->get();


            if (!empty($blog)) {
                foreach ($blog as $key => $val) {
                    $date_obj = date_create($val->create_date);
                    $blog[$key]->create_date = date_format($date_obj, 'F j, Y');
                    $blog[$key]->blog_content = $val->blog_content;
                    $blog[$key]->read_more = 0;

                    $string = strip_tags($val->blog_content);

                    if (strlen($string) > 200) {

                        // truncate string
                        $blog[$key]->read_more = 1;
                        $stringCut = substr($string, 0, 200);

                        // make sure it ends in a word so assassinate doesn't become ass...
                        $string = substr($stringCut, 0, strrpos($stringCut, ' '));
                    }

                    $blog[$key]->blog_content = $string;
                    if ($val->thumb != '') {
                        $blog[$key]->thumb = asset('geekmeet/uploads/blog/' . $val->thumb);
                    } else {
                        $blog[$key]->thumb = asset('geekmeet/uploads/no-image-available.jpg');
                        $blog[$key]->title = 'no-image-available.jpg';
                    }
                }
            }
            
        $data = array(
            'event' => $event,
            'blog' => $blog,
            'organization' => array(),
            'venue' => array()
        );
                            
        return response()->json(['error' => '', 'message' => '', 'data' => $data], 200);
        }else{
         return response()->json(['error' => '', 'message' => 'No data found'], 422);
        }

    }

    public function search_for_user() {
        $inputs = json_decode(file_get_contents("php://input"), true);
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->user_id;

        $search = $inputs['search'];

        if ($search != '') {
            // Get Event
            $event = DB::table('gm_events')->select(DB::raw('event_id,   contact_info,website,audience,contact_info,event_title,event_description,start_date,end_date,event_organize_by,start_time,image_id,end_time,gm_venue.address as location,gm_venue.venue_name,category_id'))->leftJoin('gm_venue', 'gm_events.location', '=', 'gm_venue.venue_id')
                            ->where(function ($query) use ($search) {
                                $query->orWhere('category_id', 'LIKE', '%' . $search . '%');
                                $query->orWhere('event_title', 'LIKE', '%' . $search . '%');
                                return $query->orWhere('event_description', 'LIKE', '%' . $search . '%');
                            })
                            ->orderBy('start_date', 'asc')->get();


            if (!empty($event)) {
                foreach ($event as $key => $val) {
                    $date_obj = date_create($val->start_date);
                    $event[$key]->start_date = date_format($date_obj, 'd M');
                    $date_obj = date_create($val->end_date);
                    $event[$key]->end_date = date_format($date_obj, 'd M');
                    $event[$key]->start_time = date('h:i A', strtotime($val->start_time));
                    $event[$key]->end_time = date('h:i A', strtotime($val->end_time));
                    $event[$key]->readmore = 0;
                    $string = strip_tags($val->event_description);
                    if (strlen($string) > 200) {

                        // truncate string
                        $stringCut = substr($string, 0, 200);

                        // make sure it ends in a word so assassinate doesn't become ass...
                        $string = substr($stringCut, 0, strrpos($stringCut, ' '));

                        $event[$key]->readmore = 1;
                    }

                    $event[$key]->event_description = $string;



                    if ($val->image_id != '') {
                        if (strpos($val->image_id, ',') !== false) {
                            $img_arr = explode(',', $val->image_id);
                        } else {
                            $img_arr = array($val->image_id);
                        }
                        $featurd_arr = array();
                        foreach ($img_arr as $img_val) {
                            $image_data = Image::where('image_id', $img_val)->where('featured_image', 1)->get()->take(1)->toArray();
                            if (!empty($image_data)) {
                                $featurd_arr = $image_data;
                            }
                        }

                        if (count($featurd_arr) > 0) {
                            $event[$key]->featured_image = asset('geekmeet/uploads/events/' . $featurd_arr[0]['name']);
                            $event[$key]->title = $featurd_arr[0]['title'];
                        } else {
                            $event[$key]->featured_image = asset('geekmeet/uploads/no-image-available.jpg');
                            $event[$key]->title = 'default.jpg';
                        }
                    } else {
                        $event[$key]->featured_image = asset('geekmeet/uploads/no-image-available.jpg');
                        $event[$key]->title = 'no-image-available.jpg';
                    }

                    $event[$key]->rating = $this->eventRating($val->event_id);
                    $event[$key]->comment_count = Review::where('event_id', $val->event_id)->count();
                    $event[$key]->owner = 0;
                    if ($user_id == $val->event_organize_by) {
                        $event[$key]->owner = 1;
                    }
                }
            }



            // Get Blog

            $blog = DB::table('gm_blogs')->select(DB::raw('blog_id,blog_title,blog_content,gm_blogs.status,gm_blogs.published_date as create_date,gm_images.name as thumb,users.username,title'))->leftJoin('users', 'gm_blogs.added_by', '=', 'users.user_id')->leftJoin('gm_images', 'gm_blogs.thumb', '=', 'gm_images.image_id')
                            ->where(function ($query) use ($search) {
                                $query->orWhere('blog_category', 'LIKE', '%' . $search . '%');
                                $query->orWhere('blog_title', 'LIKE', '%' . $search . '%');
                                return $query->orWhere('blog_content', 'LIKE', '%' . $search . '%');
                            })
                            ->orderBy('published_date', 'asc')->get();


            if (!empty($blog)) {
                foreach ($blog as $key => $val) {
                    $date_obj = date_create($val->create_date);
                    $blog[$key]->create_date = date_format($date_obj, 'F j, Y');
                    $blog[$key]->blog_content = $val->blog_content;
                    $blog[$key]->read_more = 0;

                    $string = strip_tags($val->blog_content);

                    if (strlen($string) > 200) {

                        // truncate string
                        $blog[$key]->read_more = 1;
                        $stringCut = substr($string, 0, 200);

                        // make sure it ends in a word so assassinate doesn't become ass...
                        $string = substr($stringCut, 0, strrpos($stringCut, ' '));
                    }

                    $blog[$key]->blog_content = $string;
                    if ($val->thumb != '') {
                        $blog[$key]->thumb = asset('geekmeet/uploads/blog/' . $val->thumb);
                    } else {
                        $blog[$key]->thumb = asset('geekmeet/uploads/no-image-available.jpg');
                        $blog[$key]->title = 'no-image-available.jpg';
                    }
                }
            }


            // Get Organization
            $organization = DB::table('gm_organizations')->select(DB::raw('organization_id,gm_organizations.user_id,organization_name,organization_description,organization_location,organization_email,organization_website,organization_contact,gm_images.name as organization_logo,gm_images.title as title'))->leftJoin('gm_images', 'gm_organizations.organization_logo', '=', 'gm_images.image_id')->where('user_id', $user_id)
                            ->where(function ($query) use ($search) {
                                $query->orWhere('organization_name', 'LIKE', '%' . $search . '%');
                                $query->orWhere('organization_description', 'LIKE', '%' . $search . '%');
                                return $query->orWhere('organization_location', 'LIKE', '%' . $search . '%');
                            })
                            ->orderBy('organization_id', 'desc')->get();
            if (!empty($organization)) {
                foreach ($organization as $key => $val) {
                    if ($val->organization_logo != '') {
                        $organization[$key]->organization_logo = asset('geekmeet/uploads/user/' . $val->organization_logo);
                    } else {
                        $organization[$key]->organization_logo = asset('geekmeet/uploads/no-image-available.jpg');
                        $organization[$key]->title = 'organization_logo';
                    }
                }
            }

            // Get Location
            $location = DB::table('gm_venue')->select(DB::raw('venue_id,venue_name,gm_venue.venue_image,gm_venue.user_id,gm_cities.name as city,users.username as username,gm_states.name as state,gm_countries.name as country,gm_venue.address,gm_venue.longitude,gm_venue.latitude,gm_venue.venue_description'))->leftJoin('gm_cities', 'gm_venue.city', '=', 'gm_cities.city_id')->leftJoin('gm_states', 'gm_venue.state', '=', 'gm_states.state_id')->leftJoin('gm_countries', 'gm_venue.country', '=', 'gm_countries.id')->leftJoin('users', 'gm_venue.user_id', '=', 'users.user_id')->where('gm_venue.user_id', $user_id)
                            ->where(function ($query) use ($search) {
                                $query->orWhere('venue_name', 'LIKE', '%' . $search . '%');
                                $query->orWhere('gm_venue.address', 'LIKE', '%' . $search . '%');
                                return $query->orWhere('venue_description', 'LIKE', '%' . $search . '%');
                            })
                            ->orderBy('venue_id', 'desc')->get();


            if (!empty($location)) {
                foreach ($location as $key => $val) {
                    $image_arr = array();
                    if ($val->venue_image != '') {

                        $img_arr = explode(',', $val->venue_image);


                        foreach ($img_arr as $img_val) {
                            $image_data = Image::where('image_id', $img_val)->get()->toArray();
                            if (!empty($image_data)) {

                                $image_arr[] = array(
                                    'image_url' => asset("geekmeet/uploads/events/" . $image_data[0]['name']),
                                    'image_title' => $image_data[0]['title']
                                );
                            } else {
                                $image_arr[] = array(
                                    'image_url' => asset('geekmeet/uploads/no-image-available.jpg'),
                                    'image_title' => 'no-image-available.jpg'
                                );
                            }
                        }
                    } else {
                        $image_arr[] = array(
                            'image_url' => asset('geekmeet/uploads/no-image-available.jpg'),
                            'image_title' => 'no-image-available.jpg'
                        );
                    }

                    $location[$key]->images = $image_arr;
                }
            }
            $data = array(
                'event' => $event,
                'blog' => $blog,
                'organization' => $organization,
                'venue' => $location
            );

            return response()->json(['error' => '', 'message' => '', 'data' => $data], 200);
        }else{
         return response()->json(['error' => '', 'message' => 'No data found'], 422);
        }
    }
    
    
      public function activate_account($user_id) {
      
        if ($user_id != '') {
            $user = User::find($user_id);
            if (!empty($user)) {
                $res = User::where('user_id', $user_id)->update(array('status' =>'active'));
                if ($res) {
                    return response()->json(['error' => '', 'message' => 'Account activate successfully'], 200);
                } else {
                    return response()->json(['error' => '', 'message' => 'something went wrong'], 422);
                }
            } else {
                return response()->json(['error' => '', 'message' => 'user not found'], 422);
            }
        }
    
    }

}

?>