@extends('layouts.app')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Inventory Upload Logs') }}
    </h2>
    <a href="{{ route('inventory.upload.form') }}" class="text-sm text-blue-600 hover:text-blue-800 underline">
        &larr; {{ __('Back to Upload') }}
    </a>
</div>

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
                {!! session('success') !!}
            </div>
        @endif

        @if(session('warning'))
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg mb-4">
                {!! session('warning') !!}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            @if($batches->isEmpty())
                <div class="p-8 text-center text-gray-500">
                    {{ __('No upload logs found.') }}
                </div>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Date') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Batch ID') }}</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Total Items') }}</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Skipped') }}</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($batches as $batch)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($batch->uploaded_at)->format('M d, Y h:i A') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 font-mono whitespace-nowrap">
                                    {{ substr($batch->batch_id, 0, 8) }}...
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-center whitespace-nowrap">
                                    {{ $batch->total }}
                                </td>
                                <td class="px-6 py-4 text-sm text-center whitespace-nowrap">
                                    @if($batch->skipped_count > 0)
                                        <span class="text-red-600 font-semibold">{{ $batch->skipped_count }}</span>
                                    @else
                                        <span class="text-green-600">0</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-center whitespace-nowrap">
                                    <button onclick="toggleBatch('{{ $batch->batch_id }}')" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        {{ __('View Details') }}
                                    </button>
                                </td>
                            </tr>
                            <tr id="batch-{{ $batch->batch_id }}" class="hidden bg-gray-50">
                                <td colspan="5" class="px-6 py-4">
                                    @php
                                        $logs = \App\Models\InventoryUploadLog::where('batch_id', $batch->batch_id)
                                            ->where('admin_id', auth()->id())
                                            ->orderBy('created_at')
                                            ->get();
                                    @endphp
                                    <table class="min-w-full">
                                        <thead>
                                            <tr class="text-xs text-gray-500 uppercase">
                                                <th class="px-4 py-2 text-left">{{ __('SKU') }}</th>
                                                <th class="px-4 py-2 text-left">{{ __('Name') }}</th>
                                                <th class="px-4 py-2 text-center">{{ __('Status') }}</th>
                                                <th class="px-4 py-2 text-left">{{ __('Reason') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($logs as $log)
                                                <tr class="border-t border-gray-200">
                                                    <td class="px-4 py-2 text-sm font-mono">{{ $log->sku }}</td>
                                                    <td class="px-4 py-2 text-sm">{{ $log->name }}</td>
                                                    <td class="px-4 py-2 text-sm text-center">
                                                        @if($log->status === 'skipped')
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                                {{ __('Skipped') }}
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                {{ __('Created') }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $log->reason ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="p-4">
                    {{ $batches->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function toggleBatch(batchId) {
    const row = document.getElementById('batch-' + batchId);
    row.classList.toggle('hidden');
}
</script>
@endsection
