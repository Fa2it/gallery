@extends('layouts.app')

@section('content')
<div class="container">   
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Statistics</div>

                <div class="card-body">
                    <gallery-stats></gallery-stats>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
