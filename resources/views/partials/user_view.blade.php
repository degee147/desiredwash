<x-app-layout>
    <x-slot name="title">{{ $user->email }}</x-slot>

    {{-- JS & CSS --}}
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <style>
        #user-profile .profile-with-cover .profile-cover-buttons {
            top: unset;
            right: 10px;
        }
    </style>

    <section id="user-profile">
        <div class="row">
            <div class="col-12">
                <div class="card profile-with-cover">
                    <div class="card-img-top img-fluid bg-cover height-100" style="background: grey"></div>
                    <div class="media profil-cover-details row">
                        <div class="col-5">
                            <div class="align-self-start halfway-fab pl-3 pt-2">
                                <div class="text-left">
                                    <h3 class="card-title white">
                                        {{ $user->email }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="align-self-center halfway-fab text-center">
                                <a class="profile-image">
                                    <img src="{{ asset('assets/img/portrait/avatars/avatar-08.png') }}"
                                        class="rounded-circle img-border gradient-summer width-100" alt="Card image">
                                </a>
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="align-self-start halfway-fab pl-3 pt-2">
                                <div class="text-left">
                                    <h3 class="card-title white">
                                        Balance: ₦{{ number_format($user->wallet_balance) }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <div class="profile-cover-buttons">
                            <div class="media-body halfway-fab align-self-end">
                                <div class="text-right d-none d-sm-none d-md-none d-lg-block">
                                    <a href="{{ route('admin.users.edit', $user->id) }}"
                                        class="btn btn-xs btn-raised btn-warning btn-icon mr-1 btn-sm">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Example of About / Info Section --}}
                    <div class="profile-section">
                        <div class="row">
                            <div class="col-lg-5 col-md-5">
                                {{-- Placeholder for profile menu --}}
                            </div>
                            <div class="col-lg-5 col-md-5">
                                {{-- Placeholder --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- About section --}}
    <section id="about">
        <div class="row">
            <div class="col-4">
                <div class="content-header">
                    {{ !empty($referred_by) ? 'Referred by ' . $referred_by->name : '' }}
                </div>
            </div>
            <div class="col-2 text-right">
                <h3>{{ $label ?? 'This Month' }}:</h3>

                <form id="momentForm" class="form-horizontal" method="POST" action="#">
                    @csrf
                    <input type="hidden" name="startDate" value="{{ $startDate ?? '' }}">
                    <input type="hidden" name="endDate" value="{{ $endDate ?? '' }}">
                    <input type="hidden" name="label" value="{{ $label ?? '' }}">
                </form>
            </div>
            <div class="col-4">
                <div id="reportrange"
                    style="background:#fff;cursor:pointer;padding:5px 10px;border:1px solid #ccc;width:100%">
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span></span> <i class="fa fa-caret-down"></i>
                </div>
            </div>
        </div>

        {{-- Example Buttons --}}
        <div class="row mt-2">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header row">
                        <div class="col-sm-8">


                            <a href="javascript:void(0)"
                                class="btn mt-1 btn-info gradient-purple-deep-purple white btn-sm">
                                Refresh
                            </a>

                            <a href="javascript:void(0)"
                                class="btn btn-raised mt-1 btn-info gradient-nepal white btn-sm"
                                onclick="event.preventDefault(); if(confirm('Deposit ₦5000 to {{ addslashes($user->name) }} ?')) document.getElementById('deposit-{{ $user->id }}').submit();">
                                Deposit ₦5000
                            </a>
                            <form id="deposit-{{ $user->id }}"
                                action="{{ route('admin.users.fundUser', $user->id) }}" method="POST"
                                style="display:none;">
                                @csrf
                            </form>

                            <a href="{{ route('admin.users.edit', $user->id) }}"
                                class="btn btn-raised mt-1 btn-info gradient-nepal white btn-sm">
                                Edit User
                            </a>

                            <a href="#" class="btn btn-raised mt-1 btn-info gradient-nepal white btn-sm"
                                onclick="event.preventDefault(); if(confirm('Sure ?')) document.getElementById('toggle-live-{{ $user->id }}').submit();">
                                Toggle Live Mode
                            </a>
                            <form id="toggle-live-{{ $user->id }}"
                                action="{{ route('admin.users.toggleStatus', $user->id) }}" method="POST"
                                style="display:none;">
                                @csrf
                            </form>
                        </div>
                        <div class="col-sm-4">
                            <h3>Balance: ₦{{ number_format($user->wallet_balance) }}</h3>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="card-block">
                            <div class="row">
                                {{-- Example User Info --}}
                                <div class="col-md-3">
                                    <ul class="no-list-style">
                                        <li class="mb-2">
                                            <span class="text-bold-500 primary">
                                                <i class="fa fa-user font-small-3"></i> User
                                            </span>
                                            <span class="display-block overflow-hidden">
                                                <strong>Name:</strong>
                                                {{ $user->name }}
                                            </span>
                                            <span class="display-block overflow-hidden">
                                                <strong>Email:</strong>
                                                {{ $user->email_verified ? 'Verified' : 'Unverified' }}
                                                @unless ($user->email_verified)
                                                    <a href="javascript:void(0)"
                                                        onclick="return confirm('Send verification email?')"
                                                        class="btn btn-xs btn-raised btn-info btn-icon mr-1 btn-sm">
                                                        <i class="fa fa-envelope"></i> Send Email
                                                    </a>
                                                @endunless
                                            </span>

                                        </li>
                                    </ul>
                                </div>
                                {{-- Other columns (Bot, Balance, etc.) follow same style --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section id="orders">
        <!-- <div class="row">
        <div class="col-12">
            <div class="content-header">Orders</div>
        </div>
    </div> -->

        <div class="row">
            <div class="col-12 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <!-- <h5>Personal Information</h5> -->
                        <div class="row">
                            <div class="col-sm-4">
                                <h4 class="card-title"> Trades by
                                    <?= !empty(trim($user->name)) ? $user->name : $user->email ?>
                                </h4>

                            </div>
                            <div class="col-sm-4">
                                <fieldset class="form-group">
                                    <label for="combo_id">Combos</label>
                                    <select name="combo_id" id="combo_id" class="form-control indicator"
                                        style="width: 100%">
                                        <option value="">Select Combo</option>

                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-sm-4">
                                <fieldset class="form-group">
                                    <label for="trade_filters">Filters</label>
                                    <select name="trade_filters[]" id="trade_filters"
                                        class="form-control select2_multiple" style="width: 100%" multiple>

                                    </select>
                                </fieldset>
                            </div>

                        </div>
                        <?php //echo $this->element('signals_filter')
                        ?>
                    </div>
                    <div class="card-body">
                        <div class="card-block">
                            <!-- <div class="mb-3">
                            <span class="text-bold-500 primary">About Me:</span>
                            <span class="display-block overflow-hidden">
                            </span>
                        </div> -->
                            <!-- <hr> -->
                            <div class="row">
                                <div class="col-12 col-md-12 ">
                                    <div class="table-responsive">
                                        <?php //echo $this->element('aws_accounts_table', ['userid' => $user->id, 'ajax' => true, 'show_edit' => false, 'show_remove' => false, "idtouse"=>"aws_accounts_table"])
                                        ?>
                                        @include('partials.orders_table', [
                                            'userid' => $user->id,
                                            'searching' => auth()->user()->sa,
                                        ])
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="refs">
        <!-- <div class="row">
        <div class="col-12">
            <div class="content-header">Orders</div>
        </div>
    </div> -->

        <div class="row">
            <div class="col-12 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <!-- <h5>Personal Information</h5> -->
                        <div class="row">
                            <div class="col-8">
                                <h4 class="card-title"> Referrals by
                                    <?= $user->name ?>
                                </h4>
                            </div>
                            <div class="col-4 text-right">
                            </div>
                        </div>
                        <?php //echo $this->element('signals_filter')
                        ?>
                    </div>
                    <div class="card-body">
                        <div class="card-block">
                            <!-- <div class="mb-3">
                            <span class="text-bold-500 primary">About Me:</span>
                            <span class="display-block overflow-hidden">
                            </span>
                        </div> -->
                            <!-- <hr> -->
                            <div class="row">
                                <div class="col-12 col-md-12 ">
                                    <div class="table-responsive">
                                        <?php //echo $this->element('aws_accounts_table', ['userid' => $user->id, 'ajax' => true, 'show_edit' => false, 'show_remove' => false, "idtouse"=>"aws_accounts_table"])
                                        ?>
                                        @include('partials.user_table', [
                                            'refcode' => $user->refcode,
                                            'record_name' => 'referrals',
                                        ])
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>

    </section>


    <section id="testtrades">
        <!-- <div class="row">
        <div class="col-12">
            <div class="content-header">Orders</div>
        </div>
    </div> -->

        <div class="row">
            <div class="col-12 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <!-- <h5>Personal Information</h5> -->
                        <div class="row">
                            <div class="col-8">
                                <h4 class="card-title"> Test Trades for
                                    <?= $user->email ?>
                                </h4>
                            </div>
                            <div class="col-4 text-right">

                            </div>
                        </div>
                        <?php //echo $this->element('signals_filter')
                        ?>
                    </div>
                    <div class="card-body">
                        <div class="card-block">
                            <!-- <div class="mb-3">
                            <span class="text-bold-500 primary">About Me:</span>
                            <span class="display-block overflow-hidden">
                            </span>
                        </div> -->
                            <!-- <hr> -->
                            <div class="row">
                                <div class="col-12 col-md-12 ">
                                    <div class="table-responsive">
                                        <?php //echo $this->element('aws_accounts_table', ['userid' => $user->id, 'ajax' => true, 'show_edit' => false, 'show_remove' => false, "idtouse"=>"aws_accounts_table"])
                                        ?>


                                        {{-- @include('partials.autopilot.trades_table', [
                                            'userid' => $user->id,
                                            'testing' => true,
                                            'idtouse' => 'trades_table2',
                                            'searching' => auth()->user()->sa ? true : false,
                                        ]) --}}
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>

    </section>



    <section id="refs">
        <!-- <div class="row">
        <div class="col-12">
            <div class="content-header">Orders</div>
        </div>
    </div> -->

        <div class="row">
            <div class="col-12 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <!-- <h5>Personal Information</h5> -->
                        <div class="row">
                            <div class="col-8">
                                <h4 class="card-title"> Subscriptions by
                                    <?= $user->name ?>
                                </h4>
                            </div>
                            <div class="col-4 text-right">
                            </div>
                        </div>
                        <?php //echo $this->element('signals_filter')
                        ?>
                    </div>
                    <div class="card-body">
                        <div class="card-block">
                            <!-- <div class="mb-3">
                            <span class="text-bold-500 primary">About Me:</span>
                            <span class="display-block overflow-hidden">
                            </span>
                        </div> -->
                            <!-- <hr> -->
                            <div class="row">
                                <div class="col-12 col-md-12 ">
                                    <div class="table-responsive">
                                        <?php //echo $this->element('aws_accounts_table', ['userid' => $user->id, 'ajax' => true, 'show_edit' => false, 'show_remove' => false, "idtouse"=>"aws_accounts_table"])
                                        ?>
                                        {{-- @include('partials.autopilot.subscriptions_table', [
                                            'user_id' => $user->id,
                                        ]) --}}
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>

    </section>


    <section id="bookings">
        <!-- <div class="row">
        <div class="col-12">
            <div class="content-header">Orders</div>
        </div>
    </div> -->

        <div class="row">
            <div class="col-12 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <!-- <h5>Personal Information</h5> -->
                        <div class="row">
                            <div class="col-8">
                                <h4 class="card-title"> Bookings by
                                    <?= $user->name ?>
                                </h4>
                            </div>
                            <div class="col-4 text-right">
                            </div>
                        </div>
                        <?php //echo $this->element('signals_filter')
                        ?>
                    </div>
                    <div class="card-body">
                        <div class="card-block">
                            <!-- <div class="mb-3">
                            <span class="text-bold-500 primary">About Me:</span>
                            <span class="display-block overflow-hidden">
                            </span>
                        </div> -->
                            <!-- <hr> -->
                            <div class="row">
                                <div class="col-12 col-md-12 ">
                                    <div class="table-responsive">
                                        <?php //echo $this->element('aws_accounts_table', ['userid' => $user->id, 'ajax' => true, 'show_edit' => false, 'show_remove' => false, "idtouse"=>"aws_accounts_table"])
                                        ?>
                                        {{-- @include('partials.autopilot.bookings_table', [
                                            'record_name' => 'bookings',
                                            'user' => $user,
                                        ]) --}}
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>

    </section>




    {{-- Date Range Picker Script --}}
    @include('partials.daterangepicker_script')

</x-app-layout>
