<?php

use App\User;

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the controller to call when that URI is requested.
  |
 */





Route::get('/', array('uses' => 'AuthenticateController@login'));


$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {


    $api->group(['middleware' => 'cors'], function ($api) {

        $api->any('login', 'App\Http\Controllers\AuthenticateController@authenticate');
        $api->post('event', 'App\Http\Controllers\Apiv1Controller@get_event');
        $api->post('blog', 'App\Http\Controllers\Apiv1Controller@get_blog');
        $api->get('event_category', 'App\Http\Controllers\Apiv1Controller@get_category_menu');
        $api->get('blog_category', 'App\Http\Controllers\Apiv1Controller@get_blog_category');
        $api->get('category_list', 'App\Http\Controllers\Apiv1Controller@category_list');
        $api->post('popular_event', 'App\Http\Controllers\Apiv1Controller@get_popular_event');
        $api->post('upcoming_event', 'App\Http\Controllers\Apiv1Controller@get_upcoming_event');
        $api->get('event_detail/{id}', 'App\Http\Controllers\Apiv1Controller@event_detail');
        $api->get('blog_detail/{id}', 'App\Http\Controllers\Apiv1Controller@blog_detail');
        $api->post('social_login', 'App\Http\Controllers\Apiv1Controller@social_login');
        $api->get('country', 'App\Http\Controllers\Apiv1Controller@get_country');
        $api->get('state/{id}', 'App\Http\Controllers\Apiv1Controller@get_state');
        $api->get('city/{id}', 'App\Http\Controllers\Apiv1Controller@get_city');
        $api->get('get_review/{id}', 'App\Http\Controllers\Apiv1Controller@getReview');
        $api->post('add_visit', 'App\Http\Controllers\Apiv1Controller@visitCount');
        $api->get('banner/{title}', 'App\Http\Controllers\Apiv1Controller@get_banner');
        $api->post('forgot_password', 'App\Http\Controllers\Apiv1Controller@forgotPassword');
        $api->get('checkForgotToken/{any}', 'App\Http\Controllers\Apiv1Controller@checkForgotToken');
        $api->post('reset_password', 'App\Http\Controllers\Apiv1Controller@resetPassword');
        $api->post('contactUs', 'App\Http\Controllers\Apiv1Controller@contact_us');
        $api->any('upload_images', 'App\Http\Controllers\Apiv1Controller@upload_image');
        $api->any('remove_image', 'App\Http\Controllers\Apiv1Controller@remove_image');
        $api->any('delete_per_image','App\Http\Controllers\EventController@delete_per_image');
        $api->any('set_featured_image', 'App\Http\Controllers\EventController@featured_image');
        $api->get('print/{id}', 'App\Http\Controllers\Apiv1Controller@print_event');
        $api->post('search', 'App\Http\Controllers\Apiv1Controller@search');
        $api->post('registration', 'App\Http\Controllers\Apiv1Controller@registration');
        $api->get('activate_account/{id}', 'App\Http\Controllers\Apiv1Controller@activate_account');
      

        $api->group([ 'middleware' => 'jwt.auth'], function ($api) {

            $api->any('logout', 'App\Http\Controllers\AuthenticateController@logout');
            $api->get('profile', 'App\Http\Controllers\Apiv1Controller@get_profile');
            $api->post('update_profile', 'App\Http\Controllers\Apiv1Controller@update_profile');
            $api->get('venue', 'App\Http\Controllers\Apiv1Controller@get_venue');
            $api->get('organization', 'App\Http\Controllers\Apiv1Controller@get_organization');
            $api->post('add_event', 'App\Http\Controllers\Apiv1Controller@add_event');
            $api->post('add_review', 'App\Http\Controllers\Apiv1Controller@add_review');
            $api->post('edit_review', 'App\Http\Controllers\Apiv1Controller@edit_review');
            $api->post('add_rating', 'App\Http\Controllers\Apiv1Controller@add_rating');
            $api->post('add_reply', 'App\Http\Controllers\Apiv1Controller@add_review_reply');
            $api->get('organization_detail/{id}', 'App\Http\Controllers\Apiv1Controller@organization_detail');
            $api->get('organization_list', 'App\Http\Controllers\Apiv1Controller@organization_list');
            $api->post('add_organization', 'App\Http\Controllers\Apiv1Controller@storeOrganization');
            $api->post('update_organization', 'App\Http\Controllers\Apiv1Controller@updateOrganization');
            $api->get('delete_organization/{id}', 'App\Http\Controllers\Apiv1Controller@deleteOrganization');
            $api->get('venue_list', 'App\Http\Controllers\Apiv1Controller@venue_list');
            $api->post('add_venue', 'App\Http\Controllers\Apiv1Controller@storeVenue');
            $api->post('update_venue', 'App\Http\Controllers\Apiv1Controller@updateVenue');
            $api->get('delete_venue/{id}', 'App\Http\Controllers\Apiv1Controller@deleteVenue');
            $api->get('venue_detail/{id}', 'App\Http\Controllers\Apiv1Controller@venue_detail');
            $api->post('edit_reply', 'App\Http\Controllers\Apiv1Controller@edit_reply');
            $api->post('deleteComment', 'App\Http\Controllers\Apiv1Controller@deleteComment');
            $api->post('user_visit', 'App\Http\Controllers\Apiv1Controller@userVisitCount');
            $api->post('detailed_popular_event', 'App\Http\Controllers\Apiv1Controller@detailed_popular_event');
            $api->post('detailed_upcoming_event', 'App\Http\Controllers\Apiv1Controller@detailed_upcoming_event');
            $api->post('detailed_event', 'App\Http\Controllers\Apiv1Controller@detailed_event');
            $api->post('full_event_detail', 'App\Http\Controllers\Apiv1Controller@full_event_detail');
            $api->get('user_event_detail/{id}', 'App\Http\Controllers\Apiv1Controller@user_event_detail');
            $api->post('favorite', 'App\Http\Controllers\Apiv1Controller@favorite');
            $api->post('change_password', 'App\Http\Controllers\Apiv1Controller@changePassword');
            $api->get('edit_event_detail/{id}', 'App\Http\Controllers\Apiv1Controller@edit_event_detail');
            $api->post('update_event', 'App\Http\Controllers\Apiv1Controller@update_event');
            $api->get('delete_event/{id}', 'App\Http\Controllers\Apiv1Controller@delete_event');
            $api->post('send_friend', 'App\Http\Controllers\Apiv1Controller@sendToFriend');
            $api->post('add_attendance', 'App\Http\Controllers\Apiv1Controller@add_attendance');
            $api->any('addCalender/{id}', 'App\Http\Controllers\Apiv1Controller@addCalender');
            $api->get('edit_venue_detail/{id}', 'App\Http\Controllers\Apiv1Controller@edit_venue_detail');
            $api->post('search_for_user', 'App\Http\Controllers\Apiv1Controller@search_for_user');
          
        });


    });
});

