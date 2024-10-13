<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\ProductList;
use Validator;

class ProductListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $productLists = ProductList::with('product')->get();
        return response()->json([
            'status' => 'success',
            'productLists' => $productLists
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer',
            'status' => 'required|string',
        ]);

        if ($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $productLists = ProductList::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Product List created successfully',
            'productList' => $productLists,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $productLists = ProductList::with('product')->find($id);
        if(!$productLists){
            return response()->json([
                'status' => 'error',
                'message' => 'Product List not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'productList' => $productLists,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer',
            'status' => 'required|string',
        ]);

        if ($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $productList = ProductList::find($id);
        if(!$productList) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product List not found',
            ], 404);
        }

        $productList->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Product List updated successfully',
            'productList' => $productList,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        $productList = ProductList::find($id);
        if(!$productList){
            return response()->json([
                'status' => 'error',
                'message' => 'Product List not found',
            ], 404);
        }

        $productList->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Product List deleted successfully',
        ]);
    }

    //Method untuk pencarian berdasarkan query params
    public function search(Request $request)
    {
        $quantity = $request->get('quantity');
        $status = $request->get('status');

        $query = ProductList::query();

        if (!is_null($quantity)) {
            $query->where('quantity', '=', $quantity);
        }

        if (!is_null($status)) {
            $query->where('status', '=', $status);
        }

        $productList = $query->with('product')->get();

        if ($productList->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product List not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'productList' => $productList,
        ]);
    }
}
