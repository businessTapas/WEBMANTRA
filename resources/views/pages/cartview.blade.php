<section class="h-100 gradient-custom">
    <div class="container py-5">
        <div class="row d-flex justify-content-center my-4">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header py-3">
                        <h5 class="mb-0">Cart - <span id="checkcartCount">{{ count($product_details) }}</span> items
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Single item -->
                        @foreach ($product_details as $product)
                            <div class="product-row-{{ $product->id }}">
                                <div class="col-lg-3 col-md-12 mb-4 mb-lg-0">
                                    <!-- Image -->
                                    <div class="bg-image hover-overlay hover-zoom ripple rounded"
                                        data-mdb-ripple-color="light">
                                        <img src="{{ asset($product->photo) }}" class="w-100"
                                            alt="Blue Jeans Jacket" />
                                        <a href="#">
                                            <div class="mask" style="background-color: rgba(251, 251, 251, 0.2)">
                                            </div>
                                        </a>
                                    </div>
                                    <!-- Image -->
                                </div>

                                <div class="col-lg-5 col-md-6 mb-4 mb-lg-0">
                                    <!-- Data -->
                                    <p><strong>{{ $product->title }}</strong></p>
                                    <!-- Data -->
                                </div>

                                <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
                                    <!-- Quantity -->
                                    <div class="d-flex mb-4" style="max-width: 300px">
                                        @if ($product->qty > 1)
                                            <button type="button" class="btn btn-primary decreaseQty"
                                                data-url="{{ route('cart.update') }}" data-id="{{ $product->id }}"
                                                data-cart="decriment">-</button>
                                        @else
                                            <button type="button" class="btn btn-primary decreaseQty"
                                                data-url="{{ route('cart.update') }}" disabled
                                                data-id="{{ $product->id }}" data-cart="decriment">-</button>
                                        @endif
                                        <div data-mdb-input-init class="form-outline">
                                            <input id="form1" min="0" name="quantity"
                                                value="{{ $product->qty }}" type="number"
                                                class="form-control product-qty" data-id="{{ $product->id }}"
                                                readonly />
                                            <label class="form-label" for="form1">Quantity</label>
                                        </div>
                                        @if ($product->qty < 10)
                                            <button type="button" class="btn btn-warning increaseQty"
                                                data-url="{{ route('cart.update') }}" data-id="{{ $product->id }}"
                                                data-cart="incriment">+</button>
                                        @else
                                            <button type="button" class="btn btn-warning increaseQty"
                                                data-url="{{ route('cart.update') }}" disabled
                                                data-id="{{ $product->id }}" data-cart="incriment">+</button>
                                        @endif
                                        <button type="button" class="btn btn-danger deleteQty"
                                            data-url="{{ route('cart-delete', $product->id) }}"
                                            data-id="{{ $product->id }}"><i class="fa-solid fa-trash"></i></button>
                                    </div>
                                    <!-- Quantity -->

                                    <!-- Price -->
                                    <p class="text-start text-md-center">
                                        @if ((float) $product->discount > 0)
                                            @php
                                                $discountPercentage = ($product->discount * $product->price) / 100;
                                                $discountedPrice = $product->price - $discountPercentage;
                                            @endphp
                                            <strong>{{ number_format($discountedPrice, 2) }}</strong>
                                        @else
                                            <strong>{{ number_format($product->price, 2) }}</strong>
                                        @endif
                                    </p>
                                    <!-- Price -->
                                </div>
                            </div>
                            <hr class="my-4" />
                        @endforeach
                        <!-- Single item -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
