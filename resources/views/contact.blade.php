<x-guest-layout>
    <x-slot name="title">Contact</x-slot>

    <div class="section-inner">
        <div class="container container-fluid-lg">
            <div class="title-block text-center">
                <div class="title-block__label">[ Get in Touch With Us ]</div>
                <h4 class="title-block__title">Contact Information</h4>
            </div>
            <div class="row info03__wrapper">
                <div class="col-custom-450 col-6 col-md-3">
                    <div class="info03"><i class="info03__icon icons-484169"></i>
                        <h6 class="info03__title">Post Address</h6>
                        <address>8494 Signal Hill Road Manassas,<br>VA, 20110</address>
                    </div>
                </div>
                <div class="col-custom-450 col-6 col-md-3">
                    <div class="info03"><i class="info03__icon icons-483947"></i>
                        <h6 class="info03__title">Contact Phone</h6>
                        <address><a href="tel:1(800)7654321">1 (800) 765-43-21</a></address>
                    </div>
                </div>
                <div class="col-custom-450 col-6 col-md-3">
                    <div class="info03"><i class="info03__icon icons-1004017"></i>
                        <h6 class="info03__title">E-mail Address</h6>
                        <address><a href="mailto:info@yourlaundrysite.com">info@yourlaundrysite.com</a></address>
                    </div>
                </div>
                <div class="col-custom-450 col-6 col-md-3">
                    <div class="info03"><i class="info03__icon icons-59252"></i>
                        <h6 class="info03__title">Opening Hours</h6>
                        <address>Mon-Fri 08:00 AM - 05:00 PM</address>
                        <address>Sat-Sun 10:00 AM - 5:00 PM</address>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tt-posirion-relative">
        <div class="container container-fluid-lg">
            <div class="row">
                <div class="col-lg-6">
                    <div class="map-layout">
                        <div class="title-block">
                            <h4 class="title-block__title">Get in Touch.<br>We're Here to Help.</h4>
                        </div>
                        <p>We look forward to helping you create and maintain a clean, healthy environment that’s as
                            enjoyable as it is functional.</p>
                        <form class="form-default" id="contact-form" method="post" novalidate="novalidate"
                            action="{{ route('contact') }}#">
                            <div class="form-group"><input type="text" name="name" class="form-control"
                                    placeholder="Your name"></div>
                            <div class="row row-align-col">
                                <div class="col-md-6">
                                    <div class="form-group"><input type="text" name="email" class="form-control"
                                            placeholder="E-mail"></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group"><input type="text" name="phonenumber"
                                            class="form-control" placeholder="Phone"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <textarea name="message" rows="7" class="form-control" placeholder="Your message"></textarea>
                            </div>
                            <div class="form-group">
                                <div class="checkbox-group"><input type="checkbox" id="checkBox01" name="checkbox">
                                    <label for="checkBox01"><span class="check"></span> <span class="box"></span> I
                                        accept the privacy and terms.</label>
                                </div>
                            </div>
                            <div class="tt-btn tt-btn__wide"><span class="mask">Send Question</span> <button
                                    type="submit" class="button">Send Message</button></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div id="map" class="map-layout-wrapper lazyload" data-bg="images/map-contact.jpg"><iframe
                src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" class="lazyload"
                data-expand="-220"
                data-src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3111.5820008392016!2d-77.45701318432829!3d38.75035326320268!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89b65b93b943fb07%3A0xc8712e829da92b9e!2sHistoric%20District%2C%208494%20Signal%20Hill%20Rd%2C%20Manassas%2C%20VA%2020110%2C%20USA!5e0!3m2!1sen!2sua!4v1605172763191!5m2!1sen!2sua"
                allowfullscreen="" aria-hidden="false"></iframe></div>
    </div>
</x-guest-layout>
