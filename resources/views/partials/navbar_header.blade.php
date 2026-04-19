<nav class="navbar navbar-expand-lg navbar-light bg-faded">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" data-toggle="collapse" class="navbar-toggle d-lg-none float-left">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <img class="img-fluid logo backend_logo" alt="#" src="{{ asset($logo) }}">
        </div>
        <div class="navbar-container">
            <div id="navbarSupportedContent" class="collapse navbar-collapse">
                <span>
                    {{ auth()->user()->name }}
                </span>

                <ul class="navbar-nav">
                    <li class="dropdown nav-item">
                        <a id="dropdownBasic3" href="#" data-toggle="dropdown"
                            class="nav-link position-relative dropdown-toggle">
                            <i class="ft-user font-medium-3 blue-grey darken-4"></i>
                            <p class="d-none">User Settings</p>
                        </a>
                        <div aria-labelledby="dropdownBasic3" class="dropdown-menu dropdown-menu-right">

                            <a href="{{ route('home') }}" class="dropdown-item py-1">
                                <i class="ft-home mr-2"></i>
                                <span>Home</span>
                            </a>
                            <a href="{{ route('dashboard.profile') }}" class="dropdown-item" data-close="true"
                                title="Profile">
                                <i class="fa fa-user"></i><span>&nbsp;&nbsp;&nbsp; Profile</span>
                            </a>
                            <a href="{{ route('dashboard.password') }}" class="dropdown-item" data-close="true"
                                title="Change Password">
                                <i class="fa fa-key"></i><span>&nbsp;&nbsp;&nbsp; Password</span>
                            </a>
                            <a href="{{ route('logout') }}" class="dropdown-item" data-close="true" title="Sign Out"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="ft-power mr-2"></i><span>Logout</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
