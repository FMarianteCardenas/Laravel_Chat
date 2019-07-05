<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;
use App\User;
use App\Events\LogedInUser;
use App\Notifications\ContactLogedIn;
use App\Notifications\ContactLogOut;
use Notification;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request){
        //dd("entro",$request);
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
                //dd(Auth::user()->activo);
                $user = User::findOrFail(Auth::user()->id);
                $user->online = true;
                $user->save();
                //buscanto todos los contactos del usuario que inició sesion
                $contacts_one = $user->contacts_one;
                $contacts_two = $user->contacts_two;

                $users = $contacts_one->merge($contacts_two);
                //dd($users);
                //enviando notificacion a todos los contactos del usuario
                Notification::send($users, new ContactLogedIn($user));

                //otra opcion para enviar datos de nueva conexion de un usuario, la diferencia es que se usa
                //un foreach que retrasa la app un poco dependiendo de la cantidad de contactos del usuario
                // foreach ($contacts as $key => $contact) {
                //     Broadcast(new LogedInUser($user,$contact->id));
                // }
                //dd($user);
               
                return redirect()->route('home');
        }
        else{
                //dd("entro a redirect",$request);
                return redirect()->back()->withInput()->withErrors(['message'=>'Credenciales Incorrectas']);
        }
    }

    public function logout(Request $request){
        $user = User::findOrFail(Auth::user()->id);
        $user->online = 0;
        $user->save();
        $contacts_one = $user->contacts_one;
        $contacts_two = $user->contacts_two;
        $users = $contacts_one->merge($contacts_two);
        Notification::send($users,new ContactLogOut($user));
        $this->guard()->logout();
        $request->session()->flush();
        $request->session()->regenerate();
    
        return redirect('/');
    }
}
