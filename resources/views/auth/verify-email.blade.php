<x-guest-layout>
    <x-slot name="title">Email Verification</x-slot>

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
                        <h3 class="text-center mb-4">Verify Your Email Address</h3>
                        <p class="text-center mb-4">A fresh verification link has been sent to your email address.</p>

                        @if (session('status') == 'verification-link-sent')
                            <div class="alert alert-success" role="alert">
                                A new verification link has been sent to your email address.
                            </div>
                        @endif

                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Resend Verification Link</button>
                            </div>
                        </form>

                        <p class="mt-4 text-center small">If you did not receive the email, check your spam folder or
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                class="text-decoration-none">click here to logout</a>.
                        </p>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>

</x-guest-layout>
