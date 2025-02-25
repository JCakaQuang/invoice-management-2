<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\Session;

/**
 * Controller quản lý các chức năng liên quan đến hóa đơn
 */
class InvoiceController extends Controller
{
    /**
     * Hiển thị giao diện tạo hóa đơn mới với danh sách sản phẩm và giỏ hàng hiện tại
     * 
     * @param Request $request Chứa dữ liệu tìm kiếm sản phẩm
     * @return \Illuminate\View\View View tạo hóa đơn
     */
    public function create(Request $request)
    {
        // Lấy từ khóa tìm kiếm từ request
        $search = $request->input('search');

        // Truy vấn danh sách sản phẩm, lọc theo từ khóa tìm kiếm nếu có
        $products = Product::when($search, function ($query, $search) {
            return $query->where('name', 'LIKE', '%' . $search . '%');
        })->get();

        // Lấy dữ liệu giỏ hàng từ session
        $cart = session()->get('cart', []);

        // Trả về view với dữ liệu sản phẩm, giỏ hàng và từ khóa tìm kiếm
        return view('invoices.create', compact('products', 'cart', 'search'));
    }

    /**
     * Thêm sản phẩm vào giỏ hàng tạm thời
     * 
     * @param Request $request Chứa thông tin sản phẩm và số lượng cần thêm
     * @return \Illuminate\Http\RedirectResponse Chuyển hướng về trang tạo hóa đơn
     */
    public function addToCart(Request $request)
    {
        // Lấy ID sản phẩm từ request
        $productId = $request->input('product_id');
        // Đảm bảo số lượng ít nhất là 1
        $quantity = max(1, (int) $request->input('quantity', 1));

        // Tìm sản phẩm trong cơ sở dữ liệu
        $product = Product::find($productId);
        if (!$product) {
            // Nếu sản phẩm không tồn tại, trả về thông báo lỗi
            return redirect()->back()->with('error', 'Sản phẩm không tồn tại!');
        }

        // Lấy giỏ hàng từ session hoặc khởi tạo mảng rỗng
        $cart = Session::get('cart', []);

        // Kiểm tra sản phẩm đã có trong giỏ hàng chưa
        if (isset($cart[$productId])) {
            // Nếu đã có, tăng số lượng
            $cart[$productId]['quantity'] += $quantity;
        } else {
            // Nếu chưa có, thêm sản phẩm mới vào giỏ hàng
            $cart[$productId] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
            ];
        }

        // Cập nhật lại giỏ hàng trong session
        Session::put('cart', $cart);

