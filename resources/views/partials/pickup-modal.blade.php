<div class="modal fade" id="modalMRequestQuote" tabindex="-1" role="dialog" aria-label="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-body form-default modal-layout-dafault"><button type="button" class="close"
                    data-dismiss="modal" aria-hidden="true"><span class="icons-860796"></span></button>
                <div class="modal-titleblock">
                    <div class="modal-title">Schedule a Pickup</div>
                </div>
                <form class="form-modal" id="jsFormRequestQuote" method="post" novalidate="novalidate"
                    action="index.html#">
                    <div class="form-group"><input type="text" name="name" class="form-control" id="modalName01"
                            placeholder="Your Name *"></div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group"><input type="text" name="email" class="form-control"
                                    id="modalEmail01" placeholder="Your e-mail address *"></div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group"><input type="text" name="phone" class="form-control"
                                    id="modalPhone01" placeholder="Your phone number"></div>
                        </div>
                    </div>
                    <div class="form-group"><input type="text" name="modalAddress" class="form-control"
                            id="modalAddress" placeholder="Address *"></div>
                    <div class="form-group">
                        <div class="custom-select"><select name="typeofholiday" class="tt-select">
                                <option value="Service">Service</option>
                                <option value="Service 02">Service 02</option>
                                <option value="Service 03">Service 03</option>
                                <option value="Service 04">Service 04</option>
                                <option value="Service 05">Service 05</option>
                            </select></div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group"><input name="date-pick-up" placeholder="Pick-Up Date"
                                    autocomplete="off" data-timepicker="true" class="js_datepicker-1 form-control"
                                    type="text">
                                <div class="form-group__icon icons-747310"></div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group"><input name="date-delivery" placeholder="Delivery Date"
                                    autocomplete="off" data-timepicker="true" class="js_datepicker-2 form-control"
                                    type="text">
                                <div class="form-group__icon icons-747310"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <textarea name="message" class="form-control" placeholder="Your comment"></textarea>
                    </div>
                    <div class="tt-btn tt-btn__wide"><span class="mask">Order Now</span> <button type="submit"
                            class="button">Order Now</button></div>
                </form>
            </div>
        </div>
    </div>
</div>
