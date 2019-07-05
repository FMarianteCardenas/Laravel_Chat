<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Broadcast::channel('App.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });
Broadcast::channel('messages.{id}', function ($user, $id) {
    //dd($user->id,$id);
    return $user->id === (int) $id;
});

Broadcast::channel('user.has.loged.In.{id}', function($user,$id){
    //se transmite solo si el usuario no es el mismo
    //dd((int) $user->id === (int) $id);
    //dd($user,$id);
    return (int) Auth::user()->id === (int) $user->id;
    //return (int) $user->id === (int) $id;
});

Broadcast::channel('user.notifications.{id}',function($user,$id){
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{id}', function($user,$id){
    return true;
});