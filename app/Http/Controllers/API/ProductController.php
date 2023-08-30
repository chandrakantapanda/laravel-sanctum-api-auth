<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Validator;
class ProductController extends Controller {
    public function index()
    {
        $products = Product::all();
        $response = [
            'success' => true,
            'message' => 'Products retrieved successfully.',
            'data'    => $products,
        ];        
    return response()->json($response, 200);
    }
    public function store(Request $request) {//dd('asila');
        $request->validate([
            'name' => 'required',
            'detail' => 'required',
            'slug' => 'required',
            'price' => 'required'
        ]);
        $input = $request->all();
        $product = Product::create($input);
        $response = [
            'success' => true,
            'message' => 'Products retrieved successfully.',
            'data'    => $product,
        ];   
        // return Product::create($request->all());
    }
    public function show($id){
        $product = Product::find($id);
  
        if (is_null($product)) {
            $response = [
                'success' => false,
                'message' => 'Product not found.',
                'data'    => [],
            ];
            return response()->json($response, 404); 
        }
        $response = [
            'success' => true,
            'message' => 'Product retrieved successfully..',
            'data'    =>  $product,
        ];        
        return response()->json($response, 200);
    }

    public function update(Request $request, $id){
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'name' => 'required',
            'detail' => 'required',
            'price' => 'required'
        ]);
   
        if($validator->fails()){
            $response = [
                'success' => false,
                'message' => 'Validation Error.',
                'data'    => $validator->errors(),
            ];
            return response()->json($response, 404);     
        }
        $product = Product::find($id);

        $product->name = $input['name'];
        $product->detail = $input['detail'];
        $product->price = $input['price'];
        $product->update();
       
        $response = [
            'success' => true,
            'message' => 'Product updatedd successfully..',
            'data'    =>  $product,
        ];        
        return response()->json($response, 200);
    }
    public function destroy($id)
    {
        Product::destroy($id);
        $response = [
            'success' => true,
            'message' => 'Product deleted successfully..',
            'data'    =>  [],
        ];        
        return response()->json($response, 200);
    }
}
