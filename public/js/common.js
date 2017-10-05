$(document).ready(function() {

	/*-----------------------------------/
	/*	TOP NAVIGATION AND LAYOUT
	/*----------------------------------*/

	$('.btn-toggle-fullwidth').on('click', function() {
		if(!$('body').hasClass('layout-fullwidth')) {
			$('body').addClass('layout-fullwidth');

		} else {
			$('body').removeClass('layout-fullwidth');
			$('body').removeClass('layout-default'); // also remove default behaviour if set
		}

		// $(this).find('.lnr').toggleClass('lnr-arrow-left-circle lnr-arrow-right-circle');

		if($(window).innerWidth() < 1025) {
			if(!$('body').hasClass('offcanvas-active')) {
				$('body').addClass('offcanvas-active');
			} else {
				$('body').removeClass('offcanvas-active');
			}
		}
	});

	$(window).on('load', function() {
		if($(window).innerWidth() < 1025) {
			$('.btn-toggle-fullwidth').find('.icon-arrows')
			.removeClass('icon-arrows-move-left')
			.addClass('icon-arrows-move-right');
		}

		// adjust right sidebar top position
		$('.right-sidebar').css('top', $('.navbar').innerHeight());

		// if page has content-menu, set top padding of main-content
		if($('.has-content-menu').length > 0) {
			$('.navbar + .main-content').css('padding-top', $('.navbar').innerHeight());
		}

		// for shorter main content
		if($('.main').height() < $('#sidebar-nav').height()) {
			$('.main').css('min-height', $('#sidebar-nav').height());
		}
	});


	/*-----------------------------------/
	/*	SIDEBAR NAVIGATION
	/*----------------------------------*/

	$('.sidebar a[data-toggle="collapse"]').on('click', function() {
		if($(this).hasClass('collapsed')) {
			$(this).addClass('active');
		} else {
			$(this).removeClass('active');
		}
	});

	if( $('.sidebar-scroll').length > 0 ) {
		$('.sidebar-scroll').slimScroll({
			height: '95%',
			wheelStep: 2,
		});
	}

	$('.notifications-scroll').slimScroll({
		height: 'auto',
		wheelStep: 5,
	});

	/*-----------------------------------/
	/*	PANEL FUNCTIONS
	/*----------------------------------*/

	// panel remove
	$('.panel .btn-remove').click(function(e){

		e.preventDefault();
		$(this).parents('.panel').fadeOut(300, function(){
			$(this).remove();
		});
	});

	// panel collapse/expand
	var affectedElement = $('.panel-body');

	$('.panel .btn-toggle-collapse').clickToggle(
		function(e) {
			e.preventDefault();

			// if has scroll
			if( $(this).parents('.panel').find('.slimScrollDiv').length > 0 ) {
				affectedElement = $('.slimScrollDiv');
			}

			$(this).parents('.panel').find(affectedElement).slideUp(300);
			$(this).find('i.lnr-chevron-up').toggleClass('lnr-chevron-down');
		},
		function(e) {
			e.preventDefault();

			// if has scroll
			if( $(this).parents('.panel').find('.slimScrollDiv').length > 0 ) {
				affectedElement = $('.slimScrollDiv');
			}

			$(this).parents('.panel').find(affectedElement).slideDown(300);
			$(this).find('i.lnr-chevron-up').toggleClass('lnr-chevron-down');
		}
	);


	/*-----------------------------------/
	/*	PANEL SCROLLING
	/*----------------------------------*/

	if( $('.panel-scrolling').length > 0) {
		$('.panel-scrolling .panel-body').slimScroll({
			height: '430px',
			wheelStep: 2,
		});
	}

	if( $('#panel-scrolling-demo').length > 0) {
		$('#panel-scrolling-demo .panel-body').slimScroll({
			height: '175px',
			wheelStep: 2,
		});
	}

	/*-----------------------------------/
	/*	TODO LIST
	/*----------------------------------*/

	$('.todo-list input').change( function() {
		if( $(this).prop('checked') ) {
			$(this).parents('li').addClass('completed');
		}else {
			$(this).parents('li').removeClass('completed');
		}
	});


	/*-----------------------------------/
	/* TOASTR NOTIFICATION
	/*----------------------------------*/

	if($('#toastr-demo').length > 0) {
		toastr.options.timeOut = "false";
		toastr.options.closeButton = true;
		toastr['info']('Hi there, this is notification demo with HTML support. So, you can add HTML elements like <a href="#">this link</a>');

		$('.btn-toastr').on('click', function() {
			$context = $(this).data('context');
			$message = $(this).data('message');
			$position = $(this).data('position');

			if($context == '') {
				$context = 'info';
			}

			if($position == '') {
				$positionClass = 'toast-left-top';
			} else {
				$positionClass = 'toast-' + $position;
			}

			toastr.remove();
			toastr[$context]($message, '' , { positionClass: $positionClass });
		});

		$('#toastr-callback1').on('click', function() {
			$message = $(this).data('message');

			toastr.options = {
				"timeOut": "300",
				"onShown": function() { alert('onShown callback'); },
				"onHidden": function() { alert('onHidden callback'); }
			}

			toastr['info']($message);
		});

		$('#toastr-callback2').on('click', function() {
			$message = $(this).data('message');

			toastr.options = {
				"timeOut": "10000",
				"onclick": function() { alert('onclick callback'); },
			}

			toastr['info']($message);

		});

		$('#toastr-callback3').on('click', function() {
			$message = $(this).data('message');

			toastr.options = {
				"timeOut": "10000",
				"closeButton": true,
				"onCloseClick": function() { alert('onCloseClick callback'); }
			}

			toastr['info']($message);
		});
	}

});

