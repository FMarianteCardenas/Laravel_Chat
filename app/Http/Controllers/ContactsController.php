<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Message;
use Auth;
use DB;
use App\Events\NewMessage;

class ContactsController extends Controller
{
    public function get(){
        $contacts = User::where('id','!=',Auth::user()->id)->get();

        $unreadIds = Message::select(\DB::raw('`from` as sender_id, count(`from`) as messages_count'))
        ->where('to',Auth::user()->id)
        ->where('read',false)
        ->groupBy('from')
        ->get();

        $contacts = $contacts->map(function($contact) use ($unreadIds){
            $contactUnread = $unreadIds->where('sender_id',$contact->id)->first();
            $contact->unread = $contactUnread? $contactUnread->messages_count:0;
            return $contact;
        });
        return response()->json($contacts);
    }

    public function getMessagesFor($id){
        //reseteo de todos los mensajes como leídos
        Message::where('from',$id)
        ->where('to',Auth::user()->id)
        ->update(['read'=>true]);
        //$messages = Message::where('from',$id)->orWhere('to',$id)->get();
        $messages = Message::where([['from','=',$id],['to','=',Auth::user()->id]])
        ->orWhere([['from','=',Auth::user()->id],['to','=',$id]])
        ->orderBy('id','ASC')
        ->get();
        //dd($messages);
        return response()->json($messages);
    }

    public function send(Request $request){
        $message = Message::create([
            'from'=>Auth::user()->id,
            'to'=>$request->contact_id,
            'message'=> $request->text
        ]);

        Broadcast(new NewMessage($message));

        return response()->json($message);
    }
}
