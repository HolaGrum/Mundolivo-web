<header class="appshell-header container-fluid">

    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <a href="#" id="hamburger" class="me-2">
                {!! icon('hamburger') !!}
            </a>
            <h1 class="mb-0">@yield('title')</h1>
        </div>
        <nav class="d-flex align-items-center">
            @stack('page-actions')
            @if ($appshell->quick_links['enabled'])
                <span class="text-secondary mx-1">&nbsp;</span>
                <button class="btn btn-sm border-0 dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false" id="quicklinks" type="button">
                    {!! icon('quick-links', 'muted') !!}
                </button>

                <ul class="dropdown-menu" aria-labelledby="quicklinks">
                    <li><h6 class="dropdown-header my-2">Enlaces rápidos</h6></li>
                    @foreach(helper('quickLinks')->links() as $item)
                        <li><a class="dropdown-item" href="{{ $item['link'] }}">{{ $item['label'] }}</a></li>
                    @endforeach
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('appshell.quicklinks.index') }}">Configurar enlaces rápidos...</a></li>
                </ul>
            @endif
        </nav>
    </div>

</header>

<div id="mobile-sidebar-overlay"></div>
<div id="mobile-menu">
    <div class="mobile-menu-header d-flex justify-content-between align-items-center p-3">
        <a href="{{ $appshell->url }}" class="text-white text-decoration-none d-flex align-items-center">
            <img src="{{ asset('/assets/logo.svg') }}" class="sidebar-logo-img me-2" alt="Vanilo" style="max-height: 28px; width: auto;" />
            <h5 class="mb-0 sidebar-logo-text">{{ $appshell->name }}</h5>
        </a>
        <a href="#" id="mobile-menu-close" class="text-white">
            <i class="zmdi zmdi-close" style="font-size:24px"></i>
        </a>
    </div>
    <nav class="nav sidebar-nav flex-column" id="appshell-mobile-nav">
        @include('appshell::layouts.default._nav')
    </nav>
</div>

@if ($appshell->isSearchEnabled())
    @include('appshell::layouts.default._search')
@endif