// login

Route::post('login', array('uses' => 'AuthenticateController@login'));
Route::get('logout', array('uses' => 'AuthenticateController@doLogout'));

Route::group(['middleware' => ['check']], function () {
    Route::get('dashboard', array('uses' => 'DashboardController@index'));

    // Category
    Route::resource('category', 'CategoryController');
    Route::get('category_list', 'CategoryController@getCategoryData');
    Route::get('category/delete/{id}', 'CategoryController@delete');

    // Event
    Route::resource('event', 'EventController');
    Route::get('event_list', 'EventController@getEventData');
    Route::any('state', 'EventController@getState');
    Route::any('city', 'EventController@getCity');
    Route::any('upload_image', 'EventController@upload_image');
    Route::any('delete_image', 'EventController@delete_image');
    Route::any('featured_image', 'EventController@featured_image');
    Route::any('delete_per_image', 'EventController@delete_per_image');
    Route::get('event/delete/{id}', 'EventController@delete');
    Route::any('venue', 'EventController@getVenue');
    Route::any('user_organization', 'EventController@getOrganization');
    Route::any('add_location', 'EventController@addLocation');

    // Member

    Route::resource('member', 'MemberController');
    Route::get('member_list', 'MemberController@getMemberData');
    Route::get('member/delete/{id}', 'MemberController@delete');

    // location 
    Route::resource('location', 'LocationController');
    Route::get('location/{id}', 'LocationController@getlocation');
    Route::get('location/create/{user_id}', 'LocationController@createlocation');
    Route::get('location/edit/{location_id}', 'LocationController@editlocation');
    Route::post('location/store', 'LocationController@storelocation');
    Route::get('location_list/{id}', 'LocationController@getLocationData');
    Route::get('location/delete/{id}/{user_id}', 'LocationController@delete');

    // organization

    Route::resource('organization', 'OrganizationController');
//    Route::get('organisation/{id}', 'OrganizationController@getorganisation');
    Route::get('organization/create/{user_id}', 'OrganizationController@createOrganization');
    Route::post('organization/store', 'OrganizationController@storeorganization');
    Route::get('organization/edit/{organization_id}', 'OrganizationController@editorganization');
    Route::get('organization_list/{id}', 'OrganizationController@getOrganizationData');
    Route::get('organization/delete/{id}/{user_id}', 'OrganizationController@delete');


    // Blog

    Route::resource('blog', 'BlogController');
    Route::get('blog_list', 'BlogController@getBlogData');
    Route::get('blog/delete/{id}', 'BlogController@delete');
    Route::get('blog/download/{id}', 'BlogController@download_media');
    Route::any('upload_media', 'BlogController@upload_media');
    Route::any('delete_media', 'BlogController@delete_media');
    Route::any('blog/search', 'BlogController@seacrh');

    // Blog Category
    Route::resource('blog_category', 'BlogCategoryController');
    Route::get('blog_category_list', 'BlogCategoryController@getBlogCategoryData');
    Route::get('blog_category/delete/{id}', 'BlogCategoryController@delete');
    Route::any('delete_per_media', 'BlogController@delete_per_media');
    
    // Email Management
    Route::resource('email', 'EmailController');
});


