<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use App\User;
use App\Message;
use App\Upload;
use Auth;
use DB;
use File;
use App\Events\NewMessage;
use App\Notifications\MessageWatched;

class ContactsController extends Controller
{
    public function get(){
        //$contacts = User::where('id','!=',Auth::user()->id)->get();
        // $contacts = DB::table('users')
        // ->leftjoin('networks',function($join){
        //     $join->on('networks.user_one_id','=','users.id')
        //          ->orOn('networks.user_two_id','=','users.id');
        // })
        // ->where('networks.user_one_id','=',Auth::user()->id)
        // ->orWhere('networks.user_two_id','=',Auth::user()->id)
        // ->where('users.id','!=',Auth::user()->id)
        // ->get();
        //dd($contacts);
        $contacts = Auth::user()->contacts_one;
        $contacts_two = Auth::user()->contacts_two;
        foreach ($contacts_two as $key => $contact) {
            $contacts->push($contact);
        }

        //dd($contacts_one,$contacts_two);

        $unreadIds = Message::select(\DB::raw('`from` as sender_id, count(`from`) as messages_count'))
        ->where('to',Auth::user()->id)
        ->where('read',false)
        ->groupBy('from')
        ->get();
        //dd($unreadIds);

        $contacts = $contacts->map(function($contact) use ($unreadIds){
            $contactUnread = $unreadIds->where('sender_id',$contact->id)->first();
            $contact->unread = $contactUnread? $contactUnread->messages_count:0;
            return $contact;
        });

        //dd($contacts);
        return response()->json($contacts);
    }

    public function getMessagesFor($id){
        //reseteo de todos los mensajes como leídos
        Message::where('from',$id)
        ->where('to',Auth::user()->id)
        ->update(['read'=>true]);

        //obteniendo todos los mensajes entre el contacto y el usuario autenticado
        $messages = Message::where([['from','=',$id],['to','=',Auth::user()->id]])
        ->orWhere([['from','=',Auth::user()->id],['to','=',$id]])
        ->orderBy('id','ASC')
        ->get();

        //ultimo mensaje leido
        $last_message = Message::where([['from','=',$id],['to','=',Auth::user()->id]])
        ->orWhere([['from','=',Auth::user()->id],['to','=',$id]])
        ->orderBy('id','DESC')
        ->first();

        //dd($last_message);

        //enviar una notificacion al usuario remitente que el usuario autenticado leyo sus mensajes
        $user = User::findOrFail($id);
        $user->notify(new MessageWatched($last_message));

        $files = Upload::where([['from','=',$id],['to','=',Auth::user()->id]])
        ->orderBy('id','DESC')
        ->get();
        //dd($messages);
        return response()->json(['messages'=>$messages,'files'=>$files]);
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

    public function markAsRead($id){
        $message = Message::findOrFail($id);
        $message->read = 1;
        $message->save();

        $user = $message->fromContact;
        //notificando al remitente que se leyó el mensaje
        $user->notify(new MessageWatched($message));
        return response()->json(['message'=>'Mensaje actualizado como leído']);
    }

    public function saveFile(Request $request){
        //dd($request->file,$request->to);
        //return Storage::download('uploads//LBRIQnrMd1Kcd92evZW2MNSW4pkNIy2nCTjTIPkI.jpeg');
        
        $file = Input::file('file');
        $filename = $file->getClientOriginalName();
        //$hash = hash( 'sha256', time());

        $path = $request->file('file')->store('uploads', 'public');

        $file = Upload::create([
            'filename' => $filename,
            'mime' => $file->getClientMimeType(),
            'path' => $path,
            'size' => $file->getClientSize(),
            'from' => Auth::user()->id,
            'to' => $request->to
        ]);
                    
        return response()->json(['success' => true,'id' => $file->id,'path'=>$path], 200);
                        
        // if(Storage::disk('local')->put($path.'/'.$filename,  File::get($file))){
                
        // }
                        
        // return response()->json(['success' => false], 500);
    }

    public function downloadFile($file_id){
        $file = Upload::findOrFail($file_id);
        //return Storage::download(storage_path('app/public').'/uploads/B1RqrrkVLPJLIaDRBzRM6EjLLqBSVYjcKcF6fDDo.jpeg');
        
        $headers = array(
           'Content-Type: '.$file->mime,
         );
        return response()->download(storage_path('app/public').$file->path, $file->filename, $headers);
    }
}
