@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Image Gallery </div>
                <div class="card-body">
                    <gallery-photo usertoken="{{Auth::user()->api_token}}" 
                        uuid="{{Auth::user()->id}}"></gallery-photo>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