// toggle function
$.fn.clickToggle = function( f1, f2 ) {
	return this.each( function() {
		var clicked = false;
		$(this).bind('click', function() {
			if(clicked) {
				clicked = false;
				return f2.apply(this, arguments);
			}

			clicked = true;
			return f1.apply(this, arguments);
		});
	});

}

// number format function
function number_format (number, decimals, dec_point, thousands_sep) {
  // Strip all characters but numerical ones.
  number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
  var n = !isFinite(+number) ? 0 : +number,
  prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
  sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
  dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
  s = '',
  toFixedFix = function (n, prec) {
   var k = Math.pow(10, prec);
   return '' + Math.round(n * k) / k;
  };

  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
  if (s[0].length > 3) {
   s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '').length < prec) {
   s[1] = s[1] || '';
   s[1] += new Array(prec - s[1].length + 1).join('0');
  }
  return s.join(dec);
}

function getCurrentDateTime()
{
	var d = new Date();
	var months = new Array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');

	var month_array = new Array('January', 'Febuary', 'March', 'April', 'May', 'June', 'July', 'Augest', 'September', 'October', 'November', 'December');

	var currentYear = d.getFullYear();
	var month = d.getMonth();
	var currentMonth = months[month];
	var currentMonth1 = month_array[month];
	var currentDate = d.getDate();
	currentDate = currentDate < 10 ? '0'+currentDate : currentDate;

	var hours = d.getHours();
	var minutes = d.getMinutes();
	var seconds = d.getSeconds();
  if(hours < 10){
    hours = '0'+hours;
  }
	minutes = minutes < 10 ? '0'+minutes : minutes;
	seconds = seconds < 10 ? '0'+seconds : seconds;

	var strDate = currentYear.toString()+currentMonth.toString()+currentDate.toString();
  var strTime = hours.toString() + minutes.toString() + seconds.toString();
  return (strDate + '_' + strTime);
}

// add zero value infront
function pad (str, max) {
  str = str.toString();
  return str.length < max ? pad("0" + str, max) : str;
}

// filter
(function ($) {
  $('#filter').keyup(function () {
    var rex = new RegExp($(this).val(), 'i');
    $('.searchable tr').hide();
    $('.searchable tr').filter(function () {
      return rex.test($(this).text());
    }).show();
  })
}(jQuery));

function markAsRead(notification_id){
  $.get('/markAsRead/'+notification_id);
}

function markAllAsRead(){
  $.get('/markAllAsRead', function(data, status, xhr){
		console.log(data);
		console.log(status);
		console.log(xhr.status);
		if(xhr.status == 200){
			if(data.status == true) location.reload();
			else alert('Error.');
		}
		else{
			alert('Whoops. Error.');
		}
  });
}
