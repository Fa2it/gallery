<?php

namespace App\Http\Controllers\Api\v1;

use App\Gallery;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($start, $limits)
    {
        return response()->json( Gallery::UserPhotos($start, $limits), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return response()->json( Gallery::CreateUserPhoto(), 200);
    }

    /**
     * Display a list of the resource base on 
     * weekday and weekend days
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return response()->json( Gallery::stats(), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Gallery::RemoveUserPhoto( $id );
    }
}
