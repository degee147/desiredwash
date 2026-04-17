<x-app-layout>
    <x-slot name="title">My Referrals</x-slot>

    <section id="file-export">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-8">
                                <h4 class="card-title">My Referrals</h4>
                            </div>
                            <div class="col-4 text-right">
                                {{--
                                <a href="{{ route('users.create') }}" class="btn round btn-raised btn-dark">
                                    <i class="fa fa-plus"></i>&nbsp; New User
                                </a>
                                --}}
                            </div>
                        </div>
                    </div>
                    <div class="card-body collapse show">
                        <div class="card-block card-dashboard">
                            {{--
                            <p class="card-text">
                                Exporting data from a table can often be a key part of a complex application. The Buttons extension
                                for DataTables provides three plug-ins that provide overlapping functionality for data export.
                            </p>
                            --}}
                            @include('partials.autopilot.user_table', [
                                'refcode' => $currentUser->refcode ?? '',
                                'record_name' => 'referrals',
                            ])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
