<?php


Route::domain('api.travninja.test')->namespace('Api')->group(function (){

});

Route::domain('main.travninja.test')->namespace('MainDashboard')->middleware('main-dashboard')->group(function (){
    Route::get('/','HomeController@index');
});


Route::domain('{agency}.travninja.test')->middleware('agency-check')->group(function (){
    Route::get('/', 'LandingController@index');
});

Auth::routes();


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
