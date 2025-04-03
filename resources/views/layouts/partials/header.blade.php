<header class="navbar navbar-expand-md d-print-none">
    <div class="container-xl">
        <div class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
            @yield('page-title')
        </div>
        <div class="navbar-nav flex-row order-md-last d-none d-lg-flex">
            <div class="d-none d-md-flex">
                <div class="nav-item dropdown moonicon">
                    <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1"
                        aria-label="Select language">
                        <!-- Download SVG icon from http://tabler.io/icons/icon/language -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-1" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M4 5h7"></path>
                            <path d="M9 3v2c0 4.418 -2.239 8 -5 8"></path>
                            <path d="M5 9c0 2.144 2.952 3.908 6.7 4"></path>
                            <path d="M12 20l4 -9l4 9"></path>
                            <path d="M19.1 18h-6.2"></path>
                        </svg>
                        @php($languages = ['en' => 'En', 'fr' => 'Fr', 'ar' => 'Ar'])
                        <span>{{ $languages[Session::get('locale', config('app.locale'))] }}</span>
                    </a>
                </div>
                <a href="?theme=dark" class="nav-link px-0 hide-theme-dark moonicon" title="Enable dark mode"
                    data-bs-toggle="tooltip" data-bs-placement="bottom">
                    <!-- Download SVG icon from http://tabler.io/icons/icon/moon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="icon icon-1">
                        <path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z" />
                    </svg>
                </a>
                <a href="?theme=light" class="nav-link px-0 hide-theme-light" title="Enable light mode"
                    data-bs-toggle="tooltip" data-bs-placement="bottom">
                    <!-- Download SVG icon from http://tabler.io/icons/icon/sun -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="icon icon-1">
                        <path d="M12 12m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                        <path
                            d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7" />
                    </svg>
                </a>
            </div>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown"
                    aria-label="Open user menu">
                    <span class="avatar avatar-sm"
                        style="background-image: url('https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=random');"></span>
                    <div class="d-none d-xl-block ps-2">
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <a href="./profile.html" class="dropdown-item">Profile</a>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('mode.switch', 'ltr') }}"
                        class="dropdown-item {{ Session::get('mode') === 'ltr' ? 'active' : '' }}">LTR</a>
                    <a href="{{ route('mode.switch', 'rtl') }}"
                        class="dropdown-item {{ Session::get('mode') === 'rtl' ? 'active' : '' }}">RTL</a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}" class="dropdown-item"
                            onclick="event.preventDefault(); this.closest('form').submit();">Logout</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
