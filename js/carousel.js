$(document).ready(function(){		//mi archivo para las funciones de mi carousel 

	$('#carousel').carouFredSel({
		width: '100%',
		align: false,
		items: {
			width: $('#contenedorCarousel').width() * 0.15,
			height: 500,
			visible: 1,
			minimum: 1
		},
		scroll: {
			items: 1,
			timeoutDuration : 5000,
			onBefore: function(data) {
 
				//	encuentra el slide actual y el siguiente
				var currentSlide = $('.slide.active', this),
					nextSlide = data.items.visible,
					_width = $('#contenedorCarousel').width();
 
				//	adaptalo a la version perqueña
				currentSlide.stop().animate({
					width: _width * 0.15
				});		
				currentSlide.removeClass( 'active' );
 
				//	oculta el slide actual
				data.items.old.add( data.items.visible ).find( '.slide-block' ).stop().fadeOut();					
 
				//	cuando haga click que me haga la animación
				nextSlide.addClass( 'active' );
				nextSlide.stop().animate({
					width: _width * 0.7
				});						
			},
			onAfter: function(data) {
				//	muestra activo el slide actual
				data.items.visible.last().find( '.slide-block' ).stop().fadeIn();
			}
		},
		onCreate: function(data){
 
			var newitems = $('.slide',this).clone( true ),
				_width = $('#contenedorCarousel').width();
 
			$(this).trigger( 'insertItem', [newitems, newitems.length, false] );
 
			//	muestra la imagenes 
			$('.slide', this).fadeIn();
			$('.slide:first-child', this).addClass( 'active' );
			$('.slide', this).width( _width * 0.15 );
 
			$('.slide:first-child', this).animate({
				width: _width * 0.7
			});
 
			//	muestra primero el panel del slide
			$(this).find( '.slide-block' ).hide();
			$(this).find( '.slide.active .slide-block' ).stop().fadeIn();
		}
	});
 
	//	Evento cuando hago click
	$('#carousel').children().click(function() {
		$('#carousel').trigger( 'slideTo', [this] );
	});
 
	//	para que se adapte al tamaño de mi pantalla
	$(window).resize(function(){
 
		var slider = $('#carousel'),
			_width = $('#contenedorCarousel').width();
 
		//	muestra las imagenes
		slider.find( '.slide' ).width( _width * 0.15 );
 
		slider.find( '.slide.active' ).width( _width * 0.7 );
 
		//	actualiza el anchi del slide
		slider.trigger( 'configuration', ['items.width', _width * 0.15] );
	});
 

});