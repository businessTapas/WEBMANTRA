// $(document).ready(function () {
// 	$.ajaxSetup({
// 		headers: {
// 			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
// 		}
// 	});
// });
// Select all links with hashes
jQuery('a[href*="#"].hash')
	// Remove links that don't actually link to anything
	.not('[href="#"]')
	.not('[href="#0"]')
	.click(function (event) {
		// On-page links
		if (
			location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '')
			&&
			location.hostname == this.hostname
		) {
			// Figure out element to scroll to
			var target = jQuery(this.hash);
			target = target.length ? target : jQuery('[name=' + this.hash.slice(1) + ']');
			// Does a scroll target exist?
			if (target.length) {
				// Only prevent default if animation is actually gonna happen
				event.preventDefault();
				jQuery('html, body').animate({
					scrollTop: target.offset().top
				}, 1000, function () {
					// Callback after animation
					// Must change focus!
					var $target = jQuery(target);
					$target.focus();
					if ($target.is(":focus")) { // Checking if the target was focused
						return false;
					} else {
						$target.attr('tabindex', '-1'); // Adding tabindex for elements not focusable
						$target.focus(); // Set focus again
					}
					;
				});
			}
		}
	});

$('.hash_area a.hash').on('click', function (e) {
	e.preventDefault();

	$('html, body').animate({
		scrollTop: $($(this).attr('href')).offset().top - 170
	}, 500, 'linear');
});



// backto-top btn script
var btn = jQuery('#backto-top');
jQuery(window).scroll(function () {
	if (jQuery(window).scrollTop() > 300) {
		btn.addClass('show');
	} else {
		btn.removeClass('show');
	}
});

btn.on('click', function (e) {
	e.preventDefault();
	jQuery('html, body').animate({ scrollTop: 0 }, '1000');
});
// backto-top btn script end



// Counter
document.addEventListener("DOMContentLoaded", () => {
	function counter(id, start, end, duration) {
		let obj = document.getElementById(id),
			current = start,
			range = end - start,
			increment = end > start ? 1 : -1,
			step = Math.abs(Math.floor(duration / range)),
			timer = setInterval(() => {
				current += increment;
				obj.textContent = current;
				if (current == end) {
					clearInterval(timer);
				}
			}, step);
	}
	jQuery('.counter_txt').each(function (index) {
		var elem_count = jQuery(this).text();
		var elem_id = jQuery(this).attr('id');
		counter(elem_id, 0, elem_count, 4000);
	});
});
// Counter

/* jQuery(document).ready(function ($) {
	jQuery('.stellarnav').stellarNav({
		theme: 'dark',
		breakpoint: 991,
		position: 'right',
	});

	jQuery("#fixed-top").scrollToFixed();
}); */

/* jQuery(document).ready(function () {
	var owl = jQuery('.bestseller');
	owl.owlCarousel({
		margin: 0,
		smartSpeed: 500,
		nav: true,
		dots: false,
		autoplay: true,
		autoplayHoverPause: true,
		loop: false,
		autoplayTimeout: 1700,
		responsive: {
			0: { items: 2 },
			480: { items: 2 },
			576: { items: 2 },
			768: { items: 3 },
			992: { items: 4 },
			1200: { items: 5 }
		}
	})
}); */

/* jQuery(document).ready(function () {
	var owl = jQuery('.category');
	owl.owlCarousel({
		margin: 0,
		smartSpeed: 500,
		nav: true,
		dots: false,
		autoplay: true,
		autoplayHoverPause: true,
		loop: true,
		autoplayTimeout: 1700,
		responsive: {
			0: { items: 2 },
			480: { items: 2 },
			576: { items: 2 },
			768: { items: 2 },
			992: { items: 4 },
			1200: { items: 4 }
		}
	})

	var owl = jQuery('.isotope');
	owl.owlCarousel({
		margin: 0,
		smartSpeed: 500,
		nav: true,
		dots: false,
		autoplay: true,
		autoplayHoverPause: true,
		loop: true,
		autoplayTimeout: 1700,
		responsive: {
			0: { items: 1 },
			480: { items: 2 },
			576: { items: 2 },
			768: { items: 3 },
			992: { items: 4 },
			1200: { items: 5 }
		}
	})
}); */


