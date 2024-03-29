<?php

namespace App\Http\Controllers;

use App\Models\election;
use Illuminate\Http\Request;

class ElectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return election::with('constituencies')->get();
       
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\election  $election
     * @return \Illuminate\Http\Response
     */
    public function show(election $election)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\election  $election
     * @return \Illuminate\Http\Response
     */
    public function edit(election $election)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\election  $election
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, election $election)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\election  $election
     * @return \Illuminate\Http\Response
     */
    public function destroy(election $election)
    {
        //
    }
}
