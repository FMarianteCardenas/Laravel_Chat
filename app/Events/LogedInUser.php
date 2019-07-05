<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\User;
use Auth;
use DB;

class LogedInUser implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $channels = [];
    public $contact_id;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user,$contact_id)
    {
        //dd($user);
        $this->user = $user;
        $this->contact_id = $contact_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // return [
        //     new PrivateChannel($this->channels[0]),
        //     new PrivateChannel($this->channels[1])
        // ];
        return new PrivateChannel('user.has.loged.In.'.$this->contact_id);
    }

    public function broadcastWith(){
        //datos que se van a transmitir en el canal
        return [
            "user_name"=>$this->user->name,
            "id"=>$this->user->id];
    }

    public function broadcastWhen()
    {
        //contactos del usuario autenticado
        // $contacts = DB::table('networks')
        // ->where('user_one_id','=',Auth::user()->id)
        // ->orWhere('user_two_id','=',Auth::user()->id)
        // ->get();
        // $var = false;
        //buscar si el usuario autenticado tiene de contacto al que inicio sesión
        // foreach ($contacts as $key => $item) {
        //     if(($item->user_one_id == $this->user->id && $item->user_two_id == Auth::user()->id ) || ($item->user_two_id == $this->user->id && $item->user_one_id == Auth::user()->id)){
        //         $var = true;
        //     }
        // }
        // $contacts_one = Auth::user()->contacts_one;
        // $contacts_two = Auth::user()->contacts_two;
        // dd($var,$contacts);
        return true;
    }

}
