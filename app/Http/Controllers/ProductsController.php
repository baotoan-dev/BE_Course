<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index()
    {
        $products = [
            '0' => [
                'name' => 'Product 1',
                'price' => 100
            ],
            '1' => [
                'name' => 'Product 2',
                'price' => 200
            ],
            '2' => [
                'name' => 'Product 3',
                'price' => 300
            ]
        ];

        $title = 'List of Products';

        $products = json_decode(json_encode($products));

        // return view('products.index', compact('products', 'title'));
        return view('products.index')->with('products', $products)->with('title', $title);
    }

    public function detail($id)
    {
        $products = [
            '0' => [
                'name' => 'Product 1',
                'price' => 100
            ],
            '1' => [
                'name' => 'Product 2',
                'price' => 200
            ],
            '2' => [
                'name' => 'Product 3',
                'price' => 300
            ]
        ];

        $product = $products[$id];

        $title = 'Product Detail';

        $product = json_decode(json_encode($product));

        return view('products.detail', compact('product', 'title'));
    }
}
