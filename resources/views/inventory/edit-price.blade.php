@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-lg mx-auto">
        <div class="bg-white border border-blue-300 rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-semibold text-blue-700 mb-2">{{ __('Edit Prices') }}</h2>
            <p class="text-gray-500 text-sm mb-6">{{ $product->name }} ({{ $product->sku }})</p>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('inventory.update', $product->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Purchase Price (per piece)') }}</label>
                        <input type="number" name="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}"
                               class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" step="0.01" min="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Sell Price (per piece)') }}</label>
                        <input type="number" name="price" value="{{ old('price', $product->price) }}"
                               class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" step="0.01" min="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Bulk Purchase Price') }}</label>
                        <input type="number" name="purchase_price_bulk" value="{{ old('purchase_price_bulk', $product->purchase_price_bulk) }}"
                               class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" step="0.01" min="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Bulk Sell Price') }}</label>
                        <input type="number" name="selling_price_bulk" value="{{ old('selling_price_bulk', $product->selling_price_bulk) }}"
                               class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" step="0.01" min="0">
                    </div>
                </div>

                <div class="flex justify-between items-center mt-6">
                    <a href="{{ route('inventory.list') }}" class="text-gray-600 hover:text-gray-800">{{ __('Cancel') }}</a>
                    <button type="submit"
                            class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-150">
                        {{ __('Update Prices') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
