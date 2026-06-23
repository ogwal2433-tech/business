<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ __('Choose a Plan') }} - SmartBiz</title>
    <link rel="stylesheet" href="styles.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to top right, #cfe8ff, #ffffff, #cbdff7);
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        .page-container {
            max-width: 960px;
            margin: 0 auto;
        }

        .error-box {
            max-width: 600px;
            margin: 0 auto 1.5rem;
            background: #fee2e2;
            color: #b91c1c;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 0.9rem;
            border: 1px solid #fca5a5;
        }

        .info-box {
            max-width: 600px;
            margin: 0 auto 1.5rem;
            background: #dbeafe;
            color: #1e40af;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 0.9rem;
            border: 1px solid #93c5fd;
        }

        .success-box {
            max-width: 600px;
            margin: 0 auto 1.5rem;
            background: #dcfce7;
            color: #166534;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 0.9rem;
            border: 1px solid #86efac;
        }

        /* Pending Approval State */
        .pending-state {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            padding: 2.5rem 2rem;
            max-width: 500px;
            margin: 0 auto 2rem;
            text-align: center;
        }

        .pending-icon {
            width: 72px;
            height: 72px;
            background: #fef3c7;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
        }

        .pending-icon i {
            font-size: 2rem;
            color: #d97706;
        }

        .pending-state h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .pending-state p {
            color: #64748b;
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .pending-state .plan-name {
            font-weight: 700;
            color: #2563eb;
        }

        .pending-spinner {
            display: inline-block;
            width: 40px;
            height: 40px;
            border: 3px solid #e2e8f0;
            border-top-color: #2563eb;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-bottom: 1rem;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .header-icon {
            width: 64px;
            height: 64px;
            background: rgba(37, 99, 235, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        .header-icon i {
            font-size: 1.75rem;
            color: #2563eb;
        }

        .header h1 {
            color: #1e293b;
            font-size: 1.75rem;
            font-weight: 800;
        }

        .header p {
            color: #64748b;
            font-size: 0.9rem;
            max-width: 480px;
            margin: 0.5rem auto 0;
        }

        /* Plan Grid */
        .plan-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        @media (min-width: 640px) {
            .plan-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (min-width: 768px) {
            .plan-grid { grid-template-columns: repeat(3, 1fr); }
        }

        .plan-card {
            position: relative;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: box-shadow 0.2s;
        }

        .plan-card:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .popular-badge {
            background: linear-gradient(to right, #2563eb, #6366f1);
            color: white;
            font-size: 0.7rem;
            font-weight: 700;
            text-align: center;
            padding: 0.35rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .card-body {
            padding: 1.5rem 1.25rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .plan-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.75rem;
        }

        .plan-icon.basic { background: #f1f5f9; color: #475569; }
        .plan-icon.popular { background: #dbeafe; color: #2563eb; }
        .plan-icon.premium { background: #f3e8ff; color: #7c3aed; }

        .plan-name {
            text-align: center;
            font-size: 1.2rem;
            font-weight: 700;
            color: #1e293b;
        }

        .plan-desc {
            text-align: center;
            font-size: 0.8rem;
            color: #64748b;
            margin-top: 0.25rem;
        }

        .plan-price {
            text-align: center;
            padding: 0.75rem 0;
        }

        .plan-price .amount {
            font-size: 2rem;
            font-weight: 800;
            color: #1e293b;
        }

        .plan-price .period {
            color: #64748b;
            font-size: 0.85rem;
        }

        .plan-features {
            list-style: none;
            padding: 0;
            margin: 0 0 1rem;
            flex: 1;
        }

        .plan-features li {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            font-size: 0.85rem;
            color: #475569;
            padding: 0.35rem 0;
        }

        .plan-features li i {
            margin-top: 0.2rem;
            flex-shrink: 0;
        }

        .plan-features li i.fa-check-circle { color: #22c55e; }
        .plan-features li i.fa-users,
        .plan-features li i.fa-calendar { color: #2563eb; }

        .btn-subscribe {
            display: block;
            width: 100%;
            padding: 0.75rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.9rem;
            border: 2px solid #e2e8f0;
            background: white;
            color: #1e293b;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
        }

        .btn-subscribe:hover {
            border-color: #2563eb;
            color: #2563eb;
        }

        .btn-subscribe.primary {
            background: linear-gradient(to right, #2563eb, #6366f1);
            color: white;
            border: none;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
        }

        .btn-subscribe.primary:hover {
            background: linear-gradient(to right, #1d4ed8, #4f46e5);
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.4);
        }

        /* Previous Plan Card */
        .prev-plan {
            max-width: 480px;
            margin: 0 auto 1.5rem;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .prev-plan-icon {
            width: 40px;
            height: 40px;
            background: #f1f5f9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .prev-plan-icon i { color: #64748b; }

        .prev-plan-body { flex: 1; min-width: 0; }

        .prev-plan-body .label {
            font-size: 0.8rem;
            color: #64748b;
        }

        .prev-plan-body .value {
            font-weight: 600;
            color: #1e293b;
            font-size: 0.9rem;
        }

        .prev-plan-body .value span {
            font-weight: 400;
            font-size: 0.8rem;
            color: #94a3b8;
        }

        .prev-plan-link {
            font-size: 0.85rem;
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
            flex-shrink: 0;
            transition: color 0.2s;
        }

        .prev-plan-link:hover { color: #1d4ed8; }

        /* Logout */
        .logout-link {
            text-align: center;
        }

        .logout-link a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.85rem;
            transition: color 0.2s;
        }

        .logout-link a:hover { color: #475569; }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            max-width: 400px;
            margin: 0 auto;
        }

        .empty-icon {
            width: 64px;
            height: 64px;
            background: #f1f5f9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        .empty-icon i { font-size: 1.5rem; color: #94a3b8; }

        .empty-state p { color: #64748b; font-size: 0.9rem; }

        /* Footer */
        footer {
            text-align: center;
            padding: 1.5rem 0 0.5rem;
            font-size: 0.8rem;
            color: #94a3b8;
        }

        /* Fade-in animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.4s ease-out;
        }
    </style>
</head>
<body>
    <div class="page-container">
        @if(session('error'))
            <div class="error-box">{{ session('error') }}</div>
        @endif
        @if(session('info'))
            <div class="info-box">{{ session('info') }}</div>
        @endif
        @if(session('success'))
            <div class="success-box">{{ session('success') }}</div>
        @endif

        @if($pendingSub ?? false)
            <!-- Pending Approval State -->
            <div class="pending-state fade-in">
                <div class="pending-spinner"></div>
                <div class="pending-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h2>{{ __('Awaiting Approval') }}</h2>
                <p>{{ __('You have chosen the') }} <span class="plan-name">{{ $pendingSub->plan?->name ?? '' }}</span> {{ __('plan. Your subscription is pending approval from the system administrator.') }}</p>
                <p class="mt-4 text-sm">{{ __('You will be able to log in once your subscription is approved.') }}</p>
                <div class="mt-6 pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-400 mb-3">{{ __('If approval is delayed, contact us:') }}</p>
                    <div class="flex items-center justify-center gap-3">
                        <a href="tel:0787860378" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 text-sm font-semibold transition-colors shadow-sm">
                            <i class="fas fa-phone"></i> 0787 860 378
                        </a>
                        <a href="https://wa.me/255787860378" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-xl hover:bg-green-700 text-sm font-semibold transition-colors shadow-sm">
                            <i class="fab fa-whatsapp"></i> {{ __('WhatsApp') }}
                        </a>
                    </div>
                </div>
                <div class="mt-4 text-xs text-gray-400">
                    {{ __('Requested') }}: {{ $pendingSub->created_at->format('d M Y H:i') }}
                </div>
            </div>
            <script>
            (function() {
                var poll = setInterval(function() {
                    fetch('{{ route('admin.subscription.status') }}')
                        .then(function(r) { return r.json(); })
                        .then(function(data) {
                            if (!data.pending) {
                                clearInterval(poll);
                                window.location.href = '{{ route('admin.dashboard') }}';
                            }
                        })
                        .catch(function() {});
                }, 5000);
            })();
            </script>
        @else
            <!-- Header -->
            <div class="header fade-in">
                <div class="header-icon">
                    <i class="fas fa-crown"></i>
                </div>
                <h1>{{ __('Choose a Plan') }}</h1>
                <p>{{ __('Your subscription has expired. Select a plan below to continue using SmartBiz.') }}</p>
            </div>

            <!-- Plan Cards -->
            @php $plans = $plans ?? collect(); @endphp
            <div class="plan-grid fade-in">
                @forelse($plans as $plan)
                    @if($plan->slug === 'free-trial') @continue @endif
                    <div class="plan-card">
                        @if($loop->index === 1)
                            <div class="popular-badge">{{ __('Most Popular') }}</div>
                        @endif

                        <div class="card-body" style="{{ $loop->index === 1 ? 'padding-top: 1rem;' : '' }}">
                            <div class="plan-icon {{ $loop->index === 0 ? 'basic' : ($loop->index === 1 ? 'popular' : 'premium') }}">
                                @if($loop->index === 0)
                                    <i class="fas fa-rocket"></i>
                                @elseif($loop->index === 1)
                                    <i class="fas fa-star"></i>
                                @else
                                    <i class="fas fa-gem"></i>
                                @endif
                            </div>

                            <div class="plan-name">{{ $plan->name }}</div>
                            @if($plan->description)
                                <div class="plan-desc">{{ $plan->description }}</div>
                            @endif

                            <div class="plan-price">
                                <span class="amount">{{ businessCurrency() }} {{ number_format($plan->price) }}</span>
                                <span class="period"> / {{ $plan->duration_days }} {{ __('days') }}</span>
                            </div>

                            <ul class="plan-features">
                                <li>
                                    <i class="fas fa-users"></i>
                                    <span>{{ __('Up to') }} <strong>{{ $plan->max_employees > 0 ? $plan->max_employees : __('Unlimited') }}</strong> {{ __('employees') }}</span>
                                </li>
                                <li>
                                    <i class="fas fa-calendar"></i>
                                    <span><strong>{{ $plan->duration_days }}</strong> {{ __('days of access') }}</span>
                                </li>
                                @if($plan->features && is_array($plan->features))
                                    @foreach($plan->features as $feature)
                                        <li>
                                            <i class="fas fa-check-circle"></i>
                                            <span>{{ $feature }}</span>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>

                            <form method="POST" action="{{ route('admin.subscription.subscribe') }}">
                                @csrf
                                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                <button type="submit" class="btn-subscribe {{ $loop->index === 1 ? 'primary' : '' }}">
                                    @if($loop->index === 0)
                                        {{ __('Get Started') }}
                                    @elseif($loop->index === 1)
                                        {{ __('Choose Pro') }}
                                    @else
                                        {{ __('Choose Plan') }}
                                    @endif
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="empty-state" style="grid-column: 1 / -1;">
                        <div class="empty-icon"><i class="fas fa-box-open"></i></div>
                        <p>{{ __('No plans available. Please contact the system administrator.') }}</p>
                    </div>
                @endforelse
            </div>

            <!-- Previous Plan -->
            @php
                $user = auth()->user();
                $currentSub = $user->subscription;
            @endphp
            @if($currentSub && $currentSub->plan)
                <div class="prev-plan fade-in">
                    <div class="prev-plan-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="prev-plan-body">
                        <div class="label">{{ __('Previous Plan') }}</div>
                        <div class="value">{{ $currentSub->plan->name }} <span>({{ __('expired') }} {{ $currentSub->end_date?->diffForHumans() ?? '' }})</span></div>
                    </div>
                    <a href="{{ route('admin.subscription.my') }}" class="prev-plan-link">
                        {{ __('Details') }} <i class="fas fa-arrow-right" style="font-size: 0.75rem;"></i>
                    </a>
                </div>
            @endif
        @endif

        <!-- Logout -->
        <div class="logout-link fade-in">
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> {{ __('Logout') }}
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
        </div>

        <footer>
            &copy; {{ date('Y') }} SmartBiz. {{ __('All rights reserved.') }}
        </footer>
    </div>
</body>
</html>
