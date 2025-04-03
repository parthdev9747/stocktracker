<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="index" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('build/images/logo-dark.png') }}" alt="" height="17">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="index" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('build/images/logo-light.png') }}" alt="" height="17">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span>Menu</span></li>
                <li class="nav-item">
                    <a class="nav-link menu-link {{ isActiveDropdown(['dashboard']) }}" href="{{ route('dashboard') }}">
                        <i class="ri-dashboard-2-line"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link {{ isActiveDropdown(['market.dashboard']) }}"
                        href="{{ route('market.dashboard') }}">
                        <i class="ri-dashboard-2-line"></i> <span>Market Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link {{ isActiveDropdown(['indices.index']) }}"
                        href="{{ route('indices.index') }}">
                        <i class="ri-dashboard-2-line"></i> <span>Indices</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link {{ isActiveDropdown(['holidays']) }}" href="{{ route('holidays') }}">
                        <i class="ri-dashboard-2-line"></i> <span>Holiday</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link {{ isActiveDropdown(['index-names.index']) }}"
                        href="{{ route('index-names.index') }}">
                        <i class="ri-dashboard-2-line"></i> <span>Index names</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link {{ isActiveDropdown(['symbol.index']) }}"
                        href="{{ route('symbol.index') }}">
                        <i class="ri-dashboard-2-line"></i> <span>Symbol</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link {{ isActiveDropdown(['stock-historical-data.index']) }}"
                        href="{{ route('stock-historical-data.index') }}">
                        <i class="ri-dashboard-2-line"></i> <span>Stock historical data</span>
                    </a>
                </li>
                @canany(['list-role', 'add-role', 'edit-role', 'delete-role', 'list-user', 'add-user', 'edit-user',
                    'delete-user'])
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ isActiveRoute(['user.index', 'user.create', 'user.edit', 'role.index', 'role.create', 'role.edit', 'role.list']) }}"
                            href="#sidebarDashboards" data-bs-toggle="collapse" role="button" aria-expanded="false"
                            aria-controls="sidebarDashboards">
                            <i class="ri-dashboard-2-line"></i> <span>User Management</span>
                        </a>
                        <div class="collapse menu-dropdown {{ isActiveDropdown(['user.index', 'user.create', 'user.edit', 'role.index', 'role.create', 'role.edit', 'role.list', 'permission.index', 'permission.create', 'permission.edit', 'permission.list']) }}"
                            id="sidebarDashboards">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('user.index') }}"
                                        class="nav-link {{ isActiveRoute(['user.index', 'user.edit', 'user.create']) }}">Users</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('role.index') }}"
                                        class="nav-link {{ isActiveRoute(['role.index', 'role.edit', 'role.create']) }}">Roles</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('permission.index') }}"
                                        class="nav-link {{ isActiveRoute(['permission.index', 'permission.edit', 'permission.create']) }}">Permission</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endcan <!-- end Dashboard Menu -->

            </ul>
        </div>
        <!-- Sidebar -->
    </div>
    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>
