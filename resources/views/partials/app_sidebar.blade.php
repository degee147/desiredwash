<div data-active-color="black" data-background-color="white" data-image="" class="app-sidebar">
    <!-- Sidebar Header starts-->
    <div class="sidebar-header">
        <div class="logo clearfix">
            <a href="{{ route('dashboard') }}" class="logo-text float-left">
                <div class="logo-img">
                    <img src="{{ asset('logo.png') }}" style="max-width: 30px;">
                </div>
                <!-- <span class="text align-middle">BKMK</span> -->
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

                    <li class="nav-item @activeClass(['dashboard'])">
                        <a href="{{ route('dashboard') }}">
                            <i class="fa fa-home"></i>
                            <span data-i18n="" class="menu-title">Dashboard</span>
                        </a>
                    </li>

                    {{-- @if (auth()->user()->isSuperAdmin())
                        <li class="nav-item @activeClass(['autopilot.cpanel'])">
                            <a href="{{ route('autopilot.cpanel') }}">
                                <i class="fa fa-tachometer"></i>
                                <span data-i18n="" class="menu-title">Control</span>
                            </a>
                        </li>
                    @endif
                    <li class="nav-item @activeClass(['autopilot.trends.index'])">
                        <a href="{{ route('autopilot.trends.index') }}">
                            <i class="fa fa-line-chart"></i>
                            <span data-i18n="" class="menu-title">Trends</span>
                        </a>
                    </li>

                    @if (auth()->user()->isSuperAdmin())
                        <li class="nav-item @activeClass(['autopilot.signals.index'])">
                            <a href="{{ route('autopilot.signals.index') }}">
                                <i class="fa fa-signal"></i>
                                <span data-i18n="" class="menu-title">Signals</span>
                            </a>
                        </li>
                    @endif

                    <li class="nav-item @activeClass(['autopilot.trades.index'])">
                        <a href="{{ route('autopilot.trades.index') }}">
                            <i class="fa fa-exchange"></i>
                            <span data-i18n="" class="menu-title">Trades</span>
                        </a>
                    </li>
                    @if (auth()->user()->isSuperAdmin())
                        <li class="nav-item @activeClass(['ror'])">
                            <a href="{{ route('ror') }}">
                                <i class="fa fa-plane"></i>
                                <span data-i18n="" class="menu-title">ROR</span>
                            </a>
                        </li>

                        <li class="nav-item @activeClass(['autopilot.notifications'])">
                            <a href="{{ route('autopilot.notifications') }}">
                                <i class="fa fa-bell"></i>
                                <span data-i18n="" class="menu-title">Notifications</span>
                            </a>
                        </li>
                        <li class="nav-item @activeClass(['autopilot.settings'])">
                            <a href="{{ route('autopilot.settings') }}">
                                <i class="fa fa-cogs"></i>
                                <span data-i18n="" class="menu-title">Settings</span>
                            </a>
                        </li> --}}


                    {{-- <li class="nav-item @activeClass(['users.index'])">
                        <a href="{{ route('autopilot.users.index') }}">
                            <i class="ft-users"></i>
                            <span data-i18n="" class="menu-title">Users</span>
                            <span class="tag badge badge-pill badge-dark float-right mr-1 mt-1">
                                {{ $viewCounts['users'] ?? '' }}
                            </span>
                        </a>
                    </li> --}}

                    {{-- <li class="nav-item @activeClass(['autopilot.combos.index'])">
                        <a href="{{ route('autopilot.combos.index') }}">
                            <i class="fa fa-trophy"></i>
                            <span data-i18n="" class="menu-title">Combos</span>
                        </a>
                    </li>

                    <li class="nav-item @activeClass(['autopilot.subscriptions.index'])">
                        <a href="{{ route('autopilot.subscriptions.index') }}">
                            <i class="fa fa-money-bill"></i>
                            <span data-i18n="" class="menu-title">Subscriptions</span>
                        </a>
                    </li>
                    <li class="nav-item @activeClass(['autopilot.coupons.index'])">
                        <a href="{{ route('autopilot.coupons.index') }}">
                            <i class="fa fa-star"></i>
                            <span data-i18n="" class="menu-title">Coupons</span>
                        </a>
                    </li>
                    @endif



                    <li class="nav-item @activeClass(['autopilot.pricing'])">
                        <a href="{{ route('autopilot.pricing') }}">
                            <i class="fa fa-play"></i>
                            <span data-i18n="" class="menu-title">Upgrade</span>
                        </a>
                    </li> --}}


                    {{-- Uncomment if needed --}}
                    {{-- <li class="nav-item @activeClass(['users.changePassword'])">
                        <a href="{{ route('users.changePassword') }}">
                            <i class="fa fa-key"></i>
                            <span data-i18n="" class="menu-title">Change Password</span>
                        </a>
                    </li> --}}

                    <li class="nav-item">
                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form2').submit();">
                            <i class="ft-power"></i>
                            <span data-i18n="" class="menu-title">Logout </span>
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
