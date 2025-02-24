@extends('layouts.app')

@section('content')
@if(session('success'))
    <p class="success-message">{{ session('success') }}</p>
@endif

<div class="form-container">
    <form action="{{ route('products.store') }}" method="POST">
        @csrf
        <label for="name">Tên sản phẩm:</label>
        <input type="text" name="name" required class="input-field">

        <label for="price">Giá:</label>
        <input type="number" name="price" step="0.01" required class="input-field">

        <label for="stock">Số lượng:</label>
        <input type="number" name="stock" min="1" required class="input-field">

        <button type="submit" class="submit-button">Thêm sản phẩm</button>
    </form>
</div>



<style>
    .form-container {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        max-width: 400px;
        margin: 20px auto;
    }

    .input-field {
        width: 100%;
        padding: 8px;
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .submit-button {
        width: 100%;
        padding: 10px;
        background-color: #28a745;
        color: white;
        border: none;
        cursor: pointer;
        border-radius: 5px;
        transition: 0.3s;
    }

    .submit-button:hover {
        background-color: #218838;
    }

    .button {
        display: inline-block;
        margin-top: 10px;
        padding: 8px 12px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        transition: 0.3s;
    }

    .button:hover {
        background-color: #0056b3;
    }

    .success-message {
        color: green;
        font-weight: bold;
        text-align: center;
    }
</style>
@endsection