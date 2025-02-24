@extends('layouts.app')

@section('content')

@if(session('success'))
<p class="success-message">{{ session('success') }}</p>
@endif

@if(session('error'))
<p class="error-message">{{ session('error') }}</p>
@endif

<div class="container">
    <div class="invoice-box">

        <h2>Danh sách hóa đơn</h2>
        <div class="search-box">
            <form action="{{ route('invoices.index') }}" method="GET">
                <input type="text" name="search" placeholder="Nhập mã hóa đơn..." value="{{ request('search') }}">
                <input type="date" name="from_date" value="{{ request('from_date') }}">
                <input type="date" name="to_date" value="{{ request('to_date') }}">
                <button type="submit" class="search-button">Tìm kiếm</button>
            </form>
        </div>

        <table>
            <tr>
                <th>Mã hóa đơn</th>
                <th>Ngày tạo</th>
                <th>Tổng tiền</th>
                <th>Thao tác</th>
            </tr>
            @forelse($invoices as $invoice)
            <tr>
                <td>#{{ $invoice->id }}</td>
                <td>{{ $invoice->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ number_format($invoice->total_price, 0, ',', '.') }} đ</td>
                <td>
                    <a href="{{ route('invoices.details', $invoice) }}" class="add-button">Chi tiết</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4">Không có hóa đơn nào</td>
            </tr>
            @endforelse
        </table>
    </div>

    <div class="pagination">
        {{ $invoices->links() }}
    </div>
</div>

<style>
    .container {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        gap: 20px;
    }

    .invoice-box {
        width: 80%;
        background: #fff;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border: 1px solid #ddd;
    }

    h2 {
        text-align: center;
        margin-bottom: 10px;
        font-size: 22px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    th,
    td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: center;
    }

    th {
        background-color: #f4f4f4;
        font-weight: bold;
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

    .add-button {
        display: inline-block;
        padding: 6px 10px;
        background-color: #ffc107;
        border: none;
        cursor: pointer;
        border-radius: 4px;
        text-decoration: none;
        color: black;
        font-weight: bold;
        transition: 0.3s;
    }

    .add-button:hover {
        background-color: #e0a800;
    }

    .success-message {
        color: green;
        font-weight: bold;
        text-align: center;
    }

    .error-message {
        color: red;
        font-weight: bold;
        text-align: center;
    }

    .pagination {
        margin-top: 10px;
        text-align: center;
    }

    .search-box {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-bottom: 15px;
}

.search-box input {
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.search-button {
    padding: 8px 12px;
    background-color: #007bff;
    color: white;
    border: none;
    cursor: pointer;
    border-radius: 4px;
    transition: 0.3s;
}

.search-button:hover {
    background-color: #0056b3;
}
</style>

@endsection