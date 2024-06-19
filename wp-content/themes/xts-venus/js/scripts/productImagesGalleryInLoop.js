/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on( 'xtsSingleProductAccordionClick xtsWishlistRemoveSuccess xtsProductTabLoaded xtsElementorProductTabsReady xtsPjaxComplete xtsProductLoadMoreReInit', function() {
			XTSThemeModule.imagesGalleryInLoop();
		}
	);

	$.each(
		[
		'frontend/element_ready/xts_products.default',
		'frontend/element_ready/xts_single_product_tabs.default'
		],
		function(index, value) {
			XTSThemeModule.xtsElementorAddAction( value, function() {
				XTSThemeModule.imagesGalleryInLoop();
			});
		}
	);

	XTSThemeModule.imagesGalleryInLoop = function() {
		var $products = $( '.xts-product' );

		$products.each( function () {
			let $product        = $( this );
			let $galleryWrapper = $product.find( '.xts-product-grid-slider-wrapp' );

			if ( $galleryWrapper.length && ! $galleryWrapper.hasClass( 'xts-inited' ) ) {
				addGalleryLoopEvents( $product );

				$galleryWrapper.addClass( 'xts-inited' );
			}
		});

		function addGalleryLoopEvents( $product ) {
			$product.on( 'click', '.xts-prev, .xts-next', function(e) {
				e.preventDefault();

				let $navButton      = $( this );
				let $galleryWrapper = $navButton.parents( '.xts-product-grid-slider-wrapp' );
				let $product        = $navButton.parents( '.xts-product' );

				if ( ! $galleryWrapper.hasClass( 'xts-nav-arrows' ) && XTSThemeModule.$window.width() > 1024 || ! $galleryWrapper.hasClass( 'xts-nav-md-arrows' ) && XTSThemeModule.$window.width() <= 1024 || $product.hasClass( 'xts-product-swatched' ) || $product.hasClass( 'xts-variation-active' ) ) {
					return;
				}

				let $productImageWrapper = $product.find( '.xts-product-image' );
				let $slides              = $galleryWrapper.find( '.xts-product-grid-slide' );
				let index                = $galleryWrapper.find( '.xts-product-grid-slide.xts-active' ).index();

				if ( $navButton.hasClass( 'xts-prev' ) ) {
					index--;
				} else if ( $navButton.hasClass( 'xts-next' ) ) {
					index++;
				}

				if ( -1 === index ) {
					index = $slides.length - 1;
				} else if ( $slides.length === index ) {
					index = 0;
				}

				updateImage( $productImageWrapper, $slides.eq( index ) );
			});

			function updateImage( $productImageWrapper, $slide ) {
				let $maybeImage           = {};
				let $slides               = $slide.parent().children()
				let $slidePreview         = $slide.parent().find( '.xts-active' );
				let $productImages        = $productImageWrapper.find( '> img' );
				let $productImagePictures = $productImageWrapper.find( 'picture' );

				$slide.siblings().removeClass( 'xts-active' );
				$slide.addClass( 'xts-active' );

				if ( $productImages.length ) {
					$productImages.addClass( 'xts-hide' );

					$maybeImage = $productImageWrapper.find( '.wp-image-' + $slide.data( 'image-id' ) )
				} else if ( $productImagePictures.length ) {
					if ( ! $productImagePictures.first().hasClass( 'wp-image-' + $slides.first().data( 'image-id' ) ) ) {
						$productImagePictures.first().addClass( 'wp-image-' + $slides.first().data( 'image-id' ) )
					}

					$productImagePictures.addClass( 'xts-hide' );

					$maybeImage = $productImageWrapper.find( '.wp-image-' + $slide.data( 'image-id' ) )

					if ( ! $maybeImage.length ) {
						$maybeImage = $productImageWrapper.find( 'img[src=' + $slidePreview.data( 'src' ) + ']' ).parent();
					}
				}

				if ( $maybeImage.length ) {
					$maybeImage.removeClass( 'xts-hide' );

					return;
				}

				let hoverImageUrl    = $slide.data( 'image-src' );
				let hoverImageSrcSet = $slide.data( 'image-srcset' );
				let hoverImageSizes  = $slide.data( 'image-sizes' );

				if ( $productImages.length ) {
					let $newImage = $productImages.first().clone();
					$newImage.attr( 'src', hoverImageUrl );
					$newImage.attr( 'loading', null );

					if ( hoverImageSrcSet ) {
						$newImage.attr( 'srcset', hoverImageSrcSet );
						$newImage.attr( 'sizes', hoverImageSizes );
					} else if ( $newImage.attr( 'srcset' ) ) {
						$newImage.attr( 'srcset', hoverImageUrl );
					}

					$newImage.removeClass( 'xts-hide wp-image-' + $slides.first().data( 'image-id' ) );
					$newImage.addClass( 'wp-image-' + $slide.data( 'image-id' ) );

					$productImages.parent().append( $newImage );
				} else if ( $productImagePictures.length ) {
					let $newPictures = $productImagePictures.first().clone();

					$newPictures.find( 'img' ).attr( 'src', hoverImageUrl );
					$newPictures.find( 'source' ).attr( 'srcset', hoverImageUrl );

					if ( hoverImageSrcSet ) {
						$newPictures.find( 'img' ).attr( 'srcset', hoverImageSrcSet ).attr( 'sizes', hoverImageSizes );
						$newPictures.find( 'source' ).attr( 'srcset', hoverImageSrcSet ).attr( 'sizes', hoverImageSizes );
					} else if ( $newPictures.find( 'img' ).attr( 'srcset' ) ) {
						$newPictures.find( 'img' ).attr( 'srcset', hoverImageUrl );
					}

					$newPictures.removeClass( 'xts-hide wp-image-' + $slides.first().data( 'image-id' ) );
					$newPictures.addClass( 'wp-image-' + $slide.data( 'image-id' ) );

					$productImagePictures.parent().append( $newPictures );
				}
			}

			if ( $product.hasClass('product-type-variable') ) {
				let $form = $product.find('.xts-variations_form');
				let $swatchesWrapper = $product.find('.xts-product-swatches');

				if ( $form.length ) {
					$form.one('show_variation', function () {
						let $productImageWrapper = $product.find( '.xts-product-image' );
						let $slides              = $product.find( '.xts-product-grid-slider-wrapp .xts-product-grid-slide' );

						if ( 1 < $productImageWrapper.find( '> *' ).length ) {
							updateImage( $productImageWrapper, $slides.eq( 0 ) );
						}
					});
				} else if ( $swatchesWrapper.length ) {
					$swatchesWrapper.find('.xts-loop-swatch').on('click', function () {
						if ( $(this).hasClass('xts-active') ) {
							return;
						}

						let $productImageWrapper = $product.find( '.xts-product-image' );
						let $slides              = $product.find( '.xts-product-grid-slider-wrapp .xts-product-grid-slide' );

						if ( 1 < $productImageWrapper.find( '> *' ).length ) {
							updateImage( $productImageWrapper, $slides.eq( 0 ) );
						}
					});
				}
			}
		}
	};

	$( document ).ready( function() {
		XTSThemeModule.imagesGalleryInLoop();
	});
})( jQuery );
