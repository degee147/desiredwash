<x-app-layout>
    <x-slot name="title">Bot Settings</x-slot>

    <!-- Basic Pagination start -->
    <section id="basic-pagination">
        <!-- <div class="row">
        <div class="col-sm-12 mt-2">
            <div class="content-header">Settings</div>
        </div>
    </div> -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Bot Settings</h4>
                    </div>
                    <div class="card-body">
                        <div class="card-block">

                            <div class="row">

                                <div class="col-md-12">
                                    <div class="px-3">

                                        {{-- @dd($bot->getAttributes()) --}}
                                        <form action="{{ route('autopilot.users.bot', $bot->user_id) }}" method="POST"
                                            enctype="multipart/form-data" class="form form-horizontal">
                                            @csrf
                                            @method('POST')

                                            <div class="form-body">
                                                {{-- Active --}}
                                                <div class="form-group row">
                                                    <label class="col-md-3 label-control"
                                                        style="margin-top: 20px;">Active</label>
                                                    <div class="col-md-6">
                                                        <label>
                                                            <input type="radio" name="active" value="1"
                                                                class="option-input radio"
                                                                {{ $bot->active ? 'checked' : '' }}> On
                                                            &nbsp;&nbsp;&nbsp;
                                                        </label>
                                                        <label>
                                                            <input type="radio" name="active" value="0"
                                                                class="option-input radio"
                                                                {{ !$bot->active ? 'checked' : '' }}> Off
                                                        </label>
                                                        <br><span>Turn Bot on or off (Admin only)</span>
                                                    </div>
                                                </div>

                                                {{-- Trading --}}
                                                <div class="form-group row">
                                                    <label class="col-md-3 label-control"
                                                        style="margin-top: 20px;">Trading</label>
                                                    <div class="col-md-6">
                                                        <label>
                                                            <input type="radio" name="trading" value="1"
                                                                class="option-input radio"
                                                                {{ $bot->trading ? 'checked' : '' }}> On
                                                            &nbsp;&nbsp;&nbsp;
                                                        </label>
                                                        <label>
                                                            <input type="radio" name="trading" value="0"
                                                                class="option-input radio"
                                                                {{ !$bot->trading ? 'checked' : '' }}> Off
                                                        </label>
                                                        <br><span>Turn trading on or off</span>
                                                    </div>
                                                </div>

                                                {{-- Simulation --}}
                                                <div class="form-group row">
                                                    <label class="col-md-3 label-control"
                                                        style="margin-top: 20px;">Simulation</label>
                                                    <div class="col-md-6">
                                                        <label>
                                                            <input type="radio" name="simulation" value="1"
                                                                class="option-input radio"
                                                                {{ $bot->simulation ? 'checked' : '' }}> On
                                                            &nbsp;&nbsp;&nbsp;
                                                        </label>
                                                        <label>
                                                            <input type="radio" name="simulation" value="0"
                                                                class="option-input radio"
                                                                {{ !$bot->simulation ? 'checked' : '' }}> Off
                                                        </label>
                                                        <br><span>Turn Simulation on or off</span>
                                                    </div>
                                                </div>

                                                {{-- Include partial (bot_form equivalent) --}}
                                                @include('partials.autopilot.bot_form')
                                            </div>

                                            <div class="form-actions">
                                                <div class="row clearfix">
                                                    <div class="col-sm-9 offset-3">
                                                        <button type="submit"
                                                            class="btn btn-raised btn-primary">Save</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Basic Pagination end -->
</x-app-layout>
