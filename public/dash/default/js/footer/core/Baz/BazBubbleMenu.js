/*
* @title: Baz Bubble Menu
* @description: Adds bubble menu to any page.
* @developer: guru@bazaari.com.au
*
* @Usage: 	<div id="Selector"></div>
* 			$('#Selector').bazBubbleMenu(options);
*
* @options:
		trigger		: has button style (size background-color)
						size options: tiny(30px), small(40px), medium(50px), large(60px), big(70px), huge(80px)
						background-color options: BB colors: bg-primary, bg-warning, bg-danger, etc.
			trigger: {
				type: 'click', // Either "click" or "menu" (Default : "menu")
				icon: "fa fa-caret-square-o-up",
				style: "small bg-info disabled",
				tooltip: {
					tipText: "Top",
					tipPosition: "right",
				},
			},
		position 	: top-left, top-right, bottom-left, bottom-right',
		direction 	: horizontal, vertical,
		backdrop 	: true, false - backdrop is called via <div class="bubble-backdrop"></div>
		onOpen 		: function() {} - execute function when menu is open,
		onClose 	: function() {} - execute function when menu is close,
		buttons 	: [{}] - list of buttons with options:
			buttons:[
				{
					button: {
						style: "small bg-warning", //default : large bg-default
						icon: "fa fa-bars" //default : fa fa-bars
					},
					tooltip: { // default: none
						tipText: "help",
						tipPosition: "left",
					},
					onClick: function(){
						console.log("fire");
					}
				}
			]
*/
(function ($){
	var DataKey = 'bb.bubblemenu';

	var Default = {
		trigger		: {},
		position 	: 'bottom-right',
		direction 	: 'vertical',
		backdrop 	: false,
		onOpen 		: function() {},
		onClose 	: function() {},
		onClick		: function() {},
		buttons 	: [{}],
		closeIcon	: 'fa fa-times'
	};

	var Event = {
		collapsed	: 'collapsed.bubblemenu',
		expanded	: 'expanded.bubblemenu',
		clicked		: 'clicked.bubblemenu'
	};

	// BazBubbleMenu Class Definition
	// =========================

	var BazBubbleMenu = function (element, options) {
		this.element = element;
		this.options = options;

		if (this.element === '') {
			throw new Error('Selector not defined. Please specify selector option');
		}

		this.init();
		this._setUpListeners();
	};

	BazBubbleMenu.prototype.init = function () {
		if (this.options.trigger.type === 'click') {
			$(this.element).addClass('bubble_menu ' + this.options.position + ' click');
		} else if (this.options.trigger.type === 'menu'){
			$(this.element).addClass('bubble_menu ' + this.options.position + ' ' + this.options.direction);
		}
		$(this.element).append(
			'<button class="bubble primary ' +
			(this.options.trigger.style ? this.options.trigger.style : 'large bg-primary') +
			'" ' +
			(this.options.trigger.tooltip ? 'data-toggle="tooltip" title="' + this.options.trigger.tooltip.tipText + '" data-placement="' +
				this.options.trigger.tooltip.tipPosition : '') +
			'"><i class="' +
			(this.options.trigger.icon ? this.options.trigger.icon : 'fa fa-bars') +
			'"></i></button><dl></dl>'
			);

		var element = this.element;

		if (this.options.buttons) {
			$(this.options.buttons).each(function(e, i) {
				if (i.button) {
					$(element).find('dl').append(
						'<dt><button ' +
						(i.tooltip ? 'data-toggle="tooltip" title="' + i.tooltip.tipText + '" data-placement="' + i.tooltip.tipPosition : '') +
						'" class="bubble ' +
						(i.button.style ? i.button.style : 'small bg-default') +
						' btn' + (e) +
						'"><i class="' +
						(i.button.icon ? i.button.icon : 'fa fa-bars') +
						'"></i></button></dt>'
						);
					$(element).find(' dl .btn' + (e)).click(function() {
						i.onClick();
					});
				}
			});
		}
	};

	BazBubbleMenu.prototype.click = function () {
		var clickedEvent = $.Event(Event.clicked);
		if (this.options.onClick) {
			this.options.onClick();
		}
		$(this.element).trigger(clickedEvent);
	};

	BazBubbleMenu.prototype.toggle = function () {
		var isOpen = $(this.element).find('dl').is('.visible');

		if (isOpen) {
			this.collapse();
		} else {
			this.expand();
		}
	};

	BazBubbleMenu.prototype.expand = function () {
		var expandedEvent = $.Event(Event.expanded);
		var triggerIcon = this.options.trigger.icon;
		var closeIcon = this.options.closeIcon;

		$(this.element).children('button').addClass('active');
		$(this.element).children('button').children('i').removeClass(triggerIcon).addClass(closeIcon);
		if (this.options.backdrop) {
			$('.bubble-backdrop').addClass('bubble-backdrop-visible');
		}
		$(this.element).find('dl').addClass('visible');
		$(this.element).find('dl dt button').each(function(e, i) {
			$(i).addClass('transform active');
		});
		if (this.options.onOpen) {
			this.options.onOpen();
		}
		$(this.element).trigger(expandedEvent);
	};

	BazBubbleMenu.prototype.collapse = function () {
		var collapsedEvent = $.Event(Event.collapsed);
		var triggerIcon = this.options.trigger.icon;
		var closeIcon = this.options.closeIcon;

		$(this.element).children('button').removeClass('active');
		$(this.element).children('button').children('i').removeClass(closeIcon).addClass(triggerIcon);
		$('.bubble-backdrop').removeClass('bubble-backdrop-visible');
		$(this.element).find('dl').removeClass('visible');
		$(this.element).find('dl dt button').each(function(e, i) {
			$(i).removeClass('transform active');
		});
		if (this.options.onClose) {
			this.options.onClose();
		}
		$(this.element).trigger(collapsedEvent);
	};

	BazBubbleMenu.prototype._setUpListeners = function () {
		var that = this;
		var triggerType = this.options.trigger.type;

		$(this.element).children('button').click(function(e) {
			if (triggerType !== 'undefined') {
				if (triggerType === 'menu' || triggerType === 'undefined') {
					if (e) {
						e.preventDefault();
					}
					that.toggle($(this));
					return false;
				} else if (triggerType === 'click') {
					if (e) {
						e.preventDefault();
					}
					that.click($(this));
					return false;
				}
			} else {
				// Default Toggle
				if (e) {
					e.preventDefault();
				}
				that.toggle($(this));
				return false;
			}
		});

		$(that.element).find('dl').click(function() {
			that.collapse($(this));
			return false;
		});
	};

	// Plugin Definition
	// =================
	function Plugin(option) {
		return this.each(function() {
			var $this = $(this);
			var data = $this.data(DataKey);

			if (!data) {
				var options = $.extend({}, Default, $this.data(), typeof option === 'object' && option);
				$this.data(DataKey, (data = new BazBubbleMenu($this, options)));
			}

			if (typeof option === 'string') {
				if (typeof data[option] === 'undefined') {
					throw new Error('No method named ' + option);
				}
				data[option]();
			}
		});
	}

	var old = $.fn.bazBubbleMenu;

	$.fn.bazBubbleMenu             = Plugin;
	$.fn.bazBubbleMenu.Constructor = BazBubbleMenu;

	// No Conflict Mode
	// ================
	$.fn.bazBubbleMenu.noConflict = function () {
		$.fn.bazBubbleMenu = old;
		return this;
	};
})(jQuery);