        // Chuyển hướng về trang tạo hóa đơn với thông báo thành công
        return redirect()->route('invoices.create')->with('success', 'Sản phẩm đã được thêm vào hóa đơn!');
    }

    /**
     * Lưu hóa đơn và các mặt hàng trong giỏ hàng vào cơ sở dữ liệu
     * 
     * @param Request $request Request HTTP
     * @return \Illuminate\Http\RedirectResponse Chuyển hướng về trang tạo hóa đơn
     */
    public function store(Request $request)
    {
        // Bắt đầu giao dịch cơ sở dữ liệu để đảm bảo tính toàn vẹn dữ liệu
        DB::beginTransaction();

        try {
            // Lấy giỏ hàng từ session
            $cart = Session::get('cart', []);

            // Kiểm tra giỏ hàng có trống không
            if (empty($cart)) {
                return redirect()->route('invoices.create')
                    ->with('error', 'Hóa đơn trống!');
            }

            // Tạo hóa đơn mới
            $invoice = new Invoice();
            // Tính tổng giá trị hóa đơn từ tất cả các mặt hàng trong giỏ
            $invoice->total_price = array_sum(array_map(fn($item) => $item['quantity'] * $item['price'], $cart));
            // Gán ID người dùng đang đăng nhập là người tạo hóa đơn
            $invoice->user_id = auth()->id();
            // Lưu hóa đơn vào cơ sở dữ liệu
            $invoice->save();

            // Duyệt qua từng sản phẩm trong giỏ hàng để lưu chi tiết hóa đơn
            foreach ($cart as $productId => $item) {
                // Tìm sản phẩm trong cơ sở dữ liệu
                $product = Product::find($productId);

                // Kiểm tra sản phẩm có tồn tại không
                if (!$product) {
                    throw new \Exception("Sản phẩm không tồn tại: {$productId}");
                }

                // Kiểm tra số lượng tồn kho có đủ không
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Sản phẩm {$product->name} không đủ số lượng trong kho");
                }

                // Tạo chi tiết hóa đơn (invoice item)
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $productId,
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);

                // Cập nhật số lượng tồn kho sau khi bán
                $product->stock -= $item['quantity'];
                $product->save();
            }

            // Hoàn tất giao dịch cơ sở dữ liệu nếu không có lỗi
            DB::commit();

            // Xóa giỏ hàng sau khi đã lưu hóa đơn thành công
            Session::forget('cart');

            // Chuyển hướng về trang tạo hóa đơn với thông báo thành công
            return redirect()->route('invoices.create')
                ->with('success', 'Hóa đơn đã được lưu thành công!');
        } catch (\Exception $e) {
            // Hoàn tác giao dịch cơ sở dữ liệu nếu có lỗi
            DB::rollBack();

            // Chuyển hướng về trang tạo hóa đơn với thông báo lỗi
            return redirect()->route('invoices.create')
                ->with('error', 'Lỗi khi lưu hóa đơn: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị danh sách hóa đơn với khả năng tìm kiếm và lọc theo ngày
     * 
     * @param Request $request Chứa thông tin tìm kiếm và lọc
     * @return \Illuminate\View\View View danh sách hóa đơn
     */
    public function index(Request $request)
    {
        // Khởi tạo query builder để truy vấn hóa đơn
        $query = Invoice::query();

        // Nếu có từ khóa tìm kiếm, lọc theo ID hóa đơn
        if ($request->filled('search')) {
            $query->where('id', $request->search);
        }

        // Nếu có khoảng thời gian, lọc hóa đơn theo ngày tạo
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [$request->from_date . ' 00:00:00', $request->to_date . ' 23:59:59']);
        }

        // Lấy danh sách hóa đơn, sắp xếp theo thời gian tạo giảm dần và phân trang
        $invoices = $query->orderBy('created_at', 'desc')->paginate(10);

        // Trả về view với dữ liệu hóa đơn
        return view('invoices.index', compact('invoices'));
    }

    /**
     * Hiển thị chi tiết một hóa đơn cụ thể
     * 
     * @param Invoice $invoice Đối tượng hóa đơn cần xem chi tiết
     * @return \Illuminate\View\View View chi tiết hóa đơn
     */
    public function details(Invoice $invoice)
    {
        // Trả về view với dữ liệu hóa đơn
        return view('invoices.details', compact('invoice'));
    }

    /**
     * Xóa một sản phẩm khỏi giỏ hàng tạm thời
     * 
     * @param Request $request Chứa chỉ số sản phẩm cần xóa
     * @return \Illuminate\Http\RedirectResponse Chuyển hướng về trang trước đó
     */
    public function removeFromCart(Request $request)
    {
        // Lấy chỉ số sản phẩm cần xóa từ request
        $index = $request->input('index');
        // Lấy giỏ hàng từ session
        $cart = session()->get('cart', []);
        
        // Kiểm tra sản phẩm có tồn tại trong giỏ hàng không
        if (isset($cart[$index])) {
            // Nếu có, xóa sản phẩm khỏi giỏ hàng
            unset($cart[$index]);
            // Sắp xếp lại chỉ số mảng để tránh lỗ hổng
            $cart = array_values($cart);
            // Cập nhật lại giỏ hàng trong session
            session()->put('cart', $cart);
            // Chuyển hướng về trang trước với thông báo thành công
            return redirect()->back()->with('success', 'Sản phẩm đã được xóa khỏi hóa đơn tạm.');
        }
        
        // Nếu không tìm thấy sản phẩm, trả về thông báo lỗi
        return redirect()->back()->with('error', 'Không tìm thấy sản phẩm trong hóa đơn tạm.');
    }

    /**
     * Xóa tất cả sản phẩm khỏi giỏ hàng tạm thời
     * 
     * @return \Illuminate\Http\RedirectResponse Chuyển hướng về trang trước đó
     */
    public function clearCart()
    {
        // Xóa toàn bộ giỏ hàng khỏi session
        session()->forget('cart');
        // Chuyển hướng về trang trước với thông báo thành công
        return redirect()->back()->with('success', 'Hóa đơn tạm đã được xóa.');
    }
}