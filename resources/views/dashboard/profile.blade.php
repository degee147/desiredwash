<x-app-layout>
    <x-slot name="title">Update Profile</x-slot>

    <section id="horizontal-form-layouts">
        <div class="row">
            <div class="col-sm-12">
                <div class="content-header">Update Profile</div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{-- Optional: Add a card title or info here --}}
                    </div>
                    <div class="card-body">
                        @include('partials.user_form', [
                            'show_balance' => false,
                            'show_role' => false,
                            'show_fullname' => false,
                            'show_password' => false,
                            'action' => route('dashboard.updateProfile'),
                        ])
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
