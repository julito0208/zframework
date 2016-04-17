(function(jQuery){
	

	jQuery.dateInput = function(dateInput, opts) {
		
		
		if(this == jQuery) return new jQuery.dateInput(dateInput, opts);
				
		/*------------------------------------------------------------------*/

		var opts = $.extend({}, jQuery.dateInput.defaultOptions, opts);
		var $this = this;
		
		var time = opts.time;
		var seconds = opts.seconds;
		
		if(!opts.format) opts.format = time ? (seconds ? jQuery.dateInput.defaultOptions.timeSecondsFormat : jQuery.dateInput.defaultOptions.timeFormat) : jQuery.dateInput.defaultOptions.simpleFormat;
		
		
		var dateInput = $(dateInput);
		
		if(!dateInput.data('____dateInput____')) {
			dateInput.data('____dateInput____', $this);
		} else {
			return dateInput.data('____dateInput____');
		}
		
		var dateSpan = $('<span />').insertAfter(dateInput).addClass('date-input');
		
		var inputSpan = $('<span />').addClass('input input-border').appendTo(dateSpan);
		
		
		var dayInput = $("<input type='text' />").attr({'maxlength': 2}).addClass('date-input-day').appendTo(inputSpan);
		
		inputSpan.append("<span class='separator'>/</span>");
		
		var monthInput = $("<input type='text' />").attr({'maxlength': 2}).addClass('date-input-month').appendTo(inputSpan);
		
		inputSpan.append("<span class='separator'>/</span>");
				
		var yearInput = $("<input type='text' />").attr({'maxlength': 4}).addClass('date-input-year').appendTo(inputSpan);
				
		var calendarButton = $('<a />').css({'text-decoration':'none'}).addClass('calendar-button htmlinputdatetimecontrol-button').html('&nbsp;').attr({'href': 'javascript: void(0)', 'title': 'Seleccionar'}).bind('click', function() {
			
			$.modalDialog.calendar(selectedDate, function(date) { setSelectedDate(date); });
			
		}).appendTo(dateSpan);
		
		
		dateInput.data('renderNode', dateSpan);
		
		
		inputSpan.find('input').bind('focusin', function() {
			
			inputSpan.addClass('input-border-focused');
			
		}).bind('focusout', function() {
			
			inputSpan.removeClass('input-border-focused');
			
		});
		
		
		
		dayInput.bind('change', function() {
			

			var day = Number.parseInt($(this).val());
			setSelectedDate(new Date(selectedDate.getFullYear(), selectedDate.getMonth(), day, selectedDate.getHours(), selectedDate.getMinutes(), selectedDate.getSeconds()));
			
		});
		
		
		
		monthInput.bind('change', function() {
			

			var month = Number.parseInt($(this).val())-1;
			setSelectedDate(new Date(selectedDate.getFullYear(), month, selectedDate.getDate(), selectedDate.getHours(), selectedDate.getMinutes(), selectedDate.getSeconds()));
			
		});
	
	
		
		yearInput.bind('change', function() {
			

			var year = Number.parseInt($(this).val());
			setSelectedDate(new Date(year > 0 ? (year < 50 ? year + 2000 : (year < 100 ? year + 1900 : year)): selectedDate.getFullYear(), selectedDate.getMonth(), selectedDate.getDate(), selectedDate.getHours(), selectedDate.getMinutes(), selectedDate.getSeconds()));
			
		});
		
		
		
		if(time) {
			
			var timeInputSpan = $('<span />').appendTo(dateSpan).addClass('input input-border');
			
				
			var hourInput = $("<input type='text' />").attr({'maxlength': 2}).addClass('date-input-hour').appendTo(timeInputSpan);
			
			timeInputSpan.append("<span class='separator'>:</span>");
			
			var minutesInput = $("<input type='text' />").attr({'maxlength': 2}).addClass('date-input-minutes').appendTo(timeInputSpan);
			
			
			if(seconds) {
			
				timeInputSpan.append("<span class='separator'>:</span>");
					
				var secondsInput = $("<input type='text' />").attr({'maxlength': 4}).addClass('date-input-seconds').appendTo(timeInputSpan);
				
			}
			
			
			timeInputSpan.find('input').bind('focusin', function() {
				
				timeInputSpan.addClass('input-border-focused');
				
			}).bind('focusout', function() {
				
				timeInputSpan.removeClass('input-border-focused');
				
			});

			
			hourInput.bind('change', function() {
			

				var hour = Number.parseInt($(this).val());
				setSelectedDate(new Date(selectedDate.getFullYear(), selectedDate.getMonth(), selectedDate.getDate(), hour, selectedDate.getMinutes(), selectedDate.getSeconds()));
				
			});
			
			
			
			minutesInput.bind('change', function() {
				
	
				var minutes = Number.parseInt($(this).val());
				setSelectedDate(new Date(selectedDate.getFullYear(), selectedDate.getMonth(), selectedDate.getDate(), selectedDate.getHours(), minutes, selectedDate.getSeconds()));
				
			});
		
		
			
			if(seconds) {
			
				secondsInput.bind('change', function() {
					
		
					var seconds = Number.parseInt($(this).val());
					setSelectedDate(new Date(selectedDate.getFullYear(), selectedDate.getMonth(), selectedDate.getDate(), selectedDate.getHours(), selectedDate.getMinutes(), seconds));
					
				});
				
			}
			

		}
		
		
		
		
		dateInput.bind('disabled', function() {
		
			dateSpan.find('input').attr('disabled', dateInput.attr('disabled'));	
			
		});
		
		
		
		dateSpan.find('input').bind('keydown', function(evt) {
			
			
			if(evt.which == $.KEY_UP) $(this).val(Number.parseInt($(this).val())-1).trigger('change');
				
			else if(evt.which == $.KEY_DOWN) $(this).val(Number.parseInt($(this).val())+1).trigger('change');
			
			
		});
		
		
		/*---------------------------------------------------------------------------------------------------------- */
		
		var selectedDate;
		
		
		var setSelectedDate = function(date) {
			
			var date = Date.parse(date, opts.format);
			selectedDate = date;
			
			dayInput.val(date.format('%d'));
			monthInput.val(date.format('%m'));
			yearInput.val(date.format('%Y'));
			
			if(time) {
				
				hourInput.val(date.format('%H'));
				minutesInput.val(date.format('%M'));
				
				if(seconds) {
					
					secondsInput.val(date.format('%S'));
					
				}
				
			}
						
			var formatted = date.format(opts.format);
			
			if(dateInput.val() != formatted) dateInput.val(formatted).trigger('change');
				
		};
		
		
		setSelectedDate(dateInput.val() ? dateInput.val() : (opts.val ? opts.val : new Date()));
		
		/*---------------------------------------------------------------------------------------------------------- */
		
		
		$this.disabled = function(disabled) {
						
			if(arguments.length > 0) {
				
				dateInput.disabled(disabled);
				
				if(disabled) calendarButton.css({'display': 'none'});
				else calendarButton.css({'display': ''});
				
				return $this;
				
			} else return dateInput.attr('disabled');
			
		};
		
		
		$this.getInput = function() {
			
			return dateInput;
		
		};
		
		
		
		
		$this.readOnly = function(readOnly) {
						
			if(arguments.length > 0) {
				
				dateInput.attr('readonly', readOnly);
				dateSpan.find('input').attr('readonly', readOnly);	
				
				if(readOnly) calendarButton.css({'visibility': 'hidden'});
				else calendarButton.css({'visibility': 'visible'});
				
				return $this;
				
			} else return dateInput.attr('readonly');
			
		};
		
		
		$this.val = function(value) {
			
			
			if(arguments.length > 0) {
				
				setSelectedDate(value);
				return $this;
				
			} else return dateInput.val();
			
		};
		
		
		$this.selectedDate = function(value) {
			
			
			if(arguments.length > 0) {
				
				setSelectedDate(value);
				return $this;
				
			} else return selectedDate;
			
		};
		
		
		$this.date = $this.selectedDate;
		
		
		/*---------------------------------------------------------------------------------------------------------- */
		
		
		if(dateInput.attr('readonly')) {
			$this.readOnly(true);
		}
		
		if(dateInput.attr('disabled')) {
			$this.disabled(true);
		}
		
		return $this;
	};
	
	
	
	jQuery.dateInput.defaultOptions = {
		
		simpleFormat: '%Y-%m-%d',
		timeFormat: '%Y-%m-%d %H:%M',
		timeSecondsFormat: '%Y-%m-%d %H:%M:%S',
		time: false,
		seconds: false
		
	};
	
	
	jQuery.fn.dateInput = function(opts){
		return jQuery.dateInput(this, opts);		
	};
	
	
	
	
	
	jQuery.dateTimeInput = function(dateInput, opts) {
		
		
		if(this == jQuery) return new jQuery.dateTimeInput(dateInput, opts);
				
		return new jQuery.dateInput(dateInput, $.extend({}, opts, {time: true}));
		
	};
		

	
	jQuery.fn.dateTimeInput = function(opts){
		return jQuery.dateTimeInput(this, opts);		
	};
	
	
	
	jQuery.timeInput = function(timeInput, opts) {
		
		
		if(this == jQuery) return new jQuery.timeInput(timeInput, opts);
				
		/*------------------------------------------------------------------*/

		var opts = $.extend({}, jQuery.timeInput.defaultOptions, opts);
		var $this = this;
		
		if(!opts.format) opts.format = opts.seconds ? jQuery.timeInput.defaultOptions.secondsFormat : jQuery.timeInput.defaultOptions.simpleFormat;
		
		var seconds = opts.seconds;
		
		var timeInput = $(timeInput);
		
		var timeSpan = $('<span />').insertAfter(timeInput).addClass('date-input');
		
		var inputSpan = $('<span />').addClass('input input-border').appendTo(timeSpan);
		
		
		var hourInput = $("<input type='text' />").attr({'maxlength': 2}).addClass('date-input-hour').appendTo(inputSpan);
		
		inputSpan.append("<span class='separator'>:</span>");
		
		var minutesInput = $("<input type='text' />").attr({'maxlength': 2}).addClass('date-input-minutes').appendTo(inputSpan);
		
		
		if(seconds) {
		
			inputSpan.append("<span class='separator'>:</span>");
				
			var secondsInput = $("<input type='text' />").attr({'maxlength': 4}).addClass('date-input-seconds').appendTo(inputSpan);
			
		}
				
		
		inputSpan.find('input').bind('focusin', function() {
			
			inputSpan.addClass('input-border-focused');
			
		}).bind('focusout', function() {
			
			inputSpan.removeClass('input-border-focused');
			
		});
		
		
		
		hourInput.bind('change', function() {
			

			var hour = Number.parseInt($(this).val());
			setSelectedDate(new Date(selectedDate.getFullYear(), selectedDate.getMonth(), selectedDate.getDate(), hour, selectedDate.getMinutes(), selectedDate.getSeconds()));
			
		});
		
		
		
		minutesInput.bind('change', function() {
			

			var minutes = Number.parseInt($(this).val());
			setSelectedDate(new Date(selectedDate.getFullYear(), selectedDate.getMonth(), selectedDate.getDate(), selectedDate.getHours(), minutes, selectedDate.getSeconds()));
			
		});
	
	
		
		if(seconds) {
		
			secondsInput.bind('change', function() {
				
	
				var seconds = Number.parseInt($(this).val());
				setSelectedDate(new Date(selectedDate.getFullYear(), selectedDate.getMonth(), selectedDate.getDate(), selectedDate.getHours(), selectedDate.getMinutes(), seconds));
				
			});
			
		}
		
		
		
		timeInput.bind('disabled', function() {
		
			timeSpan.find('input').attr('disabled',timeInput.attr('disabled'));	
			
		});
		
		
		/*---------------------------------------------------------------------------------------------------------- */
		
		var selectedDate;
		
		
		var setSelectedDate = function(date) {
			
			var date = Date.parse(date, opts.format);
			selectedDate = date;
			
			hourInput.val(date.format('%H'));
			minutesInput.val(date.format('%M'));
			
			if(seconds) secondsInput.val(date.format('%S'));
						
			var formatted = date.format(opts.format);
			
			if(timeInput.val() != formatted) timeInput.val(formatted).trigger('change');
				
		};
		
		
		setSelectedDate(timeInput.val() ? timeInput.val() : (opts.val ? opts.val : new Date()));
		
		/*---------------------------------------------------------------------------------------------------------- */
		
		
		$this.disabled = function(disabled) {
						
			if(arguments.length > 0) {
				
				timeInput.disabled(disabled);
				
				return $this;
				
			} else return timeInput.attr('disabled');
			
		};
		
		
		$this.getInput = function() {
			
			return timeInput;
		
		};
		
		
		
		
		$this.readOnly = function(readOnly) {
						
			if(arguments.length > 0) {
				
				timeInput.attr('readonly', readOnly);
				timeSpan.find('input').attr('readonly', readOnly);	
				
				return $this;
				
			} else return timeInput.attr('readonly');
			
		};
		
		
		$this.val = function(value) {
						
			if(arguments.length > 0) {
				
				setSelectedDate(value);
				return $this;
				
			} else return timeInput.val();
			
		};
		
		
		/*---------------------------------------------------------------------------------------------------------- */
		
		return $this;
	};
	
	
	
	jQuery.timeInput.defaultOptions = {
		
		simpleFormat: '%H-%M',
		secondsFormat: '%H-%M-%S',
		seconds: false
		
	};
	
	
	jQuery.fn.timeInput = function(opts){
		return jQuery.timeInput(this, opts);		
	};


})(jQuery);