/* $(document).ready(function () {
	$('#example, #example2, #example3, #example4, #example5, #example6, #example7, #example8').dataTable({
		"ordering": false
	});
}); */



$(".filter-button").click(function () {
	var value = $(this).attr('data-filter');
	if (value == "all") {
		$('.filter').show('1000');
	} else {
		$(".filter").not('.' + value).hide('3000');
		$('.filter').filter('.' + value).show('3000');
	}
	if ($(".filter-button").removeClass("active")) {
		$(this).removeClass("active");
	}
	$(this).addClass("active");
});

$(function () {
	$('.order-now-btn').on('click', function (e) {
		e.preventDefault();
		let url = $(this).data('url');
		$.ajax({
			type: "GET",
			url: url,
			success: function (response) {
				if (response.success) {
					$('#cartCount').text(response.cart_count);
					$('#cartModal .modal-body').html(response.cartView);
					$('#cartModal .modal-footer .check-out').removeClass('d-none');
					$('#cartModal').modal('show');
				} else {
					alert(response.message);
				}
			},
			error: function (xhr) {
				alert(xhr.responseJSON.message); // Show error message if AJAX fails
			}
		});
	})
});

$(function () {
	$('#cart_page').on('click', function (e) {
		e.preventDefault();
		let url = $(this).data('url');
		$.ajax({
			type: "GET",
			url: url,
			// data: "data",
			// dataType: "dataType",
			success: function (response) {
				if (response.success) {
					$('#cartModal .modal-body').html(response.cartView);
					$('#cartModal .modal-footer .check-out').removeClass('d-none');
					$('#cartModal').modal('show');
				} else if (response.empty_cart) {
					$('#cartModal .modal-body').html(response.message);
					$('#cartModal .modal-footer .check-out').addClass('d-none');
					$('#cartModal').modal('show');
				}
			},
			error: function (xhr) {
				alert(xhr.responseJSON.message);
			}
		});
	});
});

$(function () {
	$(document).on('click', '.decreaseQty, .increaseQty', function (e) {
		e.preventDefault();

		let button = $(this);
		let product_id = button.data('id');
		let action = button.data('cart');
		let url = button.data('url');

		$.ajax({
			type: "POST",
			url: url,
			data: {
				product_id: product_id,
				action: action,
				// _token: "{{ csrf_token() }}"
			},
			headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
			success: function (response) {
				if (response.success) {
					// Update quantity input
					$('.product-qty[data-id="' + product_id + '"]').val(response.quantity);

					// Disable/enable buttons based on quantity
					let decreaseBtn = $('.decreaseQty[data-id="' + product_id + '"]');
					let increaseBtn = $('.increaseQty[data-id="' + product_id + '"]');

					if (response.quantity <= 1) {
						decreaseBtn.prop('disabled', true);
					} else {
						decreaseBtn.prop('disabled', false);
					}

					if (response.quantity >= 10) {
						increaseBtn.prop('disabled', true);
					} else {
						increaseBtn.prop('disabled', false);
					}
				} else {
					alert(response.message);
				}
			},
			error: function () {
				alert('Something went wrong, please try again.');
			}
		});
	});
});

$(function () {
	$(document).on('click', '.deleteQty', function (e) {
		e.preventDefault();
		// alert('hii');
		let button = $(this);
		let product_id = button.data('id');
		let url = button.data('url');
		$.ajax({
			type: "GET",
			url: url,
			success: function (response) {
				if (response.success) {
					alert(response.message);
					$('#cartModal .modal-body').html(response.cartView);
					$('#cartCount').text(response.cart_count);
				} else if (response.empty_cart) {
					$('#cartCount').text(response.cart_count);
					$('#cartModal .modal-body').html(response.message);
					$('#cartModal .modal-footer .check-out').addClass('d-none');
					// $('#cartModal').modal('show');
				} else {
					alert(response.message);
				}
			},
			error: function () {
				alert('Something went wrong! Please try again.');
			}
		});
	})
});

