@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-50 py-4 sm:py-6">
    <div class="max-w-4xl mx-auto px-3 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-5">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-white flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Product
                    </h2>
                    <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-all duration-200 text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to List
                    </a>
                </div>
            </div>

            <form action="{{ route('products.update', $product->id) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="border-b border-gray-200 pb-4 mb-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">{{ __('SKU') }}:</span>
                            <span class="font-medium ml-2">{{ $product->sku }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">{{ __('Name') }}:</span>
                            <span class="font-medium ml-2">{{ $product->name }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">{{ __('Current Stock') }}:</span>
                            <span class="font-medium ml-2">{{ number_format($product->quantity) }} {{ $product->unit ?? __('pcs') }}</span>
                        </div>
                    </div>
                </div>

                <h3 class="text-sm font-semibold text-gray-700 mb-4">{{ __('Price Settings') }}</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="purchase_price" class="block text-xs font-semibold text-gray-700 mb-1.5">{{ __('Purchase Price') }} ({{ __('Per Piece') }})</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">UGx</span>
                            <input type="number" step="0.01" min="0" id="purchase_price" name="purchase_price"
                                   value="{{ old('purchase_price', $product->purchase_price) }}"
                                   class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('purchase_price') border-red-500 @enderror" />
                        </div>
                        @error('purchase_price') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="price" class="block text-xs font-semibold text-gray-700 mb-1.5">{{ __('Sell Price') }} ({{ __('Per Piece') }})</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">UGx</span>
                            <input type="number" step="0.01" min="0" id="price" name="price" value="{{ old('price', $product->price) }}"
                                   class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('price') border-red-500 @enderror" />
                        </div>
                        @error('price') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="purchase_price_per_dozen" class="block text-xs font-semibold text-gray-700 mb-1.5">{{ __('Purchase Price') }} ({{ __('Per Dozen') }})</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">UGx</span>
                            <input type="number" step="0.01" min="0" id="purchase_price_per_dozen" name="purchase_price_per_dozen"
                                   value="{{ old('purchase_price_per_dozen', $product->purchase_price_per_dozen) }}"
                                   class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('purchase_price_per_dozen') border-red-500 @enderror" />
                        </div>
                        @error('purchase_price_per_dozen') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="selling_price_per_dozen" class="block text-xs font-semibold text-gray-700 mb-1.5">{{ __('Sell Price') }} ({{ __('Per Dozen') }})</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">UGx</span>
                            <input type="number" step="0.01" min="0" id="selling_price_per_dozen" name="selling_price_per_dozen"
                                   value="{{ old('selling_price_per_dozen', $product->selling_price_per_dozen) }}"
                                   class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('selling_price_per_dozen') border-red-500 @enderror" />
                        </div>
                        @error('selling_price_per_dozen') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="purchase_price_per_carton" class="block text-xs font-semibold text-gray-700 mb-1.5">{{ __('Purchase Price') }} ({{ __('Per Carton') }})</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">UGx</span>
                            <input type="number" step="0.01" min="0" id="purchase_price_per_carton" name="purchase_price_per_carton"
                                   value="{{ old('purchase_price_per_carton', $product->purchase_price_per_carton) }}"
                                   class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('purchase_price_per_carton') border-red-500 @enderror" />
                        </div>
                        @error('purchase_price_per_carton') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="selling_price_per_carton" class="block text-xs font-semibold text-gray-700 mb-1.5">{{ __('Sell Price') }} ({{ __('Per Carton') }})</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">UGx</span>
                            <input type="number" step="0.01" min="0" id="selling_price_per_carton" name="selling_price_per_carton"
                                   value="{{ old('selling_price_per_carton', $product->selling_price_per_carton) }}"
                                   class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('selling_price_per_carton') border-red-500 @enderror" />
                        </div>
                        @error('selling_price_per_carton') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-100">
                    <button type="submit"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center gap-2 font-semibold text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update Product
                    </button>
                    <a href="{{ route('products.index') }}"
                       class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 font-semibold text-sm">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
