<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Invoice;

class ProductController extends Controller
{
    public function create()
    {
        return view('products.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:1',
        ]);

        // Kiểm tra sản phẩm đã tồn tại chưa
        $product = Product::where('name', $request->name)->first();

        if ($product) {
            // Nếu tồn tại, cập nhật số lượng sản phẩm
            $product->increment('stock', $request->stock);
        } else {
            // Nếu chưa có, thêm mới sản phẩm
            Product::create([
                'name' => $request->name,
                'price' => $request->price,
                'stock' => $request->stock,
            ]);
        }

        return redirect()->route('products.create')->with('success', 'Sản phẩm đã được cập nhật!');
    }

    
}