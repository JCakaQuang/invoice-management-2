@extends('layouts.app')

@section('content')

<h2>Hóa đơn #{{ $invoice->id }}</h2>
<p>Tổng tiền: {{ number_format($invoice->total_price, 2) }} VND</p>
<table>
    <tr>
        <th>Sản phẩm</th>
        <th>Số lượng</th>
        <th>Giá</th>
    </tr>
    @foreach ($invoice->items as $item)
    <tr>
        <td>{{ $item->product->name }}</td>
        <td>{{ $item->quantity }}</td>
        <td>{{ number_format($item->price, 2) }} VND</td>
    </tr>
    @endforeach
</table>
@endsection