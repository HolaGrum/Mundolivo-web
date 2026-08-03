@extends('appshell::layouts.private')

@section('title', __('Dashboard'))

@section('content')
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar avatar-lg bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center">
                            <i class="zmdi zmdi-shopping-cart fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-1">{{ __('Orders') }}</h6>
                        <h3 class="mb-0">{{ $orderCount }}</h3>
                    </div>
                </div>
                <div class="mt-3">
                    <small class="text-muted">
                        <span class="text-success fw-semibold">{{ $pendingOrders }}</span> {{ __('Pending') }} &middot;
                        <span class="text-warning fw-semibold">{{ $processingOrders }}</span> {{ __('Processing') }} &middot;
                        <span class="text-success fw-semibold">{{ $completedOrders }}</span> {{ __('Completed') }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar avatar-lg bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center">
                            <i class="zmdi zmdi-box fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-1">{{ __('Products') }}</h6>
                        <h3 class="mb-0">{{ $productCount }}</h3>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('vanilo.admin.product.index') }}" class="text-decoration-none small">
                        {{ __('View all products') }} <i class="zmdi zmdi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar avatar-lg bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center">
                            <i class="zmdi zmdi-account fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-1">{{ __('Users') }}</h6>
                        <h3 class="mb-0">{{ $userCount }}</h3>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('appshell.user.index') }}" class="text-decoration-none small">
                        {{ __('Manage users') }} <i class="zmdi zmdi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar avatar-lg bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center">
                            <i class="zmdi zmdi-swap-alt fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-1">{{ __('Channels') }}</h6>
                        <h3 class="mb-0">{{ $channelCount }}</h3>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('vanilo.admin.channel.index') }}" class="text-decoration-none small">
                        {{ __('Manage channels') }} <i class="zmdi zmdi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 d-flex align-items-center justify-content-between">
                <h5 class="mb-0">{{ __('Quick Actions') }}</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6 col-lg-4">
                        <a href="{{ route('vanilo.admin.product.create') }}" class="btn btn-outline-primary w-100 py-3 d-flex align-items-center justify-content-center gap-2">
                            <i class="zmdi zmdi-plus-circle"></i>
                            {{ __('New Product') }}
                        </a>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <a href="{{ route('vanilo.admin.order.index') }}" class="btn btn-outline-primary w-100 py-3 d-flex align-items-center justify-content-center gap-2">
                            <i class="zmdi zmdi-format-list-bulleted"></i>
                            {{ __('Orders') }}
                        </a>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <a href="{{ route('appshell.customer.index') }}" class="btn btn-outline-primary w-100 py-3 d-flex align-items-center justify-content-center gap-2">
                            <i class="zmdi zmdi-accounts"></i>
                            {{ __('Customers') }}
                        </a>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <a href="{{ route('vanilo.admin.promotion.index') }}" class="btn btn-outline-primary w-100 py-3 d-flex align-items-center justify-content-center gap-2">
                            <i class="zmdi zmdi-gift"></i>
                            {{ __('Promotions') }}
                        </a>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <a href="{{ route('vanilo.admin.channel.index') }}" class="btn btn-outline-primary w-100 py-3 d-flex align-items-center justify-content-center gap-2">
                            <i class="zmdi zmdi-swap-alt"></i>
                            {{ __('Channels') }}
                        </a>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <a href="{{ route('appshell.settings.index') }}" class="btn btn-outline-primary w-100 py-3 d-flex align-items-center justify-content-center gap-2">
                            <i class="zmdi zmdi-settings"></i>
                            {{ __('Settings') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0">
                <h5 class="mb-0">{{ __('My Account') }}</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px">
                        <i class="zmdi zmdi-account fs-5"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">{{ $user->name }}</h6>
                        <small class="text-muted">{{ $user->email }}</small>
                    </div>
                </div>
                <hr>
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('appshell.user.index') }}" class="text-decoration-none">
                        <i class="zmdi zmdi-accounts me-2"></i> {{ __('Users') }}
                    </a>
                    <a href="{{ route('vanilo.admin.tax-rate.index') }}" class="text-decoration-none">
                        <i class="zmdi zmdi-money-box me-2"></i> {{ __('Tax Rates') }}
                    </a>
                    <a href="{{ route('vanilo.admin.shipping-method.index') }}" class="text-decoration-none">
                        <i class="zmdi zmdi-truck me-2"></i> {{ __('Shipping Methods') }}
                    </a>
                    <a href="{{ route('vanilo.admin.payment-method.index') }}" class="text-decoration-none">
                        <i class="zmdi zmdi-credit-card me-2"></i> {{ __('Payment Methods') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
