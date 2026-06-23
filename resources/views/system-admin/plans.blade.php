@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1">{{ __('Subscription Plans') }}</h1>
            <p class="text-gray-600 text-sm">{{ __('Manage pricing plans and features') }}</p>
        </div>
        <a href="{{ route('system-admin.dashboard') }}" class="mt-3 sm:mt-0 inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> {{ __('Back') }}
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">{{ session('success') }}</div>
    @endif

    <button onclick="openPlanModal()" class="mb-6 inline-flex items-center px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors shadow-lg">
        <i class="fas fa-plus mr-2"></i> {{ __('Create New Plan') }}
    </button>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        @foreach($plans as $plan)
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden {{ $plan->is_active ? '' : 'opacity-70' }}">
                <div class="p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-bold text-gray-900">{{ $plan->name }}</h3>
                        @if(!$plan->is_active)
                            <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">{{ __('Inactive') }}</span>
                        @endif
                    </div>
                    <p class="text-3xl font-bold text-blue-600 mb-1">{{ businessCurrency() }} {{ number_format($plan->price) }}</p>
                    <p class="text-sm text-gray-500 mb-4">/ {{ $plan->duration_days }} {{ __('days') }}</p>

                    <div class="space-y-2 text-sm">
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-users w-5 text-gray-400 mr-2"></i>
                            {{ $plan->max_employees > 0 ? __(':count employees max', ['count' => $plan->max_employees]) : __('Unlimited employees') }}
                        </div>
                        @if($plan->features && is_array($plan->features))
                            @foreach($plan->features as $feature)
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-check w-5 text-green-500 mr-2"></i>
                                    <span>{{ $feature }}</span>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    @if($plan->description)
                        <p class="text-xs text-gray-500 mt-4 italic">{{ $plan->description }}</p>
                    @endif
                </div>
                <div class="px-5 py-3 bg-gray-50 border-t border-gray-200">
                    <button onclick="openEditPlanModal({{ $plan->id }})" class="w-full inline-flex items-center justify-center px-3 py-1.5 border border-blue-300 text-sm font-medium rounded-lg text-blue-700 hover:bg-blue-50 transition-colors">
                        <i class="fas fa-edit mr-1"></i> {{ __('Edit') }}
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Create/Edit Plan Modal -->
<div id="planModal" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 overflow-y-auto py-8" onclick="if(event.target===this)closePlanModal()">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 my-auto" onclick="event.stopPropagation()">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-5 py-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-white" id="planModalTitle"><i class="fas fa-box mr-2"></i> {{ __('Create Plan') }}</h3>
            <button onclick="closePlanModal()" class="text-white/80 hover:text-white text-xl leading-none">&times;</button>
        </div>
        <form method="POST" id="planForm" class="p-5 space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Plan Name') }}</label>
                    <input type="text" name="name" id="plan_name" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Slug') }}</label>
                    <input type="text" name="slug" id="plan_slug" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Price') }}</label>
                    <input type="number" name="price" id="plan_price" step="1" min="0" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Duration (days)') }}</label>
                    <input type="number" name="duration_days" id="plan_duration" min="1" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Max Employees') }}</label>
                    <input type="number" name="max_employees" id="plan_max_emp" min="0" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500" placeholder="0 = unlimited">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Description') }}</label>
                <textarea name="description" id="plan_description" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Features (one per line)') }}</label>
                <textarea name="features" id="plan_features" rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500" placeholder="{{ __('Sales recording') . PHP_EOL . __('Inventory management') . PHP_EOL . __('Basic reports') }}"></textarea>
            </div>
            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="plan_active" value="1" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="plan_active" class="ml-2 text-sm text-gray-700">{{ __('Active') }}</label>
            </div>
            <button type="submit" class="w-full py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors">{{ __('Save Plan') }}</button>
        </form>
    </div>
</div>

<script>
function openPlanModal() {
    document.getElementById('planForm').action = '{{ route('system-admin.plans.store') }}';
    document.getElementById('planForm').method = 'POST';
    document.getElementById('planModalTitle').innerHTML = '<i class="fas fa-box mr-2"></i> {{ __("Create Plan") }}';
    document.getElementById('plan_name').value = '';
    document.getElementById('plan_slug').value = '';
    document.getElementById('plan_price').value = '';
    document.getElementById('plan_duration').value = '30';
    document.getElementById('plan_max_emp').value = '0';
    document.getElementById('plan_description').value = '';
    document.getElementById('plan_features').value = '';
    document.getElementById('plan_active').checked = true;
    document.getElementById('planModal').style.display = 'flex';
}

function openEditPlanModal(id) {
    fetch('/system-admin/plans/' + id + '/edit')
        .then(r => r.json())
        .then(plan => {
            var form = document.getElementById('planForm');
            form.action = '/system-admin/plans/' + id;
            var methodInput = form.querySelector('input[name="_method"]');
            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                form.appendChild(methodInput);
            }
            methodInput.value = 'POST';
            document.getElementById('planModalTitle').innerHTML = '<i class="fas fa-edit mr-2"></i> {{ __("Edit Plan") }}';
            document.getElementById('plan_name').value = plan.name;
            document.getElementById('plan_slug').value = plan.slug;
            document.getElementById('plan_price').value = plan.price;
            document.getElementById('plan_duration').value = plan.duration_days;
            document.getElementById('plan_max_emp').value = plan.max_employees;
            document.getElementById('plan_description').value = plan.description || '';
            document.getElementById('plan_features').value = Array.isArray(plan.features) ? plan.features.join('\n') : '';
            document.getElementById('plan_active').checked = plan.is_active;
            document.getElementById('planModal').style.display = 'flex';
        });
}

function closePlanModal() {
    document.getElementById('planModal').style.display = 'none';
}
</script>
@endsection
