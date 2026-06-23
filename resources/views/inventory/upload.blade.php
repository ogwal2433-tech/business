@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-50 py-4 sm:py-6">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">

        <!-- Header Actions -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 mb-6">
            <a href="{{ route('inventory.download.template') }}"
               class="inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md font-medium text-sm flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <span>{{ __('Download Template') }}</span>
            </a>

            <form action="{{ route('inventory.bulk.upload') }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="flex flex-col sm:flex-row gap-2 flex-1">
                @csrf
                <input
                    type="file"
                    name="inventory_file"
                    accept=".csv"
                    required
                    class="flex-1 border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white"
                    aria-label="{{ __('Select inventory file to upload') }}"
                />
                <button
                    type="submit"
                    class="flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 disabled:cursor-not-allowed text-white px-5 py-2.5 rounded-xl transition font-semibold text-sm shadow-sm hover:shadow-md flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                    </svg>
                    <span>{{ __('Upload CSV') }}</span>
                </button>
            </form>
        </div>

        @if(session('message'))
            <p class="text-sm text-gray-600 mb-4">{{ session('message') }}</p>
        @endif
        <p id="uploadStatus" class="text-sm text-gray-600 hidden mb-4"></p>

        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-xl shadow-sm">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        @if(session('inventory_upload'))
                            <h3 class="text-sm font-semibold text-green-800 mb-1">{{ __('Inventory Upload Successful!') }}</h3>
                        @endif
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('warning'))
            <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-500 rounded-xl shadow-sm">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="text-sm font-semibold text-yellow-800 mb-1">{{ __('Notice') }}</h3>
                        <p class="text-sm text-yellow-700">{!! session('warning') !!}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Error Messages -->
        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl shadow-sm">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="text-sm font-semibold text-red-800 mb-2">{{ __('Please correct the following errors:') }}</h3>
                        <ul class="text-sm text-red-700 list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-5">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-white flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                        </svg>
                        <span>{{ __('Product Information') }}</span>
                    </h2>
                    <a href="{{ route('inventory.upload.logs') }}" class="text-sm text-white/80 hover:text-white underline">
                        {{ __('View Upload Logs') }}
                    </a>
                </div>
            </div>

            <form method="POST" action="{{ route('inventory.upload.process') }}" class="p-6">
                @csrf

                <div id="products-container" class="space-y-5">
                    <!-- Initial Product Item -->
                    <div class="product-item border border-gray-200 rounded-xl p-5 bg-white shadow-sm hover:shadow-md transition-shadow" data-index="0">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4">
                            <!-- SKU with Scanner -->
                            <div class="sm:col-span-2 lg:col-span-3">
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                                    </svg>
                                    <span>{{ __('SKU') }} *</span>
                                </label>
                                <div class="flex gap-1.5">
                                    <input type="text" name="products[0][sku]" class="sku-input flex-1 min-w-0 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required placeholder="{{ __('e.g., PROD001') }}">
                                    <button type="button" class="scan-btn bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg transition-colors flex items-center justify-center shadow-sm hover:shadow-md flex-shrink-0" title="{{ __('Scan Barcode') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Product Name -->
                            <div class="sm:col-span-2 lg:col-span-4">
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    <span>{{ __('Product Name') }} *</span>
                                </label>
                                <input type="text" name="products[0][name]" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="{{ __('e.g., Apple iPhone 14') }}">
                            </div>

                            <!-- Quantity -->
                            <div class="sm:col-span-1 lg:col-span-2">
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                    <span>{{ __('Quantity') }} *</span>
                                </label>
                                <input type="number" name="products[0][quantity]" class="quantity-input w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" min="1" required placeholder="0">
                            </div>

                            <!-- Unit Selection -->
                            <div class="sm:col-span-1 lg:col-span-2">
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    <span>{{ __('Unit') }}</span>
                                </label>
                                <select name="products[0][unit]" class="unit-select w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                    <option value="piece" selected>{{ __('Piece') }}</option>
                                    <option value="dozen">{{ __('Dozen') }} (12)</option>
                                    <option value="carton">{{ __('Carton') }} (24)</option>
                                </select>
                            </div>

                            <!-- Remove Button -->
                            <div class="sm:col-span-2 lg:col-span-1 flex items-end">
                                <button type="button" class="remove-product w-9 h-9 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-colors flex items-center justify-center disabled:opacity-30 disabled:cursor-not-allowed" disabled aria-disabled="true" title="{{ __('Delete product') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>

                            <!-- Bulk Purchase Price -->
                            <div class="sm:col-span-1 lg:col-span-3 bulk-group hidden">
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                    {{ __('Bulk Purchase') }}
                                    <span class="text-xs text-gray-400 bulk-label"></span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">UGx</span>
                                    <input type="text" name="products[0][purchase_price_bulk]" class="bulk-purchase number-input w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="0">
                                </div>
                            </div>

                            <!-- Bulk Selling Price -->
                            <div class="sm:col-span-1 lg:col-span-3 bulk-group hidden">
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                    {{ __('Bulk Selling') }}
                                    <span class="text-xs text-gray-400 bulk-label"></span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">UGx</span>
                                    <input type="text" name="products[0][selling_price_bulk]" class="bulk-sell number-input w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="0">
                                </div>
                            </div>

                            <!-- Per-piece Purchase -->
                            <div class="sm:col-span-1 lg:col-span-3">
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">{{ __('Buy Price/Unit') }}</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">UGx</span>
                                    <input type="text" name="products[0][purchase_price]" class="per-piece-purchase number-input w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>

                            <!-- Per-piece Sell -->
                            <div class="sm:col-span-1 lg:col-span-3">
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">{{ __('Sell Price/Unit') }}</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">UGx</span>
                                    <input type="text" name="products[0][price]" class="per-piece-sell number-input w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="0">
                                </div>
                            </div>

                            <!-- Profit Margin Display -->
                            <div class="sm:col-span-2 lg:col-span-12 mt-2 profit-display hidden">
                                <div class="bg-gradient-to-r from-green-50 to-blue-50 border border-green-200 rounded-xl p-4">
                                    <div class="grid grid-cols-3 gap-4">
                                        <div class="text-center">
                                            <div class="text-xs font-medium text-gray-500 mb-0.5">{{ __('Profit/Unit') }}</div>
                                            <div class="profit-amount text-lg font-bold text-blue-700">UGx 0</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-xs font-medium text-gray-500 mb-0.5">{{ __('Margin') }}</div>
                                            <div class="profit-margin text-lg font-bold text-green-700">0%</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-xs font-medium text-gray-500 mb-0.5">{{ __('Total Profit') }}</div>
                                            <div class="total-profit text-lg font-bold text-purple-700">UGx 0</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 flex flex-wrap gap-3">
                    <button type="button" id="add-product" class="inline-flex bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md items-center gap-2 font-semibold text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        {{ __('Add Product') }}
                    </button>
                    <button type="submit" class="inline-flex bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md items-center gap-2 font-semibold text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                        </svg>
                        {{ __('Upload Inventory') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scanner Modal -->
<div id="scanner-modal" class="fixed inset-0 bg-black bg-opacity-75 hidden items-center justify-center z-50 p-3 sm:p-4">
    <div class="bg-white p-3 sm:p-8 rounded-lg sm:rounded-2xl shadow-2xl relative max-w-2xl w-full mx-auto max-h-[90vh] overflow-y-auto">
        <div class="text-center mb-3 sm:mb-6">
            <h3 class="text-lg sm:text-2xl font-semibold text-gray-900 flex items-center justify-center gap-2 sm:gap-3">
                <svg class="w-5 h-5 sm:w-8 sm:h-8 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
                <span>{{ __('Barcode Scanner') }}</span>
            </h3>
            <p class="text-xs sm:text-base text-gray-600 mt-1 sm:mt-2">{{ __('Position barcode in view') }}</p>
        </div>

        <!-- Camera Container -->
            <div class="relative bg-gray-900 rounded-lg sm:rounded-xl overflow-hidden mx-auto" style="max-width: 100%; aspect-ratio: 16/9;">
            <video id="barcode-video" class="w-full h-full object-contain" autoplay playsinline muted></video>

            <!-- Scanner Overlay -->
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="border-2 border-white border-dashed rounded-lg w-40 h-20 sm:w-64 sm:h-32 flex items-center justify-center">
                    <div class="text-white text-xs text-center">
                        <svg class="w-5 h-5 sm:w-8 sm:h-8 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                        {{ __('Align Barcode here') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Scanner Controls -->
        <div class="mt-3 sm:mt-6 text-center">
            <p id="scanner-feedback" class="text-xs sm:text-sm min-h-6 mb-3 sm:mb-4"></p>
            <div class="flex flex-col gap-2 sm:gap-3">
                <button id="toggle-torch"
                    class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-3 rounded-xl transition-colors flex items-center justify-center gap-2 font-semibold text-sm shadow-md hover:shadow-lg w-full hidden">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                    {{ __('Torch') }}
                </button>
                <button id="close-scanner" class="bg-red-600 hover:bg-red-700 text-white px-5 py-3 rounded-xl transition-colors flex items-center justify-center gap-2 font-semibold text-sm shadow-md hover:shadow-lg w-full">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    {{ __('Close Scanner') }}
                    <span class="block sm:hidden text-xs opacity-75 mt-0.5">{{ __('Tap to close') }}</span>
                </button>
                <button id="retry-scan" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-xl transition-colors flex items-center justify-center gap-2 font-semibold text-sm shadow-md hover:shadow-lg w-full hidden">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    {{ __('Scan Again') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/@zxing/library@latest"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let productCount = 1;
    const codeReader = new ZXing.BrowserMultiFormatReader();
    let currentScanner = null;
    let torchAvailable = false;
    let torchOn = false;
    let currentStream = null;
    let selectedDeviceId = null;

    // Add new product
    document.getElementById('add-product').addEventListener('click', function() {
        const container = document.getElementById('products-container');
        const template = document.querySelector('.product-item').cloneNode(true);
        template.setAttribute('data-index', productCount);
        template.querySelectorAll('input, select').forEach(input => {
            const name = input.getAttribute('name').replace('[0]', `[${productCount}]`);
            input.setAttribute('name', name);
            if (input.classList.contains('sku-input')) input.value = '';
            if (input.classList.contains('quantity-input')) input.value = '';
            if (input.classList.contains('unit-select')) input.value = 'piece';
            if (input.classList.contains('per-piece-purchase') || input.classList.contains('per-piece-sell')) {
                input.value = '';
                input.readOnly = false;
                input.classList.remove('bg-blue-50');
            }
            if (input.classList.contains('bulk-purchase') || input.classList.contains('bulk-sell')) {
                input.value = '';
            }
        });
        template.querySelectorAll('.bulk-group').forEach(g => g.classList.add('hidden'));
        template.querySelector('.profit-display').classList.add('hidden');
        const removeBtn = template.querySelector('.remove-product');
        removeBtn.disabled = false;
        removeBtn.setAttribute('aria-disabled', 'false');
        container.appendChild(template);
        productCount++;
        attachEventListeners(template);
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-product') && !e.target.closest('.remove-product').disabled) {
            const item = e.target.closest('.product-item');
            if (document.querySelectorAll('.product-item').length > 1) item.remove();
        }
    });

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('unit-select')) {
            const item = e.target.closest('.product-item');
            const unit = e.target.value;
            const isBulk = unit !== 'piece';
            item.querySelectorAll('.bulk-group').forEach(g => g.classList.toggle('hidden', !isBulk));
            item.querySelectorAll('.bulk-label').forEach(l => {
                l.textContent = unit === 'dozen' ? '{{ __("(per dozen)") }}' : unit === 'carton' ? '{{ __("(per carton)") }}' : '';
            });
            const perPiece = item.querySelector('.per-piece-purchase');
            const perSell = item.querySelector('.per-piece-sell');
            perPiece.readOnly = isBulk;
            perSell.readOnly = isBulk;
            perPiece.classList.toggle('bg-blue-50', isBulk);
            perSell.classList.toggle('bg-blue-50', isBulk);
            calculatePrices(item);
        }
    });

    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('number-input')) formatNumberInput(e.target);
        if (['bulk-purchase','bulk-sell','per-piece-purchase','per-piece-sell','quantity-input'].some(c => e.target.classList.contains(c))) {
            calculatePrices(e.target.closest('.product-item'));
        }
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.scan-btn')) openScanner(e.target.closest('.scan-btn'));
    });

    document.getElementById('close-scanner').addEventListener('click', closeScanner);
    document.getElementById('retry-scan').addEventListener('click', async function() {
        document.getElementById('scanner-feedback').textContent = '';
        this.classList.add('hidden');
        document.getElementById('toggle-torch').classList.add('hidden');
        torchOn = false;
        try { await startScanning(); } catch (e) { /* handled inside */ }
    });

    document.getElementById('toggle-torch').addEventListener('click', function() {
        if (!currentStream) return;
        const track = currentStream.getVideoTracks()[0];
        if (track && track.applyConstraints) {
            torchOn = !torchOn;
            track.applyConstraints({ advanced: [{ torch: torchOn }] }).catch(() => {});
        }
    });

    function attachEventListeners(element) {
        element.querySelector('.unit-select').addEventListener('change', function(e) {
            const item = e.target.closest('.product-item');
            const unit = e.target.value;
            const isBulk = unit !== 'piece';
            item.querySelectorAll('.bulk-group').forEach(g => g.classList.toggle('hidden', !isBulk));
            item.querySelectorAll('.bulk-label').forEach(l => {
                l.textContent = unit === 'dozen' ? '{{ __("(per dozen)") }}' : unit === 'carton' ? '{{ __("(per carton)") }}' : '';
            });
            const perPiece = item.querySelector('.per-piece-purchase');
            const perSell = item.querySelector('.per-piece-sell');
            perPiece.readOnly = isBulk;
            perSell.readOnly = isBulk;
            perPiece.classList.toggle('bg-blue-50', isBulk);
            perSell.classList.toggle('bg-blue-50', isBulk);
            calculatePrices(item);
        });
        element.querySelectorAll('.number-input').forEach(input => {
            input.addEventListener('input', function() {
                formatNumberInput(this);
                calculatePrices(element);
            });
        });
        element.querySelector('.scan-btn').addEventListener('click', function(e) {
            openScanner(e.target.closest('.scan-btn'));
        });
    }

    function formatNumberInput(input) {
        let v = input.value.replace(/,/g, '');
        if (!isNaN(v) && v !== '') input.value = parseInt(v).toLocaleString('en-US');
    }

    function calculatePrices(item) {
        const unit = item.querySelector('.unit-select').value;
        const qty = parseInt(item.querySelector('.quantity-input').value) || 0;
        const bulkP = parseFloat(item.querySelector('.bulk-purchase')?.value.replace(/,/g, '')) || 0;
        const bulkS = parseFloat(item.querySelector('.bulk-sell')?.value.replace(/,/g, '')) || 0;
        const ppInput = item.querySelector('.per-piece-purchase');
        const spInput = item.querySelector('.per-piece-sell');
        const profitEl = item.querySelector('.profit-display');
        let pp, sp;
        if (unit === 'piece') {
            pp = parseFloat(ppInput.value.replace(/,/g, '')) || 0;
            sp = parseFloat(spInput.value.replace(/,/g, '')) || 0;
        } else {
            const perUnit = unit === 'dozen' ? 12 : 24;
            pp = bulkP / perUnit;
            sp = bulkS / perUnit;
            ppInput.value = parseInt(pp).toLocaleString('en-US');
            spInput.value = parseInt(sp).toLocaleString('en-US');
        }
        if (pp > 0 && sp > 0) {
            const profitPer = sp - pp;
            const margin = (profitPer / pp) * 100;
            item.querySelector('.profit-amount').textContent = `UGx ${parseInt(profitPer).toLocaleString('en-US')}`;
            item.querySelector('.profit-margin').textContent = `${margin.toFixed(1)}%`;
            item.querySelector('.total-profit').textContent = `UGx ${parseInt(profitPer * qty).toLocaleString('en-US')}`;
            profitEl.classList.remove('hidden');
        } else {
            profitEl.classList.add('hidden');
        }
    }

    async function openScanner(button) {
        currentScanner = button;
        const modal = document.getElementById('scanner-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.getElementById('toggle-torch').classList.add('hidden');
        torchOn = false;
        setFeedback('{{ __('Initializing camera...') }}', 'text-blue-600');
        await startScanning();
    }

    function setFeedback(text, color) {
        const fb = document.getElementById('scanner-feedback');
        fb.textContent = text;
        fb.className = `text-xs sm:text-sm ${color} min-h-6 mb-3 sm:mb-4`;
    }

    function stopMediaTracks() {
        if (currentStream) {
            currentStream.getTracks().forEach(t => t.stop());
            currentStream = null;
        }
    }

    async function startScanning() {
        const video = document.getElementById('barcode-video');
        stopMediaTracks();
        document.getElementById('toggle-torch').classList.add('hidden');
        torchOn = false;

        try {
            // Prefer rear camera with environment facing mode
            const devices = await navigator.mediaDevices.enumerateDevices();
            const cameras = devices.filter(d => d.kind === 'videoinput');
            const rearCam = cameras.find(d => /back|environment|rear/i.test(d.label));
            selectedDeviceId = rearCam ? rearCam.deviceId : (cameras[0]?.deviceId || null);

            const constraints = {
                video: {
                    deviceId: selectedDeviceId ? { exact: selectedDeviceId } : undefined,
                    facingMode: selectedDeviceId ? undefined : 'environment',
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                },
                audio: false
            };

            currentStream = await navigator.mediaDevices.getUserMedia(constraints);
            video.srcObject = currentStream;

            // Check torch availability
            const track = currentStream.getVideoTracks()[0];
            if (track && track.getCapabilities) {
                const caps = track.getCapabilities();
                torchAvailable = !!(caps && caps.torch);
                if (torchAvailable) document.getElementById('toggle-torch').classList.remove('hidden');
            }

            codeReader.decodeFromVideoElement(video, (result, err) => {
                if (result) {
                    const item = currentScanner.closest('.product-item');
                    const sku = result.text;
                    item.querySelector('.sku-input').value = sku;
                    setFeedback('{{ __("✅ Barcode scanned! Looking up product...") }}', 'text-green-600');
                    fetch(`/inventory/lookup/${encodeURIComponent(sku)}`)
                        .then(r => r.json())
                        .then(data => {
                            if (data.found) {
                                item.querySelector('input[name$="[name]"]').value = data.name;
                                if (data.quantity) item.querySelector('.quantity-input').value = data.quantity;
                                if (data.unit) {
                                    item.querySelector('.unit-select').value = data.unit;
                                    item.querySelector('.unit-select').dispatchEvent(new Event('change'));
                                }
                                if (data.purchase_price) {
                                    item.querySelector('.per-piece-purchase').value = data.purchase_price;
                                    if (data.purchase_price_bulk) item.querySelector('.bulk-purchase').value = data.purchase_price_bulk;
                                }
                                if (data.price) {
                                    item.querySelector('.per-piece-sell').value = data.price;
                                    if (data.selling_price_bulk) item.querySelector('.bulk-sell').value = data.selling_price_bulk;
                                }
                                calculatePrices(item);
                                setFeedback('{{ __("✅ Product auto-filled!") }}', 'text-green-600');
                            } else {
                                setFeedback('{{ __("✅ Barcode scanned!") }}', 'text-green-600');
                            }
                        })
                        .catch(() => setFeedback('{{ __("✅ Barcode scanned!") }}', 'text-green-600'));
                    document.getElementById('retry-scan').classList.remove('hidden');
                    document.getElementById('toggle-torch').classList.add('hidden');
                    if (torchOn) { torchOn = false; }
                    setTimeout(closeScanner, 1500);
                }
                if (err && !(err instanceof ZXing.NotFoundException)) {
                    setFeedback('{{ __('Scanner error:') }} ' + err.message, 'text-red-600');
                }
            });
        } catch (err) {
            if (err.name === 'NotAllowedError') {
                setFeedback('{{ __("Camera access denied. Please allow camera permissions in your browser settings.") }}', 'text-red-600');
            } else if (err.name === 'NotFoundError') {
                setFeedback('{{ __("No camera found on this device.") }}', 'text-red-600');
            } else {
                setFeedback('{{ __("Camera error:") }} ' + err.message, 'text-red-600');
            }
        }
    }

    function closeScanner() {
        const modal = document.getElementById('scanner-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        codeReader.reset();
        stopMediaTracks();
        document.getElementById('retry-scan').classList.add('hidden');
        document.getElementById('toggle-torch').classList.add('hidden');
        torchOn = false;
    }

    // Close modal on backdrop click
    document.getElementById('scanner-modal').addEventListener('click', function(e) {
        if (e.target === this) closeScanner();
    });

    attachEventListeners(document.querySelector('.product-item'));
});
</script>

<style>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.product-item {
    animation: fadeInUp 0.5s ease-out;
}

input[type="number"] {
    -moz-appearance: textfield;
}

input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
#scanner-modal {
    animation: fadeInUp 0.3s ease-out;
}

::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

@media (max-width: 640px) {
    .product-item {
        padding: 0.75rem;
    }
    button {
        min-height: 40px;
    }
}

@media (hover: none) and (pointer: coarse) {
    button {
        min-height: 44px;
    }
}
</style>
@endsection

