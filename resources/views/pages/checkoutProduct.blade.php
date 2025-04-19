@foreach ($product_details as $product_detail)
    @php
        if ((float) $product_detail->discount > 0) {
            $discount_amount = ($product_detail->price * $product_detail->discount) / 100;
            $price = $product_detail->price - $discount_amount;
        } else {
            $price = $product_detail->price;
        }

        $total_price = $price * $product_detail->qty;
        $itemGst = $total_price * .18;
        $grand_total = $total_price + $itemGst;
    @endphp
    <div class="d-flex align-items-center mt-3">
        <img src="{{ asset($product_detail->photo) }}" class="product-img me-3">
        <div>
            <strong class="d-block">{{ $product_detail->title }}</strong>
            {{-- ₹2,770.00 --}}
            ₹{{ number_format($total_price, 2) }}
            <input type="hidden" value="{{ $product_detail->id }}" name="products[{{ $product_detail->id }}][id]">
            <input type="hidden" value="{{ $product_detail->title }}" name="products[{{ $product_detail->id }}][title]">
            <input type="hidden" value="{{ number_format($price, 2) }}" name="products[{{ $product_detail->id }}][price]">
            <input type="hidden" value="{{ number_format($total_price, 2) }}" name="products[{{ $product_detail->id }}][sub_total]">
            <input type="hidden" value="{{ number_format($itemGst, 2) }}" name="products[{{ $product_detail->id }}][gst]">
            <input type="hidden" value="{{ number_format($grand_total, 2) }}" name="products[{{ $product_detail->id }}][total]">
        </div>
    </div>

    <div class="mt-3 d-flex align-items-center input-group">
        @if ($product_detail->qty > 1)
            <button type="button" class="btn btn-outline-secondary btn-sm checkDecr" data-id="{{ $product_detail->id }}"
                data-cart="decriment" data-url="{{ route('checkout-update') }}">-</button>
        @else
            <button class="btn btn-outline-secondary btn-sm" disabled>-</button>
        @endif
        <input type="text" class="form-control form-control-sm mx-2 text-center" value="{{ $product_detail->qty }}"
            style="width: 50px;" readonly>
        <input type="hidden" value="{{ $product_detail->qty }}" name="products[{{ $product_detail->id }}][qty]">
        @if ($product_detail->qty < 10)
            <button type="button" class="btn btn-outline-secondary btn-sm checkIncre"
                data-id="{{ $product_detail->id }}" data-cart="incriment"
                data-url="{{ route('checkout-update') }}">+</button>
        @else
            <button class="btn btn-outline-secondary btn-sm" disabled>+</button>
        @endif
        {{-- <button class="btn btn-save-later btn-sm ms-3">Save for later</button> --}}
        <button type="button" class="text-danger ms-auto checkDel" data-id="{{ $product_detail->id }}"
            data-url="{{ route('checkout-delete', $product_detail->id) }}"
            data-home="{{ route('home') }}">Delete</button>
    </div>
@endforeach
<div class="mt-4">
    <table class="table table-borderless">
        <tr>
            <td>Subtotal:</td>
            <td class="text-end">₹{{ number_format($subtotal, 2) }}</td>
            <input type="hidden" value="{{ number_format($subtotal, 2) }}" name="sub_total" id="sub_total">
        </tr>
        <tr>
            <td>GST:</td>
            <td class="text-end">₹{{ number_format($gst, 2) }}</td>
            <input type="hidden" value="{{ number_format($gst, 2) }}" name="gst" id="gst">
        </tr>
        <tr>
            <td>Delivery Charge:</td>
            <td class="text-end">₹{{ number_format(100, 2) }}</td>
            <input type="hidden" value="{{ number_format(100, 2) }}" name="delivery" id="delivery">
        </tr>
        <tr class="fw-bold">
            <td>Total:</td>
            <td class="text-end">₹{{ number_format($subtotal + $gst + 100, 2) }}</td>
            <input type="hidden" value="{{ number_format($subtotal + $gst + 100, 2) }}" name="total_amount"
                id="total_amount">
            <input type="hidden" value="{{ $cartCount }}" name="quantity" id="quantity">
        </tr>
    </table>
</div>
