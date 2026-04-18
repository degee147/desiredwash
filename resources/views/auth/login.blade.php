<x-guest-layout>
    <x-slot name="title">Login</x-slot>

    <!--=====================================-->
    <!--=       breadcrumb Area Start       =-->
    <!--=====================================-->
    {{-- <section class="breadcrumb-wrap-layout1 bg-color-old-lace">
        <div class="container">
            <div class="breadcrumb-layout1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Login</li>
                    </ol>
                </nav>
            </div>
        </div>
    </section> --}}
    @if (false)
        <section class="newsletter-wrap-layout1 space-top-60 space-bottom-60 bg-color-light-1 transition-default">
            <div class="container">
                <div class="newsletter-box-layout1 box-border-dark-1 radius-default bg-color-perano">
                    <h2 class="entry-title h2-medium f-w-700 color-dark-1-fixed">Login to Your Account</h2>
                    <p class="entry-description color-dark-1-fixed">Access your dashboard, track your activity, and
                        manage
                        your account securely.</p>

                    <!-- Laravel Login Form -->
                    <form method="POST" action="{{ route('login') }}"
                        class="newsletter-form box-border-dark-1 box-shadow-large shadow-style-2 shadow-fixed transition-default radius-default">
                        @csrf

                        <!-- Session Status -->
                        <x-auth-session-status class="mb-4 text-white" :status="session('status')" />

                        <!-- Email -->
                        <input type="email" name="email" id="email"
                            class="email-input @error('email') is-invalid @enderror" placeholder="Enter your email"
                            value="{{ old('email') }}" required autofocus autocomplete="username">
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600 text-sm" />

                        <!-- Password -->
                        <input type="password" name="password" id="password"
                            class="email-input mt-4 @error('password') is-invalid @enderror"
                            placeholder="Enter your password" required autocomplete="current-password">
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600 text-sm" />

                        <!-- Remember Me and Forgot Password -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <label class="d-flex align-items-center">
                                <input type="checkbox" name="remember" id="remember" class="me-2">
                                <span class="text-white">Remember Me</span>
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                    class="link-text-form text-white text-sm">Forgot
                                    Password?</a>
                            @endif
                        </div>

                        <input type="hidden" name="redirect"
                            value="{{ session('url.intended', request()->query('redirect', route('dashboard'))) }}" />

                        <!-- Submit -->
                        <button type="submit" class="axil-btn mt-4 w-100">Login Now <i
                                class="solid-navigation"></i></button>

                        <!-- Optional Register Link -->
                        <p class="mt-4 text-center text-white">
                            Don't have an account?
                            <a href="{{ route('register', ['redirect' => session('url.intended', route('dashboard')) ?? url()->previous()]) }}"
                                class="text-dark text-decoration-underline">Register</a>
                        </p>
                    </form>

                    <!-- Optional Decorative Elements -->
                    <ul class="elements-wrap img-height-100">
                        <li><img width="57" height="53" src="assets/media/elements/element1.webp" alt="Element">
                        </li>
                        <li><img width="120" height="186" src="assets/media/elements/element2.webp" alt="Element">
                        </li>
                    </ul>
                </div>
            </div>
        </section>
    @endif

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg rounded-4">
                    <div class="card-body p-4">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <h3 class="text-center mb-4">Login to Your Account</h3>
                        <form method="POST" action="{{ route('login') }}">
                            <!-- CSRF Token -->
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" name="email" class="form-control" id="email"
                                    placeholder="Enter your email" required autofocus>
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" id="password"
                                    placeholder="Enter your password" required>
                            </div>

                            <!-- Remember Me and Forgot -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">Remember me</label>
                                </div>
                                <a href="/forgot-password" class="small text-decoration-none">Forgot Password?</a>
                            </div>

                            <!-- Submit -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>

                            <!-- Register Link -->
                            <p class="mt-4 text-center small">Don't have an account?
                                <a href="/register" class="text-decoration-none">Register</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>




</x-guest-layout>
