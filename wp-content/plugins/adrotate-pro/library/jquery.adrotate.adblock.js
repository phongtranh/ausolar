/*
Detect Ad Blocker tools in browsers and show a nag window in response
Arnan de Gans from AJdG Solutions (http://meandmymac.net, https://ajdg.solutions)
Version: 0.1
With help from: N/a
Original code: Arnan de Gans
*/

/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2015 AJdG Solutions (Arnan de Gans). All Rights Reserved.
*  ADROTATE is a trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

/*
== Settings ==
time : 		Seconds the layover is shown [integer: seconds, defaults to 5]
content :	Nag message [string: defaults to 'Ad blocker detected! Please wait %time% seconds or disable your ad blocker!', %time% is replaced with the remaining secconds]
fade :		Fade effect for the nag message [boolean: defaults to true]
speed :		Fade speed [integer: milliseconds, defaults to 200]
*/

(function($){
    $.fn.extend({
        adblockdetect: function(options) {
			var defaults = {
				time : 5,
				content : 'Ad blocker detected! Please wait %time% seconds or disable your ad blocker!',
				fade : true,
				speed : 300
			}
			
			var options = $.extend(defaults, options);
			var adblockTimer = false;
			var html = '<div id="adblock_notice"><div id="adblock_notice_bg"></div><div id="adblock_notice_content">%content%</div></div>';
			
            return this.each(function() {
				init(this);
            });
			
			function init(e) {
				$(e).prepend(html.replace('%content%', options.content.replace('%time%', '<span id="adblock_notice_time">' + options.time.toString() + '</span>')));
				resizeAdblockNotice();
				$('#adblock_notice').hide();
				$(window).resize(function() { resizeAdblockNotice(); } );
				
				if(detectAdBlock()) {
					adblockTimer = setTimeout(function() { countdown() }, 1000);
					
					if(options.fade) {
						$('#adblock_notice').fadeIn(options.speed);
					} else {
						$('#adblock_notice').show();
					}

					$(document).bind( "click", function(e) { e.preventDefault() });
				}
			}
			
			function unblock() {
				if(options.fade) {
					var s = options.speed;
					$('#adblock_notice').fadeOut(s);
					setTimeout(function() { $('#adblock_notice').remove(); }, s);
				} else {
					$('#adblock_notice').remove();
				}
				
				$(document).unbind( "click" );
				clearTimeout(adblockTimer);
			}

			function countdown() {
				if(options.time <= 0) {
					unblock();
					return;
				}
				
				options.time -= 1;
				$('#adblock_notice_time').html(options.time);
				setTimeout(function() { countdown() }, 1000);
			}
			
			function resizeAdblockNotice() {
				$('#adblock_notice_bg').css('width', ($(window).width()).toString() + 'px');
				$('#adblock_notice_bg').css('height', ($(window).height()).toString() + 'px');
				$('#adblock_notice_content').css('top', (($(window).height() / 2) - ($('#adblock_notice_content').height() / 2)).toString() + 'px');
				$('#adblock_notice_content').css('left', (($(window).width() / 2) - ($('#adblock_notice_content').width() / 2)).toString() + 'px');
			}
			
			function detectAdBlock() {
				if($('img.adblock').css('display') == 'none' || $('img.adblock').height() == 0) {
					return true;
				}
				return false;
			}
		}
	});       
})(jQuery);