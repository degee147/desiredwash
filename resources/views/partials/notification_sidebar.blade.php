<aside id="notification-sidebar" class="notification-sidebar d-none d-sm-none d-md-block">
    <a class="notification-sidebar-close">
        <i class="ft-x font-medium-3"></i>
    </a>
    <div class="side-nav notification-sidebar-content">
        <div class="row">
            <div class="col-12 mt-1">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a id="base-tab1" data-toggle="tab" aria-controls="tab1" href="#activity-tab"
                            aria-expanded="true" class="nav-link active">Activity</a>
                    </li>
                    {{--
                    <li class="nav-item">
                        <a id="base-tab2" data-toggle="tab" aria-controls="tab2" href="#chat-tab" aria-expanded="false" class="nav-link">Chat</a>
                    </li>
                    --}}
                    <li class="nav-item">
                        <a id="base-tab3" data-toggle="tab" aria-controls="tab3" href="#settings-tab"
                            aria-expanded="false" class="nav-link">Settings</a>
                    </li>
                </ul>
                <div class="tab-content px-1 pt-1">
                    <div id="activity-tab" role="tabpanel" aria-expanded="true" aria-labelledby="base-tab1"
                        class="tab-pane active">
                        <div id="activity" class="col-12 timeline-left">
                            <h6 class="mt-1 mb-3 text-bold-400">RECENT ACTIVITY</h6>
                            <div id="timeline" class="timeline-left timeline-wrapper">
                                <ul class="timeline">
                                    <li class="timeline-line"></li>
                                    <li class="timeline-item">
                                        <div class="timeline-badge">
                                            <span data-toggle="tooltip" data-placement="right"
                                                title="Portfolio project work" class="bg-purple bg-lighten-1">
                                                <i class="ft-shopping-cart"></i>
                                            </span>
                                        </div>
                                        <div class="col s9 recent-activity-list-text">
                                            <a href="#" class="deep-purple-text medium-small">just now</a>
                                            <p class="mt-0 mb-2 fixed-line-height font-weight-300 medium-small">Jim Doe
                                                Purchased new equipments for zonal office.</p>
                                        </div>
                                    </li>
                                    <li class="timeline-item">
                                        <div class="timeline-badge">
                                            <span data-toggle="tooltip" data-placement="right"
                                                title="Portfolio project work" class="bg-info bg-lighten-1">
                                                <i class="fa fa-plane"></i>
                                            </span>
                                        </div>
                                        <div class="col s9 recent-activity-list-text">
                                            <a href="#" class="cyan-text medium-small">Yesterday</a>
                                            <p class="mt-0 mb-2 fixed-line-height font-weight-300 medium-small">Your
                                                Next flight for USA will be on 15th August 2015.</p>
                                        </div>
                                    </li>
                                    <li class="timeline-item">
                                        <div class="timeline-badge">
                                            <span data-toggle="tooltip" data-placement="right"
                                                title="Portfolio project work" class="bg-success bg-lighten-1">
                                                <i class="ft-mic"></i>
                                            </span>
                                        </div>
                                        <div class="col s9 recent-activity-list-text">
                                            <a href="#" class="green-text medium-small">5 Days Ago</a>
                                            <p class="mt-0 mb-2 fixed-line-height font-weight-300 medium-small">Natalya
                                                Parker Send you a voice mail for next conference.</p>
                                        </div>
                                    </li>
                                    <li class="timeline-item">
                                        <div class="timeline-badge">
                                            <span data-toggle="tooltip" data-placement="right"
                                                title="Portfolio project work" class="bg-warning bg-lighten-1">
                                                <i class="ft-map-pin"></i>
                                            </span>
                                        </div>
                                        <div class="col s9 recent-activity-list-text">
                                            <a href="#" class="amber-text medium-small">1 Week Ago</a>
                                            <p class="mt-0 mb-2 fixed-line-height font-weight-300 medium-small">Jessy
                                                Jay open a new store at S.G Road.</p>
                                        </div>
                                    </li>
                                    <li class="timeline-item">
                                        <div class="timeline-badge">
                                            <span data-toggle="tooltip" data-placement="right"
                                                title="Portfolio project work" class="bg-red bg-lighten-1">
                                                <i class="ft-inbox"></i>
                                            </span>
                                        </div>
                                        <div class="col s9 recent-activity-list-text">
                                            <a href="#" class="deep-orange-text medium-small">2 Week Ago</a>
                                            <p class="mt-0 mb-2 fixed-line-height font-weight-300 medium-small">voice
                                                mail for conference.</p>
                                        </div>
                                    </li>
                                    <li class="timeline-item">
                                        <div class="timeline-badge">
                                            <span data-toggle="tooltip" data-placement="right"
                                                title="Portfolio project work" class="bg-cyan bg-lighten-1">
                                                <i class="ft-mic"></i>
                                            </span>
                                        </div>
                                        <div class="col s9 recent-activity-list-text">
                                            <a href="#" class="brown-text medium-small">1 Month Ago</a>
                                            <p class="mt-0 mb-2 fixed-line-height font-weight-300 medium-small">Natalya
                                                Parker Send you a voice mail for next conference.</p>
                                        </div>
                                    </li>
                                    <li class="timeline-item">
                                        <div class="timeline-badge">
                                            <span data-toggle="tooltip" data-placement="right"
                                                title="Portfolio project work" class="bg-amber bg-lighten-1">
                                                <i class="ft-map-pin"></i>
                                            </span>
                                        </div>
                                        <div class="col s9 recent-activity-list-text">
                                            <a href="#" class="deep-purple-text medium-small">3 Month Ago</a>
                                            <p class="mt-0 mb-2 fixed-line-height font-weight-300 medium-small">Jessy
                                                Jay open a new store at S.G Road.</p>
                                        </div>
                                    </li>
                                    <li class="timeline-item">
                                        <div class="timeline-badge">
                                            <span data-toggle="tooltip" data-placement="right"
                                                title="Portfolio project work" class="bg-grey bg-lighten-1">
                                                <i class="ft-inbox"></i>
                                            </span>
                                        </div>
                                        <div class="col s9 recent-activity-list-text">
                                            <a href="#" class="grey-text medium-small">1 Year Ago</a>
                                            <p class="mt-0 mb-2 fixed-line-height font-weight-300 medium-small">voice
                                                mail for conference.</p>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div id="chat-tab" aria-labelledby="base-tab2" class="tab-pane">
                        <div id="chatapp" class="col-12">
                            <h6 class="mt-1 mb-3 text-bold-400">RECENT CHAT</h6>
                            <div class="collection border-none">
                                @foreach ([
        ['img' => 'avatar-s-12.png', 'name' => 'Elizabeth Elliott', 'time' => '5.00 AM', 'msg' => 'Thank you'],
        ['img' => 'avatar-s-6.png', 'name' => 'Mary Adams', 'time' => '4.14 AM', 'msg' => 'Hello Boo'],
        ['img' => 'avatar-s-11.png', 'name' => 'Caleb Richards', 'time' => '9.00 PM', 'msg' => 'Keny !'],
        ['img' => 'avatar-s-18.png', 'name' => 'June Lane', 'time' => '4.14 AM', 'msg' => 'Ohh God'],
        ['img' => 'avatar-s-1.png', 'name' => 'Edward Fletcher', 'time' => '5.15 PM', 'msg' => 'Love you'],
        ['img' => 'avatar-s-2.png', 'name' => 'Crystal Bates', 'time' => '8.00 AM', 'msg' => 'Can we'],
        ['img' => 'avatar-s-3.png', 'name' => 'Nathan Watts', 'time' => '9.53 PM', 'msg' => 'Great!'],
        ['img' => 'avatar-s-15.png', 'name' => 'Willard Wood', 'time' => '4.20 AM', 'msg' => 'Do it'],
        ['img' => 'avatar-s-19.png', 'name' => 'Ronnie Ellis', 'time' => '5.30 PM', 'msg' => 'Got that'],
        ['img' => 'avatar-s-14.png', 'name' => 'Gwendolyn Wood', 'time' => '4.34 AM', 'msg' => 'Like you'],
        ['img' => 'avatar-s-13.png', 'name' => 'Daniel Russell', 'time' => '12.00 AM', 'msg' => 'Thank you'],
        ['img' => 'avatar-s-22.png', 'name' => 'Sarah Graves', 'time' => '11.14 PM', 'msg' => 'Okay you'],
        ['img' => 'avatar-s-9.png', 'name' => 'Andrew Hoffman', 'time' => '7.30 PM', 'msg' => 'Can do'],
        ['img' => 'avatar-s-20.png', 'name' => 'Camila Lynch', 'time' => '2.00 PM', 'msg' => 'Leave it'],
    ] as $chat)
                                    <div class="media mb-1">
                                        <a>
                                            <img alt="96x96"
                                                src="{{ asset('assets/img/portrait/small/' . $chat['img']) }}"
                                                class="media-object d-flex mr-3 bg-primary height-50 rounded-circle">
                                        </a>
                                        <div class="media-body">
                                            <div class="clearfix">
                                                <h4 class="font-medium-1 primary mt-1 mb-0 mr-auto float-left">
                                                    {{ $chat['name'] }} </h4>
                                                <span
                                                    class="medium-small float-right blue-grey-text text-lighten-3">{{ $chat['time'] }}</span>
                                            </div>
                                            <p class="text-muted font-small-3">{{ $chat['msg'] }} </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div id="settings-tab" aria-labelledby="base-tab3" class="tab-pane">
                        <div id="settings" class="col-12">
                            <h6 class="mt-1 mb-3 text-bold-400">GENERAL SETTINGS</h6>
                            <ul class="list-unstyled">
                                <li>
                                    <div class="togglebutton">
                                        <div class="switch">
                                            <span class="text-bold-500">Notifications</span>
                                            <div class="float-right">
                                                <div class="custom-control custom-checkbox mb-2 mr-sm-2 mb-sm-0">
                                                    <input id="notifications1" checked="checked" type="checkbox"
                                                        class="custom-control-input cz-bg-image-display">
                                                    <label for="notifications1" class="custom-control-label"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p>Use checkboxes when looking for yes or no answers.</p>
                                </li>
                                <li>
                                    <div class="togglebutton">
                                        <div class="switch">
                                            <span class="text-bold-500">Show recent activity</span>
                                            <div class="float-right">
                                                <div class="custom-control custom-checkbox mb-2 mr-sm-2 mb-sm-0">
                                                    <input id="recent-activity1" checked="checked" type="checkbox"
                                                        class="custom-control-input cz-bg-image-display">
                                                    <label for="recent-activity1"
                                                        class="custom-control-label"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p>The for attribute is necessary to bind our custom checkbox with the input.</p>
                                </li>
                                <li>
                                    <div class="togglebutton">
                                        <div class="switch">
                                            <span class="text-bold-500">Notifications</span>
                                            <div class="float-right">
                                                <div class="custom-control custom-checkbox mb-2 mr-sm-2 mb-sm-0">
                                                    <input id="notifications2" type="checkbox"
                                                        class="custom-control-input cz-bg-image-display">
                                                    <label for="notifications2" class="custom-control-label"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p>Use checkboxes when looking for yes or no answers.</p>
                                </li>
                                <li>
                                    <div class="togglebutton">
                                        <div class="switch">
                                            <span class="text-bold-500">Show recent activity</span>
                                            <div class="float-right">
                                                <div class="custom-control custom-checkbox mb-2 mr-sm-2 mb-sm-0">
                                                    <input id="recent-activity2" type="checkbox"
                                                        class="custom-control-input cz-bg-image-display">
                                                    <label for="recent-activity2"
                                                        class="custom-control-label"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p>The for attribute is necessary to bind our custom checkbox with the input.</p>
                                </li>
                                <li>
                                    <div class="togglebutton">
                                        <div class="switch">
                                            <span class="text-bold-500">Show your emails</span>
                                            <div class="float-right">
                                                <div class="custom-control custom-checkbox mb-2 mr-sm-2 mb-sm-0">
                                                    <input id="show-emails" type="checkbox"
                                                        class="custom-control-input cz-bg-image-display">
                                                    <label for="show-emails" class="custom-control-label"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p>Use checkboxes when looking for yes or no answers.</p>
                                </li>
                                <li>
                                    <div class="togglebutton">
                                        <div class="switch">
                                            <span class="text-bold-500">Show Task statistics</span>
                                            <div class="float-right">
                                                <div class="custom-control custom-checkbox mb-2 mr-sm-2 mb-sm-0">
                                                    <input id="show-stats" type="checkbox"
                                                        class="custom-control-input cz-bg-image-display">
                                                    <label for="show-stats" class="custom-control-label"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p>The for attribute is necessary to bind our custom checkbox with the input.</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</aside>
<script src="{{ asset('assets/js/notification-sidebar.js') }}" type="text/javascript"></script>
