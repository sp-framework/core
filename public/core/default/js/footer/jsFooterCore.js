/* global exports require define globalThis */
/*!
 * AdminLTE v3.1.0 (https://adminlte.io)
 * Copyright 2014-2021 Colorlib <https://colorlib.com>
 * Licensed under MIT (https://github.com/ColorlibHQ/AdminLTE/blob/master/LICENSE)
 */
(function (global, factory) {
  typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports, require('jquery')) :
  typeof define === 'function' && define.amd ? define(['exports', 'jquery'], factory) :
  (global = typeof globalThis !== 'undefined' ? globalThis : global || self, factory(global.adminlte = {}, global.jQuery));
}(this, (function (exports, $) {

  function _interopDefaultLegacy (e) { return e && typeof e === 'object' && 'default' in e ? e : { 'default': e }; }

  var $__default = /*#__PURE__*/_interopDefaultLegacy($);

  /**
   * --------------------------------------------
   * AdminLTE CardRefresh.js
   * License MIT
   * --------------------------------------------
   */
  /**
   * Constants
   * ====================================================
   */

  var NAME$e = 'CardRefresh';
  var DATA_KEY$e = 'lte.cardrefresh';
  var EVENT_KEY$7 = "." + DATA_KEY$e;
  var JQUERY_NO_CONFLICT$e = $__default['default'].fn[NAME$e];
  var EVENT_LOADED = "loaded" + EVENT_KEY$7;
  var EVENT_OVERLAY_ADDED = "overlay.added" + EVENT_KEY$7;
  var EVENT_OVERLAY_REMOVED = "overlay.removed" + EVENT_KEY$7;
  var CLASS_NAME_CARD$1 = 'card';
  var SELECTOR_CARD$1 = "." + CLASS_NAME_CARD$1;
  var SELECTOR_DATA_REFRESH = '[data-card-widget="card-refresh"]';
  var Default$c = {
    source: '',
    sourceSelector: '',
    params: {},
    trigger: SELECTOR_DATA_REFRESH,
    content: '.card-body',
    loadInContent: true,
    loadOnInit: true,
    responseType: '',
    overlayTemplate: '<div class="overlay"><i class="fas fa-2x fa-sync-alt fa-spin"></i></div>',
    onLoadStart: function onLoadStart() {},
    onLoadDone: function onLoadDone(response) {
      return response;
    }
  };

  var CardRefresh = /*#__PURE__*/function () {
    function CardRefresh(element, settings) {
      this._element = element;
      this._parent = element.parents(SELECTOR_CARD$1).first();
      this._settings = $__default['default'].extend({}, Default$c, settings);
      this._overlay = $__default['default'](this._settings.overlayTemplate);

      if (element.hasClass(CLASS_NAME_CARD$1)) {
        this._parent = element;
      }

      if (this._settings.source === '') {
        throw new Error('Source url was not defined. Please specify a url in your CardRefresh source option.');
      }
    }

    var _proto = CardRefresh.prototype;

    _proto.load = function load() {
      var _this = this;

      this._addOverlay();

      this._settings.onLoadStart.call($__default['default'](this));

      $__default['default'].get(this._settings.source, this._settings.params, function (response) {
        if (_this._settings.loadInContent) {
          if (_this._settings.sourceSelector !== '') {
            response = $__default['default'](response).find(_this._settings.sourceSelector).html();
          }

          _this._parent.find(_this._settings.content).html(response);
        }

        _this._settings.onLoadDone.call($__default['default'](_this), response);

        _this._removeOverlay();
      }, this._settings.responseType !== '' && this._settings.responseType);
      $__default['default'](this._element).trigger($__default['default'].Event(EVENT_LOADED));
    };

    _proto._addOverlay = function _addOverlay() {
      this._parent.append(this._overlay);

      $__default['default'](this._element).trigger($__default['default'].Event(EVENT_OVERLAY_ADDED));
    };

    _proto._removeOverlay = function _removeOverlay() {
      this._parent.find(this._overlay).remove();

      $__default['default'](this._element).trigger($__default['default'].Event(EVENT_OVERLAY_REMOVED));
    } // Private
    ;

    _proto._init = function _init() {
      var _this2 = this;

      $__default['default'](this).find(this._settings.trigger).on('click', function () {
        _this2.load();
      });

      if (this._settings.loadOnInit) {
        this.load();
      }
    } // Static
    ;

    CardRefresh._jQueryInterface = function _jQueryInterface(config) {
      var data = $__default['default'](this).data(DATA_KEY$e);

      var _options = $__default['default'].extend({}, Default$c, $__default['default'](this).data());

      if (!data) {
        data = new CardRefresh($__default['default'](this), _options);
        $__default['default'](this).data(DATA_KEY$e, typeof config === 'string' ? data : config);
      }

      if (typeof config === 'string' && /load/.test(config)) {
        data[config]();
      } else {
        data._init($__default['default'](this));
      }
    };

    return CardRefresh;
  }();
  /**
   * Data API
   * ====================================================
   */


  $__default['default'](document).on('click', SELECTOR_DATA_REFRESH, function (event) {
    if (event) {
      event.preventDefault();
    }

    CardRefresh._jQueryInterface.call($__default['default'](this), 'load');
  });
  $__default['default'](function () {
    $__default['default'](SELECTOR_DATA_REFRESH).each(function () {
      CardRefresh._jQueryInterface.call($__default['default'](this));
    });
  });
  /**
   * jQuery API
   * ====================================================
   */

  $__default['default'].fn[NAME$e] = CardRefresh._jQueryInterface;
  $__default['default'].fn[NAME$e].Constructor = CardRefresh;

  $__default['default'].fn[NAME$e].noConflict = function () {
    $__default['default'].fn[NAME$e] = JQUERY_NO_CONFLICT$e;
    return CardRefresh._jQueryInterface;
  };

  /**
   * --------------------------------------------
   * AdminLTE CardWidget.js
   * License MIT
   * --------------------------------------------
   */
  /**
   * Constants
   * ====================================================
   */

  var NAME$d = 'CardWidget';
  var DATA_KEY$d = 'lte.cardwidget';
  var EVENT_KEY$6 = "." + DATA_KEY$d;
  var JQUERY_NO_CONFLICT$d = $__default['default'].fn[NAME$d];
  var EVENT_EXPANDED$3 = "expanded" + EVENT_KEY$6;
  var EVENT_COLLAPSED$4 = "collapsed" + EVENT_KEY$6;
  var EVENT_MAXIMIZED = "maximized" + EVENT_KEY$6;
  var EVENT_MINIMIZED = "minimized" + EVENT_KEY$6;
  var EVENT_REMOVED$1 = "removed" + EVENT_KEY$6;
  var CLASS_NAME_CARD = 'card';
  var CLASS_NAME_COLLAPSED$1 = 'collapsed-card';
  var CLASS_NAME_COLLAPSING = 'collapsing-card';
  var CLASS_NAME_EXPANDING = 'expanding-card';
  var CLASS_NAME_WAS_COLLAPSED = 'was-collapsed';
  var CLASS_NAME_MAXIMIZED = 'maximized-card';
  var SELECTOR_DATA_REMOVE = '[data-card-widget="remove"]';
  var SELECTOR_DATA_COLLAPSE = '[data-card-widget="collapse"]';
  var SELECTOR_DATA_MAXIMIZE = '[data-card-widget="maximize"]';
  var SELECTOR_CARD = "." + CLASS_NAME_CARD;
  var SELECTOR_CARD_HEADER = '.card-header';
  var SELECTOR_CARD_BODY = '.card-body';
  var SELECTOR_CARD_FOOTER = '.card-footer';
  var Default$b = {
    animationspeed: 'normal',
    collapseTrigger: SELECTOR_DATA_COLLAPSE,
    removeTrigger: SELECTOR_DATA_REMOVE,
    maximizeTrigger: SELECTOR_DATA_MAXIMIZE,
    collapseIcon: 'fa-minus',
    expandIcon: 'fa-plus',
    maximizeIcon: 'fa-expand',
    minimizeIcon: 'fa-compress'
  };

  var CardWidget = /*#__PURE__*/function () {
    function CardWidget(element, settings) {
      this._element = element;
      this._parent = element.parents(SELECTOR_CARD).first();

      if (element.hasClass(CLASS_NAME_CARD)) {
        this._parent = element;
      }

      this._settings = $__default['default'].extend({}, Default$b, settings);
    }

    var _proto = CardWidget.prototype;

    _proto.collapse = function collapse() {
      var _this = this;

      this._parent.addClass(CLASS_NAME_COLLAPSING).children(SELECTOR_CARD_BODY + ", " + SELECTOR_CARD_FOOTER).slideUp(this._settings.animationspeed, function () {
        _this._parent.addClass(CLASS_NAME_COLLAPSED$1).removeClass(CLASS_NAME_COLLAPSING);
      });

      this._parent.find("> " + SELECTOR_CARD_HEADER + " " + this._settings.collapseTrigger + " ." + this._settings.collapseIcon).addClass(this._settings.expandIcon).removeClass(this._settings.collapseIcon);

      this._element.trigger($__default['default'].Event(EVENT_COLLAPSED$4), this._parent);
    };

    _proto.expand = function expand() {
      var _this2 = this;

      this._parent.addClass(CLASS_NAME_EXPANDING).children(SELECTOR_CARD_BODY + ", " + SELECTOR_CARD_FOOTER).slideDown(this._settings.animationspeed, function () {
        _this2._parent.removeClass(CLASS_NAME_COLLAPSED$1).removeClass(CLASS_NAME_EXPANDING);
      });

      this._parent.find("> " + SELECTOR_CARD_HEADER + " " + this._settings.collapseTrigger + " ." + this._settings.expandIcon).addClass(this._settings.collapseIcon).removeClass(this._settings.expandIcon);

      this._element.trigger($__default['default'].Event(EVENT_EXPANDED$3), this._parent);
    };

    _proto.remove = function remove() {
      this._parent.slideUp();

      this._element.trigger($__default['default'].Event(EVENT_REMOVED$1), this._parent);
    };

    _proto.toggle = function toggle() {
      if (this._parent.hasClass(CLASS_NAME_COLLAPSED$1)) {
        this.expand();
        return;
      }

      this.collapse();
    };

    _proto.maximize = function maximize() {
      this._parent.find(this._settings.maximizeTrigger + " ." + this._settings.maximizeIcon).addClass(this._settings.minimizeIcon).removeClass(this._settings.maximizeIcon);

      this._parent.css({
        height: this._parent.height(),
        width: this._parent.width(),
        transition: 'all .15s'
      }).delay(150).queue(function () {
        var $element = $__default['default'](this);
        $element.addClass(CLASS_NAME_MAXIMIZED);
        $__default['default']('html').addClass(CLASS_NAME_MAXIMIZED);

        if ($element.hasClass(CLASS_NAME_COLLAPSED$1)) {
          $element.addClass(CLASS_NAME_WAS_COLLAPSED);
        }

        $element.dequeue();
      });

      this._element.trigger($__default['default'].Event(EVENT_MAXIMIZED), this._parent);
    };

    _proto.minimize = function minimize() {
      this._parent.find(this._settings.maximizeTrigger + " ." + this._settings.minimizeIcon).addClass(this._settings.maximizeIcon).removeClass(this._settings.minimizeIcon);

      this._parent.css('cssText', "height: " + this._parent[0].style.height + " !important; width: " + this._parent[0].style.width + " !important; transition: all .15s;").delay(10).queue(function () {
        var $element = $__default['default'](this);
        $element.removeClass(CLASS_NAME_MAXIMIZED);
        $__default['default']('html').removeClass(CLASS_NAME_MAXIMIZED);
        $element.css({
          height: 'inherit',
          width: 'inherit'
        });

        if ($element.hasClass(CLASS_NAME_WAS_COLLAPSED)) {
          $element.removeClass(CLASS_NAME_WAS_COLLAPSED);
        }

        $element.dequeue();
      });

      this._element.trigger($__default['default'].Event(EVENT_MINIMIZED), this._parent);
    };

    _proto.toggleMaximize = function toggleMaximize() {
      if (this._parent.hasClass(CLASS_NAME_MAXIMIZED)) {
        this.minimize();
        return;
      }

      this.maximize();
    } // Private
    ;

    _proto._init = function _init(card) {
      var _this3 = this;

      this._parent = card;
      $__default['default'](this).find(this._settings.collapseTrigger).click(function () {
        _this3.toggle();
      });
      $__default['default'](this).find(this._settings.maximizeTrigger).click(function () {
        _this3.toggleMaximize();
      });
      $__default['default'](this).find(this._settings.removeTrigger).click(function () {
        _this3.remove();
      });
    } // Static
    ;

    CardWidget._jQueryInterface = function _jQueryInterface(config) {
      var data = $__default['default'](this).data(DATA_KEY$d);

      var _options = $__default['default'].extend({}, Default$b, $__default['default'](this).data());

      if (!data) {
        data = new CardWidget($__default['default'](this), _options);
        $__default['default'](this).data(DATA_KEY$d, typeof config === 'string' ? data : config);
      }

      if (typeof config === 'string' && /collapse|expand|remove|toggle|maximize|minimize|toggleMaximize/.test(config)) {
        data[config]();
      } else if (typeof config === 'object') {
        data._init($__default['default'](this));
      }
    };

    return CardWidget;
  }();
  /**
   * Data API
   * ====================================================
   */


  $__default['default'](document).on('click', SELECTOR_DATA_COLLAPSE, function (event) {
    if (event) {
      event.preventDefault();
    }

    CardWidget._jQueryInterface.call($__default['default'](this), 'toggle');
  });
  $__default['default'](document).on('click', SELECTOR_DATA_REMOVE, function (event) {
    if (event) {
      event.preventDefault();
    }

    CardWidget._jQueryInterface.call($__default['default'](this), 'remove');
  });
  $__default['default'](document).on('click', SELECTOR_DATA_MAXIMIZE, function (event) {
    if (event) {
      event.preventDefault();
    }

    CardWidget._jQueryInterface.call($__default['default'](this), 'toggleMaximize');
  });
  /**
   * jQuery API
   * ====================================================
   */

  $__default['default'].fn[NAME$d] = CardWidget._jQueryInterface;
  $__default['default'].fn[NAME$d].Constructor = CardWidget;

  $__default['default'].fn[NAME$d].noConflict = function () {
    $__default['default'].fn[NAME$d] = JQUERY_NO_CONFLICT$d;
    return CardWidget._jQueryInterface;
  };

  /**
   * --------------------------------------------
   * AdminLTE ControlSidebar.js
   * License MIT
   * --------------------------------------------
   */
  /**
   * Constants
   * ====================================================
   */

  var NAME$c = 'ControlSidebar';
  var DATA_KEY$c = 'lte.controlsidebar';
  var EVENT_KEY$5 = "." + DATA_KEY$c;
  var JQUERY_NO_CONFLICT$c = $__default['default'].fn[NAME$c];
  var EVENT_COLLAPSED$3 = "collapsed" + EVENT_KEY$5;
  var EVENT_EXPANDED$2 = "expanded" + EVENT_KEY$5;
  var SELECTOR_CONTROL_SIDEBAR = '.control-sidebar';
  var SELECTOR_CONTROL_SIDEBAR_CONTENT$1 = '.control-sidebar-content';
  var SELECTOR_DATA_TOGGLE$4 = '[data-widget="control-sidebar"]';
  var SELECTOR_HEADER$1 = '.main-header';
  var SELECTOR_FOOTER$1 = '.main-footer';
  var CLASS_NAME_CONTROL_SIDEBAR_ANIMATE = 'control-sidebar-animate';
  var CLASS_NAME_CONTROL_SIDEBAR_OPEN$1 = 'control-sidebar-open';
  var CLASS_NAME_CONTROL_SIDEBAR_SLIDE = 'control-sidebar-slide-open';
  var CLASS_NAME_LAYOUT_FIXED$1 = 'layout-fixed';
  var CLASS_NAME_NAVBAR_FIXED = 'layout-navbar-fixed';
  var CLASS_NAME_NAVBAR_SM_FIXED = 'layout-sm-navbar-fixed';
  var CLASS_NAME_NAVBAR_MD_FIXED = 'layout-md-navbar-fixed';
  var CLASS_NAME_NAVBAR_LG_FIXED = 'layout-lg-navbar-fixed';
  var CLASS_NAME_NAVBAR_XL_FIXED = 'layout-xl-navbar-fixed';
  var CLASS_NAME_FOOTER_FIXED = 'layout-footer-fixed';
  var CLASS_NAME_FOOTER_SM_FIXED = 'layout-sm-footer-fixed';
  var CLASS_NAME_FOOTER_MD_FIXED = 'layout-md-footer-fixed';
  var CLASS_NAME_FOOTER_LG_FIXED = 'layout-lg-footer-fixed';
  var CLASS_NAME_FOOTER_XL_FIXED = 'layout-xl-footer-fixed';
  var Default$a = {
    controlsidebarSlide: true,
    scrollbarTheme: 'os-theme-light',
    scrollbarAutoHide: 'l',
    target: SELECTOR_CONTROL_SIDEBAR
  };
  /**
   * Class Definition
   * ====================================================
   */

  var ControlSidebar = /*#__PURE__*/function () {
    function ControlSidebar(element, config) {
      this._element = element;
      this._config = config;
    } // Public


    var _proto = ControlSidebar.prototype;

    _proto.collapse = function collapse() {
      var $body = $__default['default']('body');
      var $html = $__default['default']('html');
      var target = this._config.target; // Show the control sidebar

      if (this._config.controlsidebarSlide) {
        $html.addClass(CLASS_NAME_CONTROL_SIDEBAR_ANIMATE);
        $body.removeClass(CLASS_NAME_CONTROL_SIDEBAR_SLIDE).delay(300).queue(function () {
          $__default['default'](target).hide();
          $html.removeClass(CLASS_NAME_CONTROL_SIDEBAR_ANIMATE);
          $__default['default'](this).dequeue();
        });
      } else {
        $body.removeClass(CLASS_NAME_CONTROL_SIDEBAR_OPEN$1);
      }

      $__default['default'](this._element).trigger($__default['default'].Event(EVENT_COLLAPSED$3));
    };

    _proto.show = function show() {
      var $body = $__default['default']('body');
      var $html = $__default['default']('html'); // Collapse the control sidebar

      if (this._config.controlsidebarSlide) {
        $html.addClass(CLASS_NAME_CONTROL_SIDEBAR_ANIMATE);
        $__default['default'](this._config.target).show().delay(10).queue(function () {
          $body.addClass(CLASS_NAME_CONTROL_SIDEBAR_SLIDE).delay(300).queue(function () {
            $html.removeClass(CLASS_NAME_CONTROL_SIDEBAR_ANIMATE);
            $__default['default'](this).dequeue();
          });
          $__default['default'](this).dequeue();
        });
      } else {
        $body.addClass(CLASS_NAME_CONTROL_SIDEBAR_OPEN$1);
      }

      this._fixHeight();

      this._fixScrollHeight();

      $__default['default'](this._element).trigger($__default['default'].Event(EVENT_EXPANDED$2));
    };

    _proto.toggle = function toggle() {
      var $body = $__default['default']('body');
      var shouldClose = $body.hasClass(CLASS_NAME_CONTROL_SIDEBAR_OPEN$1) || $body.hasClass(CLASS_NAME_CONTROL_SIDEBAR_SLIDE);

      if (shouldClose) {
        // Close the control sidebar
        this.collapse();
      } else {
        // Open the control sidebar
        this.show();
      }
    } // Private
    ;

    _proto._init = function _init() {
      var _this = this;

      var $body = $__default['default']('body');
      var shouldNotHideAll = $body.hasClass(CLASS_NAME_CONTROL_SIDEBAR_OPEN$1) || $body.hasClass(CLASS_NAME_CONTROL_SIDEBAR_SLIDE);

      if (shouldNotHideAll) {
        $__default['default'](SELECTOR_CONTROL_SIDEBAR).not(this._config.target).hide();
        $__default['default'](this._config.target).css('display', 'block');
      } else {
        $__default['default'](SELECTOR_CONTROL_SIDEBAR).hide();
      }

      this._fixHeight();

      this._fixScrollHeight();

      $__default['default'](window).resize(function () {
        _this._fixHeight();

        _this._fixScrollHeight();
      });
      $__default['default'](window).scroll(function () {
        var $body = $__default['default']('body');
        var shouldFixHeight = $body.hasClass(CLASS_NAME_CONTROL_SIDEBAR_OPEN$1) || $body.hasClass(CLASS_NAME_CONTROL_SIDEBAR_SLIDE);

        if (shouldFixHeight) {
          _this._fixScrollHeight();
        }
      });
    };

    _proto._isNavbarFixed = function _isNavbarFixed() {
      var $body = $__default['default']('body');
      return $body.hasClass(CLASS_NAME_NAVBAR_FIXED) || $body.hasClass(CLASS_NAME_NAVBAR_SM_FIXED) || $body.hasClass(CLASS_NAME_NAVBAR_MD_FIXED) || $body.hasClass(CLASS_NAME_NAVBAR_LG_FIXED) || $body.hasClass(CLASS_NAME_NAVBAR_XL_FIXED);
    };

    _proto._isFooterFixed = function _isFooterFixed() {
      var $body = $__default['default']('body');
      return $body.hasClass(CLASS_NAME_FOOTER_FIXED) || $body.hasClass(CLASS_NAME_FOOTER_SM_FIXED) || $body.hasClass(CLASS_NAME_FOOTER_MD_FIXED) || $body.hasClass(CLASS_NAME_FOOTER_LG_FIXED) || $body.hasClass(CLASS_NAME_FOOTER_XL_FIXED);
    };

    _proto._fixScrollHeight = function _fixScrollHeight() {
      var $body = $__default['default']('body');
      var $controlSidebar = $__default['default'](this._config.target);

      if (!$body.hasClass(CLASS_NAME_LAYOUT_FIXED$1)) {
        return;
      }

      var heights = {
        scroll: $__default['default'](document).height(),
        window: $__default['default'](window).height(),
        header: $__default['default'](SELECTOR_HEADER$1).outerHeight(),
        footer: $__default['default'](SELECTOR_FOOTER$1).outerHeight()
      };
      var positions = {
        bottom: Math.abs(heights.window + $__default['default'](window).scrollTop() - heights.scroll),
        top: $__default['default'](window).scrollTop()
      };
      var navbarFixed = this._isNavbarFixed() && $__default['default'](SELECTOR_HEADER$1).css('position') === 'fixed';
      var footerFixed = this._isFooterFixed() && $__default['default'](SELECTOR_FOOTER$1).css('position') === 'fixed';
      var $controlsidebarContent = $__default['default'](this._config.target + ", " + this._config.target + " " + SELECTOR_CONTROL_SIDEBAR_CONTENT$1);

      if (positions.top === 0 && positions.bottom === 0) {
        $controlSidebar.css({
          bottom: heights.footer,
          top: heights.header
        });
        $controlsidebarContent.css('height', heights.window - (heights.header + heights.footer));
      } else if (positions.bottom <= heights.footer) {
        if (footerFixed === false) {
          var top = heights.header - positions.top;
          $controlSidebar.css('bottom', heights.footer - positions.bottom).css('top', top >= 0 ? top : 0);
          $controlsidebarContent.css('height', heights.window - (heights.footer - positions.bottom));
        } else {
          $controlSidebar.css('bottom', heights.footer);
        }
      } else if (positions.top <= heights.header) {
        if (navbarFixed === false) {
          $controlSidebar.css('top', heights.header - positions.top);
          $controlsidebarContent.css('height', heights.window - (heights.header - positions.top));
        } else {
          $controlSidebar.css('top', heights.header);
        }
      } else if (navbarFixed === false) {
        $controlSidebar.css('top', 0);
        $controlsidebarContent.css('height', heights.window);
      } else {
        $controlSidebar.css('top', heights.header);
      }

      if (footerFixed && navbarFixed) {
        $controlsidebarContent.css('height', '100%');
        $controlSidebar.css('height', '');
      } else if (footerFixed || navbarFixed) {
        $controlsidebarContent.css('height', '100%');
        $controlsidebarContent.css('height', '');
      }
    };

    _proto._fixHeight = function _fixHeight() {
      var $body = $__default['default']('body');
      var $controlSidebar = $__default['default'](this._config.target + " " + SELECTOR_CONTROL_SIDEBAR_CONTENT$1);

      if (!$body.hasClass(CLASS_NAME_LAYOUT_FIXED$1)) {
        $controlSidebar.attr('style', '');
        return;
      }

      var heights = {
        window: $__default['default'](window).height(),
        header: $__default['default'](SELECTOR_HEADER$1).outerHeight(),
        footer: $__default['default'](SELECTOR_FOOTER$1).outerHeight()
      };
      var sidebarHeight = heights.window - heights.header;

      if (this._isFooterFixed() && $__default['default'](SELECTOR_FOOTER$1).css('position') === 'fixed') {
        sidebarHeight = heights.window - heights.header - heights.footer;
      }

      $controlSidebar.css('height', sidebarHeight);

      if (typeof $__default['default'].fn.overlayScrollbars !== 'undefined') {
        $controlSidebar.overlayScrollbars({
          className: this._config.scrollbarTheme,
          sizeAutoCapable: true,
          scrollbars: {
            autoHide: this._config.scrollbarAutoHide,
            clickScrolling: true
          }
        });
      }
    } // Static
    ;

    ControlSidebar._jQueryInterface = function _jQueryInterface(operation) {
      return this.each(function () {
        var data = $__default['default'](this).data(DATA_KEY$c);

        var _options = $__default['default'].extend({}, Default$a, $__default['default'](this).data());

        if (!data) {
          data = new ControlSidebar(this, _options);
          $__default['default'](this).data(DATA_KEY$c, data);
        }

        if (data[operation] === 'undefined') {
          throw new Error(operation + " is not a function");
        }

        data[operation]();
      });
    };

    return ControlSidebar;
  }();
  /**
   *
   * Data Api implementation
   * ====================================================
   */


  $__default['default'](document).on('click', SELECTOR_DATA_TOGGLE$4, function (event) {
    event.preventDefault();

    ControlSidebar._jQueryInterface.call($__default['default'](this), 'toggle');
  });
  $__default['default'](document).ready(function () {
    ControlSidebar._jQueryInterface.call($__default['default'](SELECTOR_DATA_TOGGLE$4), '_init');
  });
  /**
   * jQuery API
   * ====================================================
   */

  $__default['default'].fn[NAME$c] = ControlSidebar._jQueryInterface;
  $__default['default'].fn[NAME$c].Constructor = ControlSidebar;

  $__default['default'].fn[NAME$c].noConflict = function () {
    $__default['default'].fn[NAME$c] = JQUERY_NO_CONFLICT$c;
    return ControlSidebar._jQueryInterface;
  };

  /**
   * --------------------------------------------
   * AdminLTE DirectChat.js
   * License MIT
   * --------------------------------------------
   */
  /**
   * Constants
   * ====================================================
   */

  var NAME$b = 'DirectChat';
  var DATA_KEY$b = 'lte.directchat';
  var EVENT_KEY$4 = "." + DATA_KEY$b;
  var JQUERY_NO_CONFLICT$b = $__default['default'].fn[NAME$b];
  var EVENT_TOGGLED = "toggled" + EVENT_KEY$4;
  var SELECTOR_DATA_TOGGLE$3 = '[data-widget="chat-pane-toggle"]';
  var SELECTOR_DIRECT_CHAT = '.direct-chat';
  var CLASS_NAME_DIRECT_CHAT_OPEN = 'direct-chat-contacts-open';
  /**
   * Class Definition
   * ====================================================
   */

  var DirectChat = /*#__PURE__*/function () {
    function DirectChat(element) {
      this._element = element;
    }

    var _proto = DirectChat.prototype;

    _proto.toggle = function toggle() {
      $__default['default'](this._element).parents(SELECTOR_DIRECT_CHAT).first().toggleClass(CLASS_NAME_DIRECT_CHAT_OPEN);
      $__default['default'](this._element).trigger($__default['default'].Event(EVENT_TOGGLED));
    } // Static
    ;

    DirectChat._jQueryInterface = function _jQueryInterface(config) {
      return this.each(function () {
        var data = $__default['default'](this).data(DATA_KEY$b);

        if (!data) {
          data = new DirectChat($__default['default'](this));
          $__default['default'](this).data(DATA_KEY$b, data);
        }

        data[config]();
      });
    };

    return DirectChat;
  }();
  /**
   *
   * Data Api implementation
   * ====================================================
   */


  $__default['default'](document).on('click', SELECTOR_DATA_TOGGLE$3, function (event) {
    if (event) {
      event.preventDefault();
    }

    DirectChat._jQueryInterface.call($__default['default'](this), 'toggle');
  });
  /**
   * jQuery API
   * ====================================================
   */

  $__default['default'].fn[NAME$b] = DirectChat._jQueryInterface;
  $__default['default'].fn[NAME$b].Constructor = DirectChat;

  $__default['default'].fn[NAME$b].noConflict = function () {
    $__default['default'].fn[NAME$b] = JQUERY_NO_CONFLICT$b;
    return DirectChat._jQueryInterface;
  };

  /**
   * --------------------------------------------
   * AdminLTE Dropdown.js
   * License MIT
   * --------------------------------------------
   */
  /**
   * Constants
   * ====================================================
   */

  var NAME$a = 'Dropdown';
  var DATA_KEY$a = 'lte.dropdown';
  var JQUERY_NO_CONFLICT$a = $__default['default'].fn[NAME$a];
  var SELECTOR_NAVBAR = '.navbar';
  var SELECTOR_DROPDOWN_MENU = '.dropdown-menu';
  var SELECTOR_DROPDOWN_MENU_ACTIVE = '.dropdown-menu.show';
  var SELECTOR_DROPDOWN_TOGGLE = '[data-toggle="dropdown"]';
  var CLASS_NAME_DROPDOWN_RIGHT = 'dropdown-menu-right';
  var CLASS_NAME_DROPDOWN_SUBMENU = 'dropdown-submenu'; // TODO: this is unused; should be removed along with the extend?

  var Default$9 = {};
  /**
   * Class Definition
   * ====================================================
   */

  var Dropdown = /*#__PURE__*/function () {
    function Dropdown(element, config) {
      this._config = config;
      this._element = element;
    } // Public


    var _proto = Dropdown.prototype;

    _proto.toggleSubmenu = function toggleSubmenu() {
      this._element.siblings().show().toggleClass('show');

      if (!this._element.next().hasClass('show')) {
        this._element.parents(SELECTOR_DROPDOWN_MENU).first().find('.show').removeClass('show').hide();
      }

      this._element.parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function () {
        $__default['default']('.dropdown-submenu .show').removeClass('show').hide();
      });
    };

    _proto.fixPosition = function fixPosition() {
      var $element = $__default['default'](SELECTOR_DROPDOWN_MENU_ACTIVE);

      if ($element.length === 0) {
        return;
      }

      if ($element.hasClass(CLASS_NAME_DROPDOWN_RIGHT)) {
        $element.css({
          left: 'inherit',
          right: 0
        });
      } else {
        $element.css({
          left: 0,
          right: 'inherit'
        });
      }

      var offset = $element.offset();
      var width = $element.width();
      var visiblePart = $__default['default'](window).width() - offset.left;

      if (offset.left < 0) {
        $element.css({
          left: 'inherit',
          right: offset.left - 5
        });
      } else if (visiblePart < width) {
        $element.css({
          left: 'inherit',
          right: 0
        });
      }
    } // Static
    ;

    Dropdown._jQueryInterface = function _jQueryInterface(config) {
      return this.each(function () {
        var data = $__default['default'](this).data(DATA_KEY$a);

        var _config = $__default['default'].extend({}, Default$9, $__default['default'](this).data());

        if (!data) {
          data = new Dropdown($__default['default'](this), _config);
          $__default['default'](this).data(DATA_KEY$a, data);
        }

        if (config === 'toggleSubmenu' || config === 'fixPosition') {
          data[config]();
        }
      });
    };

    return Dropdown;
  }();
  /**
   * Data API
   * ====================================================
   */


  $__default['default'](SELECTOR_DROPDOWN_MENU + " " + SELECTOR_DROPDOWN_TOGGLE).on('click', function (event) {
    event.preventDefault();
    event.stopPropagation();

    Dropdown._jQueryInterface.call($__default['default'](this), 'toggleSubmenu');
  });
  $__default['default'](SELECTOR_NAVBAR + " " + SELECTOR_DROPDOWN_TOGGLE).on('click', function (event) {
    event.preventDefault();

    if ($__default['default'](event.target).parent().hasClass(CLASS_NAME_DROPDOWN_SUBMENU)) {
      return;
    }

    setTimeout(function () {
      Dropdown._jQueryInterface.call($__default['default'](this), 'fixPosition');
    }, 1);
  });
  /**
   * jQuery API
   * ====================================================
   */

  $__default['default'].fn[NAME$a] = Dropdown._jQueryInterface;
  $__default['default'].fn[NAME$a].Constructor = Dropdown;

  $__default['default'].fn[NAME$a].noConflict = function () {
    $__default['default'].fn[NAME$a] = JQUERY_NO_CONFLICT$a;
    return Dropdown._jQueryInterface;
  };

  /**
   * --------------------------------------------
   * AdminLTE ExpandableTable.js
   * License MIT
   * --------------------------------------------
   */
  /**
    * Constants
    * ====================================================
    */

  var NAME$9 = 'ExpandableTable';
  var DATA_KEY$9 = 'lte.expandableTable';
  var EVENT_KEY$3 = "." + DATA_KEY$9;
  var JQUERY_NO_CONFLICT$9 = $__default['default'].fn[NAME$9];
  var EVENT_EXPANDED$1 = "expanded" + EVENT_KEY$3;
  var EVENT_COLLAPSED$2 = "collapsed" + EVENT_KEY$3;
  var SELECTOR_TABLE = '.expandable-table';
  var SELECTOR_EXPANDABLE_BODY = '.expandable-body';
  var SELECTOR_DATA_TOGGLE$2 = '[data-widget="expandable-table"]';
  var SELECTOR_ARIA_ATTR = 'aria-expanded';
  /**
    * Class Definition
    * ====================================================
    */

  var ExpandableTable = /*#__PURE__*/function () {
    function ExpandableTable(element, options) {
      this._options = options;
      this._element = element;
    } // Public


    var _proto = ExpandableTable.prototype;

    _proto.init = function init() {
      $__default['default'](SELECTOR_DATA_TOGGLE$2).each(function (_, $header) {
        var $type = $__default['default']($header).attr(SELECTOR_ARIA_ATTR);
        var $body = $__default['default']($header).next(SELECTOR_EXPANDABLE_BODY).children().first().children();

        if ($type === 'true') {
          $body.show();
        } else if ($type === 'false') {
          $body.hide();
          $body.parent().parent().addClass('d-none');
        }
      });
    };

    _proto.toggleRow = function toggleRow() {
      var $element = this._element;
      var time = 500;
      var $type = $element.attr(SELECTOR_ARIA_ATTR);
      var $body = $element.next(SELECTOR_EXPANDABLE_BODY).children().first().children();
      $body.stop();

      if ($type === 'true') {
        $body.slideUp(time, function () {
          $element.next(SELECTOR_EXPANDABLE_BODY).addClass('d-none');
        });
        $element.attr(SELECTOR_ARIA_ATTR, 'false');
        $element.trigger($__default['default'].Event(EVENT_COLLAPSED$2));
      } else if ($type === 'false') {
        $element.next(SELECTOR_EXPANDABLE_BODY).removeClass('d-none');
        $body.slideDown(time);
        $element.attr(SELECTOR_ARIA_ATTR, 'true');
        $element.trigger($__default['default'].Event(EVENT_EXPANDED$1));
      }
    } // Static
    ;

    ExpandableTable._jQueryInterface = function _jQueryInterface(operation) {
      return this.each(function () {
        var data = $__default['default'](this).data(DATA_KEY$9);

        if (!data) {
          data = new ExpandableTable($__default['default'](this));
          $__default['default'](this).data(DATA_KEY$9, data);
        }

        if (typeof operation === 'string' && /init|toggleRow/.test(operation)) {
          data[operation]();
        }
      });
    };

    return ExpandableTable;
  }();
  /**
    * Data API
    * ====================================================
    */


  $__default['default'](SELECTOR_TABLE).ready(function () {
    ExpandableTable._jQueryInterface.call($__default['default'](this), 'init');
  });
  $__default['default'](document).on('click', SELECTOR_DATA_TOGGLE$2, function () {
    ExpandableTable._jQueryInterface.call($__default['default'](this), 'toggleRow');
  });
  /**
    * jQuery API
    * ====================================================
    */

  $__default['default'].fn[NAME$9] = ExpandableTable._jQueryInterface;
  $__default['default'].fn[NAME$9].Constructor = ExpandableTable;

  $__default['default'].fn[NAME$9].noConflict = function () {
    $__default['default'].fn[NAME$9] = JQUERY_NO_CONFLICT$9;
    return ExpandableTable._jQueryInterface;
  };

  /**
   * --------------------------------------------
   * AdminLTE Fullscreen.js
   * License MIT
   * --------------------------------------------
   */
  /**
   * Constants
   * ====================================================
   */

  var NAME$8 = 'Fullscreen';
  var DATA_KEY$8 = 'lte.fullscreen';
  var JQUERY_NO_CONFLICT$8 = $__default['default'].fn[NAME$8];
  var SELECTOR_DATA_WIDGET$2 = '[data-widget="fullscreen"]';
  var SELECTOR_ICON = SELECTOR_DATA_WIDGET$2 + " i";
  var Default$8 = {
    minimizeIcon: 'fa-compress-arrows-alt',
    maximizeIcon: 'fa-expand-arrows-alt'
  };
  /**
   * Class Definition
   * ====================================================
   */

  var Fullscreen = /*#__PURE__*/function () {
    function Fullscreen(_element, _options) {
      this.element = _element;
      this.options = $__default['default'].extend({}, Default$8, _options);
    } // Public


    var _proto = Fullscreen.prototype;

    _proto.toggle = function toggle() {
      if (document.fullscreenElement || document.mozFullScreenElement || document.webkitFullscreenElement || document.msFullscreenElement) {
        this.windowed();
      } else {
        this.fullscreen();
      }
    };

    _proto.fullscreen = function fullscreen() {
      if (document.documentElement.requestFullscreen) {
        document.documentElement.requestFullscreen();
      } else if (document.documentElement.webkitRequestFullscreen) {
        document.documentElement.webkitRequestFullscreen();
      } else if (document.documentElement.msRequestFullscreen) {
        document.documentElement.msRequestFullscreen();
      }

      $__default['default'](SELECTOR_ICON).removeClass(this.options.maximizeIcon).addClass(this.options.minimizeIcon);
    };

    _proto.windowed = function windowed() {
      if (document.exitFullscreen) {
        document.exitFullscreen();
      } else if (document.webkitExitFullscreen) {
        document.webkitExitFullscreen();
      } else if (document.msExitFullscreen) {
        document.msExitFullscreen();
      }

      $__default['default'](SELECTOR_ICON).removeClass(this.options.minimizeIcon).addClass(this.options.maximizeIcon);
    } // Static
    ;

    Fullscreen._jQueryInterface = function _jQueryInterface(config) {
      var data = $__default['default'](this).data(DATA_KEY$8);

      if (!data) {
        data = $__default['default'](this).data();
      }

      var _options = $__default['default'].extend({}, Default$8, typeof config === 'object' ? config : data);

      var plugin = new Fullscreen($__default['default'](this), _options);
      $__default['default'](this).data(DATA_KEY$8, typeof config === 'object' ? config : data);

      if (typeof config === 'string' && /toggle|fullscreen|windowed/.test(config)) {
        plugin[config]();
      } else {
        plugin.init();
      }
    };

    return Fullscreen;
  }();
  /**
    * Data API
    * ====================================================
    */


  $__default['default'](document).on('click', SELECTOR_DATA_WIDGET$2, function () {
    Fullscreen._jQueryInterface.call($__default['default'](this), 'toggle');
  });
  /**
   * jQuery API
   * ====================================================
   */

  $__default['default'].fn[NAME$8] = Fullscreen._jQueryInterface;
  $__default['default'].fn[NAME$8].Constructor = Fullscreen;

  $__default['default'].fn[NAME$8].noConflict = function () {
    $__default['default'].fn[NAME$8] = JQUERY_NO_CONFLICT$8;
    return Fullscreen._jQueryInterface;
  };

  /**
   * --------------------------------------------
   * AdminLTE IFrame.js
   * License MIT
   * --------------------------------------------
   */
  /**
   * Constants
   * ====================================================
   */

  var NAME$7 = 'IFrame';
  var DATA_KEY$7 = 'lte.iframe';
  var JQUERY_NO_CONFLICT$7 = $__default['default'].fn[NAME$7];
  var SELECTOR_DATA_TOGGLE$1 = '[data-widget="iframe"]';
  var SELECTOR_DATA_TOGGLE_CLOSE = '[data-widget="iframe-close"]';
  var SELECTOR_DATA_TOGGLE_SCROLL_LEFT = '[data-widget="iframe-scrollleft"]';
  var SELECTOR_DATA_TOGGLE_SCROLL_RIGHT = '[data-widget="iframe-scrollright"]';
  var SELECTOR_DATA_TOGGLE_FULLSCREEN = '[data-widget="iframe-fullscreen"]';
  var SELECTOR_CONTENT_WRAPPER = '.content-wrapper';
  var SELECTOR_CONTENT_IFRAME = SELECTOR_CONTENT_WRAPPER + " iframe";
  var SELECTOR_TAB_NAV = SELECTOR_DATA_TOGGLE$1 + ".iframe-mode .nav";
  var SELECTOR_TAB_NAVBAR_NAV = SELECTOR_DATA_TOGGLE$1 + ".iframe-mode .navbar-nav";
  var SELECTOR_TAB_NAVBAR_NAV_ITEM = SELECTOR_TAB_NAVBAR_NAV + " .nav-item";
  var SELECTOR_TAB_NAVBAR_NAV_LINK = SELECTOR_TAB_NAVBAR_NAV + " .nav-link";
  var SELECTOR_TAB_CONTENT = SELECTOR_DATA_TOGGLE$1 + ".iframe-mode .tab-content";
  var SELECTOR_TAB_EMPTY = SELECTOR_TAB_CONTENT + " .tab-empty";
  var SELECTOR_TAB_LOADING = SELECTOR_TAB_CONTENT + " .tab-loading";
  var SELECTOR_TAB_PANE = SELECTOR_TAB_CONTENT + " .tab-pane";
  var SELECTOR_SIDEBAR_MENU_ITEM = '.main-sidebar .nav-item > a.nav-link';
  var SELECTOR_SIDEBAR_SEARCH_ITEM = '.sidebar-search-results .list-group-item';
  var SELECTOR_HEADER_MENU_ITEM = '.main-header .nav-item a.nav-link';
  var SELECTOR_HEADER_DROPDOWN_ITEM = '.main-header a.dropdown-item';
  var CLASS_NAME_IFRAME_MODE = 'iframe-mode';
  var CLASS_NAME_FULLSCREEN_MODE = 'iframe-mode-fullscreen';
  var Default$7 = {
    onTabClick: function onTabClick(item) {
      return item;
    },
    onTabChanged: function onTabChanged(item) {
      return item;
    },
    onTabCreated: function onTabCreated(item) {
      return item;
    },
    autoIframeMode: true,
    autoItemActive: true,
    autoShowNewTab: true,
    allowDuplicates: false,
    loadingScreen: true,
    useNavbarItems: true,
    scrollOffset: 40,
    scrollBehaviorSwap: false,
    iconMaximize: 'fa-expand',
    iconMinimize: 'fa-compress'
  };
  /**
   * Class Definition
   * ====================================================
   */

  var IFrame = /*#__PURE__*/function () {
    function IFrame(element, config) {
      this._config = config;
      this._element = element;

      this._init();
    } // Public


    var _proto = IFrame.prototype;

    _proto.onTabClick = function onTabClick(item) {
      this._config.onTabClick(item);
    };

    _proto.onTabChanged = function onTabChanged(item) {
      this._config.onTabChanged(item);
    };

    _proto.onTabCreated = function onTabCreated(item) {
      this._config.onTabCreated(item);
    };

    _proto.createTab = function createTab(title, link, uniqueName, autoOpen) {
      var _this = this;

      var tabId = "panel-" + uniqueName;
      var navId = "tab-" + uniqueName;

      if (this._config.allowDuplicates) {
        tabId += "-" + Math.floor(Math.random() * 1000);
        navId += "-" + Math.floor(Math.random() * 1000);
      }

      var newNavItem = "<li class=\"nav-item\" role=\"presentation\"><a href=\"#\" class=\"btn-iframe-close\" data-widget=\"iframe-close\" data-type=\"only-this\"><i class=\"fas fa-times\"></i></a><a class=\"nav-link\" data-toggle=\"row\" id=\"" + navId + "\" href=\"#" + tabId + "\" role=\"tab\" aria-controls=\"" + tabId + "\" aria-selected=\"false\">" + title + "</a></li>";
      $__default['default'](SELECTOR_TAB_NAVBAR_NAV).append(unescape(escape(newNavItem)));
      var newTabItem = "<div class=\"tab-pane fade\" id=\"" + tabId + "\" role=\"tabpanel\" aria-labelledby=\"" + navId + "\"><iframe src=\"" + link + "\"></iframe></div>";
      $__default['default'](SELECTOR_TAB_CONTENT).append(unescape(escape(newTabItem)));

      if (autoOpen) {
        if (this._config.loadingScreen) {
          var $loadingScreen = $__default['default'](SELECTOR_TAB_LOADING);
          $loadingScreen.fadeIn();
          $__default['default'](tabId + " iframe").ready(function () {
            if (typeof _this._config.loadingScreen === 'number') {
              _this.switchTab("#" + navId);

              setTimeout(function () {
                $loadingScreen.fadeOut();
              }, _this._config.loadingScreen);
            } else {
              _this.switchTab("#" + navId);

              $loadingScreen.fadeOut();
            }
          });
        } else {
          this.switchTab("#" + navId);
        }
      }

      this.onTabCreated($__default['default']("#" + navId));
    };

    _proto.openTabSidebar = function openTabSidebar(item, autoOpen) {
      if (autoOpen === void 0) {
        autoOpen = this._config.autoShowNewTab;
      }

      var $item = $__default['default'](item).clone();

      if ($item.attr('href') === undefined) {
        $item = $__default['default'](item).parent('a').clone();
      }

      $item.find('.right, .search-path').remove();
      var title = $item.find('p').text();

      if (title === '') {
        title = $item.text();
      }

      var link = $item.attr('href');

      if (link === '#' || link === '' || link === undefined) {
        return;
      }

      var uniqueName = link.replace('./', '').replace(/["&'./:=?[\]]/gi, '-').replace(/(--)/gi, '');
      var navId = "tab-" + uniqueName;

      if (!this._config.allowDuplicates && $__default['default']("#" + navId).length > 0) {
        return this.switchTab("#" + navId);
      }

      if (!this._config.allowDuplicates && $__default['default']("#" + navId).length === 0 || this._config.allowDuplicates) {
        this.createTab(title, link, uniqueName, autoOpen);
      }
    };

    _proto.switchTab = function switchTab(item) {
      var $item = $__default['default'](item);
      var tabId = $item.attr('href');
      $__default['default'](SELECTOR_TAB_EMPTY).hide();
      $__default['default'](SELECTOR_TAB_NAVBAR_NAV + " .active").tab('dispose').removeClass('active');

      this._fixHeight();

      $item.tab('show');
      $item.parents('li').addClass('active');
      this.onTabChanged($item);

      if (this._config.autoItemActive) {
        this._setItemActive($__default['default'](tabId + " iframe").attr('src'));
      }
    };

    _proto.removeActiveTab = function removeActiveTab(type, element) {
      if (type == 'all') {
        $__default['default'](SELECTOR_TAB_NAVBAR_NAV_ITEM).remove();
        $__default['default'](SELECTOR_TAB_PANE).remove();
        $__default['default'](SELECTOR_TAB_EMPTY).show();
      } else if (type == 'all-other') {
        $__default['default'](SELECTOR_TAB_NAVBAR_NAV_ITEM + ":not(.active)").remove();
        $__default['default'](SELECTOR_TAB_PANE + ":not(.active)").remove();
      } else if (type == 'only-this') {
        var $navClose = $__default['default'](element);
        var $navItem = $navClose.parent('.nav-item');
        var $navItemParent = $navItem.parent();
        var navItemIndex = $navItem.index();
        var tabId = $navClose.siblings('.nav-link').attr('aria-controls');
        $navItem.remove();
        $__default['default']("#" + tabId).remove();

        if ($__default['default'](SELECTOR_TAB_CONTENT).children().length == $__default['default'](SELECTOR_TAB_EMPTY + ", " + SELECTOR_TAB_LOADING).length) {
          $__default['default'](SELECTOR_TAB_EMPTY).show();
        } else {
          var prevNavItemIndex = navItemIndex - 1;
          this.switchTab($navItemParent.children().eq(prevNavItemIndex).find('a.nav-link'));
        }
      } else {
        var _$navItem = $__default['default'](SELECTOR_TAB_NAVBAR_NAV_ITEM + ".active");

        var _$navItemParent = _$navItem.parent();

        var _navItemIndex = _$navItem.index();

        _$navItem.remove();

        $__default['default'](SELECTOR_TAB_PANE + ".active").remove();

        if ($__default['default'](SELECTOR_TAB_CONTENT).children().length == $__default['default'](SELECTOR_TAB_EMPTY + ", " + SELECTOR_TAB_LOADING).length) {
          $__default['default'](SELECTOR_TAB_EMPTY).show();
        } else {
          var _prevNavItemIndex = _navItemIndex - 1;

          this.switchTab(_$navItemParent.children().eq(_prevNavItemIndex).find('a.nav-link'));
        }
      }
    };

    _proto.toggleFullscreen = function toggleFullscreen() {
      if ($__default['default']('body').hasClass(CLASS_NAME_FULLSCREEN_MODE)) {
        $__default['default'](SELECTOR_DATA_TOGGLE_FULLSCREEN + " i").removeClass(this._config.iconMinimize).addClass(this._config.iconMaximize);
        $__default['default']('body').removeClass(CLASS_NAME_FULLSCREEN_MODE);
        $__default['default'](SELECTOR_TAB_EMPTY + ", " + SELECTOR_TAB_LOADING).height('auto');
        $__default['default'](SELECTOR_CONTENT_WRAPPER).height('auto');
        $__default['default'](SELECTOR_CONTENT_IFRAME).height('auto');
      } else {
        $__default['default'](SELECTOR_DATA_TOGGLE_FULLSCREEN + " i").removeClass(this._config.iconMaximize).addClass(this._config.iconMinimize);
        $__default['default']('body').addClass(CLASS_NAME_FULLSCREEN_MODE);
      }

      $__default['default'](window).trigger('resize');

      this._fixHeight(true);
    } // Private
    ;

    _proto._init = function _init() {
      if (window.frameElement && this._config.autoIframeMode) {
        $__default['default']('body').addClass(CLASS_NAME_IFRAME_MODE);
      } else if ($__default['default'](SELECTOR_CONTENT_WRAPPER).hasClass(CLASS_NAME_IFRAME_MODE)) {
        if ($__default['default'](SELECTOR_TAB_CONTENT).children().length > 2) {
          var $el = $__default['default'](SELECTOR_TAB_PANE + ":first-child");
          $el.show();

          this._setItemActive($el.find('iframe').attr('src'));
        }

        this._setupListeners();

        this._fixHeight(true);
      }
    };

    _proto._navScroll = function _navScroll(offset) {
      var leftPos = $__default['default'](SELECTOR_TAB_NAVBAR_NAV).scrollLeft();
      $__default['default'](SELECTOR_TAB_NAVBAR_NAV).animate({
        scrollLeft: leftPos + offset
      }, 250, 'linear');
    };

    _proto._setupListeners = function _setupListeners() {
      var _this2 = this;

      $__default['default'](window).on('resize', function () {
        setTimeout(function () {
          _this2._fixHeight();
        }, 1);
      });
      $__default['default'](document).on('click', SELECTOR_SIDEBAR_MENU_ITEM + ", " + SELECTOR_SIDEBAR_SEARCH_ITEM, function (e) {
        e.preventDefault();

        _this2.openTabSidebar(e.target);
      });

      if (this._config.useNavbarItems) {
        $__default['default'](document).on('click', SELECTOR_HEADER_MENU_ITEM + ", " + SELECTOR_HEADER_DROPDOWN_ITEM, function (e) {
          e.preventDefault();

          _this2.openTabSidebar(e.target);
        });
      }

      $__default['default'](document).on('click', SELECTOR_TAB_NAVBAR_NAV_LINK, function (e) {
        e.preventDefault();

        _this2.onTabClick(e.target);

        _this2.switchTab(e.target);
      });
      $__default['default'](document).on('click', SELECTOR_TAB_NAVBAR_NAV_LINK, function (e) {
        e.preventDefault();

        _this2.onTabClick(e.target);

        _this2.switchTab(e.target);
      });
      $__default['default'](document).on('click', SELECTOR_DATA_TOGGLE_CLOSE, function (e) {
        e.preventDefault();
        var target = e.target;

        if (target.nodeName == 'I') {
          target = e.target.offsetParent;
        }

        _this2.removeActiveTab(target.attributes['data-type'] ? target.attributes['data-type'].nodeValue : null, target);
      });
      $__default['default'](document).on('click', SELECTOR_DATA_TOGGLE_FULLSCREEN, function (e) {
        e.preventDefault();

        _this2.toggleFullscreen();
      });
      var mousedown = false;
      var mousedownInterval = null;
      $__default['default'](document).on('mousedown', SELECTOR_DATA_TOGGLE_SCROLL_LEFT, function (e) {
        e.preventDefault();
        clearInterval(mousedownInterval);
        var scrollOffset = _this2._config.scrollOffset;

        if (!_this2._config.scrollBehaviorSwap) {
          scrollOffset = -scrollOffset;
        }

        mousedown = true;

        _this2._navScroll(scrollOffset);

        mousedownInterval = setInterval(function () {
          _this2._navScroll(scrollOffset);
        }, 250);
      });
      $__default['default'](document).on('mousedown', SELECTOR_DATA_TOGGLE_SCROLL_RIGHT, function (e) {
        e.preventDefault();
        clearInterval(mousedownInterval);
        var scrollOffset = _this2._config.scrollOffset;

        if (_this2._config.scrollBehaviorSwap) {
          scrollOffset = -scrollOffset;
        }

        mousedown = true;

        _this2._navScroll(scrollOffset);

        mousedownInterval = setInterval(function () {
          _this2._navScroll(scrollOffset);
        }, 250);
      });
      $__default['default'](document).on('mouseup', function () {
        if (mousedown) {
          mousedown = false;
          clearInterval(mousedownInterval);
          mousedownInterval = null;
        }
      });
    };

    _proto._setItemActive = function _setItemActive(href) {
      $__default['default'](SELECTOR_SIDEBAR_MENU_ITEM + ", " + SELECTOR_HEADER_DROPDOWN_ITEM).removeClass('active');
      $__default['default'](SELECTOR_HEADER_MENU_ITEM).parent().removeClass('active');
      var $headerMenuItem = $__default['default'](SELECTOR_HEADER_MENU_ITEM + "[href$=\"" + href + "\"]");
      var $headerDropdownItem = $__default['default'](SELECTOR_HEADER_DROPDOWN_ITEM + "[href$=\"" + href + "\"]");
      var $sidebarMenuItem = $__default['default'](SELECTOR_SIDEBAR_MENU_ITEM + "[href$=\"" + href + "\"]");
      $headerMenuItem.each(function (i, e) {
        $__default['default'](e).parent().addClass('active');
      });
      $headerDropdownItem.each(function (i, e) {
        $__default['default'](e).addClass('active');
      });
      $sidebarMenuItem.each(function (i, e) {
        $__default['default'](e).addClass('active');
        $__default['default'](e).parents('.nav-treeview').prevAll('.nav-link').addClass('active');
      });
    };

    _proto._fixHeight = function _fixHeight(tabEmpty) {
      if (tabEmpty === void 0) {
        tabEmpty = false;
      }

      if ($__default['default']('body').hasClass(CLASS_NAME_FULLSCREEN_MODE)) {
        var windowHeight = $__default['default'](window).height();
        var navbarHeight = $__default['default'](SELECTOR_TAB_NAV).outerHeight();
        $__default['default'](SELECTOR_TAB_EMPTY + ", " + SELECTOR_TAB_LOADING + ", " + SELECTOR_CONTENT_IFRAME).height(windowHeight - navbarHeight);
        $__default['default'](SELECTOR_CONTENT_WRAPPER).height(windowHeight);
      } else {
        var contentWrapperHeight = parseFloat($__default['default'](SELECTOR_CONTENT_WRAPPER).css('height'));

        var _navbarHeight = $__default['default'](SELECTOR_TAB_NAV).outerHeight();

        if (tabEmpty == true) {
          setTimeout(function () {
            $__default['default'](SELECTOR_TAB_EMPTY + ", " + SELECTOR_TAB_LOADING).height(contentWrapperHeight - _navbarHeight);
          }, 50);
        } else {
          $__default['default'](SELECTOR_CONTENT_IFRAME).height(contentWrapperHeight - _navbarHeight);
        }
      }
    } // Static
    ;

    IFrame._jQueryInterface = function _jQueryInterface(operation) {
      var data = $__default['default'](this).data(DATA_KEY$7);

      var _options = $__default['default'].extend({}, Default$7, $__default['default'](this).data());

      if (!data) {
        data = new IFrame(this, _options);
        $__default['default'](this).data(DATA_KEY$7, data);
      }

      if (typeof operation === 'string' && /createTab|openTabSidebar|switchTab|removeActiveTab/.test(operation)) {
        var _data;

        for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
          args[_key - 1] = arguments[_key];
        }

        (_data = data)[operation].apply(_data, args);
      }
    };

    return IFrame;
  }();
  /**
   * Data API
   * ====================================================
   */


  $__default['default'](window).on('load', function () {
    IFrame._jQueryInterface.call($__default['default'](SELECTOR_DATA_TOGGLE$1));
  });
  /**
   * jQuery API
   * ====================================================
   */

  $__default['default'].fn[NAME$7] = IFrame._jQueryInterface;
  $__default['default'].fn[NAME$7].Constructor = IFrame;

  $__default['default'].fn[NAME$7].noConflict = function () {
    $__default['default'].fn[NAME$7] = JQUERY_NO_CONFLICT$7;
    return IFrame._jQueryInterface;
  };

  /**
   * --------------------------------------------
   * AdminLTE Layout.js
   * License MIT
   * --------------------------------------------
   */
  /**
   * Constants
   * ====================================================
   */

  var NAME$6 = 'Layout';
  var DATA_KEY$6 = 'lte.layout';
  var JQUERY_NO_CONFLICT$6 = $__default['default'].fn[NAME$6];
  var SELECTOR_HEADER = '.main-header';
  var SELECTOR_MAIN_SIDEBAR = '.main-sidebar';
  var SELECTOR_SIDEBAR$1 = '.main-sidebar .sidebar';
  var SELECTOR_CONTENT = '.content-wrapper';
  var SELECTOR_CONTROL_SIDEBAR_CONTENT = '.control-sidebar-content';
  var SELECTOR_CONTROL_SIDEBAR_BTN = '[data-widget="control-sidebar"]';
  var SELECTOR_FOOTER = '.main-footer';
  var SELECTOR_PUSHMENU_BTN = '[data-widget="pushmenu"]';
  var SELECTOR_LOGIN_BOX = '.login-box';
  var SELECTOR_REGISTER_BOX = '.register-box';
  var SELECTOR_PRELOADER = '.preloader';
  var CLASS_NAME_SIDEBAR_COLLAPSED$1 = 'sidebar-collapse';
  var CLASS_NAME_SIDEBAR_FOCUSED = 'sidebar-focused';
  var CLASS_NAME_LAYOUT_FIXED = 'layout-fixed';
  var CLASS_NAME_CONTROL_SIDEBAR_SLIDE_OPEN = 'control-sidebar-slide-open';
  var CLASS_NAME_CONTROL_SIDEBAR_OPEN = 'control-sidebar-open';
  var Default$6 = {
    scrollbarTheme: 'os-theme-light',
    scrollbarAutoHide: 'l',
    panelAutoHeight: true,
    panelAutoHeightMode: 'min-height',
    preloadDuration: 200,
    loginRegisterAutoHeight: true
  };
  /**
   * Class Definition
   * ====================================================
   */

  var Layout = /*#__PURE__*/function () {
    function Layout(element, config) {
      this._config = config;
      this._element = element;
    } // Public


    var _proto = Layout.prototype;

    _proto.fixLayoutHeight = function fixLayoutHeight(extra) {
      if (extra === void 0) {
        extra = null;
      }

      var $body = $__default['default']('body');
      var controlSidebar = 0;

      if ($body.hasClass(CLASS_NAME_CONTROL_SIDEBAR_SLIDE_OPEN) || $body.hasClass(CLASS_NAME_CONTROL_SIDEBAR_OPEN) || extra === 'control_sidebar') {
        controlSidebar = $__default['default'](SELECTOR_CONTROL_SIDEBAR_CONTENT).outerHeight();
      }

      var heights = {
        window: $__default['default'](window).height(),
        header: $__default['default'](SELECTOR_HEADER).length > 0 ? $__default['default'](SELECTOR_HEADER).outerHeight() : 0,
        footer: $__default['default'](SELECTOR_FOOTER).length > 0 ? $__default['default'](SELECTOR_FOOTER).outerHeight() : 0,
        sidebar: $__default['default'](SELECTOR_SIDEBAR$1).length > 0 ? $__default['default'](SELECTOR_SIDEBAR$1).height() : 0,
        controlSidebar: controlSidebar
      };

      var max = this._max(heights);

      var offset = this._config.panelAutoHeight;

      if (offset === true) {
        offset = 0;
      }

      var $contentSelector = $__default['default'](SELECTOR_CONTENT);

      if (offset !== false) {
        if (max === heights.controlSidebar) {
          $contentSelector.css(this._config.panelAutoHeightMode, max + offset);
        } else if (max === heights.window) {
          $contentSelector.css(this._config.panelAutoHeightMode, max + offset - heights.header - heights.footer);
        } else {
          $contentSelector.css(this._config.panelAutoHeightMode, max + offset - heights.header);
        }

        if (this._isFooterFixed()) {
          $contentSelector.css(this._config.panelAutoHeightMode, parseFloat($contentSelector.css(this._config.panelAutoHeightMode)) + heights.footer);
        }
      }

      if (!$body.hasClass(CLASS_NAME_LAYOUT_FIXED)) {
        return;
      }

      if (typeof $__default['default'].fn.overlayScrollbars !== 'undefined') {
        $__default['default'](SELECTOR_SIDEBAR$1).overlayScrollbars({
          className: this._config.scrollbarTheme,
          sizeAutoCapable: true,
          scrollbars: {
            autoHide: this._config.scrollbarAutoHide,
            clickScrolling: true
          }
        });
      } else {
        $__default['default'](SELECTOR_SIDEBAR$1).css('overflow-y', 'auto');
      }
    };

    _proto.fixLoginRegisterHeight = function fixLoginRegisterHeight() {
      var $body = $__default['default']('body');
      var $selector = $__default['default'](SELECTOR_LOGIN_BOX + ", " + SELECTOR_REGISTER_BOX);

      if ($selector.length === 0) {
        $body.css('height', 'auto');
        $__default['default']('html').css('height', 'auto');
      } else {
        var boxHeight = $selector.height();

        if ($body.css(this._config.panelAutoHeightMode) !== boxHeight) {
          $body.css(this._config.panelAutoHeightMode, boxHeight);
        }
      }
    } // Private
    ;

    _proto._init = function _init() {
      var _this = this;

      // Activate layout height watcher
      this.fixLayoutHeight();

      if (this._config.loginRegisterAutoHeight === true) {
        this.fixLoginRegisterHeight();
      } else if (this._config.loginRegisterAutoHeight === parseInt(this._config.loginRegisterAutoHeight, 10)) {
        setInterval(this.fixLoginRegisterHeight, this._config.loginRegisterAutoHeight);
      }

      $__default['default'](SELECTOR_SIDEBAR$1).on('collapsed.lte.treeview expanded.lte.treeview', function () {
        _this.fixLayoutHeight();
      });
      $__default['default'](SELECTOR_MAIN_SIDEBAR).on('mouseenter mouseleave', function () {
        if ($__default['default']('body').hasClass(CLASS_NAME_SIDEBAR_COLLAPSED$1)) {
          _this.fixLayoutHeight();
        }
      });
      $__default['default'](SELECTOR_PUSHMENU_BTN).on('collapsed.lte.pushmenu shown.lte.pushmenu', function () {
        setTimeout(function () {
          _this.fixLayoutHeight();
        }, 300);
      });
      $__default['default'](SELECTOR_CONTROL_SIDEBAR_BTN).on('collapsed.lte.controlsidebar', function () {
        _this.fixLayoutHeight();
      }).on('expanded.lte.controlsidebar', function () {
        _this.fixLayoutHeight('control_sidebar');
      });
      $__default['default'](window).resize(function () {
        _this.fixLayoutHeight();
      });
      setTimeout(function () {
        $__default['default']('body.hold-transition').removeClass('hold-transition');
      }, 50);
      setTimeout(function () {
        var $preloader = $__default['default'](SELECTOR_PRELOADER);

        if ($preloader) {
          $preloader.css('height', 0);
          setTimeout(function () {
            $preloader.children().hide();
          }, 200);
        }
      }, this._config.preloadDuration);
    };

    _proto._max = function _max(numbers) {
      // Calculate the maximum number in a list
      var max = 0;
      Object.keys(numbers).forEach(function (key) {
        if (numbers[key] > max) {
          max = numbers[key];
        }
      });
      return max;
    };

    _proto._isFooterFixed = function _isFooterFixed() {
      return $__default['default'](SELECTOR_FOOTER).css('position') === 'fixed';
    } // Static
    ;

    Layout._jQueryInterface = function _jQueryInterface(config) {
      if (config === void 0) {
        config = '';
      }

      return this.each(function () {
        var data = $__default['default'](this).data(DATA_KEY$6);

        var _options = $__default['default'].extend({}, Default$6, $__default['default'](this).data());

        if (!data) {
          data = new Layout($__default['default'](this), _options);
          $__default['default'](this).data(DATA_KEY$6, data);
        }

        if (config === 'init' || config === '') {
          data._init();
        } else if (config === 'fixLayoutHeight' || config === 'fixLoginRegisterHeight') {
          data[config]();
        }
      });
    };

    return Layout;
  }();
  /**
   * Data API
   * ====================================================
   */


  $__default['default'](window).on('load', function () {
    Layout._jQueryInterface.call($__default['default']('body'));
  });
  $__default['default'](SELECTOR_SIDEBAR$1 + " a").on('focusin', function () {
    $__default['default'](SELECTOR_MAIN_SIDEBAR).addClass(CLASS_NAME_SIDEBAR_FOCUSED);
  }).on('focusout', function () {
    $__default['default'](SELECTOR_MAIN_SIDEBAR).removeClass(CLASS_NAME_SIDEBAR_FOCUSED);
  });
  /**
   * jQuery API
   * ====================================================
   */

  $__default['default'].fn[NAME$6] = Layout._jQueryInterface;
  $__default['default'].fn[NAME$6].Constructor = Layout;

  $__default['default'].fn[NAME$6].noConflict = function () {
    $__default['default'].fn[NAME$6] = JQUERY_NO_CONFLICT$6;
    return Layout._jQueryInterface;
  };

  /**
   * --------------------------------------------
   * AdminLTE PushMenu.js
   * License MIT
   * --------------------------------------------
   */
  /**
   * Constants
   * ====================================================
   */

  var NAME$5 = 'PushMenu';
  var DATA_KEY$5 = 'lte.pushmenu';
  var EVENT_KEY$2 = "." + DATA_KEY$5;
  var JQUERY_NO_CONFLICT$5 = $__default['default'].fn[NAME$5];
  var EVENT_COLLAPSED$1 = "collapsed" + EVENT_KEY$2;
  var EVENT_SHOWN = "shown" + EVENT_KEY$2;
  var SELECTOR_TOGGLE_BUTTON$1 = '[data-widget="pushmenu"]';
  var SELECTOR_BODY = 'body';
  var SELECTOR_OVERLAY = '#sidebar-overlay';
  var SELECTOR_WRAPPER = '.wrapper';
  var CLASS_NAME_COLLAPSED = 'sidebar-collapse';
  var CLASS_NAME_OPEN$3 = 'sidebar-open';
  var CLASS_NAME_IS_OPENING$1 = 'sidebar-is-opening';
  var CLASS_NAME_CLOSED = 'sidebar-closed';
  var Default$5 = {
    autoCollapseSize: 992,
    enableRemember: false,
    noTransitionAfterReload: true
  };
  /**
   * Class Definition
   * ====================================================
   */

  var PushMenu = /*#__PURE__*/function () {
    function PushMenu(element, options) {
      this._element = element;
      this._options = $__default['default'].extend({}, Default$5, options);

      if ($__default['default'](SELECTOR_OVERLAY).length === 0) {
        this._addOverlay();
      }

      this._init();
    } // Public


    var _proto = PushMenu.prototype;

    _proto.expand = function expand() {
      var $bodySelector = $__default['default'](SELECTOR_BODY);

      if (this._options.autoCollapseSize && $__default['default'](window).width() <= this._options.autoCollapseSize) {
        $bodySelector.addClass(CLASS_NAME_OPEN$3);
      }

      $bodySelector.addClass(CLASS_NAME_IS_OPENING$1).removeClass(CLASS_NAME_COLLAPSED + " " + CLASS_NAME_CLOSED).delay(50).queue(function () {
        $bodySelector.removeClass(CLASS_NAME_IS_OPENING$1);
        $__default['default'](this).dequeue();
      });

      if (this._options.enableRemember) {
        localStorage.setItem("remember" + EVENT_KEY$2, CLASS_NAME_OPEN$3);
      }

      $__default['default'](this._element).trigger($__default['default'].Event(EVENT_SHOWN));
    };

    _proto.collapse = function collapse() {
      var $bodySelector = $__default['default'](SELECTOR_BODY);

      if (this._options.autoCollapseSize && $__default['default'](window).width() <= this._options.autoCollapseSize) {
        $bodySelector.removeClass(CLASS_NAME_OPEN$3).addClass(CLASS_NAME_CLOSED);
      }

      $bodySelector.addClass(CLASS_NAME_COLLAPSED);

      if (this._options.enableRemember) {
        localStorage.setItem("remember" + EVENT_KEY$2, CLASS_NAME_COLLAPSED);
      }

      $__default['default'](this._element).trigger($__default['default'].Event(EVENT_COLLAPSED$1));
    };

    _proto.toggle = function toggle() {
      if ($__default['default'](SELECTOR_BODY).hasClass(CLASS_NAME_COLLAPSED)) {
        this.expand();
      } else {
        this.collapse();
      }
    };

    _proto.autoCollapse = function autoCollapse(resize) {
      if (resize === void 0) {
        resize = false;
      }

      if (!this._options.autoCollapseSize) {
        return;
      }

      var $bodySelector = $__default['default'](SELECTOR_BODY);

      if ($__default['default'](window).width() <= this._options.autoCollapseSize) {
        if (!$bodySelector.hasClass(CLASS_NAME_OPEN$3)) {
          this.collapse();
        }
      } else if (resize === true) {
        if ($bodySelector.hasClass(CLASS_NAME_OPEN$3)) {
          $bodySelector.removeClass(CLASS_NAME_OPEN$3);
        } else if ($bodySelector.hasClass(CLASS_NAME_CLOSED)) {
          this.expand();
        }
      }
    };

    _proto.remember = function remember() {
      if (!this._options.enableRemember) {
        return;
      }

      var $body = $__default['default']('body');
      var toggleState = localStorage.getItem("remember" + EVENT_KEY$2);

      if (toggleState === CLASS_NAME_COLLAPSED) {
        if (this._options.noTransitionAfterReload) {
          $body.addClass('hold-transition').addClass(CLASS_NAME_COLLAPSED).delay(50).queue(function () {
            $__default['default'](this).removeClass('hold-transition');
            $__default['default'](this).dequeue();
          });
        } else {
          $body.addClass(CLASS_NAME_COLLAPSED);
        }
      } else if (this._options.noTransitionAfterReload) {
        $body.addClass('hold-transition').removeClass(CLASS_NAME_COLLAPSED).delay(50).queue(function () {
          $__default['default'](this).removeClass('hold-transition');
          $__default['default'](this).dequeue();
        });
      } else {
        $body.removeClass(CLASS_NAME_COLLAPSED);
      }
    } // Private
    ;

    _proto._init = function _init() {
      var _this = this;

      this.remember();
      this.autoCollapse();
      $__default['default'](window).resize(function () {
        _this.autoCollapse(true);
      });
    };

    _proto._addOverlay = function _addOverlay() {
      var _this2 = this;

      var overlay = $__default['default']('<div />', {
        id: 'sidebar-overlay'
      });
      overlay.on('click', function () {
        _this2.collapse();
      });
      $__default['default'](SELECTOR_WRAPPER).append(overlay);
    } // Static
    ;

    PushMenu._jQueryInterface = function _jQueryInterface(operation) {
      return this.each(function () {
        var data = $__default['default'](this).data(DATA_KEY$5);

        var _options = $__default['default'].extend({}, Default$5, $__default['default'](this).data());

        if (!data) {
          data = new PushMenu(this, _options);
          $__default['default'](this).data(DATA_KEY$5, data);
        }

        if (typeof operation === 'string' && /collapse|expand|toggle/.test(operation)) {
          data[operation]();
        }
      });
    };

    return PushMenu;
  }();
  /**
   * Data API
   * ====================================================
   */


  $__default['default'](document).on('click', SELECTOR_TOGGLE_BUTTON$1, function (event) {
    event.preventDefault();
    var button = event.currentTarget;

    if ($__default['default'](button).data('widget') !== 'pushmenu') {
      button = $__default['default'](button).closest(SELECTOR_TOGGLE_BUTTON$1);
    }

    PushMenu._jQueryInterface.call($__default['default'](button), 'toggle');
  });
  $__default['default'](window).on('load', function () {
    PushMenu._jQueryInterface.call($__default['default'](SELECTOR_TOGGLE_BUTTON$1));
  });
  /**
   * jQuery API
   * ====================================================
   */

  $__default['default'].fn[NAME$5] = PushMenu._jQueryInterface;
  $__default['default'].fn[NAME$5].Constructor = PushMenu;

  $__default['default'].fn[NAME$5].noConflict = function () {
    $__default['default'].fn[NAME$5] = JQUERY_NO_CONFLICT$5;
    return PushMenu._jQueryInterface;
  };

  /**
   * --------------------------------------------
   * AdminLTE SidebarSearch.js
   * License MIT
   * --------------------------------------------
   */
  /**
   * Constants
   * ====================================================
   */

  var NAME$4 = 'SidebarSearch';
  var DATA_KEY$4 = 'lte.sidebar-search';
  var JQUERY_NO_CONFLICT$4 = $__default['default'].fn[NAME$4];
  var CLASS_NAME_OPEN$2 = 'sidebar-search-open';
  var CLASS_NAME_ICON_SEARCH = 'fa-search';
  var CLASS_NAME_ICON_CLOSE = 'fa-times';
  var CLASS_NAME_HEADER = 'nav-header';
  var CLASS_NAME_SEARCH_RESULTS = 'sidebar-search-results';
  var CLASS_NAME_LIST_GROUP = 'list-group';
  var SELECTOR_DATA_WIDGET$1 = '[data-widget="sidebar-search"]';
  var SELECTOR_SIDEBAR = '.main-sidebar .nav-sidebar';
  var SELECTOR_NAV_LINK = '.nav-link';
  var SELECTOR_NAV_TREEVIEW = '.nav-treeview';
  var SELECTOR_SEARCH_INPUT$1 = SELECTOR_DATA_WIDGET$1 + " .form-control";
  var SELECTOR_SEARCH_BUTTON = SELECTOR_DATA_WIDGET$1 + " .btn";
  var SELECTOR_SEARCH_ICON = SELECTOR_SEARCH_BUTTON + " i";
  var SELECTOR_SEARCH_LIST_GROUP = "." + CLASS_NAME_LIST_GROUP;
  var SELECTOR_SEARCH_RESULTS = "." + CLASS_NAME_SEARCH_RESULTS;
  var SELECTOR_SEARCH_RESULTS_GROUP = SELECTOR_SEARCH_RESULTS + " ." + CLASS_NAME_LIST_GROUP;
  var Default$4 = {
    arrowSign: '->',
    minLength: 3,
    maxResults: 7,
    highlightName: true,
    highlightPath: false,
    highlightClass: 'text-light',
    notFoundText: 'No element found!'
  };
  var SearchItems = [];
  /**
   * Class Definition
   * ====================================================
   */

  var SidebarSearch = /*#__PURE__*/function () {
    function SidebarSearch(_element, _options) {
      this.element = _element;
      this.options = $__default['default'].extend({}, Default$4, _options);
      this.items = [];
    } // Public


    var _proto = SidebarSearch.prototype;

    _proto.init = function init() {
      var _this = this;

      if ($__default['default'](SELECTOR_DATA_WIDGET$1).length === 0) {
        return;
      }

      if ($__default['default'](SELECTOR_DATA_WIDGET$1).next(SELECTOR_SEARCH_RESULTS).length === 0) {
        $__default['default'](SELECTOR_DATA_WIDGET$1).after($__default['default']('<div />', {
          class: CLASS_NAME_SEARCH_RESULTS
        }));
      }

      if ($__default['default'](SELECTOR_SEARCH_RESULTS).children(SELECTOR_SEARCH_LIST_GROUP).length === 0) {
        $__default['default'](SELECTOR_SEARCH_RESULTS).append($__default['default']('<div />', {
          class: CLASS_NAME_LIST_GROUP
        }));
      }

      this._addNotFound();

      $__default['default'](SELECTOR_SIDEBAR).children().each(function (i, child) {
        _this._parseItem(child);
      });
    };

    _proto.search = function search() {
      var _this2 = this;

      var searchValue = $__default['default'](SELECTOR_SEARCH_INPUT$1).val().toLowerCase();

      if (searchValue.length < this.options.minLength) {
        $__default['default'](SELECTOR_SEARCH_RESULTS_GROUP).empty();

        this._addNotFound();

        this.close();
        return;
      }

      var searchResults = SearchItems.filter(function (item) {
        return item.name.toLowerCase().includes(searchValue);
      });
      var endResults = $__default['default'](searchResults.slice(0, this.options.maxResults));
      $__default['default'](SELECTOR_SEARCH_RESULTS_GROUP).empty();

      if (endResults.length === 0) {
        this._addNotFound();
      } else {
        endResults.each(function (i, result) {
          $__default['default'](SELECTOR_SEARCH_RESULTS_GROUP).append(_this2._renderItem(escape(result.name), escape(result.link), result.path));
        });
      }

      this.open();
    };

    _proto.open = function open() {
      $__default['default'](SELECTOR_DATA_WIDGET$1).parent().addClass(CLASS_NAME_OPEN$2);
      $__default['default'](SELECTOR_SEARCH_ICON).removeClass(CLASS_NAME_ICON_SEARCH).addClass(CLASS_NAME_ICON_CLOSE);
    };

    _proto.close = function close() {
      $__default['default'](SELECTOR_DATA_WIDGET$1).parent().removeClass(CLASS_NAME_OPEN$2);
      $__default['default'](SELECTOR_SEARCH_ICON).removeClass(CLASS_NAME_ICON_CLOSE).addClass(CLASS_NAME_ICON_SEARCH);
    };

    _proto.toggle = function toggle() {
      if ($__default['default'](SELECTOR_DATA_WIDGET$1).parent().hasClass(CLASS_NAME_OPEN$2)) {
        this.close();
      } else {
        this.open();
      }
    } // Private
    ;

    _proto._parseItem = function _parseItem(item, path) {
      var _this3 = this;

      if (path === void 0) {
        path = [];
      }

      if ($__default['default'](item).hasClass(CLASS_NAME_HEADER)) {
        return;
      }

      var itemObject = {};
      var navLink = $__default['default'](item).clone().find("> " + SELECTOR_NAV_LINK);
      var navTreeview = $__default['default'](item).clone().find("> " + SELECTOR_NAV_TREEVIEW);
      var link = navLink.attr('href');
      var name = navLink.find('p').children().remove().end().text();
      itemObject.name = this._trimText(name);
      itemObject.link = link;
      itemObject.path = path;

      if (navTreeview.length === 0) {
        SearchItems.push(itemObject);
      } else {
        var newPath = itemObject.path.concat([itemObject.name]);
        navTreeview.children().each(function (i, child) {
          _this3._parseItem(child, newPath);
        });
      }
    };

    _proto._trimText = function _trimText(text) {
      return $.trim(text.replace(/(\r\n|\n|\r)/gm, ' '));
    };

    _proto._renderItem = function _renderItem(name, link, path) {
      var _this4 = this;

      path = path.join(" " + this.options.arrowSign + " ");
      name = unescape(name);

      if (this.options.highlightName || this.options.highlightPath) {
        var searchValue = $__default['default'](SELECTOR_SEARCH_INPUT$1).val().toLowerCase();
        var regExp = new RegExp(searchValue, 'gi');

        if (this.options.highlightName) {
          name = name.replace(regExp, function (str) {
            return "<strong class=\"" + _this4.options.highlightClass + "\">" + str + "</strong>";
          });
        }

        if (this.options.highlightPath) {
          path = path.replace(regExp, function (str) {
            return "<strong class=\"" + _this4.options.highlightClass + "\">" + str + "</strong>";
          });
        }
      }

      var groupItemElement = $__default['default']('<a/>', {
        href: link,
        class: 'list-group-item'
      });
      var searchTitleElement = $__default['default']('<div/>', {
        class: 'search-title'
      }).html(name);
      var searchPathElement = $__default['default']('<div/>', {
        class: 'search-path'
      }).html(path);
      groupItemElement.append(searchTitleElement).append(searchPathElement);
      return groupItemElement;
    };

    _proto._addNotFound = function _addNotFound() {
      $__default['default'](SELECTOR_SEARCH_RESULTS_GROUP).append(this._renderItem(this.options.notFoundText, '#', []));
    } // Static
    ;

    SidebarSearch._jQueryInterface = function _jQueryInterface(config) {
      var data = $__default['default'](this).data(DATA_KEY$4);

      if (!data) {
        data = $__default['default'](this).data();
      }

      var _options = $__default['default'].extend({}, Default$4, typeof config === 'object' ? config : data);

      var plugin = new SidebarSearch($__default['default'](this), _options);
      $__default['default'](this).data(DATA_KEY$4, typeof config === 'object' ? config : data);

      if (typeof config === 'string' && /init|toggle|close|open|search/.test(config)) {
        plugin[config]();
      } else {
        plugin.init();
      }
    };

    return SidebarSearch;
  }();
  /**
   * Data API
   * ====================================================
   */


  $__default['default'](document).on('click', SELECTOR_SEARCH_BUTTON, function (event) {
    event.preventDefault();

    SidebarSearch._jQueryInterface.call($__default['default'](SELECTOR_DATA_WIDGET$1), 'toggle');
  });
  $__default['default'](document).on('keyup', SELECTOR_SEARCH_INPUT$1, function (event) {
    if (event.keyCode == 38) {
      event.preventDefault();
      $__default['default'](SELECTOR_SEARCH_RESULTS_GROUP).children().last().focus();
      return;
    }

    if (event.keyCode == 40) {
      event.preventDefault();
      $__default['default'](SELECTOR_SEARCH_RESULTS_GROUP).children().first().focus();
      return;
    }

    setTimeout(function () {
      SidebarSearch._jQueryInterface.call($__default['default'](SELECTOR_DATA_WIDGET$1), 'search');
    }, 100);
  });
  $__default['default'](document).on('keydown', SELECTOR_SEARCH_RESULTS_GROUP, function (event) {
    var $focused = $__default['default'](':focus');

    if (event.keyCode == 38) {
      event.preventDefault();

      if ($focused.is(':first-child')) {
        $focused.siblings().last().focus();
      } else {
        $focused.prev().focus();
      }
    }

    if (event.keyCode == 40) {
      event.preventDefault();

      if ($focused.is(':last-child')) {
        $focused.siblings().first().focus();
      } else {
        $focused.next().focus();
      }
    }
  });
  $__default['default'](window).on('load', function () {
    SidebarSearch._jQueryInterface.call($__default['default'](SELECTOR_DATA_WIDGET$1), 'init');
  });
  /**
   * jQuery API
   * ====================================================
   */

  $__default['default'].fn[NAME$4] = SidebarSearch._jQueryInterface;
  $__default['default'].fn[NAME$4].Constructor = SidebarSearch;

  $__default['default'].fn[NAME$4].noConflict = function () {
    $__default['default'].fn[NAME$4] = JQUERY_NO_CONFLICT$4;
    return SidebarSearch._jQueryInterface;
  };

  /**
   * --------------------------------------------
   * AdminLTE NavbarSearch.js
   * License MIT
   * --------------------------------------------
   */
  /**
   * Constants
   * ====================================================
   */

  var NAME$3 = 'NavbarSearch';
  var DATA_KEY$3 = 'lte.navbar-search';
  var JQUERY_NO_CONFLICT$3 = $__default['default'].fn[NAME$3];
  var SELECTOR_TOGGLE_BUTTON = '[data-widget="navbar-search"]';
  var SELECTOR_SEARCH_BLOCK = '.navbar-search-block';
  var SELECTOR_SEARCH_INPUT = '.form-control';
  var CLASS_NAME_OPEN$1 = 'navbar-search-open';
  var Default$3 = {
    resetOnClose: true,
    target: SELECTOR_SEARCH_BLOCK
  };
  /**
   * Class Definition
   * ====================================================
   */

  var NavbarSearch = /*#__PURE__*/function () {
    function NavbarSearch(_element, _options) {
      this._element = _element;
      this._config = $__default['default'].extend({}, Default$3, _options);
    } // Public


    var _proto = NavbarSearch.prototype;

    _proto.open = function open() {
      $__default['default'](this._config.target).css('display', 'flex').hide().fadeIn().addClass(CLASS_NAME_OPEN$1);
      $__default['default'](this._config.target + " " + SELECTOR_SEARCH_INPUT).focus();
    };

    _proto.close = function close() {
      $__default['default'](this._config.target).fadeOut().removeClass(CLASS_NAME_OPEN$1);

      if (this._config.resetOnClose) {
        $__default['default'](this._config.target + " " + SELECTOR_SEARCH_INPUT).val('');
      }
    };

    _proto.toggle = function toggle() {
      if ($__default['default'](this._config.target).hasClass(CLASS_NAME_OPEN$1)) {
        this.close();
      } else {
        this.open();
      }
    } // Static
    ;

    NavbarSearch._jQueryInterface = function _jQueryInterface(options) {
      return this.each(function () {
        var data = $__default['default'](this).data(DATA_KEY$3);

        var _options = $__default['default'].extend({}, Default$3, $__default['default'](this).data());

        if (!data) {
          data = new NavbarSearch(this, _options);
          $__default['default'](this).data(DATA_KEY$3, data);
        }

        if (!/toggle|close|open/.test(options)) {
          throw new Error("Undefined method " + options);
        }

        data[options]();
      });
    };

    return NavbarSearch;
  }();
  /**
   * Data API
   * ====================================================
   */


  $__default['default'](document).on('click', SELECTOR_TOGGLE_BUTTON, function (event) {
    event.preventDefault();
    var button = $__default['default'](event.currentTarget);

    if (button.data('widget') !== 'navbar-search') {
      button = button.closest(SELECTOR_TOGGLE_BUTTON);
    }

    NavbarSearch._jQueryInterface.call(button, 'toggle');
  });
  /**
   * jQuery API
   * ====================================================
   */

  $__default['default'].fn[NAME$3] = NavbarSearch._jQueryInterface;
  $__default['default'].fn[NAME$3].Constructor = NavbarSearch;

  $__default['default'].fn[NAME$3].noConflict = function () {
    $__default['default'].fn[NAME$3] = JQUERY_NO_CONFLICT$3;
    return NavbarSearch._jQueryInterface;
  };

  /**
   * --------------------------------------------
   * AdminLTE Toasts.js
   * License MIT
   * --------------------------------------------
   */
  /**
   * Constants
   * ====================================================
   */

  var NAME$2 = 'Toasts';
  var DATA_KEY$2 = 'lte.toasts';
  var EVENT_KEY$1 = "." + DATA_KEY$2;
  var JQUERY_NO_CONFLICT$2 = $__default['default'].fn[NAME$2];
  var EVENT_INIT = "init" + EVENT_KEY$1;
  var EVENT_CREATED = "created" + EVENT_KEY$1;
  var EVENT_REMOVED = "removed" + EVENT_KEY$1;
  var SELECTOR_CONTAINER_TOP_RIGHT = '#toastsContainerTopRight';
  var SELECTOR_CONTAINER_TOP_LEFT = '#toastsContainerTopLeft';
  var SELECTOR_CONTAINER_BOTTOM_RIGHT = '#toastsContainerBottomRight';
  var SELECTOR_CONTAINER_BOTTOM_LEFT = '#toastsContainerBottomLeft';
  var CLASS_NAME_TOP_RIGHT = 'toasts-top-right';
  var CLASS_NAME_TOP_LEFT = 'toasts-top-left';
  var CLASS_NAME_BOTTOM_RIGHT = 'toasts-bottom-right';
  var CLASS_NAME_BOTTOM_LEFT = 'toasts-bottom-left';
  var POSITION_TOP_RIGHT = 'topRight';
  var POSITION_TOP_LEFT = 'topLeft';
  var POSITION_BOTTOM_RIGHT = 'bottomRight';
  var POSITION_BOTTOM_LEFT = 'bottomLeft';
  var Default$2 = {
    position: POSITION_TOP_RIGHT,
    fixed: true,
    autohide: false,
    autoremove: true,
    delay: 1000,
    fade: true,
    icon: null,
    image: null,
    imageAlt: null,
    imageHeight: '25px',
    title: null,
    subtitle: null,
    close: true,
    body: null,
    class: null
  };
  /**
   * Class Definition
   * ====================================================
   */

  var Toasts = /*#__PURE__*/function () {
    function Toasts(element, config) {
      this._config = config;

      this._prepareContainer();

      $__default['default']('body').trigger($__default['default'].Event(EVENT_INIT));
    } // Public


    var _proto = Toasts.prototype;

    _proto.create = function create() {
      var toast = $__default['default']('<div class="toast" role="alert" aria-live="assertive" aria-atomic="true"/>');
      toast.data('autohide', this._config.autohide);
      toast.data('animation', this._config.fade);

      if (this._config.class) {
        toast.addClass(this._config.class);
      }

      if (this._config.delay && this._config.delay != 500) {
        toast.data('delay', this._config.delay);
      }

      var toastHeader = $__default['default']('<div class="toast-header">');

      if (this._config.image != null) {
        var toastImage = $__default['default']('<img />').addClass('rounded mr-2').attr('src', this._config.image).attr('alt', this._config.imageAlt);

        if (this._config.imageHeight != null) {
          toastImage.height(this._config.imageHeight).width('auto');
        }

        toastHeader.append(toastImage);
      }

      if (this._config.icon != null) {
        toastHeader.append($__default['default']('<i />').addClass('mr-2').addClass(this._config.icon));
      }

      if (this._config.title != null) {
        toastHeader.append($__default['default']('<strong />').addClass('mr-auto').html(this._config.title));
      }

      if (this._config.subtitle != null) {
        toastHeader.append($__default['default']('<small />').html(this._config.subtitle));
      }

      if (this._config.close == true) {
        var toastClose = $__default['default']('<button data-dismiss="toast" />').attr('type', 'button').addClass('ml-2 mb-1 close').attr('aria-label', 'Close').append('<span aria-hidden="true">&times;</span>');

        if (this._config.title == null) {
          toastClose.toggleClass('ml-2 ml-auto');
        }

        toastHeader.append(toastClose);
      }

      toast.append(toastHeader);

      if (this._config.body != null) {
        toast.append($__default['default']('<div class="toast-body" />').html(this._config.body));
      }

      $__default['default'](this._getContainerId()).prepend(toast);
      var $body = $__default['default']('body');
      $body.trigger($__default['default'].Event(EVENT_CREATED));
      toast.toast('show');

      if (this._config.autoremove) {
        toast.on('hidden.bs.toast', function () {
          $__default['default'](this).delay(200).remove();
          $body.trigger($__default['default'].Event(EVENT_REMOVED));
        });
      }
    } // Static
    ;

    _proto._getContainerId = function _getContainerId() {
      if (this._config.position == POSITION_TOP_RIGHT) {
        return SELECTOR_CONTAINER_TOP_RIGHT;
      }

      if (this._config.position == POSITION_TOP_LEFT) {
        return SELECTOR_CONTAINER_TOP_LEFT;
      }

      if (this._config.position == POSITION_BOTTOM_RIGHT) {
        return SELECTOR_CONTAINER_BOTTOM_RIGHT;
      }

      if (this._config.position == POSITION_BOTTOM_LEFT) {
        return SELECTOR_CONTAINER_BOTTOM_LEFT;
      }
    };

    _proto._prepareContainer = function _prepareContainer() {
      if ($__default['default'](this._getContainerId()).length === 0) {
        var container = $__default['default']('<div />').attr('id', this._getContainerId().replace('#', ''));

        if (this._config.position == POSITION_TOP_RIGHT) {
          container.addClass(CLASS_NAME_TOP_RIGHT);
        } else if (this._config.position == POSITION_TOP_LEFT) {
          container.addClass(CLASS_NAME_TOP_LEFT);
        } else if (this._config.position == POSITION_BOTTOM_RIGHT) {
          container.addClass(CLASS_NAME_BOTTOM_RIGHT);
        } else if (this._config.position == POSITION_BOTTOM_LEFT) {
          container.addClass(CLASS_NAME_BOTTOM_LEFT);
        }

        $__default['default']('body').append(container);
      }

      if (this._config.fixed) {
        $__default['default'](this._getContainerId()).addClass('fixed');
      } else {
        $__default['default'](this._getContainerId()).removeClass('fixed');
      }
    } // Static
    ;

    Toasts._jQueryInterface = function _jQueryInterface(option, config) {
      return this.each(function () {
        var _options = $__default['default'].extend({}, Default$2, config);

        var toast = new Toasts($__default['default'](this), _options);

        if (option === 'create') {
          toast[option]();
        }
      });
    };

    return Toasts;
  }();
  /**
   * jQuery API
   * ====================================================
   */


  $__default['default'].fn[NAME$2] = Toasts._jQueryInterface;
  $__default['default'].fn[NAME$2].Constructor = Toasts;

  $__default['default'].fn[NAME$2].noConflict = function () {
    $__default['default'].fn[NAME$2] = JQUERY_NO_CONFLICT$2;
    return Toasts._jQueryInterface;
  };

  /**
   * --------------------------------------------
   * AdminLTE TodoList.js
   * License MIT
   * --------------------------------------------
   */
  /**
   * Constants
   * ====================================================
   */

  var NAME$1 = 'TodoList';
  var DATA_KEY$1 = 'lte.todolist';
  var JQUERY_NO_CONFLICT$1 = $__default['default'].fn[NAME$1];
  var SELECTOR_DATA_TOGGLE = '[data-widget="todo-list"]';
  var CLASS_NAME_TODO_LIST_DONE = 'done';
  var Default$1 = {
    onCheck: function onCheck(item) {
      return item;
    },
    onUnCheck: function onUnCheck(item) {
      return item;
    }
  };
  /**
   * Class Definition
   * ====================================================
   */

  var TodoList = /*#__PURE__*/function () {
    function TodoList(element, config) {
      this._config = config;
      this._element = element;

      this._init();
    } // Public


    var _proto = TodoList.prototype;

    _proto.toggle = function toggle(item) {
      item.parents('li').toggleClass(CLASS_NAME_TODO_LIST_DONE);

      if (!$__default['default'](item).prop('checked')) {
        this.unCheck($__default['default'](item));
        return;
      }

      this.check(item);
    };

    _proto.check = function check(item) {
      this._config.onCheck.call(item);
    };

    _proto.unCheck = function unCheck(item) {
      this._config.onUnCheck.call(item);
    } // Private
    ;

    _proto._init = function _init() {
      var _this = this;

      var $toggleSelector = this._element;
      $toggleSelector.find('input:checkbox:checked').parents('li').toggleClass(CLASS_NAME_TODO_LIST_DONE);
      $toggleSelector.on('change', 'input:checkbox', function (event) {
        _this.toggle($__default['default'](event.target));
      });
    } // Static
    ;

    TodoList._jQueryInterface = function _jQueryInterface(config) {
      return this.each(function () {
        var data = $__default['default'](this).data(DATA_KEY$1);

        if (!data) {
          data = $__default['default'](this).data();
        }

        var _options = $__default['default'].extend({}, Default$1, typeof config === 'object' ? config : data);

        var plugin = new TodoList($__default['default'](this), _options);
        $__default['default'](this).data(DATA_KEY$1, typeof config === 'object' ? config : data);

        if (config === 'init') {
          plugin[config]();
        }
      });
    };

    return TodoList;
  }();
  /**
   * Data API
   * ====================================================
   */


  $__default['default'](window).on('load', function () {
    TodoList._jQueryInterface.call($__default['default'](SELECTOR_DATA_TOGGLE));
  });
  /**
   * jQuery API
   * ====================================================
   */

  $__default['default'].fn[NAME$1] = TodoList._jQueryInterface;
  $__default['default'].fn[NAME$1].Constructor = TodoList;

  $__default['default'].fn[NAME$1].noConflict = function () {
    $__default['default'].fn[NAME$1] = JQUERY_NO_CONFLICT$1;
    return TodoList._jQueryInterface;
  };

  /**
   * --------------------------------------------
   * AdminLTE Treeview.js
   * License MIT
   * --------------------------------------------
   */
  /**
   * Constants
   * ====================================================
   */

  var NAME = 'Treeview';
  var DATA_KEY = 'lte.treeview';
  var EVENT_KEY = "." + DATA_KEY;
  var JQUERY_NO_CONFLICT = $__default['default'].fn[NAME];
  var EVENT_EXPANDED = "expanded" + EVENT_KEY;
  var EVENT_COLLAPSED = "collapsed" + EVENT_KEY;
  var EVENT_LOAD_DATA_API = "load" + EVENT_KEY;
  var SELECTOR_LI = '.nav-item';
  var SELECTOR_LINK = '.nav-link';
  var SELECTOR_TREEVIEW_MENU = '.nav-treeview';
  var SELECTOR_OPEN = '.menu-open';
  var SELECTOR_DATA_WIDGET = '[data-widget="treeview"]';
  var CLASS_NAME_OPEN = 'menu-open';
  var CLASS_NAME_IS_OPENING = 'menu-is-opening';
  var CLASS_NAME_SIDEBAR_COLLAPSED = 'sidebar-collapse';
  var Default = {
    trigger: SELECTOR_DATA_WIDGET + " " + SELECTOR_LINK,
    animationspeed: 300,
    accordion: true,
    expandSidebar: false,
    sidebarButtonSelector: '[data-widget="pushmenu"]'
  };
  /**
   * Class Definition
   * ====================================================
   */

  var Treeview = /*#__PURE__*/function () {
    function Treeview(element, config) {
      this._config = config;
      this._element = element;
    } // Public


    var _proto = Treeview.prototype;

    _proto.init = function init() {
      $__default['default']("" + SELECTOR_LI + SELECTOR_OPEN + " " + SELECTOR_TREEVIEW_MENU + SELECTOR_OPEN).css('display', 'block');

      this._setupListeners();
    };

    _proto.expand = function expand(treeviewMenu, parentLi) {
      var _this = this;

      var expandedEvent = $__default['default'].Event(EVENT_EXPANDED);

      if (this._config.accordion) {
        var openMenuLi = parentLi.siblings(SELECTOR_OPEN).first();
        var openTreeview = openMenuLi.find(SELECTOR_TREEVIEW_MENU).first();
        this.collapse(openTreeview, openMenuLi);
      }

      parentLi.addClass(CLASS_NAME_IS_OPENING);
      treeviewMenu.stop().slideDown(this._config.animationspeed, function () {
        parentLi.addClass(CLASS_NAME_OPEN);
        $__default['default'](_this._element).trigger(expandedEvent);
      });

      if (this._config.expandSidebar) {
        this._expandSidebar();
      }
    };

    _proto.collapse = function collapse(treeviewMenu, parentLi) {
      var _this2 = this;

      var collapsedEvent = $__default['default'].Event(EVENT_COLLAPSED);
      parentLi.removeClass(CLASS_NAME_IS_OPENING + " " + CLASS_NAME_OPEN);
      treeviewMenu.stop().slideUp(this._config.animationspeed, function () {
        $__default['default'](_this2._element).trigger(collapsedEvent);
        treeviewMenu.find(SELECTOR_OPEN + " > " + SELECTOR_TREEVIEW_MENU).slideUp();
        treeviewMenu.find(SELECTOR_OPEN).removeClass(CLASS_NAME_OPEN);
        //Fix bug with adminlte not removing multilevel class .menu-is-opening
        treeviewMenu.find('.' + CLASS_NAME_IS_OPENING).removeClass(CLASS_NAME_IS_OPENING);
      });
    };

    _proto.toggle = function toggle(event) {
      var $relativeTarget = $__default['default'](event.currentTarget);
      var $parent = $relativeTarget.parent();
      var treeviewMenu = $parent.find("> " + SELECTOR_TREEVIEW_MENU);

      if (!treeviewMenu.is(SELECTOR_TREEVIEW_MENU)) {
        if (!$parent.is(SELECTOR_LI)) {
          treeviewMenu = $parent.parent().find("> " + SELECTOR_TREEVIEW_MENU);
        }

        if (!treeviewMenu.is(SELECTOR_TREEVIEW_MENU)) {
          return;
        }
      }

      event.preventDefault();
      var parentLi = $relativeTarget.parents(SELECTOR_LI).first();
      var isOpen = parentLi.hasClass(CLASS_NAME_OPEN);

      if (isOpen) {
        this.collapse($__default['default'](treeviewMenu), parentLi);
      } else {
        this.expand($__default['default'](treeviewMenu), parentLi);
      }
    } // Private
    ;

    _proto._setupListeners = function _setupListeners() {
      var _this3 = this;

      var elementId = this._element.attr('id') !== undefined ? "#" + this._element.attr('id') : '';
      $__default['default'](document).on('click', "" + elementId + this._config.trigger, function (event) {
        _this3.toggle(event);
      });
    };

    _proto._expandSidebar = function _expandSidebar() {
      if ($__default['default']('body').hasClass(CLASS_NAME_SIDEBAR_COLLAPSED)) {
        $__default['default'](this._config.sidebarButtonSelector).PushMenu('expand');
      }
    } // Static
    ;

    Treeview._jQueryInterface = function _jQueryInterface(config) {
      return this.each(function () {
        var data = $__default['default'](this).data(DATA_KEY);

        var _options = $__default['default'].extend({}, Default, $__default['default'](this).data());

        if (!data) {
          data = new Treeview($__default['default'](this), _options);
          $__default['default'](this).data(DATA_KEY, data);
        }

        if (config === 'init') {
          data[config]();
        }
      });
    };

    return Treeview;
  }();
  /**
   * Data API
   * ====================================================
   */


  $__default['default'](window).on(EVENT_LOAD_DATA_API, function () {
    $__default['default'](SELECTOR_DATA_WIDGET).each(function () {
      Treeview._jQueryInterface.call($__default['default'](this), 'init');
    });
  });
  /**
   * jQuery API
   * ====================================================
   */

  $__default['default'].fn[NAME] = Treeview._jQueryInterface;
  $__default['default'].fn[NAME].Constructor = Treeview;

  $__default['default'].fn[NAME].noConflict = function () {
    $__default['default'].fn[NAME] = JQUERY_NO_CONFLICT;
    return Treeview._jQueryInterface;
  };

  exports.CardRefresh = CardRefresh;
  exports.CardWidget = CardWidget;
  exports.ControlSidebar = ControlSidebar;
  exports.DirectChat = DirectChat;
  exports.Dropdown = Dropdown;
  exports.ExpandableTable = ExpandableTable;
  exports.Fullscreen = Fullscreen;
  exports.IFrame = IFrame;
  exports.Layout = Layout;
  exports.NavbarSearch = NavbarSearch;
  exports.PushMenu = PushMenu;
  exports.SidebarSearch = SidebarSearch;
  exports.Toasts = Toasts;
  exports.TodoList = TodoList;
  exports.Treeview = Treeview;

  Object.defineProperty(exports, '__esModule', { value: true });

})));
/* exported BazHelpers */
/* globals */
/*
* @title                    : BazHelpers
* @description              : Baz Helper Tools Lib (include Various helper tools)
* @developer                : guru@bazaari.com.au
* @usage                    : BazHelpers._function_(_options_);
* @functions                :
* @options                  :
*/

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

// eslint-disable-next-line no-unused-vars
var BazHelpers = function() {
    var BazHelpers = void 0;

    // Error
    function error(errorMsg) {
        throw new Error(errorMsg);
    }

    function bazModal(options) {
        var close, closeButton, createButton, updateButton, title, modalCentered, modalScrollable, modalSize, modalWidth;
        if (!options.modalId) {
            error('modalId not present!');
        } else {
            if (options.modalTitle) {
                title = options.modalTitle;
            } else {
                title = '';
            }
            if (options.modalCentered) {
                modalCentered = 'modal-dialog-centered';
            } else {
                modalCentered = '';
            }
            if (options.modalScrollable) {
                modalScrollable = 'modal-dialog-scrollable';
            } else {
                modalScrollable = '';
            }
            if (options.modalSize) {
                modalSize = 'modal-' + options.modalSize;
            } else {
                modalSize = '';
            }

            if (options.modalWidth) {
                modalWidth = 'style="max-width:' + options.modalWidth + ';"';
            } else {
                modalWidth = '';
            }
            if (options.modalButtons.close) {
                closeButton = '<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>';
                close = '<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">' +
                            '<span aria-hidden="true">&times;</span>' +
                        '</button>'
            } else {
                closeButton = '';
                close = '';
            }
            if (options.modalButtons.create) {
                createButton = '<button type="button" class="btn btn-sm btn-' + options.modalButtons.create.type + '">' + options.modalButtons.create.title + '</button>'
            } else {
                createButton = '';
            }
            if (options.modalButtons.update) {
                updateButton = '<button type="button" class="btn btn-sm btn-' + options.modalButtons.update.type + '">' + options.modalButtons.update.title + '</button>'
            } else {
                updateButton = '';
            }
        }
        var modalHTML =
            '<div class="modal fadeIn ' + options.modalAdditionalClasses + '" id="' + options.modalId + '-modal" tabindex="-1"  aria-labelledby="' +
            options.modalId + '-label" aria-hidden="true" data-backdrop="' + options.modalBackdrop + '" data-keyboard="' + options.modalEscClose + '">' +
            '<div ' + modalWidth + ' class="modal-dialog ' + modalCentered + ' ' + modalScrollable + ' ' + modalSize + '" role="document">' +
            '<div class="modal-content rounded-0 ' + options.modalContentAdditionalClasses + '">';

        if (options.modalHeader) {
            modalHTML +=
                '<div class="modal-header border-bottom-0 rounded-0 bg-' + options.modalType + ' ' + options.modalHeaderAdditionalClasses + '"><h5 class="modal-title" id="' +
                options.modalId + '-label">' + title + '</h5>' + close + '</div>';
        }

        modalHTML += '<div class="modal-body ' + options.modalBodyAdditionalClasses + '"></div>';

        if (options.modalFooter) {
            modalHTML += '<div class="modal-footer ' + options.modalFooterAdditionalClasses + '">' +
                                closeButton +
                                createButton +
                                updateButton +
                            '</div>';
        }

        modalHTML += '</div></div></div>';

        $(options.modalAppendOn).append(modalHTML);
    }

    function bazCreateHtmlList(obj) {
        var output = '';
        output += '<ul>';
        Object.keys(obj).forEach(function(k) {
            if (typeof obj[k] == "object" && obj[k] !== null){
                output += '<li>' + k + ' : ';
                output += bazCreateHtmlList(obj[k]);
                output += '</li>';
            } else {
                output += '<li>' + k + ' : ' + obj[k] + '</li>';
            }
        });
        output += '</ul>';

        return output;
    }

    // Copy object
    function iterationCopy(src) {
        var target = { };
        for (var prop in src) {
            if (src.hasOwnProperty(prop)) {
                target[prop] = src[prop];
            }
        }
        return target;
    }

    // Check if Object Empty
    function isEmpty(obj) {
        for (var prop in obj) {
            if (obj.hasOwnProperty(prop)) {
                return false;
            }
        }
        return true;
    }

    //find object key by value
    function getKeyByValue(objectOrArray, value) {
        if (typeof objectOrArray === 'object') {
            return Object.keys(objectOrArray).find(key => objectOrArray[key] == value);
        } else if (Array.isArray(objectOrArray)) {
            return objectOrArray.indexOf(value);
        }

        return false;
    }

    function bazHelpersConstructor() {
        // if something needs to be constructed
        return null;
    }

    /**
     * @param {*} val - value to check
     *
     * @returns {boolean} true if the value is then-able
     */
    function isPromise(val) {
        return val != null && typeof val.then === 'function';
    }

    /**
     * src  github interval-promise
     * @param {function} func - function to execute
     * @param {number|function(number):number} intervalLength - length in ms to wait before executing again
     * @param {{iterations: Infinity|number, stopOnError: boolean}} [options]
     *
     * @returns {Promise} Promise object with no result
     */
    function interval(func, intervalLength, options = { }) {

        validateArgs(func, intervalLength, options);

        var defaults = {
            iterations: Infinity,
            stopOnError: true
        };
        var settings = Object.assign(defaults, options);

        return new Promise(function (rootPromiseResolve, rootPromiseReject) {

            var callFunction = function callFunction(currentIteration) {

                // Set up a way to track if a "stop" was requested by the user function
                var stopRequested = false;
                var stop = function stop() {
                    stopRequested = true;
                };

                // Set up a function to call the next iteration. This is abstracted so it can be called by .then(), or in .catch(), if options allow.
                var callNext = function callNext() {
                    // If we've hit the desired number of iterations, or stop was called, resolve the root promise and return
                    if (currentIteration === settings.iterations || stopRequested) {
                        rootPromiseResolve();
                        return;
                    }

                    // Otherwise, call the next iteration
                    callFunction(currentIteration + 1);
                };

                // Calculate our interval length
                var calculatedIntervalLength = typeof intervalLength === 'function' ? intervalLength(currentIteration) : intervalLength;

                // If the interval length was calculated, validate the result
                if (typeof intervalLength === 'function') {
                    if (!Number.isInteger(calculatedIntervalLength) || calculatedIntervalLength < 0) {
                        rootPromiseReject(new Error('Function for "intervalLength" argument must return a non-negative integer.'));
                        return;
                    }
                }

                // Call the user function after the desired interval length. After, call the next iteration (and/or handle error)
                setTimeout(function () {

                    var returnVal = func(currentIteration, stop);

                    // Ensure that the value returned is a promise
                    if (!isPromise(returnVal)) {
                        rootPromiseReject(new Error('Return value of "func" must be a Promise.'));
                        return;
                    }

                    returnVal.then(callNext).catch(function (err) {
                        if (!settings.stopOnError) {
                            callNext();
                            return;
                        }

                        rootPromiseReject(err);
                    });
                }, calculatedIntervalLength);
            };

            callFunction(1);
        });
    }

    /**
     * A helper function to validate the arguments passed to interval(...)
     *
     * @param {*} func
     * @param {*} intervalLength
     * @param {*} options
     */
    function validateArgs(func, intervalLength, options) {

        // Validate "func"
        if (typeof func !== 'function') {
            throw new TypeError('Argument 1, "func", must be a function.');
        }

        // Validate "intervalLength"
        if (typeof intervalLength === 'number') {
            if (!Number.isInteger(intervalLength) || intervalLength < 0) {
                throw new TypeError('Argument 2, "intervalLength", must be a non-negative integer or a function that returns a non-negative integer.');
            }
        } else if (typeof intervalLength !== 'function') {
            throw new TypeError('Argument 2, "intervalLength", must be a non-negative integer or a function that returns a non-negative integer.');
        }

        // Validate options...
        if ((typeof options === 'undefined' ? 'undefined' : _typeof(options)) !== 'object') {
            throw new TypeError('Argument 3, "options", must be an object.');
        }

        // Validate passed keys
        var allowedKeys = ['iterations', 'stopOnError'];

        Object.keys(options).forEach(function (key) {
            if (!allowedKeys.includes(key)) {
                throw new TypeError('Option "' + key + '" is not a valid option.');
            }
        });

        // validate "iterations" option (if passed)
        if (typeof options.iterations !== 'undefined') {
            if (options.iterations !== Infinity && (!Number.isInteger(options.iterations) || options.iterations < 1)) {
                throw new TypeError('Option "iterations" must be Infinity or an integer greater than 0.');
            }
        }

        // validate "stopOnError" option (if passed)
        if (typeof options.stopOnError !== 'undefined') {
            if (typeof options.stopOnError !== 'boolean') {
                throw new TypeError('Option "stopOnError" must be a boolean.');
            }
        }
    }

    function findPath(obj, key, array = true) {
        const path = [];
        const keyExists = (obj) => {
            if (!obj || (typeof obj !== "object" && !Array.isArray(obj))) {
                return false;
            }
            else if (obj.hasOwnProperty(key)) {
                return true;
            }
            else if (Array.isArray(obj)) {
                let parentKey = path.length ? path.pop() : "";

                for (let i = 0; i < obj.length; i++) {
                    path.push(`${parentKey}[${i}]`);
                    const result = keyExists(obj[i], key);
                    if (result) {
                        return result;
                    }
                    path.pop();
                }
            }
            else {
                for (const k in obj) {
                    path.push(k);
                    const result = keyExists(obj[k], key);
                    if (result) {
                        return result;
                    }
                    path.pop();
                }
            }
            return false;
        };

        keyExists(obj);

        if (array) {
            return path;
        }

        return path.join(".");
    }

    function fetchFromObject(obj, key) {
        if (typeof obj === 'undefined') {
            return false;
        }

        var _index = key.indexOf('.')

        if (_index > -1) {
            return fetchFromObject(obj[key.substring(0, _index)], key.substr(_index + 1));
        }

        return obj[key];
    }

    function setTimeoutTimers() {
        var timers = []

        const getIndex = (array, attr, value) => {
            for (let i = 0; i < array.length; i += 1) {
                if (array[i][attr] === value) {
                    return i
                }
            }
            return -1
        };

        // add
        const add = (func, time = 1000, dataCollectionObj = null, identifier = null) => {
            var id = setTimeout(() => {
                let index = getIndex(timers, 'id', id)
                timers.splice(index, 1)
                func()
            }, time);

            timers.push({
                id: id,
                dataCollectionObj : dataCollectionObj,
                identifier : identifier,
                time: time,
                debug: func.toString()
            });

            return true;
        };

        // get all active timers
        const all = () => timers

        // stop timer by timer id
        const stop = (id = null, dataCollectionObj = null, identifier = null) => {
            let index;
            if (dataCollectionObj) {
                index = getIndex(timers, 'dataCollectionObj', dataCollectionObj);
                if (index !== -1) {
                    id = timers[index]['id'];
                }
            } else if (identifier) {
                index = getIndex(timers, 'identifier', identifier);
                if (index !== -1) {
                    id = timers[index]['id'];
                }
            } else {
                index = getIndex(timers, 'id', id);
            }
            if (index !== -1) {
                clearTimeout(timers[index].id)
                timers.splice(index, 1)
            }

            return true;
        };

        // stop all timers
        const stopAll = () => {
            for (let i = 0; i < timers.length; i++) {
                clearTimeout(timers[i].id)
            }
            timers = []

            return timers;
        };

        return {
            add: add,
            all: all,
            stop: stop,
            stopAll: stopAll,
        };
    }

    function getTimerId(identifier) {
        var timers = BazHelpers.setTimeoutTimers.all();
        var id = false;

        if (timers.length > 0) {
            $(timers).each(function(i, timer) {
                if (timer['identifier'] === identifier) {
                    id = timer['id'];
                }
            });
        }

        return id;
    }

    function ping(source, option, callback) {
        var promise, resolve, reject;
        this.option = option || {};
        this.favicon = this.option.favicon || "/favicon.ico";
        this.logError = this.option.logError || false;

        if (typeof Promise !== "undefined") {
            promise = new Promise(function(_resolve, _reject) {
                resolve = _resolve;
                reject = _reject;
            });
        }

        var self = this;
        self.wasSuccess = false;
        self.img = new Image();
        self.img.onload = onload;
        self.img.onerror = onerror;

        var timer = null;
        var start = new Date();

        function onload(e) {
            self.wasSuccess = true;
            pingCheck.call(self, e);
        }

        function onerror(e) {
            self.wasSuccess = false;
            pingCheck.call(self, e);
        }

        /**
         * Times ping and triggers callback.
         */
        function pingCheck() {
            if (timer) {
                clearTimeout(timer);
            }

            var pong = new Date() - start;

            if (!callback) {
                if (promise) {
                    return this.wasSuccess ? resolve(pong) : reject(pong);
                } else {
                    throw new Error("Promise is not supported by your browser. Use callback instead.");
                }
            } else if (typeof callback === "function") {
                if (!this.wasSuccess) {
                    if (self.logError) {
                        //eslint-disable-next-line
                        console.error("error loading resource");
                    }
                    if (promise) {
                        reject(pong);
                    }

                    return callback("error", pong);
                }

                if (promise) {
                    resolve(pong);
                }

                return callback(null, pong);
            } else {
                throw new Error("Callback is not a function.");
            }
        }

        self.img.src = source + self.favicon + "?" + (+new Date()); // Trigger image load with cache buster

        return promise;
    }

    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    function stringToBoolean(string) {
        switch (string.toLowerCase().trim()) {
            case "true":
            case "yes":
            case "1":
              return true;

            case "false":
            case "no":
            case "0":
            case null:
            case undefined:
            case 'undefined':
              return false;

            default:
              return JSON.parse(string);
        }
    }

    function setup(BazHelpersConstructor) {
        BazHelpers = BazHelpersConstructor;

        BazHelpers.defaults = {
            'modalId'                               : '',
            'modalTitle'                            : '',
            'modalCentered'                         : false,
            'modalScrollable'                       : false,
            'modalSize'                             : 'lg',
            'modalWidth'                            : '',
            'modalAdditionalClasses'                : '',
            'modalBackdrop'                         : 'static',
            'modalEscClose'                         : true,
            'modalContentAdditionalClasses'         : '',
            'modalHeader'                           : true,
            'modalType'                             : 'primary',
            'modalHeaderAdditionalClasses'          : '',
            'modalBodyAdditionalClasses'            : '',
            'modalFooter'                           : true,
            'modalFooterAdditionalClasses'          : '',
            'modalAppendOn'                         : 'body',
            'modalButtons'                          : {
                'close'                             : false,
                'create'                            : {
                    'id'                            : 'add',
                    'title'                         : 'Add',
                    'type'                          : 'primary',
                    'action'                        : 'post',
                    'actionUrl'                     : '{{createActionUrl}}',
                    'createSuccessRedirectUrl'      : '{{createSuccessRedirectUrl}}',
                    'createSuccessNotifyMessage'    : '{{createSuccessNotifyMessage}}'
                },
                'update'                            : {
                    'id'                            : 'update',
                    'title'                         : 'Update',
                    'type'                          : 'primary',
                    'action'                        : 'post',
                    'actionUrl'                     : '{{updateActionUrl}}',
                    'updateSuccessRedirectUrl'      : '{{updateSuccessRedirectUrl}}',
                    'createSuccessNotifyMessage'    : '{{createSuccessNotifyMessage}}'
                }
            }
        }

        BazHelpers.modal = function(options) {
            bazModal(_extends(BazHelpers.defaults, options));
        }

        BazHelpers.createHtmlList = function(options) {
            var objToHtml = bazCreateHtmlList(options.obj);
            return objToHtml;
        }

        BazHelpers.iterationCopy = function(src) {
            return iterationCopy(src);
        }

        BazHelpers.md5 = function(d){var r = M(V(Y(X(d),8*d.length)));return r.toLowerCase()};function M(d){for(var _,m="0123456789ABCDEF",f="",r=0;r<d.length;r++)_=d.charCodeAt(r),f+=m.charAt(_>>>4&15)+m.charAt(15&_);return f}function X(d){for(var _=Array(d.length>>2),m=0;m<_.length;m++)_[m]=0;for(m=0;m<8*d.length;m+=8)_[m>>5]|=(255&d.charCodeAt(m/8))<<m%32;return _}function V(d){for(var _="",m=0;m<32*d.length;m+=8)_+=String.fromCharCode(d[m>>5]>>>m%32&255);return _}function Y(d,_){d[_>>5]|=128<<_%32,d[14+(_+64>>>9<<4)]=_;for(var m=1732584193,f=-271733879,r=-1732584194,i=271733878,n=0;n<d.length;n+=16){var h=m,t=f,g=r,e=i;f=md5_ii(f=md5_ii(f=md5_ii(f=md5_ii(f=md5_hh(f=md5_hh(f=md5_hh(f=md5_hh(f=md5_gg(f=md5_gg(f=md5_gg(f=md5_gg(f=md5_ff(f=md5_ff(f=md5_ff(f=md5_ff(f,r=md5_ff(r,i=md5_ff(i,m=md5_ff(m,f,r,i,d[n+0],7,-680876936),f,r,d[n+1],12,-389564586),m,f,d[n+2],17,606105819),i,m,d[n+3],22,-1044525330),r=md5_ff(r,i=md5_ff(i,m=md5_ff(m,f,r,i,d[n+4],7,-176418897),f,r,d[n+5],12,1200080426),m,f,d[n+6],17,-1473231341),i,m,d[n+7],22,-45705983),r=md5_ff(r,i=md5_ff(i,m=md5_ff(m,f,r,i,d[n+8],7,1770035416),f,r,d[n+9],12,-1958414417),m,f,d[n+10],17,-42063),i,m,d[n+11],22,-1990404162),r=md5_ff(r,i=md5_ff(i,m=md5_ff(m,f,r,i,d[n+12],7,1804603682),f,r,d[n+13],12,-40341101),m,f,d[n+14],17,-1502002290),i,m,d[n+15],22,1236535329),r=md5_gg(r,i=md5_gg(i,m=md5_gg(m,f,r,i,d[n+1],5,-165796510),f,r,d[n+6],9,-1069501632),m,f,d[n+11],14,643717713),i,m,d[n+0],20,-373897302),r=md5_gg(r,i=md5_gg(i,m=md5_gg(m,f,r,i,d[n+5],5,-701558691),f,r,d[n+10],9,38016083),m,f,d[n+15],14,-660478335),i,m,d[n+4],20,-405537848),r=md5_gg(r,i=md5_gg(i,m=md5_gg(m,f,r,i,d[n+9],5,568446438),f,r,d[n+14],9,-1019803690),m,f,d[n+3],14,-187363961),i,m,d[n+8],20,1163531501),r=md5_gg(r,i=md5_gg(i,m=md5_gg(m,f,r,i,d[n+13],5,-1444681467),f,r,d[n+2],9,-51403784),m,f,d[n+7],14,1735328473),i,m,d[n+12],20,-1926607734),r=md5_hh(r,i=md5_hh(i,m=md5_hh(m,f,r,i,d[n+5],4,-378558),f,r,d[n+8],11,-2022574463),m,f,d[n+11],16,1839030562),i,m,d[n+14],23,-35309556),r=md5_hh(r,i=md5_hh(i,m=md5_hh(m,f,r,i,d[n+1],4,-1530992060),f,r,d[n+4],11,1272893353),m,f,d[n+7],16,-155497632),i,m,d[n+10],23,-1094730640),r=md5_hh(r,i=md5_hh(i,m=md5_hh(m,f,r,i,d[n+13],4,681279174),f,r,d[n+0],11,-358537222),m,f,d[n+3],16,-722521979),i,m,d[n+6],23,76029189),r=md5_hh(r,i=md5_hh(i,m=md5_hh(m,f,r,i,d[n+9],4,-640364487),f,r,d[n+12],11,-421815835),m,f,d[n+15],16,530742520),i,m,d[n+2],23,-995338651),r=md5_ii(r,i=md5_ii(i,m=md5_ii(m,f,r,i,d[n+0],6,-198630844),f,r,d[n+7],10,1126891415),m,f,d[n+14],15,-1416354905),i,m,d[n+5],21,-57434055),r=md5_ii(r,i=md5_ii(i,m=md5_ii(m,f,r,i,d[n+12],6,1700485571),f,r,d[n+3],10,-1894986606),m,f,d[n+10],15,-1051523),i,m,d[n+1],21,-2054922799),r=md5_ii(r,i=md5_ii(i,m=md5_ii(m,f,r,i,d[n+8],6,1873313359),f,r,d[n+15],10,-30611744),m,f,d[n+6],15,-1560198380),i,m,d[n+13],21,1309151649),r=md5_ii(r,i=md5_ii(i,m=md5_ii(m,f,r,i,d[n+4],6,-145523070),f,r,d[n+11],10,-1120210379),m,f,d[n+2],15,718787259),i,m,d[n+9],21,-343485551),m=safe_add(m,h),f=safe_add(f,t),r=safe_add(r,g),i=safe_add(i,e)}return Array(m,f,r,i)}function md5_cmn(d,_,m,f,r,i){return safe_add(bit_rol(safe_add(safe_add(_,d),safe_add(f,i)),r),m)}function md5_ff(d,_,m,f,r,i,n){return md5_cmn(_&m|~_&f,d,_,r,i,n)}function md5_gg(d,_,m,f,r,i,n){return md5_cmn(_&f|m&~f,d,_,r,i,n)}function md5_hh(d,_,m,f,r,i,n){return md5_cmn(_^m^f,d,_,r,i,n)}function md5_ii(d,_,m,f,r,i,n){return md5_cmn(m^(_|~f),d,_,r,i,n)}function safe_add(d,_){var m=(65535&d)+(65535&_);return(d>>16)+(_>>16)+(m>>16)<<16|65535&m}function bit_rol(d,_){return d<<_|d>>>32-_}

        BazHelpers.isEmpty = function(obj) {
            return isEmpty(obj);
        }

        BazHelpers.getKeyByValue = function(objectOrArray, value) {
            return getKeyByValue(objectOrArray, value);
        }

        BazHelpers.interval = function(func, intervalLength, options = { }) {
            return interval(func, intervalLength, options);
        }

        BazHelpers.findPath = function(obj, key, array) {
            return findPath(obj, key, array);
        }

        BazHelpers.fetchFromObject = function(obj, key) {
            return fetchFromObject(obj, key);
        }

        BazHelpers.setTimeoutTimers = (function() {
            return setTimeoutTimers();
        })();

        BazHelpers.getTimerId = function(identifier) {
            return getTimerId(identifier);
        }

        BazHelpers.ping = function(source, options, callback) {
            ping(source, options, callback);
        }

        BazHelpers.capitalizeFirstLetter = function(string) {
            return capitalizeFirstLetter(string);
        }

        BazHelpers.stringToBoolean = function(string) {
            return stringToBoolean(string);
        }
    }

    setup(bazHelpersConstructor);

    return bazHelpersConstructor;
}();
/* exported BazContentSection */
/* globals BazContentFields */
/*
* @title                    : BazContentSection
* @description              : Baz Core Lib
* @developer                : guru@bazaari.com.au
* @usage                    : BazContentSection._function_(_options_);
* @functions                : BazHeader, BazFooter, BazUpdateBreadcrumb
* @options                  :
*/

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
// var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var BazContentSection = function() {
    var BazContentSection = void 0;
    var dataCollection = window.dataCollection;
    var componentId, sectionId;

    // Error
    // function error(errorMsg) {
    //     throw new Error(errorMsg);
    // }

    //Header
    function init(options) {
        componentId = $('#' + options.sectionId).parents('.component')[0].id;
        sectionId = options.sectionId;

        if (!dataCollection[componentId]) {
            dataCollection[componentId] = { };
        }
        if (!dataCollection[componentId][sectionId]) {
            dataCollection[componentId][sectionId] = { };
        }
        BazContentFields.init({
            'componentId'   : componentId,
            'sectionId'     : sectionId
        });
    }

    function bazContentSection() {
        // if something needs to be constructed
        return null;
    }

    function setup(BazContentSectionConstructor) {
        BazContentSection = BazContentSectionConstructor;
        BazContentSection.defaults = {
            wizardId : null
        };
        BazContentSection.init = function(options) {
            init(_extends(BazContentSection.defaults, options));
        }
    }

    setup(bazContentSection);


    return bazContentSection;
}();
$(document).on('libsLoadComplete bazContentLoaderAjaxComplete bazContentLoaderModalComplete bazContentWizardAjaxComplete', function() {
    if ($('.section').length > 0) {
        $('.section').each(function() {
            BazContentSection.init({'sectionId' : $(this)[0].id});
        });
    }
});

/* globals define exports BazContentFieldsValidator BazContentFields paginatedPNotify Pace BazCore BazContentLoader */
/*
* @title                    : BazContentSectionWithForm
* @description              : Baz Lib for Content (Sections With Form)
* @developer                : guru@bazaari.com.au
* @usage                    : ('#'+ sectionId).BazContentSectionWithForm;
* @functions                :
* @options                  :
*/
(function (global, factory) {
    typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports) :
    typeof define === 'function' && define.amd ? define(['exports'], factory) :
    (global = global || self, factory(global.BazLibs = {}));
}(this, function (exports) {

    var BazContentSectionWithForm = function ($) {

        var NAME                    = 'BazContentSectionWithForm';
        var DATA_KEY                = 'baz.contentsectionwithform';
        // var EVENT_KEY               = "." + DATA_KEY;
        var JQUERY_NO_CONFLICT      = $.fn[NAME];
        // var Event = {
        // };
        // var ClassName = {
        // };
        // var Selector = {
        // };
        var Default = {
            task                    : null
        };
        var dataCollection,
            componentId,
            sectionId,
            extractComponentId,
            that,
            thatV,
            formJsTreeSelector;
        var types = { };
        var tabIds = [];
        var validationObject = { };

        var BazContentSectionWithForm = function () {
            function BazContentSectionWithForm(element, settings) {
                that = this;
                this._element = element;
                this._settings = settings;
            }

            var _proto = BazContentSectionWithForm.prototype;

            _proto._error = function _error(message) {
                throw new Error(message);
            };

            _proto._init = function _init(options) {
                componentId = $(this._element).parents('.component')[0].id;
                sectionId = $(this._element)[0].id;

                dataCollection = window['dataCollection'];

                if (!dataCollection[componentId]) {
                    dataCollection[componentId] = { };
                }
                if (!dataCollection[componentId][sectionId]) {
                    dataCollection[componentId][sectionId] = { };
                }

                if (!dataCollection[componentId][sectionId]['data']) {
                    dataCollection[componentId][sectionId]['data'] = { };
                }
                if (!dataCollection[componentId][sectionId]['dataToSubmit']) {
                    dataCollection[componentId][sectionId]['dataToSubmit'] = { };
                }

                if (options.task === 'validateForm') {
                    this._validateForm();
                } else if (options.task === 'sectionToObj') {
                    this._sectionToObj();
                } else {
                    validationObject = {
                        'componentId'   : componentId,
                        'sectionId'     : sectionId,
                        'on'            : 'section'
                    }

                    if ($('#' + sectionId + '-form-fields').length > 0) {
                        this._buildFormJsTree();
                        validationObject['formJsTreeSelector'] = formJsTreeSelector;
                    }

                    BazContentFieldsValidator.initValidator(validationObject);

                    BazContentFields.init({
                        'componentId'   : componentId,
                        'sectionId'     : sectionId
                    });
                    this._initSectionButtonsAndActions();
                }

                if ($('.btn-tool-reset-cache').length === 1) {
                    if (dataCollection.env.currentId == '0') {
                        $('.btn-tool-reset-cache').hide();
                    } else {
                        $('.btn-tool-reset-cache').addClass('contentAjaxLink');

                        var url = window.location.href;

                        if (!url.includes('resetcache')) {
                            $('.btn-tool-reset-cache').attr('href', url + '/q/resetcache/true');
                        } else {
                            $('.btn-tool-reset-cache').attr('href', url);
                        }

                        BazCore.bazContent();
                    }
                }
            };

            _proto._validateForm = function _validateForm() {
                validationObject = {
                    'componentId'     : componentId,
                    'sectionId'       : sectionId,
                    'onSuccess'       : false,
                    'type'            : 'section',
                    'preValidated'    : false,
                    'formId'          : null,
                    'on'              : 'section'
                }
                if ($('#' + sectionId + '-form-fields').length > 0) {
                    validationObject['formJsTreeSelector'] = formJsTreeSelector;
                }
                var validated = BazContentFieldsValidator.validateForm(validationObject);

                return validated;
            };

            _proto._redoFormJsTree = function _redoFormJsTree() {
                $(formJsTreeSelector).jstree('destroy');
                that._buildFormJsTree();
                BazContentFields.init({'fieldId':formJsTreeSelector[0].id});

                validationObject = {
                    'on'              : 'section',
                    'type'            : 'section',
                    'formLocation'    : sectionId
                }
                if ($('#' + sectionId + '-form-fields').length > 0) {
                    validationObject['formJsTreeSelector'] = formJsTreeSelector;
                }
                BazContentFieldsValidator.cancelValidatingForm(validationObject);//cancel any form validation as jstree has changed
            }

            _proto._buildFormJsTree = function _buildFormJsTree() {
                formJsTreeSelector = $('#' + sectionId + '-form-fields');

                $(formJsTreeSelector).empty();

                // Create Tree
                $(formJsTreeSelector).append('<ul></ul>');

                // Grab Icons from Controller
                types.item = {"icon" : "fas fa-fw fa-angle-right"};

                // Grab Fields from Tabs Note: attr "jstree-search" is used to populate tree
                $('#' + sectionId + '-form .nav-tabs li a').each(function() {
                    if ($(this).data('jstree') == false) {
                        return;
                    }

                    var tabId = $(this).attr('href').replace('#', '');
                    tabIds.push(tabId);
                    var tabName = $(this).html().toUpperCase();

                    types[tabId] = {"icon" : "fas fa-fw fa-chevron-right"};

                    $(formJsTreeSelector).
                        find('ul').
                        first().
                        append(
                            '<li data-tabid="' + tabId + '" data-jstree=' + '{"type":"' + tabId + '"} class="text-uppercase">' + tabName +
                            '<ul data-tabid="' + tabId + '-ul"></ul>' +
                            '</li>'
                        );

                    // $('#' + sectionId + '-form-fields li:contains("' + tabName + '")').first().append('<ul></ul>');

                    $('#' + tabId).find("[jstree-search]").each(function() {
                        var fieldId = this.id.replace('-jstreesearch', '');

                        if ($(this).attr('jstree-search') !== '') {
                            if (!$('#' + fieldId).parents('.form-group').hasClass('d-none') &&
                                $('#' + fieldId).attr('disabled') !== 'disabled'
                            ) {
                                    // $('#' + sectionId + '-form-fields li:contains("' + tabName + '")').
                                    // find('ul').
                                    // first().
                                    $('[data-tabid="' + tabId + '-ul"]').append(
                                        '<li data-tabid="' + tabId +
                                            '" data-jstreeid="' + $(this)[0].id +
                                            '" data-jstree=' + '{"type":' + '"item"}>' +
                                            $(this).attr('jstree-search') +
                                        '</li>'
                                );
                            }
                        }
                    });
                });

                dataCollection[componentId][sectionId][sectionId + '-form-fields'] =
                    $.extend(dataCollection[componentId][sectionId][sectionId + '-form-fields'], {
                        "types" : types,
                        "plugins": ["search","types"],
                        "search": {
                            'show_only_matches': true,
                            'search_callback'  : function(str, node) {
                                var word, words = [];
                                var searchFor = str.toUpperCase().replace(/^\s+/g, '').replace(/\s+$/g, '');
                                if (searchFor.indexOf(',') >= 0) {
                                    words = searchFor.split(',');
                                } else {
                                    words = [searchFor];
                                }
                                for (var i = 0; i < words.length; i++) {
                                    word = words[i];
                                    if ((node.text || "").indexOf(word) >= 0) {
                                        return true;
                                    }
                                }
                                return false;
                            }
                        }
                    });

                // Init jstree selection process
                $(formJsTreeSelector).on('select_node.jstree', function() {
                    var selfId = $(this).jstree('get_selected',true)[0];
                    // if (selfId.parent === '#') {
                        $(formJsTreeSelector).jstree('open_node', selfId);
                    // } else {
                        $(tabIds).each(function(index,tabId) {
                            var tab = $('#' + sectionId + '-form').find('[href="#' + tabId + '"]');

                            $(tab).removeClass('active');
                            $(tab).attr('area-selected', false);
                            $('#' + tabId).removeClass('active show');
                        })

                        var activateTab = $('#' + sectionId + '-form').find('[href="#' + selfId.data.tabid + '"]');
                        $(activateTab).addClass('active');
                        $(activateTab).attr('area-selected', true);

                        $('#' + selfId.data.tabid).addClass('active show');

                        $('#' + selfId.data.jstreeid).parent().addClass('bg-info disabled animated fadeIn');
                        setTimeout(function() {
                            $('#' + selfId.data.jstreeid).parent().removeClass('bg-info disabled animated fadeIn');
                        }, 2000);
                    // }
                });
            }

            _proto._initSectionButtonsAndActions = function _initSectionButtonsAndActions() {
                if ($('#' + sectionId + '-id').val() === '' || $('#' + sectionId + '-id').val() === '0') {
                    $('#' + sectionId + ' .card-footer button.addData').attr('hidden', false);
                    if ($('#' + sectionId + ' .card-footer button.addData').length === 1) {
                        $('#' + sectionId + ' .card-footer button.cancelForm').attr('hidden', false);
                    } else {
                        $('#' + sectionId + ' .card-footer button.closeForm').attr('hidden', false);
                    }
                } else if ('#' + sectionId + ' .card-footer button.updateData') {
                    $('#' + sectionId + ' .card-footer button.updateData').attr('hidden', false);
                    if ($('#' + sectionId + ' .card-footer button.updateData').length === 1) {
                        $('#' + sectionId + ' .card-footer button.cancelForm').attr('hidden', false);
                    } else {
                        $('#' + sectionId + ' .card-footer button.closeForm').attr('hidden', false);
                    }
                }

                $('#' + sectionId + ' .card-footer button.closeForm, #' + sectionId + ' .card-footer button.cancelForm').click(function(e) {
                    e.preventDefault();

                    if ($(this).is('.closeForm')) {
                        $('body').trigger('sectionWithFormCloseForm');
                    }
                    if ($(this).is('.cancelForm')) {
                        $('body').trigger('sectionWithFormCancelForm');
                    }
                });

                $('#' + sectionId + ' .card-footer button.addData, #' + sectionId + ' .card-footer button.updateData').click(function(e) {
                    e.preventDefault();

                    var mainButton = this;

                    if (that._validateForm()) {
                        if (dataCollection.env.devmode && dataCollection.env.devmode == '1') {
                            $(this).children('i').attr('hidden', true);
                            $(this).attr('disabled', false);
                        } else {
                            $(this).children('i').attr('hidden', false);
                            $(this).attr('disabled', true);
                        }
                        // if ($('.dz-clickable').length > 0) {
                        //     $('.dz-clickable').each(function(index, dzButton) {
                        //         var dzButtonId = $(dzButton)[0].id;
                        //         var extractedButtonId = dzButtonId.replace("-dropzone-upload", "");

                        //         if (dataCollection[componentId][sectionId][extractedButtonId]["dropzone"].files.length > 0) {
                        //             dataCollection[componentId][sectionId][extractedButtonId].save();

                        //             dataCollection[componentId][sectionId][extractedButtonId]["dropzone"].on("queuecomplete", function() {
                        //                 that._runAjax(mainButton, $(mainButton).attr('actionurl'), $.param(that._sectionToObj()));
                        //             });
                        //         } else {
                        //             that._runAjax(mainButton, $(mainButton).attr('actionurl'), $.param(that._sectionToObj()));
                        //         }
                        //     });
                        // } else {
                            var sectionToObj = that._sectionToObj();

                            if (dataCollection[componentId][sectionId][sectionId + '-form']['onSubmit']) {
                                if (dataCollection[componentId][sectionId][sectionId + '-form']['onSubmit']()) {
                                    that._runAjax(mainButton, $(mainButton).attr('actionurl'), $.param(sectionToObj));
                                } else {
                                    $(mainButton).attr('disabled', false);
                                    $(mainButton).children('i').attr('hidden', true);
                                }
                            } else {
                                that._runAjax(mainButton, $(mainButton).attr('actionurl'), $.param(sectionToObj));
                            }

                        // }
                    }
                });
            }

            _proto._runAjax = function _runAjax(thisButtonId, url, dataToSubmit) {
                $.ajax({
                    'url'           : url,
                    'data'          : dataToSubmit,
                    'method'        : 'post',
                    'dataType'      : 'json',
                    'success'       : function(response) {
                                        if (dataCollection[componentId][sectionId][sectionId + '-form']['onSubmitResponse']) {
                                            if (!dataCollection[componentId][sectionId][sectionId + '-form']['onSubmitResponse'](response)) {
                                                return;
                                            }
                                        }

                                        if (response.responseCode == '0') {
                                            if ($(thisButtonId).data('successnotify') === true) {
                                                paginatedPNotify('success', {
                                                    title   : response.responseMessage,
                                                });
                                            }

                                            if ($(thisButtonId).data('actiontarget') === 'mainContent') {
                                                BazContentLoader.loadAjax($(thisButtonId), {
                                                    ajaxBefore                      : function () {
                                                                                        Pace.restart();
                                                                                        $("#baz-content").empty();
                                                                                        $("#loader").attr('hidden', false);
                                                                                    },
                                                    ajaxFinished                    : function () {
                                                                                        BazCore.updateBreadcrumb();
                                                                                        $("#loader").attr('hidden', true);
                                                                                    },
                                                    ajaxError                       : function () {
                                                                                        $("#loader").attr('hidden', true);
                                                                                        BazCore.updateBreadcrumb();
                                                                                    }
                                                });
                                            } else if ($(thisButtonId).data('actiontarget') === 'cardBody') {
                                                $(thisButtonId).parent().siblings('.card-body').empty().append(
                                                    '<div class="overlay"><i class="fas fa-2x fa-sync-alt fa-spin"></i></div>'
                                                    );
                                                $(thisButtonId).parent().siblings('.card-body').load($(thisButtonId).attr('href'), response);
                                                $(thisButtonId).attr('disabled', false);
                                            } else if (!$(thisButtonId).data('actiontarget') || $(thisButtonId).data('actiontarget') === '') {
                                                $(thisButtonId).attr('disabled', false);
                                            }

                                            if ($(thisButtonId).is('.addData')) {
                                                $('body').trigger('sectionWithFormDataAdded');
                                            }
                                            if ($(thisButtonId).is('.updateData')) {
                                                $('body').trigger('sectionWithFormDataUpdated');
                                            }
                                            if (dataCollection[componentId] && dataCollection[componentId][sectionId][sectionId + '-form']['onSuccessResponse']) {
                                                dataCollection[componentId] && dataCollection[componentId][sectionId][sectionId + '-form']['onSuccessResponse'](response);
                                            }
                                        } else {
                                            $(thisButtonId).attr('disabled', false);
                                            paginatedPNotify('error', {
                                                title   : response.responseMessage
                                            });
                                            dataCollection[componentId][sectionId]['dataToSubmit'] = { };
                                            if (response.tokenKey && response.token) {
                                                $('#security-token').attr('name', response.tokenKey);
                                                $('#security-token').val(response.token);
                                            }

                                            if (dataCollection[componentId] && dataCollection[componentId][sectionId][sectionId + '-form']['onErrorResponse']) {
                                                dataCollection[componentId] && dataCollection[componentId][sectionId][sectionId + '-form']['onErrorResponse'](response);
                                            }
                                        }
                                        $(thisButtonId).children('i').attr('hidden', true);
                                    }
                });
            }

            _proto._sectionToObj = function _sectionToObj() {

                if (!dataCollection[componentId][sectionId]['data']) {
                    dataCollection[componentId][sectionId]['data'] = { };
                }
                if (!dataCollection[componentId][sectionId]['dataToSubmit']) {
                    dataCollection[componentId][sectionId]['dataToSubmit'] = { };
                }
                var stripComponentId;

                $('#' + sectionId).find('[data-bazscantype]').each(function(index,bazScanField) {
                    extractComponentId = $(bazScanField)[0].id.split('-');
                    extractComponentId = extractComponentId[extractComponentId.length - 1];
                    if (bazScanField.tagName !== 'FIELDSET' && $(bazScanField).parents('fieldset').data('bazscantype') !== 'datatable') {
                        if (bazScanField.tagName === 'INPUT' && bazScanField.type === 'checkbox') {
                            if ($(bazScanField).data('bazpostoncreate') === true ||
                                $(bazScanField).data('bazpostonupdate') === true ||
                                $(bazScanField).data('bazdevpost') === true) {
                                if ($(bazScanField)[0].checked === true) {
                                    dataCollection[componentId][sectionId]['data'][extractComponentId] = '1';
                                } else {
                                    dataCollection[componentId][sectionId]['data'][extractComponentId] = '0';
                                }
                            }
                        } else if (bazScanField.tagName === 'INPUT' || bazScanField.tagName === "TEXTAREA") {
                            if ($(bazScanField).data('bazpostoncreate') === true ||
                                $(bazScanField).data('bazpostonupdate') === true ||
                                $(bazScanField).data('bazdevpost') === true) {
                                if ($(bazScanField)[0].value === 'undefined') {//kill if incorrect Data
                                    that._error('data is undefined!');
                                    return;
                                } else {
                                    dataCollection[componentId][sectionId]['data'][extractComponentId] = $(bazScanField)[0].value;
                                }
                            }
                        } else if ($(bazScanField).data('bazscantype') === 'select2') {
                            thatV = bazScanField;
                            if ($(bazScanField).data('bazpostoncreate') === true ||
                                $(bazScanField).data('bazpostonupdate') === true ||
                                $(bazScanField).data('bazdevpost') === true) {

                                if ($(thatV)[0]['multiple'] || $(thatV).data('create') == true) {
                                    dataCollection[componentId][sectionId]['data'][extractComponentId] = { };
                                    dataCollection[componentId][sectionId]['data'][extractComponentId]['data'] = [];
                                    var select2Data = $(bazScanField).select2('data');
                                    var newTags = [];

                                    $(select2Data).each(function(i,v){
                                        if (v.newTag) {
                                            newTags.push(v.text);
                                        } else {
                                            var thisSelectId = v.id;
                                            var thisSelectName = v.text;

                                            if ($(thatV)[0]['multiple-object']) {
                                                var thisSelectObject = { };
                                                thisSelectObject[thisSelectId] = thisSelectName;
                                                dataCollection[componentId][sectionId]['data'][extractComponentId]['data'].push(thisSelectObject);
                                            } else {
                                                dataCollection[componentId][sectionId]['data'][extractComponentId]['data'].push(thisSelectId);
                                            }
                                        }
                                    });
                                    if (newTags.length > 0) {
                                        dataCollection[componentId][sectionId]['data'][extractComponentId]['newTags'] = newTags;
                                    }
                                } else {
                                    if ($(thatV).val() === '') {
                                        dataCollection[componentId][sectionId]['data'][extractComponentId] = 0;
                                    } else {
                                        dataCollection[componentId][sectionId]['data'][extractComponentId] = $(thatV).val();
                                    }
                                }
                            }
                        } else if ($(bazScanField).data('bazscantype') === 'radio' || $(bazScanField).data('bazscantype') === 'radio-button-group') {// icheck-radio
                            if ($(bazScanField).data('bazpostoncreate') === true ||
                                $(bazScanField).data('bazpostonupdate') === true ||
                                $(bazScanField).data('bazdevpost') === true) {
                                dataCollection[componentId][sectionId]['data'][extractComponentId] =
                                $(bazScanField).find('input[type=radio]:checked').data('value');
                            }
                        } else if ($(bazScanField).data('bazscantype') === 'trumbowyg') {//trumbowyg
                            if ($(bazScanField).data('bazpostoncreate') === true ||
                                $(bazScanField).data('bazpostonupdate') === true ||
                                $(bazScanField).data('bazdevpost') === true) {
                                dataCollection[componentId][sectionId]['data'][extractComponentId] = $(bazScanField).trumbowyg('html');
                            }
                        } else if ($(bazScanField).data('bazscantype') === 'counters') {//counters
                            thatV = bazScanField;
                            if ($(bazScanField).data('bazpostoncreate') === true ||
                                $(bazScanField).data('bazpostonupdate') === true ||
                                $(bazScanField).data('bazdevpost') === true) {
                                dataCollection[componentId][sectionId]['data'][extractComponentId] = { };
                                $(bazScanField).find('span').each(function(i,v) {
                                    var thisCounterId = $(v).parent('div')[0].id;
                                    dataCollection[componentId][sectionId]['data'][extractComponentId][thisCounterId] = $(v).html();
                                });
                            }
                        } else if ($(bazScanField).data('bazscantype') === 'html') {//html
                            thatV = bazScanField;
                            if ($(bazScanField).data('bazpostoncreate') === true ||
                                $(bazScanField).data('bazpostonupdate') === true ||
                                $(bazScanField).data('bazdevpost') === true) {
                                if (!dataCollection[componentId][sectionId]['data'][extractComponentId]) {
                                    dataCollection[componentId][sectionId]['data'][extractComponentId] = $(bazScanField).html();
                                }
                            }
                        } else if ($(bazScanField).data('bazscantype') === 'jstree') {//jstree, only object is created, data is generated in the html file.
                            thatV = bazScanField;
                            if ($(bazScanField).data('bazpostoncreate') === true ||
                                $(bazScanField).data('bazpostonupdate') === true ||
                                $(bazScanField).data('bazdevpost') === true) {
                                if (!dataCollection[componentId][sectionId]['data'][extractComponentId]) {
                                    dataCollection[componentId][sectionId]['data'][extractComponentId] = { };
                                }
                            }
                        }

                        if (dataCollection[componentId][sectionId][$(bazScanField)[0].id] &&
                            dataCollection[componentId][sectionId][$(bazScanField)[0].id].afterExtract) {
                            dataCollection[componentId][sectionId][$(bazScanField)[0].id].afterExtract();
                        }
                    }
                });
                // Add tables data to dataCollection
                // for (var section in tableData) {
                //  for (var data in tableData[section]) {
                //      var excludeActions = false;
                //      var excludeSeqAndSort = false;
                //      var currentTableDataLength = 0;
                //      if ((sectionsOptions[data].bazdatatable.rowButtons.canDelete === true) || (sectionsOptions[data].bazdatatable.rowButtons.canEdit === true)) {
                //          excludeActions = true;
                //      }
                //      if (sectionsOptions[data].datatable.rowReorder === true) {
                //          excludeSeqAndSort = true;
                //      }
                //      dataCollection[componentId][section][data] = [];
                //      $.each(tableData[section][data].rows().data(), function(i,v) {
                //          var startAt = 0;
                //          if (excludeSeqAndSort && excludeActions) {
                //              currentTableDataLength = v.length - 3;
                //              startAt = 2;
                //          } else if (!excludeSeqAndSort && excludeActions) {
                //              currentTableDataLength = v.length - 1;
                //          } else if (excludeSeqAndSort && !excludeActions) {
                //              currentTableDataLength = v.length - 2;
                //              startAt = 2;
                //          }
                //          var thatI = i;
                //          dataCollection[componentId][section][data][i] = { };
                //          for (var j = 0; j < currentTableDataLength; j++) {
                //              var columnData;
                //              var columnDataHasId = v[startAt].match(/id="(.*?)"/g)
                //              if (columnDataHasId) {
                //                  columnData = (columnDataHasId.toString().match(/"(.*?)"/g)).toString().replace(/"/g, '');
                //              } else {
                //                  columnData = v[startAt];
                //              }
                //              dataCollection[componentId][section][data][thatI][dataTableFields[componentId][section][data][startAt]] = columnData;
                //              startAt++;
                //          }
                //      });
                // }
                if (dataCollection[componentId][sectionId].data.id === '' || !dataCollection[componentId][sectionId].data.id) {//Create
                    var dataToSubmit;

                    $('#' + sectionId).find('[data-bazpostoncreate=true]').each(function() {
                        stripComponentId = $(this)[0].id.split('-');
                        stripComponentId = stripComponentId[stripComponentId.length - 1];
                        if (typeof dataCollection[componentId][sectionId].data[stripComponentId] === 'object' ||
                            $.isArray(dataCollection[componentId][sectionId].data[stripComponentId])
                        ) {
                            dataToSubmit = JSON.stringify(dataCollection[componentId][sectionId].data[stripComponentId]);
                        } else {
                            dataToSubmit = dataCollection[componentId][sectionId].data[stripComponentId];
                        }
                        dataCollection[componentId][sectionId]['dataToSubmit'][stripComponentId] = dataToSubmit;
                    });
                } else {//Edit
                    $('#' + sectionId).find('[data-bazpostonupdate=true]').each(function() {
                        stripComponentId = $(this)[0].id.split('-');
                        stripComponentId = stripComponentId[stripComponentId.length - 1];
                        if (typeof dataCollection[componentId][sectionId].data[stripComponentId] === 'object' ||
                            $.isArray(dataCollection[componentId][sectionId].data[stripComponentId])
                        ) {
                            dataToSubmit = JSON.stringify(dataCollection[componentId][sectionId].data[stripComponentId]);
                        } else {
                            dataToSubmit = dataCollection[componentId][sectionId].data[stripComponentId];
                        }
                        dataCollection[componentId][sectionId]['dataToSubmit'][stripComponentId] = dataToSubmit;
                    });
                }
                dataCollection[componentId][sectionId]['dataToSubmit'][$('#security-token').attr('name')] = $('#security-token').val();

                return dataCollection[componentId][sectionId]['dataToSubmit'];
            };

            BazContentSectionWithForm._jQueryInterface = function _jQueryInterface(options) {
                dataCollection = window['dataCollection'];
                componentId = $(this).parents('.component')[0].id;
                sectionId = $(this)[0].id;
                dataCollection[componentId][sectionId]['BazContentSectionWithForm'] = $(this).data(DATA_KEY);
                options = $.extend({}, Default, options);

                if (!dataCollection[componentId][sectionId]['BazContentSectionWithForm']) {
                    dataCollection[componentId][sectionId]['BazContentSectionWithForm'] = new BazContentSectionWithForm($(this), options);
                    $(this).data(DATA_KEY, typeof options === 'string' ? 'options need to be an object and not string' : options);
                    dataCollection[componentId][sectionId]['BazContentSectionWithForm']._init(options);
                } else {
                    delete dataCollection[componentId][sectionId]['BazContentSectionWithForm'];
                    dataCollection[componentId][sectionId]['BazContentSectionWithForm'] = new BazContentSectionWithForm($(this), options);
                    $(this).data(DATA_KEY, typeof options === 'string' ? 'options need to be an object and not string' : options);
                    dataCollection[componentId][sectionId]['BazContentSectionWithForm']._init(options);
                }
            };

        return BazContentSectionWithForm;

        }();

    $(document).on('libsLoadComplete bazContentLoaderAjaxComplete bazContentLoaderModalComplete bazContentWizardAjaxComplete', function() {
        $('body').find('.sectionWithForm').each(function() {
            // if ($(this).data('bazdevmodetools') === 'false' ||
            //     $(this).data('bazdevmodetools') === false) {
                BazContentSectionWithForm._jQueryInterface.call($(this));
            // }
        });
    });

    $.fn[NAME] = BazContentSectionWithForm._jQueryInterface;
    $.fn[NAME].Constructor = BazContentSectionWithForm;

    $.fn[NAME].noConflict = function () {
        $.fn[NAME] = JQUERY_NO_CONFLICT;
        return BazContentSectionWithForm._jQueryInterface;
    };

    return BazContentSectionWithForm;
}(jQuery);

exports.BazContentSectionWithForm = BazContentSectionWithForm;

Object.defineProperty(exports, '__esModule', { value: true });

}));
/* globals define exports BazContentFieldsValidator BazContentFields PNotify */
/*
* @title                    : BazContentSectionWithFormToDatatable
* @description              : Baz Lib for Content (Sections With Form)
* @developer                : guru@bazaari.com.au
* @usage                    : ('#'+ sectionId).BazContentSectionWithFormToDatatable;
* @functions                :
* @options                  :
*/
(function (global, factory) {
    typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports) :
    typeof define === 'function' && define.amd ? define(['exports'], factory) :
    (global = global || self, factory(global.BazLibs = {}));
}(this, function (exports) {

    var BazContentSectionWithFormToDatatable = function ($) {

        var NAME                    = 'BazContentSectionWithFormToDatatable';
        var DATA_KEY                = 'baz.contentsectionwithform';
        // var EVENT_KEY               = "." + DATA_KEY;
        var JQUERY_NO_CONFLICT      = $.fn[NAME];
        // var Event = {
        // };
        // var ClassName = {
        // };
        // var Selector = {
        // };
        var Default = {
            task                    : null
        };
        var dataCollection,
            componentId,
            sectionId,
            that,
            sectionOptions,
            extractDatatableFieldsData,
            multiTable,
            selectedTable,
            dataTableFields,
            pnotifySound;

        var BazContentSectionWithFormToDatatable = function () {
            function BazContentSectionWithFormToDatatable(element, settings) {
                that = this;
                that._element = element;
                that._settings = settings;
            }

            var _proto = BazContentSectionWithFormToDatatable.prototype;

            _proto._error = function _error(message) {
                throw new Error(message);
            };

            _proto._init = function _init(options) {
                pnotifySound = dataCollection.env.sounds.pnotifySound;
                componentId = $(this._element).parents('.component')[0].id;
                sectionId = $(this._element)[0].id;

                dataTableFields = { };
                dataTableFields[componentId] = { };
                dataTableFields[componentId][sectionId] = { };

                sectionOptions = dataCollection[componentId][sectionId];
                sectionOptions['datatables'] = { };

                dataCollection = window['dataCollection'];

                if (!dataCollection[componentId]) {
                    dataCollection[componentId] = { };
                }
                if (!dataCollection[componentId][sectionId]) {
                    dataCollection[componentId][sectionId] = { };
                }

                if (options.task === 'tableDataToObj') {
                    this._tableDataToObj();
                } else {
                    BazContentFields.init({
                        'componentId'   : componentId,
                        'sectionId'     : sectionId
                    });

                    BazContentFieldsValidator.initValidator({
                        'componentId'   : componentId,
                        'sectionId'     : sectionId,
                        'on'            : 'section'
                    });
                    this._fieldsToDatatable(sectionId);
                }

                // $('body').on('dataArrToTableData', function() {
                //     that._dataArrToTableData();
                // });
            }

            _proto._validateForm = function (onSuccess, type, preValidated, formId) {

                var validated = BazContentFieldsValidator.validateForm({
                    'componentId'     : componentId,
                    'sectionId'       : sectionId,
                    'onSuccess'       : onSuccess,
                    'type'            : type,
                    'preValidated'    : preValidated,
                    'formId'          : formId,
                    'on'              : 'section'
                });

                return validated;
            };

            //Fields to Datatable
            _proto._fieldsToDatatable = function(fieldsetDatatable) {
                var addSeq = [];

                var columnDefsObj =
                    [
                        { 'targets': 0, 'visible': false },
                        { 'orderable': true, 'className': 'reorder', 'targets': 0 },
                        { 'orderable': false, 'targets': '_all' }
                    ];

                if (sectionOptions[fieldsetDatatable + '-datatables']) {
                    $.each(sectionOptions[fieldsetDatatable + '-datatables'], function(i,v) {
                        if (sectionOptions[v].datatable.rowReorder) {
                            if (!sectionOptions[v].datatable.columnDefs) {
                                sectionOptions[v].datatable.columnDefs = [];
                                sectionOptions[v].datatable.columnDefs = columnDefsObj;
                            } else {
                                sectionOptions[v].datatable.columnDefs = $.merge(columnDefsObj, sectionOptions[v].datatable.columnDefs);
                            }
                            addSeq.push('true');
                        }
                    });
                } else {
                    that._error('Datatable Parameters missing for datatable - ' + fieldsetDatatable);
                }

                if ($.inArray('true', addSeq) !== -1) {
                    $('#' + fieldsetDatatable + '-fields').prepend(
                        '<div class="row margin-top-10 d-none">' +
                        '   <div class="col-md-12">' +
                        '       <label>SEQ</label>' +
                        '       <div data-bazScan="true" data-bazScanType="seq" id="' + fieldsetDatatable + '-seq"></div>' +
                        '   </div>' +
                        '</div>' +
                        '<div class="row margin-top-10 d-none">' +
                        '   <div class="col-md-12">' +
                        '       <label>SORT</label>' +
                        '       <div data-bazScan="true" data-bazScanType="html" id="' + fieldsetDatatable + '-sort"><i class="fa fa-sort"></i></div>' +
                        '   </div>' +
                        '</div>'
                    );
                }

                $(sectionOptions[fieldsetDatatable + '-datatables']).each(function(i,v) {
                    // Generate table th
                    var extractDatatableFieldsLabel = that._extractDatatableFieldsLabel(fieldsetDatatable, v);
                    var labels = [];
                    for (var label in extractDatatableFieldsLabel) {
                        labels.push('<th class="pb-1 pt-1">' + extractDatatableFieldsLabel[label].labelName + '</th>');
                    }
                    labels = labels.join('');
                    $('#' + v).append(
                        '<div class="margin-bottom-10" id="' + v + '-div">' +
                        '<label class="text-uppercase">' + sectionOptions[v].tableTitle + '</label> '+
                        ' <i data-toggle="tooltip" data-placement="right" title="' + sectionOptions[v].tableTitle + ' table" class="fa fa-question-circle fa-1 helper"></i>' +
                        '<table id="' + v + '-data" class="table table-striped dt-responsive compact" style="margin:0 !important;"' +
                        ' width="100%" cellspacing="0"><thead>' +
                        labels + '</thead><tbody></tbody></table></div>'
                        );
                    //Init Datatable
                    sectionOptions['datatables'][v] = $('#' + v + '-data').DataTable(sectionOptions[v].datatable);
                    if (sectionOptions[v].datatable.rowReorder) {
                        // If rowReorder enabled
                        sectionOptions['datatables'][v].on('row-reorder', function() {
                            that._rowReorderRedoSeq(sectionOptions['datatables'][v], v);
                            // that._rowReorderDatatableDataToObject(details, sectionId, fieldsetDatatable, v);
                            sectionOptions['datatables'][v].draw();
                        });
                    }
                });

                //Assign button click
                $('#' + fieldsetDatatable + '-assign-button').click(function(e) {
                    e.preventDefault();

                    var datatable;

                    if ($(sectionOptions[fieldsetDatatable + '-datatables']).length > 1) {
                        datatable = $('#' + sectionOptions[fieldsetDatatable].dataTableSelector.id)[0].value;
                    } else {
                        datatable = sectionOptions[fieldsetDatatable + '-datatables'][0];
                    }

                    $('#' + fieldsetDatatable + '-fields').siblings().find('.has-error').removeClass('has-error has-feedback');//remove previous validation
                    $('#' + fieldsetDatatable + '-fields').siblings().find('table').removeClass('border-danger').addClass('border-default');//remove previous validation
                    $('#' + fieldsetDatatable + '-fields').siblings().find('.help-block').remove();//remove previous validation

                    //Execute preExtraction script passed from the html(js script)
                    if (sectionOptions[datatable].preExtraction) {
                        sectionOptions[datatable].preExtraction(sectionOptions['datatables'][datatable]);
                    }

                    extractDatatableFieldsData = that._extractDatatableFieldsData(fieldsetDatatable, datatable, false);

                    //Execute postExtraction script passed from the html(js script)
                    if (sectionOptions[datatable].postExtraction) {
                        sectionOptions[datatable].postExtraction(sectionOptions['datatables'][datatable], extractDatatableFieldsData);
                    }

                    var validated = that._validateForm(false, 'section', false, fieldsetDatatable + '-form');

                    if (validated) {

                        var rowAdded =
                            that._addExtractFieldsToDatatable(
                                null,
                                extractDatatableFieldsData,
                                fieldsetDatatable,
                                datatable,
                                false,
                                false
                            );

                        if (rowAdded) {
                            $('#' + fieldsetDatatable).find('.jstreevalidate').val('');

                            // that._validateForm(false, 'sections', true, null);

                            sectionOptions['datatables'][datatable].responsive.recalc();

                            that._registerDatatableButtons(
                                sectionOptions['datatables'][datatable],
                                $('#' + datatable + '-div'),
                                datatable,
                                sectionId,
                                fieldsetDatatable
                            );

                            // var table = sectionOptions['datatables'][datatable];

                            sectionOptions['datatables'][datatable].on('responsive-display', function (showHide) {
                                if (showHide) {
                                    that._registerDatatableButtons(
                                        sectionOptions['datatables'][datatable],
                                        $('#' + datatable + '-div'),
                                        datatable,
                                        sectionId,
                                        fieldsetDatatable
                                    );
                                }
                            });

                            //Execute postSuccess script passed from the html(js script)
                            if (sectionOptions[datatable].postSuccess) {
                                sectionOptions[datatable].postSuccess(sectionOptions['datatables'][datatable], extractDatatableFieldsData);
                            }

                            that._clearDatatableFormData(datatable, fieldsetDatatable);

                            $('body').trigger(
                                {
                                    'type'     :'formToDatatableTableAssignClicked',
                                    'rowsCount': sectionOptions['datatables'][datatable].rows().count()
                                }
                            );
                        }
                    }
                });
            };

            //Extract Fields Datatable data
            _proto._extractDatatableFieldsLabel = function(fieldsetDatatable, datatable) {
                var extractedLabel = null;
                extractedLabel = { };
                var counter = 0;
                dataTableFields[componentId][sectionId][datatable] = [];
                $('#' + fieldsetDatatable + '-fieldset').find('[data-bazscantype]').each(function(i,v) {
                    if ($(v).data('bazscantype')) {
                        if (!($(v).data('bazscantype') === 'tableSelector' || $(v).data('bazscantype') === 'dropzone')) {
                            extractedLabel[counter] = { };
                            if ($(v).data('bazscantype') === 'jstree') {//jstree
                                extractedLabel[counter].labelName = $(v).parents('.form-group').children('label').text();
                            } else if ($(v).data('bazscantype') === 'radio') {// icheck-radio
                                extractedLabel[counter].labelName = $(v).children('label').text();
                            } else if ($(v).data('bazscantype') === 'checkbox') {// icheck-checkbox
                                if ($(v).siblings('label').text() === '') {
                                    extractedLabel[counter].labelName = $(v).parents('.form-group').children('label').text();
                                } else {
                                    extractedLabel[counter].labelName = $(v).siblings('label').text();
                                }
                            } else {
                                extractedLabel[counter].labelName = $(v).siblings('label').text();
                            }
                            dataTableFields[componentId][sectionId][datatable].push($(v)[0].id);
                        }
                    }
                    counter++;
                });
                //Add buttons
                if (sectionOptions[datatable].bazdatatable && sectionOptions[datatable].bazdatatable.rowButtons) {
                    extractedLabel[counter] = { };
                    extractedLabel[counter].labelName = 'ACTIONS';
                    dataTableFields[componentId][sectionId][datatable].push(fieldsetDatatable + '-actions');
                }
                return extractedLabel;
            };

            //Extract Fields Datatable data
            _proto._extractDatatableFieldsData = function(fieldsetDatatable, datatable, isEdit) {
                var extractedFieldsData = null;
                var extractedJstreeData = null;
                var finalExtractedData = null;
                extractedFieldsData = { };
                extractedJstreeData = { };
                finalExtractedData = { };
                var counter = 0;

                $('#' + fieldsetDatatable + '-fieldset').find('[data-bazscantype]').each(function(i,v) {
                    extractedFieldsData[counter] = { };
                    extractedFieldsData[counter].id = v.id;
                    if ($(v).data('bazscantype') && !$(v).attr('disabled')) {
                        if (!($(v).data('bazscantype') === 'tableSelector' || $(v).data('bazscantype') === 'dropzone')) {
                            // extractedFieldsData[counter].data = $('#' + v.id); //Enable if you need all data

                            if (v.tagName === 'INPUT' && v.type === 'checkbox') {
                                if ($(v)[0].checked === true) {
                                    extractedFieldsData[counter].extractedData = 'YES';
                                } else {
                                    extractedFieldsData[counter].extractedData = 'NO';
                                }
                            } else if (v.tagName === 'INPUT' || v.tagName === "TEXTAREA") {
                                if ($(v)[0].value === 'undefined') {//kill if incorrect Data
                                    that._error('data is undefined!');
                                    return;
                                } else {
                                    extractedFieldsData[counter].extractedData = $(v)[0].value;
                                }
                            }
                            if ($(v).data('bazscantype') === 'select2') {
                                extractedFieldsData[counter].extractedData = null;
                                $($(v)[0].selectedOptions).each(function(i,v){
                                    if (!extractedFieldsData[counter].extractedData) {
                                        extractedFieldsData[counter].extractedData = '<span class="' + $(v)[0].value + '">' + $(v)[0].text + '</span><br>';
                                    } else {
                                        extractedFieldsData[counter].extractedData = extractedFieldsData[counter].extractedData + '<span class="' + $(v)[0].value + '">' + $(v)[0].text + '</span><br>';
                                    }
                                });
                            }
                            if ($(v).data('bazscantype') === 'jstree') {//jstree
                                var treeData = that._getJsTreeSelectedNodePath(fieldsetDatatable + '-form', $(v));
                                extractedJstreeData[counter] = { };
                                for (i = 0; i < Object.keys(treeData).length; i++) {
                                    extractedJstreeData[counter][i] = { };
                                    extractedJstreeData[counter][i].id = v.id;
                                    extractedJstreeData[counter][i].extractedData = '<span class="' + treeData[i].id + '" data-jstreeId="' + treeData[i].jstreeId + '">' + treeData[i].path + '</span><br>';
                                    extractedJstreeData[counter][i].absolutePath = treeData[i].path;
                                    extractedJstreeData[counter][i].nodeName = treeData[i].nodeName;
                                }
                            }
                            if ($(v).data('bazscantype') === 'radio') {// icheck-radio
                                extractedFieldsData[counter].extractedData = $(v).find('input:checked').siblings('label').text();
                            }
                            if ($(v).data('bazscantype') === 'trumbowyg') {//trumbowyg
                                extractedFieldsData[counter].extractedData = $(v).trumbowyg('html')
                            }
                            if ($(v).data('bazscantype') === 'html') {//HTML (as-is data)
                                extractedFieldsData[counter].extractedData = $(v).html();
                            }
                            if ($(v).data('bazscantype') === 'seq') {//sequence
                                extractedFieldsData[counter].extractedData = $(v).html();
                            }
                        }

                        if ($(v).data('bazscantype') === 'tableSelector') {
                            selectedTable = $(v).val();
                            multiTable = true;
                        }
                    } else {
                        if ($(v).data().disabledtxt) {
                            extractedFieldsData[counter].extractedData = $(v).data().disabledtxt;
                        } else {
                            extractedFieldsData[counter].extractedData = '-';
                        }
                    }
                    counter++;
                });

                var rowId = 0;

                if (isEdit && multiTable) {
                    datatable = selectedTable;
                }

                if (datatable) {
                    if (sectionOptions['datatables'][datatable].row().count() >= 0) {
                        rowId = sectionOptions['datatables'][datatable].row().count() + 1;
                    }

                    if (Object.keys(extractedJstreeData).length > 0) {
                        for (var jstreesData in extractedJstreeData) {
                            for (var jstreeData in extractedJstreeData[jstreesData]) {
                                finalExtractedData[jstreeData] = { };
                                for (var fieldsData in extractedFieldsData) {
                                    finalExtractedData[jstreeData][fieldsData] = extractedFieldsData[fieldsData];
                                    if (fieldsData === jstreesData) {
                                        finalExtractedData[jstreeData][fieldsData] = extractedJstreeData[jstreesData][jstreeData];
                                    }
                                    if (sectionOptions[datatable].bazdatatable.rowButtons) {
                                        //Add Action Buttons
                                        finalExtractedData[jstreeData][counter] = { };
                                        finalExtractedData[jstreeData][counter].extractedData = rowId;
                                        if (sectionOptions[datatable].bazdatatable.rowButtons.canDelete && !sectionOptions[datatable].bazdatatable.rowButtons.canEdit) {
                                            finalExtractedData[0][counter].id = fieldsetDatatable + '-actions';
                                            finalExtractedData[jstreeData][counter].extractedData =
                                                '<button data-row-id="' + rowId +
                                                '" type="button" class="btn btn-xs btn-danger float-right ml-1 tableDeleteButton"><i class="fa fas fa-fw text-xs fa-trash"></i></button>';
                                        } else if (!sectionOptions[datatable].bazdatatable.rowButtons.canDelete && sectionOptions[datatable].bazdatatable.rowButtons.canEdit) {
                                            finalExtractedData[0][counter].id = fieldsetDatatable + '-actions';
                                            finalExtractedData[jstreeData][counter].extractedData = '<button data-row-id="' + rowId + '" type="button" class="btn btn-xs btn-primary float-right tableEditButton"><i class="fa fas fa-fw text-xs fa-edit"></i></button>';
                                        } else if (sectionOptions[datatable].bazdatatable.rowButtons.canDelete && sectionOptions[datatable].bazdatatable.rowButtons.canEdit) {
                                            finalExtractedData[0][counter].id = fieldsetDatatable + '-actions';
                                            finalExtractedData[jstreeData][counter].extractedData = '<button data-row-id="' + rowId + '" type="button" class="btn btn-xs btn-danger float-right ml-1 tableDeleteButton"><i class="fa fas fa-fw text-xs fa-trash"></i></button>' +
                                                                                    '<button data-row-id="' + rowId + '" type="button" class="btn btn-xs btn-primary float-right tableEditButton"><i class="fa fas fa-fw text-xs fa-edit"></i></button>';
                                        }
                                    }
                                }
                                rowId++;
                            }
                        }
                    } else {//No JS Tree data extraction
                        finalExtractedData[0] = { };
                        for (var noJstree in extractedFieldsData) {
                            finalExtractedData[0][noJstree] = extractedFieldsData[noJstree];
                            if (sectionOptions[datatable].bazdatatable && sectionOptions[datatable].bazdatatable.rowButtons) {
                                //Add Action Buttons
                                finalExtractedData[0][counter] = { };
                                finalExtractedData[0][counter].extractedData = rowId;
                                if (sectionOptions[datatable].bazdatatable.rowButtons.canDelete && !sectionOptions[datatable].bazdatatable.rowButtons.canEdit) {
                                    finalExtractedData[0][counter].id = fieldsetDatatable + '-actions';
                                    finalExtractedData[0][counter].extractedData =
                                    '<button data-row-id="' + rowId + '" type="button" class="btn btn-xs btn-danger float-right ml-1 tableDeleteButton"><i class="fa fas fa-fw text-xs fa-trash"></i></button>';
                                } else if (!sectionOptions[datatable].bazdatatable.rowButtons.canDelete && sectionOptions[datatable].bazdatatable.rowButtons.canEdit) {
                                    finalExtractedData[0][counter].id = fieldsetDatatable + '-actions';
                                    finalExtractedData[0][counter].extractedData =
                                    '<button data-row-id="' + rowId + '" type="button" class="btn btn-xs btn-primary float-right tableEditButton"><i class="fa fas fa-fw text-xs fa-edit"></i></button>';
                                } else if (sectionOptions[datatable].bazdatatable.rowButtons.canDelete && sectionOptions[datatable].bazdatatable.rowButtons.canEdit) {
                                    finalExtractedData[0][counter].id = fieldsetDatatable + '-actions';
                                    finalExtractedData[0][counter].extractedData =
                                    '<button data-row-id="' + rowId + '" type="button" class="btn btn-xs btn-danger float-right ml-1 tableDeleteButton"><i class="fa fas fa-fw text-xs fa-trash"></i></button>' +
                                                                            '<button data-row-id="' + rowId + '" type="button" class="btn btn-xs btn-primary float-right tableEditButton"><i class="fa fas fa-fw text-xs fa-edit"></i></button>';
                                }
                            }
                        }
                        rowId++;
                    }
                    return finalExtractedData;
                } else {
                    return false;
                }
            };

            //Add extracted fields data to datatable
            _proto._addExtractFieldsToDatatable = function(rowIndex, extractDatatableFieldsData, fieldsetDatatable, datatable, isEdit, isImport) {
                var migrateData = false;
                var oldDataTable;
                // Need to convert to array to add to datatable to merge them later to object and add values to datatable
                var rowExtractedId = [];
                var rowExtractedData = [];
                var found = false;

                if (isEdit && multiTable) {
                    if (datatable !== selectedTable ){
                        migrateData = true;
                    }
                    oldDataTable = datatable;
                    datatable = selectedTable;
                }

                if (!isEdit && sectionOptions[datatable].datatable.rowReorder) {
                    var seq = sectionOptions['datatables'][datatable].rows().count();
                    if (seq === 0) {
                        seq = 1;
                    } else {
                        seq = seq + 1;
                    }
                    for (var dataRows in extractDatatableFieldsData) {
                        var oldId = extractDatatableFieldsData[dataRows][0].id;
                        extractDatatableFieldsData[dataRows][0] = { };
                        extractDatatableFieldsData[dataRows][0].id = oldId;
                        extractDatatableFieldsData[dataRows][0].extractedData = seq;
                        seq++;
                    }
                }

                for (var rows in extractDatatableFieldsData) {
                    rowExtractedData[rows] = [];
                    rowExtractedId[rows] = [];
                    for (var row in extractDatatableFieldsData[rows]) {
                        rowExtractedData[rows].push(extractDatatableFieldsData[rows][row].extractedData);//to datatable
                        rowExtractedId[rows].push(extractDatatableFieldsData[rows][row].id);
                    }
                }

                if (sectionOptions[datatable].bazdatatable && sectionOptions[datatable].bazdatatable.compareData) {
                    if (sectionOptions['datatables'][datatable].rows().count() > 0) {

                        $('#' + datatable).children().find('tbody tr').removeClass('animated fadeIn bg-warning');
                        $('#' + datatable).children().find('tbody tr').children().removeClass('animated fadeIn bg-warning');

                        found =
                            that._compareData(
                                sectionOptions[datatable].bazdatatable.compareData,
                                extractDatatableFieldsData,
                                sectionOptions['datatables'][datatable].rows().data(),
                                rowIndex,
                                datatable
                            );
                    }
                }

                if (found) {
                    PNotify.notice({
                        title: 'Input data already exists in table!'
                    });

                    pnotifySound.play();

                    return false;
                } else {
                    if (rowIndex !== null) {//rowIndex is from editDatatableRow
                        if (!migrateData) {
                            $(rowExtractedData).each(function(i,v) {
                                sectionOptions['datatables'][datatable].row(rowIndex).data(v).draw();
                            });
                        } else {
                            sectionOptions['datatable'][oldDataTable].row(rowIndex).remove().draw();
                            $(rowExtractedData).each(function(i,v) {
                                var drawnRow = sectionOptions['datatables'][datatable].row.add(v).draw().node();
                                $(drawnRow).children('td').addClass('pb-1 pt-1');
                            });
                            // that._deleteDatatableDataFromObject(rowIndex, fieldsetDatatable, sectionId, oldDataTable);
                            if (!isImport) {
                                that._tableDataToObj();
                            }

                            that._registerDatatableButtons(
                               sectionOptions['datatable'][oldDataTable],
                               $('#' + datatable + '-div'),
                               datatable,
                               sectionId,
                               fieldsetDatatable
                            );
                        }
                        that._registerDatatableButtons(
                           sectionOptions['datatables'][datatable],
                           $('#' + datatable + '-div'),
                           datatable,
                           sectionId,
                           fieldsetDatatable
                        );
                        rowIndex = null;
                    } else {//add new row
                        $(rowExtractedData).each(function(i,v) {
                            var drawnRow = sectionOptions['datatables'][datatable].row.add(v).draw().node();
                            $(drawnRow).children('td').addClass('pb-1 pt-1');
                        });
                        that._registerDatatableButtons(
                           sectionOptions['datatables'][datatable],
                           $('#' + datatable + '-div'),
                           datatable,
                           sectionId,
                           fieldsetDatatable
                        );
                    }
                    //Add data to object
                    // this._addEditDatatableDataToObject(rowIndex, rowExtractedId, rowExtractedData, fieldsetDatatable, sectionId, datatable);
                    if (!isImport) {
                        that._tableDataToObj();
                    }

                    $('body').trigger(
                        {
                            'type'     :'formToDatatableTableUpdated',
                            'rowsCount': sectionOptions['datatables'][datatable].rows().count()
                        }
                    );

                    return true;
                }
            };

            //Edit table Row
            _proto._editDatatableRow = function(fieldsetDatatable, rowIndex, rowData, datatable) {
                var fieldsetFields = [];
                if ($(sectionOptions[fieldsetDatatable + '-datatables']).length > 1) {
                    $('#' + fieldsetDatatable + '-fieldset').find('[data-bazscantype]').each(function(i,v) {
                        if (!($(v).data('bazscantype') === 'tableSelector' || $(v).data('bazscantype') === 'dropzone')) {
                            fieldsetFields.push($(v));
                        }
                    });
                    $('#' + fieldsetDatatable + '-fieldset').find('[data-bazscantype]').each(function(i,v) {// Selector is always in the end.
                        if (($(v).data('bazscantype') === 'tableSelector')) {
                            fieldsetFields.push($(v));
                        }
                    });
                } else {
                    fieldsetFields = $('#' + fieldsetDatatable + '-fieldset').find('[data-bazscantype]');
                }

                $(fieldsetFields).each(function(i,v) {
                    if ($(v).data('bazscantype')) {
                        if ($(v).data('bazscantype') === 'seq') {
                            $(v).html(rowData[i]);
                        } else if ($(v).data('bazscantype') !== 'html') {
                            if (v.tagName === 'INPUT' && v.type === 'checkbox') {
                                if (rowData[i] === 'YES') {
                                    $(v).prop('checked', true);
                                } else if (rowData[i] === 'NO') {
                                    $(v).prop('checked', false);
                                }
                            } else if (v.tagName === 'INPUT' || v.tagName === 'TEXTAREA') {
                                $(v).val(rowData[i]);
                            }
                            if (v.tagName === "SELECT" && $(v).data('bazscantype') !== 'tableSelector') {//Select2
                                if (rowData[i]) {
                                    var selectarr = rowData[i].split('<br>');
                                    var selectArr = [];
                                    $(selectarr).each(function(i,v) {
                                        if (v !== "") {
                                            var extractIds = v.match(/(["'])(?:(?=(\\?))\2.)*?\1/g); //match double or single quotes
                                            if (extractIds) {
                                                selectArr.push(extractIds[0].replace(/"/g, ''));
                                            }
                                        }
                                    });
                                    $(v).val(selectArr);
                                    $(v).trigger('change');
                                }
                            }
                            if (v.tagName === "SELECT" && $(v).data('bazscantype') === 'tableSelector') {
                                $(v).val(datatable);
                                $(v).trigger('change');
                            }
                            if (v.tagName === 'DIV') {
                                if ($(v).data('bazscantype') === 'jstree') {//jstree
                                    if (rowData[i]) {
                                        var jstreearr = rowData[i].split('<br>');
                                        var jstreeArr = [];
                                        $(jstreearr).each(function(i,v) {
                                            if (v !== "") {
                                                var extractJstreeId = v.match(/data-jstreeId=".*"/g);
                                                var extractIds = extractJstreeId[0].match(/(["'])(?:(?=(\\?))\2.)*?\1/g); //match double or single quotes
                                                jstreeArr.push(extractIds[0].replace(/"/g, ''));
                                            }
                                        });
                                        $(v).jstree('select_node', jstreeArr);
                                    }
                                }
                                if ($(v).data('bazscantype') === 'radio') {//radio
                                    $(v).find('input').each(function() {
                                        if (rowData[i] === $(this).siblings('label').text()) {
                                            $(this).prop('checked', true);
                                        } else {
                                            $(this).prop('checked', false);
                                        }
                                    });
                                }
                                if ($(v).data('bazscantype') === 'trumbowyg') {//trumbowyg
                                    $(v).trumbowyg('html', rowData[i]);
                                }
                            }
                        }
                    }
                });

                // Enable cancel/update button.
                $('#' + fieldsetDatatable + '-cancel-button').attr('hidden', false);
                $('#' + fieldsetDatatable + '-update-button').attr('hidden', false);
                $('#' + fieldsetDatatable + '-assign-button').attr('hidden', true);
                // Then we extract data again, Compare again, Update data
                $('#' + fieldsetDatatable + '-update-button').off();//Important
                $('#' + fieldsetDatatable + '-update-button').click(function(e) {
                    e.preventDefault();

                    var validated = that._validateForm(false, 'section', false, fieldsetDatatable + '-form');

                    if (validated) {

                        //Execute preExtraction script passed from the html(js script)
                        if (sectionOptions[datatable].preExtraction) {
                            sectionOptions[datatable].preExtraction(sectionOptions['datatables'][datatable], extractDatatableFieldsData);
                        }

                        extractDatatableFieldsData = that._extractDatatableFieldsData(fieldsetDatatable, datatable, true);

                        //Execute postExtraction script passed from the html(js script)
                        if (sectionOptions[datatable].postExtraction) {
                            sectionOptions[datatable].postExtraction(sectionOptions['datatables'][datatable], extractDatatableFieldsData);
                        }

                        var rowAdded =
                            that._addExtractFieldsToDatatable(
                                rowIndex,
                                extractDatatableFieldsData,
                                fieldsetDatatable,
                                datatable,
                                true,
                                false
                            );
                        if (rowAdded) {
                            //Execute postSuccess script passed from the html(js script)
                            if (sectionOptions[datatable].postSuccess) {
                                sectionOptions[datatable].postSuccess(sectionOptions['datatables'][datatable], extractDatatableFieldsData);
                            }
                        }

                        that._clearDatatableFormData(datatable, fieldsetDatatable);
                    }
                    // Hide cancel/update button.
                    $('#' + fieldsetDatatable + '-cancel-button').attr('hidden', true);
                    $('#' + fieldsetDatatable + '-update-button').attr('hidden', true);
                    $('#' + fieldsetDatatable + '-assign-button').attr('hidden', false);
                    $('body').trigger(
                        {
                            'type'     :'formToDatatableTableUpdatedClicked',
                            'rowsCount': sectionOptions['datatables'][datatable].rows().count()
                        }
                    );
                });
                $('#' + fieldsetDatatable + '-cancel-button').off();
                $('#' + fieldsetDatatable + '-cancel-button').click(function() {
                    // Hide cancel/update button.
                    $('#' + fieldsetDatatable + '-cancel-button').attr('hidden', true);
                    $('#' + fieldsetDatatable + '-update-button').attr('hidden', true);
                    $('#' + fieldsetDatatable + '-assign-button').attr('hidden', false);
                    that._clearDatatableFormData(datatable, fieldsetDatatable);
                    $('body').trigger(
                        {
                            'type'     :'formToDatatableTableCancelClicked',
                            'rowsCount': sectionOptions['datatables'][datatable].rows().count()
                        }
                    );
                });

            };

            //Compare extracted fields data with data already in table
            _proto._compareData = function(compareData, inputData, currentTableData, rowIndexToExcl, datatable) {

                var foundRow, foundColumn;
                var excludeActions = false;
                var excludeSeqAndSort = false;

                if ((sectionOptions[datatable].bazdatatable.rowButtons.canDelete === true) ||
                    (sectionOptions[datatable].bazdatatable.rowButtons.canEdit === true)) {
                    excludeActions = true;
                }

                if (sectionOptions[datatable].datatable.rowReorder === true) {
                    excludeSeqAndSort = true;
                }

                if (!compareData.inclIds &&
                    !compareData.exclIds &&
                    (compareData === 'rows' || compareData === 'columns')
                   ) {
                    for (var a = 0; a < currentTableData.length; a++) {
                        if (a !== rowIndexToExcl) {
                            for (var aInputData in inputData) {
                                var compareAllData = compareAll(compareData, inputData[aInputData], currentTableData[a]);

                                if (compareAllData !== false) {
                                    if (compareData === 'rows') {
                                        foundRow = $('#' + datatable).find('tbody tr')[a];
                                        $(foundRow).addClass('animated fadeIn bg-warning');
                                    } else if (compareData === 'columns') {
                                        foundColumn =
                                            $($('#' + datatable).find('tbody tr')[a]).find('td')[compareAllData];
                                        $(foundColumn).addClass('animated fadeIn bg-warning');
                                    }
                                    return true;
                                }
                            }
                        }
                    }
                    return false;
                } else if (compareData.inclIds && Object.keys(compareData.inclIds).length > 0) {
                    for (var b = 0; b < currentTableData.length; b++) {
                        if (b !== rowIndexToExcl) {
                            for (var bInputData in inputData) {
                                if (compareOnlyInclIds(compareData.inclIds, inputData[bInputData], currentTableData[b])) {
                                    foundRow = $('#' + datatable).find('tbody tr')[b];
                                    $(foundRow).addClass('animated fadeIn bg-warning');
                                    return true;
                                }
                            }
                        }
                    }
                    return false;
                } else if (compareData.exclIds && Object.keys(compareData.exclIds).length > 0) {
                    for (var c = 0; c < currentTableData.length; c++) {
                        if (c !== rowIndexToExcl) {
                            for (var cInputData in inputData) {
                                if (compareAllMinusExclIds(compareData.exclIds, inputData[cInputData], currentTableData[c])) {
                                    foundRow = $('#' + datatable).find('tbody tr')[c];
                                    $(foundRow).addClass('animated fadeIn bg-warning');
                                    return true;
                                }
                            }
                        }
                    }
                    return false;
                } else {
                    return false;
                }

                function compareAll(compareData, inputData, currentTableData) {
                    var currentTableDataLength = 0;
                    var startAt = 0;

                    if (excludeSeqAndSort && excludeActions) {
                        currentTableDataLength = currentTableData.length - 3;
                        startAt = 2;
                    } else if (!excludeSeqAndSort && excludeActions) {
                        currentTableDataLength = currentTableData.length - 1;
                    } else if (excludeSeqAndSort && !excludeActions) {
                        currentTableDataLength = currentTableData.length - 2;
                        startAt = 2;
                    }

                    if (compareData === 'rows') {
                        var match = [];
                        for (var i = 0; i < currentTableDataLength; i++) {
                            if (currentTableData[startAt] === inputData[startAt].extractedData) {
                                match[i] = 'true';
                            } else {
                                match[i] = 'false';
                            }
                            startAt++;
                        }

                        if ($.inArray('false', match) === -1) {
                            return true;
                        } else {
                            return false;
                        }
                    } else if (compareData === 'columns') {
                        for (var j = 0; j < currentTableDataLength; j++) {
                            if (currentTableData[startAt] === inputData[startAt].extractedData) {
                                return startAt;
                            }
                            startAt++;
                        }
                        return false;
                    }
                }

                function compareOnlyInclIds(inclIds, inputData, currentTableData) {
                    var inclIdsArray = [];
                    var uniqueData = { };
                    var uniqueDataFound = [];
                    var found = [];
                    var currentTableDataLength = 0;
                    var startAt = 0;
                    if (excludeSeqAndSort && excludeActions) {
                        currentTableDataLength = currentTableData.length - 3;
                        startAt = 2;
                    } else if (!excludeSeqAndSort && excludeActions) {
                        currentTableDataLength = currentTableData.length - 1;
                    } else if (excludeSeqAndSort && !excludeActions) {
                        currentTableDataLength = currentTableData.length - 2;
                        startAt = 2;
                    }
                    for (var inclId in inclIds) {
                        for (var sweepInclIds in inputData) {
                            if (inclId === inputData[sweepInclIds].id) {
                                if (inclIds[inclId].length >= 0) {
                                    uniqueData[sweepInclIds] = [];
                                    for (var uniqueInclId in inclIds[inclId]) {
                                        uniqueData[sweepInclIds].push(inclIds[inclId][uniqueInclId]);
                                    }
                                }
                                inclIdsArray.push(Number(sweepInclIds));
                                break;
                            }
                        }
                    }
                    for (var i = 0; i < currentTableDataLength; i++) {
                        var foundInclId = null;
                        foundInclId = $.inArray(startAt,inclIdsArray);
                        if (foundInclId !== -1) {
                            if (currentTableData[startAt] === inputData[startAt].extractedData) {
                                if (uniqueData[startAt].length > 0 ) {
                                    for (var j = 0; j < uniqueData[startAt].length; j++) {
                                        if (uniqueData[startAt][j] === inputData[startAt].extractedData) {
                                            uniqueDataFound.push('true');
                                        }
                                    }
                                } else {
                                    found.push('true');
                                }
                            }
                        }
                        startAt++;
                    }
                    if (found.length === Object.keys(inclIds).length) {
                        return true;
                    } else if (uniqueDataFound.length > 0) {
                        return true;
                    } else {
                        return false;
                    }
                }

                function compareAllMinusExclIds(exclIds, inputData, currentTableData) {
                    var exclIdsArray = [];
                    var currentTableDataLength = 0;
                    var startAt = 0;
                    if (excludeSeqAndSort && excludeActions) {
                        currentTableDataLength = currentTableData.length - 3;
                        startAt = 2;
                    } else if (!excludeSeqAndSort && excludeActions) {
                        currentTableDataLength = currentTableData.length - 1;
                    } else if (excludeSeqAndSort && !excludeActions) {
                        currentTableDataLength = currentTableData.length - 2;
                        startAt = 2;
                    }
                    for (var exclId in exclIds) {
                        for (var sweepExclIds in inputData) {
                            if (exclId === inputData[sweepExclIds].id) {
                                exclIdsArray.push(Number(sweepExclIds));
                                break;
                            }
                        }
                    }
                    for (var i = 0; i < currentTableDataLength; i++) {
                        var foundExclId = null;
                        foundExclId = $.inArray(startAt,exclIdsArray);
                        if (foundExclId === -1) {
                            if (currentTableData[startAt] === inputData[startAt].extractedData) {
                                return true;
                            } else {
                                return false;
                            }
                        }
                        startAt++;
                    }
                }
            };

            _proto._rowReorderRedoSeq = function(table, datatable) {
                var redoSeq = 1;
                $('#' + datatable).find('td.reorder').each(function() {
                    $(this).html(redoSeq);
                    redoSeq++;
                });
            };

            //Register table row edit and delete buttons
            _proto._registerDatatableButtons = function(table, el, datatable, sectionId, fieldsetDatatable) {
                var rowIndex, rowData;
                $(el).find('table').each(function() {
                    $(this).find('.tableDeleteButton').each(function() {
                        $(this).off();
                        $(this).click(function() {
                            if ($(this).closest('tr').hasClass('child')) {
                                rowIndex = table.row($(this).closest('tr').prev('tr')).index();
                                rowData = table.row($(this).closest('tr').prev('tr')).data();
                                table.row($(this).closest('tr').prev('tr')).remove().draw();
                                that._rowReorderRedoSeq(table, datatable);
                                // that._deleteDatatableDataFromObject(rowIndex, fieldsetDatatable, sectionId, datatable);
                                that._tableDataToObj();
                                that._clearDatatableFormData(datatable, fieldsetDatatable);
                            } else {
                                rowIndex = table.row($(this).closest('tr')).index();
                                rowData = table.row($(this).parents('tr')).data();
                                table.row($(this).parents('tr')).remove().draw();
                                that._rowReorderRedoSeq(table, datatable);
                                // that._deleteDatatableDataFromObject(rowIndex, fieldsetDatatable, sectionId, datatable);
                                that._tableDataToObj();
                                that._clearDatatableFormData(datatable, fieldsetDatatable);
                            }
                            that._registerDatatableButtons(table, el, datatable, sectionId, fieldsetDatatable);
                            //Execute postSuccess script passed from the html(js script)
                            if (sectionOptions[datatable].postSuccess) {
                                sectionOptions[datatable].postSuccess(sectionOptions['datatables'][datatable]);
                            }
                            // Hide cancel/update button.
                            $('#' + fieldsetDatatable + '-cancel-button').attr('hidden', true);
                            $('#' + fieldsetDatatable + '-update-button').attr('hidden', true);
                            $('#' + fieldsetDatatable + '-assign-button').attr('hidden', false);

                            $('body').trigger(
                                {
                                    'type'     :'formToDatatableTableRowDelete',
                                    'rowIndex' : rowIndex,
                                    'rowData'  : rowData,
                                    'rowsCount': table.rows().count()
                                }
                            );

                            if (table.rows().count() === 0) {
                                $('body').trigger(
                                    {
                                        'type'     :'formToDatatableTableEmpty',
                                        'rowsCount': table.rows().count()
                                    }
                                );
                            }
                            that._tableDataToObj();
                        });
                    });
                    $(this).find('.tableEditButton').each(function() {
                        $(this).off();
                        $(this).click(function() {
                            if ($(this).closest('tr').hasClass('child')) {
                                rowIndex = table.row($(this).closest('tr').prev('tr')).index();
                                rowData = table.row($(this).closest('tr').prev('tr')).data();
                            } else {
                                rowIndex = table.row($(this).closest('tr')).index();
                                rowData = table.row($(this).closest('tr')).data();
                            }
                            // var popActions = rowData.pop();//get rid of actions
                            that._clearDatatableFormData(datatable, fieldsetDatatable);
                            that._editDatatableRow(fieldsetDatatable, rowIndex, rowData, datatable);
                            //Execute onEdit script passed from the html(js script)
                            if (sectionOptions[datatable].onEdit) {
                                sectionOptions[datatable].onEdit(sectionOptions['datatables'][datatable]);
                            }
                            $('body').trigger(
                                {
                                    'type'     :'formToDatatableTableRowEdit',
                                    'rowIndex' : rowIndex,
                                    'rowData'  : rowData,
                                    'rowsCount': table.rows().count()
                                }
                            );
                        });
                    });
                });
            };

            //Clear form data on success insertion
            _proto._clearDatatableFormData = function(datatable, fieldsetDatatable) {
                var fieldsToClear;

                if (sectionOptions[datatable].bazdatatable.keepFieldsData) {
                    var fieldsToKeep = sectionOptions[datatable].bazdatatable.keepFieldsData;
                }
                var allFields = [];
                $('#' + fieldsetDatatable + '-fieldset').find('[data-bazscantype]').each(function(i,v) {
                    allFields.push($(v)[0].id);
                });

                if (fieldsToKeep && fieldsToKeep.length > 0) {
                    fieldsToClear = $(allFields).not(sectionOptions[datatable].bazdatatable.keepFieldsData).get();//diff array
                } else if (fieldsToKeep && fieldsToKeep.length === 0) {
                    fieldsToClear = null;
                } else {
                    fieldsToClear = allFields;
                }

                if (fieldsToClear) {
                    $.each(fieldsToClear, function(i,v) {
                        if ($('#' + v).data('bazscantype')) {
                            if ($('#' + v)[0].tagName === 'INPUT' && $('#' + v)[0].type === 'checkbox') {
                                $('#' + v).prop('checked', $('#' + v).prop('defaultChecked'));
                            } else if ($('#' + v)[0].tagName === 'INPUT' || $('#' + v)[0].tagName === 'TEXTAREA') {
                                $('#' + v).val('');
                            }
                            if ($('#' + v)[0].tagName === "SELECT") {//select2
                                $('#' + v).children('option').attr('disabled', false);
                                $('#' + v).val(null).trigger('change');
                            }
                            if ($('#' + v)[0].tagName === 'DIV') {
                                if ($('#' + v).data('bazscantype') === 'jstree') {//jstree
                                    $('#' + v).jstree('deselect_all');
                                }
                                if ($('#' + v).data('bazscantype') === 'radio') {//radio
                                    if ($('#' + v).find('input[checked]').length !== 0) {
                                        $('#' + v).find('input[checked]').prop('checked', true);
                                    } else {
                                        $('#' + v).find('input').each(function(i,v) {
                                            $('#' + v).prop('checked', false);
                                        });
                                    }
                                }
                                if ($('#' + v).data('bazscantype') === 'trumbowyg') {//trumbowyg
                                    $('#' + v).trumbowyg('empty');
                                }
                            }
                        }
                    });
                }
            };

            // Add tables data to dataCollection
            _proto._tableDataToObj = function()
            {
                // for (var sectionId in tableData) {
                    for (var data in sectionOptions['datatables']) {
                        var excludeActions = false;
                        var excludeSeqAndSort = false;
                        var currentTableDataLength = 0;

                        if ((sectionOptions[data].bazdatatable.rowButtons.canDelete === true) ||
                            (sectionOptions[data].bazdatatable.rowButtons.canEdit === true)
                        ) {
                            excludeActions = true;
                        }

                        if (sectionOptions[data].datatable.rowReorder === true) {
                            excludeSeqAndSort = true;
                        }

                        dataCollection[componentId][sectionId][data]['data'] = [];

                        $.each(sectionOptions['datatables'][data].rows().data(), function(i,v) {
                            var startAt = 0;
                            if (excludeSeqAndSort && excludeActions) {
                                currentTableDataLength = v.length - 3;
                                startAt = 2;
                            } else if (!excludeSeqAndSort && excludeActions) {
                                currentTableDataLength = v.length - 1;
                            } else if (excludeSeqAndSort && !excludeActions) {
                                currentTableDataLength = v.length - 2;
                                startAt = 2;
                            }
                            var thatI = i;
                            dataCollection[componentId][sectionId][data]['data'][i] = { };
                            for (var j = 0; j < currentTableDataLength; j++) {
                                var columnData;
                                if (v[startAt]) {
                                    var columnDataHasClass = v[startAt].match(/class="(.*?)"/g)
                                }
                                if (columnDataHasClass) {
                                    columnData = (columnDataHasClass.toString().match(/"(.*?)"/g)).toString().replace(/"/g, '');
                                } else {
                                    columnData = v[startAt];
                                }
                                dataCollection[componentId][sectionId][data]['data'][thatI][dataTableFields[componentId][sectionId][data][startAt]] = columnData;
                                startAt++;
                            }
                        });
                    }
                // }
            }

            // Add tables data from dataCollection
            _proto._dataArrToTableData = function()
            {
                for (var table in sectionOptions['datatables']) {
                    for (var data in sectionOptions[table]['data']) {
                        that._addExtractFieldsToDatatable(
                            null,
                            {"0" : sectionOptions[table]['data'][data]},
                            sectionId,
                            table,
                            false,
                            true
                        );
                    }
                }

                $('body').trigger(
                    {
                        'type'     :'formToDatatableTableImportComplete'
                    });
            }

            BazContentSectionWithFormToDatatable._jQueryInterface = function _jQueryInterface(options) {
                dataCollection = window['dataCollection'];
                componentId = $(this).parents('.component')[0].id;
                sectionId = $(this)[0].id;

                dataCollection[componentId][sectionId]['BazContentSectionWithFormToDatatable'] = $(this).data(DATA_KEY);
                options = $.extend({}, Default, options);

                if (!dataCollection[componentId][sectionId]['BazContentSectionWithFormToDatatable']) {
                    dataCollection[componentId][sectionId]['BazContentSectionWithFormToDatatable'] = new BazContentSectionWithFormToDatatable($(this), options);
                    $(this).data(DATA_KEY, typeof options === 'string' ? 'options need to be an object and not string' : options);
                    dataCollection[componentId][sectionId]['BazContentSectionWithFormToDatatable']._init(options);
                } else {
                    delete dataCollection[componentId][sectionId]['BazContentSectionWithFormToDatatable'];
                    dataCollection[componentId][sectionId]['BazContentSectionWithFormToDatatable'] = new BazContentSectionWithFormToDatatable($(this), options);
                    $(this).data(DATA_KEY, typeof options === 'string' ? 'options need to be an object and not string' : options);
                    dataCollection[componentId][sectionId]['BazContentSectionWithFormToDatatable']._init(options);
                }
            };

        return BazContentSectionWithFormToDatatable;

        }();

    $(document).on('libsLoadComplete bazContentLoaderAjaxComplete bazContentLoaderModalComplete bazContentWizardAjaxComplete', function() {
        $('body').find('.sectionWithFormToDatatable').each(function() {
            // if ($(this).data('bazdevmodetools') === 'false' ||
            //     $(this).data('bazdevmodetools') === false) {
                BazContentSectionWithFormToDatatable._jQueryInterface.call($(this));
            // }
        });
    });

    $.fn[NAME] = BazContentSectionWithFormToDatatable._jQueryInterface;
    $.fn[NAME].Constructor = BazContentSectionWithFormToDatatable;

    $.fn[NAME].noConflict = function () {
        $.fn[NAME] = JQUERY_NO_CONFLICT;
        return BazContentSectionWithFormToDatatable._jQueryInterface;
    };

    return BazContentSectionWithFormToDatatable;
}(jQuery);

exports.BazContentSectionWithFormToDatatable = BazContentSectionWithFormToDatatable;

Object.defineProperty(exports, '__esModule', { value: true });

}));
/* globals define exports BazContentLoader BazContentFields Swal PNotify */
/*
* @title                    : BazContentSectionWithListing
* @description              : Baz Lib for Content (Sections With Form)
* @developer                : guru@bazaari.com.au
* @usage                    : ('#'+ sectionId).BazContentSectionWithListing;
* @functions                :
* @options                  :
*/
(function (global, factory) {
    typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports) :
    typeof define === 'function' && define.amd ? define(['exports'], factory) :
    (global = global || self, factory(global.BazLibs = {}));
}(this, function (exports) {

    var BazContentSectionWithListing = function ($) {

        var NAME                    = 'BazContentSectionWithListing';
        var DATA_KEY                = 'baz.contentsectionslist';
        // var EVENT_KEY               = "." + DATA_KEY;
        var JQUERY_NO_CONFLICT      = $.fn[NAME];
        // var Event = {
        // };
        // var ClassName = {
        // };
        // var Selector = {
        // };
        var Default = {
            task     : null
        };
        var dataCollection,
            componentId,
            sectionId,
            // pnotifySound,
            classes,
            that,
            thisOptions,
            datatableOptions,
            swalSound;
        var listColumns = { };
        var filter, query;

        var BazContentSectionWithListing = function () {
            function BazContentSectionWithListing(element, settings) {
                that = this;
                this._element = element;
                this._settings = settings;
            }

            var _proto = BazContentSectionWithListing.prototype;

            _proto._error = function _error(message) {
                throw new Error(message);
            };

            _proto._init = function _init(options) {
                componentId = $(this._element).parents('.component')[0].id;
                sectionId = $(this._element)[0].id;
                query = '';

                dataCollection = window['dataCollection'];
                // pnotifySound = dataCollection.env.sounds.pnotifySound
                swalSound = dataCollection.env.sounds.swalSound

                if (!dataCollection[componentId]) {
                    dataCollection[componentId] = { };
                }
                if (!dataCollection[componentId][sectionId]) {
                    dataCollection[componentId][sectionId] = { };
                }

                if ($(this._element).is('.sectionWithListingFilter')) {
                    this._buildListingFilters(options);
                    filter = true;
                }

                if ($(this._element).is('.sectionWithListingDatatable')) {
                    this._buildListingDatatable(options);
                    query = thisOptions.listOptions.postParams;
                    filter = false;
                }
            };

            //Build listing filters
            _proto._buildListingFilters = function() {
                BazContentFields.init({
                    'componentId'   : componentId,
                    'sectionId'     : sectionId
                });
                $(this._element).BazContentSectionWithFormToDatatable();

                function toggleFilterButtons(sectionId) {
                    if ($('#' + sectionId + '-filters option:selected').data()['filter_type'] == 0) {
                        $('#' + sectionId + '-edit, #' + sectionId + '-share').attr("disabled", true);
                        $('#' + sectionId + '-delete').addClass('disabled');
                    } else if ($('#' + sectionId + '-filters option:selected').data()['filter_type'] == 1) {
                        $('#' + sectionId + '-edit, #' + sectionId + '-share').attr("disabled", false);
                        $('#' + sectionId + '-delete').removeClass('disabled');
                    }
                    if ($('#' + sectionId + '-filters option:selected').data()['shared'] == 1) {
                        $('#' + sectionId + '-edit, #' + sectionId + '-share').attr("disabled", true);
                        $('#' + sectionId + '-delete').addClass('disabled');
                    }
                }
                        // $('#' + sectionId + '-filters option:selected').data()['filter_type'] === 1    //User

                toggleFilterButtons(sectionId);

                //Filter Buttons
                $('#' + sectionId + '-filters').change(function() {
                    toggleFilterButtons(sectionId + '-filter');

                    if ($('#' + sectionId + '-filter-alert').length === 1) {
                        $('#' + sectionId + '-filter-alert').remove();
                    }


                    if (thisOptions.listOptions.postParams.order) {
                        query = { };
                        query['conditions'] = $('#' + sectionId + '-filter-filters option:selected').data()['conditions'];
                        query['order'] = thisOptions.listOptions.postParams.order;
                    } else {
                        query = $('#' + sectionId + '-filter-filters option:selected').data()['conditions'];
                    }

                    that._filterRunAjax(
                        1,
                        datatableOptions.paginationCounters.limit,
                        query
                    );
                });

                //Open Sharing Modal
                $('#' + sectionId + '-share').click(function(e) {
                    e.preventDefault();

                    var filterUrl = $('#' + sectionId + '-filter-filters option:selected').data()['url'];
                    var sharedIds = $('#' + sectionId + '-filter-filters option:selected').data()['shared_ids'];

                    if (sharedIds && sharedIds.rids) {
                        for (var rid in sharedIds.rids) {
                            $('#' + sectionId + '-filter-sharing-rids').append(
                                '<option value="' + sharedIds.rids[rid]['id'] + '" selected>' + sharedIds.rids[rid]['name'] + '</option>'
                            );
                        }
                        $('#' + sectionId + '-filter-sharing-rids').trigger('select2:change');
                    }

                    if (sharedIds && sharedIds.eids) {
                        for (var eid in sharedIds.eids) {
                            $('#' + sectionId + '-filter-sharing-eids').append(
                                '<option value="' + sharedIds.eids[eid]['id'] + '" selected>' + sharedIds.eids[eid]['name'] + '</option>'
                            );
                        }
                        $('#' + sectionId + '-filter-sharing-eids').trigger('select2:change');
                    }

                    if (sharedIds && sharedIds.aids) {
                        for (var aid in sharedIds.aids) {
                            $('#' + sectionId + '-filter-sharing-aids').append(
                                '<option value="' + sharedIds.aids[aid]['id'] + '" selected>' + sharedIds.aids[aid]['name'] + '</option>'
                            );
                        }
                        $('#' + sectionId + '-filter-sharing-aids').trigger('select2:change');
                    }

                    $('#' + sectionId + '-filter-sharing-direct-url').val(filterUrl);

                    $('#' + sectionId + '-filter-sharing-modal').modal('show');

                    //Share Buttons
                    $('#' + sectionId + '-filter-share-filter, #' + sectionId + '-filter-sharing-modal-button-close').off();
                    $('#' + sectionId + '-filter-sharing-modal-button-close').click(function() {
                        $('#' + sectionId + '-filter-sharing-rids').empty().trigger('select2:change');
                        $('#' + sectionId + '-filter-sharing-eids').empty().trigger('select2:change');
                        $('#' + sectionId + '-filter-sharing-aids').empty().trigger('select2:change');
                    });
                    $('#' + sectionId + '-filter-share-filter').click(function(e) {
                        e.preventDefault();

                        var rids = $('#' + sectionId + '-filter-sharing-rids').select2('data');
                        var uids;
                        if ($('#' + sectionId + '-filter-sharing-eids').length > 0) {
                            uids = $('#' + sectionId + '-filter-sharing-eids').select2('data');
                        } else if ($('#' + sectionId + '-filter-sharing-aids').length > 0) {
                            uids = $('#' + sectionId + '-filter-sharing-aids').select2('data');
                        }

                        //Save To Db
                        var postData = { };
                        postData['id'] = $('#' + sectionId + '-filter-filters option:selected').data()['id'];
                        postData['component_id'] = $('#' + sectionId + '-filter-filters option:selected').data()['component_id'];
                        postData['filter_type'] = '1';
                        postData['shared_ids'] = { };
                        postData['shared_ids']['rids'] = [];
                        if (rids.length > 0) {
                            $(rids).each(function(index, rid) {
                                postData['shared_ids']['rids'].push(rid.id);
                            });
                        }
                        if ($('#' + sectionId + '-filter-sharing-eids').length > 0) {
                            postData['shared_ids']['eids'] = [];
                            if (uids.length > 0) {
                                $(uids).each(function(index, eid) {
                                    postData['shared_ids']['eids'].push(eid.id);
                                });
                            }
                        } else if ($('#' + sectionId + '-filter-sharing-aids').length > 0) {
                            postData['shared_ids']['aids'] = [];
                            if (uids.length > 0) {
                                $(uids).each(function(index, aid) {
                                    postData['shared_ids']['aids'].push(aid.id);
                                });
                            }
                        }
                        postData['shared_ids'] = JSON.stringify(postData['shared_ids']);

                        postData[$('#security-token').attr('name')] = $('#security-token').val();

                        var url = $(this).attr('href');

                        // //Update Filter
                        $.post(url, postData, function(response) {
                            if (response.responseCode === 0) {
                                PNotify.success({
                                    'title' : response.responseMessage
                                });
                                if (response.filters) {
                                    redoFiltersOptions(query, sectionId, response);
                                }
                            } else {
                                PNotify.error({
                                    'title' : response.responseMessage
                                });
                            }
                            if (response.tokenKey && response.token) {
                                $('#security-token').attr('name', response.tokenKey);
                                $('#security-token').val(response.token);
                            }
                            $('#' + sectionId + '-filter-sharing-rids').empty().trigger('select2:change');
                            $('#' + sectionId + '-filter-sharing-eids').empty().trigger('select2:change');
                            $('#' + sectionId + '-filter-sharing-aids').empty().trigger('select2:change');
                        }, 'json');

                        $('#' + sectionId + '-filter-sharing-modal').modal('hide');
                    });
                });

                //Reset
                $('#' + sectionId + '-reset').click(function(e) {
                    e.preventDefault();

                    resetFilters();
                });
                function resetFilters() {
                    query = '';
                    var defaultFilter = null;

                    $('#' + sectionId + '-filter-filters').children().each(function(index, filter) {
                        if ($(filter).data()['account_id'] != 0 &&
                            $(filter).data()['is_default'] == 1
                        ) {
                            query = $(filter).data()['conditions'];
                            defaultFilter = filter;
                            return false;
                        } else if ($(filter).data()['account_id'] == 0 &&
                                   $(filter).data()['is_default'] == 1
                        ) {
                            query = $(filter).data()['conditions'];
                            defaultFilter = filter;
                        }
                    });

                    that._filterRunAjax(
                        1,
                        datatableOptions.paginationCounters.limit,
                        query
                    );

                    toggleFilterButtons(sectionId + '-filter');

                    if (defaultFilter) {
                        $('#' + sectionId + '-filter-filters').val($(defaultFilter).val());
                        $('#' + sectionId + '-edit, #' + sectionId + '-share').attr("disabled", true);
                        $('#' + sectionId + '-delete').addClass('disabled');
                    }
                }

                //Add / Open Modal
                $('#' + sectionId + '-add').click(function(e) {
                    e.preventDefault();

                    $('#' + sectionId + '-filter-save-add').attr('hidden', false);
                    $('#' + sectionId + '-filter-save-update').attr('hidden', true);

                    clearStoredData();

                    $('#' + sectionId + '-filter-modal').modal('show');
                });

                //Clone / Open Modal
                $('#' + sectionId + '-clone').click(function(e) {
                    e.preventDefault();

                    var selectedFilter = $('#' + sectionId + '-filter-filters option:selected');

                    if ($(selectedFilter).data()['conditions'] === '') {
                        PNotify.error({'title': 'Show All filter cannot be cloned'});
                        return;
                    }

                    var postData = { };
                    postData['id'] = selectedFilter.data()['id'];
                    postData['component_id'] = selectedFilter.data()['component_id'];
                    postData['clone'] = true;
                    postData[$('#security-token').attr('name')] = $('#security-token').val();

                    var url = $(this).attr('href');

                    $.post(url, postData, function(response) {
                        if (response.responseCode === 0) {
                            PNotify.success({
                                'title'     : response.responseMessage
                            });
                            if (response.filters) {
                                redoFiltersOptions('', sectionId, response);
                            }
                            resetFilters();
                            toggleFilterButtons(sectionId + '-filter');
                        } else {
                            PNotify.error({
                                'title'     : response.responseMessage
                            });
                        }
                        if (response.tokenKey && response.token) {
                            $('#security-token').attr('name', response.tokenKey);
                            $('#security-token').val(response.token);
                        }
                    }, 'json');

                    toggleFilterButtons(sectionId + '-filter');
                });

                //Edit / Open Modal
                $('#' + sectionId + '-edit').click(function(e) {
                    e.preventDefault();

                    $('#' + sectionId + '-filter-save-add').attr('hidden', true);
                    $('#' + sectionId + '-filter-save-update').attr('hidden', false);

                    editFilter();

                    $('#' + sectionId + '-filter-modal').modal('show');
                });
                function editFilter() {
                    var selectedFilter = $('#' + sectionId + '-filter-filters option:selected');

                    var conditionsStr = $(selectedFilter).data().conditions;

                    if (conditionsStr === '') { //Empty string for show all condition
                        return;
                    }
                    var conditions = conditionsStr.substring(0, conditionsStr.length - 1);
                    var conditionsRows = conditions.split('&');
                    var conditionsColumns = [];

                    $.each(conditionsRows, function(index, row) {
                        conditionsColumns[index] = row.split('|');
                    });

                    var select2FieldData;

                    //Andor Object
                    select2FieldData = $('#' + sectionId + '-filter-andor option');
                    var andOrS2 = { };
                    select2FieldData.each(function(index, data) {
                        if ($(data).data().value) {
                            andOrS2[$(data).data().value] = $(data)[0].innerHTML;
                        }
                    });

                    if ($('#' + sectionId + '-filter-andor').data().disabledtext) {
                        andOrS2[$('#' + sectionId + '-filter-andor').data().disabledtext] =
                        $('#' + sectionId + '-filter-andor').data().disabledtext;
                    }

                    //Andor Object
                    select2FieldData = $('#' + sectionId + '-filter-field option');
                    var fieldS2 = { };
                    select2FieldData.each(function(index, data) {
                        if ($(data).data().value) {
                            fieldS2[$(data).data().value] = $(data)[0].innerHTML;
                        }
                    });

                    //Andor Object
                    select2FieldData = $('#' + sectionId + '-filter-operator option');
                    var operatorS2 = { };
                    select2FieldData.each(function(index, data) {
                        if ($(data).data().value) {
                            operatorS2[$(data).data().value] = $(data)[0].innerHTML;
                        }
                    });

                    var columns = { };

                    $.each(conditionsColumns, function(index, column) {

                        columns[index] = { };
                        columns[index][0] = { };
                        columns[index][0]['id'] = sectionId + '-filter-andor';

                        if (!andOrS2[column[0]]) {
                            columns[index][0]['extractedData'] = '<span class="' + column[0] + '"></span><br>';
                        } else {
                            columns[index][0]['extractedData'] = '<span class="' + column[0] + '">' + andOrS2[column[0]] + '</span><br>';
                        }

                        columns[index][1] = { };
                        columns[index][1]['id'] = sectionId + '-filter-field';
                        columns[index][1]['extractedData'] = '<span class="' + column[1] + '">' + fieldS2[column[1]] + '</span><br>';

                        columns[index][2] = { };
                        columns[index][2]['id'] = sectionId + '-filter-operator';
                        columns[index][2]['extractedData'] = '<span class="' + column[2] + '">' + operatorS2[column[2]] + '</span><br>';

                        columns[index][3] = { };
                        columns[index][3]['id'] = sectionId + '-filter-value';
                        columns[index][3]['extractedData'] = column[3];

                        columns[index][4] = { };
                        columns[index][4]['id'] = sectionId + '-filter-actions';
                        columns[index][4]['extractedData'] =
                            '<button data-row-id="' + (index + 1) + '" type="button" class="btn btn-xs btn-danger float-right ml-1' +
                            ' tableDeleteButton"><i class="fa fas fa-fw text-xs fa-trash"></i></button><button data-row-id="' +
                            (index + 1) + '" type="button" class="btn btn-xs btn-primary float-right tableEditButton"><i class="fa ' +
                            'fas fa-fw text-xs fa-edit"></i></button>';
                    });

                    dataCollection[componentId][sectionId + '-filter'][sectionId + '-filter-table']['data'] = columns;

                    dataCollection[componentId][sectionId + '-filter']['BazContentSectionWithFormToDatatable']._dataArrToTableData();

                    $('#' + sectionId + '-filter-id').val($(selectedFilter).data()['id']);
                    $('#' + sectionId + '-filter-name').val($(selectedFilter).data()['name']);

                    if ($(selectedFilter).data()['is_default'] === 1) {
                        $('#' + sectionId + '-filter-default')[0].checked = true;
                        $('#' + sectionId + '-filter-default').attr('disabled', true);
                    } else {
                        $('#' + sectionId + '-filter-default')[0].checked = false;
                        $('#' + sectionId + '-filter-default').attr('disabled', false);
                    }
                }
                $('body').on('formToDatatableTableImportComplete', function () {
                    dataCollection[componentId][sectionId + '-filter']['BazContentSectionWithFormToDatatable']._tableDataToObj();
                    $('#' + sectionId + '-filter-name').attr('disabled', false);
                });

                //Delete
                $('#' + sectionId + '-delete').click(function(e) {
                    e.preventDefault();

                    var selectedFilter = $('#' + sectionId + '-filter-filters option:selected');

                    Swal.fire({
                        title                       : '<span class="text-danger"> Delete Filter ' + selectedFilter.data()['name'] + '?</span>',
                        icon                        : 'question',
                        background                  : 'rgba(0,0,0,.8)',
                        backdrop                    : 'rgba(0,0,0,.6)',
                        buttonsStyling              : false,
                        confirmButtonText           : 'Delete',
                        customClass                 : {
                            'confirmButton'             : 'btn btn-danger text-uppercase',
                            'cancelButton'              : 'ml-2 btn btn-secondary text-uppercase',
                        },
                        showCancelButton            : true,
                        keydownListenerCapture      : true,
                        allowOutsideClick           : true,
                        allowEscapeKey              : true,
                        didOpen                     : function() {
                            swalSound.play();
                        }
                    }).then((result) => {
                        if (result.value) {
                            if ($(selectedFilter).data().ns === true) {
                                $(selectedFilter).remove();
                                $('#' + sectionId + '-edit, #' + sectionId + '-share').attr("disabled", true);
                                $('#' + sectionId + '-delete').addClass('disabled');

                                PNotify.success({
                                    'title'     : selectedFilter.data()['name'] + ' deleted successfully.'
                                });
                                resetFilters();
                            } else {
                                var postData = { };
                                postData['id'] = selectedFilter.data()['id'];
                                postData['component_id'] = selectedFilter.data()['component_id'];
                                postData[$('#security-token').attr('name')] = $('#security-token').val();

                                var url = $(this).attr('href');

                                $.post(url, postData, function(response) {
                                    if (response.responseCode === 0) {
                                        PNotify.success({
                                            'title'     : selectedFilter.data()['name'] + ' deleted successfully.'
                                        });
                                        if (response.filters) {
                                            redoFiltersOptions('', sectionId, response);
                                        }
                                        resetFilters();
                                    } else {
                                        PNotify.error({
                                            'title'     : 'Cannot delete filter.'
                                        });
                                    }
                                    if (response.tokenKey && response.token) {
                                        $('#security-token').attr('name', response.tokenKey);
                                        $('#security-token').val(response.token);
                                    }
                                }, 'json');
                            }
                        }
                    });
                });

                // Add Numeric for numberic fields
                $('#' + sectionId + '-field').children().each(function(index, field) {
                    if ($(field).data()['number'] == true) {
                        var html = $(field).html();
                        $(field).html(html + ' (Numeric)');
                    }
                });

                //Enable/Disable Operators as per field type (numeric/alphanumeric)
                $('#' + sectionId + '-field').on('select2:select', function(e) {

                    var options = $('#' + sectionId + '-filter-operator').children();

                    if ($(e.params.data.element).data()['number'] == true) {
                        options.each(function(index, option) {
                            $(option).attr('disabled', false);
                        });
                        $('#' + sectionId + '-filter-value').attr('pattern', "([0-9]+.{0,1}[0-9]*,{0,1})*[0-9]");

                    } else if ($(e.params.data.element).data()['number'] == false) {
                        options.each(function(index, option) {
                            if ($(option).val() === 'lessthan' ||
                                $(option).val() === 'lessthanequals' ||
                                $(option).val() === 'greaterthan' ||
                                $(option).val() === 'greaterthanequals'
                            ) {
                                $(option).attr('disabled', true);
                            } else {
                                $(option).attr('disabled', false);
                            }
                        });
                        $('#' + sectionId + '-filter-value').attr('pattern', "");
                    }

                    $('#' + sectionId + '-filter-operator').trigger('change');
                });

                //Enable/Disable value field on empty/notempty operator
                $('#' + sectionId + '-operator').on('select2:select', function(e) {
                    if ($(e.params.data.element).val() === 'empty' ||
                        $(e.params.data.element).val() === 'notempty'
                    ) {
                        $('#' + sectionId + '-filter-value').attr('disabled', true);
                    } else {
                        $('#' + sectionId + '-filter-value').attr('disabled', false);
                    }
                });

                $('body').on('formToDatatableTableRowEdit', function(e) {
                    if ($('#' + sectionId + '-filter-operator').val() === 'empty' ||
                        $('#' + sectionId + '-filter-operator').val() === 'notempty'
                    ) {
                        $('#' + sectionId + '-filter-value').attr('disabled', true);
                    } else {
                        $('#' + sectionId + '-filter-value').attr('disabled', false);
                    }

                    $('#' + sectionId + '-filter-andor').val('and').trigger('change');
                    $('#' + sectionId + '-filter-value').attr('disabled', false);

                    if (e.rowsCount === 1) {
                        $('#' + sectionId + '-filter-andor').attr('disabled', true);
                    } else {
                        if (e.rowIndex === 0) {
                            $('#' + sectionId + '-filter-andor').attr('disabled', true);
                        } else {
                            $('#' + sectionId + '-filter-andor').attr('disabled', false);
                        }
                    }
                });

                //Add Name
                $('#' + sectionId + '-name').keyup(function() {
                    if ($(this).val() !== '') {
                        $('#' + sectionId + '-filter-save-add').removeClass('disabled');
                        $('#' + sectionId + '-filter-save-update').removeClass('disabled');
                    } else {
                        $('#' + sectionId + '-filter-save-add').addClass('disabled');
                        $('#' + sectionId + '-filter-save-update').addClass('disabled');
                    }
                });

                //Adding to table
                $('#' + sectionId + '-assign-button').click(function(e) {
                    e.preventDefault();
                    onFormToDatatableTableUpdate(e);

                    // $('#' + sectionId + '-filter-name').attr('disabled', false);
                    // $('#' + sectionId + '-filter-value').attr('disabled', false);

                    // $('#' + sectionId + '-filter-andor').attr('disabled', false);
                    // $('#' + sectionId + '-filter-andor').val('and').trigger('change');
                });

                $('body').on('formToDatatableTableUpdatedClicked', function(e) {
                    onFormToDatatableTableUpdate(e);
                });

                $('#' + sectionId + '-default').click(function() {

                    var postData = { };
                    postData['component_id'] = $('#' + sectionId + '-filter-filters option:selected').data()['component_id'];
                    postData[$('#security-token').attr('name')] = $('#security-token').val();

                    var url = $(this).data('href');

                    $.post(url, postData, function(response) {
                        if (response.responseCode === 0) {
                            Swal.fire({
                                title                       : '<span class="text-danger"> Filter ' + response.defaultFilter[0].name + ' is already set as default. ' +
                                                              'Make this filter default instead?</span>',
                                icon                        : 'question',
                                background                  : 'rgba(0,0,0,.8)',
                                backdrop                    : 'rgba(0,0,0,.6)',
                                buttonsStyling              : false,
                                confirmButtonText           : 'Yes',
                                cancelButtonText            : 'No',
                                customClass                 : {
                                    'confirmButton'             : 'btn btn-info text-uppercase',
                                    'cancelButton'              : 'ml-2 btn btn-secondary text-uppercase',
                                },
                                showCancelButton            : true,
                                keydownListenerCapture      : true,
                                allowOutsideClick           : false,
                                allowEscapeKey              : false,
                                didOpen                     : function() {
                                    swalSound.play();
                                }
                            }).then((result) => {
                                if (result.isDismissed) {
                                    $('#' + sectionId + '-filter-default')[0].checked = false;
                                }
                            });
                        }
                        if (response.tokenKey && response.token) {
                            $('#security-token').attr('name', response.tokenKey);
                            $('#security-token').val(response.token);
                        }
                    }, 'json');

                    $('#' + sectionId + '-filter-save-add').removeClass('disabled');
                    $('#' + sectionId + '-filter-save-update').removeClass('disabled');
                });

                //If Only 1 row - Remove And/Or
                $('body').on('formToDatatableTableRowDelete', function(e) {
                    onFormToDatatableTableUpdate(e);
                });

                function onFormToDatatableTableUpdate(e) {
                    //Remove numeric from edit data
                    $('#' + sectionId + '-filter-table-data tbody tr').each(function(index, tr) {
                       var field = $(tr).find('td')[1];
                       if (field) {
                           field.innerHTML = field.innerHTML.replace(" (Numeric)", "");
                       }
                    });
                    $('#' + sectionId + '-filter-andor').val('and').trigger('change');
                    $('#' + sectionId + '-filter-andor').attr('disabled', false);

                    if (e.rowsCount === 1) {
                        $($('#' + sectionId + '-filter-table-data tbody tr')[0]).find('td')[0].innerHTML = '-';
                    }
                    if (e.rowsCount < 1) {
                        $('#' + sectionId + '-filter-name').attr('disabled', true);
                        $('#' + sectionId + '-filter-save-add').addClass('disabled');
                        $('#' + sectionId + '-filter-save-update').addClass('disabled');
                    } else {
                        $('#' + sectionId + '-filter-name').attr('disabled', false);
                        $('#' + sectionId + '-filter-save-add').removeClass('disabled');
                        $('#' + sectionId + '-filter-save-update').removeClass('disabled');
                    }
                }

                //Save
                $('#' + sectionId + '-save-add, #' + sectionId + '-save-update').click(function(e) {
                    e.preventDefault();

                    query = '';

                    var selectedFilter = $('#' + sectionId + '-filter-filters option:selected');

                    var tableData =
                        dataCollection[componentId][sectionId + '-filter'][sectionId + '-filter-table']['data'];

                    $.each(tableData, function(index, data) {
                        if (index === 0) {
                            query +=
                                '-|' +
                                data[sectionId + '-filter-field'] + '|' +
                                data[sectionId + '-filter-operator'] + '|' +
                                data[sectionId + '-filter-value'] + '&';
                        } else {
                            query +=
                                data[sectionId + '-filter-andor'] + '|' +
                                data[sectionId + '-filter-field'] + '|' +
                                data[sectionId + '-filter-operator'] + '|' +
                                data[sectionId + '-filter-value']
                                + '&';
                        }
                    });

                    var filterName = $('#' + sectionId + '-filter-name').val();

                    if (filterName === '') {
                        $('#' + sectionId + '-filter-name').addClass('is-invalid');
                        $('#' + sectionId + '-filter-name').focus(function() {
                            $(this).removeClass('is-invalid');
                        });
                        return;
                    }

                    //Save To Db
                    var postData = { };
                    postData['id'] = $('#' + sectionId + '-filter-id').val();
                    postData['name'] = filterName;
                    postData['conditions'] = query;
                    postData['component_id'] = $(selectedFilter).data()['component_id'];
                    postData['filter_type'] = '1';
                    postData[$('#security-token').attr('name')] = $('#security-token').val();

                    if ($('#' + sectionId + '-filter-default')[0].checked === true) {
                        postData['is_default'] = '1';
                        filterName = filterName + ' (Default)';
                    } else {
                        postData['is_default'] = '0';
                    }

                    var url = $(this).attr('href');

                    // if (postData['id'] !== '') {
                    //     url = 'filter/update';
                    // } else {
                    //     url = 'filter/add';
                    // }

                    //Update Filter
                    $.post(url, postData, function(response) {
                        if (response.responseCode === 0) {
                            PNotify.success({
                                'title' : response.responseMessage
                            });
                            if (response.filters) {
                                redoFiltersOptions(query, sectionId, response);
                            }
                        } else {
                            PNotify.error({
                                'title' : response.responseMessage
                            });
                        }
                        if (response.tokenKey && response.token) {
                            $('#security-token').attr('name', response.tokenKey);
                            $('#security-token').val(response.token);
                        }
                    }, 'json');

                    //Make Filter Call
                    $('#' + sectionId + '-filter-modal').modal('hide');
                    that._filterRunAjax(
                        1,
                        datatableOptions.paginationCounters.limit,
                        query
                    );

                    clearStoredData();
                });

                function redoFiltersOptions(query, sectionId, data) {
                    var filtersOptions = '';

                    $.each(data.filters, function(index, filter) {
                        filtersOptions += '<option ';
                        var filterName = filter['name'];

                        for (var filterColumn in filter) {
                            filtersOptions += 'data-' + filterColumn + '="' + filter[filterColumn] + '" ';
                        }

                        filtersOptions += 'data-value="' + filter['id'] + '" value="' + filter['id'] + '" ';

                        if (filter['is_default'] == '1') {
                            filterName = filter['name'] + ' (Default)';
                        }
                        if (filter['filter_type'] == '0') {
                            filterName = filter['name'] + ' (System)';
                        // } else if (filter['filter_type'] == '2') {
                        //     filterName = filter['name'] + ' (Shared)';
                        }
                        if (filter['shared_ids']) {
                            if (filter['employee_full_name']) {
                                filterName = filter['name'] + ' (Shared by ' + filter['employee_full_name'] + ')';
                            } else if (filter['account_email']) {
                                filterName = filter['name'] + ' (Shared by ' + filter['account_email'] + ')';
                            }
                        }

                        if (filter['conditions'] === query) {
                            filtersOptions += 'selected';
                        }

                        filtersOptions += '>' + filterName;
                        filtersOptions += '</option>';
                    });
                    $('#' + sectionId + '-filter-filters').empty().append(filtersOptions);
                    toggleFilterButtons(sectionId + '-filter');
                }

                function clearStoredData() {
                    $('#' + sectionId + '-filter-andor').val('and').trigger('change');
                    $('#' + sectionId + '-filter-field').val(null).trigger('change');
                    $('#' + sectionId + '-filter-field-error').remove();
                    $('#' + sectionId + '-filter-operator').val(null).trigger('change');
                    $('#' + sectionId + '-filter-operator-error').remove();
                    $('#' + sectionId + '-filter-value').val('');
                    $('#' + sectionId + '-filter-id').val('');
                    $('#' + sectionId + '-filter-name').val('');
                    $('#' + sectionId + '-filter-default')[0].checked = 0;

                    dataCollection[componentId][sectionId + '-filter']['datatables'][sectionId + '-filter-table']
                        .rows().clear().draw();

                    dataCollection[componentId][sectionId + '-filter'][sectionId + '-filter-table']['data'] = [];
                    $('#' + sectionId + '-filter-name').attr('disabled', true);
                    $('#' + sectionId + '-filter-name').val('New Filter');
                    $('#' + sectionId + '-filter-save-add').addClass('disabled');
                    $('#' + sectionId + '-filter-save-update').addClass('disabled');
                    $('#' + sectionId + '-filter-cancel-button').attr('hidden', true);
                    $('#' + sectionId + '-filter-update-button').attr('hidden', true);
                    $('#' + sectionId + '-filter-assign-button').attr('hidden', false);
                    $('#' + sectionId + '-filter-update-button').off();//Important
                }

                //Modal Close button
                $('#' + sectionId + '-modal .modal-close').click(function(e) {
                    e.preventDefault();

                    clearStoredData();
                });

                if ($('#listing-filters-quick').length === 1) {
                    var dataType, selectedId;

                    $('#listing-filters-quick a').click(function(e) {
                        e.preventDefault();

                        $('#' + sectionId + '-filter-quick').attr('disabled', false);
                        $('#' + sectionId + '-filter-quick').val('');
                        $('#' + sectionId + '-filter-search').attr('disabled', false);

                        $('#' + sectionId + '-filter-quick-prepend-dropdown-button span').text($(this).text().toUpperCase());

                        selectedId = $(this).data('id');

                        dataType = $('#' + sectionId + '-filter-field').find('[data-value="' + $(this).data('id') + '"]').data('datatype');
                    });

                    $('#' + sectionId + '-search').click(function(e) {
                        e.preventDefault();

                        if (dataType == 0) {
                            query = '-|' + selectedId + '|equals|' + $('#' + sectionId + '-filter-quick').val().trim() + '&';
                        } else {
                            query = '-|' + selectedId + '|like|%' + $('#' + sectionId + '-filter-quick').val().trim() + '%&';
                        }

                        that._filterRunAjax(
                            1,
                            datatableOptions.paginationCounters.limit,
                            query
                        );
                    });
                }
            }

            //Build listing datatable
            _proto._buildListingDatatable = function() {
                // For checkbox Sorting
                $.fn.dataTable.ext.order['dom-checkbox'] = function  ( settings, col ) {
                    return this.api().column( col, {order:'index'} ).nodes().map( function ( td ) {
                        return $('input', td).prop('checked') ? '1' : '0';
                    } );
                };
                // For radio sorting
                $.fn.dataTable.ext.order['dom-radio'] = function ( settings, col ) {
                    return this.api().column( col, {order:'index'} ).nodes().map( function ( td ) {
                        return $('input[type=radio]:checked', td).prop('checked') ? '1' : '0';
                    } );
                };

                thisOptions = dataCollection[componentId][sectionId];
                listColumns[thisOptions.listOptions.tableName] = [];

                datatableOptions = thisOptions.listOptions.datatable;
                datatableOptions.columns = JSON.parse(datatableOptions.columns);

                var selectOptions, dom, showHideExportButton, showHideColumnsButton;

                if (datatableOptions.showHideColumnsButton === 'true' ||
                    datatableOptions.showHideColumnsButton === '1' ||
                    datatableOptions.showHideExportButton === 'true' ||
                    datatableOptions.showHideExportButton === '1') {
                    dom =
                    '<"row mb-1"<"col-md-3 col-xs-12"B><"col-md-3 col-xs-12"l><"col-md-3 col-xs-12"f><"col-md-3 col-xs-12"p>>' +
                    '<"row mb-1"<"col"tr>>' +
                    '<"row"<"col-md-6 col-xs-12"i><"col-md-6 col-xs-12"p>>';
                } else {
                    dom =
                    '<"row mb-1"<"col-md-4 col-xs-12"l><"col-md-4 col-xs-12"f><"col-md-4 col-xs-12"p>>' +
                    '<"row mb-1"<"col"tr>>' +
                    '<"row"<"col-md-6 col-xs-12"i><"col-md-6 col-xs-12"p>>';
                }

                // ID Column
                if (datatableOptions.addIdColumn === 'true' || datatableOptions.addIdColumn === '1') {
                    if (!datatableOptions.columns.find(({name}) => name === 'id')) {
                        listColumns[thisOptions.listOptions.tableName].push({
                            data    : 'id',
                            title   : 'ID'
                        });
                    }
                }

                var columnsCount = 0;

                // All Columns (except ID and __control and replacedColumns)
                $.each(datatableOptions.columns[0], function(index,column) {
                    var disabled;

                    // disable column ordering
                    var disableColumnOrdering = datatableOptions.disableColumnsOrdering.includes(column.id);
                    if (disableColumnOrdering) {
                        disabled = false;
                    } else {
                        disabled = true;
                    }

                    //Bug with responsive datatable plugin. If the column has mobile keyword, the column is automatically converted into responsive.
                    var columnid;
                    if (column.id.search(/mobile/g) !== -1) {
                        columnid = column.id.replace(/mobile/i, 'mobil');//Just change the keyword to something else.
                    } else {
                        columnid = column.id
                    }

                    if (datatableOptions.colTextTruncate) {
                        classes = 'data-' + columnid + ' text-truncate dt-colTextTruncate';
                    } else {
                        classes = 'data-' + columnid;
                    }

                    if (datatableOptions.tableCompact) {
                        classes = classes + ' pb-1 pt-1';
                    }

                    listColumns[thisOptions.listOptions.tableName].push({
                        data            : column.id,
                        title           : column.name.toUpperCase(),
                        orderable       : disabled,
                        className       : classes
                    });
                    columnsCount++;
                });

                // Hide Columns
                var hideColumns = [];
                if (datatableOptions.hideIdColumn === 'true' || datatableOptions.hideIdColumn === '1') {
                    hideColumns.push(0);
                }

                if (columnsCount > datatableOptions.NoOfColumnsToShow) {
                    var colDiff = columnsCount - datatableOptions.NoOfColumnsToShow;
                    for (var i = 1; i <= colDiff; i++) {
                        hideColumns.push(columnsCount - i);
                    }
                }

                // Column Select
                if (datatableOptions.select === 'true' || datatableOptions.select === '1') {
                    selectOptions = {
                        style       : datatableOptions.selectStyle,
                        className   : 'bg-lightblue'
                    }
                    //Add class datatable-pointer to each row.
                    datatableOptions.createdRow = function (row) {
                        $(row).addClass("dataTable-pointer");
                    }
                } else {
                    selectOptions = false;
                }
                if (datatableOptions.selectAll === 'true' || datatableOptions.selectAll === '1') {
                    var selectAllNoneButton = ['selectAll','selectNone'];
                }

                // Column reorder disallow column 1st (id) and last (__control)
                if (datatableOptions.colReorder === 'true' || datatableOptions.colReorder === '1') {
                    datatableOptions.colReorder = $.extend(datatableOptions.colReorder, {
                        fixedColumnsLeft    : 1,//id
                        fixedColumnsRight   : 1,//__control
                    });
                } else {
                    datatableOptions.colReorder = false;
                }

                if (datatableOptions.stateSave === 'true' || datatableOptions.stateSave === '1') {
                    datatableOptions.stateSave = true;
                } else {
                    datatableOptions.stateSave = false;
                }

                if (datatableOptions.fixedHeader === 'true' || datatableOptions.fixedHeader === '1') {
                    datatableOptions.fixedHeader = true;
                } else {
                    datatableOptions.fixedHeader = false;
                }

                if (datatableOptions.searching === 'true' || datatableOptions.searching === '1') {
                    datatableOptions.searching = true;
                } else {
                    datatableOptions.searching = false;
                }

                if (datatableOptions.paging === 'true' || datatableOptions.paging === '1') {
                    datatableOptions.paging = true;
                } else {
                    datatableOptions.paging = false;
                }

                if (datatableOptions.showHideColumnsButton === 'true' || datatableOptions.showHideColumnsButton === '1') {
                    showHideColumnsButton =
                        {
                            extend          : 'colvis',
                            text            : function() {
                                                var totCols = listColumns[thisOptions.listOptions.tableName].length;
                                                var hiddenCols = hideColumns.length;
                                                var shownCols = totCols - hiddenCols;
                                                return '<i class="fas fa-eye fa-fw"></i> (' + shownCols + '/' + totCols + ')';
                                            },
                            className       : 'btn-sm btn-' + datatableOptions.showHideColumnsButtonType,
                            prefixButtons   : [{
                                                extend      : 'colvisGroup',
                                                text        : 'SHOW ALL',
                                                show        : ':hidden'
                                            },
                                            {
                                                extend      : 'colvisRestore',
                                                text        : 'RESTORE'
                                            }]
                        }
                }

                if (datatableOptions.showHideExportButton === 'true' || datatableOptions.showHideExportButton === '1') {
                    showHideExportButton =
                        {
                            extend          : 'collection',
                            text            : 'Export',
                            className       : 'btn-sm btn-' + datatableOptions.showHideColumnsButtonType,
                            buttons         : [{
                                                text            : 'Excel',
                                                title           : 'DataExport - ' + thisOptions.listOptions.componentName,
                                                extend          : 'excelHtml5',
                                                footer          : false,
                                                exportOptions   : {
                                                                    columns: ':visible'
                                                                }
                                            },
                                            {
                                                text            : 'CSV',
                                                extend          : 'csvHtml5',
                                                fieldSeparator  : ',',
                                                exportOptions   : {
                                                                    columns: ':visible'
                                                                }
                                            }
                                            ]
                        }
                }

                datatableOptions = $.extend(datatableOptions, {
                    columns         : listColumns[thisOptions.listOptions.tableName],
                    rowId           : 'id',
                    colReorder      : datatableOptions.colReorder,
                    order           : datatableOptions.order,
                    stateSave       : datatableOptions.stateSave,
                    fixedHeader     : datatableOptions.fixedHeader,
                    searching       : datatableOptions.searching,
                    responsive      : datatableOptions.responsive,
                    paging          : datatableOptions.paging,
                    lengthMenu      : datatableOptions.lengthMenu,
                    select          : selectOptions,
                    columnDefs      : [{
                                        visible         : false,
                                        targets         : hideColumns
                                    }],
                    dom             : dom,
                    buttons         : [showHideColumnsButton, showHideExportButton, selectAllNoneButton],
                    language       : {
                                        paginate        : {
                                                            previous    : '<i class="fa fa-angle-left"></i>',
                                                            next        : '<i class="fas fa-angle-right"></i>'
                                                        },
                                        zeroRecords     : datatableOptions.zeroRecords,
                                        infoEmpty       : 'No entries found',
                                        infoFiltered    : ' - filtered from _MAX_ shown entries',
                                        searchPlaceholder: 'Search shown ' + thisOptions.listOptions.componentName + '...',
                                        select          : {
                                            rows    : {
                                                    _   : 'Selected %d rows. Click the selected row again to deselect',
                                                    0   : '<i class="fas fa-fw fa-info-circle"></i>Click a row to select it',
                                                    1   : 'Selected 1 row. Click the selected row again to deselect'
                                                }
                                        },
                                        buttons         : {
                                            selectAll   : '<i class="fas fa-fw fa-check-double"></i>',
                                            selectNone  : '<span class="fa-stack">' +
                                                          '<i class="fas fa-fw fa-check-double fa-stack-1x"></i>' +
                                                          '<i class="fas fa-fw fa-ban fa-stack-2x text-danger"></i>' +
                                                          '</span>'
                                        }
                                    },
                    initComplete    : function() {
                                        //Remove button secondary
                                        $('.btn-' + datatableOptions.showHideColumnsButtonType).removeClass('btn-secondary');

                                        // Adjust hidden columns counter text in button
                                        $('#' + sectionId + '-table').on('column-visibility.dt', function(e) {
                                            var visCols = $('#' + sectionId + '-table thead tr:first th').length;
                                            //Below: The minus 2 because of the 2 extra buttons SHOW ALL and RESTORE
                                            var tblCols = $('.dt-button-collection a').length - 2;
                                            $('.buttons-colvis[aria-controls=' + sectionId + '-table] span').html('<i class="fa fa-eye fa-fw"></i> (' + visCols + '/' + tblCols + ')');
                                            thisOptions['datatable'].responsive.recalc();
                                            e.stopPropagation();
                                        });
                                    },
                    drawCallback    : function() {
                                        that._drawCallback();
                                    }
                });

                if (thisOptions.listOptions.postUrl) {
                    that._runDatatableAjax(thisOptions.listOptions.postParams);
                } else {
                    // Enable paging if data is more than 20 on static datatable
                    if (datatableOptions.pagination && datatableOptions.paginationCounters.filtered_items > 20) {
                        $.extend(thisOptions.listOptions.datatable, {
                            paging : true,
                        });
                    }
                    $('#listing-data-loader').hide();
                    $('#listing-primary-buttons').attr('hidden', false);
                    $('#listing-secondary-buttons').attr('hidden', false);
                    $('#listing-filters').attr('hidden', false);
                    that._tableInit(false);
                    that._registerEvents();
                }
            }

            _proto._runDatatableAjax = function(postData, reDraw) {
                var url = thisOptions.listOptions.postUrl;
                $.ajax({
                    url         : url,
                    method      : 'post',
                    dataType    : 'json',
                    data        : postData,
                    success     : function(response) {
                        if (response.responseCode != 0) {
                            PNotify.error({
                                title   : response.responseMessage
                            });
                        }
                        $('#listing-data-loader').hide();
                        $('#listing-primary-buttons').attr('hidden', false);
                        $('#listing-secondary-buttons').attr('hidden', false);
                        $('#listing-filters').attr('hidden', false);
                        $.extend(thisOptions.listOptions.datatable, JSON.parse(response.rows));
                        $('body').trigger(
                            {
                                'type'     : 'sectionWithListingLoaded'
                            }
                        );
                    }
                }).done(function(response) {
                    if (response.tokenKey && response.token) {
                        $('#security-token').attr('name', response.tokenKey);
                        $('#security-token').val(response.token);
                    }
                    that._tableInit(reDraw);
                    that._registerEvents();
                });
                    // TODO: fix card-body height when more rows are loaded.
                    // TODO: BULK Edit/Delete
                    //     // tableData[sectionId].buttons().container().appendTo('#products-list-buttons .col-sm-6:eq(0)');
                    //     // that.fixHeight('fixedHeight');
                    //     // $('#' + sectionId + '-list-table').on('length.dt', function() {
                    //     //     that.fixHeight('fixedHeight');
                    //     // });
                    //     // $('#' + sectionId + '-filter').on('collapsed.boxwidget expanded.boxwidget', function() {
                    //     //     that.fixHeight('fixedHeight');
                    //     // });
                    //     // $('#' + sectionId + '-filter-filters-apply').click(function() {
                    //     //     $('#' + sectionId + '-filter .box').trigger('collapse.boxwidget');
                    //     //     that.fixHeight('fixedHeight');
                    //     // });
                    //     // $('#' + sectionId + '-list').find('.dataTables_info').addClass('pull-right');
                    //     //  }
                    //     // });
                // });
            }

            //Initialize Table
            _proto._tableInit = function(reDraw) {
                // All Columns (except ID and __control)
                $.each(listColumns, function(index,column) {
                    // Ordering of checkbox and radio columns
                    for (var replaceColumn in datatableOptions.replaceColumns) {
                        if (replaceColumn === column.data) {
                            if (datatableOptions.replaceColumns[replaceColumn] === 'customSwitch') {
                                column.orderDataType = 'dom-checkbox';
                            } else if (datatableOptions.replaceColumns[replaceColumn] === 'radioButtons') {
                                column.orderDataType = 'dom-radio';
                            }
                        }
                    }
                });

                if (!reDraw) {
                    // Pagination
                    if (datatableOptions.pagination && datatableOptions.paginationCounters.filtered_items > 20) {
                        $.extend(thisOptions.listOptions.datatable, {
                            paging : true,
                            pagingType : 'simple',
                        });

                        datatableOptions['language']['zeroRecords'] = '<i class="fas fa-cog fa-spin"></i> Loading...';
                    }
                    if (datatableOptions.tableCompact) {
                        classes = 'data-actions pb-1 pt-1';
                    } else {
                        classes = 'data-actions';
                    }
                    // Control Column
                    if (datatableOptions.rowControls) {
                        listColumns[thisOptions.listOptions.tableName].push({
                            data        : '__control',
                            title       : 'ACTIONS',
                            orderable   : false,
                            className   : classes
                        });
                    }

                    if (thisOptions.customFunctions.beforeTableInit) {
                        thisOptions.customFunctions.beforeTableInit();
                    }

                    thisOptions['datatable'] = $('#' + thisOptions.listOptions.tableName).DataTable(datatableOptions);

                    if (thisOptions.customFunctions.afterTableInit) {
                        if (datatableOptions.headerClass) {
                            var thead = $('#' + thisOptions.listOptions.tableName).children('thead').children('tr');

                            if (thead) {
                                thead.addClass(datatableOptions.headerClass).css({'position':'relative','z-index':999});
                            }
                        }

                        thisOptions.customFunctions.afterTableInit();
                    }

                    thisOptions['datatable'].columns.adjust().responsive.recalc();

                    that._updateCounters();

                    // Datatable Events
                    //Responsive
                    thisOptions['datatable'].on('draw responsive-resize responsive-display', function() {
                        BazContentLoader.init({});
                        thisOptions['datatable'].columns.adjust().responsive.recalc();
                    });

                    //Toggle response rows open/close
                    thisOptions['datatable'].on('responsive-display', function(e, datatable, row, showHide) {
                        if (showHide) {
                            $($(row.node()).next('.child')).find('li').prepend(
                                '<i class="fa fas fa-fw fa-plus-circle text-info dtr-expand mr-1 dataTable-pointer"><i>'
                            );

                            that._changeResponsiveLiWidths($($(row.node()).next('.child')));

                            $($(row.node()).next('.child')).find('.dtr-expand').click(function() {
                                if ($(this).parent().is('.text-truncate')) {
                                    $(this).parent().removeClass('text-truncate dt-colTextTruncate');
                                    $(this).removeClass('fa-plus-circle text-info').addClass('fa-minus-circle text-danger');
                                } else {
                                    $(this).parent().addClass('text-truncate dt-colTextTruncate');
                                    $(this).removeClass('fa-minus-circle text-danger').addClass('fa-plus-circle text-info');
                                    that._changeResponsiveLiWidths($($(row.node()).next('.child')));
                                }
                            });
                        }
                    });

                    //Search
                    $('.dataTables_filter').find('input').keyup(function() {
                        if ($(this).val() === '') {
                            that._updateCounters();
                        }
                    });

                    thisOptions['datatable'].on('draw', function () {
                        if ($('#' + sectionId + '-table tbody td.dataTables_empty').length === 1) {
                            $('.dataTables_empty').last().html('No entries found');
                        }
                    });

                    //Length Change
                    thisOptions['datatable'].on('length.dt', function (e, settings, len) {
                        if (len === -1) {
                            len = datatableOptions.paginationCounters.filtered_items;
                        }

                        that._filterRunAjax(
                            datatableOptions.paginationCounters.first,
                            len,
                            query
                        );
                    });

                } else { //redraw used on pagination prev and next

                    if (thisOptions.customFunctions.beforeRedraw) {
                        thisOptions.customFunctions.beforeRedraw();
                    }

                    thisOptions['datatable'].rows.add(datatableOptions.data).draw();

                    if (thisOptions.customFunctions.afterRedraw) {
                        thisOptions.customFunctions.afterRedraw();
                    }

                    that._updateCounters();
                }

                if (datatableOptions.rowControls) {
                    BazContentLoader.init({});
                }
            }

            //Update width for open child
            _proto._changeResponsiveLiWidths = function(child) {
                var width = child.width();

                var titleWidth = ((20 * width) / 100) / 16;//rem
                var liWidth = width/16;

                child.find('.dtr-title').css({"width" : titleWidth + 'rem'});
                child.find('.dtr-title').parent('li').removeClass('dt-colTextTruncate');
                child.find('.dtr-data').parent('li').css({"width" : liWidth + 'rem'});
            }

            //Register __control(Action buttons)
            _proto._registerEvents = function() {
                // customSwitch Toggle Function
                // $('#' + sectionId + '-table .custom-switch input').each(function(index,rowSwitchInput) {
                //     $(rowSwitchInput).click(function() {
                //         var rowSwitchInputId = $(rowSwitchInput)[0].id;
                //         var url = dataCollection.env.rootPath + 'index.php?route=' + $(rowSwitchInput).data('switchactionurl');
                //         var columnId = $(rowSwitchInput).data('columnid');
                //         var checked = $(rowSwitchInput).is('[checked]');
                //         var columnsDataToInclude = $(rowSwitchInput).data('switchactionincludecolumnsdata').split(',');
                //         var rowData;
                //         rowData = thisOptions['datatable'].row($(this).parents('tr')).data();
                //         if (checked) {
                //             rowData[columnId] = 0;
                //             $(rowSwitchInput).attr('checked', false);
                //             document.getElementById(rowSwitchInputId).checked = false;
                //         } else {
                //             rowData[columnId] = 1;
                //             $(rowSwitchInput).attr('checked', true);
                //             document.getElementById(rowSwitchInputId).checked = true;
                //         }
                //         var name = $(rowSwitchInput).parents('td').siblings('.data-' + $(rowSwitchInput).data('notificationtextfromcolumn')).html();
                //         var switchOnText = name + ' enabled';
                //         var switchOffText = name + ' disabled';
                //         if (checked) {
                //             Swal.fire({
                //                 title                       : '<span class="text-danger"> Disable ' + name + '?</span>',
                //                 icon                        : 'question',
                //                 background                  : 'rgba(0,0,0,.8)',
                //                 backdrop                    : 'rgba(0,0,0,.6)',
                //                 customClass                 : {
                //                     'confirmButton'             : 'btn btn-danger text-uppercase',
                //                     'cancelButton'              : 'ml-2 btn btn-secondary text-uppercase',
                //                 },
                //                 buttonsStyling              : false,
                //                 confirmButtonText           : 'Disable',
                //                 showCancelButton            : true,
                //                 keydownListenerCapture      : true,
                //                 allowOutsideClick           : false,
                //                 allowEscapeKey              : false,
                //                 onOpen                      : function() {
                //                     swalSound.play();
                //                 }
                //             }).then((result) => {
                //                 if (result.value) {
                //                     runAjax(false, switchOffText);
                //                 } else {
                //                     $(rowSwitchInput).attr('checked', true);
                //                     document.getElementById(rowSwitchInputId).checked = true;
                //                 }
                //             });
                //         } else {
                //             runAjax(true, switchOnText);
                //         }
                //         function runAjax(status, notificationText) {
                //             var dataToSubmit = { };
                //             for (var data in rowData) {
                //                 if (columnsDataToInclude.includes(data)) {
                //                     dataToSubmit[data] = rowData[data];
                //                 }
                //             }
                //             $.ajax({
                //                 url         : url,
                //                 method      : 'post',
                //                 data        : dataToSubmit,
                //                 dataType    : 'json',
                //                 success     : function(response) {
                //                     if (response.responseCode === 0) {
                //                         PNotify.success({
                //                             title           : notificationText,
                //                             cornerClass     : 'ui-pnotify-sharp'
                //                         });
                //                         $(rowSwitchInput).attr('checked', status);
                //                         document.getElementById(rowSwitchInputId).checked = true;
                //                     } else {
                //                         PNotify.error({
                //                             title           : 'Error!',
                //                             cornerClass     : 'ui-pnotify-sharp'
                //                         });
                //                         $(rowSwitchInput).attr('checked', false);
                //                         document.getElementById(rowSwitchInputId).checked = false;
                //                     }
                //                     pnotifySound.play();
                //                     if (response.tokenKey && response.token) {
                //                         $('#security-token').attr('name', response.tokenKey);
                //                         $('#security-token').val(response.token);
                //                     }
                //                 }
                //             });
                //         }
                //     });
                // });

                // RadioButtons
                // $('#' + sectionId + '-table .btn-group-toggle label').each(function(index,radioButtonsLabel) {
                //     $(radioButtonsLabel).click(function() {
                //         var currentCheckedId, currentCheckedLabel;
                //         $(this).siblings('label').children('input').each(function(index,sibling) {
                //             if (sibling.checked) {
                //                 currentCheckedId = sibling.id;
                //                 currentCheckedLabel = sibling.parentElement;
                //             } else if (sibling.defaultChecked) {
                //                 currentCheckedId = sibling.id;
                //                 currentCheckedLabel = sibling.parentElement;
                //             }
                //         });
                //         var thisId = $(this).children('input')[0].id;
                //         var url = dataCollection.env.rootPath + 'index.php?route=' + $(this).children('input').data('radiobuttonsactionurl');
                //         var columnId = $(this).children('input').data('columnid');
                //         var dataValue = $(this).children('input').data('value');
                //         var checked = false;
                //         if ($(this).children('input').is('[checked]') || $(this).children('input')[0].defaultChecked) {
                //             checked = true;
                //         }
                //         var radioChangeText = $(this).parents('td').siblings('.data-' + $(this).children('input').data('notificationtextfromcolumn')).html() + ' ' +
                //                                 $(this).children('input').data('columnid') + ' changed';
                //         if (!checked) {
                //             Swal.fire({
                //                 title                       : '<span class="text-danger"> Change ' +
                //                                                 $(this).parents('td').siblings('.data-' +
                //                                                 $(this).children('input').data('notificationtextfromcolumn')).html() + ' ' +
                //                                                 $(this).children('input').data('columnid') +
                //                                                 '?</span>',
                //                 icon                        : 'question',
                //                 background                  : 'rgba(0,0,0,.8)',
                //                 backdrop                    : 'rgba(0,0,0,.6)',
                //                 buttonsStyling              : false,
                //                 confirmButtonText           : 'Change',
                //                 customClass                 : {
                //                     'confirmButton'             : 'btn btn-danger text-uppercase',
                //                     'cancelButton'              : 'ml-2 btn btn-secondary text-uppercase',
                //                 },
                //                 showCancelButton            : true,
                //                 keydownListenerCapture      : true,
                //                 allowOutsideClick           : false,
                //                 allowEscapeKey              : false,
                //                 onOpen                      : function() {
                //                     swalSound.play();
                //                 }
                //             }).then((result) => {
                //                 if (result.value) {
                //                     runAjax(false, radioChangeText);
                //                 } else {
                //                     $(this).removeClass('focus active');
                //                     $('#' + currentCheckedId).attr('checked', true);
                //                     document.getElementById(currentCheckedId).checked = true;
                //                     $(currentCheckedLabel).addClass('focus active');
                //                 }
                //             });
                //         }

                //         function runAjax(status, notificationText) {
                //             var columnsDataToInclude = $('#' + thisId).data('radiobuttonsactionincludecolumnsdata').split(',');
                //             var rowData = thisOptions['datatable'].row($('#' + thisId).parents('tr')).data();
                //             var dataToSubmit = { };
                //             for (var data in rowData) {
                //                 if (columnsDataToInclude.includes(data)) {
                //                     if (columnId === data) {
                //                         dataToSubmit[data] = dataValue;
                //                     } else {
                //                         dataToSubmit[data] = rowData[data];
                //                     }
                //                 }
                //             }
                //             $.ajax({
                //                 url         : url,
                //                 method      : 'post',
                //                 data        : dataToSubmit,
                //                 dataType    : 'json',
                //                 success     : function(response) {
                //                     if (response.responseCode === 1) {
                //                         PNotify.removeAll()
                //                         PNotify.success({
                //                             title           : notificationText,
                //                             cornerClass     : 'ui-pnotify-sharp'
                //                         });
                //                         $('#' + currentCheckedId).attr('checked', false);
                //                         document.getElementById(currentCheckedId).checked = false;
                //                         $('#' + thisId).attr('checked', true);
                //                         document.getElementById(thisId).checked = true;
                //                     } else {
                //                         PNotify.error({
                //                             title           : 'Error!',
                //                             cornerClass     : 'ui-pnotify-sharp'
                //                         });
                //                         $('#' + thisId).parent('label').removeClass('focus active');
                //                         $('#' + thisId).attr('checked', false);
                //                         document.getElementById(thisId).checked = false;
                //                         $('#' + currentCheckedId).attr('checked', true);
                //                         document.getElementById(currentCheckedId).checked = true;
                //                         $(currentCheckedLabel).addClass('focus active');
                //                     }
                //                     pnotifySound.play();
                //                     if (response.tokenKey && response.token) {
                //                         $('#security-token').attr('name', response.tokenKey);
                //                         $('#security-token').val(response.token);
                //                     }
                //                 }
                //             });
                //         }
                //     });
                // });

                // Deleting Row (element .rowRemove)
                $('#' + sectionId + '-table .rowRemove').each(function(index,rowRemove) {
                    $(rowRemove).off();
                    $(rowRemove).click(function(e) {
                        e.preventDefault();
                        var thisButton = this;
                        var url = $(this).attr('href');
                        var deleteText = $(this).parents('td').siblings('.data-' + $(this).data('notificationtextfromcolumn')).text();
                        var dataToSend = { };
                        dataToSend.id = thisOptions['datatable'].row($(thisButton).parents('tr')).id();
                        Swal.fire({
                            title                       : '<span class="text-danger"> Delete ' + deleteText + '?</span>',
                            icon                        : 'question',
                            background                  : 'rgba(0,0,0,.8)',
                            backdrop                    : 'rgba(0,0,0,.6)',
                            buttonsStyling              : false,
                            confirmButtonText           : 'Delete',
                            customClass                 : {
                                'confirmButton'             : 'btn btn-danger text-uppercase',
                                'cancelButton'              : 'ml-2 btn btn-secondary text-uppercase',
                            },
                            showCancelButton            : true,
                            keydownListenerCapture      : true,
                            allowOutsideClick           : false,
                            allowEscapeKey              : false,
                            didOpen                      : function() {
                                swalSound.play();
                            }
                        }).then((result) => {
                            if (result.value) {
                                if (datatableOptions.sendConfirmRemove === 'true' || datatableOptions.sendConfirmRemove === '1') {
                                    dataToSend.confirm = '1';
                                }
                                dataToSend[$('#security-token').attr('name')] = $('#security-token').val();

                                $.ajax({
                                    url         : url,
                                    method      : 'post',
                                    dataType    : 'json',
                                    data        : dataToSend,
                                    success     : function(response) {
                                        if (response.tokenKey && response.token) {
                                            $('#security-token').attr('name', response.tokenKey);
                                            $('#security-token').val(response.token);
                                        }
                                        if (response.responseCode != 0) {
                                            PNotify.error({
                                                title           : response.responseMessage,
                                            });
                                        }
                                    }
                                }).done(function(response) {
                                    if (response.responseCode === 0) {
                                        that._filterRunAjax(
                                            datatableOptions.paginationCounters.current,
                                            datatableOptions.paginationCounters.limit,
                                            query
                                        );

                                        if (dataCollection[componentId][sectionId]['afterRowRemove']) {
                                            dataCollection[componentId][sectionId]['afterRowRemove']();
                                        }
                                    }
                                });
                            }
                        });
                    });
                });

                if ($('.btn-tool-reset-cache').length === 1) {
                    $('.btn-tool-reset-cache').off();
                    $('.btn-tool-reset-cache').click(function(e) {
                        e.preventDefault();

                        that._filterRunAjax(
                            1,
                            datatableOptions.paginationCounters.limit,
                            query,
                            true
                        );
                    });
                }
                $('body').trigger('sectionWithListingEventsRegistered');
            }

            _proto._updateCounters = function() {
                var counters = { };

                counters.total = datatableOptions.paginationCounters.total_items;
                counters.filtered_total = datatableOptions.paginationCounters.filtered_items;
                counters.end = datatableOptions.paginationCounters.limit * datatableOptions.paginationCounters.current;
                counters.start = (counters.end - datatableOptions.paginationCounters.limit) + 1;

                if (datatableOptions.paginationCounters.current === datatableOptions.paginationCounters.last) {
                    counters.end = datatableOptions.paginationCounters.filtered_items;
                }
                if (query && filter) {
                    $('#' + sectionId + '-table_info').empty().html(
                        "Showing " + counters.start + " to " + counters.end +
                        " of " + counters.filtered_total + " filtered entries (Total entries: " + counters.total + ")"
                    );
                } else {
                    $('#' + sectionId + '-table_info').empty().html(
                        "Showing " + counters.start + " to " + counters.end + " of " + counters.filtered_total + " entries"
                    );
                }
            }

            _proto._filterRunAjax = function(page = null, limit = null, filterQuery = null, resetCache = false) {
                if (!page) {
                    page = datatableOptions.paginationCounters.current;
                }
                if (!limit) {
                    limit = datatableOptions.paginationCounters.limit;
                }
                if (!filterQuery) {
                    filterQuery = query;
                }

                thisOptions['datatable'].rows().clear().draw();
                $('.dataTables_empty').last().html('<i class="fas fa-cog fa-spin"></i> Loading...');

                var postData = { };
                postData['page'] = page;
                postData['limit'] = limit;

                if (filterQuery.conditions || filterQuery.order) {
                    postData['conditions'] = filterQuery.conditions;
                    postData['order'] = filterQuery.order;
                } else {
                    postData['conditions'] = filterQuery;
                }

                if (resetCache) {
                    postData['resetCache'] = 'true';
                }

                that._runDatatableAjax(postData, true);
            }

            _proto._drawCallback = function() {
                if (datatableOptions.pagination &&
                    datatableOptions.paginationCounters.filtered_items > 20 &&
                    (datatableOptions.paginationCounters.filtered_items !== datatableOptions.paginationCounters.limit)
                ) {
                    if (datatableOptions.paginationCounters.current !== datatableOptions.paginationCounters.first) {
                        $('.paginate_button.previous').removeClass('disabled');
                        $('.paginate_button.previous').click(function() {
                            that._filterRunAjax(
                                datatableOptions.paginationCounters.previous,
                                datatableOptions.paginationCounters.limit,
                                query
                            );
                        });
                    }
                    if (datatableOptions.paginationCounters.current !== datatableOptions.paginationCounters.last) {
                        $('.paginate_button.next').removeClass('disabled');
                        $('.paginate_button.next').click(function() {
                            that._filterRunAjax(
                                datatableOptions.paginationCounters.next,
                                datatableOptions.paginationCounters.limit,
                                query
                            );
                        });
                    }
                }
            }

            BazContentSectionWithListing._jQueryInterface = function _jQueryInterface(options) {
                dataCollection = window['dataCollection'];
                componentId = $(this).parents('.component')[0].id;
                sectionId = $(this)[0].id;

                dataCollection[componentId][sectionId]['BazContentSectionWithListing'] = $(this).data(DATA_KEY);
                options = $.extend({}, Default, options);

                if (!dataCollection[componentId][sectionId]['BazContentSectionWithListing']) {
                    dataCollection[componentId][sectionId]['BazContentSectionWithListing'] = new BazContentSectionWithListing($(this), options);
                    $(this).data(DATA_KEY, typeof options === 'string' ? 'options need to be an object and not string' : options);
                    dataCollection[componentId][sectionId]['BazContentSectionWithListing']._init(options);
                } else {
                    delete dataCollection[componentId][sectionId]['BazContentSectionWithListing'];
                    dataCollection[componentId][sectionId]['BazContentSectionWithListing'] = new BazContentSectionWithListing($(this), options);
                    $(this).data(DATA_KEY, typeof options === 'string' ? 'options need to be an object and not string' : options);
                    dataCollection[componentId][sectionId]['BazContentSectionWithListing']._init(options);
                }
            };

        return BazContentSectionWithListing;

        }();

    $(document).on('libsLoadComplete bazContentLoaderAjaxComplete bazContentWizardAjaxComplete', function() {
        if ($('.sectionWithListingFilter').length > 0) {
            $('.sectionWithListingFilter').each(function() {
                BazContentSectionWithListing._jQueryInterface.call($(this));
            });
        }
        if ($('.sectionWithListingDatatable').length > 0) {
            $('.sectionWithListingDatatable').each(function() {
                BazContentSectionWithListing._jQueryInterface.call($(this));
            });
        }
    });

    $.fn[NAME] = BazContentSectionWithListing._jQueryInterface;
    $.fn[NAME].Constructor = BazContentSectionWithListing;

    $.fn[NAME].noConflict = function () {
        $.fn[NAME] = JQUERY_NO_CONFLICT;
        return BazContentSectionWithListing._jQueryInterface;
    };

    return BazContentSectionWithListing;
}(jQuery);

exports.BazContentSectionWithListing = BazContentSectionWithListing;

Object.defineProperty(exports, '__esModule', { value: true });

}));
/* exported BazContentSectionWithStorage */
/* globals  */
/*
* @title                    : BazContentSectionWithStorage
* @description              : Baz Storage Lib
* @developer                : guru@bazaari.com.au
* @usage                    : BazContentSectionWithStorage._function_(_options_);
* @functions                :
* @options                  :
*/

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
// var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var BazContentSectionWithStorage = function() {
    var BazContentSectionWithStorage = void 0;
    var dataCollection = window.dataCollection;
    var componentId, sectionId, treeId, tableId, dropzoneId, fileId, modalId, thisOptions, datatableOptions, files;
    var dateAdded, dateChanged, extractDateAdded, extractDateChanged, dataSize, dataStatus;
    var storageURL = window.dataCollection.env.rootPath + 'index.php?route=common/storage/view';

    // Error
    // function error(errorMsg) {
    //     throw new Error(errorMsg);
    // }

    //Header
    function init(options) {
        componentId = $(options.storageId).parents('.component')[0].id;
        sectionId = $(options.storageId)[0].id;
        treeId = sectionId + '-tree';
        tableId = sectionId + '-table';
        dropzoneId = sectionId + '-dropzone';
        fileId = sectionId + '-file';
        modalId = sectionId + '-modal';

        if (!dataCollection[componentId]) {
            dataCollection[componentId] = { };
        }
        if (!dataCollection[componentId][sectionId]) {
            dataCollection[componentId][sectionId] = { };
        }

        fixHeight();
        initTable();
        initDz();
        getStorageList();
        registerButtons();
    }

    function fixHeight() {
        $(document).on('heightFixed', function() {
            var bodyHeight = $('#' + sectionId + '-card .card-body').height();
            var buttonsHeight = $('#' + sectionId + '-buttons-div').height();
            var storageContentHeight = bodyHeight - buttonsHeight;
            $('#' + sectionId + '-content-div').css({
                'min-height'    : storageContentHeight,
                'max-height'    : storageContentHeight,
            });
            $('div.dataTables_scrollBody').css({'max-height':storageContentHeight - 100});
            $('#' + treeId + '-div').css({'max-height':storageContentHeight - 10, 'overflow': 'auto'})
        });
    }

    function initTable() {
        var listColumns = { };
        var selectOptions, rowId;
        thisOptions = dataCollection[componentId][sectionId][tableId];
        thisOptions['rowsData'] = { };
        datatableOptions = thisOptions.listOptions.datatable;
        listColumns[thisOptions.listOptions.tableName] = [];
        var hideColumns = [];

        $.each(datatableOptions.columns, function(index,column) {
            listColumns[thisOptions.listOptions.tableName].push({
                data            : column,
                title           : column.toUpperCase()
            });
        });
        listColumns[thisOptions.listOptions.tableName][1]['title'] = 'NAME';
        listColumns[thisOptions.listOptions.tableName][2]['title'] = 'SIZE (kB)';
        listColumns[thisOptions.listOptions.tableName][3]['title'] = 'MODIFIED';

        // Hide ID Column
        hideColumns.push(0);
        // Number of Columns to show/hide
        if (!datatableOptions.NoOfColumnsToShow) {
            datatableOptions.NoOfColumnsToShow = 4;
        }
        if (datatableOptions.columns.length > datatableOptions.NoOfColumnsToShow) {
            var colDiff = datatableOptions.columns.length - datatableOptions.NoOfColumnsToShow;
            for (var i = 1; i <= colDiff; i++) {
                hideColumns.push(datatableOptions.columns.length - i);
            }
        }

        // Column Select
        if (datatableOptions.select) {
            selectOptions = {
                style       : datatableOptions.selectStyle,
                className   : 'bg-info'
            }
        } else {
            selectOptions = false;
        }

        datatableOptions = $.extend(datatableOptions, {
            columns         : listColumns[thisOptions.listOptions.tableName],
            rowId           : 'id',
            fixedHeader     : datatableOptions.fixedHeader,
            responsive      : datatableOptions.responsive,
            paging          : false,
            scrollY         : 100,
            scrollCollapse  : true,
            select          : selectOptions,
            searching       : false,
            lengthMenu      : false,
            columnDefs      : [{
                                visible         : false,
                                targets         : hideColumns
                            },
                            { "width": "60%", "targets": 1 }],
            language       : {
                                paginate        : {
                                                    previous    : '<i class="fa fa-angle-left"></i>',
                                                    next        : '<i class="fas fa-angle-right"></i>'
                                                },
                                zeroRecords     : datatableOptions.zeroRecords,
                                info            : 'Showing _START_ to _END_ of _TOTAL_ files',
                                infoEmpty       : '',
                                select          : {
                                    rows    : {
                                            _   : 'Selected %d files. Click again to deselect file',
                                            0   : '',
                                            1   : 'Selected 1 file. Click again to deselect file'
                                        }
                                }
                            },
            initComplete    : function() {
                            },
            // drawCallback    : function() {
            //                     drawCallback();
            //                 }
        });

        // Pagination
        // if (datatableOptions.paging) {
        //     $.extend(thisOptions.listOptions.datatable, {
        //         paging : true,
        //         pagingType : 'simple',
        //     });
        //     datatableOptions['language']['zeroRecords'] = '<i class="fas fa-cog fa-spin"></i> Loading...';
        // }

        thisOptions['datatable'] = $('#' + thisOptions.listOptions.tableName).DataTable(datatableOptions);
        // Select
        thisOptions['datatable']
            .on('select', function(e, dt, type, indexes) {
                rowId = thisOptions['datatable'].row(indexes).data().id;
                thisOptions['rowsData'][rowId] = { };
                thisOptions['rowsData'][thisOptions['datatable'].row(indexes).data().id] = thisOptions['datatable'].row(indexes).data();
                updateMimeIcon(thisOptions['rowsData'][thisOptions['datatable'].row(indexes).data().id]);
                updateInfo('files', thisOptions['rowsData'], true);
                $('#' + fileId + '-info').attr('disabled', false);
            })
            .on('deselect', function(e, dt, type, indexes) {
                rowId = thisOptions['datatable'].row(indexes).data().id;
                delete thisOptions['rowsData'][rowId];
                if (Object.keys(thisOptions['rowsData']) > 0) {
                    updateInfo('files', thisOptions['rowsData'], false, rowId);
                } else {
                    $('#' + fileId + '-info').attr('disabled', true);
                }
            });
    }

    function updateMimeIcon(rowData) {
        if (rowData['mime'] === 'text/plain') {
            rowData['mime_icon'] = 'fas fa-fw fa-file-alt';
        } else if (rowData['mime'] === 'application/pdf') {
            rowData['mime_icon'] = 'fas fa-fw fa-file-pdf';
        } else if (rowData['mime'] === 'application/msword' ||
                    rowData['mime'] === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
            rowData['mime_icon'] = 'fas fa-fw fa-file-word';
        } else if (rowData['mime'] === 'text/csv') {
            rowData['mime_icon'] = 'fas fa-fw fa-file-csv';
        } else if (rowData['mime'] === 'application/vnd.ms-excel' ||
                    rowData['mime'] === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
            rowData['mime_icon'] = 'fas fa-fw fa-file-excel';
        } else if (rowData['mime'] === 'image/jpeg' ||
                    rowData['mime'] === 'image/png' ||
                    rowData['mime'] === 'image/bmp' ||
                    rowData['mime'] === 'image/gif') {
            rowData['mime_icon'] = 'fas fa-fw fa-file-image';
        } else {
            rowData['mime_icon'] = 'fas fa-fw fa-file';
        }
    }

    function redrawTable(data) {
         $.fn.dataTable.settings[0]["oLanguage"]["select"]["rows"][0] = "<i class=\"fas fa-fw fa-info-circle\"></i>Click row to select file";
        thisOptions['datatable'].rows.add(data).draw();
        thisOptions['rowsData'] = { };
        $('#' + fileId + '-info').attr('disabled', true);
    }

    function initDz() {
        dataCollection[componentId][sectionId][dropzoneId]['dropzone'] = $('#' + dropzoneId).dropzone(dataCollection[componentId][sectionId][dropzoneId]);
    }

    function getStorageList() {
        doAjax(storageURL, { }, 'get', null);
    }

    function getStorageItems(data) {
        if (!data.node.original['data-type'] || data.node.original['data-type'] === '1') {
            var postData;
            $('#' + treeId).jstree(true).set_icon(data.node, 'fas fa-cog fa-fw fa-spin');
            if (data.node.parent === '#') {
                postData = {'folder_id' : data.node.li_attr['data-id']};
            } else {
                postData = {'folder_id' : data.node.original['data-id']};
            }
            doAjax(storageURL, postData, 'post', data);
            updateInfo('folder', data);
        } else {
            updateInfo('folder', data);
        }
    }

    function updateInfo(type, data, select, deselectId) {
        if (type === 'folder') {
            if ($('#' + modalId + '-info-content').length > 0) {
                $('#' + modalId + '-info-content').remove();
            }
            $('#' + modalId + ' .modal-body').append(
                '<div class="row" id="' + modalId + '-info-content">' +
                    '<div class="col border p-2">' +
                        '<div class="row" id="' + modalId + '-preview"></div>' +
                        '<div class="row" id="' + modalId + '-name"></div>' +
                        '<div class="row" id="' + modalId + '-description"></div>' +
                        '<div class="row" id="' + modalId + '-size"></div>' +
                        '<div class="row" id="' + modalId + '-added"></div>' +
                        '<div class="row" id="' + modalId + '-modified"></div>' +
                        '<div class="row" id="' + modalId + '-location"></div>' +
                        '<div class="row" id="' + modalId + '-status"></div>' +
                    '</div>' +
                '</div>'
            );

            if (data.node.parent !== '#') {
                if (data.node.original['data-added'] !== '0') {
                    dateAdded = new Date(data.node.original['data-added'] * 1000);
                    extractDateAdded = dateAdded.toDateString() + ' ' + dateAdded.toTimeString();
                }
                if (data.node.original['data-changed'] !== '0') {
                    dateChanged = new Date(data.node.original['data-changed'] * 1000);
                    extractDateChanged = dateChanged.toDateString() + ' ' + dateChanged.toTimeString();
                } else {
                    extractDateChanged = extractDateAdded;
                }

                if (data.node.original['data-size'] === null) {
                    dataSize = '-';
                } else {
                    dataSize = data.node.original['data-size'] + ' KB';
                }

                if (data.node.original['data-status'] === '0' || data.node.original['data-status'] === '1') {
                    dataStatus = '<span class="badge badge-warning">Processing...</span>';
                } else if (data.node.original['data-status'] === '2') {
                    dataStatus = '<span class="badge badge-success">Ok</span>';
                } else if (data.node.original['data-status'] === '3' || data.node.original['data-status'] === '4') {
                    dataStatus = '<span class="badge badge-danger">Error</span>';
                }

                $('#' + modalId + '-preview').empty().append(
                    '<div class="col text-center m-1 p-1">' +
                        '<span><i class="' + data.node.original['data-icon'] + ' fa-6x"></i></span>' +
                    '</div>'
                );
                $('#' + modalId + '-name').empty().append(
                    '<div class="col-md-2 col-xs-12">' +
                        '<span class="text-upper text-bold">Name</span>' +
                    '</div>' +
                    '<div class="col-md-10 col-xs-12" >' +
                        '<span>: ' + data.node.text + '</span>' +
                    '</div>'
                );
                $('#' + modalId + '-size').empty().append(
                    '<div class="col-md-2 col-xs-12">' +
                        '<span class="text-upper text-bold">Size</span>' +
                    '</div>' +
                    '<div class="col-md-10 col-xs-12" >' +
                        '<span>: ' + dataSize + '</span>' +
                    '</div>'
                );
                $('#' + modalId + '-added').empty().append(
                    '<div class="col-md-2 col-xs-12">' +
                        '<span class="text-upper text-bold">Added On</span>' +
                    '</div>' +
                    '<div class="col-md-10 col-xs-12" >' +
                        '<span>: ' + extractDateAdded + '</span>' +
                    '</div>'
                );
                $('#' + modalId + '-modified').empty().append(
                    '<div class="col-md-2 col-xs-12">' +
                        '<span class="text-upper text-bold">Modified On</span>' +
                    '</div>' +
                    '<div class="col-md-10 col-xs-12" >' +
                        '<span>: ' + extractDateChanged + '</span>' +
                    '</div>'
                );
                $('#' + modalId + '-location').empty().append(
                    '<div class="col-md-2 col-xs-12">' +
                        '<span class="text-upper text-bold">Location</span>' +
                    '</div>' +
                    '<div class="col-md-10 col-xs-12" >' +
                        '<span>: ' + 'location' + '</span>' +
                    '</div>'
                );
                $('#' + modalId + '-status').empty().append(
                    '<div class="col-md-2 col-xs-12">' +
                        '<span class="text-upper text-bold">Status</span>' +
                    '</div>' +
                    '<div class="col-md-10 col-xs-12" >' +
                        '<span>: ' + dataStatus + '</span>' +
                    '</div>'
                );
            } else {
                $('#' + modalId + '-preview').empty().append(
                    '<div class="col text-center m-1 p-1">' +
                        '<span><i class="fas fa-fw fa-hdd fa-6x"></i></span>' +
                    '</div>'
                );
                $('#' + modalId + '-name').empty().append(
                    '<div class="col-md-2 col-xs-12">' +
                        '<span class="text-upper text-bold">Name</span>' +
                    '</div>' +
                    '<div class="col-md-10 col-xs-12" >' +
                        '<span>: ' + data.node.text + '</span>' +
                    '</div>'
                );
                $('#' + modalId + '-description').empty().append(
                    '<div class="col-md-2 col-xs-12">' +
                        '<span class="text-upper text-bold">Description</span>' +
                    '</div>' +
                    '<div class="col-md-10 col-xs-12" >' +
                        '<span>: ' + data.node.data.description + '</span>' +
                    '</div>'
                );
            }
        } else if (type === 'files') {
            if (select && Object.keys(data).length === 1) {
                if ($('#' + modalId + '-info-content').length > 0) {
                    $('#' + modalId + '-info-content').remove();
                }
                $('#' + modalId + ' .modal-body').append(
                    '<div class="row" id="' + modalId + '-info-content">' +
                        '<div class="col card-body p-0">' +
                            '<ul class="nav nav-tabs" id="' + modalId + '-info-content-files-tabs-links" role="tablist">' +
                                '<li class="nav-item">' +
                                    '<a class="nav-link active" id="' + modalId + '-info-content-files-tabs-' + Object.keys(data)[0] + '-tab" ' +
                                    'data-toggle="pill" href="#' + modalId + '-info-content-files-tabs-' + Object.keys(data)[0] + '" ' +
                                    'role="tab" aria-controls="' + modalId + '-info-content-files-tabs-' + Object.keys(data)[0] + '" ' +
                                    'aria-selected="true">' + data[Object.keys(data)[0]]['entry'] + '</a>' +
                                '</li>' +
                            '</ul>' +
                            '<div class="tab-content" id="' + modalId + '-info-content-files-tabs-content">' +
                                '<div class="tab-pane fade show active" id="' + modalId + '-info-content-files-tabs-' + Object.keys(data)[0] + '" ' +
                                'role="tabpanel" aria-labelledby="' + modalId + '-info-content-files-tabs-' + Object.keys(data)[0] + '">' +
                                    '<div class="col border-bottom p-2">' +
                                        '<div class="row" id="' + modalId + '-preview-' + Object.keys(data)[0] + '">' +
                                            '<div class="col-md-12 text-center">' +
                                                '<i class="' + data[Object.keys(data)[0]]['mime_icon'] + ' fa-6x"></i>' +
                                            '</div>' +
                                        '</div>' +
                                        '<div class="row" id="' + modalId + '-name-' + Object.keys(data)[0] + '">' +
                                            '<div class="col-md-2 col-xs-12">' +
                                                '<span class="text-upper text-bold">Name</span>' +
                                            '</div>' +
                                            '<div class="col-md-10 col-xs-12" >' +
                                                '<span>: ' + data[Object.keys(data)[0]]['entry'] + '</span>' +
                                            '</div>' +
                                        '</div>' +
                                        '<div class="row" id="' + modalId + '-size-' + Object.keys(data)[0] + '">' +
                                            '<div class="col-md-2 col-xs-12">' +
                                                '<span class="text-upper text-bold">Size</span>' +
                                            '</div>' +
                                            '<div class="col-md-10 col-xs-12" >' +
                                                '<span>: ' + data[Object.keys(data)[0]]['entry_size'] + ' kB</span>' +
                                            '</div>' +
                                        '</div>' +
                                        '<div class="row" id="' + modalId + '-added-' + Object.keys(data)[0] + '">' +
                                            '<div class="col-md-2 col-xs-12">' +
                                                '<span class="text-upper text-bold">Added</span>' +
                                            '</div>' +
                                            '<div class="col-md-10 col-xs-12" >' +
                                                '<span>: ' + data[Object.keys(data)[0]]['added'] + '</span>' +
                                            '</div>' +
                                        '</div>' +
                                        '<div class="row" id="' + modalId + '-modified-' + Object.keys(data)[0] + '">' +
                                            '<div class="col-md-2 col-xs-12">' +
                                                '<span class="text-upper text-bold">Modified</span>' +
                                            '</div>' +
                                            '<div class="col-md-10 col-xs-12" >' +
                                                '<span>: ' + data[Object.keys(data)[0]]['changed'] + '</span>' +
                                            '</div>' +
                                        '</div>' +
                                        '<div class="row" id="' + modalId + '-location-' + Object.keys(data)[0] + '">' +
                                            '<div class="col-md-2 col-xs-12">' +
                                                '<span class="text-upper text-bold">Location</span>' +
                                            '</div>' +
                                            '<div class="col-md-10 col-xs-12" >' +
                                                '<span>: LOCATION</span>' +
                                            '</div>' +
                                        '</div>' +
                                        '<div class="row" id="' + modalId + '-status-' + Object.keys(data)[0] + '">' +
                                            '<div class="col-md-2 col-xs-12">' +
                                                '<span class="text-upper text-bold">Status</span>' +
                                            '</div>' +
                                            '<div class="col-md-10 col-xs-12" >' +
                                                '<span>: ' + data[Object.keys(data)[0]]['status'] + '</span>' +
                                            '</div>' +
                                        '</div>' +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>'
                );
            } else if (select && Object.keys(data).length > 1 && Object.keys(data).length < 6) {
                var links = '';
                var linksContent = '';

                for (var selectedFileData in data) {
                    links +=
                            '<li class="nav-item">' +
                                '<a class="nav-link" id="' + modalId + '-info-content-files-tabs-' + selectedFileData + '-tab" ' +
                                'data-toggle="pill" href="#' + modalId + '-info-content-files-tabs-' + selectedFileData + '" ' +
                                'role="tab" aria-controls="' + modalId + '-info-content-files-tabs-' + selectedFileData + '" ' +
                                'aria-selected="true">' + data[selectedFileData]['entry'] + '</a>' +
                            '</li>';

                    linksContent +=
                        '<div class="tab-pane fade" id="' + modalId + '-info-content-files-tabs-' + selectedFileData + '" ' +
                        'role="tabpanel" aria-labelledby="' + modalId + '-info-content-files-tabs-' + selectedFileData + '">' +
                            '<div class="col border-bottom p-2">' +
                                '<div class="row" id="' + modalId + '-preview-' + selectedFileData + '">' +
                                    '<div class="col-md-12 text-center">' +
                                        '<i class="' + data[selectedFileData]['mime_icon'] + ' fa-6x"></i>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="row" id="' + modalId + '-name-' + selectedFileData + '">' +
                                    '<div class="col-md-2 col-xs-12">' +
                                        '<span class="text-upper text-bold">Name</span>' +
                                    '</div>' +
                                    '<div class="col-md-10 col-xs-12" >' +
                                        '<span>: ' + data[selectedFileData]['entry'] + '</span>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="row" id="' + modalId + '-size-' + selectedFileData + '">' +
                                    '<div class="col-md-2 col-xs-12">' +
                                        '<span class="text-upper text-bold">Size</span>' +
                                    '</div>' +
                                    '<div class="col-md-10 col-xs-12" >' +
                                        '<span>: ' + data[selectedFileData]['entry_size'] + ' kB</span>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="row" id="' + modalId + '-added-' + selectedFileData + '">' +
                                    '<div class="col-md-2 col-xs-12">' +
                                        '<span class="text-upper text-bold">Added</span>' +
                                    '</div>' +
                                    '<div class="col-md-10 col-xs-12" >' +
                                        '<span>: ' + data[selectedFileData]['added'] + '</span>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="row" id="' + modalId + '-modified-' + selectedFileData + '">' +
                                    '<div class="col-md-2 col-xs-12">' +
                                        '<span class="text-upper text-bold">Modified</span>' +
                                    '</div>' +
                                    '<div class="col-md-10 col-xs-12" >' +
                                        '<span>: ' + data[selectedFileData]['changed'] + '</span>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="row" id="' + modalId + '-location-' + selectedFileData + '">' +
                                    '<div class="col-md-2 col-xs-12">' +
                                        '<span class="text-upper text-bold">Location</span>' +
                                    '</div>' +
                                    '<div class="col-md-10 col-xs-12" >' +
                                        '<span>: LOCATION</span>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="row" id="' + modalId + '-status-' + selectedFileData + '">' +
                                    '<div class="col-md-2 col-xs-12">' +
                                        '<span class="text-upper text-bold">Status</span>' +
                                    '</div>' +
                                    '<div class="col-md-10 col-xs-12" >' +
                                        '<span>: ' + data[selectedFileData]['status'] + '</span>' +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                        '</div>';
                }

                $('#' + modalId + '-info-content').empty().append(
                    '<div class="col card-body p-0">' +
                        '<ul class="nav nav-tabs" id="' + modalId + '-info-content-files-tabs-links" role="tablist">' +
                            links +
                        '</ul>' +
                        '<div class="tab-content" id="' + modalId + '-info-content-files-tabs-content">' +
                            linksContent +
                        '</div>' +
                    '</div>'
                    );

                $($('#' + modalId + '-info-content-files-tabs-links li a')[0]).addClass('active');
                $($('#' + modalId + '-info-content-files-tabs-content .tab-pane')[0]).addClass('active show');
            } else if (select && Object.keys(data).length > 5) { //Allow only 5 files information to be viewed at a time
                $('#' + fileId + '-info').attr('disabled', true);
            } else if (!select) {
                $('#' + modalId + '-info-content-files-tabs-' + deselectId + '-tab').parent('li').remove();
                $('#' + modalId + '-info-content-files-tabs-' + deselectId).remove();
                $($('#' + modalId + '-info-content-files-tabs-links li a')[0]).addClass('active');
                $($('#' + modalId + '-info-content-files-tabs-content .tab-pane')[0]).addClass('active show');
            }
        }
    }

    function doAjax(url, postData, method, jstreeData) {
        $.ajax({
            'url'           : url,
            'data'          : postData,
            'method'        : method,
            'dataType'      : 'json',
            'success'       : function(content) {
                if (content.storages) {
                    for (var storage in content.storages) {
                        $('#' + treeId + ' ul').append(
                            '<li data-id="' + content.storages[storage].id + '" data-description="' + content.storages[storage].description +
                                '" data-jstree=\'{"icon" : "fas fa-fw fa-hdd"}\'>' + content.storages[storage].name + '</li>'
                        );
                    }
                    // Init Jstree
                    dataCollection[componentId][sectionId][treeId]['jstree'] = $('#' + treeId).jstree(dataCollection[componentId][sectionId][treeId]);
                    // Register Get Folders on Select Event
                    $('#' + treeId).on('select_node.jstree', function(e, data) {
                        $('#' + treeId + '-info').attr('disabled', false);
                        $('#' + fileId + '-upload').attr('disabled', false);
                        $('#' + sectionId + '-dropzone-div label span').empty().append(
                            data.node.text
                            );
                        getStorageItems(data);
                    });
                } else if (content.storage_content) {
                    $.fn.dataTable.settings[0]["oLanguage"]["sEmptyTable"] = '<i class="fas fa-cog fa-spin"></i> Loading...';
                    thisOptions['datatable'].rows().clear().draw();

                    var folderData = [];
                    var filesData = [];
                    files = false;

                    for (var folderContent in content.storage_content) {
                        if (content.storage_content[folderContent].type === '1') {
                            folderData.push({
                                'data-id'           : content.storage_content[folderContent].id,
                                'data-parent_id'    : content.storage_content[folderContent].parent_id,
                                'data-added'        : content.storage_content[folderContent].added,
                                'data-changed'      : content.storage_content[folderContent].changed,
                                'text'              : content.storage_content[folderContent].entry,
                                'data-icon'         : 'fas fa-fw fa-folder',
                                'icon'              : 'fas fa-fw fa-folder',
                                'data-size'         : content.storage_content[folderContent].entry_size,
                                'data-status'       : content.storage_content[folderContent].status,
                                'data-type'         : content.storage_content[folderContent].type
                            });
                        } else if (content.storage_content[folderContent].type === '2') {
                            files = true;
                            var today = new Date();
                            if (content.storage_content[folderContent].added !== '0') {
                                dateAdded = new Date(content.storage_content[folderContent].added * 1000);
                                if (today.toDateString() === dateAdded.toDateString()) {
                                    extractDateAdded = dateAdded.getHours() + ':' + dateAdded.getMinutes() + ':' + dateAdded.getSeconds();
                                } else {
                                    extractDateAdded = dateAdded.toDateString() + ' ' +
                                        dateAdded.getHours() + ':' + dateAdded.getMinutes() + ':' + dateAdded.getSeconds();
                                }
                            }
                            if (content.storage_content[folderContent].changed !== '0') {
                                dateChanged = new Date(content.storage_content[folderContent].changed * 1000);
                                if (today.toDateString() === dateChanged.toDateString()) {
                                    extractDateChanged = dateChanged.getHours() + ':' + dateChanged.getMinutes() + ':' + dateChanged.getSeconds();
                                } else {
                                    extractDateChanged = dateChanged.toDateString() + ' ' +
                                        dateChanged.getHours() + ':' + dateChanged.getMinutes() + ':' + dateChanged.getSeconds();
                                }
                            } else {
                                extractDateChanged = extractDateAdded;
                            }

                            if (content.storage_content[folderContent].entry_size === null) {
                                dataSize = '-';
                            } else {
                                dataSize = content.storage_content[folderContent].entry_size;
                            }

                            if (content.storage_content[folderContent].status === '0' || content.storage_content[folderContent].status === '1') {
                                dataStatus = '<span class="badge badge-warning">Processing...</span>';
                            } else if (content.storage_content[folderContent].status === '2') {
                                dataStatus = '<span class="badge badge-success">Ok</span>';
                            } else if (content.storage_content[folderContent].status === '3' || content.storage_content[folderContent].status === '4') {
                                dataStatus = '<span class="badge badge-danger">Error</span>';
                            }

                            filesData.push({
                                'id'                    : content.storage_content[folderContent].id,
                                'added'                 : extractDateAdded,
                                'changed'               : extractDateChanged,
                                // 'childs'                : content.storage_content[folderContent].childs,
                                'entry'                 : content.storage_content[folderContent].entry,
                                'entry_size'            : dataSize,
                                'parent_id'             : content.storage_content[folderContent].parent_id,
                                'status'                : dataStatus,
                                'type'                  : content.storage_content[folderContent].type,
                                'mime'                  : content.storage_content[folderContent].mime
                            });
                        }
                    }
                    if (files) {
                        redrawTable(filesData);
                    } else {
                        $.fn.dataTable.settings[0]["oLanguage"]["sEmptyTable"] = 'No files in folder <strong>' + jstreeData.node.text + '</strong>';
                        $.fn.dataTable.settings[0]["oLanguage"]["select"]["rows"][0] = '';
                        thisOptions['datatable'].rows().clear().draw();
                    }

                    if ($('#' + treeId).jstree(true).get_node(jstreeData.node).children.length > 0) {
                        $('#' + treeId).
                            jstree(true).delete_node($('#' + treeId).jstree(true).get_node(jstreeData.node).children);
                        $.each(folderData, function(index,folder) {
                            $('#' + treeId).jstree(true).create_node(jstreeData.node, folder);
                        });
                    } else {
                        $.each(folderData, function(index,folder) {
                            $('#' + treeId).jstree(true).create_node(jstreeData.node, folder);
                        });
                    }

                    $('#' + treeId).jstree(true).open_node(jstreeData.node);

                    if (jstreeData.node.parent === '#') {
                        $('#' + treeId).jstree(true).set_icon(jstreeData.node, 'fas fa-fw fa-hdd');
                    } else {
                        $('#' + treeId).jstree(true).set_icon(jstreeData.node, 'fas fa-fw fa-folder-open');
                    }
                } else if (content.length === 0) {
                    $.fn.dataTable.settings[0]["oLanguage"]["sEmptyTable"] = 'No files in folder';
                    $.fn.dataTable.settings[0]["oLanguage"]["select"]["rows"][0] = '';

                    thisOptions['datatable'].rows().clear().draw();

                    if (jstreeData.node.parent === '#') {
                        $('#' + treeId).jstree(true).set_icon(jstreeData.node, 'fas fa-fw fa-hdd');
                    } else {
                        $('#' + treeId).jstree(true).set_icon(jstreeData.node, 'fas fa-fw fa-folder-open');
                    }

                }
            },
            'complete'      : function() {
            }
        });
    }

    function registerButtons() {
        $('#' + treeId + '-info').click(function(e) {
            e.preventDefault();
            $('#' + modalId + ' .modal-header h5').empty().append(
                'Folder Information'
                );
            $('#' + sectionId + '-modal').modal('show');
        });
        $('#' + fileId + '-info').click(function(e) {
            e.preventDefault();
            $('#' + modalId + ' .modal-header h5').empty().append(
                'File(s) Information'
                );
            $('#' + sectionId + '-modal').modal('show');
        });
        $('#' + fileId + '-upload').click(function(e) {
            e.preventDefault();
            $('#' + sectionId + '-dropzone-div').removeClass('d-none');
        });
        $('#' + dropzoneId + '-close').click(function(e) {
            e.preventDefault();
            $('#' + sectionId + '-dropzone-div').addClass('d-none');
        });
    }

    function bazContentSectionWithWizard() {
        // if something needs to be constructed
        return null;
    }

    function setup(BazContentSectionWithStorageConstructor) {
        BazContentSectionWithStorage = BazContentSectionWithStorageConstructor;
        BazContentSectionWithStorage.defaults = { };
        BazContentSectionWithStorage.init = function(options) {
            init(_extends(BazContentSectionWithStorage.defaults, options));
        }
    }

    setup(bazContentSectionWithWizard);


    return bazContentSectionWithWizard;
}();
$(document).on('libsLoadComplete bazContentLoaderAjaxComplete bazContentLoaderModalComplete bazContentWizardAjaxComplete', function() {
    if ($('.sectionWithStorage').length > 0) {
        $('.sectionWithStorage').each(function() {
            BazContentSectionWithStorage.init({'storageId' : $(this)});
        });
    }
});

/* exported BazContentSectionWithWizard */
/* globals PNotify */
/*
* @title                    : BazContentSectionWithWizard
* @description              : Baz Core Lib
* @developer                : guru@bazaari.com.au
* @usage                    : BazContentSectionWithWizard._function_(_options_);
* @functions                : BazHeader, BazFooter, BazUpdateBreadcrumb
* @options                  :
*/

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
// var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var BazContentSectionWithWizard = function() {
    var BazContentSectionWithWizard = void 0;
    var dataCollection = window.dataCollection;
    var componentId, sectionId , wizardOptions, originalTitle, steps, lastStep, review;

    // Error
    // function error(errorMsg) {
    //     throw new Error(errorMsg);
    // }

    //Header
    function init(options) {
        componentId = $(options.wizardId).parents('.component')[0].id;
        sectionId = $(options.wizardId)[0].id;

        if (!dataCollection[componentId]) {
            dataCollection[componentId] = { };
        }
        if (!dataCollection[componentId][sectionId]) {
            dataCollection[componentId][sectionId] = { };
        }

        steps = dataCollection[componentId][sectionId]['steps'];
        dataCollection[componentId][sectionId]['reviewHtml'] = '';
        review = dataCollection[componentId][sectionId]['reviewHtml'];
        wizardOptions = dataCollection[componentId][sectionId];

        if (wizardOptions['startAtStep'] != "0") {
            wizardOptions['currentStep'] = Number(wizardOptions['startAtStep']);
        } else {
            wizardOptions['currentStep'] = 0;
        }

        lastStep = wizardOptions.steps.length - 1;

        // ReviewDiv
        if (wizardOptions.showReview == true) {
            review = '<div class="accordion" id="' + sectionId + '-review-accordion"></div>';
            $('#' + sectionId + '-' + lastStep + '-data').html(review);
        }

        // Lets grab the component Ids & section Ids of the steps
        for (var step in steps) {
            if ($('#' + sectionId + '-' + step + '-data .component').length > 0) {
                steps[step]['componentId'] = $('#' + sectionId + '-' + step + '-data .component')[0].id;
            }

            if ($('#' + sectionId + '-' + step + '-data .section').length > 0) {
                steps[step]['sectionId'] = $('#' + sectionId + '-' + step + '-data .section')[0].id;
                steps[step]['type'] = 'section';
            }

            if ($('#' + sectionId + '-' + step + '-data .sectionWithForm').length > 0) {
                steps[step]['sectionId'] = $('#' + sectionId + '-' + step + '-data .sectionWithForm')[0].id;
                steps[step]['type'] = 'form';
                steps[step]['validate'] = true;
                if (wizardOptions.showReview) {
                    buildReview(step);
                    $('#' + sectionId + '-review-accordion').append(review);
                }
            }

            if ($('#' + sectionId + '-' + step + '-data .sectionWithList').length > 0) {
                steps[step]['sectionId'] = $('#' + sectionId + '-' + step + '-data .sectionWithList')[0].id;
                steps[step]['type'] = 'datatable';
            }

            if (steps[step]['ajax']) {
                $('#' + sectionId + '-' + step + '-data').load(dataCollection.env.rootPath + steps[step]['ajax']);
            }
        }

        // Make all contentAjaxLink to contentModalLink if section is Datatable
        // $('#' + sectionId + '-data .contentAjaxLink').addClass('contentModalLink').removeClass('contentAjaxLink');
        // Change Modal Size to xl
        // BazContentLoader.init({'modalSize' : 'xl'});

        $('#' + sectionId + '-' + wizardOptions['currentStep'] + '-step').addClass('current');

        if ($('#' + sectionId + '-' + wizardOptions['currentStep'] + '-description').length > 0) {
            $('#' + sectionId + '-' + wizardOptions['currentStep'] + '-description').attr('hidden', false);
        }

        $('#' + sectionId + '-' + wizardOptions['currentStep'] + '-data').attr('hidden', false);

        originalTitle = $('#' + sectionId + ' div.card-header span.title').first().html();

        if (wizardOptions['startAtStep'] != "0") {
            for (var i = 0; i < wizardOptions['currentStep']; i++) {
                $('#' + sectionId + '-' + i + '-step').addClass('visited');
                if (steps[i]['type'] == 'form') {
                    steps[i]['submitted'] = true;
                }
            }
        }

        updateTitle();
        initWizardStepsButtons();
        hideHeaderFooter();

        // var runFirstTime = true;
        // $(document).ajaxComplete(function(e, xhr, settings) {
        //     //eslint-disable-next-line
        //     console.log(runFirstTime);
        //     if (runFirstTime) {
        //         for (var ajaxStep in steps) {
        //             var reviewBeforeId, reviewAfterId;
        //             var url = dataCollection.env.rootPath + steps[ajaxStep]['ajax'];
        //             if (url === settings.url) {
        //                 //eslint-disable-next-line
        //                 console.log(ajaxStep);
        //                 reviewBeforeId = Number(ajaxStep) - 1;
        //                 reviewAfterId = Number(ajaxStep) + 1;
        //                 if ($('#' + sectionId + '-' + ajaxStep + '-data .component').length > 0) {
        //                     steps[ajaxStep]['componentId'] = $('#' + sectionId + '-' + ajaxStep + '-data .component')[0].id;
        //                 }
        //                 if ($('#' + sectionId + '-' + ajaxStep + '-data .sectionWithForm').length > 0) {
        //                     steps[ajaxStep]['sectionId'] = $('#' + sectionId + '-' + ajaxStep + '-data .sectionWithForm')[0].id;
        //                     steps[ajaxStep]['type'] = 'form';
        //                     steps[ajaxStep]['validate'] = true;
        //                     if (wizardOptions.showReview) {
        //                         buildReview(ajaxStep);
        //                         if ($('#' + sectionId + '-' + reviewBeforeId + '-review').length > 0) {
        //                             $('#' + sectionId + '-' + reviewBeforeId + '-review').after(review);
        //                         } else if ($('#' + sectionId + '-' + reviewAfterId + '-review').length > 0) {
        //                             $('#' + sectionId + '-' + reviewAfterId + '-review').before(review);
        //                         }
        //                     }
        //                 }
        //                 hideHeaderFooter();
        //                 $('body').trigger('bazContentWizardAjaxComplete');
        //             }
        //         }
        //         runFirstTime = false;
        //     }
        // });
    }

    function updateTitle() {
        $('#' + sectionId + ' div.card-header span.title').addClass('text-uppercase');
        var title = originalTitle + ' : ' + steps[wizardOptions['currentStep']].title;
        $('#' + sectionId + ' div.card-header span.title').first().html(title);
    }

    function hideHeaderFooter() {
        $('#' + sectionId + '-data').children().each(function(index, child) {
            $('#' + child.id + ' .card-header').each(function() {
                if (!$(this).parents().hasClass('accordion')) {
                    $(this).attr('hidden', true);
                }
                if ($(this).children('ul').hasClass('nav-tabs')) {
                    $(this).attr('hidden', false);
                }
            });
            $('#' + child.id + ' .card-footer').each(function() {
                if (!$(this).parents().hasClass('accordion')) {
                    $(this).attr('hidden', true);
                }
            });
        });
    }

    function buildReview(step) {
        var stripComponentId;
        wizardOptions['steps'][step]['dataToSubmit'] = [];
        $('#' + sectionId + '-' + step + '-data .form-group').each(function(index, field) {
            if ($(field).find('[data-bazpostoncreate=true]').length > 0) {
                stripComponentId = $(field).find('[data-bazpostoncreate=true]')[0].id.split('-');
                wizardOptions['steps'][step]['dataToSubmit'].push({
                    id : stripComponentId[1],
                    title : $(field).children('label').text()
                });
            }
            // For Radio Buttons
            if ($(field).data('bazpostoncreate')) {
                stripComponentId = $(field)[0].id.split('-');
                wizardOptions['steps'][step]['dataToSubmit'].push({
                    id : stripComponentId[1],
                    title : $(field).children('label').text()
                });
            }
        });
        review =
            '<div class="card mb-0" id="' + sectionId + '-' + step + '-review">' +
                '<div class="card-header" id="' + sectionId + '-' + step + '-accordioncard-header">' +
                    '<h4 class="card-title">' +
                        '<button class="btn btn-link text-uppercase" type="button" data-toggle="collapse" data-target="#' +
                            sectionId + '-' + step + '-accordioncard" aria-control="' + sectionId + '-' + step + '-accordioncard">';
                            if (wizardOptions['steps'][step]['icon']) {
                                review += '<i class="fas fa-fw fa-' + wizardOptions['steps'][step]['icon'] + ' mr-1"></i>' + wizardOptions['steps'][step]['title']
                            } else {
                                review += wizardOptions['steps'][step]['title'];
                            }
                            review +=
                        '</button>' +
                    '</h4>' +
                '</div>' +
                '<div id="' + sectionId + '-' + step + '-accordioncard" class="collapse" area-labelledby="' +
                    sectionId + '-' + step + '-accordioncard-header" data-parent="#' + sectionId + '-review-accordion">' +
                    '<div class="card-body">' +
                    '</div>' +
                '</div>' +
            '</div>';
    }

    function initWizardStepsButtons() {
        $('#' + sectionId + '-previous').off();
        $('#' + sectionId + '-next').off();

        var nextDisabled = false;
        var nextHidden = false;

        if (wizardOptions.canCancel) {
            $('#' + sectionId + '-cancel').attr('hidden', false);
        }

        if (wizardOptions['steps'][wizardOptions['currentStep']]['nextDisabled'] == true) {
            nextDisabled = true;
        } else {
            nextDisabled = false;
        }
        if (wizardOptions['steps'][wizardOptions['currentStep']]['nextHidden'] == true) {
            nextHidden = true;
        } else {
            nextHidden = false;
        }

        if (wizardOptions['currentStep'] === 0) {
            $('#' + sectionId + '-previous').attr('hidden', true);
            $('#' + sectionId + '-next').attr('hidden', nextHidden);
            $('#' + sectionId + '-next').attr('disabled', nextDisabled);
            $('#' + sectionId + '-done').attr('hidden', true);
            $('#' + sectionId + '-submit').attr('hidden', true);
        } else if (wizardOptions['currentStep'] === lastStep) {
            if (wizardOptions['steps'][wizardOptions['currentStep']]['goBack'] != false) {
                $('#' + sectionId + '-previous').attr('hidden', false);
                $('#' + sectionId + '-previous').attr('disabled', false);
            } else {
                $('#' + sectionId + '-previous').attr('hidden', true);
            }
            $('#' + sectionId + '-next').attr('hidden', true);
        } else {
            if (wizardOptions['steps'][wizardOptions['currentStep']]['goBack'] != false) {
                $('#' + sectionId + '-previous').attr('hidden', false);
                $('#' + sectionId + '-previous').attr('disabled', false);
            } else {
                $('#' + sectionId + '-previous').attr('hidden', true);
            }
            $('#' + sectionId + '-next').attr('hidden', nextHidden);
            $('#' + sectionId + '-next').attr('disabled', nextDisabled);
            $('#' + sectionId + '-done').attr('hidden', true);
            $('#' + sectionId + '-submit').attr('hidden', true);
        }

        // Previous Button
        $('#' + sectionId + '-previous').click(function() {
            $('#' + sectionId + '-submit').off();
            var previousStep = wizardOptions['currentStep'] - 1;
            $('#' + sectionId + '-' + wizardOptions['currentStep'] + '-step').addClass('visited').removeClass('current');
            $('#' + sectionId + '-' + previousStep + '-step').addClass('current');
            $('#' + sectionId + '-' + wizardOptions['currentStep'] + '-description').attr('hidden', true);
            $('#' + sectionId + '-' + wizardOptions['currentStep'] + '-data').attr('hidden', true);
            $('#' + sectionId + '-' + previousStep + '-description').attr('hidden', false);
            $('#' + sectionId + '-' + previousStep + '-data').attr('hidden', false);
            if (wizardOptions['steps'][wizardOptions['currentStep']]['onPrevious']) {
                wizardOptions['steps'][wizardOptions['currentStep']]['onPrevious']();
            }
            wizardOptions['currentStep'] = previousStep;
            updateTitle();
            initWizardStepsButtons();
        });

        // Next Button
        $('#' + sectionId + '-next').click(function() {
            $(this).children('i').attr('hidden', false);
            $(this).attr('disabled', true);
            // Validate form & extract data on successful validation
            if (steps[wizardOptions['currentStep']]['validate']) {
                $('#' + steps[wizardOptions['currentStep']]['sectionId']).BazContentSectionWithForm({
                    'task'      : 'validateForm'
                });

                if (dataCollection[steps[wizardOptions['currentStep']]['componentId']][steps[wizardOptions['currentStep']]['sectionId']]['formValidator'].numberOfInvalids() === 0) {
                    $('#' + steps[wizardOptions['currentStep']]['sectionId']).BazContentSectionWithForm({
                        'task'      : 'sectionToObj'
                    });

                    // Create Review Body for this step
                    var fields = '';
                    $.each(steps[wizardOptions['currentStep']]['dataToSubmit'], function(index, field) {
                        fields += '<div class="row"><div class="col text-bold">' + field.title + '</div><div class="col">: ';

                        if ($('#' + steps[wizardOptions['currentStep']]['componentId'] + '-' + field.id).data('bazscantype') === 'select2') {
                            fields += $('#' + steps[wizardOptions['currentStep']]['componentId'] + '-' + field.id + ' option:selected').html();
                        } else if ($('#' + steps[wizardOptions['currentStep']]['componentId'] + '-' + field.id).data('bazscantype') === 'radio') {
                            fields += $('#' + steps[wizardOptions['currentStep']]['componentId'] + '-' + field.id + ' :checked').parent('label').text().trim();
                        } else {
                            fields += dataCollection[steps[wizardOptions['currentStep']]['componentId']][steps[wizardOptions['currentStep']]['sectionId']]['data'][field.id];
                        }
                        fields += '</div></div>';
                    });
                    $('#' + sectionId + '-' + wizardOptions['currentStep'] + '-accordioncard .card-body').html(fields);

                    // Submit form if submitOnNext
                    if (steps[wizardOptions['currentStep']]['submitOnNext']) {
                        var formIdVal = $('#' + steps[wizardOptions['currentStep']]['sectionId'] + '-id').val();
                        if ($('#' + steps[wizardOptions['currentStep']]['sectionId'] + '-addData').length > 0 &&
                            formIdVal === ''
                        ) {
                            doAjax(
                                $('#' + steps[wizardOptions['currentStep']]['sectionId'] + '-addData').attr('actionurl'),
                                steps[wizardOptions['currentStep']]['componentId'],
                                steps[wizardOptions['currentStep']]['sectionId'],
                                wizardOptions['currentStep'],
                                false
                                );
                        } else if ($('#' + steps[wizardOptions['currentStep']]['sectionId'] + '-updateData').length > 0 &&
                                   formIdVal !== ''
                        ) {
                            doAjax(
                                $('#' + steps[wizardOptions['currentStep']]['sectionId'] + '-updateData').attr('actionurl'),
                                steps[wizardOptions['currentStep']]['componentId'],
                                steps[wizardOptions['currentStep']]['sectionId'],
                                wizardOptions['currentStep'],
                                false
                                );
                        }
                    } else {
                        goNext();
                    }
                } else {
                    $('#' + sectionId + '-next').children('i').attr('hidden', true);
                    $('#' + sectionId + '-next').attr('disabled', false);
                }
            } else {
                goNext();
            }
        });
        $('[data-toggle="tooltip"]').tooltip();
        $('[data-toggle="popover"]').popover('enable');
    }

    function goNext() {
        if (wizardOptions['steps'][wizardOptions['currentStep']]['onNext']) {
            var onNext = new Promise(wizardOptions['steps'][wizardOptions['currentStep']]['onNext']);

            onNext.then(function(success) {
                $('#' + sectionId + '-next').children('i').attr('hidden', true);
                $('#' + sectionId + '-next').attr('disabled', false);

                if (!success) {
                    return;
                }

                hideHeaderFooter();

                processGoNext();
            });
        } else {
            processGoNext();
        }
    }

    function processGoNext() {
        $('#' + sectionId + '-next').children('i').attr('hidden', true);
        $('#' + sectionId + '-next').attr('disabled', false);

        var nextStep = wizardOptions['currentStep'] + 1;
        $('#' + sectionId + '-' + wizardOptions['currentStep'] + '-step').addClass('visited').removeClass('current');
        $('#' + sectionId + '-' + nextStep + '-step').addClass('current');
        $('#' + sectionId + '-' + wizardOptions['currentStep'] + '-description').attr('hidden', true);
        $('#' + sectionId + '-' + wizardOptions['currentStep'] + '-data').attr('hidden', true);
        $('#' + sectionId + '-' + nextStep + '-description').attr('hidden', false);
        $('#' + sectionId + '-' + nextStep + '-data').attr('hidden', false);

        wizardOptions['currentStep'] = nextStep;
        if (wizardOptions['currentStep'] === lastStep) {
            if (wizardOptions.showReview) {
                $('#' + sectionId + '-review-accordion button').first().removeClass('collapsed');
                $('#' + sectionId + '-review-accordion .collapse').first().addClass('show');
            }
            $('#' + sectionId + '-next').attr('hidden', true);
            var submitButtonActive = null;
            for (var step in steps) {
                if (steps[step].type === 'form') {
                    if (!steps[step]['submitted']) {
                        $('#' + sectionId + '-submit').attr('hidden', false);//show submit if form has been previous submitted
                        submitButtonActive = true;
                        break;
                    }
                }
            }
            if (submitButtonActive) {
                $('#' + sectionId + '-submit').click(function() {
                    for (var step in steps) {
                        if (steps[step].type === 'form') {
                            if (!steps[step].submitted) {
                                var formIdVal = $('#' + steps[wizardOptions['currentStep']]['sectionId'] + '-id').val();
                                if ($('#' + steps[wizardOptions['currentStep']]['sectionId'] + '-addData').length > 0 &&
                                    formIdVal === ''
                                ) {
                                    doAjax(
                                        $('#' + steps[wizardOptions['currentStep']]['sectionId'] + '-addData').attr('actionurl'),
                                        steps[wizardOptions['currentStep']]['componentId'],
                                        steps[wizardOptions['currentStep']]['sectionId'],
                                        wizardOptions['currentStep'],
                                        false
                                        );
                                } else if ($('#' + steps[wizardOptions['currentStep']]['sectionId'] + '-updateData').length > 0 &&
                                           formIdVal !== ''
                                ) {
                                    doAjax(
                                        $('#' + steps[wizardOptions['currentStep']]['sectionId'] + '-updateData').attr('actionurl'),
                                        steps[wizardOptions['currentStep']]['componentId'],
                                        steps[wizardOptions['currentStep']]['sectionId'],
                                        wizardOptions['currentStep'],
                                        false
                                        );
                                }
                            }
                        }
                    }
                });
            } else {
                $('#' + sectionId + '-done').attr('hidden', false);//if all forms are submitted, then show done.
                $('#' + sectionId + '-done').click(function() {
                    $('#' + sectionId + '-review-accordion').collapse('dispose');
                });
            }
        }
        updateTitle();
        initWizardStepsButtons();
    }

    function doAjax(formUrl, formComponentId, formSectionId, step, lastStep) {
        wizardOptions['steps'][step]['submittedData'] = $.param(dataCollection[formComponentId][formSectionId].dataToSubmit);

        $.post(formUrl, $.param(dataCollection[formComponentId][formSectionId].dataToSubmit), function(response) {
            var success = false;

            wizardOptions['steps'][step]['responseCode'] = response.responseCode;
            wizardOptions['steps'][step]['responseMessage'] = response.responseMessage;

            if (response.responseCode == 0) {
                if (response.responseData) {
                    wizardOptions['steps'][step]['responseData'] = response.responseData;
                }
                wizardOptions['steps'][step]['submitted'] = true;
                success = true;
                $('#' + sectionId + '-' + step + '-accordioncard-header').removeClass('bg-danger').addClass('bg-success');
            } else {
                PNotify.error({
                    title   : response.responseMessage,
                });
                $('#' + sectionId + '-next').children('i').attr('hidden', true);
                $('#' + sectionId + '-next').attr('disabled', false);
                $('#' + sectionId + '-' + step + '-accordioncard-header').removeClass('bg-success').addClass('bg-danger');
            }
            if (lastStep) {
                if ($('#' + sectionId + '-review-accordion .bg-danger').length === 0) {
                    $('#' + sectionId + '-submit').off();
                    $('#' + sectionId + '-previous').attr('hidden', true);
                    $('#' + sectionId + '-submit').attr('hidden', true);
                    $('#' + sectionId + '-done').attr('hidden', false);
                }
            } else if (!lastStep && success === true) {
                goNext();
            }
            if (response.tokenKey && response.token) {
                $('#security-token').attr('name', response.tokenKey);
                $('#security-token').val(response.token);
            }
        }, 'json');
    }

    function bazContentSectionWithWizard() {
        // if something needs to be constructed
        return null;
    }

    function setup(BazContentSectionWithWizardConstructor) {
        BazContentSectionWithWizard = BazContentSectionWithWizardConstructor;
        BazContentSectionWithWizard.defaults = { };
        BazContentSectionWithWizard.init = function(options) {
            init(_extends(BazContentSectionWithWizard.defaults, options));
        }
    }

    setup(bazContentSectionWithWizard);


    return bazContentSectionWithWizard;
}();
$(document).on('libsLoadComplete bazContentLoaderAjaxComplete', function() {
    if ($('.sectionWithWizard').length > 0) {
        $('.sectionWithWizard').each(function() {
            BazContentSectionWithWizard.init({'wizardId' : $(this)});
        });
    }
});

/* exported BazContentFields */
/* globals PNotify flatpickr */
/*
* @title                    : BazContentFields
* @description              : Baz Lib for Content (Sections With Form)
* @developer                : guru@bazaari.com.au
* @usage                    : ('#'+ section/componentID).BazContentFields;
* @functions                :
* @options                  :
*/
// (function (global, factory) {
//     typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports) :
//     typeof define === 'function' && define.amd ? define(['exports'], factory) :
//     (global = global || self, factory(global.BazLibs = {}));
// }(this, function (exports) {

//     var BazContentFields = function ($) {

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
// var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

// eslint-disable-next-line no-unused-vars
var BazContentFields = function() {
    var BazContentFields = void 0;
        // var NAME                    = 'BazContentFields';
        // var DATA_KEY                = 'baz.contentfields';
        // var EVENT_KEY               = "." + DATA_KEY;
        // var JQUERY_NO_CONFLICT      = $.fn[NAME];
        // var Event = {
        // };
        // var ClassName = {
        // };
        // var Selector = {
        // };
        // var Default = {
        // };
    var componentId,
        sectionId,
        pnotifySound,
        dataCollection;

    var minValText, maxValText, minLengthText, maxLengthText, thisFieldId;

    // Error
    function error(errorMsg) {
        throw new Error(errorMsg);
    }

    //Init
    function init(options) {
        componentId = options.componentId;
        sectionId = options.sectionId;
        dataCollection = window['dataCollection'];
        pnotifySound = dataCollection.env.sounds.pnotifySound;

        if ($('body').find('.flatpickr-calendar').length > 0) {
            $('body').find('.flatpickr-calendar').remove();
        }
        if ($('body').find('.dz-hidden-input').length > 0) {
            $('body').find('.dz-hidden-input').remove();
        }
    }

        // var BazContentFields = function () {
        //     function BazContentFields(element, settings) {
        //         that = this;
        //         this._element = element;
        //         this._settings = $.extend({}, Default, settings);

        //         if ($('body').find('.flatpickr-calendar').length > 0) {
        //             $('body').find('.flatpickr-calendar').remove();
        //         }
        //         if ($('body').find('.dz-hidden-input').length > 0) {
        //             $('body').find('.dz-hidden-input').remove();
        //         }

        //         this._init();
        //         if (this._settings.fieldId) {
        //             this._bazInitField();
        //         } else {
        //             this._bazInitFields();
        //         }
        //     }

        //     var _proto = BazContentFields.prototype;

        //     _proto._error = function(message) {
        //         throw new Error(message);
        //     };

        //     _proto._init = function _init() {
        //         componentId = $(this._element).parents('.component')[0].id;
        //         sectionId = $(this._element)[0].id;
        //         dataCollection = window['dataCollection'];
        //         pnotifySound = dataCollection.env.sounds.pnotifySound

        //         // Grab Components HTML Code (future use)
        //         // if (!dataCollection[componentId].html){
        //         //     dataCollection[componentId].html = $('#' + componentId).parents('.container-fluid').html();
        //         // }
        //         // dataCollection[componentId][sectionId].html = $('#' + sectionId).html();

        //         // TODO Decide what to do with section without any fields.
        //         // I can only think of tabs being made available via section, which needs to be initialized.
        //         // ALSO TABS CAN HAVE OPTION TO ENABLE A PARTICULAR TABID or FIRST TAB ID
        //     };

        //     _proto._bazInitField = function _bazInitField() {
        //     };

            // _proto._bazInitFields = function _bazInitFields() {
                // tableData[sectionId] = { };//building object used during save
    function initFields(options = null) {
        var fields;
        if (options && options.fieldId) {
            componentId = options.componentId;
            sectionId = options.sectionId;
            fields = $('#' + sectionId).find('#' + options.fieldId);
        } else {
            fields = $('#' + sectionId).find('[data-bazscantype]');
        }

        // Iterate through the component
        $(fields).each(function(index,bazScanField) {
            // if (bazScanField.tagName !== 'FIELDSET' && $(bazScanField).parents('fieldset').data('bazscantype') !== 'datatable') {
                if (dataCollection[componentId][sectionId][bazScanField.id]) {
                    dataCollection[componentId][sectionId][bazScanField.id].bazScanType = bazScanField.dataset.bazscantype;
                    if (bazScanField.dataset.bazscantype === 'input') {
                        initInput(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                    } else if (bazScanField.dataset.bazscantype === 'select2') {
                        initSelect2(bazScanField.id, sectionId, dataCollection[componentId][sectionId][bazScanField.id]);
                    } else if (bazScanField.dataset.bazscantype === 'checkbox') {
                        initCheckbox(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                    } else if (bazScanField.dataset.bazscantype === 'radio' || bazScanField.dataset.bazscantype === 'radio-button-group') {
                        initRadio(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                    } else if (bazScanField.dataset.bazscantype === 'flatpickr') {
                        initFlatpickr(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                    } else if (bazScanField.dataset.bazscantype === 'colorpicker') {
                        initColorpicker(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                    } else if (bazScanField.dataset.bazscantype === 'textarea') {
                        initTextarea(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                    } else if (bazScanField.dataset.bazscantype === 'json') {
                        initJson(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                    } else if (bazScanField.dataset.bazscantype === 'trumbowyg') {
                        initTrumbowyg(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                    } else if (bazScanField.dataset.bazscantype === 'counters') {
                        initCounters(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                    } else if (bazScanField.dataset.bazscantype === 'jstree') {
                        initJstree(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                    } else if (bazScanField.dataset.bazscantype === 'html') {
                        initHTML(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                    } else if (bazScanField.dataset.bazscantype === 'dropzone') {
                        initDropzone(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                    } else if (bazScanField.dataset.bazscantype === 'datatable') {
                        initDatatable(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                    }
                } else {
                    error('Individual sections parameters missing for ' + bazScanField.id);
                }
            // }
        });
        dataCollection[componentId][sectionId]['initFields'] = true;
    }

    function maxLength(fieldId, options) {
        thisFieldId = fieldId;
        fieldId = document.getElementById(fieldId);
        if (fieldId.hasAttribute('minlength') ||
            fieldId.hasAttribute('maxlength') ||
            fieldId.hasAttribute('max')) {
            if (fieldId.hasAttribute('maxlength')) {
                maxLengthText = ' UsedChar: %charsTyped% MaxChar: %charsTotal%';
            } else {
                maxLengthText = '';
            }
            if (fieldId.hasAttribute('minlength')) {
                minLengthText = 'MinChar: ' + fieldId.attributes.minlength.value + ' ';
            } else {
                minLengthText = '';
            }
            if (fieldId.hasAttribute('min')) {
                minValText = 'MinVal: ' + fieldId.attributes.min.value + ' ';
                options.customMaxAttribute = 'min';
            } else {
                minValText = '';
            }
            if (fieldId.hasAttribute('max')) {
                maxValText = 'MaxVal: ' + fieldId.attributes.max.value + ' ';
                options.customMaxAttribute = 'max';
            } else {
                maxValText = '';
            }
            options = $.extend({
                currentInput            : $(fieldId),
                alwaysShow              : true,
                allowOverMax            : false,
                thresholdAmount         : 5,
                thresholdPercent        : 20,
                message                 : minValText + maxValText + minLengthText + maxLengthText,
                placement               : 'top-right-inside'
            }, options);
            dataCollection[componentId][sectionId][thisFieldId]['maxlength'] = $(fieldId).maxlength(options);
        }
    }

    // Restricts input for each element in the set of matched elements to the given fieldInputTypeTextFilter.
    function applyInputFilter(field, filter) {
        if (!$.fn.inputFilter) {
            (function($) {
                $.fn.inputFilter = function(inputFilter) {
                    return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function() {
                        if (inputFilter(this.value)) {
                            this.oldValue = this.value;
                            this.oldSelectionStart = this.selectionStart;
                            this.oldSelectionEnd = this.selectionEnd;
                        } else if (this.hasOwnProperty("oldValue")) {
                            this.value = this.oldValue;
                            this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
                        } else {
                            this.value = "";
                        }
                    });
                };
            }(jQuery));
        }

        if (filter === 'int') {
            $(field).inputFilter(function(value) {
              return /^-?\d*$/.test(value);
            });
        } else if (filter === 'positiveInt') {
            $(field).inputFilter(function(value) {
              return /^\d*$/.test(value);
            });
        } else if (filter === 'positiveIntMax') {
            $(field).inputFilter(function(value) {
              return /^\d*$/.test(value) && (value === "" || parseInt(value) <= $(field).attr('max'));
            });
        } else if (filter === 'float') {
            $(field).inputFilter(function(value) {
              return /^-?\d*[.]?\d*$/.test(value);
            });
        } else if (filter === 'positiveFloat') {
            $(field).inputFilter(function(value) {
              return /^\d*[.]?\d*$/.test(value);
            });
        } else if (filter === 'positiveFloatMax') {
            $(field).inputFilter(function(value) {
              return /^\d*[.]?\d*$/.test(value) && (value === "" || parseFloat(value) <= $(field).attr('max'));
            });
        } else if (filter === 'percent') {
            $(field).inputFilter(function(value) {
              return /^-?\d*[.]?\d{0,2}$/.test(value);
            });
        } else if (filter === 'positivePercent') {
            $(field).inputFilter(function(value) {
              return /^\d*[.]?\d{0,2}$/.test(value);
            });
        } else if (filter === 'positivePercentMax') {
            $(field).inputFilter(function(value) {
              return /^\d*[.]?\d{0,2}$/.test(value) && (value === "" || parseFloat(value) <= $(field).attr('max'));
            });
        } else if (filter === 'currency') {
            $(field).inputFilter(function(value) {
              return /^-?\d*[.,]?\d{0,2}$/.test(value);
            });
        } else if (filter === 'positiveCurrency') {
            $(field).inputFilter(function(value) {
              return /^\d*[.,]?\d{0,2}$/.test(value);
            });
        } else if (filter === 'positiveCurrencyMax') {
            $(field).inputFilter(function(value) {
              return /^\d*[.,]?\d{0,2}$/.test(value) && (value === "" || parseFloat(value) <= $(field).attr('max'));
            });
        } else if (filter === 'char') {
            $(field).inputFilter(function(value) {
              return /^[a-z]*$/i.test(value);
            });
        } else if (filter === 'hex') {
            $(field).inputFilter(function(value) {
              return /^[0-9a-f]*$/i.test(value);
            });
        }
    }

    function initInput(fieldId, options) {
        if (options.beforeInit) {
            options.beforeInit(dataCollection, options);
        }
        thisFieldId = fieldId;
        fieldId = document.getElementById(fieldId);
        var buttonId, button, buttonArr;

        if (fieldId.previousElementSibling && fieldId.previousElementSibling.children[0]) {
            if (fieldId.previousElementSibling.children[0].classList.contains('dropdown-toggle')) {
                buttonArr = fieldId.previousElementSibling.children[1].querySelectorAll('a');
                for (button = buttonArr.length - 1; button >= 0; button--) {
                    buttonId = buttonArr[button].id;
                    if (options[buttonId]) {
                        buttonArr[button].addEventListener('click', function(buttonId) {
                            options[buttonId.target.id]();//call function
                        }, false);
                    }
                }
            } else if (!fieldId.previousElementSibling.children[0].classList.contains('dropdown-toggle')) {
                if (fieldId.previousElementSibling.children[0].tagName === 'BUTTON') {
                    buttonId = fieldId.previousElementSibling.children[0].id;
                    if (options[buttonId]) {
                        buttonId.addEventListener('click', function(buttonId) {
                            options[buttonId]();//call function
                        }, false);
                    }
                }
            }
        }
        if (fieldId.nextElementSibling && fieldId.nextElementSibling.children[0]) {
            if (fieldId.nextElementSibling.children[0].classList.contains('dropdown-toggle')) {
                buttonArr = fieldId.nextElementSibling.children[1].querySelectorAll('a');
                for (button = buttonArr.length - 1; button >= 0; button--) {
                    buttonId = buttonArr[button].id;
                    if (options[buttonId]) {
                        buttonArr[button].addEventListener('click', function(buttonId) {
                            options[buttonId.target.id]();//call function
                        }, false);
                    }
                }
            } else if (!fieldId.nextElementSibling.children[0].classList.contains('dropdown-toggle')) {
                if (fieldId.nextElementSibling.children[0].tagName === 'BUTTON') {
                    buttonId = fieldId.nextElementSibling.children[0].id;
                    if (options[buttonId]) {
                        buttonId.addEventListener('click', function(buttonId) {
                            options[buttonId]();//call function
                        }, false);
                    }
                }
            }
        }
        if ($(fieldId).attr('type') === 'text' && $(fieldId).data('fieldinputfilter')) {
            applyInputFilter($(fieldId), $(fieldId).data('fieldinputfilter'));
        }

        if ($(fieldId).attr('type') === 'password') {
            if ($(fieldId).parents('.form-group').siblings().children().length > 0 &&
                $(fieldId).parents('.form-group').siblings().children()[0].id ===
                    fieldId.id + '-strength-meter'
            ) {
                $('#' + fieldId.id + '-strength-meter').strengthMeter({
                    "url" : dataCollection.env.httpScheme + '://' + dataCollection.env.httpHost + '/' + dataCollection.env.appRoute + '/auth/checkPwStrength'
                });
            }

            if ($('#' + fieldId.id + '-visibility').length > 0) {
                $('#' + fieldId.id + '-visibility').off();
                $('#' + fieldId.id + '-visibility').click(function(e) {
                    e.preventDefault();

                    if ($(fieldId).attr('type') === 'password') {
                        $(fieldId).attr('type', 'text');
                        $('#' + fieldId.id + '-visibility').children('i').removeClass('fa-eye-slash').addClass('fa-eye');
                    } else if ($(fieldId).attr('type') === 'text') {
                        $(fieldId).attr('type', 'password');
                        $('#' + fieldId.id + '-visibility').children('i').removeClass('fa-eye').addClass('fa-eye-slash');
                    }
                });
            }

            if ($('#' + fieldId.id + '-password_generate').length > 0) {
                $('#' + fieldId.id + '-password_generate').off();
                $('#' + fieldId.id + '-password_generate').click(function(e) {
                    e.preventDefault();

                    var postData = { };
                    postData[$('#security-token').attr('name')] = $('#security-token').val();

                    if ($('#' + sectionId + '-password_policy_complexity').val() === 'simple' ||
                        $('#' + sectionId + '-password-policy-simple').length > 0
                    ) {
                        postData['passwordpolicycomplexity'] = 'simple';
                        if ($('#' + sectionId + '-password_policy_simple_acceptable_level').val() == '1' ||
                            $('#' + sectionId + '-password-policy-simple').data('simple_acceptable_level') == '1'
                        ) {
                            postData['passwordpolicylengthmin'] = '4';
                        } else if ($('#' + sectionId + '-password_policy_simple_acceptable_level').val() == '2' ||
                            $('#' + sectionId + '-password-policy-simple').data('simple_acceptable_level') == '2'
                        ) {
                            postData['passwordpolicylengthmin'] = '8';
                        } else if ($('#' + sectionId + '-password_policy_simple_acceptable_level').val() == '3' ||
                            $('#' + sectionId + '-password-policy-simple').data('simple_acceptable_level') == '3'
                        ) {
                            postData['passwordpolicylengthmin'] = '10';
                        } else if ($('#' + sectionId + '-password_policy_simple_acceptable_level').val() == '4' ||
                            $('#' + sectionId + '-password-policy-simple').data('simple_acceptable_level') == '4'
                        ) {
                            postData['passwordpolicylengthmin'] = '12';
                        }
                        postData = $.extend(postData, $('#' + sectionId + '-password-policy-simple').data());
                    } else if ($('#' + sectionId + '-password_policy_complexity').val() === 'complex' ||
                               $('#' + sectionId + '-password-policy-complex').length > 0
                    ) {
                        postData['passwordpolicycomplexity'] = 'complex';
                        if ($('#' + sectionId + '-password_policy_complexity').val() === 'complex') {
                            postData['passwordpolicylengthmin'] = $('#' + sectionId + '-password_policy_length_min').val();
                            if (postData['passwordpolicylengthmin'] === '' || postData['passwordpolicylengthmin'] === '0') {
                                postData['passwordpolicylengthmin'] = 8;
                            }
                            postData['passwordpolicylengthmax'] = $('#' + sectionId + '-password_policy_length_max').val();
                            if (postData['passwordpolicylengthmax'] === '' || postData['passwordpolicylengthmax'] === '0') {
                                postData['passwordpolicylengthmax'] = 8;
                            }
                            postData['passwordpolicyuppercase'] = $('#' + sectionId + '-password_policy_uppercase')[0].checked;
                            if ($('#' + sectionId + '-password_policy_uppercase')[0].checked === true) {
                                postData['passwordpolicyuppercasemincount'] = $('#' + sectionId + '-password_policy_uppercase_min_count').val();
                                postData['passwordpolicyuppercasemaxcount'] = $('#' + sectionId + '-password_policy_uppercase_max_count').val();
                                postData['passwordpolicyuppercaseinclude'] = $('#' + sectionId + '-password_policy_uppercase_include').val();
                            }
                            postData['passwordpolicylowercase'] = $('#' + sectionId + '-password_policy_lowercase')[0].checked;
                            if ($('#' + sectionId + '-password_policy_lowercase')[0].checked === true) {
                                postData['passwordpolicylowercasemincount'] = $('#' + sectionId + '-password_policy_lowercase_min_count').val();
                                postData['passwordpolicylowercasemaxcount'] = $('#' + sectionId + '-password_policy_lowercase_max_count').val();
                                postData['passwordpolicylowercaseinclude'] = $('#' + sectionId + '-password_policy_lowercase_include').val();
                            }
                            postData['passwordpolicynumbers'] = $('#' + sectionId + '-password_policy_numbers')[0].checked;
                            if ($('#' + sectionId + '-password_policy_numbers')[0].checked === true) {
                                postData['passwordpolicynumbersmincount'] = $('#' + sectionId + '-password_policy_numbers_min_count').val();
                                postData['passwordpolicynumbersmaxcount'] = $('#' + sectionId + '-password_policy_numbers_max_count').val();
                                postData['passwordpolicynumbersinclude'] = $('#' + sectionId + '-password_policy_numbers_include').val();
                            }
                            postData['passwordpolicysymbols'] = $('#' + sectionId + '-password_policy_symbols')[0].checked;
                            if ($('#' + sectionId + '-password_policy_symbols')[0].checked === true) {
                                postData['passwordpolicysymbolsmincount'] = $('#' + sectionId + '-password_policy_symbols_min_count').val();
                                postData['passwordpolicysymbolsmaxcount'] = $('#' + sectionId + '-password_policy_symbols_max_count').val();
                                postData['passwordpolicysymbolsinclude'] = $('#' + sectionId + '-password_policy_symbols_include').val();

                            }
                            postData['passwordpolicyavoidsimilar'] = $('#' + sectionId + '-password_policy_avoid_similar')[0].checked;
                            if ($('#' + sectionId + '-password_policy_avoid_similar')[0].checked === true) {
                                postData['passwordpolicyavoidsimilarcharacters'] = $('#' + sectionId + '-password_policy_avoid_similar_characters').val();
                            }
                        } else if ($('#' + sectionId + '-password-policy-complex').length > 0) {
                            postData = $.extend(postData, $('#' + sectionId + '-password-policy-complex').data());
                        }
                    }

                    var url = dataCollection.env.httpScheme + '://' + dataCollection.env.httpHost + '/' + dataCollection.env.appRoute + '/auth/generatePw'
                    $.post(url, postData, function(response) {
                        if (response.responseCode == 0) {
                            PNotify.success(response.responseMessage);
                        } else {
                            PNotify.error(response.responseMessage);
                        }
                        if (response.responseData.password) {
                            $(fieldId).val(response.responseData.password).trigger('change');
                            if ($(fieldId).attr('type') === 'password') {
                                $(fieldId).attr('type', 'text');
                                $('#' + fieldId.id + '-visibility').children('i').removeClass('fa-eye-slash').addClass('fa-eye');
                            }
                        }
                        if (response.tokenKey && response.token) {
                            $("#security-token").attr("name", response.tokenKey);
                            $("#security-token").val(response.token);
                        }
                    }, 'json');
                });
            }
        }

        if ($('#' + fieldId.id + '-fieldEdit').length > 0) {
            $('#' + fieldId.id + '-fieldEdit').off();
            $('#' + fieldId.id + '-fieldEdit').click(function(e) {
                e.preventDefault();

                if ($(fieldId).attr('disabled') === 'disabled') {
                    $(fieldId).attr('disabled', false);
                } else {
                    $(fieldId).attr('disabled', true);
                }
            });
        }

        maxLength(thisFieldId, options);
        if (options.afterInit) {
            options.afterInit(dataCollection, options);
        }
    }

    function initSelect2(fieldId, sectionId, options) {
        if (options.beforeInit) {
            options.beforeInit(dataCollection, options);
        }
        thisFieldId = fieldId;
        fieldId = document.getElementById(fieldId);
        options = $.extend({
            placeholder: 'MISSING PLACEHOLDER'
        }, options);

        if ($(fieldId).data('create') && $(fieldId).data('create') === true) {
            options = $.extend({
                tags: true,
                createTag: function(params) {
                    var term = $.trim(params.term);

                    if (term === '') {
                        return null;
                    }

                    return {
                        id: term.toLowerCase(),
                        text: term,
                        newTag: true
                    }
                }
            }, options);
        }

        dataCollection[componentId][sectionId][thisFieldId]['select2'] = $(fieldId).select2(options);
        // validation
        if (dataCollection[componentId][sectionId][sectionId + '-form'] &&
            dataCollection[componentId][sectionId][sectionId + '-form'].rules[thisFieldId] === 'required') {
            $(fieldId).on('select2:select', function() {
                $(this).valid();
            });
        }
        if (options.afterInit) {
            options.afterInit(dataCollection, options);
        }
    }

    function initCheckbox(fieldId, options) {
        if (options.beforeInit) {
            options.beforeInit(dataCollection, options);
        }
        thisFieldId = fieldId;
        fieldId = document.getElementById(fieldId);
        if (options.afterInit) {
            options.afterInit(dataCollection, options);
        }
    }

    function initRadio(fieldId, options) {
        if (options.beforeInit) {
            options.beforeInit(dataCollection, options);
        }
        thisFieldId = fieldId;
        fieldId = document.getElementById(fieldId);
        // Remove checked radio bg on toggle
        if ($(fieldId).find('label.btn').length > 0) {
            $(fieldId).find('label.btn').each(function() {
                $(this).click(function() {
                    $(this).siblings('label.btn').each(function() {
                        $(this).removeClass(function(index, css) {
                            return (css.match(/\bbg-\S+/g) || []).join(' ');
                        });
                    });
                });
            });
        }
        if (options.afterInit) {
            options.afterInit(dataCollection, options);
        }
    }

    function initFlatpickr(fieldId, options) {
        if (options.beforeInit) {
            options.beforeInit();
        }
        // thisFieldId = fieldId;
        // fieldId = $('#' + fieldId).parent();
        options = $.extend({
            mode            : 'single',
            enableTime      : false,
            altInput        : true,
            altFormat       : "d-m-Y",
            dateFormat      : "d-m-Y",
        }, options);

        dataCollection[componentId][sectionId][fieldId]['flatpickr'] = flatpickr('#' + fieldId, options);

        if ($('#' + fieldId + '-clear').length > 0) {
            $('#' + fieldId + '-clear').click(function() {
                dataCollection[componentId][sectionId][fieldId]['flatpickr'].clear();
                dataCollection[componentId][sectionId][fieldId]['flatpickr'].close();
            });
        }
        if (options.afterInit) {
            options.afterInit(dataCollection, options);
        }
    }

    function initColorpicker(fieldId, options) {
        if (options.beforeInit) {
            options.beforeInit();
        }

        var id = $('#' + fieldId).parents('.input-group')[0].id;

        dataCollection[componentId][sectionId][fieldId]['colorpicker'] = $('#' + id).colorpicker(options);

        if (options.afterInit) {
            options.afterInit(dataCollection, options);
        }
    }

    function initTextarea(fieldId, options) {
        if (options.beforeInit) {
            options.beforeInit(dataCollection, options);
        }
        thisFieldId = fieldId;
        fieldId = document.getElementById(fieldId);
        maxLength(thisFieldId, options);
        if (options.afterInit) {
            options.afterInit(dataCollection, options);
        }
    }

    function initJson(fieldId, options) {
        if (options.beforeInit) {
            options.beforeInit(dataCollection, options);
        }
        thisFieldId = fieldId;
        fieldId = document.getElementById(fieldId);
        maxLength(thisFieldId, options);
        if (options.afterInit) {
            options.afterInit(dataCollection, options);
        }
    }

    function initTrumbowyg(fieldId, options) {
        if (options.beforeInit) {
            options.beforeInit(dataCollection, options);
        }
        thisFieldId = fieldId;
        fieldId = document.getElementById(fieldId);
        options = $.extend({
            imageWidthModalEdit: true,
            urlProtocol: true,
            tagsToRemove: ['script', 'link'],
            btnsDef: {
                image: {
                    dropdown: ['insertImage', 'base64', 'upload'],
                    ico: 'insertImage'
                },
                justifyFull: {
                    dropdown: ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                    ico: 'justifyFull'
                },
                unorderedList: {
                    dropdown: ['unorderedList', 'orderedList'],
                    ico: 'unorderedList'
                }
            },
            btns: [
                ['viewHTML', 'formatting', 'historyUndo', 'historyRedo'],
                ['fontfamily', 'fontsize', 'superscript', 'subscript'],
                ['strong', 'em', 'del', 'lineheight', 'preformatted', 'specialChars'],
                ['foreColor', 'backColor'],
                ['justifyFull'],
                ['unorderedList'],
                ['table', 'horizontalRule'],
                ['removeformat', 'fullscreen', 'template']
            ],
            plugins: {
                table: {
                    rows: 4,
                    columns: 4
                },
                resizimg: {
                    minSize: 64,
                    step: 16,
                }
            }
        }, options);
        dataCollection[componentId][sectionId][thisFieldId]['trumbowyg'] =
            $(fieldId).trumbowyg(options);
        if (options.afterInit) {
            options.afterInit(dataCollection, options);
        }
    }

    function initCounters(fieldId, options) {
        if (options.beforeInit) {
            options.beforeInit(dataCollection, options);
        }
        thisFieldId = fieldId;
        fieldId = document.getElementById(fieldId);
        if (options.afterInit) {
            options.afterInit(dataCollection, options);
        }
    }

    function initJstree(fieldId, options) {
        if (options.beforeInit) {
            options.beforeInit();
        }
        thisFieldId = fieldId;
        fieldId = document.getElementById(fieldId);
        options = $.extend({ }, options);
        // Init
        dataCollection[componentId][sectionId][thisFieldId]['jstree'] = $(fieldId).jstree(options);

        var jstreeElements = $(fieldId).jstree().get_json('#',{'flat':true});
        if (jstreeElements.length === 0) {
            $('#' + thisFieldId + '-tree-search-input').parents('.form-group').first().addClass('d-none');
            $('#' + thisFieldId + '-tree-search-input').val('');
            $('#' + thisFieldId + '-tools-add').attr('hidden', true);
            $('#' + thisFieldId + '-tools-edit').attr('hidden', true);
            $('#' + thisFieldId + '-tools-collapse').attr('hidden', true);
            $('#' + thisFieldId + '-tools-expand').attr('hidden', true);

            var emptyTreeText = 'NO ' + $('#' + thisFieldId + '-tree-div').siblings('label').html() + '!';
            if (options.bazJstreeOptions.treeEmptyText && options.bazJstreeOptions.treeEmptyText !== '') {
                emptyTreeText = options.bazJstreeOptions.treeEmptyText;
            }
            $('#' + thisFieldId + '-tree-div').append(
                '<span id="' + thisFieldId + '-tree-empty">' + emptyTreeText + '</span>'
            );

            if (options.afterInit) {
                options.afterInit(dataCollection, options);
            }
            return;
        }

        // Search
        if (options.bazJstreeOptions.search == null || options.bazJstreeOptions.search) {
            $('#' + thisFieldId + '-tree-search-input').parents('.form-group').first().removeClass('d-none');
            $('#' + thisFieldId + '-tree-search-input').off();
            $('#' + thisFieldId + '-tree-search-input').on('keyup', function() {
                $(fieldId).jstree(true).search($(this).val());
            });
        }

        var selectedNode;
        // Add New Node
        if (options.bazJstreeOptions.add == null || options.bazJstreeOptions.add) {
            $('#' + thisFieldId + '-tools-add').attr('hidden', false);
            $('#' + thisFieldId + '-tools-add').off();
            $('#' + thisFieldId + '-tools-add').click(function(e) {
                e.preventDefault();
                var thisFieldId = $(fieldId)[0].id;
                selectedNode = $(fieldId).jstree('get_selected', true);
                // Check if node are selected and only 1 is selected
                if ($(selectedNode).length !== 1) {
                    $('.ui-pnotify').remove();
                    PNotify.notice({
                        title: 'None or Multiple ' + options.bazJstreeOptions.treeName + ' selected!',
                        text: 'Please select only 1 ' + options.bazJstreeOptions.treeName + ' to create a new node under it'
                    });
                    pnotifySound.play();
                    return false;
                } else {
                    $('#' + thisFieldId + '-tree-search-input').parents('.form-group').first().addClass('d-none');
                    $('#' + thisFieldId + '-tree-edit-input').parents('.form-group').first().addClass('d-none');
                    $('#' + thisFieldId + '-tree-add-input').parents('.form-group').first().removeClass('d-none');
                    $('#' + thisFieldId + '-tree-add-input').focus();
                    $('#' + thisFieldId + '-tree-add-input-cancel').click(function() {
                        $('#' + thisFieldId + '-tree-add-input').val(null);
                        $('#' + thisFieldId + '-tree-search-input').parents('.form-group').first().removeClass('d-none');
                        $('#' + thisFieldId + '-tree-add-input').parents('.form-group').first().addClass('d-none');
                        $('#' + thisFieldId + '-tree-add-input-success').off();
                    });
                    $('#' + thisFieldId + '-tree-add-input-success').click(function() {
                        modifyJsTree($(fieldId), thisFieldId, 'addNode', this, $('#' + thisFieldId + '-tree-add-input'), selectedNode, options.bazJstreeOptions.addFunction);
                    });
                    $('#' + thisFieldId + '-tree-add-input').keypress(function() {
                        var keycode = (event.keyCode ? event.keyCode : event.which);
                        if(keycode == '13'){
                            modifyJsTree($(fieldId), thisFieldId, 'addNode', this, $('#' + thisFieldId + '-tree-add-input-success'), selectedNode, options.bazJstreeOptions.addFunction);
                        }
                    });
                }
            });
        }
        // Edit Selected Node
        if (options.bazJstreeOptions.edit == null || options.bazJstreeOptions.edit) {
            $('#' + thisFieldId + '-tools-edit').attr('hidden', false);
            $('#' + thisFieldId + '-tools-edit').off();
            $('#' + thisFieldId + '-tools-edit').click(function(e) {
                e.preventDefault();
                selectedNode = $(fieldId).jstree('get_selected', true);
                var thisFieldId = $(fieldId)[0].id;
            // Check if node are selected and only 1 is selected
                if ($(selectedNode).length !== 1) {
                    $('.ui-pnotify').remove();
                    PNotify.notice({
                        title: 'None or Multiple ' + options.bazJstreeOptions.treeName + ' selected!',
                        text: 'Please select only 1 ' + options.bazJstreeOptions.treeName + ' to rename',
                    });
                    pnotifySound.play();
                    return false;
                } else {
                    $('#' + thisFieldId + '-tree-search-input').parents('.form-group').first().addClass('d-none');
                    $('#' + thisFieldId + '-tree-add-input').parents('.form-group').first().addClass('d-none');
                    $('#' + thisFieldId + '-tree-edit-input').parents('.form-group').first().removeClass('d-none');
                    $('#' + thisFieldId + '-tree-edit-input').val(selectedNode[0].text).focus();
                    $('#' + thisFieldId + '-tree-edit-input-cancel').click(function() {
                        $('#' + thisFieldId + '-tree-edit-input').val(null);
                        $('#' + thisFieldId + '-tree-search-input').parents('.form-group').first().removeClass('d-none');
                        $('#' + thisFieldId + '-tree-edit-input').parents('.form-group').first().addClass('d-none');
                        $('#' + thisFieldId + '-tree-edit-input-success').off();
                    });
                    $('#' + thisFieldId + '-tree-edit-input-success').click(function() {
                        modifyJsTree($(fieldId), thisFieldId, 'editNode', this, $('#' + thisFieldId + '-tree-edit-input'), selectedNode, options.bazJstreeOptions.editFunction);
                    });
                    $('#' + thisFieldId + '-tree-edit-input').keypress(function() {
                        var keycode = (event.keyCode ? event.keyCode : event.which);
                        if(keycode == '13'){
                            modifyJsTree($(fieldId), thisFieldId, 'editNode', this, $('#' + thisFieldId + '-tree-edit--input-success'), selectedNode, options.bazJstreeOptions.editFunction);
                        }
                    });
                }
            });
        }
        // Collapse all Nodes
        if (options.bazJstreeOptions.collapse == null || options.bazJstreeOptions.collapse) {
            $('#' + thisFieldId + '-tools-collapse').attr('hidden', false);
            $('#' + thisFieldId + '-tools-collapse').off();
            $('#' + thisFieldId + '-tools-collapse').click(function(e) {
                e.preventDefault();
                $(fieldId).jstree('deselect_all');
                $(fieldId).jstree('close_all');
            });
        }
        // Expand all Nodes
        if (options.bazJstreeOptions.expand == null || options.bazJstreeOptions.expand) {
            $('#' + thisFieldId + '-tools-expand').attr('hidden', false);
            $('#' + thisFieldId + '-tools-expand').off();
            $('#' + thisFieldId + '-tools-expand').click(function(e) {
                e.preventDefault();
                $(fieldId).jstree('deselect_all');
                $(fieldId).jstree('open_all');
            });
        }
        // First Open
        if (options.bazJstreeOptions.firstOpen == null || options.bazJstreeOptions.firstOpen) {
            var firstId = $(fieldId)[0].children[0].children[0].id;
            $(fieldId).jstree('open_node', firstId);
        }
        //All Open
        if (options.bazJstreeOptions.allOpen == null || options.bazJstreeOptions.allOpen) {
            $(fieldId).jstree('open_all');
        }
        //All Checked
        if (options.bazJstreeOptions.allChecked == null || options.bazJstreeOptions.allChecked) {
            $(fieldId).jstree('check_all');
        }
        // Show all children if root is clicked
        if (options.bazJstreeOptions.toggleAllChildren == null || options.bazJstreeOptions.toggleAllChildren) {
            $(fieldId).on('select_node.jstree', function(e, data) {
                if (data.node.children.length > 0) {
                    $(fieldId).jstree('open_all', data.node.id);
                }
            });
            $(fieldId).on('close_node.jstree', function(e, data) {
                $(fieldId).jstree('deselect_node', data.node.id);
            });
        }
        // Select only EndNode to perform actions
        if (options.bazJstreeOptions.selectEndNodeOnly == null || options.bazJstreeOptions.selectEndNodeOnly) {
            $(fieldId).on('select_node.jstree', function (e,data) {
                if (data.node.children.length > 0) {
                    $(fieldId).jstree('deselect_node', data.node.id);
                }
            });
        }
        //HideAll Jstree default icons (only works if fieldJstreeDoubleClickToggle is set to true)
        if (options.bazJstreeOptions.hideJstreeIcon == null || options.bazJstreeOptions.hideJstreeIcon) {
            $(fieldId).find('.jstree-ocl').hide();
            $(fieldId).on('open_node.jstree close_node.jstree', function() {
                $(fieldId).find('.jstree-ocl').hide();
            });
        }
        if ($(fieldId).parents('form').length !== 0) {
            if (options[$(fieldId).parents('form')[0].id] && options[$(fieldId).parents('form')[0].id].rules[$(fieldId)[0].id + '-validate'] === 'required') {
                $(fieldId).on('select_node.jstree', function() {
                    $('#' + $(this)[0].id + '-validate').val(null);
                    if ($(fieldId).jstree('get_selected', true).length > 0 ) {
                        $('#' + $(this)[0].id + '-validate').val('selected');
                        $('#' + $(this)[0].id + '-validate').valid();
                        $(fieldId).removeClass('border-danger').addClass('border-default');
                        $(fieldId).siblings('#' + $(this)[0].id + '-tree-search').find('.border-danger').removeClass('border-danger').addClass('border-default');
                        $(fieldId).siblings('#' + $(this)[0].id + '-tree-search').find('.bg-danger').removeClass('bg-danger').addClass('bg-default');
                    }
                });
            }
        }
        // ModifyJsTree
        function modifyJsTree(tree, optionsId, task, elthis, elthat, selectedNode, runFunction) {
            var thisFieldId = optionsId;
            if (task === 'addNode') {
                tree.jstree('create_node',
                    $('#' + selectedNode[0].id),
                    $('#' + optionsId + '-tree-add-input').val(),
                    'last',
                    function() {
                        tree.jstree('open_node', $('#' + selectedNode[0].id));
                    }
                );
                $('#' + thisFieldId + '-tree-search-input').parents('.form-group').first().removeClass('d-none');
                $('#' + thisFieldId + '-tree-add-input').parents('.form-group').first().addClass('d-none');
                $('#' + optionsId + '-tree-add-input').val(null);
                $(elthis).off();
                $(elthat).off();
                runFunction();
            } else if (task === 'editNode') {
                tree.jstree('rename_node',
                    $('#' + selectedNode[0].id),
                    $('#' + optionsId + '-tree-edit-input').val()
                );
                $('#' + thisFieldId + '-tree-search-input').parents('.form-group').first().removeClass('d-none');
                $('#' + thisFieldId + '-tree-edit-input').parents('.form-group').first().addClass('d-none');
                $('#' + optionsId + '-tree-edit-input').val(null);
                $(elthis).off();
                $(elthat).off();
                runFunction();
            }
        }

        if (options.bazJstreeOptions.replaceIdWithDataField && options.bazJstreeOptions.replaceIdWithDataField !== '') {
            var allNodes = $(fieldId).jstree().get_json('#',{'flat':true});

            for (var i = 0; i < allNodes.length; i++) {
                var newId = null;
                if (allNodes[i].data[options.bazJstreeOptions.replaceIdWithDataField]) {
                    newId = allNodes[i].data[options.bazJstreeOptions.replaceIdWithDataField];
                }
                if (newId) {
                    $(fieldId).jstree().set_id(allNodes[i], newId);
                }
            }
        }

        if (options.afterInit) {
            options.afterInit(dataCollection, options);
        }
    }

    function initHTML(fieldId, options) {
        if (options.beforeInit) {
            options.beforeInit(dataCollection, options);
        }
        if (options.afterInit) {
            options.afterInit(dataCollection, options);
        }
    }

    function initDropzone(fieldId, options) {
        if (options.beforeInit) {
            options.beforeInit();
        }
        thisFieldId = fieldId;
        fieldId = document.getElementById(fieldId);
        dataCollection[componentId][sectionId][thisFieldId]['dropzone'] = $(fieldId).dropzone(options);
        if (options.afterInit) {
            options.afterInit(dataCollection, options);
        }
    }

    function initDatatable(fieldId, options) {
        if (options.beforeInit) {
            options.beforeInit();
        }
        thisFieldId = fieldId;
        fieldId = document.getElementById(fieldId);
        if (options.dataTables) {
            for (var datatable in options.dataTables) {
                var datatableTable = options.dataTables[datatable];
                var datatableConfig = dataCollection[componentId][sectionId][datatableTable];
                if (datatableConfig) {
                    if (datatableConfig.bazdatatable && datatableConfig.bazdatatable.compareData) {
                        if (datatableConfig.bazdatatable.compareData.inclIds) {
                            var datatableIncludes = datatableConfig.bazdatatable.compareData.inclIds;
                            for (var datatableInclude in datatableIncludes) {
                                var toolTipTitle = $('#' + datatableInclude).parents('.form-group').find('label').siblings('i').attr('title');
                                toolTipTitle += '<br><span>NOTE: Field should be unique</span>';
                                if (datatableIncludes[datatableInclude].length > 0) {
                                    toolTipTitle += '<br><span>UNIQUE KEYWORDS: ' + datatableIncludes[datatableInclude].toString() + '</span>';
                                }
                                $('#' + datatableInclude).parents('.form-group').find('label').siblings('i').attr('title', toolTipTitle).addClass('text-warning');
                            }
                        }
                        // NOTE: exclude is very difficult to narrow. Avoid using excludes and use twig template {{fieldUnique}}
                    }
                } else {
                    error('Datatable ' + datatableTable + ' is defined, but no configuration assigned to it!')
                }
            }
            // this._fieldsToDatatable(fieldId);
        } else {
            error('Tables not assigned to ' + thisFieldId + '. They need to be assigned in an array, please see documentation');
        }
        if (options.afterInit) {
            options.afterInit(dataCollection, options);
        }
    }

            // BazContentFields._jQueryInterface = function _jQueryInterface(options) {
            //     var data = $(this).data(DATA_KEY);
            //     var _options = $.extend({}, Default, options);

            //     if (!data) {
            //         data = new BazContentFields($(this), _options);
            //         $(this).data(DATA_KEY, typeof _options === 'string' ? data : _options);
            //     }
            // };
    // }
    function bazContentFieldsConstructor() {
        // if something needs to be constructed
        return null;
    }

    function setup(BazContentFieldsConstructor) {
        BazContentFields = BazContentFieldsConstructor;
        BazContentFields.defaults = { };
        BazContentFields.init = function(options) {
            if (options.fieldId) {
                initFields(_extends(BazContentFields.defaults, options));
            } else {
                init(_extends(BazContentFields.defaults, options));
                initFields();
            }
        }
    }

    setup(bazContentFieldsConstructor);

    return bazContentFieldsConstructor;

}();
//         return BazContentFields;

//         }();

//     $.fn[NAME] = BazContentFields._jQueryInterface;
//     $.fn[NAME].Constructor = BazContentFields;

//     $.fn[NAME].noConflict = function () {
//         $.fn[NAME] = JQUERY_NO_CONFLICT;
//         return BazContentFields._jQueryInterface;
//     };

//     return BazContentFields;
// }(jQuery);

// exports.BazContentFields = BazContentFields;

// Object.defineProperty(exports, '__esModule', { value: true });

// }));
/* exported BazContentFieldsValidator */
/* globals */
/*
* @title                    : BazContentFieldsValidator
* @description              : Baz Content Fields Validator Lib
* @developer                : guru@bazaari.com.au
* @usage                    : BazContentFieldsValidator._function_(_options_);
* @functions                :
* @options                  :
*/

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
// var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

$('body').on('libsLoadComplete', function() {
    if ($.validator && !$.validator.methods.json) {
        $.validator.addMethod("json", function(value) {
            try {
                JSON.parse(value);
                return true;
            } catch (error) {
                return false;
            }
        }, 'Please enter correct JSON format data.');
    }
});

// eslint-disable-next-line no-unused-vars
var BazContentFieldsValidator = function() {
    var BazContentFieldsValidator = void 0;
    var componentId,
        sectionId,
        on,
        errorSound,
        hasErrorCount, //Error counts to show during validation.
        formLocation, //Location of form, either in section or in datatable.
        validateForms = { }, //Validation of form on section submit
        validateDatatableOnSections, //Validation of datatable on section submit
        validateFormsOnDatatable, //Validate datatable form on datable submit
        dataCollection,
        formJsTreeSelector = null;
    var formValid = false;
    var hasError = []; //Validation, list of fields that has errors
    // var tableData = { }; //Datatable Data

    // Error
    function error(errorMsg) {
        throw new Error(errorMsg);
    }

    //Init
    function init(options) {
        componentId = options.componentId;
        sectionId = options.sectionId;
        dataCollection = window['dataCollection'];
        errorSound = dataCollection.env.sounds.swalSound //Error Sound for Swal

        formJsTreeSelector = null;
        if (options.formJsTreeSelector) {
            formJsTreeSelector = options.formJsTreeSelector;
        }

        if (options.on === 'section') {
            on = sectionId;
        } else if (options.on === 'component'){
            on = componentId;
        } else if (options.on === 'datatable'){
            on = componentId;// Check
        } else {
            error('on option not set in BazContentFieldsValidator.')
        }
    }

    //Init Validator
    function initValidator() {
        dataCollection[componentId][sectionId]['initValidator'] = true;
        var formId, validateOptions;
        validateForms[componentId] = { };
        validateForms[componentId][sectionId] = [];
        validateDatatableOnSections = { };
        validateFormsOnDatatable = [];
        if (!$.fn.validate) {
            error('Validator not found!');
        } else {
            $('#' + on).find('form').each(function(index,form) {

                formId = $(form)[0].id;

                $.validator.setDefaults({
                    debug: false,
                    ignore: ':submit, :reset, :image, :disabled, :hidden, .ignore, .cr-slider',
                    onkeyup: false,
                    onclick: false,
                    submitHandler: function() { },
                    focusInvalid: false
                });

                validateOptions = {
                    errorElement: 'div',
                    errorPlacement: function (error,element) {
                        if (!formValid) {
                            $(element).parents('.form-group').append(error);
                            $(error).addClass('text-uppercase text-danger text-xs help-block');
                            $(element).closest('.form-group').addClass('has-feedback');
                        }
                    },
                    highlight: function (element) {
                        $(element).closest('.form-group').addClass('has-error');
                    },
                    // unhighlight: function () { },
                    success: function (element) {
                        if (!formValid) {
                            var type = $(element).parents('form').data('validateon');
                            var formId = $(element).parents('form')[0].id;
                            componentId = $(element).parents('.component')[0].id;

                            if ($(element).parents('.sectionWithForm').length > 0) {
                                sectionId = $(element).parents('.sectionWithForm')[0].id;
                            } else if ($(element).parents('.sectionWithFormToDatatable').length > 0) {
                                sectionId = $(element).parents('.sectionWithFormToDatatable')[0].id;
                            }

                            $(element).closest('.form-group').removeClass('has-error');
                            $(element).closest('.help-block').remove();

                            validateForm(componentId, sectionId, true, type, true, formId);
                        }
                    }
                };
                if (dataCollection[componentId][sectionId][sectionId + '-form']) {
                    validateOptions = _extends(validateOptions, dataCollection[componentId][sectionId][sectionId + '-form']);
                }

                dataCollection[componentId][sectionId]['formValidator'] = $(form).validate(validateOptions);//init validate form

                if ($(form).data('validateon') === 'section') {
                    validateForms[componentId][sectionId].push(formId);
                }
                if ($(form).data('validateon') === 'datatable') {
                    validateFormsOnDatatable.push(formId);
                }
            });
            if ($('div[data-validateon="section"]').length !== 0) {
                $('div[data-validateon="section"]').each(function (index, datatable) {
                    if (!validateDatatableOnSections[$(datatable).parents('section')[0].id]) {
                        validateDatatableOnSections[$(datatable).parents('section')[0].id] = [ ];
                        validateDatatableOnSections[$(datatable).parents('section')[0].id].push(datatable.id);
                    } else {
                        validateDatatableOnSections[$(datatable).parents('section')[0].id].push(datatable.id);
                    }
                });
            }
        }
    }

    //Validate Sections on Submit
    function validateForm(componentId, sectionId, onSuccess, type, preValidated, formId) {
        if (!preValidated) {
            formValid = false;
            if (type === 'section') {
                // formLocation = componentId;
                // for (var component in validateForms[componentId]) {
                //     $.each(validateForms[componentId][sectionId], function(index, form) {
                //         $('#' + form).submit();
                //     });

                //     if (!($.isEmptyObject(validateDatatableOnSections))) {
                //         //Validating datatable if empty, throw error
                //         for (var sections in validateDatatableOnSections) {
                //             if (validateDatatableOnSections[sections].length > 0) {
                //                 $.each(validateDatatableOnSections[sections], function(index, datatable) {
                //                     if (!tableData[sections][datatable].data().any()) {
                //                         $('#' + datatable + '-table-div').addClass('form-group has-error has-feedback');
                //                         $('#' + datatable + '-table-data').removeClass('border-default').addClass('border-danger');
                //                         $('#' + datatable + '-table-error').remove();
                //                         $('#' + datatable).append(
                //                             '<div id="' + datatable + '-table-error" class="text-danger help-block">Table cannot be empty!</div>'
                //                         );
                //                     }
                //                 });
                //             }
                //         }
                //     }
                // }
            // } else if (type === 'section') {
                formLocation = sectionId;
                $.each(validateForms[componentId][sectionId], function(index, form) {
                    $('#' + form).submit();
                });
            } else if (type === 'datatable') {
                formLocation = formId;
                $('#' + formId).submit();
            }

            hasError = [];
            $('#' + formLocation).find('.has-error').each(function(index,errorId) {
                var id = $(errorId).find('label').html();
                hasError.push(id.toUpperCase());
            });
            hasErrorCount = hasError.length;

            if (!preValidated && hasErrorCount > 0) {
                $('#' + formLocation + '-alert').remove();
                $('#' + formLocation).before(
                '<div id="' + formLocation + '-alert" class="alert alert-danger alert-dismissible animated fadeIn rounded-0 mb-0">' +
                '   <button id="' + formLocation + '-alert-dismiss" type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' +
                '   <i class="icon fa fa-ban"></i>You have <strong>'+ hasErrorCount + '</strong> errors! ' +
                '   Please fix these errors before submitting the data' +
                '<div>'
                );
                errorSound.play();
                if (type === 'section') {
                    if (formJsTreeSelector) {
                        // BazContentFields.fixHeight('fixedHeight');
                        $(formJsTreeSelector).jstree(true).settings.search.search_callback = function(str, node) {
                            var word, words = [];
                            var searchFor = str.toUpperCase().replace(/^\s+/g, '').replace(/\s+$/g, '');
                            if (searchFor.indexOf(',') >= 0) {
                                words = searchFor.split(',');
                            } else {
                                words = [searchFor];
                            }
                            for (var i = 0; i < words.length; i++) {
                                word = words[i];
                                if ((node.text || "").indexOf(word) >= 0) {
                                    if (node.text === word) {
                                        return true;
                                    }
                                }
                            }
                            return false;
                        }
                        $(formJsTreeSelector).jstree(true).refresh();
                        // $('#' + formLocation + '-sections-tree').children('.card').removeClass('box-primary').addClass('box-danger');
                        // $('#' + formLocation + '-sections-tree').find('.card-header').children('strong').html(' Errors');
                        // $('#' + formLocation + '-sections-tree').find('.card-tools').addClass('hidden');
                        // $('#' + formLocation + '-sections-tree').find('.widget-icon').children('i').removeClass('fa-bars').addClass('fa-ban');
                        $(formJsTreeSelector).jstree(true).search(hasError.toString());
                        $('#' + formLocation + '-form-fields').find('.jstree-anchor').addClass('text-danger').css("text-transform", 'uppercase');
                        // $('#' + formLocation + '-form-fields-search-input').val(hasError.toString());
                        // $('#' + formLocation + '-form-fields-search-input').siblings('.input-group-addon').addClass('hidden');
                        // $('#' + formLocation + '-form-fields-search-input').siblings('.input-group-btn').removeClass('hidden');
                        // $('#' + formLocation + '-form-fields-search-input').attr('disabled', true);
                        // $('#' + formLocation + '-form-fields-search-cancel').click(function() {
                        //     cancelValidatingForm(type, formLocation);
                        // });
                    }
                    $('#' + formLocation + '-alert-dismiss').click(function() {
                        cancelValidatingForm(type, formLocation);
                    });
                    return false;
                // } else if (type === 'section') {
                //     $('#' + formLocation + '-alert-dismiss').click(function() {
                //         if ($(this).parents('.sectionWithForm').length > 0) {
                //             formLocation = $(this).parent().siblings('.sectionWithForm')[0].id;
                //         } else if ($(this).parents('.sectionWithFormToDatatable').length > 0) {
                //             formLocation = $(this).parent().siblings('.sectionWithFormToDatatable')[0].id;
                //         }
                //         cancelValidatingForm(type, formLocation);
                //     });
                //     return false;
                } else if (type === 'datatable') {
                    $('#' + formLocation + '-alert-dismiss').click(function() {
                        formLocation = $(this).parent().siblings('.sectionWithFormToDatatable')[0].id;
                        cancelValidatingForm(type, formLocation);
                    });
                    return false;
                }
            } else {
                if (type === 'datatable') {
                    return true;
                }
                formValid = true;
                return true;
            }
        } else {
            if (type === 'section') {

                hasError = [];
                $('#' + formLocation).find('.has-error').each(function(index,errorId) {
                    var id = $(errorId).find('label').html();
                    hasError.push(id.toUpperCase());
                });
                hasErrorCount = hasError.length;

                if (hasErrorCount > 0) {
                    $('#' + formLocation + '-alert').find('strong').html(hasErrorCount);
                    if (formJsTreeSelector) {
                        $(formJsTreeSelector).jstree(true).search(hasError.toString());
                        $('#' + formLocation + '-form-fields-tree-search-input').val(hasError.toString());
                        $('#' + formLocation + '-form-fields').find('.jstree-anchor').addClass('text-danger').css("text-transform", 'uppercase');
                    }
                    return false;
                } else {
                    if (!onSuccess) {
                        cancelValidatingForm(type, formLocation);
                    } else {
                        cancelValidatingForm(type, formLocation);
                    }
                    return true;
                }
            // } else if (type === 'section') {
            //     hasErrorCount = $('#' + sectionId).find('.has-error').length;
            //     hasError = [];

            //     $('#' + sectionId).find('.has-error').each(function(index,errorId) {
            //         var id = $(errorId).children('label').html();
            //         hasError.push(id.toUpperCase());
            //     });

            //     if (hasErrorCount > 0) {
            //         $('#' + sectionId + '-alert').find('strong').html(hasErrorCount);
            //         return false;
            //     } else {
            //         if (!onSuccess) {
            //             cancelValidatingForm(type, sectionId);
            //         } else {
            //             cancelValidatingForm(type, sectionId);
            //         }
            //         return true;
            //     }
            } else if (type === 'datatable') {
                if (hasErrorCount > 0) {
                    $('#' + formLocation + '-alert').find('strong').html(hasErrorCount);
                    return false;
                } else {
                    cancelValidatingForm(type, formLocation);
                    return true;
                }
            }
        }
    }

    //Cancel validating form
    function cancelValidatingForm(type, formLocation) {
        $('#' + formLocation + '-alert').remove();

        if (type === 'section') {
            if (formJsTreeSelector) {
                // BazContentFields.fixHeight('fixedHeight');
                // $('#' + formLocation + '-sections-tree').children('.card').removeClass('box-danger').addClass('box-primary');
                // $('#' + formLocation + '-sections-tree').find('.card-header').children('strong').html(' Sections');
                // $('#' + formLocation + '-sections-tree').find('.card-tools').removeClass('hidden');
                // $('#' + formLocation + '-sections-tree').find('.widget-icon').children('i').removeClass('fa-ban').addClass('fa-bars');
                // $('#' + formLocation + '-sections-jstree').find('.jstree-anchor').css("text-transform", 'uppercase');
                $('#' + formLocation + '-form-fields-tree-search-input').val('');
                $(formJsTreeSelector).jstree(true).search('');
                // $('#' + formLocation + '-sections-fields-search').attr('disabled', false);
                // $('#' + formLocation + '-sections-fields-search').siblings('.input-group-addon').removeClass('hidden');
                // $('#' + formLocation + '-sections-fields-search').siblings('.input-group-btn').addClass('hidden');
                $(formJsTreeSelector).jstree(true).settings.search.search_callback = function(str, node) {
                    var word, words = [];
                    var searchFor = str.toUpperCase().replace(/^\s+/g, '').replace(/\s+$/g, '');
                    if (searchFor.indexOf(',') >= 0) {
                        words = searchFor.split(',');
                    } else {
                        words = [searchFor];
                    }
                    for (var i = 0; i < words.length; i++) {
                        word = words[i];
                        if ((node.text || "").indexOf(word) >= 0) {
                            return true;
                        }
                    }
                    return false;
                }
            }
        } else if (type === 'datatable') {
            if ($('#' + formLocation).find('div').is('[data-bazscantype="jstree"]')) {
                $('#' + formLocation).find('[data-bazscantype="jstree"]').removeClass('border-danger').addClass('border-default');
                $('#' + formLocation).find('[type="search"]').removeClass('border-danger');
                $('#' + formLocation).find('[type="search"]').siblings('.input-group-addon').removeClass('bg-danger').addClass('bg-default');
            }
        }

        $('#' + formLocation).find('.form-group').each(function(i,v) {
            $(v).removeClass('has-error has-feedback');
        });

        $('#' + formLocation).find('.help-block').each(function(i,v) {
            $(v).remove();
        });

        //Cancel Validating datatable
        for (var sections in validateDatatableOnSections) {
            if (validateDatatableOnSections[sections].length > 0) {
                $.each(validateDatatableOnSections[sections], function(index, datatable) {
                    $('#' + datatable + '-data').removeClass('border-danger').addClass('border-default');
                });
            }
        }
    }

    function bazContentFieldsValidatorConstructor() {
        // if something needs to be constructed
        return null;
    }

    function setup(BazContentFieldsValidatorConstructor) {
        BazContentFieldsValidator = BazContentFieldsValidatorConstructor;
        BazContentFieldsValidator.initValidator = function(options) {
            BazContentFieldsValidator.defaults = { };
            init(_extends(BazContentFieldsValidator.defaults, options));
            initValidator();
        }
        BazContentFieldsValidator.validateForm = function(options) {
            BazContentFieldsValidator.defaults = { };
            init(_extends(BazContentFieldsValidator.defaults, options));
            var validate =
                validateForm(options.componentId, options.sectionId, options.onSuccess, options.type, options.preValidated, options.formId);
            return validate;
        }
        BazContentFieldsValidator.cancelValidatingForm = function(options) {
            BazContentFieldsValidator.defaults = { };
            init(_extends(BazContentFieldsValidator.defaults, options));
            cancelValidatingForm(options.type, options.formLocation);
        }
    }

    setup(bazContentFieldsValidatorConstructor);

    return bazContentFieldsValidatorConstructor;
}();
/* exported BazTunnels */
/* globals BazNotifications BazMessenger BazAnnouncements BazProgress ab BazHelpers */
/*
* @title                    : BazTunnels
* @description              : Baz Tunnels Lib for wstunnels
* @developer                : guru@bazaari.com.au
* @usage                    : BazTunnels._function_(_options_);
* @functions                : BazTunnelsInit
* @options                  :
*/

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
// var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

// eslint-disable-next-line no-unused-vars
var BazTunnels = function() {
    var BazTunnels = void 0;
    var dataCollection, timerId;
    dataCollection = window.dataCollection;
    dataCollection.env.wsTunnels = { };
    if (dataCollection.env.httpScheme === 'http') {
        dataCollection.env.wsTunnels.protocol = 'ws';
    } else if (dataCollection.env.httpScheme === 'https') {
        dataCollection.env.wsTunnels.protocol = 'wss';
    }
    dataCollection.env.wsTunnels.messenger = { };
    dataCollection.env.wsTunnels.pusher = { };

    // var reconnectMessengerTunnel = null;
    // var reconnectPusherTunnel = null;

    // Error
    // function error(errorMsg) {
    //     throw new Error(errorMsg);
    // }

    //Init
    function init() {
        // initMessengerOTR();
        initPusherTunnel();
    }

    // Init Messenger tunnel as needed. Messages can be transmitted purely on WSS avoiding message to be added to DB.
    function initMessengerOTR() {
        var url;

        if (dataCollection.env.websocketAppRoute) {
            url = dataCollection.env.wsTunnels.protocol + '://' + dataCollection.env.httpHost + '/messenger/app/' + dataCollection.env.websocketAppRoute;
        } else {
            url = dataCollection.env.wsTunnels.protocol + '://' + dataCollection.env.httpHost + '/messenger/';
        }

        dataCollection.env.wsTunnels.messenger = new WebSocket(url);

        dataCollection.env.wsTunnels.messenger.onopen = null;
        dataCollection.env.wsTunnels.messenger.onopen = function() {
            timerId = BazHelpers.getTimerId('initMessengerOTR');
            if (timerId) {
                BazHelpers.setTimeoutTimers.stop(timerId, null, 'initMessengerOTR');
            }
            BazMessenger.initOTR();
        };

        dataCollection.env.wsTunnels.messenger.onclose = null;
        dataCollection.env.wsTunnels.messenger.onclose = function() {
            BazMessenger.otrServiceOffline();
            BazHelpers.setTimeoutTimers.add(function() {
                BazNotifications.serviceOffline();
                initMessengerOTR();
            }, 10000, null, 'initMessengerOTR');
        };

        dataCollection.env.wsTunnels.messenger.onerror = null;
        dataCollection.env.wsTunnels.messenger.onerror = function() {
            dataCollection.env.wsTunnels.messenger.close();
        };

        dataCollection.env.wsTunnels.messenger.onmessage = null;
        dataCollection.env.wsTunnels.messenger.onmessage = function(e) {
            //eslint-disable-next-line
            console.log(e.data);
        };
    }

    function initPusherTunnel(options) {
        var tunnelsToInit = ['systemNotifications','messengerNotifications','systemAnnouncements','progress'];
        if (options && options.tunnelsToInit) {
            tunnelsToInit = options.tunnelsToInit;
        }

        var url;
        if (dataCollection.env.websocketAppRoute) {
            url = dataCollection.env.wsTunnels.protocol + '://' + dataCollection.env.httpHost + '/pusher/app/' + dataCollection.env.websocketAppRoute;
        } else {
            url = dataCollection.env.wsTunnels.protocol + '://' + dataCollection.env.httpHost + '/pusher/';
        }

        dataCollection.env.wsTunnels.pusher =
            new ab.Session(url,
                function() {
                    //eslint-disable-next-line
                    console.info('WebSocket connection open');

                    if (tunnelsToInit.includes('systemNotifications')) {
                        dataCollection.env.wsTunnels.pusher.subscribe('systemNotifications', function(topic, data) {
                            BazNotifications.onMessage('systemNotifications', data);
                        });
                    }

                    if (tunnelsToInit.includes('messengerNotifications')) {
                        dataCollection.env.wsTunnels.pusher.subscribe('messengerNotifications', function(topic, data) {
                            BazMessenger.onMessage(data);
                        });
                    }

                    if (tunnelsToInit.includes('systemAnnouncements')) {
                        dataCollection.env.wsTunnels.pusher.subscribe('systemAnnouncements', function(topic, data) {
                            BazAnnouncements.onMessage('systemAnnouncements', data);
                        });
                    }

                    if (tunnelsToInit.includes('progress')) {
                        dataCollection.env.wsTunnels.pusher.subscribe('progress', function(topic, data) {
                            BazProgress.onMessage(data);
                        });
                    }

                    timerId = BazHelpers.getTimerId('initPusherTunnel');
                    if (timerId) {
                        BazHelpers.setTimeoutTimers.stop(timerId, null, 'initPusherTunnel');
                        if (tunnelsToInit.includes('systemNotifications')) {
                            BazNotifications.serviceOnline();
                        }
                        if (tunnelsToInit.includes('messengerNotifications')) {
                            BazMessenger.serviceOnline();
                        }
                        if (tunnelsToInit.includes('systemAnnouncements')) {
                            BazAnnouncements.serviceOnline();
                        }
                        if (tunnelsToInit.includes('progress')) {
                            BazProgress.serviceOnline();
                        }
                    } else {
                        if (tunnelsToInit.includes('systemNotifications')) {
                            BazNotifications.init();
                        }
                        if (tunnelsToInit.includes('messengerNotifications')) {
                            BazMessenger.init();
                        }
                        if (tunnelsToInit.includes('systemAnnouncements')) {
                            BazAnnouncements.init();
                        }
                        if (tunnelsToInit.includes('progress')) {
                            BazProgress.init();
                        }
                    }
                },
                function() {
                    if (tunnelsToInit.includes('systemNotifications')) {
                        BazNotifications.serviceOffline();
                    }
                    if (tunnelsToInit.includes('messengerNotifications')) {
                        BazMessenger.serviceOffline();
                    }
                    if (tunnelsToInit.includes('systemAnnouncements')) {
                        BazAnnouncements.serviceOffline();
                    }
                    if (tunnelsToInit.includes('progress')) {
                        BazProgress.serviceOffline();
                    }
                    //eslint-disable-next-line
                    console.warn('WebSocket connection closed');

                    BazHelpers.setTimeoutTimers.add(function() {
                        initPusherTunnel(options);
                    }, 60000, null, 'initPusherTunnel');
                },
                {
                    'skipSubprotocolCheck': true
                }
            );
    }

    function bazTunnelsConstructor() {
        // if something needs to be constructed
        return null;
    }

    function setup(BazTunnelsConstructor) {
        BazTunnels = BazTunnelsConstructor;
        BazTunnels.defaults = {
            loadHeaderAt : null,
            loadFooterAt : null
        };
        BazTunnels.init = function() {
            init();
        }
        BazTunnels.initMessengerOTR = function(options) {
            initMessengerOTR(_extends(BazTunnels.defaults, options));
        }
        BazTunnels.initPusherTunnel = function(options) {
            initPusherTunnel(_extends(BazTunnels.defaults, options));
        }
    }

    setup(bazTunnelsConstructor);

    return bazTunnelsConstructor;
}();
/* exported BazNotifications */
/* globals BazHelpers */
/*
* @title                    : BazNotifications
* @description              : Baz Notifications Lib
* @developer                : guru@bazaari.com.au
* @usage                    : BazNotifications._function_(_options_);
* @functions                : BazNotifications
* @options                  :
*/

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
// var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

// eslint-disable-next-line no-unused-vars
var BazNotifications = function() {
    var BazNotifications = void 0;
    var dataCollection;
    var initialized = false;
    var pullNotifications = false;
    var appRoute;
    var promiseInit = false;
    // Error
    // function error(errorMsg) {
    //     throw new Error(errorMsg);
    // }

    //Init
    function init(initialConnection = true) {
        initialized = true;

        dataCollection = window.dataCollection;

        if (dataCollection.env.appRoute !== '') {
            appRoute = dataCollection.env.appRoute + '/'
        } else {
            appRoute = '';
        }

        if (initialConnection) {
            serviceOnline();
        }
    }

    function serviceOnline() {
        if (!initialized) {
            init(false);
        } else {
            getNotificationsCount();
        }

        //eslint-disable-next-line
        console.log('Notification service online');

        $('.socket-icon').removeClass(function (index, className) {
            return (className.match (/(^|\s)text-\S+/g) || []).join(' ');
        }).addClass('text-success');
        $('.socket').attr('title', 'Socket Status: Online').tooltip('_fixTitle');

        initPullNotifications(false);
    }

    function serviceOffline() {
        if (!initialized) {
            init(false);
        }

        //eslint-disable-next-line
        console.log('Notification service offline');

        $('.socket-icon').removeClass(function (index, className) {
            return (className.match (/(^|\s)text-\S+/g) || []).join(' ');
        }).addClass('text-secondary');
        $('.socket').attr('title', 'Socket Status: Offline').tooltip('_fixTitle');

        initPullNotifications(true);
    }

    //Notifications
    function getNotificationsCount(responseData = null) {
        if (responseData && Object.keys(responseData).length > 0) {
            processResponseData(responseData);
        } else {
            var url = dataCollection.env.rootPath + appRoute + 'system/notifications/fetchNewNotificationsCount';

            var postData = { };
            postData[$('#security-token').attr('name')] = $('#security-token').val();

            $.post(url, postData, function(response) {
                if (response.tokenKey && response.token) {
                    $('#security-token').attr('name', response.tokenKey);
                    $('#security-token').val(response.token);
                }

                if (response.responseCode == 0 && response.responseData) {
                    processResponseData(response.responseData);
                }
            }, 'json');
        }
    }

    function processResponseData(responseData) {
        if (!Number.isInteger(responseData.count.total)) {
            parseInt(responseData.count.total);
        }

        if (window.dataCollection.env.notifications.count) {
            if (responseData.count.total === window.dataCollection.env.notifications.count.total) {
                return;
            } else if (responseData.count.total < window.dataCollection.env.notifications.count.total) {
                window.dataCollection.env.notifications.count.total = responseData.count.total;
                updateCounter(responseData);
                return;
            }

            window.dataCollection.env.notifications.count.total = responseData.count.total;
            updateCounter(responseData);
        } else {
            window.dataCollection.env.notifications.count = responseData.count;
            updateCounter(responseData);
        }

        if (responseData.count.total > 0 && !responseData.mute) {
            window.dataCollection.env.sounds.notificationSound.play();
        }
    }

    function updateCounter(responseData) {
        var notificationCount = 0;

        if (responseData.count.error > 0) {
            notificationCount = responseData.count.error;
            $('#notifications-button-counter').removeClass(function (index, className) {
                return (className.match (/(^|\s)badge-\S+/g) || []).join(' ');
            }).addClass('badge-danger');
        } else if (responseData.count.warning > 0) {
            $('#notifications-button-counter').removeClass(function (index, className) {
                return (className.match (/(^|\s)badge-\S+/g) || []).join(' ');
            }).addClass('badge-warning');
            notificationCount = responseData.count.warning;
        } else {
            $('#notifications-button-counter').removeClass(function (index, className) {
                return (className.match (/(^|\s)badge-\S+/g) || []).join(' ');
            }).addClass('badge-info');
            notificationCount = responseData.count.total;
        }

        if (notificationCount === 0) {
            $('#notifications-button-counter').html('');
        } else if (notificationCount < 10) {
            $('#notifications-button-counter').css({'right': '10px'});
            $('#notifications-button-counter').html(notificationCount);
            shakeNotificationsButton();
        } else if (notificationCount < 99) {
            $('#notifications-button-counter').css({'right': '5px'});
            $('#notifications-button-counter').html(notificationCount);
            shakeNotificationsButton();
        } else if (notificationCount > 99) {
            $('#notifications-button-counter').css({'right': 0});
            $('#notifications-button-counter').html('99+');
            shakeNotificationsButton();
        }
    }

    function initPullNotifications(offline) {
        //eslint-disable-next-line
        console.log(promiseInit);
        if (offline && !promiseInit) {
            //eslint-disable-next-line
            console.log('promiseInit');
            BazHelpers.interval(
                async(iteration, stop) => {
                    //eslint-disable-next-line
                    console.log(BazNotifications.getPullNotifications());
                    if (!BazNotifications.getPullNotifications()) {
                        BazNotifications.setPromiseInit(false);
                        stop();
                    } else {
                        BazNotifications.getNotificationsCount();
                    }
                },
                10000
            );

            promiseInit = true;
        }

        pullNotifications = offline;
    }

    function getPullNotifications() {
        return pullNotifications;
    }

    function setPromiseInit(state) {
        promiseInit = state;
    }

    function shakeNotificationsButton() {
        $('#notifications-button').addClass('animated tada');

        setTimeout(function() {
            $('#notifications-button').removeClass('animated tada');
        }, 10000);
    }

    function onMessage(type, response) {
        //eslint-disable-next-line
        console.log(type, response);
        if (response.responseCode == 0) {
            if (response.responseData && response.responseData.count && response.responseData.mute !== 'undefined') {
                getNotificationsCount(response.responseData);
            } else {
                getNotificationsCount();
            }
        } else {
            getNotificationsCount();
        }

        if ($('#baz-content section').length > 0) {
            var section = $('#baz-content section')[0].id.match(/notifications-listing/g);
            if (section && section.length === 1) {
                var component = $('#baz-content .component')[0].id;
                window["dataCollection"][component][component + '-listing']['BazContentSectionWithListing']._filterRunAjax();
            }
        }
    }

    function bazNotificationsConstructor() {
        // if something needs to be constructed
        return null;
    }

    function setup(BazNotificationsConstructor) {
        BazNotifications = BazNotificationsConstructor;
        BazNotifications.defaults = { };
        BazNotifications.init = function(options) {
            init(_extends(BazNotifications.defaults, options));
        }
        BazNotifications.serviceOnline = function(options) {
            serviceOnline(_extends(BazNotifications.defaults, options));
        }
        BazNotifications.serviceOffline = function(options) {
            serviceOffline(_extends(BazNotifications.defaults, options));
        }
        BazNotifications.initPullNotifications = function(options) {
            initPullNotifications(_extends(BazNotifications.defaults, options));
        }
        BazNotifications.onMessage = function(type, options) {
            onMessage(type, _extends(BazNotifications.defaults, options));
        }
        BazNotifications.getNotificationsCount = function() {
            getNotificationsCount();
        }
        BazNotifications.getPullNotifications = function() {
            return getPullNotifications();
        }
        BazNotifications.setPromiseInit = function(options) {
            return setPromiseInit(options);
        }
    }

    setup(bazNotificationsConstructor);

    return bazNotificationsConstructor;
}();
/* exported BazMessenger */
/* globals PNotify EmojiPicker autoComplete dayjs Swal */
/*
* @title                    : BazMessenger
* @description              : Baz Messenger Lib
* @developer                : guru@bazaari.com.au
* @usage                    : BazMessenger._function_(_options_);
* @functions                : BazMessengerInit
* @options                  :
*/

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
// var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

// eslint-disable-next-line no-unused-vars
var BazMessenger = function() {
    var BazMessenger = void 0;
    var dataCollection;
    var messengerButonIconColor = $('#messenger-button-icon').data('iconcolor');
    var initialized = false;
    var appRoute;
    // Error
    // function error(errorMsg) {
    //     throw new Error(errorMsg);
    // }

    //Init
    function init(initialConnection = true) {
        initialized = true;

        dayjs.extend(window.dayjs_plugin_advancedFormat);

        dataCollection = window.dataCollection;

        if (dataCollection.env.appRoute !== '') {
            appRoute = dataCollection.env.appRoute + '/'
        } else {
            appRoute = '';
        }

        dataCollection.env.wsTunnels.messenger['emojiPicker'] = new EmojiPicker({
            emojiable_selector: '[data-emojiable=true]',
            assetsPath: '/core/default/images/emoji-picker/',
            popupButtonClasses: 'fa fa-fw fa-smile',
        });

        dataCollection.env.wsTunnels.messenger.search =
            new autoComplete({
                data: {
                    src: async() => {
                        const url = dataCollection.env.rootPath + appRoute + 'system/messenger/searchAccount';

                        var myHeaders = new Headers();
                        myHeaders.append("accept", "application/json");

                        var formdata = new FormData();
                        formdata.append("search", document.querySelector("#messenger-main-search").value);
                        formdata.append($('#security-token').attr('name'), $('#security-token').val());

                        var requestOptions = {
                            method: 'POST',
                            headers: myHeaders,
                            body: formdata
                        };

                        const responseData = await fetch(url, requestOptions);

                        const response = await responseData.json();

                        if (response.tokenKey && response.token) {
                            $('#security-token').attr('name', response.tokenKey);
                            $('#security-token').val(response.token);
                        }
                        //eslint-disable-next-line
                        console.log(response.responseData.accounts);
                        if (response.responseData.accounts) {
                            return response.responseData.accounts;
                        } else {
                            return [];
                        }
                    },
                    key: ["name"],
                    cache: false
                },
                selector: "#messenger-main-search",
                threshold : 3,
                debounce: 500,
                searchEngine: "strict",
                resultsList: {
                    render: true,
                    container: source => {
                        source.setAttribute("id", "messenger-main-search_list");
                        source.setAttribute("class", "autoComplete_results");
                    },
                    destination: "#messenger-main-search",
                    position: "afterend",
                    element: "div",
                    className: "autoComplete_results"
                },
                maxResults: 5,
                highlight: true,
                resultItem: {
                    content: (data, source) => {
                        //eslint-disable-next-line
                        console.log(data, source);
                        source.innerHTML = data.match;
                    },
                    element: "div"
                },
                noResults: () => {
                    const result = document.createElement("li");
                    result.setAttribute("class", "autoComplete_result text-danger");
                    result.setAttribute("tabindex", "1");
                    result.innerHTML = "No search results. Click field help for more information.";

                    if (document.querySelector("#messenger-main-search_list")) {
                        $("#messenger-main-search_list").empty().append(result);
                    } else {
                        $("#messenger-main-search").parent(".form-group").append(
                            '<div id="messenger-main-search_list" class="autoComplete_results"></div>'
                        );
                        document.querySelector("#messenger-main-search_list").appendChild(result);
                    }
                },
                onSelection: (feedback) => {
                    $('#messenger-main-search').val('');
                    $('#messenger-main-search').blur();
                    //eslint-disable-next-line
                    console.log(feedback.selection.value);
                    messengerWindow(feedback.selection.value);
                    addUserToMembersUsers(feedback.selection.value);
                }
            });

        if (initialConnection) {
            serviceOnline();
        }
    }

    function serviceOnline() {
        //eslint-disable-next-line
        console.log('serviceOnline');

        if (!initialized) {
            init(false);
        }

        $("#messenger-online").attr('hidden', false);
        $("#messenger-offline").attr('hidden', true);
        $('#messenger-offline-icon').attr('hidden', true);
        $('#messenger-button-icon').removeClass(function (index, className) {
            return (className.match (/(^|\s)text-\S+/g) || []).join(' ');
        }).addClass('text-' + messengerButonIconColor);
        initListeners();
        getUnreadMessagesCount();
    }

    function serviceOffline() {
        if (!initialized) {
            init(false);
        }
    }

    function initListeners() {
        $('#messenger-main-status').off();
        $('#messenger-main-status').change(function() {
            //eslint-disable-next-line
            console.log($(this).data('status'));
            var oldStatus = $(this).data('status')

            var status = $('#messenger-main-status option:selected').val();

            if (status == 0) {
                return;
            }
            if (status != 4) {
                $('#messenger-main').attr('hidden', false);

            } else {
                $('#messenger-main').attr('hidden', true);
                $('#messenger-windows').remove();
            }

            var url = dataCollection.env.rootPath + appRoute + 'system/messenger/changestatus';

            var postData = { };
            postData[$('#security-token').attr('name')] = $('#security-token').val();
            postData['status'] = status;

            $.post(url, postData, function(response) {
                if (response.tokenKey && response.token) {
                    $('#security-token').attr('name', response.tokenKey);
                    $('#security-token').val(response.token);
                }
                if (response.responseCode == 0) {
                    var statusTextColor;
                    if (status == 1) {
                        statusTextColor = 'success';
                    } else if (status == 2) {
                        statusTextColor = 'warning';
                    } else if (status == 3) {
                        statusTextColor = 'danger';
                    } else if (status == 4) {
                        statusTextColor = 'secondary';
                    }

                    $('#messenger-button-icon').removeClass(function (index, className) {
                        return (className.match (/(^|\s)text-\S+/g) || []).join(' ');
                    }).addClass('text-' + statusTextColor);
                    messengerButonIconColor = statusTextColor;
                    if (oldStatus == '4') {
                        getUnreadMessagesCount();
                    }
                } else {
                    PNotify.error({
                        text        : response.responseMessage,
                        textTrusted : true
                    });
                }
            }, 'json');
            $(this).data('status', status);
        });

        $('#messenger-users li').each(function(index, li) {
            $(li).off();
            $(li).click(function(e) {
                e.preventDefault();
                messengerWindow($(this).data());
            });
        });

        // On delete
        $('#messenger-main-search').off();
        $('#messenger-main-search').on('input propertychange', function() {
            if ($('#messenger-main-search').val().length === 0) {
                $('#messenger-main-search_list').children().remove();
            }
        });

        $('#messenger-main-mute').off();
        $('#messenger-main-mute').click(function(e) {
            e.preventDefault();

            var thisButton = this;
            var url = dataCollection.env.rootPath + appRoute + 'system/messenger/changesettings';

            var dataToSend = { };
            dataToSend[$('#security-token').attr('name')] = $('#security-token').val();
            dataToSend['changestate'] = 1;

            $.post(url, dataToSend, function(response) {
                if (response.tokenKey && response.token) {
                    $('#security-token').attr('name', response.tokenKey);
                    $('#security-token').val(response.token);
                }

                if (response.responseCode == 0) {
                    $(thisButton).attr('hidden', true);
                    $('#messenger-main-unmute').attr('hidden', false);
                    $('#messenger-button').children('i').removeClass('fa-comment').addClass('fa-comment-slash');
                } else {
                    PNotify.error({
                        title           : response.responseMessage,
                    });
                }
            }, 'json');
        });

        $('#messenger-main-unmute').off();
        $('#messenger-main-unmute').click(function(e) {
            e.preventDefault();

            var thisButton = this;
            var url = dataCollection.env.rootPath + appRoute + 'system/messenger/changesettings';

            var dataToSend = { };
            dataToSend[$('#security-token').attr('name')] = $('#security-token').val();
            dataToSend['changestate'] = 0;

            $.post(url, dataToSend, function(response) {
                if (response.tokenKey && response.token) {
                    $('#security-token').attr('name', response.tokenKey);
                    $('#security-token').val(response.token);
                }

                if (response.responseCode == 0) {
                    $(thisButton).attr('hidden', true);
                    $('#messenger-main-mute').attr('hidden', false);
                    $('#messenger-button').children('i').removeClass('fa-comment-slash').addClass('fa-comment');
                } else {
                    PNotify.error({
                        title           : response.responseMessage,
                    });
                }
            }, 'json');
        });
    }

    function messengerWindow(user) {
        $('.messenger-counter-' + $('#messenger-user-' + user.user).data('user')).html('');

        var totalCounter = 0;
        $('#messenger-users span.badge').each(function() {
            if ($(this).html() !== '') {
                totalCounter = totalCounter + parseInt($(this).html());
            }
        });

        if (totalCounter === 0) {
            $('#messenger-button-counter').html('');
        } else if (totalCounter < 10) {
            $('#messenger-button-counter').css({'right': '10px'});
            $('#messenger-button-counter').html(totalCounter);
        } else if (totalCounter < 99) {
            $('#messenger-button-counter').css({'right': '5px'});
            $('#messenger-button-counter').html(totalCounter);
        } else if (totalCounter > 99) {
            $('#messenger-button-counter').css({'right': 0});
            $('#messenger-button-counter').html('99+');
        }

        $('#messenger-button').ControlSidebar('toggle');

        if ($('#messenger-windows #messenger-window-' + user.user).length > 0) {
            $('.messenger-input-' + user.user).focus();
            return;
        }

        var currentMessengerWindows = $('.messenger-window').length;

        var fromLeft = 5;
        var fromBottom = -12;

        if (currentMessengerWindows === 0) {
            fromLeft = 5;
            $('.main-footer').append('<div id="messenger-windows"></div>');
        } else if (currentMessengerWindows === 1) {
            fromLeft = 10 + (currentMessengerWindows * 473);
        } else if (currentMessengerWindows > 1 && currentMessengerWindows < 3) {
            fromLeft = (5 * currentMessengerWindows) + (currentMessengerWindows * 473) + 5;
        } else {
            PNotify.error({text: "Only 3 chat windows can be opened at a given time. Please close other chat windows to allow this window to open."});
            return;
        }

        var cardHeader = 'secondary';
        if (user.status == 1) {
            cardHeader = 'success';
        } else if (user.status == 2) {
            cardHeader = 'warning';
        } else if (user.status == 3) {
            cardHeader = 'danger';
        }

        $('#messenger-windows').append(
            '<div id="messenger-window-' + user.user + '" class="messenger-window" style="position: fixed;right: ' + fromLeft + 'px;bottom: ' + fromBottom + 'px;">' +
                '<div id="messenger-card-' + user.user + '" data-user="' + user.user + '" class="card card-' + cardHeader + ' rounded-0 direct-chat direct-chat-info">' +
                    '<div class="card-header rounded-0" style="min-width: 473px;">' +
                        '<h3 class="card-title text-truncate">' + user.name + '</h3>' +
                        '<div class="card-tools">' +
                            '<span class="badge badge-light mr-2 messenger-counter-' + user.user + '"></span>' +
                            '<button type="button" class="btn btn-tool" data-card-widget="collapse" data-animationspeed="0">' +
                                '<i class="fas fa-fw fa-minus"></i>' +
                            '</button>' +
                            '<button type="button" class="btn btn-tool" data-card-widget="remove" data-animationspeed="0">' +
                                '<i class="fas fa-fw fa-times"></i>'+
                            '</button>' +
                        '</div>' +
                    '</div>' +
                    '<div class="card-body rounded-0" style="width: 473px">' +
                        '<div id="direct-chat-messages-' + user.user + '" class="direct-chat-messages">' +
                            '<div class="row m-2" id="messenger-loader-' + user.user + '">' +
                            '    <div class="col">' +
                            '        <div class="fa-2x">' +
                            '            <i class="fa fa-cog fa-spin"></i> Loading Messages...' +
                            '        </div>' +
                            '    </div>' +
                            '</div>' +
                            '<div class="row text-center text-info" id="messenger-no-messages-' + user.user + '" hidden>' +
                            '    <div class="col">' +
                            '       <i class="fas fa-fw fa-exclamation-circle"></i> No Messages. Send your first message...' +
                            '    </div>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                    '<div class="card-footer rounded-0">' +
                        '<div class="input-group emoji-picker-container">' +
                            '<textarea data-emojiable="true" data-emoji-input="unicode" type="text" autocomplete="off" rows="1" style="resize: none;" name="message" placeholder="Type Message ..." class="form-control messenger-input-' + user.user + '"></textarea>' +
                            '<span class="input-group-append">' +
                                '<button data-action="add" data-msgid="" type="button" class="btn btn-primary messenger-send-' + user.user + '">' +
                                    '<i class="fab fa-fw fa-telegram-plane"></i>'+
                                '</button>' +
                            '</span>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>'
        );

        if (window.dataCollection.env.wsTunnels.messenger.emojiPicker) {
            window.dataCollection.env.wsTunnels.messenger.emojiPicker.discover();
        }

        $("#messenger-card-" + user.user).on('removed.lte.cardwidget', function(e) {
            removeCard(e);
        });

        function removeCard(e) {
            var numberOfWindows = $('.messenger-window').length;

            if (numberOfWindows > 1) {
                // var collapsedCards = $('.messenger-window .collapsed-card').length;
                var windowObj = { };
                $('.messenger-window').each(function(index, windowId) {
                    windowObj[windowId.id] = index + 1;
                });
                var windowPosition = windowObj[$(e.currentTarget).parent()[0].id];

                for (var leftWindow in windowObj) {
                    if (windowObj[leftWindow] > windowPosition) {
                        var rightVal = parseInt($('#' + leftWindow).css('right'));
                        //eslint-disable-next-line
                        console.log(rightVal);
                        // if (collapsedCards > 0) {
                        //     if ($('#' + leftWindow + ' .card-collapsed')) {
                        //         $('#' + leftWindow).css('right', rightVal - 275);
                        //     } else {
                        //         var newValue = collapsedCards * 275;
                        //         $('#' + leftWindow).css('right', rightVal - newValue);
                        //     }
                        // } else {
                            $('#' + leftWindow).css('right', rightVal - 478);
                        // }
                    }
                }
                $(e.currentTarget).parent().remove();
            } else if (numberOfWindows === 1) {
                $(e.currentTarget).parents('#messenger-windows').remove();
            } else {
                $(e.currentTarget).parent().remove();
            }
        }

        $('.messenger-input-' + user.user).keypress(function(e) {
            if (e.keyCode === 13 && !e.shiftKey) {
                e.preventDefault();

                if ($('div.messenger-input-' + user.user).html() !== '') {
                    var message = $('div.messenger-input-' + user.user).html();

                    $('div.messenger-input-' + user.user).html('');

                    sendMessage(user, message);
                }
            }
        });

        $('.messenger-send-' + user.user).click(function(e) {
            e.preventDefault();

            if ($('div.messenger-input-' + user.user).html() !== '') {
                var message = $('div.messenger-input-' + user.user).html();

                $('div.messenger-input-' + user.user).html('');

                sendMessage(user, message);
            }
        });

        //Get Messages from Server for user.user
        var url = dataCollection.env.rootPath + appRoute + 'system/messenger/getmessages';

        var postData = { };
        postData[$('#security-token').attr('name')] = $('#security-token').val();
        postData['user'] = user.user;

        $.post(url, postData, function(response) {
            if (response.tokenKey && response.token) {
                $('#security-token').attr('name', response.tokenKey);
                $('#security-token').val(response.token);
            }
            if (response.responseCode == 0) {
                $('#messenger-loader-' + user.user).attr('hidden', true);
                populateMessages(user, response.responseData.messages, response.responseData.paginationCounters);

                markAllMessagesRead(user);
            } else {
                PNotify.error({
                    text        : response.responseMessage,
                    textTrusted : true
                });
            }
        }, 'json');

        $('.messenger-input-' + user.user).focus();
    }

    function populateMessages(toUser, messages, paginationCounters, update = false) {
        if (messages.length === 0) {
            $('#messenger-no-messages-' + toUser.user).attr('hidden', false);
            return;
        }
        //eslint-disable-next-line
        console.log(toUser, messages, paginationCounters, update);

        var messagesHtml = '';

        if (paginationCounters) {
            $('#direct-chat-messages-' + toUser.user).data('current', paginationCounters.current);
            $('#direct-chat-messages-' + toUser.user).data('filtered_items', paginationCounters.filtered_items);
            $('#direct-chat-messages-' + toUser.user).data('first', paginationCounters.first);
            $('#direct-chat-messages-' + toUser.user).data('last', paginationCounters.last);
            $('#direct-chat-messages-' + toUser.user).data('limit', paginationCounters.limit);
            $('#direct-chat-messages-' + toUser.user).data('next', paginationCounters.next);
            $('#direct-chat-messages-' + toUser.user).data('previous', paginationCounters.previous);
            $('#direct-chat-messages-' + toUser.user).data('total_items', paginationCounters.total_items);

            if (paginationCounters.current < paginationCounters.last) {
                messagesHtml +=
                    '<div class="row pb-2">' +
                    '   <div class="col text-center">' +
                    '       <button class="btn btn-xs btn-primary" id="messenger-messages-load-more-' + toUser.user + '" ' +
                    '           data-toggle="tooltip" data-html="true" data-placement="auto" title="" role="button" data-original-title="">' +
                    '           <i class="fas fa-fw fa-download"></i> Load More...' +
                    '       </button>' +
                    '   </div>' +
                    '</div>';
            }
        }

        if (!toUser.portrait || (toUser.portrait && toUser.portrait === '')) {
            toUser.portrait = dataCollection.env.rootPath + '/core/default/images/general/user.png';
        } else {
            toUser.portrait =
                '/' + appRoute + 'system/storages/q/uuid/' + toUser.portrait + '/w/80';
        }
        $(messages).each(function(index, message) {
            if (message.to_account_id == toUser.user) {
                messagesHtml +=
                    '<div id="messenger-message-' + message.id + '" data-messageid="' + message.id + '" class="direct-chat-msg right">' +
                    '    <div class="direct-chat-infos clearfix">' +
                    '        <span class="direct-chat-name float-right"></span>' +
                    '        <span class="direct-chat-timestamp float-left">' +
                    '           <span>' + dayjs(message.updated_at, 'YYYY-MM-DD HH:mm:ss').format('MMMM Do YYYY, h:mm:ss a') + '</span>' +
                    '           <a href="#" class="messenger-message-tools messenger-message-edit" hidden>' +
                    '               <i class="fas fa-fw fa-edit text-warning"></i>' +
                    '           </a>' +
                    '           <a href="#" class="messenger-message-tools messenger-message-edit-cancel" hidden>' +
                    '               <i class="fas fa-fw fa-times-circle text-danger"></i>' +
                    '           </a>' +
                    '           <a href="#" class="messenger-message-tools messenger-message-remove" hidden>' +
                    '               <i class="fas fa-fw fa-trash text-danger"></i>' +
                    '           </a>' +
                    '        </span>' +
                    '    </div>' +
                    '    <img class="direct-chat-img" src="' + window.dataCollection.env.profile.portrait + '" alt="message user image">' +
                    '    <div class="direct-chat-text">' + message.message + '</div>' +
                    '</div>';
            } else {
                messagesHtml +=
                    '<div id="messenger-message-' + message.id + '" data-messageid="' + message.id + '" class="direct-chat-msg">' +
                    '    <div class="direct-chat-infos clearfix">' +
                    '        <span class="direct-chat-name float-left">' + toUser.name + '</span>' +
                    '        <span class="direct-chat-timestamp float-right">' +
                    '           <span>' + dayjs(message.updated_at, 'YYYY-MM-DD HH:mm:ss').format('MMMM Do YYYY, h:mm:ss a') + '</span>' +
                    '        </span>' +
                    '    </div>' +
                    '    <img class="direct-chat-img" src="' + toUser.portrait + '" alt="message user image">' +
                    '    <div class="direct-chat-text">' + message.message + '</div>' +
                    '</div>';
            }
        });

        if (update) {
            $('#direct-chat-messages-' + toUser.user).prepend(messagesHtml);
        } else {
            $('#direct-chat-messages-' + toUser.user).append(messagesHtml);
            $('#direct-chat-messages-' + toUser.user).scrollTop($('#direct-chat-messages-' + toUser.user).get(0).scrollHeight);
        }
        $(messages).each(function(index, message) {
            if (message.removed != 1) {
                initMessagesListeners(message.id, toUser);
                messageHover(message.id, toUser);
            }
        });

        if ($('#messenger-messages-load-more-' + toUser.user).length > 0) {
            $('#messenger-messages-load-more-' + toUser.user).off();
            $('#messenger-messages-load-more-' + toUser.user).click(function(e) {
                e.preventDefault();

                var url = dataCollection.env.rootPath + appRoute + 'system/messenger/getmessages';

                var postData = { };
                postData[$('#security-token').attr('name')] = $('#security-token').val();
                postData['user'] = toUser.user;
                postData['page'] = paginationCounters.current + 1;

                $.post(url, postData, function(response) {
                    if (response.tokenKey && response.token) {
                        $('#security-token').attr('name', response.tokenKey);
                        $('#security-token').val(response.token);
                    }
                    if (response.responseCode == 0) {
                        $('#messenger-loader-' + toUser.user).attr('hidden', true);
                        $('#messenger-messages-load-more-' + toUser.user).closest('div.row').remove();
                        populateMessages(toUser, response.responseData.messages, response.responseData.paginationCounters, true);
                    } else {
                        PNotify.error({
                            text        : response.responseMessage,
                            textTrusted : true
                        });
                    }
                }, 'json');
            });
        }
    }

    function addUserToMembersUsers(user) {
        var color;
        if (user.status != 4) {
            if (user.status == 1) {
                color = 'success';
            } else if (user.status == 2) {
                color = 'warning';
            } else if (user.status == 3) {
                color = 'danger';
            }
        } else if (user.status == 4) {
            color = 'secondary';
        }

        if (!user.portrait || (user.portrait && user.portrait === '')) {
            user.portrait = dataCollection.env.rootPath + '/core/default/images/general/user.png';
        } else {
            user.portrait =
                '/' + appRoute + 'system/storages/q/uuid/' + user.portrait + '/w/80';
        }

        var newUser =
            '<li class="nav-item" id="messenger-user-' + user.id + '" data-status="' + user.status + '" data-type="user" data-user="' +
                user.id + '" data-name="' + user.name + '" data-portrait="' + user.portrait + '">' +
                '<a id="messenger-user-' + user.id + '-link" class="nav-link" href="#">' +
                    '<img id="messenger-user-' + user.id + '-img" src="' + user.portrait + '" class="rounded-sm" style="position:relative;top: -3px; width:20px;" alt="User Image">' +
                    '<i id="messenger-user-' + user.id + '-icon" class="fa fa-fw fa-circle text-' + color + '" style="font-size: 8px;position: absolute;top: 8px;left: 27px;"></i>' +
                    '<div id="messenger-user-' + user.id + '-name" class="text-uppercase ml-2 text-truncate" style="position:relative; top: 3px;display: inline-block;width: 150px;">' + user.name + '</div>' +
                    '<span class="badge badge-info messenger-counter-' + user.id + '" style="position: relative;top: -4px;"></span>' +
                '</a>' +
            '</li>';
        if (user.status == 4) {
            $('#messenger-offline-users').append(newUser);
        } else {
            $('#messenger-online-users').append(newUser);
        }

        initListeners();

        var url = dataCollection.env.rootPath + appRoute + 'system/messenger/addusertomembersusers';

        var postData = { };
        postData[$('#security-token').attr('name')] = $('#security-token').val();
        postData['user'] = user;

        $.post(url, postData, function(response) {
            if (response.tokenKey && response.token) {
                $('#security-token').attr('name', response.tokenKey);
                $('#security-token').val(response.token);
            }
            if (response.responseCode == 0) {
                //
            } else {
                PNotify.error({
                    text        : response.responseMessage,
                    textTrusted : true
                });
            }
        }, 'json');
    }

    function sendMessage(user, message) {
        // OTR (Off The Record) - This will be initiated by a user and a request is sent to the other user/users in case of group chat.
        // Once all users accept the OTR request, OTR is enabled and when messages are sent, they bypass the DB.
        // if (dataCollection.env.wsTunnels.messenger.otr && dataCollection.env.wsTunnels.messenger.otr === true) {
        //     dataCollection.env.wsTunnels.messenger.send(user, message);

        //     return;
        // }

        var action = $('.messenger-send-' + user.user).data('action');
        var url;

        var postData = { };
        postData[$('#security-token').attr('name')] = $('#security-token').val();
        postData['user'] = user.user;
        postData['message'] = message;

        if (action === 'add') {
            url = dataCollection.env.rootPath + appRoute + 'system/messenger/add';
        } else if (action === 'update') {
            postData['id'] = $('.messenger-send-' + user.user).data('msgid');
            url = dataCollection.env.rootPath + appRoute + 'system/messenger/update';
        }

        $.post(url, postData, function(response) {
            if (response.tokenKey && response.token) {
                $('#security-token').attr('name', response.tokenKey);
                $('#security-token').val(response.token);
            }
            if (response.responseCode == 0) {
                $('#messenger-loader-' + user.user).attr('hidden', true);

                if (action === 'add') {
                    $('#direct-chat-messages-' + user.user).append(
                        '<div id="messenger-message-' + response.responseData.id + '" data-messageid="' + response.responseData.id + '" class="direct-chat-msg right">' +
                        '    <div class="direct-chat-infos clearfix">' +
                        '        <span class="direct-chat-name float-right"></span>' +
                        '        <span class="direct-chat-timestamp float-left">' +
                        '           <span>' + dayjs().format('MMMM Do YYYY, h:mm:ss a') + '</span>' +
                        '           <a href="#" class="messenger-message-tools messenger-message-edit" hidden>' +
                        '               <i class="fas fa-fw fa-edit text-warning"></i>' +
                        '           </a>' +
                        '           <a href="#" class="messenger-message-tools messenger-message-edit-cancel" hidden>' +
                        '               <i class="fas fa-fw fa-times-circle text-danger"></i>' +
                        '           </a>' +
                        '           <a href="#" class="messenger-message-tools messenger-message-remove" hidden>' +
                        '               <i class="fas fa-fw fa-trash text-danger"></i>' +
                        '           </a>' +
                        '        </span>' +
                        '    </div>' +
                        '    <img class="direct-chat-img" src="' + window.dataCollection.env.profile.portrait + '" alt="message user image">' +
                        '    <div class="direct-chat-text">' + message + '</div>' +
                        '</div>'
                    );

                    initMessagesListeners(response.responseData.id, user);
                    messageHover(response.responseData.id);
                } else if (action === 'update') {
                    $('#messenger-message-' + response.responseData.id + ' .direct-chat-timestamp span').html(dayjs().format('MMMM Do YYYY, h:mm:ss a') + ' (Edited)');
                    $('#messenger-message-' + response.responseData.id + ' .direct-chat-text').html(message);
                    $('#messenger-message-' + response.responseData.id + ' .messenger-message-edit-cancel').attr('hidden', true);
                    $('#messenger-message-' + response.responseData.id + ' .messenger-message-edit').attr('hidden', true);
                    $('#messenger-message-' + response.responseData.id + ' .messenger-message-remove').attr('hidden', true);
                    messageHover(response.responseData.id);

                    $('.messenger-send-' + user.user).removeClass('btn-warning').addClass('btn-primary');
                    $('.messenger-send-' + user.user).data('action', 'add');
                    $('.messenger-send-' + user.user).data('msgid', '');
                }
                $('#messenger-no-messages-' + user.user).attr('hidden', true);
                $('#direct-chat-messages-' + user.user).scrollTop($('#direct-chat-messages-' + user.user).get(0).scrollHeight);
            } else {
                PNotify.error({
                    text        : response.responseMessage,
                    textTrusted : true
                });
            }
        }, 'json');
    }

    function initMessagesListeners(id, user) {
        $('#messenger-message-' + id + ' .messenger-message-edit').off();
        $('#messenger-message-' + id + ' .messenger-message-edit').click(function(e) {
            e.preventDefault();

            $('div.messenger-input-' + user.user)
                .html($(this).parents('.direct-chat-infos').siblings('.direct-chat-text').html());

            $(this).attr('hidden', true);
            $('#messenger-message-' + id + ' .messenger-message-edit-cancel').attr('hidden', false);
            $('#messenger-message-' + id + ' .messenger-message-edit-cancel').off();
            $('#messenger-message-' + id + ' .messenger-message-edit-cancel').click(function(e) {
                e.preventDefault();

                $(this).attr('hidden', true);
                $('#messenger-message-' + id + ' .messenger-message-edit').attr('hidden', false);
                messageHover(id);

                $('div.messenger-input-' + user.user).empty();

                $('.messenger-send-' + user.user).removeClass('btn-warning').addClass('btn-primary');
                $('.messenger-send-' + user.user).data('action', 'add');
                $('.messenger-send-' + user.user).data('msgid', '');
            });

            $('.messenger-send-' + user.user).removeClass('btn-primary').addClass('btn-warning');
            $('.messenger-send-' + user.user).data('action', 'update');
            $('.messenger-send-' + user.user).data('msgid', id);
        });

        $('#messenger-message-' + id + ' .messenger-message-remove').off();
        $('#messenger-message-' + id + ' .messenger-message-remove').click(function(e) {
            e.preventDefault();

            Swal.fire({
                title                       : '<span class="text-danger"> Delete selected message?</span>',
                icon                        : 'question',
                background                  : 'rgba(0,0,0,.8)',
                backdrop                    : 'rgba(0,0,0,.6)',
                buttonsStyling              : false,
                confirmButtonText           : 'Delete',
                customClass                 : {
                    'confirmButton'             : 'btn btn-danger text-uppercase',
                    'cancelButton'              : 'ml-2 btn btn-secondary text-uppercase',
                },
                showCancelButton            : true,
                keydownListenerCapture      : true,
                allowOutsideClick           : false,
                allowEscapeKey              : false,
                didOpen                      : function() {
                    window.dataCollection.env.sounds.swalSound.play();
                }
            }).then((result) => {
                if (result.value) {
                    var url = dataCollection.env.rootPath + appRoute + 'system/messenger/remove';

                    var postData = { };
                    postData[$('#security-token').attr('name')] = $('#security-token').val();
                    postData['id'] = id;

                    $.post(url, postData, function(response) {
                        if (response.tokenKey && response.token) {
                            $('#security-token').attr('name', response.tokenKey);
                            $('#security-token').val(response.token);
                        }
                        if (response.responseCode == 0) {
                            $('#messenger-message-' + postData['id'] + ' .direct-chat-timestamp span').html(dayjs().format('MMMM Do YYYY, h:mm:ss a'));
                            $('#messenger-message-' + postData['id'] + ' .direct-chat-text').html('Message Removed');
                            $('#messenger-message-' + postData['id'] + ' .messenger-message-edit-cancel').attr('hidden', true);
                            $('#messenger-message-' + postData['id'] + ' .messenger-message-edit').attr('hidden', true);
                            $('#messenger-message-' + postData['id'] + ' .messenger-message-remove').attr('hidden', true);
                            $('#messenger-message-' + postData['id']).off();
                        } else {
                            PNotify.error({
                                text        : response.responseMessage,
                                textTrusted : true
                            });
                        }
                    }, 'json');
                }
            });
        });
    }

    function messageHover(id) {
        $('#messenger-message-' + id).hover(
            function() {
                $('#messenger-message-' + id + ' .messenger-message-edit').attr('hidden', false);
                $('#messenger-message-' + id + ' .messenger-message-remove').attr('hidden', false);
            },
            function() {
                $('#messenger-message-' + id + ' .messenger-message-edit').attr('hidden', true);
                $('#messenger-message-' + id + ' .messenger-message-remove').attr('hidden', true);
            }
        );
    }

    function onMessage(message) {
        //eslint-disable-next-line
        console.log(message);
        if (message.responseCode === 0) {
            if (message.responseData.type === 'statusChange') {
                userStatusChange(message.responseData.data);
            }
            if (message.responseData.type === 'newMessage') {
                userNewMessage(message.responseData.data);
            }
        }
    }

    function userStatusChange(data) {
        var color, text;

        $('#messenger-users li').each(function(index, li) {
            if ($(li).data('type') === 'user' && $(li).data('user') == data.id) {
                var parentUl = $(li).parent('ul')[0].id;

                if (data.status != 4) {
                    if (data.status == 1) {
                        color = 'success';
                        text = 'Active';
                    } else if (data.status == 2) {
                        color = 'warning';
                        text = 'Away';
                    } else if (data.status == 3) {
                        color = 'danger';
                        text = 'Busy';
                    }
                    if (parentUl === 'messenger-offline-users') {
                        $('#messenger-online-users').append($(li));
                    }
                } else if (data.status == 4) {
                    color = 'secondary';
                    text = 'Offline';
                    if (parentUl === 'messenger-online-users') {
                        $('#messenger-offline-users').append($(li));
                    }
                }
                $(li).data('status', data.status);
                $(li).attr('data-status', data.status);
                $('#messenger-user-' + data.id + '-icon').removeClass(function (index, className) {
                    return (className.match (/(^|\s)text-\S+/g) || []).join(' ');
                }).addClass('text-' + color);
            }
        });

        if ($('#messenger-card-' + data.id).length > 0) {
            $('#messenger-card-' + data.id).removeClass(function (index, className) {
                return (className.match (/(^|\s)card-\S+/g) || []).join(' ');
            }).addClass('card-' + color);

            $('#messenger-loader-' + data.id).attr('hidden', true);
            $('#direct-chat-messages-' + data.id).append(
                '<div class="direct-chat-infos clearfix">' +
                '   <span class="direct-chat-name float-right">' + text + '</span>' +
                '   <span class="direct-chat-timestamp float-left">' + dayjs().format('MMMM Do YYYY, h:mm:ss a') + '</span>' +
                '</div>'
            );
            $('#direct-chat-messages-' + data.id).scrollTop($('#direct-chat-messages-' + data.id).get(0).scrollHeight);
        }
    }

    function userNewMessage(data) {
        if (!data.user.portrait || (data.user.portrait && data.user.portrait === '')) {
            data.user.portrait = dataCollection.env.rootPath + '/core/default/images/general/user.png';
        } else {
            data.user.portrait =
                '/' + appRoute + 'system/storages/q/uuid/' + data.user.portrait + '/w/80';
        }

        if ($('#direct-chat-messages-' + data.user.id).length > 0) {
            $('#messenger-loader-' + data.user.id).attr('hidden', true);
            $('#direct-chat-messages-' + data.user.id).append(
                '<div id="messenger-message-' + data.message.id + '" data-messageid="' + data.message.id + '" class="direct-chat-msg">' +
                '    <div class="direct-chat-infos clearfix">' +
                '        <span class="direct-chat-name float-left">' + $('#messenger-user-' + data.user.id).data('name') + '</span>' +
                '        <span class="direct-chat-timestamp float-right">' +
                '           <span>' + dayjs().format('MMMM Do YYYY, h:mm:ss a') + '</span>' +
                '        </span>' +
                '    </div>' +
                '    <img class="direct-chat-img" src="' + data.user.portrait + '" alt="message user image">' +
                '    <div class="direct-chat-text">' + data.message.message + '</div>' +
                '</div>'
            );
            $('#direct-chat-messages-' + data.user.id).scrollTop($('#direct-chat-messages-' + data.user.id).get(0).scrollHeight);
        } else {
            var currentCount;

            currentCount = $('#messenger-button-counter').html();

            if (currentCount === '99+') {
                //shake bell
                return;
            }
            if (currentCount === '') {
                currentCount = 0;
            } else {
                currentCount = parseInt(currentCount);
            }
            currentCount = currentCount + 1;

            if (currentCount < 10) {
                $('#messenger-button-counter').css({'right': '10px'});
                $('#messenger-button-counter').html(currentCount);
                shakeMessengerButton();
            } else if (currentCount < 99) {
                $('#messenger-button-counter').css({'right': '5px'});
                $('#messenger-button-counter').html(currentCount);
                shakeMessengerButton();
            } else if (currentCount > 99) {
                $('#messenger-button-counter').css({'right': 0});
                $('#messenger-button-counter').html('99+');
                shakeMessengerButton();
            }

            if ($('#messenger-user-' + data.user.id).length === 0) {
                addUserToMembersUsers(data.user);
            }

            var userCounter;

            userCounter = $('.messenger-counter-' + data.user.id).html();

            if (userCounter === '99+') {
                //shake bell
                return;
            }
            if (userCounter === '') {
                userCounter = 0;
            } else {
                userCounter = parseInt(userCounter);
            }

            userCounter = userCounter + 1;

            if (userCounter < 99) {
                $('.messenger-counter-' + data.user.id).html(userCounter);
                shakeMessengerButton();
            } else if (userCounter > 99) {
                $('.messenger-counter-' + data.user.id).html('99+');
                shakeMessengerButton();
            }
            //update counters
            //Ring Bell
            //Shake
        }
    }

    function shakeMessengerButton() {
        if ($('#messenger-button-icon').is('.fa-comment')) {
            window.dataCollection.env.sounds.messengerSound.play();
        }

        $('#messenger-button').addClass('animated tada');

        setTimeout(function() {
            $('#messenger-button').removeClass('animated tada');
        }, 10000);
    }

    //Get Unread Messages Count
    function getUnreadMessagesCount() {
        var url = dataCollection.env.rootPath + appRoute + 'system/messenger/getUnreadMessagesCount';

        var postData = { };
        postData[$('#security-token').attr('name')] = $('#security-token').val();

        $.post(url, postData, function(response) {
            if (response.tokenKey && response.token) {
                $('#security-token').attr('name', response.tokenKey);
                $('#security-token').val(response.token);
            }
            if (response.responseCode == 0) {
                if (response.responseData && response.responseData.total) {
                    if (response.responseData.total < 10) {
                        $('#messenger-button-counter').css({'right': '10px'});
                        $('#messenger-button-counter').html(response.responseData.total);
                        shakeMessengerButton();
                    } else if (response.responseData.total < 99) {
                        $('#messenger-button-counter').css({'right': '5px'});
                        $('#messenger-button-counter').html(response.responseData.total);
                        shakeMessengerButton();
                    } else if (response.responseData.total > 99) {
                        $('#messenger-button-counter').css({'right': 0});
                        $('#messenger-button-counter').html('99+');
                        shakeMessengerButton();
                    }
                }

                if (response.responseData && response.responseData.unread_count) {
                    for (var user in response.responseData.unread_count) {
                        if (response.responseData.unread_count[user].count > 0) {
                            if (response.responseData.unread_count[user].count < 99) {
                                $('.messenger-counter-' + response.responseData.unread_count[user].id).html(response.responseData.unread_count[user].count);
                                shakeMessengerButton();
                            } else if (response.responseData.unread_count[user].count > 99) {
                                $('.messenger-counter-' + response.responseData.unread_count[user].id).html('99+');
                                shakeMessengerButton();
                            }
                        }
                    }
                }
            } else {
                PNotify.error({
                    text        : response.responseMessage,
                    textTrusted : true
                });
            }
        }, 'json');
    }

    function markAllMessagesRead(user) {
        var url = dataCollection.env.rootPath + appRoute + 'system/messenger/markAllMessagesRead';

        var postData = { };
        postData[$('#security-token').attr('name')] = $('#security-token').val();
        postData['user'] = user.user;

        $.post(url, postData, function(response) {
            if (response.tokenKey && response.token) {
                $('#security-token').attr('name', response.tokenKey);
                $('#security-token').val(response.token);
            }
            if (response.responseCode == 0) {
                //
            } else {
                PNotify.error({
                    text        : response.responseMessage,
                    textTrusted : true
                });
            }
        }, 'json');
    }

    function bazMessengerConstructor() {
        // if something needs to be constructed
        return null;
    }

    function otrServiceOnline() {
        //
    }

    function otrServiceOffline() {
        //
    }

    function setup(BazMessengerConstructor) {
        BazMessenger = BazMessengerConstructor;
        BazMessenger.defaults = { };
        BazMessenger.init = function(options) {
            init(_extends(BazMessenger.defaults, options));
        }
        BazMessenger.serviceOnline = function(options) {
            serviceOnline(_extends(BazMessenger.defaults, options));
        }
        BazMessenger.serviceOffline = function(options) {
            serviceOffline(_extends(BazMessenger.defaults, options));
        }
        BazMessenger.onMessage = function(options) {
            onMessage(_extends(BazMessenger.defaults, options));
        }
        BazMessenger.getUnreadMessagesCount = function(options) {
            getUnreadMessagesCount(_extends(BazMessenger.defaults, options));
        }
        BazMessenger.otrServiceOnline = function(options) {
            otrServiceOnline(_extends(BazMessenger.defaults, options));
        }
        BazMessenger.otrServiceOffline = function(options) {
            otrServiceOffline(_extends(BazMessenger.defaults, options));
        }
    }

    setup(bazMessengerConstructor);

    return bazMessengerConstructor;
}();
/* exported BazAnnouncements */
/* globals */
/*
* @title                    : BazAnnouncements
* @description              : Baz Announcements Lib
* @developer                : guru@bazaari.com.au
* @usage                    : BazAnnouncements._function_(_options_);
* @functions                : BazAnnouncements
* @options                  :
*/

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
// var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

// eslint-disable-next-line no-unused-vars
var BazAnnouncements = function() {
    var BazAnnouncements = void 0;
    var dataCollection;
    var initialized = false;
    // Error
    // function error(errorMsg) {
    //     throw new Error(errorMsg);
    // }

    //Init
    function init(initialConnection = true) {
        initialized = true;

        dataCollection = window.dataCollection;
        dataCollection.env.messenger = { };

        if (initialConnection) {
            serviceOnline();
        }
    }

    function serviceOnline() {
        if (!initialized) {
            init(false);
        }
    }

    function serviceOffline() {
        if (!initialized) {
            init(false);
        }

        initPullAnnouncements();
    }

    function initPullAnnouncements() {
        // $(document).ready(function() {
            // getAnnouncementsCount();
        // });

        // BazHelpers.interval(
        //     async(iteration, stop) => {

        //         if (stoppedPullAnnouncements) {
        //             stop();
        //         }

        //         BazTunnels.getAnnouncementsCount();
        //     },
        //     10000
        // );
    }

    function shakeBell() {
        $('#notifications-button').addClass('animated tada');

        setTimeout(function() {
            $('#notifications-button').removeClass('animated tada');
        }, 10000);
    }

    function onMessage(data) {
        //eslint-disable-next-line
        console.log(data);

        shakeBell();//This will be popup
    }

    function bazAnnouncementsConstructor() {
        // if something needs to be constructed
        return null;
    }

    function setup(BazAnnouncementsConstructor) {
        BazAnnouncements = BazAnnouncementsConstructor;
        BazAnnouncements.defaults = { };
        BazAnnouncements.init = function(options) {
            init(_extends(BazAnnouncements.defaults, options));
        }
        BazAnnouncements.serviceOnline = function(options) {
            serviceOnline(_extends(BazAnnouncements.defaults, options));
        }
        BazAnnouncements.serviceOffline = function(options) {
            serviceOffline(_extends(BazAnnouncements.defaults, options));
        }
        BazAnnouncements.onMessage = function(options) {
            onMessage(_extends(BazAnnouncements.defaults, options));
        }
    }

    setup(bazAnnouncementsConstructor);

    return bazAnnouncementsConstructor;
}();
/* exported BazProgress */
/* globals BazHelpers */
/*
* @title                    : BazProgress
* @description              : Baz Progress Lib
* @developer                : guru@bazaari.com.au
* @usage                    : BazProgress._function_(_options_);
* @functions                : BazProgress
* @options                  :
*/

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
// var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

// eslint-disable-next-line no-unused-vars
var BazProgress = function() {
    var BazProgress = void 0;
    var initialized = false;
    var progressCounter = 0;
    var online = false;
    var element, manualShowHide, hasChild, hasRemoteWeb;
    var callableFunc = null;
    var dataCollection = window.dataCollection;
    var url
    var postData = { };
    var progressOptions;
    var downloadTotal = 0;
    var downloadedBytes = 0;
    var uploadTotal = 0;
    var uploadedBytes = 0;
    var isUpload = false;
    var isDownload = false;
    // Error
    // function error(errorMsg) {
    //     throw new Error(errorMsg);
    // }

    //Init
    function init(initialConnection = true) {
        initialized = true;

        if (initialConnection) {
            serviceOnline();
        }
    }

    function serviceOnline() {
        if (!initialized) {
            init(false);
        }

        online = true;
        //eslint-disable-next-line
        console.log('Progress service online');
    }

    function serviceOffline() {
        if (!initialized) {
            init(false);
        }

        online = false;
        //eslint-disable-next-line
        console.log('Progress service offline');
    }

    function buildProgressBar(el, mSH = false, hC = false, hRW = false) {
        element = el;
        manualShowHide = mSH;
        hasChild = hC;
        hasRemoteWeb = hRW;

        $(element).html(
            '<div class="progress active progress-xs">' +
                '<div class="progress-bar progress-xs bg-info progress-bar-animated progress-bar-striped ' + $(element)[0].id + '-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" style="width: 0%"></div>' +
            '</div>' +
            '<div class="row text-center text-sm text-primary m-1">' +
                '<div class="col">' +
                    '<span class="sr-only ' + $(element)[0].id + '-progress-span"></span>' +
                    '<span class="' + $(element)[0].id + '-progress-span"></span>' +
                '</div>' +
            '</div>'
        );

        if (hasChild) {
            $(element).append(
                '<div class="progress progress-child active progress-xxs" hidden>' +
                    '<div class="progress-bar progress-xxs bg-info progress-bar-animated progress-bar-striped ' + $(element)[0].id + '-child-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" style="width: 0%"></div>' +
                '</div>' +
                '<div class="row child-progress-span text-center text-sm text-primary m-1">' +
                    '<div class="col">' +
                        '<span class="sr-only ' + $(element)[0].id + '-child-progress-span"></span>' +
                        '<span class="' + $(element)[0].id + '-child-progress-span"></span>' +
                    '</div>' +
                '</div>'
            );
        }

        if (hasRemoteWeb) {
            $(element).append(
                '<div class="progress progress-remote active progress-xxs" hidden>' +
                    '<div class="progress-bar progress-xxs bg-primary progress-bar-animated progress-bar-striped ' + $(element)[0].id + '-remote-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" style="width: 0%"></div>' +
                '</div>' +
                '<div class="row remote-progress-span text-center text-sm text-primary m-1">' +
                    '<div class="col">' +
                        '<span class="sr-only ' + $(element)[0].id + '-remote-progress-span"></span>' +
                        '<span class="' + $(element)[0].id + '-remote-progress-span"></span>' +
                    '</div>' +
                '</div>'
            );
        }
    }

    function getProgress(options) {
        progressOptions = options;

        if (callableFunc && callableFunc['beforeStart']) {
            if (callableFunc['beforeStart']() === false) {
                resetProgressCounter();
                return;
            }
        }

        if (options && options.url) {
            url = options.url;
        } else {
            url =
                dataCollection.env.httpScheme +
                '://' + dataCollection.env.httpHost + '/' +
                dataCollection.env.appRoute + '/system/progress/getProgress';
        }

        if (options && options.postData) {
            postData = $.extend(postData, options.postData);
        } else {
            postData[$('#security-token').attr('name')] = $('#security-token').val();
        }

        $.post(url, postData, function(response) {
            processResponse(response);
        }, 'json');
    }

    function processResponse(response) {
        if (response && response.length === 0) {
            return;
        }

        if (callableFunc && callableFunc['beforeProcess']) {
            if (callableFunc['beforeProcess'](response) === false) {
                resetProgressCounter();
                return;
            }
        }

        var timerId;
        if (response && response.responseCode === 0) {
            if (response.responseData) {
                var responseData;

                if (typeof response.responseData === 'string' || response.responseData instanceof String) {
                    responseData = JSON.parse(response.responseData);
                } else {
                    responseData = response.responseData;
                }

                if (responseData['preCheckComplete'] == false ||
                    (responseData['callResult'] && responseData['callResult'] === 'reset')
                ) {
                    resetProgressCounter();

                    return false;
                }

                if (responseData['total'] !== 'undefined' && responseData['completed'] !== 'undefined') {
                    if (responseData['total'] !== responseData['completed']) {
                        if (responseData['runners'] && responseData['runners']['running'] !== false) {
                            if (manualShowHide) {
                                $(element).attr('hidden', true);
                            } else {
                                $(element).attr('hidden', false);
                            }

                            if (responseData['runners']['child']) {
                                $('#' + $(element)[0].id + ' .progress-child').attr('hidden', false);
                                $('.child-progress-span').attr('hidden', false);

                                if (responseData['runners']['running'] &&
                                    responseData['runners']['running']['remoteWeb'] &&
                                    responseData['runners']['running']['remoteWebCounters']
                                ) {
                                    $('#' + $(element)[0].id + ' .progress-remote').attr('hidden', false);
                                    $('.remote-progress-span').attr('hidden', false);

                                    $('.' + $(element)[0].id + '-remote-progress-span')
                                        .html(responseData['runners']['running']['text'] + ' (' + responseData['percentComplete'] + '%)');

                                    $('.' + $(element)[0].id + '-remote-bar').css('width', responseData['percentComplete'] + '%');
                                    $('.' + $(element)[0].id + '-remote-bar').attr('aria-valuenow', responseData['percentComplete']);
                                } else {
                                    $('.progress-remote, .remote-progress-span').attr('hidden', true);

                                    $('.' + $(element)[0].id + '-child-progress-span')
                                        .html(responseData['runners']['running']['text'] + ' (' + responseData['percentComplete'] + '%)');

                                    $('.' + $(element)[0].id + '-child-bar').css('width', responseData['percentComplete'] + '%');
                                    $('.' + $(element)[0].id + '-child-bar').attr('aria-valuenow', responseData['percentComplete']);
                                }
                            } else {
                                $('#' + $(element)[0].id + ' .progress-child').attr('hidden', true);
                                $('.child-progress-span').attr('hidden', true);
                                $('.' + $(element)[0].id + '-child-bar').css('width', '0%');
                                $('.' + $(element)[0].id + '-child-bar').attr('aria-valuenow', 0);

                                if (responseData['runners']['running'] &&
                                    responseData['runners']['running']['remoteWeb'] &&
                                    responseData['runners']['running']['remoteWebCounters']
                                ) {
                                    if (responseData['runners']['running']['remoteWebCounters']) {
                                        $('#' + $(element)[0].id + ' .progress-remote').attr('hidden', false);
                                        $('.remote-progress-span').attr('hidden', false);

                                        var text = getText(responseData);

                                        $('.' + $(element)[0].id + '-remote-progress-span').html(text);

                                        $('.' + $(element)[0].id + '-remote-bar').css('width', responseData['percentComplete'] + '%');
                                        $('.' + $(element)[0].id + '-remote-bar').attr('aria-valuenow', responseData['percentComplete']);
                                    } else {
                                        $('.' + $(element)[0].id + '-remote-bar').css('width', '0%');
                                        $('.' + $(element)[0].id + '-remote-bar').attr('aria-valuenow', 0);
                                    }
                                } else {
                                    $('.progress-remote, .remote-progress-span').attr('hidden', true);

                                    $('.' + $(element)[0].id + '-progress-span')
                                        .html(responseData['runners']['running']['text'] + ' (' + responseData['percentComplete'] + '%)');

                                    $('.' + $(element)[0].id + '-bar').css('width', responseData['percentComplete'] + '%');
                                    $('.' + $(element)[0].id + '-bar').attr('aria-valuenow', responseData['percentComplete']);
                                }
                            }
                        } else {
                            $('.progress-remote, .remote-progress-span').attr('hidden', true);
                        }

                        if (online === false) {
                            timerId = BazHelpers.getTimerId('progressCounter');
                            if (timerId) {
                                BazHelpers.setTimeoutTimers.stop(timerId, null, 'progressCounter');
                            }
                            if (BazHelpers.getTimerId('getProgress') === false) {
                                BazHelpers.setTimeoutTimers.add(function() {
                                    getProgress(progressOptions);
                                }, 500, null, 'getProgress');
                            }
                        }
                    } else if (responseData['total'] === responseData['completed']) {
                        if (online === false) {
                            BazHelpers.setTimeoutTimers.stopAll();
                        }

                        if (responseData['runners']['child']) {
                            $('.' + $(element)[0].id + '-child-bar').removeClass('progress-bar-animated');
                            $('.' + $(element)[0].id + '-child-bar').css('width', '100%');
                            $('.' + $(element)[0].id + '-child-bar').attr('aria-valuenow', 100);
                            $('.' + $(element)[0].id + '-child-progress-span').html('Done (100%)');
                            $('.' + $(element)[0].id + '-child-bar').removeClass(function (index, className) {
                                return (className.match (/(^|\s)bg-\S+/g) || []).join(' ');
                            }).addClass('bg-success');
                        } else {
                            $('.' + $(element)[0].id + '-bar').removeClass('progress-bar-animated');
                            $('.' + $(element)[0].id + '-bar').css('width', '100%');
                            $('.' + $(element)[0].id + '-bar').attr('aria-valuenow', 100);
                            $('.' + $(element)[0].id + '-progress-span').html('Done (100%)');
                            $('.' + $(element)[0].id + '-bar').removeClass(function (index, className) {
                                return (className.match (/(^|\s)bg-\S+/g) || []).join(' ');
                            }).addClass('bg-success');
                            $('#' + $(element)[0].id + ' .progress-child').attr('hidden', true);
                            $('.child-progress-span').attr('hidden', true);

                            if (manualShowHide) {
                                $(element).attr('hidden', true);
                            } else {
                                $(element).attr('hidden', false);
                            }

                            if (callableFunc && callableFunc['onComplete']) {
                                callableFunc['onComplete'](response);
                            }

                            downloadTotal = 0;
                            downloadedBytes = 0;
                            uploadTotal = 0;
                            uploadedBytes = 0;
                            isUpload = false;
                            isDownload = false;
                            $('.' + $(element)[0].id + '-child-bar').css('width', '0%');
                            $('.' + $(element)[0].id + '-child-bar').attr('aria-valuenow', 0);
                            $('.' + $(element)[0].id + '-remote-bar').css('width', '0%');
                            $('.' + $(element)[0].id + '-remote-bar').attr('aria-valuenow', 0);
                            $('.progress-remote, .remote-progress-span').attr('hidden', true);
                        }
                    } else {
                        resetProgressCounter();
                    }
                } else {
                    resetProgressCounter();
                }
            } else {
                resetProgressCounter();
            }
        } else {
            resetProgressCounter();
        }

        if (callableFunc && callableFunc['afterProcess']) {
            if (callableFunc['afterProcess'](response) === false) {
                resetProgressCounter();
                return;
            }
        }
    }

    function getText(responseData) {
        var text = responseData['runners']['running']['text'] + ' (' + responseData['percentComplete'] + '%)';

        if (responseData['runners']['running']['remoteWebCounters']) {
            if (responseData['runners']['running']['remoteWebCounters']['downloadTotal'] &&
                responseData['runners']['running']['remoteWebCounters']['downloadTotal'] > 0
            ) {
                isDownload = true;
                downloadTotal = responseData['runners']['running']['remoteWebCounters']['downloadTotal'];
                downloadedBytes = responseData['runners']['running']['remoteWebCounters']['downloadedBytes'];
            } else if (responseData['runners']['running']['remoteWebCounters']['uploadTotal'] &&
                responseData['runners']['running']['remoteWebCounters']['uploadTotal'] > 0
            ) {
                isUpload = true;
                uploadTotal = responseData['runners']['running']['remoteWebCounters']['uploadTotal'];
                uploadedBytes = responseData['runners']['running']['remoteWebCounters']['uploadedBytes'];
            }
        }

        if (isDownload || isUpload) {
            if (isDownload) {
                text = responseData['runners']['running']['text'] + ' (' + responseData['percentComplete'] + '% | ' + downloadedBytes + '/' + downloadTotal + ' bytes)';
            } else if (isUpload) {
                text = responseData['runners']['running']['text'] + ' (' + responseData['percentComplete'] + '% | ' + uploadedBytes + '/' + uploadTotal + ' bytes)';
            }
        }

        return text;
    }

    function resetProgressCounter() {
        if (progressCounter !== 60) {
            progressCounter ++;

            if (online === false) {
                BazHelpers.setTimeoutTimers.stopAll();
                BazHelpers.setTimeoutTimers.add(function() {
                    getProgress(progressOptions);
                }, 1000, null, 'progressCounter');
            }
        }

        $('#' + $(element)[0].id).attr('hidden', true);
        $('.' + $(element)[0].id + '-bar').css('width', '0%');
        $('.' + $(element)[0].id + '-bar').attr('aria-valuenow', 0);
        downloadTotal = 0;
        downloadedBytes = 0;
        uploadTotal = 0;
        uploadedBytes = 0;
        isUpload = false;
        isDownload = false;
    }

    function onMessage(data) {
        //eslint-disable-next-line
        console.log(data);
        processResponse(data);
    }

    function setCallable(callable) {
        callableFunc = callable;
    }

    function bazProgressConstructor() {
        // if something needs to be constructed
        return null;
    }

    function setup(BazProgressConstructor) {
        BazProgress = BazProgressConstructor;
        BazProgress.defaults = { };
        BazProgress.init = function(options) {
            init(_extends(BazProgress.defaults, options));
        }
        BazProgress.serviceOnline = function(options) {
            serviceOnline(_extends(BazProgress.defaults, options));
        }
        BazProgress.serviceOffline = function(options) {
            serviceOffline(_extends(BazProgress.defaults, options));
        }
        BazProgress.onMessage = function(data) {
            onMessage(data);
        }
        BazProgress.getProgress = function(options, force = false) {
            if (online === false || force === true) {
                getProgress(options);
            }
        }
        BazProgress.buildProgressBar = function(el, mSH = false, child = false, remoteWeb = false) {
            buildProgressBar(el, mSH, child, remoteWeb);
        }
        BazProgress.setCallable = function(callable) {
            setCallable(callable);
        }
        BazProgress.isOnline = function() {
            return online;
        }
    }

    setup(bazProgressConstructor);

    return bazProgressConstructor;
}();