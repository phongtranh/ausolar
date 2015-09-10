jQuery(function($) 
{
	$(document).on('focusin', '.field, textarea', function() {
		if(this.title==this.value) {
			this.value = '';
		}
	}).on('focusout', '.field, textarea', function(){
		if(this.value=='') {
			this.value = this.title;
		}
	});

	$('.stars').each(function(){
		$(this).raty({
			start: 3,
			path: './css/images/'
		});
	});

	$(".sq_type .tab").hide();
	$(".sq_type .tabs .nav li:first").addClass("active").show();
	$(".sq_type .tab:first").show();

	$(".sq_type .tabs .nav li").click(function() {
		$(".sq_type .tabs .nav li").removeClass("active");
		$(this).addClass("active");
		$(".sq_type .tab").hide();
		var activeTab = $(this).find("a").attr("href");
		$(activeTab).fadeIn();
		return false;
	});

	$(".sq_type_1 .tab").hide();
	$(".sq_type_1 .tabs .nav li:first").addClass("active").show();
	$(".sq_type_1 .tab:first").show();

	$(".sq_type_1 .tabs .nav li").click(function() {
		$(".sq_type_1 .tabs .nav li").removeClass("active");
		$(this).addClass("active");
		$(".sq_type_1 .tab").hide();
		var activeTab = $(this).find("a").attr("href");
		$(activeTab).fadeIn();
		return false;
	});

	$(".sq_type_2 .tab").hide();
	$(".sq_type_2 .tabs .nav li:first").addClass("active").show();
	$(".sq_type_2 .tab:first").show();

	$(".sq_type_2 .tabs .nav li").click(function() {
		$(".sq_type_2 .tabs .nav li").removeClass("active");
		$(this).addClass("active");
		$(".sq_type_2 .tab").hide();
		var activeTab = $(this).find("a").attr("href");
		$(activeTab).fadeIn();
		return false;
	});

	$(".sq_type_3 .tab").hide();
	$(".sq_type_3 .tabs .nav li:first").addClass("active").show();
	$(".sq_type_3 .tab:first").show();

	$(".sq_type_3 .tabs .nav li").click(function() {
		$(".sq_type_3 .tabs .nav li").removeClass("active");
		$(this).addClass("active");
		$(".sq_type_3 .tab").hide();
		var activeTab = $(this).find("a").attr("href");
		$(activeTab).fadeIn();
		return false;
	});

	$(".sq_type_4 .tab").hide();
	$(".sq_type_4 .tabs .nav li:first").addClass("active").show();
	$(".sq_type_4 .tab:first").show();

	$(".sq_type_4 .tabs .nav li").click(function() {
		$(".sq_type_4 .tabs .nav li").removeClass("active");
		$(this).addClass("active");
		$(".sq_type_4 .tab").hide();
		var activeTab = $(this).find("a").attr("href");
		$(activeTab).fadeIn();
		return false;
	});

	$(".sq_type_5 .tab").hide();
	$(".sq_type_5 .tabs .nav li:first").addClass("active").show();
	$(".sq_type_5 .tab:first").show();

	$(".sq_type_5 .tabs .nav li").click(function() {
		$(".sq_type_5 .tabs .nav li").removeClass("active");
		$(this).addClass("active");
		$(".sq_type_5 .tab").hide();
		var activeTab = $(this).find("a").attr("href");
		$(activeTab).fadeIn();
		return false;
	});

	$(".sq_type_6 .tab").hide();
	$(".sq_type_6 .tabs .nav li:first").addClass("active").show();
	$(".sq_type_6 .tab:first").show();

	$(".sq_type_6 .tabs .nav li").click(function() {
		$(".sq_type_6 .tabs .nav li").removeClass("active");
		$(this).addClass("active");
		$(".sq_type_6 .tab").hide();
		var activeTab = $(this).find("a").attr("href");
		$(activeTab).fadeIn();
		return false;
	});

	$(".sq_type_7 .tab").hide();
	$(".sq_type_7 .tabs .nav li:first").addClass("active").show();
	$(".sq_type_7 .tab:first").show();

	$(".sq_type_7 .tabs .nav li").click(function() {
		$(".sq_type_7 .tabs .nav li").removeClass("active");
		$(this).addClass("active");
		$(".sq_type_7 .tab").hide();
		var activeTab = $(this).find("a").attr("href");
		$(activeTab).fadeIn();
		return false;
	});

	if( typeof($.browser) != 'undefined' && $.browser.msie && $.browser.version == 8 ){
		$('.widget-info h3.left-header').css('letter-spacing', '0');
	}

	$(function() {
		$('.tabs-nav a').click(function() {
			if (!$(this).parent().hasClass('active')) {
				var _index = $(this).parent().index();
				$(this).parents('.tabs-nav').find('li').removeClass('active');
				$(this).parent().addClass('active');
				$(this).parents('.box').find('.tab').stop(true, true).fadeOut().eq(_index).fadeIn();
			};
			return false;
		});
	});
});