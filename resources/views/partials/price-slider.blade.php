<div class="section-indent">
    <div class="lazyload tt-obj02-bg" data-bg="images/wrapper05.png"></div>
    <div class="container">
        <div class="title-block text-center">
            <div class="title-block__label">[ Affordable Prices ]</div>
            <h4 class="title-block__title">Our Dry Cleaning & Laundry Prices</h4>
            <div class="title-block__text">Our prices are simple and affordable which are easy on
                pocket<br>in comparison with the high street prices</div>
        </div>
        <div class="slick-default promo03__wrapper"
            data-slick='{
                "slidesToShow": 4,
                "autoplaySpeed": 4500,
                "slidesToScroll": 4,
                "autoplay":true,
                "responsive": [
                    {
                        "breakpoint": 1025,
                        "arrows": false,
                        "settings": {
                            "slidesToShow": 2,
                            "slidesToScroll": 2
                        }
                    },
                    {
                        "breakpoint": 651,
                        "settings": {
                            "slidesToShow": 1,
                            "slidesToScroll": 1
                        }
                    }
                ]
            }'>

            @forelse($prices ?? [] as $price)
                <div class="tt-item">
                    <div class="promo03 js-handler">
                        <div class="promo03__move">
                            @if ($price->icon_class)
                                <div class="promo03__icon {{ $price->icon_class }}"></div>
                            @else
                                <div class="promo03__icon"><i class="icons-884417"></i></div>
                            @endif

                            <div class="promo03__title">{{ $price->item_name }}</div>
                            <div class="promo03__subtitle">{{ $price->description ?? 'Professional Service' }}</div>

                            <div class="promo03__price">
                                <span class="tt-value">₦{{ number_format($price->regular_price, 2) }}</span>
                                @if ($price->express_price)
                                    <br><small style="font-size: 0.8em; color: #999;">Express:
                                        ₦{{ number_format($price->express_price, 2) }}</small>
                                @endif
                            </div>

                            <div class="promo03__show-btn">
                                <a href="#" class="tt-btn">
                                    <span class="mask">Order Now</span>
                                    <div class="button">Order Now</div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                {{-- Fallback if no prices are available --}}
                <div class="tt-item">
                    <div class="promo03 js-handler">
                        <div class="promo03__move">
                            <div class="promo03__icon icons-884417"></div>
                            <div class="promo03__title">Shirts Service</div>
                            <div class="promo03__subtitle">Washed and Pressed</div>
                            <div class="promo03__price"><span class="tt-value">₦1,000.00</span></div>
                            <div class="promo03__show-btn">
                                <a href="#" class="tt-btn">
                                    <span class="mask">Order Now</span>
                                    <div class="button">Order Now</div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
