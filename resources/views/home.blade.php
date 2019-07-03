@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">CHONCHI CHAT DE COPUCHA</div>

                <div class="card-body" id="app">
                <chat-component :user="{{auth()->user()}}"></chat-component>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
