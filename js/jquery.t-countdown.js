/*
 * T- Countdown v2.4.0
 * http://plugins.twinpictures.de/plugins/t-minus-countdown/
 *
 * Copyright 2018, Twinpictures
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
	$.fn.tminusCountDown = function (options) {
		if ($(this)[0] == undefined)
			return;

		config = {};
		$.extend(config, options);
		tminusTargetTime = this.setTminustminusTargetTime(config);
		$.data($(this)[0], 'tminusTargetTime', tminusTargetTime);

		var nowobj = $.parseJSON( tminusnow );
		nowTime = new Date(nowobj.now);

		//ofset from browser time
		browserTime = new Date();
		timeOffset = Math.floor((nowTime.valueOf()-browserTime.valueOf())/1000);
		$.data($(this)[0], 'timeOffset', timeOffset);

		style = config.style;
		$.data($(this)[0], 'style', config.style);

		$.data($(this)[0], 'status', 'play');
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
		if (config.omitWeeks){
			$.data($(this)[0], 'omitWeeks', config.omitWeeks);
		}
		$('#' + $(this).attr('id') + ' .' + style + '-digit').html('<div class="tc_top"></div><div class="tc_bottom"></div>');

		//caculate the initial difference in seconds between now and launch
		diffSecs = Math.floor((tminusTargetTime.valueOf()-nowTime.valueOf())/1000);
		//console.log(diffSecs);
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

		if (diffSecs <= 0){
			if( $.data($this[0], 'launchtarget') != 'countup' ){
				diffSecs = 0;
				$.data($this[0], 'status', 'stop');
			}
		}
		secs = Math.abs(diffSecs % 60);
		mins = Math.floor(Math.abs(diffSecs/60)%60);
		hours = Math.floor(Math.abs(diffSecs/60/60)%24);
		if ($.data($this[0], 'omitWeeks') == 'true'){
			days = Math.floor(Math.abs(diffSecs/60/60/24));
			weeks = 0;
		}
		else{
			days = Math.floor(Math.abs(diffSecs/60/60/24)%7);
			weeks = Math.floor(Math.abs(diffSecs/60/60/24/7));
		}
    	style = $.data($this[0], 'style');

		$this.dashTminusChangeTo(id, style + '-seconds_dash', secs, duration ? duration : 500);
		$this.dashTminusChangeTo(id, style + '-minutes_dash', mins, duration ? duration : 1000);
		$this.dashTminusChangeTo(id, style + '-hours_dash', hours, duration ? duration : 1000);
		$this.dashTminusChangeTo(id, style + '-days_dash', days, duration ? duration : 1000);
		$this.dashTminusChangeTo(id, style + '-days_trip_dash', days, duration ? duration : 1000);
		$this.dashTminusChangeTo(id, style + '-weeks_dash', weeks, duration ? duration : 1000);
		$this.dashTminusChangeTo(id, style + '-weeks_trip_dash', weeks, duration ? duration : 1000);

		//why exactly are we doing this?
		$.data($this[0], 'diffSecs', diffSecs);

		//console.log('update diffSecs to: ', diffSecs);
		//events
		if( $.data($this[0], 'event_id') ){
			$this.checkEvent(id, diffSecs);
		}

		if (diffSecs > 0 || $.data($this[0], 'launchtarget') == 'countup'){
			if($.data($this[0], 'status') == 'play'){

				//recaculate diffSecs

				//this is the current browser time, not so good, as we also need an offset
				nowTime = new Date();
				timeOffset = $.data($this[0], 'timeOffset');
				nowTime.setSeconds(nowTime.getSeconds() + timeOffset);
				//console.log('hey dude: ', tminusTargetTime.valueOf(), $.data($this[0], 'tminusTargetTime').valueOf() );

				diffSecs = Math.floor(($.data($this[0], 'tminusTargetTime').valueOf()-nowTime.valueOf())/1000);

				t = setTimeout( function() {
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
