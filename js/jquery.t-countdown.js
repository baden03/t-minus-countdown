/*
 * T- Countdown v2.4.5
 * http://plugins.twinpictures.de/plugins/t-minus-countdown/
 *
 * Copyright 2020, Twinpictures
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, blend, trade,
 * bake, hack, scramble, difiburlate, digest and/or sell copies of the Software,
 * and to permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

(function($){
	var zoneOffset = 0;
	var cacheOffset = 0;
	//var timeOffset = 0;

	function secstotime( seconds ){
		minus = '';
		if(seconds < 0){
			seconds = Math.abs(seconds);
			minus = '- ';
		}
		var d = Math.floor(seconds / (3600*24));
		var h = Math.floor(seconds % (3600*24) / 3600);
		var m = Math.floor(seconds % 3600 / 60);
		var s = Math.floor(seconds % 60);

		var dDisplay = d > 0 ? d + (d == 1 ? " day " : " days ") : "";
		var hDisplay = h > 0 ? h + (h == 1 ? " hour " : " hours ") : "";
		var mDisplay = m > 0 ? m + (m == 1 ? " min. " : " mins. ") : "";
		var sDisplay = s > 0 ? s + (s == 1 ? " sec." : " secs.") : "";
		return minus + dDisplay + hDisplay + mDisplay + sDisplay;
	}

	$.fn.tminusCountDown = function (options) {
		if ($(this)[0] == undefined)
			return;

		config = {};
		$.extend(config, options);
		tminusTargetTime = this.setTminustminusTargetTime(config);
		$.data($(this)[0], 'tminusTargetTime', tminusTargetTime);

		// this is the time from the server
		// it uses the correct timezone
		// it might be cached
		var nowobj = $.parseJSON( tCountAjax['tminusnow'] );
		nowTime = new Date( nowobj.now );

		// This is the time from the user
		// it uses the timezone of the user
		// it will not be cached
		browserTime = new Date();
		if ( config.debug === 'true' ) {
			$('#' + config.id + '-jsnow').html( browserTime );
		}

		// adjust for timezone and cache
		timeOffset = Math.round((browserTime.getTime() - nowTime.getTime())/1000);
		//console.log(timeOffset);
		//nowTime.setSeconds(nowTime.getSeconds() + timeOffset );

		$.ajax({
				method: 'GET',
				url: tCountAjax.api_url + 'now',
				beforeSend : function ( xhr ) {
						xhr.setRequestHeader( 'X-WP-Nonce', tCountAjax.api_nonce );
				},
				success : function( response ) {
					  //console.log( 'now time at line 68: ', nowTime);
						// this is the non-cached time from the server
						serverTime = new Date( nowobj.now );
						restTime = new Date(response.date);
						cacheOffset = Math.round((serverTime.getTime()-restTime.getTime())/1000);

						if ( config.debug === 'true' ) {
							//console.log('debug is set to "' + config.debug +'" for counter id: ' + config.id);
							zoneOffset = Math.round((browserTime.getTime()-restTime.getTime())/1000);
							$('#' + config.id + '-jstzone').html( secstotime( zoneOffset ) );

							seconds = Math.floor((tminusTargetTime.getTime()-restTime.getTime())/1000);
							$('#' + config.id + '-jstime').html( response.date );
							$('#' + config.id + '-jsdiff').html( secstotime(seconds) );
							$('#' + config.id + '-jscached').html( secstotime( cacheOffset ) );
						}
				},
				fail : function( response ) {
					console.log('error: ', response);
				}
		});

		style = config.style;
		$.data($(this)[0], 'style', config.style);

		$.data($(this)[0], 'status', 'play');
		$.data($(this)[0], 'adjusted', 'false');

		$.data($(this)[0], 'id', config.id);

		if ( config.event_id ) {
			$.data($(this)[0], 'event_id', config.event_id);
			$.ajax({
				url: tCountAjax.ajaxurl,
				type : "post",
				dataType : "json",
				data: {
					action : 'tminusevents',
					event_id : config.event_id,
					countdownNonce : tCountAjax.countdownNonce
				},
				success: $.proxy(function( jsonobj ) {
					$.data($(this)[0], 'eventObj', jsonobj);
				}, this)
			});
		}

		if( config.launchtarget ) {
			$.data($(this)[0], 'launchtarget', config.launchtarget);
		}
		if (config.onComplete){
			$.data($(this)[0], 'callback', config.onComplete);
		}
		if (config.hangtime){
			$.data($(this)[0], 'hangtime', config.hangtime);
		}

		$('#' + $(this).attr('id') + ' .' + style + '-digit').each(function(index) {
			digit = $(this).data('digit');
			$(this).html('<div class="tc_top"></div><div class="tc_bottom">'+digit+'</div>');
		});

		//caculate the initial difference in seconds between now and launch
		diffSecs = Math.floor((tminusTargetTime.getTime()-nowTime.getTime())/1000);

		//console.log('line 117', diffSecs);
		$(this).doTminusCountDown($(this).attr('id'), diffSecs, 500);

		return this;
	};

	$.fn.stopTminusCountDown = function () {
		$.data(this[0], 'status', 'stop');
	};

	$.fn.startTminusCountDown = function () {
		$.data(this[0], 'status', 'play');
		this.doTminusCountDown($(this).attr('id'),$.data(this[0], 'diffSecs'), 500);
	};

	$.fn.setTminustminusTargetTime = function (options) {
		var tminusTargetTime = new Date();
		if (options.targetDate){
			tminusTargetTime = new Date(options.targetDate.month + '/' + options.targetDate.day + '/' + options.targetDate.year + ' ' + options.targetDate.hour + ':' + options.targetDate.min + ':' + options.targetDate.sec + (options.targetDate.utc ? ' UTC' : ''));
		}
		else if (options.targetOffset){
			tminusTargetTime.setFullYear(options.targetOffset.year + tminusTargetTime.getFullYear());
			tminusTargetTime.setMonth(options.targetOffset.month + tminusTargetTime.getMonth());
			tminusTargetTime.setDate(options.targetOffset.day + tminusTargetTime.getDate());
			tminusTargetTime.setHours(options.targetOffset.hour + tminusTargetTime.getHours());
			tminusTargetTime.setMinutes(options.targetOffset.min + tminusTargetTime.getMinutes());
			tminusTargetTime.setSeconds(options.targetOffset.sec + tminusTargetTime.getSeconds());
		}

		return tminusTargetTime;
	};

	$.fn.doTminusCountDown = function (id, diffSecs, duration) {
		$this = $('#' + id);
		style = $.data($this[0], 'style');
		adjusted = $.data($this[0], 'adjusted');

		seconds_elm = $('#' + id + ' .' + style + '-seconds_dash');
		minutes_elm = $('#' + id + ' .' + style + '-minutes_dash');
		hours_elm = $('#' + id + ' .' + style + '-hours_dash');
		days_elm = $('#' + id + ' .' + style + '-days_dash');
		weeks_elm = $('#' + id + ' .' + style + '-weeks_dash');
		months_elm = $('#' + id + ' .' + style + '-months_dash');
		years_elm = $('#' + id + ' .' + style + '-years_dash');

		if (diffSecs <= 0){
			if( $.data($this[0], 'launchtarget') != 'countup' ){
				diffSecs = 0;

				// stop the countdown
				$.data($this[0], 'status', 'stop');
			}
		}
		secs = Math.abs(diffSecs % 60);
		mins = Math.floor(Math.abs(diffSecs/60)%60);

		dur = 1000;
		if(adjusted === 'false'){
			dur = 500;
		}

		if( seconds_elm.length){
			$this.dashTminusChangeTo(id, style + '-seconds_dash', secs, duration ? duration : 500);
			if(mins != minutes_elm.data('current')){
				adjusted = 'false';
			}
		}
		if(secs == 59 || adjusted === 'false'){
			if(mins != minutes_elm.data('current')){
				$this.dashTminusChangeTo(id, style + '-minutes_dash', mins, duration ? duration : dur);
				minutes_elm.data('current', mins);
			}
			if(mins == 59 || adjusted === 'false'){
				hours = Math.floor(Math.abs(diffSecs/60/60)%24);
				if(hours != hours_elm.data('current')){
					$this.dashTminusChangeTo(id, style + '-hours_dash', hours, duration ? duration : dur);
					if(hours < 23){
						hours_elm.data('current', hours);
					}
				}

				//when the day changes
				if(hours == 23 && hours != hours_elm.data('current')){
					$this.dashTminusChangeTo(id, style + '-days_dash', days_elm.data('next'), duration ? duration : 1000);
					hours_elm.data('current', hours);
					if( weeks_elm.length && weeks_elm.data('next') !== undefined){
						$this.dashTminusChangeTo(id, style + '-weeks_dash', weeks_elm.data('next'), duration ? duration : 1000);
					}
					if( months_elm.length && months_elm.data('next') !== undefined){
						$this.dashTminusChangeTo(id, style + '-months_dash', months_elm.data('next'), duration ? duration : 1000);
					}
					years_elm = $('#' + id + ' .' + style + '-years_dash');
					if( years_elm.length && years_elm.data('next') !== undefined){
						$this.dashTminusChangeTo(id, style + '-years_dash', years_elm.data('next'), duration ? duration : 1000);
					}
				}
			}
		}

    $.data($(this)[0], 'adjusted', 'true');
		//console.log('adjusted is now: ', $.data($this[0], 'adjusted') );

		//events
		if( $.data($this[0], 'event_id') ){
			$this.checkEvent(id, diffSecs);
		}

		if (diffSecs > 0 || $.data($this[0], 'launchtarget') == 'countup'){
			if($.data($this[0], 'status') == 'play'){

				//recaculate diffSecs
				//nowTime = new Date();
				//nowTime.setSeconds(nowTime.getSeconds());
				//console.log( 'nowTime at line 228: ', nowTime);
				//nowTime.setSeconds(nowTime.getSeconds() +1 );
				//console.log('hey dude: ', tminusTargetTime.valueOf(), $.data($this[0], 'tminusTargetTime').valueOf() );

				t = setTimeout( function() {
					newTime = new Date();
					newTime.setSeconds( newTime.getSeconds() - zoneOffset);
					diffSecs = Math.floor(($.data($this[0], 'tminusTargetTime').getTime()-newTime.getTime())/1000);
					$this.doTminusCountDown(id, diffSecs);
				} , 1000);
			}
		}
		//cb = $.data($this[0], 'callback')
		else if ($.data($this[0], 'callback')){
			if($.data($this[0], 'hangtime')){
				//phone's ringing dude.
			}
			$.data($this[0], 'callback')();
		}

	};

	$.fn.dashTminusChangeTo = function(id, dash, n, duration) {

		$this = $('#' + id);
		style = $.data($this[0], 'style');

		//console.log('.' + dash + ' .' + style + '-digit');
		for (var i=($this.find('.' + dash + ' .' + style + '-digit').length-1); i>=0; i--){
			var d = n%10;
			n = (n - d) / 10;
			$this.digitTminusChangeTo('#' + $this.attr('id') + ' .' + dash + ' .' + style + '-digit:eq('+i+')', d, duration);
		}
	};

	$.fn.digitTminusChangeTo = function (digit, n, duration) {
		if (!duration){
			duration = 500;
		}

		//check for number translation object
		/* for example *****
		var numTransObj = {
			0: 'a',
			1: 'b',
			2: 'c',
			3: 'd',
			4: 'e',
			5: 'f',
			6: 'g',
			7: 'h',
			8: 'i',
			9: 'j'
		};
		*******************/
		if(typeof numTransObj != "undefined"){
			n = numTransObj[n];
		}

		if ($(digit + ' div.tc_top').html() != n + ''){
			$(digit + ' div.tc_top').css({'display': 'none'});
			$(digit + ' div.tc_top').html((n ? n : '0')).stop(true, true, true).slideDown(duration);
			$(digit + ' div.tc_bottom').stop(true, true, true).animate({'height': ''}, duration, function() {
				$(digit + ' div.tc_bottom').html($(digit + ' div.tc_top').html());
				$(digit + ' div.tc_bottom').css({'display': 'block', 'height': ''});
				$(digit + ' div.tc_top').hide().slideUp(10);
			});
		}
	};

	$.fn.checkEvent = function () {
		if ( ! $.data( this[0], 'eventObj' ) ) {
			return;
		}

		var eventObj = $.data( this[0], 'eventObj' ).tevent;
		for (var key in eventObj) {
			if (eventObj[key].hasOwnProperty('tevents_event_time') && eventObj[key]['tevents_event_time'] == $.data( this[0], 'diffSecs' ) ) {
				//content (even if it's blank)
				if (eventObj[key].hasOwnProperty('tevents_target_elem') && eventObj[key]['tevents_event_target'] == 'other') {
					target_elem = eventObj[key]['tevents_target_elem'];
				}
				else{
					target_elem = '#' + $.data( this[0], 'id' ) + '-' + eventObj[key]['tevents_event_target'];
				}
				$(target_elem).html( eventObj[key]['tevents_event_content'] );

				//function
				if ( eventObj[key]['tevents_event_function'] ) {
					var fn = window[ eventObj[key]['tevents_event_function'] ];
					if(typeof fn === 'function') {
					    fn();
					}
				}
			}
		}
	}

})(jQuery);
