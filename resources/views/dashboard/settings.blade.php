<x-app-layout>
    <x-slot name="title">Bot Settings</x-slot>

    <section id="basic-pagination">
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
                                        <form method="POST"
                                            action="{{ route('autopilot.dashboard.saveBotSettings', $bot->id) }}"
                                            class="form form-horizontal" enctype="multipart/form-data">
                                            @csrf
                                            @method('POST')
                                            <div class="form-body">
                                                <div class="form-group row">
                                                    <label class="col-md-3 label-control" for="trading"
                                                        style="margin-top: 20px;">Trading</label>
                                                    <div class="col-md-6">
                                                        <div>

                                                            <input type="radio" class="option-input radio"
                                                                name="trading" value="1"
                                                                {{ old('trading', $bot->trading) == 1 ? 'checked' : '' }}>
                                                            On &nbsp;&nbsp;&nbsp;
                                                            </input>
                                                            <input type="radio" name="trading" value="0"
                                                                class="option-input radio"
                                                                {{ old('trading', $bot->trading) == 0 ? 'checked' : '' }}>
                                                            Off
                                                            </input>
                                                        </div>
                                                        <br>
                                                        <span>Turn trading on or off. This will not affect open trades,
                                                            it will only stop buying</span>
                                                    </div>
                                                </div>

                                                {{-- You may want to include the timeframe field, uncomment if needed --}}
                                                {{--
                                                <div class="form-group row">
                                                    <label class="col-md-3 label-control" for="timeframe">Chart Timeframe:
                                                        <span class="required" aria-required="true">*</span>
                                                    </label>
                                                    <div class="col-md-6">
                                                        <div class="position-relative has-icon-left">
                                                            <select name="timeframe" class="form-control" style="width: 100%">
                                                                <option value="">Select timeframe</option>
                                                                @foreach ($timeframes as $key => $value)
                                                                    <option value="{{ $key }}" {{ old('timeframe', $bot->timeframe) == $key ? 'selected' : '' }}>
                                                                        {{ $value }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <div class="form-control-position">
                                                                <i class="fa fa-chart"></i>
                                                            </div>
                                                        </div>
                                                        <span>Chart Timeframe to use for Technical analysis</span>
                                                    </div>
                                                </div>
                                                --}}

                                                {{-- Include additional bot form fields --}}
                                                @include('partials.autopilot.bot_form')
                                            </div>

                                            <div class="form-actions">
                                                <div class="row clearfix">
                                                    <div class="col-sm-9 offset-3">
                                                        <button type="submit"
                                                            class="btn btn-raised btn-primary">{{ __('Save') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div><!-- /.px-3 -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
