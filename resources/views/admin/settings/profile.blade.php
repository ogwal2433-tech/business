@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-user-cog me-2"></i> {{ __('User Settings') }}</h4>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-sm">{{ __('Back') }}</a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">{{ __('Full Name') }}</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">{{ __('Username') }}</label>
                        <input type="text" name="username" class="form-control" value="{{ old('username', $user->username) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">{{ __('Email Address') }}</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">{{ __('Business Name') }}</label>
                        <input type="text" class="form-control" value="{{ $user->business_name }}" readonly>
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="fw-semibold mb-3">{{ __('Password') }}</h5>

                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">{{ __('Current Password') }}</label>
                        <input type="password" name="current_password" class="form-control" placeholder="Leave blank to keep current">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">{{ __('New Password') }}</label>
                        <input type="password" name="new_password" class="form-control" placeholder="Min 6 characters">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">{{ __('Confirm Password') }}</label>
                        <input type="password" name="new_password_confirmation" class="form-control" placeholder="Confirm new password">
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="fas fa-save me-2"></i>{{ __('Save') }}
                    </button>
                </div>
            </form>

            {{-- Clear System Data --}}
            @if($user->isAdmin())
            <hr class="my-5">
            <div class="border border-danger rounded-3 p-4 bg-danger-subtle">
                <h5 class="text-danger fw-semibold mb-1"><i class="bi bi-exclamation-triangle"></i> {{ __('Danger Zone') }}</h5>
                <p class="text-muted small mb-3">{{ __('Permanently clear your business data within a date range. This action cannot be undone.') }}</p>

                <form method="POST" action="{{ route('admin.settings.clear-data') }}" onsubmit="return confirm('{{ __('Are you sure? This permanently deletes all selected data in the date range.') }}')">
                    @csrf

                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">{{ __('From Date') }}</label>
                            <input type="date" name="from_date" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">{{ __('To Date') }}</label>
                            <input type="date" name="to_date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ __('Data to Clear') }}</label>
                            <select name="data_types[]" multiple class="form-control" size="4" required>
                                <option value="sales">{{ __('Sales') }}</option>
                                <option value="expenses">{{ __('Expenses') }}</option>
                                <option value="credit_sales">{{ __('Credit Sales') }}</option>
                                <option value="inventory_history">{{ __('Inventory History') }}</option>
                            </select>
                            <div class="form-text text-muted small">{{ __('Hold Ctrl/Cmd to select multiple') }}</div>
                        </div>
                    </div>

                    <div class="row g-3 mt-2 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-danger">{{ __('Type DELETE to confirm') }}</label>
                            <input type="text" name="confirmation" class="form-control" placeholder="{{ __('Type DELETE') }}" required pattern="DELETE">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-trash3"></i> {{ __('Clear Data') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
