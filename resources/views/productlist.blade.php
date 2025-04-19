@extends('layout.app')
@section('content')

        <!-- /page-title -->
        <!-- Section product -->
        <section class="flat-spacing">
            <div class="container">
               
                <div class="wrapper-control-shop">
                    <div class="meta-filter-shop">
                    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
                        <div id="product-count-grid" class="count-text"></div>
                        <div id="product-count-list" class="count-text"></div>
                        <div id="applied-filters"></div>
                        <button id="remove-all" class="remove-all-filters text-btn-uppercase" style="display: none;">REMOVE ALL <i class="icon icon-close"></i></button>
                    </div>
                  
                    <div class="tf-grid-layout wrapper-shop tf-col-4" id="gridLayout">
                    @foreach ($product_lists as $product)
                        <!-- card product 1 -->
                        
                        <div class="card-product grid" data-availability="Out of stock" data-brand="adidas">
                            <div class="card-product-wrapper">
                            @if ($product->stock <= 0)
                                        <div class="discount_div">
                                            Sold Out!
                                        </div>
                                    @endif
                                <a href="#" class="product-img">
                                    <img class="lazyload img-product" data-src="{{asset($product->photo)}}" src="{{asset($product->photo)}}" alt="image-product">
                                    <img class="lazyload img-hover" data-src="{{asset($product->photo)}}" src="{{asset($product->photo)}}" alt="image-product">
                                </a>
                                <div class="list-product-btn">
                                   
                                </div>
                                @if($product->stock > 0)
                                <div class="list-btn-main">
                                    <a href="#" data-url="{{ route('add-to-cart', $product->slug) }}" class="btn-main-product  order-now-btn ">Add To cart</a>
                                </div> 
                                @endif
                            </div>
                            <div class="card-product-info">
                                <a href="product-detail.html" class="title link">{{ $product->title }}</a>
                                <span class="price current-price">${{  $product->price }}</span>
                            </div>
                        </div>
                        @endforeach
                     
                        
                    </div>
                </div>
            </div>
        </section>
        <!-- /Section product -->
        <!-- tf-has-purchased -->
  
        <!-- /tf-has-purchased -->

@endsection
