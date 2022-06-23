<?php

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
    $router->post('/register/users/developers', 'AuthController@registerDev');
    $router->post('/register/users/recruiters', 'AuthController@registerRecrut');
    $router->post('/logout', 'AuthController@logout');
    $router->post('/refresh', 'AuthController@refresh');
    //Verify user email address
    $router->post('/email/verify', ['as' => 'email.verify', 'uses' => 'AuthController@emailVerify']);
});


/**
 * API unsecured but only accessible to verified users routes
 */
$router->group(['middleware' => 'verified'], function () use ($router) {
    $router->post('api/login', 'AuthController@login');
});


/**
 * API JWT secured routes group
 */
// prefix = url. //middleware: toutes les routes du gruop passent par les middleware indiqués.

// integre toute les routes dessous
$router->group(['prefix' => 'api/secure', 'middleware' => 'jwt.auth', 'jwt.refresh'], function() use ($router){
    // Envoi email de verification. Le lien comprend un token jwt (pour cette raison que intégré dans le jwt)
    $router->post('/email/request-verification', ['as' => 'email.request.verification', 'uses' => 'AuthController@emailRequestVerification']);

    //verified email address routes
    $router->group(['middleware' => 'verified'], function() use ($router){
      /**
       * API secure users related routes
       */
      // est à l'intérieur du email router group precedent. CEs routes demandent à ce que l'email soit verifié
      $router->group(['prefix' => '/users'], function () use ($router) {
          $router->post('/logout', 'AuthController@logout');
          $router->put('/{id}', 'UsersController@updateUser');
          $router->get('/me', 'AuthController@meNoJson');
          $router->get('/search', 'UsersController@getDevSearchResults');
          $router->get('/contact', 'MailController@contactUser');
      });

      /**
       * API messages related routes
       */
      $router->group(['prefix' => '/messages/users'], function () use ($router) {
          $router->get('/', 'MessagesController@getOneFromAUser');
          $router->get('/{id}', 'MessagesController@getAllMessagesFromOneUser');
          $router->post('/', 'MessagesController@createMessageInDb');
      });

      /**
       * API favorites related routes
       */
      $router->group(['prefix' => '/favorites'], function () use ($router) {
          $router->get('/recruiters', 'FavoritesController@getOneFromOneUser');
          $router->get('/recruiters/{id}', 'FavoritesController@getAllFromOneUser');
          $router->post('/recruiters', 'FavoritesController@AddNewToProfile');
          $router->delete('/{id}', 'FavoritesController@delete');
      });
    });

});


// plus utilisé c'était à la base pour le reset V2
$router->group(['middleware' => 'auth'], function () use ($router) {
    //$router->post('/password/reset-request', 'RequestPasswordController@sendResetLinkEmail');
});


