{{-- <div class="section-indent"> --}}
<div class="container container-fluid-lg">
    <div class="title-block text-center">
        <div class="title-block__label">[ What we offer ]</div>
        <h4 class="title-block__title">Price Packages</h4>
        <div class="title-block__text">Our prices are simple and affordable which are easy on
            pocket<br>in comparison with the high street prices</div>
    </div>
    <div class="promo02__wrapper row js-init-carusel-tablet slick-default">
        @forelse($packages ?? [] as $package)
            <div class="tt-item col-md-4">
                <div class="promo02 js-handler {{ $package->is_featured ? 'featured' : '' }}">
                    <div class="promo02__icon">
                        <i class="{{ $package->icon_class ?? 'icons-2840435' }}"></i>
                    </div>
                    <div class="promo02__title">{{ $package->name }}</div>
                    <div class="promo02__subtitle">{{ $package->subtitle }}</div>
                    <div class="promo02__show-layout">
                        <ul>
                            @foreach ($package->items as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="promo02__price">
                        @if ($package->has_discount)
                            <span class="old-price">₦{{ number_format($package->old_price, 2) }}</span>
                            <span class="new-price">₦{{ number_format($package->price, 2) }}</span>
                        @else
                            ₦{{ number_format($package->price, 2) }}
                        @endif
                    </div>
                    <div class="promo02__show-btn">
                        <a href="#" class="tt-btn tt-btn__wide">
                            <span class="mask">Order Now</span>
                            <div class="button">Order Now</div>
                        </a>
                    </div>

                    @if ($package->is_featured)
                        <div class="badge-featured"
                            style="position: absolute; top: 10px; right: 10px; background: #ff6b6b; color: white; padding: 5px 10px; border-radius: 5px; font-size: 12px;">
                            Most Popular
                        </div>
                    @endif
                </div>
            </div>
        @empty
            {{-- Fallback packages if none exist --}}
            <div class="tt-item col-md-4">
                <div class="promo02 js-handler">
                    <div class="promo02__icon"><i class="icons-2840435"></i></div>
                    <div class="promo02__title">Standard Package</div>
                    <div class="promo02__subtitle">50 Clothes Per Month</div>
                    <div class="promo02__show-layout">
                        <ul>
                            <li>4 T-Shirts</li>
                            <li>1 Pairs of Jeans</li>
                            <li>3 Button-Down Shirts</li>
                            <li>1 Pair of Shorts</li>
                            <li>7 Pairs of Underwear</li>
                            <li>6 Pairs of Socks</li>
                            <li>1 Towel</li>
                            <li>1 Set of Sheets</li>
                        </ul>
                    </div>
                    <div class="promo02__price">₦349,000.00</div>
                    <div class="promo02__show-btn">
                        <a href="#" class="tt-btn tt-btn__wide">
                            <span class="mask">Order Now</span>
                            <div class="button">Order Now</div>
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>
{{-- </div> --}}
