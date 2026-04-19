<div data-active-color="black" data-background-color="white" data-image="" class="app-sidebar">
    <!-- Sidebar Header starts-->
    <div class="sidebar-header">
        <div class="logo clearfix">
            <a href="{{ route('dashboard') }}" class="logo-text float-left">
                <div class="logo-img">
                    <img src="{{ asset('logo.png') }}" style="max-width: 30px;">
                </div>
            </a>
            <a id="sidebarToggle" href="javascript:;" class="nav-toggle d-none d-sm-none d-md-none d-lg-block">
                <i data-toggle="expanded" class="ft-toggle-right toggle-icon"></i>
            </a>
            <a id="sidebarClose" href="javascript:;" class="nav-close d-block d-md-block d-lg-none d-xl-none">
                <i class="ft-x"></i>
            </a>
        </div>
    </div>
    <!-- Sidebar Header Ends-->
    <!-- main menu content-->
    <div class="sidebar-content">
        <div class="nav-container">
            <ul id="main-menu-navigation" data-menu="menu-navigation" class="navigation navigation-main">
                @auth

                    {{-- Dashboard --}}
                    <li class="nav-item @activeClass(['dashboard'])">
                        <a href="{{ route('dashboard') }}">
                            <i class="fa fa-home"></i>
                            <span data-i18n="" class="menu-title">Dashboard</span>
                        </a>
                    </li>

                    {{-- Users (admin/SA only) --}}
                    @if (auth()->user()->isAdmin())
                        <li class="nav-item @activeClass(['admin.users.*'])">
                            <a href="{{ route('admin.users.index') }}">
                                <i class="ft-users"></i>
                                <span data-i18n="" class="menu-title">Users</span>
                            </a>
                        </li>
                        @endif @if (auth()->user()->isAdmin())
                            <li class="nav-item @activeClass(['admin.zones.*'])">
                                <a href="{{ route('admin.zones.index') }}">
                                    <i class="fa fa-map-marker"></i>
                                    <span data-i18n="" class="menu-title">Zones</span>
                                </a>
                            </li>
                        @endif

                        {{-- Orders --}}
                        @if (auth()->user()->isSupport())
                            <li class="nav-item @activeClass(['admin.orders.*'])">
                                <a href="{{ route('admin.orders.index') }}">
                                    <i class="fa fa-shopping-cart"></i>
                                    <span data-i18n="" class="menu-title">Orders</span>
                                </a>
                            </li>
                        @endif

                        {{-- Transactions --}}
                        @if (auth()->user()->isAdmin())
                            <li class="nav-item @activeClass(['admin.transactions.*'])">
                                <a href="{{ route('admin.transactions.index') }}">
                                    <i class="fa fa-exchange"></i>
                                    <span data-i18n="" class="menu-title">Transactions</span>
                                </a>
                            </li>
                        @endif

                        {{-- Notifications --}}
                        {{-- @if (auth()->user()->isAdmin())
                        <li class="nav-item @activeClass(['admin.notifications.*'])">
                            <a href="{{ route('admin.notifications.index') }}">
                                <i class="fa fa-bell"></i>
                                <span data-i18n="" class="menu-title">Notifications</span>
                            </a>
                        </li>
                    @endif --}}
                        {{-- Services --}}
                        @if (auth()->user()->isAdmin())
                            <li class="nav-item @activeClass(['admin.services.*'])">
                                <a href="{{ route('admin.services.index') }}">
                                    <i class="fa fa-star-o"></i>
                                    <span data-i18n="" class="menu-title">Services</span>
                                </a>
                            </li>
                        @endif

                        {{-- Prices --}}
                        @if (auth()->user()->isAdmin())
                            <li class="nav-item @activeClass(['admin.prices.*'])">
                                <a href="{{ route('admin.prices.index') }}">
                                    <i class="fa fa-tag"></i>
                                    <span data-i18n="" class="menu-title">Prices</span>
                                </a>
                            </li>
                        @endif

                        {{-- Packages --}}
                        @if (auth()->user()->isAdmin())
                            <li class="nav-item @activeClass(['admin.packages.*'])">
                                <a href="{{ route('admin.packages.index') }}">
                                    <i class="fa fa-cube"></i>
                                    <span data-i18n="" class="menu-title">Packages</span>
                                </a>
                            </li>
                        @endif

                        @if (auth()->user()->isSuperAdmin())
                            <li class="nav-item @activeClass(['admin.settings.*'])">
                                <a href="{{ route('admin.settings.edit') }}">
                                    <i class="fa fa-cogs"></i>
                                    <span data-i18n="" class="menu-title">Settings</span>
                                </a>
                            </li>
                        @endif
                        {{-- Logout --}}
                        <li class="nav-item">
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form2').submit();">
                                <i class="ft-power"></i>
                                <span data-i18n="" class="menu-title">Logout</span>
                            </a>
                            <form id="logout-form2" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>

                    @endauth
            </ul>
        </div>
    </div>
    <div class="sidebar-background"></div>
</div>
<script src="{{ asset('assets/js/app-sidebar.js') }}" type="text/javascript"></script>
