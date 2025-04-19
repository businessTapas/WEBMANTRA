@extends('layout.app')

@section('content')
    @push('styles')
        <style>
            body {
                background-color: #f8f9fa;
            }

            .section-title {
                background-color: #fbc02d;
                padding: 10px 15px;
                font-weight: bold;
                color: #fff;
                border-radius: 5px 5px 0 0;
            }

            .checkout-container {
                background-color: #fff;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                margin-top: 20px;
            }

            .product-img {
                width: 60px;
                height: 60px;
                object-fit: cover;
                border: 1px solid #ddd;
                border-radius: 5px;
            }

            .btn-save-later {
                background-color: #f1f1f1;
                border: none;
            }

            .btn-save-later:hover {
                background-color: #e0e0e0;
            }
        </style>
    @endpush
    {{-- <!-- inner banner -->
    <section class="inner-banner">
        <img src="https://www.prelivewebsite.in/priyagopal/wp-content/uploads/2024/06/inner-banner-2.webp" class="img-fluid">
        <div class="container">
            <div class="innerbanner-text wow fadeInUp" style="visibility: visible; animation-name: fadeInUp;">
                <h1>Checkout</h1>
            </div>
        </div>
    </section>
    <!-- inner banner --> --}}
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

    @if ($cartCount > 0)
        <div class="innerpage">
            <form id="orderForm" action="{{ route('pay.with.paypal') }}" method="post" enctype="multipart/form-data">
                <div class="row">
                    @csrf
                    <!-- Left Column - Customer Information -->
                    <div class="col-md-8">
                        <div class="checkout-container">
                            <div class="section-title">Customer Information</div>
                            <form class="mt-3">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">First Name *</label>
                                        <input type="text" class="form-control" name="fname" id="fname" value="{{$user->first_name ?? ''}}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Last Name *</label>
                                        <input type="text" class="form-control" name="lname" id="lname" value="{{$user->last_name ?? ''}}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Phone *</label>
                                        <input type="number" class="form-control" name="phone" id="phone" value="{{$user->phone ?? ''}}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email *</label>
                                        <input type="email" class="form-control" name="email" id="email" value="{{$user->email ?? ''}}">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Street Address *</label>
                                        <input type="text" class="form-control" name="address" id="address" value="{{$user->userDetails->address1 ?? ''}}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Country *</label>
                                        <input type="text" class="form-control" value="India" readonly name="country"
                                            id="country">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">State *</label>
                                        <select class="form-select" name="state" id="state">
                                            <option value="">Select an option...</option>
                                            @foreach ($states as $state)
                                                <option value="{{ $state['state'] }}" {{ old('state', $user->userDetails->state ?? '') == $state['state'] ? 'selected' : '' }}>{{ $state['state'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">City *</label>
                                        <input type="text" class="form-control" name="city" id="city" value="{{$user->userDetails->city ?? ''}}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Postcode / ZIP *</label>
                                        <input type="number" class="form-control" name="pin" id="pin" value="{{$user->userDetails->pin ?? ''}}">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">Order Notes</label>
                                        <textarea class="form-control" rows="3" name="notes" id="notes"></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Right Column - Order Review -->
                    <div class="col-md-4">
                        <div class="checkout-container">
                            <div class="section-title">Order Review</div>
                            <div class="order-review-container">
                                @include('pages.checkoutProduct', [
                                    'product_details' => $product_details,
                                    'subtotal' => $subtotal,
                                    'gst' => $gst,
                                    'cartCount' => $cartCount
                                ])
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary order-place" >Place Order</button>
                    </div>
                </div>
            </form>
        </div>
    @else
        <div class="innerpage">
            <p>Your cart is empty. You will be redirected to the home page shortly.</p>
            <script>
                setTimeout(function() {
                    window.location.href = "{{ route('home') }}";
                }, 3000);
            </script>
        </div>
    @endif
@endsection
