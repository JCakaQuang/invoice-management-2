@extends('layouts.app')

@section('content')

@if(session('success'))
<p class="success-message">{{ session('success') }}</p>
@endif

@if(session('error'))
<p class="error-message">{{ session('error') }}</p>
@endif

<div class="container">
    <!-- Bảng sản phẩm -->
    <div class="product-list">
        <h2>Danh sách sản phẩm</h2>
        <a href="{{ route('products.create') }}" class="button">+ Thêm sản phẩm</a>
        <div class="search-box">
            <form action="{{ route('invoices.create') }}" method="GET">
                <input type="text" name="search" placeholder="Tìm kiếm sản phẩm..." value="{{ request('search') }}">
                <button type="submit">Tìm kiếm</button>
            </form>
        </div>

        <table class="product">
            <tr>
                <th>Sản phẩm</th>
                <th>Giá</th>
                <th>Số lượng tồn</th>
                <th>Thêm vào hóa đơn</th>
            </tr>
            @foreach ($products as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>{{ number_format($product->price, 2) }} VND</td>
                <td>{{ $product->stock }}</td>
                <td>
                    <form action="{{ route('invoices.addToCart') }}" method="POST" class="add-form">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="number" name="quantity" min="1" max="{{ $product->stock }}" value="1" class="quantity-input">
                        <button type="submit" class="add-button">Thêm</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </table>
    </div>

    <!-- Hóa đơn tạm -->
    <div class="invoice-box">
        <h2>Hóa đơn tạm</h2>
        @if (!empty($cart))
        <table>
            <tr>
                <th>Sản phẩm</th>
                <th>SL</th>
                <th>Giá</th>
                <th>Tổng</th>
            </tr>
            @php $total = 0; @endphp
            @foreach ($cart as $item)
            <tr>
                <td>{{ $item['name'] }}</td>
                <td>{{ $item['quantity'] }}</td>
                <td>{{ number_format($item['price'], 2) }} VND</td>
                <td>{{ number_format($item['quantity'] * $item['price'], 2) }} VND</td>
            </tr>
            @php $total += $item['quantity'] * $item['price']; @endphp
            @endforeach
            <tr class="total-row">
                <td colspan="3"><strong>Tổng cộng:</strong></td>
                <td><strong>{{ number_format($total, 2) }} VND</strong></td>
            </tr>
        </table>
        <form action="{{ route('invoices.store') }}" method="POST">
            @csrf
            <button type="submit" class="save-button">Lưu hóa đơn</button>
        </form>
        @else
        <p>Chưa có sản phẩm nào trong hóa đơn.</p>
        @endif
    </div>
</div>

<style>
    /* Base Layout */
    .container {
        display: flex;
        gap: 2rem;
        max-width: 1200px;
        margin: 0 auto;
        padding: 1.5rem;
    }

    /* Card Styling */
    .product-list,
    .invoice-box {
        background: #ffffff;
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        padding: 1.5rem;
    }

    .product-list {
        flex: 3;
    }

    .invoice-box {
        flex: 2;
        border: 1px solid #e5e7eb;
    }

    /* Typography */
    h2 {
        color: #1f2937;
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
    }

    /* Table Styling */
    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin: 1rem 0;
    }

    th {
        background-color: #f9fafb;
        color: #4b5563;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.875rem;
        padding: 0.75rem 1rem;
        border-bottom: 2px solid #e5e7eb;
    }

    td {
        padding: 1rem;
        color: #374151;
        border-bottom: 1px solid #e5e7eb;
    }

    .total-row {
        background-color: #f9fafb;
        font-weight: 600;
    }

    .total-row td {
        border-top: 2px solid #e5e7eb;
    }

    /* Buttons */
    .button {
        display: inline-flex;
        align-items: center;
        padding: 0.625rem 1rem;
        background-color: #10b981;
        color: white;
        text-decoration: none;
        border-radius: 0.5rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .button:hover {
        background-color: #059669;
        transform: translateY(-1px);
    }

    .save-button {
        width: 100%;
        padding: 0.75rem;
        background-color: #3b82f6;
        color: white;
        border: none;
        border-radius: 0.5rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-top: 1rem;
    }

    .save-button:hover {
        background-color: #2563eb;
        transform: translateY(-1px);
    }

    /* Form Elements */
    .add-form {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        justify-content: center;
    }

    .quantity-input {
        width: 4rem;
        padding: 0.5rem;
        text-align: center;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.875rem;
    }

    .quantity-input:focus {
        outline: none;
        border-color: #3b82f6;
        ring: 2px solid rgba(59, 130, 246, 0.5);
    }

    .add-button {
        padding: 0.5rem 1rem;
        background-color: #f59e0b;
        color: white;
        border: none;
        border-radius: 0.375rem;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .add-button:hover {
        background-color: #d97706;
    }

    /* Search Box */
    .search-box {
        margin: 1rem 0;
    }

    .search-box form {
        display: flex;
        gap: 0.5rem;
    }

    .search-box input[type="text"] {
        flex: 1;
        padding: 0.625rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.875rem;
    }

    .search-box input[type="text"]:focus {
        outline: none;
        border-color: #3b82f6;
        ring: 2px solid rgba(59, 130, 246, 0.5);
    }

    .search-box button {
        padding: 0.625rem 1rem;
        background-color: #6b7280;
        color: white;
        border: none;
        border-radius: 0.375rem;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .search-box button:hover {
        background-color: #4b5563;
    }

    /* Status Messages */
    .success-message,
    .error-message {
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        font-weight: 500;
    }

    .success-message {
        background-color: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    .error-message {
        background-color: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .container {
            flex-direction: column;
        }

        .product-list,
        .invoice-box {
            width: 100%;
        }

        .add-form {
            flex-wrap: wrap;
        }
    }
</style>

@endsection