$(function () {
	$(document).on('click', '.checkDecr, .checkIncre', function (e) {
		e.preventDefault();

		let button = $(this);
		let product_id = button.data('id');
		let action = button.data('cart');
		let url = button.data('url');

		$.ajax({
			type: "POST",
			url: url,
			data: {
				product_id: product_id,
				action: action,
				// _token: "{{ csrf_token() }}"
			},
			headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
			success: function (response) {
				$('.order-review-container').html(response);
			},
			error: function () {
				alert('Failed to update cart. Please try again.');
			}
		});
	})
});

$(function () {
	$(document).on('click', '.checkDel', function (e) {
		e.preventDefault();

		let button = $(this);
		let product_id = button.data('id');
		let url = button.data('url');
		let homeRouteUrl = button.data('home');


		$.ajax({
			type: "GET",
			url: url,
			success: function (response) {
				if (response.success) {
					if (response.cartCount > 0) {
						$('.order-review-container').html(response.html);
					} else {
						$('.innerpage').html(`
                            <p>Your cart is empty. You will be redirected to the home page shortly.</p>
                            <script>
                                setTimeout(function() {
                                    window.location.href = "${homeRouteUrl}";
                                }, 3000);
                            </script>
                        `);
					}
				}
			}
		});
	});
});

/* $(function () {
	$(document).on('click', '.order-place', function (e) {
		e.preventDefault();
		let form = $('#orderForm')[0];
		let formData = new FormData(form);
		let url = $('#orderForm').attr('action');

		$.ajax({
			type: "POST",
			url: url,
			data: formData,
			processData: false,
			contentType: false,
			beforeSend: function () {
				$('.order-place').prop('disabled', true).text('Placing Order...');
			},
			success: function (response) {
				alert(response.message);
				window.location.href = response.url;
			},
			error: function (xhr) {
				$('.order-place').prop('disabled', false).text('Place Order');

				if (xhr.status === 422) {
					// Validation errors
					let errors = xhr.responseJSON.errors;
					let errorMessages = '';
					for (let field in errors) {
						errorMessages += errors[field].join('<br>') + '<br>';
					}
					alert('Please fix the following errors:\n\n' + errorMessages.replace(/<br>/g, '\n'));
				} else {
					// Other errors (500, etc.)
					alert('Something went wrong. Please try again.');
				}
			}
		});
	});
}); */

$(function () {
	$('.view-btn').on('click', function (e) {
		e.preventDefault();
		let url = $(this).data('url');
		$.ajax({
			type: "GET",
			url: url,
			success: function (response) {
				if (response.success) {
					$('#viewModal .modal-body').html(response.view);
					$('#viewModal').modal('show');
				} else {
					alert(response.message);
				}
			},
			error: function (xhr) {
				alert(xhr.responseJSON.message || 'Something went wrong'); // Show error message if AJAX fails
			}
		});
	})
});

$(function () {
	$(document).on('click', '.profile-update', function (e) {
		e.preventDefault();
		let form = $('#profileUpdate')[0];
		let formData = new FormData(form);
		let url = $('#profileUpdate').attr('action');
		$.ajax({
			type: "POST",
			url: url,
			data: formData,
			processData: false,
			contentType: false,
			beforeSend: function () {
				$('.profile-update').prop('disabled', true).text('updating...');
			},
			success: function (response) {
				alert(response.message);
				location.reload();
			},
			error: function (xhr) {
				alert(xhr.responseJSON.message || 'Something went wrong');
				location.reload();
			}
		});
	})
})