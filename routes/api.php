<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\EmailController;
use Illuminate\Mail\Markdown;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


/**
 * API global unsecured users routes
 */
$router->group(['prefix' => 'api'], function() use ($router){
    //$router->post('/users/developers', 'UsersController@createNewDevUser');
    //$router->post('/users/recruiters', 'UsersController@createNewRecruiterUser');
/*  TEST TEST ==> */ $router->post('/users/recruiters', 'UsersController@simpleCreate');  // <=== TEST TEST
    $router->post('/register/users/developers', 'AuthController@registerDev');
    $router->post('/register/users/recruiters', 'AuthController@registerRecrut');
  //  $router->post('/login', 'AuthController@login');
    $router->post('/logout', 'AuthController@logout');
    $router->post('/refresh', 'AuthController@refresh');
});


/**
 * API JWT secured routes group
 */
$router->group(['prefix' => 'api/secure', 'middleware' => 'auth'], function() use ($router){
    /**
     * API secure users related routes
     */
    $router->group(['prefix' => '/users'], function () use ($router) {
        $router->put('/{id}', 'UsersController@updateUser');
        $router->get('/search', 'UsersController@getDevSearchResults');
        $router->get('/contact', 'MailController@contactUser');
    });

    /**
     * API messages related routes
     */
    $router->group(['prefix' => '/messages/users', 'middleware' => 'auth'], function () use ($router) {
        $router->get('/', 'MessagesController@getOneFromAUser');
        $router->get('/{id}', 'MessagesController@getAllMessagesFromOneUser');
        $router->post('/', 'MessagesController@createMessageInDb');
    });

    /**
     * API favorites related routes
     */
    $router->group(['prefix' => 'api/secure/favorites', 'middleware' => 'auth'], function () use ($router) {
        $router->get('/recruiters', 'FavoritesController@getOneFromOneUser');
        $router->get('/recruiters/{id}', 'FavoritesController@getAllFromOneUser');
        $router->post('/recruiters', 'FavoritesController@AddNewToProfile');
        $router->delete('/{id}', 'FavoritesController@delete');
    });

    /**
     * API favorites routes
     */
    $router->group(['prefix' => 'api/secure/favorites', 'middleware' => 'auth'], function () use ($router) {
        $router->get('/recruiters', 'FavoritesController@getOneFromOneUser');
        $router->get('/recruiters/{id}', 'FavoritesController@getAllFromOneUser');
        $router->post('/recruiters', 'FavoritesController@AddNewToProfile');
        $router->delete('/{id}', 'FavoritesController@delete');
    });
});


/**
 *  JWT test routes
 */
$router->group(['prefix' => 'api'], function () use ($router) {
    // $router->post('/register', 'AuthController@register');
//$router->post('/register/developers', 'AuthController@registerDev');
//$router->post('/register/recruiters', 'AuthController@registerRecrut');
//$router->post('/logout', 'AuthController@logout');
//$router->post('/refresh', 'AuthController@refresh');
});

$router->group(['prefix' => 'api', 'middleware' => 'auth'], function () use ($router) {
    $router->get('/me', 'AuthController@me');
});




$router->group(['middleware' => 'auth'], function () use ($router) {
    Route::get('/email/verify', ['as' => 'verification.notice', 'uses' => 'VerificationController@show']);
    $router->get('/email/verify/{id}/{hash}', ['as' => 'verification.verify', 'uses' => 'VerificationController@verify', 'middleware' =>'signed']);
    Route::post('/email/resend', ['as' => 'verification.resend', 'uses' => 'VerificationController@resend']);
});

$router->group(['middleware' => 'verified'], function () use ($router) {
    $router->post('/login', 'AuthController@login');
});


/*use App\Mail\JustTesting;
use Illuminate\Support\Facades\Mail;

Route::get('/send-mail', function () {

    Mail::to('newuser@example.com')->send(new JustTesting());

    return 'A message has been sent to Mailtrap!';

});*/
