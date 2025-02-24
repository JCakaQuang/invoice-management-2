<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\Session;

class InvoiceController extends Controller
{


    public function create(Request $request)
    {
        $search = $request->input('search');

        $products = Product::when($search, function ($query, $search) {
            return $query->where('name', 'LIKE', '%' . $search . '%');
        })->get();

        $cart = session()->get('cart', []);

        return view('invoices.create', compact('products', 'cart', 'search'));
    }


    public function addToCart(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);

        $product = Product::find($productId);
        if (!$product) {
            return redirect()->back()->with('error', 'Sản phẩm không tồn tại!');
        }

        // Lấy giỏ hàng từ session
        $cart = Session::get('cart', []);

        // Nếu sản phẩm đã có trong giỏ hàng, tăng số lượng
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            // Nếu chưa có, thêm sản phẩm vào giỏ hàng
            $cart[$productId] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
            ];
        }

        // Lưu giỏ hàng vào session
        Session::put('cart', $cart);

        return redirect()->route('invoices.create')->with('success', 'Sản phẩm đã được thêm vào hóa đơn!');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $cart = Session::get('cart', []);

            if (empty($cart)) {
                return redirect()->route('invoices.create')
                    ->with('error', 'Hóa đơn trống!');
            }

            // Tạo hóa đơn mới
            $invoice = new Invoice();
            $invoice->total_price = array_sum(array_map(fn($item) => $item['quantity'] * $item['price'], $cart));
            $invoice->user_id = auth()->id();
            $invoice->save();


            // Lưu chi tiết hóa đơn
            foreach ($cart as $productId => $item) {
                $product = Product::find($productId);

                if (!$product) {
                    throw new \Exception("Sản phẩm không tồn tại: {$productId}");
                }

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Sản phẩm {$product->name} không đủ số lượng trong kho");
                }

                // Tạo invoice item
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $productId,
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);

                // Cập nhật số lượng tồn kho
                $product->stock -= $item['quantity'];
                $product->save();
            }

            DB::commit();

            Session::forget('cart');

            return redirect()->route('invoices.create')
                ->with('success', 'Hóa đơn đã được lưu thành công!');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('invoices.create')
                ->with('error', 'Lỗi khi lưu hóa đơn: ' . $e->getMessage());
        }
    }

    public function index(Request $request)
    {
        $query = Invoice::query();

        if ($request->filled('search')) {
            $query->where('id', $request->search);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [$request->from_date . ' 00:00:00', $request->to_date . ' 23:59:59']);
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('invoices.index', compact('invoices'));
    }


    public function details(Invoice $invoice)
    {
        return view('invoices.details', compact('invoice'));
    }
}