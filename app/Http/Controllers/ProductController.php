<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    public function index()
    {
        $product = Product::all();
        return [
            "success" => true,
            "data" => $product
        ];
    }


    public function show(Request $request)
    {
        $product = Product::where('id', $request->id)->first();

        return [
            "success" => true,
            "data" => $product
        ];
    }

    public function add(Request $request)
    {

        if ($request->user()->currentAccessToken()->name != "adminToken") {
            return [
                "success" => false,
                "message" => "UnAuthorised Role for user"
            ];
        }

        $product = new Product();
        $product->name  = $request->name;
        $product->description  = $request->description;
        $product->price  = $request->price;
        $product->stocks  = $request->stocks;
        $product->size  = $request->size;

        $product->imgPath  = $request->file("img")->store("img", ["disk" => "my_files"]);
        $result = $product->save();

        if ($result) {
            return [
                "success" => true,
                "data" => $product,
                "token" => $request->user()->tokenCan('role:addproduct')

            ];
        } else {
            return [
                "success" => false,
                "message" => "reason unknown"
            ];
        }
    }

    public function edit($id, Request $request)
    {
        //delete img then update rest
        if ($request->user()->currentAccessToken()->name != "adminToken") {
            return [
                "success" => false,
                "message" => "UnAuthorised Role for user"
            ];
        }

        $product = Product::where('id', $id)->first();



        $product->name  = $request->name;
        $product->description  = $request->description;
        $product->price  = $request->price;
        $product->stocks  = $request->stocks;
        $product->size  = $request->size;

        //
        if ($request->file("img")) {
            File::delete($product->imgPath);
            $product->imgPath  = $request->file("img")->store("img", ["disk" => "my_files"]);
        }

        $result = $product->save();

        if ($result) {
            return [
                "success" => true,
                "data" => $product
            ];
        } else {
            return [
                "success" => false,
                "message" => "reason unknown"
            ];
        }
    }

    public function delete(Request $request)
    {
        if ($request->user()->currentAccessToken()->name != "adminToken") {
            return [
                "success" => false,
                "message" => "UnAuthorised Role for user"
            ];
        }

        // delete the img first 
        $product = Product::where("id", $request->id)->first();
        File::delete($product->imgPath);

        $result = Product::where("id", $request->id)->delete();

        if ($result) {
            return [
                "success" => true,
                "message" => "Item was Deleted"
            ];
        } else {
            return [
                "success" => true,
                "message" => "Operation Failed"
            ];
        }
    }
}
