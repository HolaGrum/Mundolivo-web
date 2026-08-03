<div class="sidebar">
    <div class="sidebar-menu">
        <nav class="d-flex justify-content-between align-items-center p-3">
            <a href="{{ $appshell->url }}" class="d-flex align-items-center text-decoration-none">
                <img src="{{ asset('/assets/logo.svg') }}" class="sidebar-logo-img me-2" alt="Vanilo" style="max-height: 36px; width: auto;" />
                <h4 class="sidebar-logo-text mb-0">{{ $appshell->name }}</h4>
            </a>
            @include('appshell::layouts.default._account_menu')

        </nav>
        <nav class="nav sidebar-nav flex-column" id="appshell-sidebar-nav">
            @include('appshell::layouts.default._nav')
        </nav>
        @include('appshell::layouts.default._sidebar_footer')
    </div>
</div>
