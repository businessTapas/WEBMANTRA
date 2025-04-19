

        <footer id="footer" class="footer">
            <div class="footer-wrap">
                <div class="footer-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="footer-infor">
                                    <div class="footer-logo">
                                        <a href="index.html">
                                            <img src="{{asset('frontend/images/logo/logo.svg')}}" alt="">
                                        </a>
                                    </div>
                                    <div class="footer-address">
                                        <p>549 Oak St.Crystal Lake, IL 60014</p>
                                        <a href="contact.html" class="tf-btn-default fw-6">GET DIRECTION<i class="icon-arrowUpRight"></i></a>
                                    </div>
                                    <ul class="footer-info">
                                        <li>
                                            <i class="icon-mail"></i>
                                            <p>themesflat@gmail.com</p>
                                        </li>
                                        <li>
                                            <i class="icon-phone"></i>
                                            <p>315-666-6688</p>
                                        </li>
                                    </ul>
                                    <ul class="tf-social-icon">
                                        <li><a href="#" class="social-facebook"><i class="icon icon-fb"></i></a></li>
                                        <li><a href="#" class="social-twiter"><i class="icon icon-x"></i></a></li>
                                        <li><a href="#" class="social-instagram"><i class="icon icon-instagram"></i></a></li>
                                        <li><a href="#" class="social-tiktok"><i class="icon icon-tiktok"></i></a></li>
                                        <li><a href="#" class="social-amazon"><i class="icon icon-amazon"></i></a></li>
                                        <li><a href="#" class="social-pinterest"><i class="icon icon-pinterest"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="footer-menu">
                                    <div class="footer-col-block">
                                        <div class="footer-heading text-button footer-heading-mobile">
                                            Infomation
                                        </div>
                                        <div class="tf-collapse-content">
                                          
                                        </div>
                                    </div>
                                    <div class="footer-col-block">
                                        <div class="footer-heading text-button footer-heading-mobile">
                                            Customer Services
                                        </div>
                                        <div class="tf-collapse-content">
                                            <ul class="footer-menu-list">
                                                <li class="text-caption-1">
                                                    <a href="#" class="footer-menu_item">Shipping</a>
                                                </li>
                                                <li class="text-caption-1">
                                                    <a href="#" class="footer-menu_item">Return & Refund</a>
                                                </li>
                                                <li class="text-caption-1">
                                                    <a href="#" class="footer-menu_item">Privacy Policy</a>
                                                </li>
                                                <li class="text-caption-1">
                                                    <a href="#l" class="footer-menu_item">Terms & Conditions</a>
                                                </li>
                                                <li class="text-caption-1">
                                                    <a href="#" class="footer-menu_item">Orders FAQs</a>
                                                </li>
                                                <li class="text-caption-1">
                                                    <a href="#" class="footer-menu_item">My Wishlist</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="footer-col-block">
                                    <div class="footer-heading text-button footer-heading-mobile">
                                        Newletter
                                    </div>
                                    <div class="tf-collapse-content">
                                       
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="footer-bottom">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <div class="footer-bottom-wrap">
                                    <div class="left">
                                        <p class="text-caption-1">©2024 Modave. All Rights Reserved.</p>
                                        <div class="tf-cur justify-content-end">
                                            <div class="tf-currencies">
                                                <select class="image-select center style-default type-currencies">
                                                    <option selected data-thumbnail="{{asset('frontend/images/country/us.svg')}}">USD</option>
                                                </select>
                                            </div>
                                            <div class="tf-languages">
                                                <select class="image-select center style-default type-languages">
                                                    <option>English</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tf-payment">
                                        <p class="text-caption-1">Payment:</p>
                                        <ul>
                                            <li>
                                                <img src="{{asset('frontend/images/payment/img-1.png')}}" alt="image-product">
                                            </li>
                                            <li>
                                                <img src="{{asset('frontend/images/payment/img-2.png')}}" alt="">
                                            </li>
                                            <li>
                                                <img src="{{asset('frontend/images/payment/img-3.png')}}" alt="">
                                            </li>
                                            <li>
                                                <img src="{{asset('frontend/images/payment/img-4.png')}}" alt="">
                                            </li>
                                            <li>
                                                <img src="{{asset('frontend/images/payment/img-5.png')}}" alt="">
                                            </li>
                                            <li>
                                                <img src="{{asset('frontend/images/payment/img-6.png')}}" alt="">
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Cart Modal -->
<div class="modal fade" id="cartModal" tabindex="-1" role="dialog" aria-labelledby="cartModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cartModalTitle">Modal title</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                {{-- <button type="button" class="btn btn-primary check-out">Check out</button> --}}
                <a href="{{ route('checkout') }}" class="btn btn-primary check-out">Check Out</a>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->