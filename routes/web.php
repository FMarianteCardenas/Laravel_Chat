<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/contacts','ContactsController@get')->name('getContacts');
Route::get('/conversation/{id}','ContactsController@getMessagesFor')->name('getMessagesFor');
Route::post('/conversation/send','ContactsController@send')->name('sendMessage');
Route::post('/message_read/{id}','ContactsController@markAsRead')->name('markAsRead');
Route::post('/send-file','Contactscontroller@saveFile')->name('saveFile');
Route::get('/download/{file_id}','ContactsController@downloadFile')->name('downloadFile');
