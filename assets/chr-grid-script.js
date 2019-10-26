jQuery(function($){ 

	var theCounter = 0;
	var maximumCompareLimit = 5
	$('.comparegrid-description').clone().prependTo('.comparegrid-content');
	$('.comparegrid-title').clone().prependTo('.comparegrid-content');

	if (typeof maybeObject != "undefined") {
		for (var property in chr_urlquery_parameters.urlQuery) {
		  if (chr_urlquery_parameters.urlQuery.hasOwnProperty(property)) {
		  	var result = chr_urlquery_parameters.urlQuery[property].split("%7C");
			for (var i = 0; i < result.length; i++) {
				$('.chr-search-sidebar .filed-value').each(function() {
					if ($(this).text() === property) {
						$(this).closest('.panel').find('.check-container').each(function() {
							if ($(this).find('small').text() === result[i]) {
								$(this).closest('.check-container').find('input[type=checkbox]').prop('checked', true);
								return false; 
							}
						});
					}
				});
			}
		  }
	}
	}
	
	$('.submit-filter').on('click', function(e) {
		e.preventDefault();
		var theLetter = '';

		if (chr_urlquery_parameters.urlQuery.hasOwnProperty('letter')) {
			theLetter = chr_urlquery_parameters.urlQuery.letter;
		}

		delete chr_urlquery_parameters.urlQuery;
		chr_urlquery_parameters.urlQuery = {};
		if (theLetter != '') {
			chr_urlquery_parameters.urlQuery["letter"] = theLetter;
		} else {
			chr_urlquery_parameters.urlQuery = {};
		}

		$(this).closest('.chr-search-sidebar').find('input[type=checkbox]:checked').each(function() {
			var theFieldName = $(this).closest('.panel').find('.filed-value').text();
			var theFieldValue = $(this).closest('.check-container').find('small').text();
			if (chr_urlquery_parameters.urlQuery[theFieldName]) {
				chr_urlquery_parameters.urlQuery[theFieldName] += '%7C' + theFieldValue;
			} else {
				chr_urlquery_parameters.urlQuery[theFieldName] = theFieldValue;
			}
		});
		var theMainLink = chr_urlquery_parameters.actualLink;
		console.log(theMainLink);
		var theLinkCounter = 0;
		for (var property in chr_urlquery_parameters.urlQuery) {
		  if (chr_urlquery_parameters.urlQuery.hasOwnProperty(property)) {
		    if (theLinkCounter == 0) {
		    	theMainLink += '?' + property + '=' + encodeURIComponent(chr_urlquery_parameters.urlQuery[property]); 
		    } else {
		    	theMainLink += '&' + property + '=' + encodeURIComponent(chr_urlquery_parameters.urlQuery[property]);  
		    }
		    theLinkCounter++;
		  }
		}
		window.location = theMainLink;
	});

	$('.chr-search-sidebar .clear-all').on('click', function(e) {
		e.preventDefault();
		if (chr_urlquery_parameters.urlQuery.hasOwnProperty('letter')) {
			window.location = chr_urlquery_parameters.actualLink + '?letter=' + chr_urlquery_parameters.urlQuery.letter;
		} else {
			window.location = chr_urlquery_parameters.actualLink;
		}
	});

	$theLetter = $('input#the-letter-value').val();
	$('[data-letter-value = ' + $theLetter + ']').addClass('active');

	$('.grid-load-more').click(function(){
 
		var button = $(this),
		    data = {
			'action': 'loadmore',
			'query': chr_grid_parameters.posts, // that's how we get params from wp_localize_script() function
			'_ajax_nonce': chr_grid_parameters.nonce,
			'page' : chr_grid_parameters.current_page
		};
 
		$.ajax({ // you can also use $.post here
			url : chr_grid_parameters.ajaxurl, // AJAX handler
			data : data,
			type : 'POST',
			beforeSend : function ( xhr ) {
				button.text('Wird geladen ...'); // change the button text, you can also add a preloader image
			},
			success : function( data ) {
				if( data ) { 					
					button.text( 'Mehr laden' ).prev().find('.grid-item:last-child').after(data); // insert new posts
					chr_grid_parameters.current_page++;

					if (theCounter >= maximumCompareLimit) {
						$('.the-grid .grid-item .check-container input[type="checkbox"]').attr('disabled', 'disabled');
						$('.the-grid .grid-item .check-container').addClass('disabled');
					}					
 
					if ( chr_grid_parameters.current_page == chr_grid_parameters.max_page ) 
						button.remove(); // if last page, remove the button
 
					// you can also fire the "post-load" event here if you use a plugin that requires it
					// $( document.body ).trigger( 'post-load' );
				} else {
					button.remove(); // if no data, remove the button as well
				}
			}
		});
	});

	var acc = document.getElementsByClassName("accordion");
	var i;

	for (i = 0; i < acc.length; i++) {
	  acc[i].addEventListener("click", function() {
	    this.classList.toggle("active");
	    var panel = this.nextElementSibling;
	    if (panel.style.maxHeight){
	      panel.style.maxHeight = null;
	    } else {
	      panel.style.maxHeight = panel.scrollHeight + "px";
	    } 
	  });
	}

	$('.chr-input').on('focus', function() {
		$(this).parent().find('.dropdown-content').addClass('show');
	});

	$('.chr-input').on('blur', function() {
		$(this).parent().find('.dropdown-content').removeClass('show');
	});

	$('.chr-input').on('keyup', function() {
		  var input, filter, ul, li, a, i;
		  input = $(this)[0];
		  $(this).parent().find('.dropdown-content').show();
		  filter = input.value.toUpperCase();
		  div = $(this).parent().find('.chr-dropdown')[0];
		  a = div.getElementsByTagName("a");
		  for (i = 0; i < a.length; i++) {
		    txtValue = a[i].textContent || a[i].innerText;
		    if (txtValue.toUpperCase().indexOf(filter) > -1) {
		      a[i].style.display = "";
		    } else {
		      a[i].style.display = "none"; 
		    }
		  }
	});

	$('.the-grid').on('click', '.grid-item .check-container input[type="checkbox"]', function() {
		var theCompareWrapper = $('.compare-wrapper');
		var theGridItem = $(this).parents('.grid-item');
		var theGridItemId = theGridItem.attr('data-inner-value');
		var theGridItemTitle = theGridItem.find('h3');
		var theCompareStorage = '';
		if ($(this).prop('checked') !== true) {
			theCounter--;
			theCompareWrapper.find('.the-counter').text(theCounter);
			theCompareWrapper.find('div[data-inner-value="' + theGridItemId + '"]').remove();
			if (!$('.compare-wrapper').find('.compare-item').length) {
				$('.compare-wrapper').removeClass('show');
			}		
		} else {
			theCounter++;
			theCompareWrapper.prepend('<div data-inner-value="' + theGridItemId + '" class="compare-item">' + theGridItemTitle.text() + '<span>x</span></div>');
			theCompareWrapper.find('.the-counter').text(theCounter);
			theCompareWrapper.addClass('show');
		}
		if (theCounter >= maximumCompareLimit) {
			$('.the-grid .grid-item .check-container input[type="checkbox"]').attr('disabled', 'disabled');
			$('.the-grid .grid-item .check-container').addClass('disabled');
		}
		
	});

	$('.compare-wrapper').on('click', '.compare-item span', function() {
		theCounter--;
		$('.compare-wrapper').find('.the-counter').text(theCounter);
		$('.the-grid .grid-item .check-container input[type="checkbox"]').removeAttr('disabled');
		$('.the-grid .grid-item .check-container').removeClass('disabled');		
		var theCompareItem = $(this).parent();
		var theCompareItemId = theCompareItem.attr('data-inner-value');
		$('.the-grid .grid-item[data-inner-value="' + theCompareItemId + '"] input[type="checkbox"]').prop('checked', false);
		theCompareItem.remove();
		if (!$('.compare-wrapper').find('.compare-item').length) {
			$('.compare-wrapper').removeClass('show');
		}
	});

	$('.compare-please').on('click', function() {
		var theComparePath = $(this).attr('data-path');
		theComparePath += '?compareitems=';
		$('.compare-wrapper .compare-item').each(function() {
			var theCompareId = $(this).attr('data-inner-value');
			theComparePath += theCompareId + '%7C';
		});
		theComparePath = theComparePath.substring(0, theComparePath.length - 3);
		window.location = theComparePath;
	});

	if ( $.isFunction($.fn.select2) ) {
		if ($('select.items-select').length) {
			$('select.items-select').select2();
			$('select.items-select').each(function() {
				$(this).val($(this).closest('.compare-item').attr('data-value-id'));
				$(this).trigger('change');
			});
			$('select.items-select').on('select2:select', function() {
				var thePureUrl = $('.the-pure-url').val();
				thePureUrl += '?compareitems=';
				$('select.items-select').each(function() {
				if (typeof $(this).select2('data')[0] != "undefined") {
					if($(this).select2('data')[0].hasOwnProperty('id')){
					    thePureUrl += $(this).select2('data')[0].id + '%7C';
					}				   
				}					
				});
				thePureUrl = thePureUrl.substring(0, thePureUrl.length - 3);
				window.location = thePureUrl;
			});
			$('.close-icon').on('click', function() {
				var thePureUrl = $('.the-pure-url').val();
				thePureUrl += '?compareitems=';
				$(this).closest('.compare-item').find('.items-select').addClass('toberemoved');
				$(' select.items-select:not(.toberemoved)').each(function() {
				if (typeof $(this).select2('data')[0] != "undefined") {
					if($(this).select2('data')[0].hasOwnProperty('id')){
					    thePureUrl += $(this).select2('data')[0].id + '%7C';
					}				   
				}					
				});
				thePureUrl = thePureUrl.substring(0, thePureUrl.length - 3);
				window.location = thePureUrl;				
			});			
		}
	}

});