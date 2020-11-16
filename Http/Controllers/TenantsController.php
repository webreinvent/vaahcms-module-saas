<?php namespace VaahCms\Modules\Saas\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class TenantsController extends Controller {

	/**
	 * Display a listing of the resource.
	 * @return Response
	 */
	public function index()
	{
        return view('index');
	}

	/**
	 * Show the form for creating a new resource.
	 * @return Response
	 */
	public function create(Request $request)
	{
        return view('create');
	}

	/**
	 * Store a newly created resource in storage.
	 * @return Response
	 */
	public function store(Request $request)
	{
        return response()->json([]);
	}

	/**
	 * Display the specified resource.
	 * @param  int  $id
	 * @return Response
	 */
	public function show(Request $request, $id)
	{
		return view('show');
	}

	/**
	 * Show the form for editing the specified resource.
	 * @param  int  $id
	 * @return Response
	 */
	public function edit(Request $request, $id)
	{
        return view('edit');
	}

	/**
	 * Update the specified resource in storage.
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Request $request, $id)
	{
        return response()->json([]);
	}

	/**
	 * Remove the specified resource from storage.
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(Request $request, $id)
	{
        return response()->json([]);
	}

}
