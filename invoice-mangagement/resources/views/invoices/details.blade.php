@extends('layouts.app')

@section('content')
<div class="container">
    <div class="back-btn">
        <a href="{{ route('invoices.index') }}" class="button">&larr; Quay lại danh sách</a>
    </div>

    <div class="invoice-box">
        <h2>Chi tiết hóa đơn #{{ $invoice->id }}</h2>

        <div class="invoice-info">
            <p><strong>Ngày tạo:</strong> {{ $invoice->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>Tổng tiền:</strong> {{ number_format($invoice->total_price, 0, ',', '.') }} đ</p>
        </div>

        <table>
            <tr>
                <th>Sản phẩm</th>
                <th>Số lượng</th>
                <th>Đơn giá</th>
                <th>Thành tiền</th>
            </tr>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->price, 0, ',', '.') }} đ</td>
                <td>{{ number_format($item->quantity * $item->price, 0, ',', '.') }} đ</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3"><strong>Tổng cộng:</strong></td>
                <td><strong>{{ number_format($invoice->total_price, 0, ',', '.') }} đ</strong></td>
            </tr>
        </table>
    </div>
</div>

<style>
    .container {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px;
    }

    .back-btn {
        width: 80%;
        text-align: left;
    }

    .button {
        display: inline-block;
        padding: 8px 12px;
        background-color: #28a745;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        transition: 0.3s;
        font-weight: bold;
    }

    .button:hover {
        background-color: #218838;
    }

    .invoice-box {
        width: 80%;
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border: 1px solid #ddd;
    }

    h2 {
        text-align: center;
        font-size: 22px;
        margin-bottom: 10px;
    }

    .invoice-info p {
        font-size: 16px;
        margin: 5px 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    th, td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: center;
    }

    th {
        background-color: #f4f4f4;
        font-weight: bold;
    }

    .total-row {
        background-color: #eee;
    }
</style>

@endsection