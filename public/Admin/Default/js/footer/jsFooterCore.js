/* globals exports require define globalThis */
/*!
 * AdminLTE v3.1.0-pre (https://adminlte.io)
 * Copyright 2014-2020 Colorlib <https://colorlib.com>
 * Licensed under MIT (https://github.com/ColorlibHQ/AdminLTE/blob/master/LICENSE)
 */
(function (global, factory) {
  'use strict';
  typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports, require('jquery')) :
  typeof define === 'function' && define.amd ? define(['exports', 'jquery'], factory) :
  (global = typeof globalThis !== 'undefined' ? globalThis : global || self, factory(global.adminlte = {}, global.jQuery));
}(this, (function (exports, $) {
  'use strict';

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

  var NAME = 'CardRefresh';
  var DATA_KEY = 'lte.cardrefresh';
  var EVENT_KEY = "." + DATA_KEY;
  var JQUERY_NO_CONFLICT = $__default['default'].fn[NAME];
  var EVENT_LOADED = "loaded" + EVENT_KEY;
  var EVENT_OVERLAY_ADDED = "overlay.added" + EVENT_KEY;
  var EVENT_OVERLAY_REMOVED = "overlay.removed" + EVENT_KEY;
  var CLASS_NAME_CARD = 'card';
  var SELECTOR_CARD = "." + CLASS_NAME_CARD;
  var SELECTOR_DATA_REFRESH = '[data-card-widget="card-refresh"]';
  var Default = {
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
      this._parent = element.parents(SELECTOR_CARD).first();
      this._settings = $__default['default'].extend({}, Default, settings);
      this._overlay = $__default['default'](this._settings.overlayTemplate);

      if (element.hasClass(CLASS_NAME_CARD)) {
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
      var data = $__default['default'](this).data(DATA_KEY);

      var _options = $__default['default'].extend({}, Default, $__default['default'](this).data());

      if (!data) {
        data = new CardRefresh($__default['default'](this), _options);
        $__default['default'](this).data(DATA_KEY, typeof config === 'string' ? data : config);
      }

      if (typeof config === 'string' && config.match(/load/)) {
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

  $__default['default'].fn[NAME] = CardRefresh._jQueryInterface;
  $__default['default'].fn[NAME].Constructor = CardRefresh;

  $__default['default'].fn[NAME].noConflict = function () {
    $__default['default'].fn[NAME] = JQUERY_NO_CONFLICT;
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

  var NAME$1 = 'CardWidget';
  var DATA_KEY$1 = 'lte.cardwidget';
  var EVENT_KEY$1 = "." + DATA_KEY$1;
  var JQUERY_NO_CONFLICT$1 = $__default['default'].fn[NAME$1];
  var EVENT_EXPANDED = "expanded" + EVENT_KEY$1;
  var EVENT_COLLAPSED = "collapsed" + EVENT_KEY$1;
  var EVENT_MAXIMIZED = "maximized" + EVENT_KEY$1;
  var EVENT_MINIMIZED = "minimized" + EVENT_KEY$1;
  var EVENT_REMOVED = "removed" + EVENT_KEY$1;
  var CLASS_NAME_CARD$1 = 'card';
  var CLASS_NAME_COLLAPSED = 'collapsed-card';
  var CLASS_NAME_COLLAPSING = 'collapsing-card';
  var CLASS_NAME_EXPANDING = 'expanding-card';
  var CLASS_NAME_WAS_COLLAPSED = 'was-collapsed';
  var CLASS_NAME_MAXIMIZED = 'maximized-card';
  var SELECTOR_DATA_REMOVE = '[data-card-widget="remove"]';
  var SELECTOR_DATA_COLLAPSE = '[data-card-widget="collapse"]';
  var SELECTOR_DATA_MAXIMIZE = '[data-card-widget="maximize"]';
  var SELECTOR_CARD$1 = "." + CLASS_NAME_CARD$1;
  var SELECTOR_CARD_HEADER = '.card-header';
  var SELECTOR_CARD_BODY = '.card-body';
  var SELECTOR_CARD_FOOTER = '.card-footer';
  var Default$1 = {
    animationSpeed: 'normal',
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
      this._parent = element.parents(SELECTOR_CARD$1).first();

      if (element.hasClass(CLASS_NAME_CARD$1)) {
        this._parent = element;
      }

      this._settings = $__default['default'].extend({}, Default$1, settings);
    }

    var _proto = CardWidget.prototype;

    _proto.collapse = function collapse() {
      var _this = this;

      this._parent.addClass(CLASS_NAME_COLLAPSING).children(SELECTOR_CARD_BODY + ", " + SELECTOR_CARD_FOOTER).slideUp(this._settings.animationSpeed, function () {
        _this._parent.addClass(CLASS_NAME_COLLAPSED).removeClass(CLASS_NAME_COLLAPSING);
      });

      this._parent.find("> " + SELECTOR_CARD_HEADER + " " + this._settings.collapseTrigger + " ." + this._settings.collapseIcon).addClass(this._settings.expandIcon).removeClass(this._settings.collapseIcon);

      this._element.trigger($__default['default'].Event(EVENT_COLLAPSED), this._parent);
    };

    _proto.expand = function expand() {
      var _this2 = this;

      this._parent.addClass(CLASS_NAME_EXPANDING).children(SELECTOR_CARD_BODY + ", " + SELECTOR_CARD_FOOTER).slideDown(this._settings.animationSpeed, function () {
        _this2._parent.removeClass(CLASS_NAME_COLLAPSED).removeClass(CLASS_NAME_EXPANDING);
      });

      this._parent.find("> " + SELECTOR_CARD_HEADER + " " + this._settings.collapseTrigger + " ." + this._settings.expandIcon).addClass(this._settings.collapseIcon).removeClass(this._settings.expandIcon);

      this._element.trigger($__default['default'].Event(EVENT_EXPANDED), this._parent);
    };

    _proto.remove = function remove() {
      this._parent.slideUp();

      this._element.trigger($__default['default'].Event(EVENT_REMOVED), this._parent);
    };

    _proto.toggle = function toggle() {
      if (this._parent.hasClass(CLASS_NAME_COLLAPSED)) {
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

        if ($element.hasClass(CLASS_NAME_COLLAPSED)) {
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
      var data = $__default['default'](this).data(DATA_KEY$1);

      var _options = $__default['default'].extend({}, Default$1, $__default['default'](this).data());

      if (!data) {
        data = new CardWidget($__default['default'](this), _options);
        $__default['default'](this).data(DATA_KEY$1, typeof config === 'string' ? data : config);
      }

      if (typeof config === 'string' && config.match(/collapse|expand|remove|toggle|maximize|minimize|toggleMaximize/)) {
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

  $__default['default'].fn[NAME$1] = CardWidget._jQueryInterface;
  $__default['default'].fn[NAME$1].Constructor = CardWidget;

  $__default['default'].fn[NAME$1].noConflict = function () {
    $__default['default'].fn[NAME$1] = JQUERY_NO_CONFLICT$1;
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

  var NAME$2 = 'ControlSidebar';
  var DATA_KEY$2 = 'lte.controlsidebar';
  var EVENT_KEY$2 = "." + DATA_KEY$2;
  var JQUERY_NO_CONFLICT$2 = $__default['default'].fn[NAME$2];
  var EVENT_COLLAPSED$1 = "collapsed" + EVENT_KEY$2;
  var EVENT_EXPANDED$1 = "expanded" + EVENT_KEY$2;
  var SELECTOR_CONTROL_SIDEBAR = '.control-sidebar';
  var SELECTOR_CONTROL_SIDEBAR_CONTENT = '.control-sidebar-content';
  var SELECTOR_DATA_TOGGLE = '[data-widget="control-sidebar"]';
  var SELECTOR_HEADER = '.main-header';
  var SELECTOR_FOOTER = '.main-footer';
  var CLASS_NAME_CONTROL_SIDEBAR_ANIMATE = 'control-sidebar-animate';
  var CLASS_NAME_CONTROL_SIDEBAR_OPEN = 'control-sidebar-open';
  var CLASS_NAME_CONTROL_SIDEBAR_SLIDE = 'control-sidebar-slide-open';
  var CLASS_NAME_LAYOUT_FIXED = 'layout-fixed';
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
  var Default$2 = {
    controlsidebarSlide: true,
    scrollbarTheme: 'os-theme-light',
    scrollbarAutoHide: 'l'
  };
  /**
   * Class Definition
   * ====================================================
   */

  var ControlSidebar = /*#__PURE__*/function () {
    function ControlSidebar(element, config) {
      this._element = element;
      this._config = config;

      this._init();
    } // Public


    var _proto = ControlSidebar.prototype;

    _proto.collapse = function collapse() {
      var $body = $__default['default']('body');
      var $html = $__default['default']('html'); // Show the control sidebar

      if (this._config.controlsidebarSlide) {
        $html.addClass(CLASS_NAME_CONTROL_SIDEBAR_ANIMATE);
        $body.removeClass(CLASS_NAME_CONTROL_SIDEBAR_SLIDE).delay(300).queue(function () {
          $__default['default'](SELECTOR_CONTROL_SIDEBAR).hide();
          $html.removeClass(CLASS_NAME_CONTROL_SIDEBAR_ANIMATE);
          $__default['default'](this).dequeue();
        });
      } else {
        $body.removeClass(CLASS_NAME_CONTROL_SIDEBAR_OPEN);
      }

      $__default['default'](this._element).trigger($__default['default'].Event(EVENT_COLLAPSED$1));
    };

    _proto.show = function show() {
      var $body = $__default['default']('body');
      var $html = $__default['default']('html'); // Collapse the control sidebar

      if (this._config.controlsidebarSlide) {
        $html.addClass(CLASS_NAME_CONTROL_SIDEBAR_ANIMATE);
        $__default['default'](SELECTOR_CONTROL_SIDEBAR).show().delay(10).queue(function () {
          $body.addClass(CLASS_NAME_CONTROL_SIDEBAR_SLIDE).delay(300).queue(function () {
            $html.removeClass(CLASS_NAME_CONTROL_SIDEBAR_ANIMATE);
            $__default['default'](this).dequeue();
          });
          $__default['default'](this).dequeue();
        });
      } else {
        $body.addClass(CLASS_NAME_CONTROL_SIDEBAR_OPEN);
      }

      this._fixHeight();

      this._fixScrollHeight();

      $__default['default'](this._element).trigger($__default['default'].Event(EVENT_EXPANDED$1));
    };

    _proto.toggle = function toggle() {
      var $body = $__default['default']('body');
      var shouldClose = $body.hasClass(CLASS_NAME_CONTROL_SIDEBAR_OPEN) || $body.hasClass(CLASS_NAME_CONTROL_SIDEBAR_SLIDE);

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

      this._fixHeight();

      this._fixScrollHeight();

      $__default['default'](window).resize(function () {
        _this._fixHeight();

        _this._fixScrollHeight();
      });
      $__default['default'](window).scroll(function () {
        var $body = $__default['default']('body');
        var shouldFixHeight = $body.hasClass(CLASS_NAME_CONTROL_SIDEBAR_OPEN) || $body.hasClass(CLASS_NAME_CONTROL_SIDEBAR_SLIDE);

        if (shouldFixHeight) {
          _this._fixScrollHeight();
        }
      });
    };

    _proto._fixScrollHeight = function _fixScrollHeight() {
      var $body = $__default['default']('body');

      if (!$body.hasClass(CLASS_NAME_LAYOUT_FIXED)) {
        return;
      }

      var heights = {
        scroll: $__default['default'](document).height(),
        window: $__default['default'](window).height(),
        header: $__default['default'](SELECTOR_HEADER).outerHeight(),
        footer: $__default['default'](SELECTOR_FOOTER).outerHeight()
      };
      var positions = {
        bottom: Math.abs(heights.window + $__default['default'](window).scrollTop() - heights.scroll),
        top: $__default['default'](window).scrollTop()
      };
      var navbarFixed = ($body.hasClass(CLASS_NAME_NAVBAR_FIXED) || $body.hasClass(CLASS_NAME_NAVBAR_SM_FIXED) || $body.hasClass(CLASS_NAME_NAVBAR_MD_FIXED) || $body.hasClass(CLASS_NAME_NAVBAR_LG_FIXED) || $body.hasClass(CLASS_NAME_NAVBAR_XL_FIXED)) && $__default['default'](SELECTOR_HEADER).css('position') === 'fixed';
      var footerFixed = ($body.hasClass(CLASS_NAME_FOOTER_FIXED) || $body.hasClass(CLASS_NAME_FOOTER_SM_FIXED) || $body.hasClass(CLASS_NAME_FOOTER_MD_FIXED) || $body.hasClass(CLASS_NAME_FOOTER_LG_FIXED) || $body.hasClass(CLASS_NAME_FOOTER_XL_FIXED)) && $__default['default'](SELECTOR_FOOTER).css('position') === 'fixed';
      var $controlSidebar = $__default['default'](SELECTOR_CONTROL_SIDEBAR);
      var $controlsidebarContent = $__default['default'](SELECTOR_CONTROL_SIDEBAR + ", " + SELECTOR_CONTROL_SIDEBAR + " " + SELECTOR_CONTROL_SIDEBAR_CONTENT);

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
    };

    _proto._fixHeight = function _fixHeight() {
      var $body = $__default['default']('body');

      if (!$body.hasClass(CLASS_NAME_LAYOUT_FIXED)) {
        return;
      }

      var heights = {
        window: $__default['default'](window).height(),
        header: $__default['default'](SELECTOR_HEADER).outerHeight(),
        footer: $__default['default'](SELECTOR_FOOTER).outerHeight()
      };
      var sidebarHeight = heights.window - heights.header;

      if ($body.hasClass(CLASS_NAME_FOOTER_FIXED) || $body.hasClass(CLASS_NAME_FOOTER_SM_FIXED) || $body.hasClass(CLASS_NAME_FOOTER_MD_FIXED) || $body.hasClass(CLASS_NAME_FOOTER_LG_FIXED) || $body.hasClass(CLASS_NAME_FOOTER_XL_FIXED)) {
        if ($__default['default'](SELECTOR_FOOTER).css('position') === 'fixed') {
          sidebarHeight = heights.window - heights.header - heights.footer;
        }
      }

      var $controlSidebar = $__default['default'](SELECTOR_CONTROL_SIDEBAR + " " + SELECTOR_CONTROL_SIDEBAR_CONTENT);
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
        var data = $__default['default'](this).data(DATA_KEY$2);

        var _options = $__default['default'].extend({}, Default$2, $__default['default'](this).data());

        if (!data) {
          data = new ControlSidebar(this, _options);
          $__default['default'](this).data(DATA_KEY$2, data);
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


  $__default['default'](document).on('click', SELECTOR_DATA_TOGGLE, function (event) {
    event.preventDefault();

    ControlSidebar._jQueryInterface.call($__default['default'](this), 'toggle');
  });
  /**
   * jQuery API
   * ====================================================
   */

  $__default['default'].fn[NAME$2] = ControlSidebar._jQueryInterface;
  $__default['default'].fn[NAME$2].Constructor = ControlSidebar;

  $__default['default'].fn[NAME$2].noConflict = function () {
    $__default['default'].fn[NAME$2] = JQUERY_NO_CONFLICT$2;
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

  var NAME$3 = 'DirectChat';
  var DATA_KEY$3 = 'lte.directchat';
  var EVENT_KEY$3 = "." + DATA_KEY$3;
  var JQUERY_NO_CONFLICT$3 = $__default['default'].fn[NAME$3];
  var EVENT_TOGGLED = "toggled" + EVENT_KEY$3;
  var SELECTOR_DATA_TOGGLE$1 = '[data-widget="chat-pane-toggle"]';
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
        var data = $__default['default'](this).data(DATA_KEY$3);

        if (!data) {
          data = new DirectChat($__default['default'](this));
          $__default['default'](this).data(DATA_KEY$3, data);
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


  $__default['default'](document).on('click', SELECTOR_DATA_TOGGLE$1, function (event) {
    if (event) {
      event.preventDefault();
    }

    DirectChat._jQueryInterface.call($__default['default'](this), 'toggle');
  });
  /**
   * jQuery API
   * ====================================================
   */

  $__default['default'].fn[NAME$3] = DirectChat._jQueryInterface;
  $__default['default'].fn[NAME$3].Constructor = DirectChat;

  $__default['default'].fn[NAME$3].noConflict = function () {
    $__default['default'].fn[NAME$3] = JQUERY_NO_CONFLICT$3;
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

  var NAME$4 = 'Dropdown';
  var DATA_KEY$4 = 'lte.dropdown';
  var JQUERY_NO_CONFLICT$4 = $__default['default'].fn[NAME$4];
  var SELECTOR_NAVBAR = '.navbar';
  var SELECTOR_DROPDOWN_MENU = '.dropdown-menu';
  var SELECTOR_DROPDOWN_MENU_ACTIVE = '.dropdown-menu.show';
  var SELECTOR_DROPDOWN_TOGGLE = '[data-toggle="dropdown"]';
  var CLASS_NAME_DROPDOWN_RIGHT = 'dropdown-menu-right';
  var CLASS_NAME_DROPDOWN_SUBMENU = 'dropdown-submenu'; // TODO: this is unused; should be removed along with the extend?

  var Default$3 = {};
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
        var data = $__default['default'](this).data(DATA_KEY$4);

        var _config = $__default['default'].extend({}, Default$3, $__default['default'](this).data());

        if (!data) {
          data = new Dropdown($__default['default'](this), _config);
          $__default['default'](this).data(DATA_KEY$4, data);
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

  $__default['default'].fn[NAME$4] = Dropdown._jQueryInterface;
  $__default['default'].fn[NAME$4].Constructor = Dropdown;

  $__default['default'].fn[NAME$4].noConflict = function () {
    $__default['default'].fn[NAME$4] = JQUERY_NO_CONFLICT$4;
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

  var NAME$5 = 'ExpandableTable';
  var DATA_KEY$5 = 'lte.expandableTable';
  var EVENT_KEY$4 = "." + DATA_KEY$5;
  var JQUERY_NO_CONFLICT$5 = $__default['default'].fn[NAME$5];
  var EVENT_EXPANDED$2 = "expanded" + EVENT_KEY$4;
  var EVENT_COLLAPSED$2 = "collapsed" + EVENT_KEY$4;
  var SELECTOR_TABLE = '.expandable-table';
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
        var $body = $__default['default']($header).next().children().first().children();

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
      var $body = $element.next().children().first().children();
      $body.stop();

      if ($type === 'true') {
        $body.slideUp(time, function () {
          $element.next().addClass('d-none');
        });
        $element.attr(SELECTOR_ARIA_ATTR, 'false');
        $element.trigger($__default['default'].Event(EVENT_COLLAPSED$2));
      } else if ($type === 'false') {
        $element.next().removeClass('d-none');
        $body.slideDown(time);
        $element.attr(SELECTOR_ARIA_ATTR, 'true');
        $element.trigger($__default['default'].Event(EVENT_EXPANDED$2));
      }
    } // Static
    ;

    ExpandableTable._jQueryInterface = function _jQueryInterface(operation) {
      return this.each(function () {
        var data = $__default['default'](this).data(DATA_KEY$5);

        if (!data) {
          data = new ExpandableTable($__default['default'](this));
          $__default['default'](this).data(DATA_KEY$5, data);
        }

        if (typeof operation === 'string' && operation.match(/init|toggleRow/)) {
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

  $__default['default'].fn[NAME$5] = ExpandableTable._jQueryInterface;
  $__default['default'].fn[NAME$5].Constructor = ExpandableTable;

  $__default['default'].fn[NAME$5].noConflict = function () {
    $__default['default'].fn[NAME$5] = JQUERY_NO_CONFLICT$5;
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

  var NAME$6 = 'Fullscreen';
  var DATA_KEY$6 = 'lte.fullscreen';
  var JQUERY_NO_CONFLICT$6 = $__default['default'].fn[NAME$6];
  var SELECTOR_DATA_WIDGET = '[data-widget="fullscreen"]';
  var SELECTOR_ICON = SELECTOR_DATA_WIDGET + " i";
  var Default$4 = {
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
      this.options = $__default['default'].extend({}, Default$4, _options);
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
      var data = $__default['default'](this).data(DATA_KEY$6);

      if (!data) {
        data = $__default['default'](this).data();
      }

      var _options = $__default['default'].extend({}, Default$4, typeof config === 'object' ? config : data);

      var plugin = new Fullscreen($__default['default'](this), _options);
      $__default['default'](this).data(DATA_KEY$6, typeof config === 'object' ? config : data);

      if (typeof config === 'string' && config.match(/toggle|fullscreen|windowed/)) {
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


  $__default['default'](document).on('click', SELECTOR_DATA_WIDGET, function () {
    Fullscreen._jQueryInterface.call($__default['default'](this), 'toggle');
  });
  /**
   * jQuery API
   * ====================================================
   */

  $__default['default'].fn[NAME$6] = Fullscreen._jQueryInterface;
  $__default['default'].fn[NAME$6].Constructor = Fullscreen;

  $__default['default'].fn[NAME$6].noConflict = function () {
    $__default['default'].fn[NAME$6] = JQUERY_NO_CONFLICT$6;
    return Fullscreen._jQueryInterface;
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

  var NAME$7 = 'Layout';
  var DATA_KEY$7 = 'lte.layout';
  var JQUERY_NO_CONFLICT$7 = $__default['default'].fn[NAME$7];
  var SELECTOR_HEADER$1 = '.main-header';
  var SELECTOR_MAIN_SIDEBAR = '.main-sidebar';
  var SELECTOR_SIDEBAR = '.main-sidebar .sidebar';
  var SELECTOR_CONTENT = '.content-wrapper';
  var SELECTOR_CONTROL_SIDEBAR_CONTENT$1 = '.control-sidebar-content';
  var SELECTOR_CONTROL_SIDEBAR_BTN = '[data-widget="control-sidebar"]';
  var SELECTOR_FOOTER$1 = '.main-footer';
  var SELECTOR_PUSHMENU_BTN = '[data-widget="pushmenu"]';
  var SELECTOR_LOGIN_BOX = '.login-box';
  var SELECTOR_REGISTER_BOX = '.register-box';
  var CLASS_NAME_SIDEBAR_FOCUSED = 'sidebar-focused';
  var CLASS_NAME_LAYOUT_FIXED$1 = 'layout-fixed';
  var CLASS_NAME_CONTROL_SIDEBAR_SLIDE_OPEN = 'control-sidebar-slide-open';
  var CLASS_NAME_CONTROL_SIDEBAR_OPEN$1 = 'control-sidebar-open';
  var Default$5 = {
    scrollbarTheme: 'os-theme-light',
    scrollbarAutoHide: 'l',
    panelAutoHeight: true,
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

      this._init();
    } // Public


    var _proto = Layout.prototype;

    _proto.fixLayoutHeight = function fixLayoutHeight(extra) {
      if (extra === void 0) {
        extra = null;
      }

      var $body = $__default['default']('body');
      var controlSidebar = 0;

      if ($body.hasClass(CLASS_NAME_CONTROL_SIDEBAR_SLIDE_OPEN) || $body.hasClass(CLASS_NAME_CONTROL_SIDEBAR_OPEN$1) || extra === 'control_sidebar') {
        controlSidebar = $__default['default'](SELECTOR_CONTROL_SIDEBAR_CONTENT$1).height();
      }

      var heights = {
        window: $__default['default'](window).height(),
        header: $__default['default'](SELECTOR_HEADER$1).length !== 0 ? $__default['default'](SELECTOR_HEADER$1).outerHeight() : 0,
        footer: $__default['default'](SELECTOR_FOOTER$1).length !== 0 ? $__default['default'](SELECTOR_FOOTER$1).outerHeight() : 0,
        sidebar: $__default['default'](SELECTOR_SIDEBAR).length !== 0 ? $__default['default'](SELECTOR_SIDEBAR).height() : 0,
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
          $contentSelector.css('min-height', max + offset);
        } else if (max === heights.window) {
          $contentSelector.css('min-height', max + offset - heights.header - heights.footer);
        } else {
          $contentSelector.css('min-height', max + offset - heights.header);
        }

        if (this._isFooterFixed()) {
          $contentSelector.css('min-height', parseFloat($contentSelector.css('min-height')) + heights.footer);
        }
      }

      if (!$body.hasClass(CLASS_NAME_LAYOUT_FIXED$1)) {
        return;
      }

      if (offset !== false) {
        $contentSelector.css('min-height', max + offset - heights.header - heights.footer);
      }

      if (typeof $__default['default'].fn.overlayScrollbars !== 'undefined') {
        $__default['default'](SELECTOR_SIDEBAR).overlayScrollbars({
          className: this._config.scrollbarTheme,
          sizeAutoCapable: true,
          scrollbars: {
            autoHide: this._config.scrollbarAutoHide,
            clickScrolling: true
          }
        });
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

        if ($body.css('min-height') !== boxHeight) {
          $body.css('min-height', boxHeight);
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

      $__default['default'](SELECTOR_SIDEBAR).on('collapsed.lte.treeview expanded.lte.treeview', function () {
        _this.fixLayoutHeight();
      });
      $__default['default'](SELECTOR_PUSHMENU_BTN).on('collapsed.lte.pushmenu shown.lte.pushmenu', function () {
        _this.fixLayoutHeight();
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
      return $__default['default'](SELECTOR_FOOTER$1).css('position') === 'fixed';
    } // Static
    ;

    Layout._jQueryInterface = function _jQueryInterface(config) {
      if (config === void 0) {
        config = '';
      }

      return this.each(function () {
        var data = $__default['default'](this).data(DATA_KEY$7);

        var _options = $__default['default'].extend({}, Default$5, $__default['default'](this).data());

        if (!data) {
          data = new Layout($__default['default'](this), _options);
          $__default['default'](this).data(DATA_KEY$7, data);
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
  $__default['default'](SELECTOR_SIDEBAR + " a").on('focusin', function () {
    $__default['default'](SELECTOR_MAIN_SIDEBAR).addClass(CLASS_NAME_SIDEBAR_FOCUSED);
  });
  $__default['default'](SELECTOR_SIDEBAR + " a").on('focusout', function () {
    $__default['default'](SELECTOR_MAIN_SIDEBAR).removeClass(CLASS_NAME_SIDEBAR_FOCUSED);
  });
  /**
   * jQuery API
   * ====================================================
   */

  $__default['default'].fn[NAME$7] = Layout._jQueryInterface;
  $__default['default'].fn[NAME$7].Constructor = Layout;

  $__default['default'].fn[NAME$7].noConflict = function () {
    $__default['default'].fn[NAME$7] = JQUERY_NO_CONFLICT$7;
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

  var NAME$8 = 'PushMenu';
  var DATA_KEY$8 = 'lte.pushmenu';
  var EVENT_KEY$5 = "." + DATA_KEY$8;
  var JQUERY_NO_CONFLICT$8 = $__default['default'].fn[NAME$8];
  var EVENT_COLLAPSED$3 = "collapsed" + EVENT_KEY$5;
  var EVENT_SHOWN = "shown" + EVENT_KEY$5;
  var SELECTOR_TOGGLE_BUTTON = '[data-widget="pushmenu"]';
  var SELECTOR_BODY = 'body';
  var SELECTOR_OVERLAY = '#sidebar-overlay';
  var SELECTOR_WRAPPER = '.wrapper';
  var CLASS_NAME_COLLAPSED$1 = 'sidebar-collapse';
  var CLASS_NAME_OPEN = 'sidebar-open';
  var CLASS_NAME_IS_OPENING = 'sidebar-is-opening';
  var CLASS_NAME_CLOSED = 'sidebar-closed';
  var Default$6 = {
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
      this._options = $__default['default'].extend({}, Default$6, options);

      if ($__default['default'](SELECTOR_OVERLAY).length === 0) {
        this._addOverlay();
      }

      this._init();
    } // Public


    var _proto = PushMenu.prototype;

    _proto.expand = function expand() {
      var $bodySelector = $__default['default'](SELECTOR_BODY);

      if (this._options.autoCollapseSize) {
        if ($__default['default'](window).width() <= this._options.autoCollapseSize) {
          $bodySelector.addClass(CLASS_NAME_OPEN);
        }
      }

      $bodySelector.addClass(CLASS_NAME_IS_OPENING).removeClass(CLASS_NAME_COLLAPSED$1 + " " + CLASS_NAME_CLOSED).delay(50).queue(function () {
        $bodySelector.removeClass(CLASS_NAME_IS_OPENING);
        $__default['default'](this).dequeue();
      });

      if (this._options.enableRemember) {
        localStorage.setItem("remember" + EVENT_KEY$5, CLASS_NAME_OPEN);
      }

      $__default['default'](this._element).trigger($__default['default'].Event(EVENT_SHOWN));
    };

    _proto.collapse = function collapse() {
      var $bodySelector = $__default['default'](SELECTOR_BODY);

      if (this._options.autoCollapseSize) {
        if ($__default['default'](window).width() <= this._options.autoCollapseSize) {
          $bodySelector.removeClass(CLASS_NAME_OPEN).addClass(CLASS_NAME_CLOSED);
        }
      }

      $bodySelector.addClass(CLASS_NAME_COLLAPSED$1);

      if (this._options.enableRemember) {
        localStorage.setItem("remember" + EVENT_KEY$5, CLASS_NAME_COLLAPSED$1);
      }

      $__default['default'](this._element).trigger($__default['default'].Event(EVENT_COLLAPSED$3));
    };

    _proto.toggle = function toggle() {
      if ($__default['default'](SELECTOR_BODY).hasClass(CLASS_NAME_COLLAPSED$1)) {
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
        if (!$bodySelector.hasClass(CLASS_NAME_OPEN)) {
          this.collapse();
        }
      } else if (resize === true) {
        if ($bodySelector.hasClass(CLASS_NAME_OPEN)) {
          $bodySelector.removeClass(CLASS_NAME_OPEN);
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
      var toggleState = localStorage.getItem("remember" + EVENT_KEY$5);

      if (toggleState === CLASS_NAME_COLLAPSED$1) {
        if (this._options.noTransitionAfterReload) {
          $body.addClass('hold-transition').addClass(CLASS_NAME_COLLAPSED$1).delay(50).queue(function () {
            $__default['default'](this).removeClass('hold-transition');
            $__default['default'](this).dequeue();
          });
        } else {
          $body.addClass(CLASS_NAME_COLLAPSED$1);
        }
      } else if (this._options.noTransitionAfterReload) {
        $body.addClass('hold-transition').removeClass(CLASS_NAME_COLLAPSED$1).delay(50).queue(function () {
          $__default['default'](this).removeClass('hold-transition');
          $__default['default'](this).dequeue();
        });
      } else {
        $body.removeClass(CLASS_NAME_COLLAPSED$1);
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
        var data = $__default['default'](this).data(DATA_KEY$8);

        var _options = $__default['default'].extend({}, Default$6, $__default['default'](this).data());

        if (!data) {
          data = new PushMenu(this, _options);
          $__default['default'](this).data(DATA_KEY$8, data);
        }

        if (typeof operation === 'string' && operation.match(/collapse|expand|toggle/)) {
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


  $__default['default'](document).on('click', SELECTOR_TOGGLE_BUTTON, function (event) {
    event.preventDefault();
    var button = event.currentTarget;

    if ($__default['default'](button).data('widget') !== 'pushmenu') {
      button = $__default['default'](button).closest(SELECTOR_TOGGLE_BUTTON);
    }

    PushMenu._jQueryInterface.call($__default['default'](button), 'toggle');
  });
  $__default['default'](window).on('load', function () {
    PushMenu._jQueryInterface.call($__default['default'](SELECTOR_TOGGLE_BUTTON));
  });
  /**
   * jQuery API
   * ====================================================
   */

  $__default['default'].fn[NAME$8] = PushMenu._jQueryInterface;
  $__default['default'].fn[NAME$8].Constructor = PushMenu;

  $__default['default'].fn[NAME$8].noConflict = function () {
    $__default['default'].fn[NAME$8] = JQUERY_NO_CONFLICT$8;
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

  var NAME$9 = 'SidebarSearch';
  var DATA_KEY$9 = 'lte.sidebar-search';
  var JQUERY_NO_CONFLICT$9 = $__default['default'].fn[NAME$9];
  var CLASS_NAME_OPEN$1 = 'sidebar-search-open';
  var CLASS_NAME_ICON_SEARCH = 'fa-search';
  var CLASS_NAME_ICON_CLOSE = 'fa-times';
  var CLASS_NAME_HEADER = 'nav-header';
  var CLASS_NAME_SEARCH_RESULTS = 'sidebar-search-results';
  var CLASS_NAME_LIST_GROUP = 'list-group';
  var SELECTOR_DATA_WIDGET$1 = '[data-widget="sidebar-search"]';
  var SELECTOR_SIDEBAR$1 = '.main-sidebar .nav-sidebar';
  var SELECTOR_NAV_LINK = '.nav-link';
  var SELECTOR_NAV_TREEVIEW = '.nav-treeview';
  var SELECTOR_SEARCH_INPUT = SELECTOR_DATA_WIDGET$1 + " .form-control";
  var SELECTOR_SEARCH_BUTTON = SELECTOR_DATA_WIDGET$1 + " .btn";
  var SELECTOR_SEARCH_ICON = SELECTOR_SEARCH_BUTTON + " i";
  var SELECTOR_SEARCH_LIST_GROUP = "." + CLASS_NAME_LIST_GROUP;
  var SELECTOR_SEARCH_RESULTS = "." + CLASS_NAME_SEARCH_RESULTS;
  var SELECTOR_SEARCH_RESULTS_GROUP = SELECTOR_SEARCH_RESULTS + " ." + CLASS_NAME_LIST_GROUP;
  var Default$7 = {
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
      this.options = $__default['default'].extend({}, Default$7, _options);
      this.items = [];
    } // Public


    var _proto = SidebarSearch.prototype;

    _proto.init = function init() {
      var _this = this;

      if ($__default['default'](SELECTOR_DATA_WIDGET$1).length == 0) {
        return;
      }

      if ($__default['default'](SELECTOR_DATA_WIDGET$1).next(SELECTOR_SEARCH_RESULTS).length == 0) {
        $__default['default'](SELECTOR_DATA_WIDGET$1).after($__default['default']('<div />', {
          class: CLASS_NAME_SEARCH_RESULTS
        }));
      }

      if ($__default['default'](SELECTOR_SEARCH_RESULTS).children(SELECTOR_SEARCH_LIST_GROUP).length == 0) {
        $__default['default'](SELECTOR_SEARCH_RESULTS).append($__default['default']('<div />', {
          class: CLASS_NAME_LIST_GROUP
        }));
      }

      this._addNotFound();

      $__default['default'](SELECTOR_SIDEBAR$1).children().each(function (i, child) {
        _this._parseItem(child);
      });
    };

    _proto.search = function search() {
      var _this2 = this;

      var searchValue = $__default['default'](SELECTOR_SEARCH_INPUT).val().toLowerCase();

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
          $__default['default'](SELECTOR_SEARCH_RESULTS_GROUP).append(_this2._renderItem(result.name, result.link, result.path));
        });
      }

      this.open();
    };

    _proto.open = function open() {
      $__default['default'](SELECTOR_DATA_WIDGET$1).parent().addClass(CLASS_NAME_OPEN$1);
      $__default['default'](SELECTOR_SEARCH_ICON).removeClass(CLASS_NAME_ICON_SEARCH).addClass(CLASS_NAME_ICON_CLOSE);
    };

    _proto.close = function close() {
      $__default['default'](SELECTOR_DATA_WIDGET$1).parent().removeClass(CLASS_NAME_OPEN$1);
      $__default['default'](SELECTOR_SEARCH_ICON).removeClass(CLASS_NAME_ICON_CLOSE).addClass(CLASS_NAME_ICON_SEARCH);
    };

    _proto.toggle = function toggle() {
      if ($__default['default'](SELECTOR_DATA_WIDGET$1).parent().hasClass(CLASS_NAME_OPEN$1)) {
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

      if (this.options.highlightName || this.options.highlightPath) {
        var searchValue = $__default['default'](SELECTOR_SEARCH_INPUT).val().toLowerCase();
        var regExp = new RegExp(searchValue, 'gi');

        if (this.options.highlightName) {
          name = name.replace(regExp, function (str) {
            return "<b class=\"" + _this4.options.highlightClass + "\">" + str + "</b>";
          });
        }

        if (this.options.highlightPath) {
          path = path.replace(regExp, function (str) {
            return "<b class=\"" + _this4.options.highlightClass + "\">" + str + "</b>";
          });
        }
      }

      return "<a href=\"" + link + "\" class=\"list-group-item\">\n        <div class=\"search-title\">\n          " + name + "\n        </div>\n        <div class=\"search-path\">\n          " + path + "\n        </div>\n      </a>";
    };

    _proto._addNotFound = function _addNotFound() {
      $__default['default'](SELECTOR_SEARCH_RESULTS_GROUP).append(this._renderItem(this.options.notFoundText, '#', []));
    } // Static
    ;

    SidebarSearch._jQueryInterface = function _jQueryInterface(config) {
      var data = $__default['default'](this).data(DATA_KEY$9);

      if (!data) {
        data = $__default['default'](this).data();
      }

      var _options = $__default['default'].extend({}, Default$7, typeof config === 'object' ? config : data);

      var plugin = new SidebarSearch($__default['default'](this), _options);
      $__default['default'](this).data(DATA_KEY$9, typeof config === 'object' ? config : data);

      if (typeof config === 'string' && config.match(/init|toggle|close|open|search/)) {
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
  $__default['default'](document).on('keyup', SELECTOR_SEARCH_INPUT, function (event) {
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

    var timer = 0;
    clearTimeout(timer);
    timer = setTimeout(function () {
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

  $__default['default'].fn[NAME$9] = SidebarSearch._jQueryInterface;
  $__default['default'].fn[NAME$9].Constructor = SidebarSearch;

  $__default['default'].fn[NAME$9].noConflict = function () {
    $__default['default'].fn[NAME$9] = JQUERY_NO_CONFLICT$9;
    return SidebarSearch._jQueryInterface;
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

  var NAME$a = 'Toasts';
  var DATA_KEY$a = 'lte.toasts';
  var EVENT_KEY$6 = "." + DATA_KEY$a;
  var JQUERY_NO_CONFLICT$a = $__default['default'].fn[NAME$a];
  var EVENT_INIT = "init" + EVENT_KEY$6;
  var EVENT_CREATED = "created" + EVENT_KEY$6;
  var EVENT_REMOVED$1 = "removed" + EVENT_KEY$6;
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
  var Default$8 = {
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
          $body.trigger($__default['default'].Event(EVENT_REMOVED$1));
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
        var _options = $__default['default'].extend({}, Default$8, config);

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


  $__default['default'].fn[NAME$a] = Toasts._jQueryInterface;
  $__default['default'].fn[NAME$a].Constructor = Toasts;

  $__default['default'].fn[NAME$a].noConflict = function () {
    $__default['default'].fn[NAME$a] = JQUERY_NO_CONFLICT$a;
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

  var NAME$b = 'TodoList';
  var DATA_KEY$b = 'lte.todolist';
  var JQUERY_NO_CONFLICT$b = $__default['default'].fn[NAME$b];
  var SELECTOR_DATA_TOGGLE$3 = '[data-widget="todo-list"]';
  var CLASS_NAME_TODO_LIST_DONE = 'done';
  var Default$9 = {
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
        var data = $__default['default'](this).data(DATA_KEY$b);

        if (!data) {
          data = $__default['default'](this).data();
        }

        var _options = $__default['default'].extend({}, Default$9, typeof config === 'object' ? config : data);

        var plugin = new TodoList($__default['default'](this), _options);
        $__default['default'](this).data(DATA_KEY$b, typeof config === 'object' ? config : data);

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
    TodoList._jQueryInterface.call($__default['default'](SELECTOR_DATA_TOGGLE$3));
  });
  /**
   * jQuery API
   * ====================================================
   */

  $__default['default'].fn[NAME$b] = TodoList._jQueryInterface;
  $__default['default'].fn[NAME$b].Constructor = TodoList;

  $__default['default'].fn[NAME$b].noConflict = function () {
    $__default['default'].fn[NAME$b] = JQUERY_NO_CONFLICT$b;
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

  var NAME$c = 'Treeview';
  var DATA_KEY$c = 'lte.treeview';
  var EVENT_KEY$7 = "." + DATA_KEY$c;
  var JQUERY_NO_CONFLICT$c = $__default['default'].fn[NAME$c];
  var EVENT_EXPANDED$3 = "expanded" + EVENT_KEY$7;
  var EVENT_COLLAPSED$4 = "collapsed" + EVENT_KEY$7;
  var EVENT_LOAD_DATA_API = "load" + EVENT_KEY$7;
  var SELECTOR_LI = '.nav-item';
  var SELECTOR_LINK = '.nav-link';
  var SELECTOR_TREEVIEW_MENU = '.nav-treeview';
  var SELECTOR_OPEN = '.menu-open';
  var SELECTOR_DATA_WIDGET$2 = '[data-widget="treeview"]';
  var CLASS_NAME_OPEN$2 = 'menu-open';
  var CLASS_NAME_IS_OPENING$1 = 'menu-is-opening';
  var CLASS_NAME_SIDEBAR_COLLAPSED = 'sidebar-collapse';
  var Default$a = {
    trigger: SELECTOR_DATA_WIDGET$2 + " " + SELECTOR_LINK,
    animationSpeed: 300,
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
      $__default['default']("" + SELECTOR_LI + SELECTOR_OPEN + " " + SELECTOR_TREEVIEW_MENU).css('display', 'block');

      this._setupListeners();
    };

    _proto.expand = function expand(treeviewMenu, parentLi) {
      var _this = this;

      var expandedEvent = $__default['default'].Event(EVENT_EXPANDED$3);

      if (this._config.accordion) {
        var openMenuLi = parentLi.siblings(SELECTOR_OPEN).first();
        var openTreeview = openMenuLi.find(SELECTOR_TREEVIEW_MENU).first();
        this.collapse(openTreeview, openMenuLi);
      }

      parentLi.addClass(CLASS_NAME_IS_OPENING$1);
      treeviewMenu.stop().slideDown(this._config.animationSpeed, function () {
        parentLi.addClass(CLASS_NAME_OPEN$2);
        $__default['default'](_this._element).trigger(expandedEvent);
      });

      if (this._config.expandSidebar) {
        this._expandSidebar();
      }
    };

    _proto.collapse = function collapse(treeviewMenu, parentLi) {
      var _this2 = this;

      var collapsedEvent = $__default['default'].Event(EVENT_COLLAPSED$4);
      parentLi.removeClass(CLASS_NAME_IS_OPENING$1 + " " + CLASS_NAME_OPEN$2);
      treeviewMenu.stop().slideUp(this._config.animationSpeed, function () {
        $__default['default'](_this2._element).trigger(collapsedEvent);
        treeviewMenu.find(SELECTOR_OPEN + " > " + SELECTOR_TREEVIEW_MENU).slideUp();
        treeviewMenu.find(SELECTOR_OPEN).removeClass(CLASS_NAME_OPEN$2);
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
      var isOpen = parentLi.hasClass(CLASS_NAME_OPEN$2);

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
        var data = $__default['default'](this).data(DATA_KEY$c);

        var _options = $__default['default'].extend({}, Default$a, $__default['default'](this).data());

        if (!data) {
          data = new Treeview($__default['default'](this), _options);
          $__default['default'](this).data(DATA_KEY$c, data);
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
    $__default['default'](SELECTOR_DATA_WIDGET$2).each(function () {
      Treeview._jQueryInterface.call($__default['default'](this), 'init');
    });
  });
  /**
   * jQuery API
   * ====================================================
   */

  $__default['default'].fn[NAME$c] = Treeview._jQueryInterface;
  $__default['default'].fn[NAME$c].Constructor = Treeview;

  $__default['default'].fn[NAME$c].noConflict = function () {
    $__default['default'].fn[NAME$c] = JQUERY_NO_CONFLICT$c;
    return Treeview._jQueryInterface;
  };

  exports.CardRefresh = CardRefresh;
  exports.CardWidget = CardWidget;
  exports.ControlSidebar = ControlSidebar;
  exports.DirectChat = DirectChat;
  exports.Dropdown = Dropdown;
  exports.ExpandableTable = ExpandableTable;
  exports.Fullscreen = Fullscreen;
  exports.Layout = Layout;
  exports.PushMenu = PushMenu;
  exports.SidebarSearch = SidebarSearch;
  exports.Toasts = Toasts;
  exports.TodoList = TodoList;
  exports.Treeview = Treeview;

  Object.defineProperty(exports, '__esModule', { value: true });

})));
/* globals define exports PNotify BazHelpers */
/*
* @title                    : BazContentDevModeTools
* @description              : Baz Lib for devmode tools for sections/content
* @developer                : guru@bazaari.com.au
* @usage                    : ('#'+ section/componentID).BazContentDevModeTools;
* @functions                :
* @options                  :
*/
(function (global, factory) {
    'use strict';
    typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports) :
    typeof define === 'function' && define.amd ? define(['exports'], factory) :
    (global = global || self, factory(global.BazLibs = {}));
}(this, function (exports) {
    'use strict';

    var BazContentDevModeTools = function ($) {

        var NAME                    = 'BazContentDevModeTools';
        var DATA_KEY                = 'baz.devmodetools';
        // var EVENT_KEY               = "." + DATA_KEY;
        var JQUERY_NO_CONFLICT      = $.fn[NAME];
        // var Event = {
        // };
        // var ClassName = {
        // };
        // var Selector = {
        // };
        var Default = {
        };
        var dataCollection = window['dataCollection'];
        var pnotifySound = new Audio(dataCollection.env.soundPath + 'pnotify.mp3');
        var sectionId,
            componentId;

        var BazContentDevModeTools = function () {
            function BazContentDevModeTools(element, settings) {
                this._element = element;
                this._settings = $.extend({}, Default, settings);

                this._init(this._settings);
                this._initSectionWithFormDevModeTools(this._settings);
            }

            var _proto = BazContentDevModeTools.prototype;

            _proto._error = function(message) {
                throw new Error(message);
            };

            _proto._init = function _init() {
                sectionId = $(this._element)[0].id;
                componentId = $(this._element).parents('.component')[0].id;
            };

            _proto._initSectionWithFormDevModeTools = function _initSectionWithFormDevModeTools() {
                if ($('#' + sectionId).parents().is('#contentModalLink-modal')) {//show cancel button if in modal
                    $('#' + sectionId + '-action-buttons button').each(function() {
                        if (!$('#' + $(this)[0].id).is('#' + sectionId + '-cancel')) {
                            $(this).addClass('d-none');
                        }
                    });
                } else {
                    $('#' + sectionId + '-action-buttons').addClass('d-none');//else hide all action buttons
                }
                $('#' + sectionId).find('[data-bazscantype]').each(function(i,v) {
                    var devModeToolsCheckbox =
                        '<div class="icheck-info">' +
                            '<input class="devModeTools" type="checkbox" id="' + $(v)[0].id + '-devmodetools-value-checkbox"/>' +
                            '<label for="' + $(v)[0].id + '-devmodetools-value-checkbox">Include field data</label>' +
                        '</div>'
                    $(v).parent('.form-group').removeClass('d-none');
                    $(v).attr('disabled', false);
                    $(v).data('bazdevpost', true);
                    if ($(v).data('bazscantype') === 'select2') {
                        var parent = $(v).parent('.form-group');
                        var id = $(v)[0].id;
                        var placeholder = $(v).siblings('label').text();
                        // var placeholder = $(v).siblings('.select2').find('.select2-selection__placeholder').text();
                        // $(v).select2('destroy');
                        $(v).remove();
                        $(parent).prepend(devModeToolsCheckbox);
                        $(parent).append(
                            '<input id="' + id + '" name="' + id + '" placeholder="' + placeholder + '"' +
                            'data-bazpostoncreate="true" data-bazdevpost="true" data-bazpostonupdate="true" data-bazscantype="input" type="text" class="' +
                            'form-control form-control-sm rounded-0"/>'
                            );
                    }
                    if ($(v).data('bazscantype') === 'radio' || $(v).data('bazscantype') === 'checkbox')  {
                        $(v).prepend(devModeToolsCheckbox);
                    } else {
                        $(v).parents('.form-group').prepend(devModeToolsCheckbox);
                    }
                });
                $('#' + sectionId + ' .card-footer').append(
                    '<div class="row">' +
                        '<div class="col" id="' + sectionId + '-devmodetools-test-tools">' +
                            '<h4 class="text-uppercase">Dev Test Tools</h4>' +
                        '</div>' +
                    '</div>'
                );
                $('#' + sectionId + '-devmodetools-test-tools').append(
                    '<div class="row"><div class="col">' +
                    '<div class="form-group">' +
                        '<label class="text-uppercase">Testing Route</label> ' +
                        '<i data-toggle="tooltip" data-html="true" data-placement="top"' +
                        ' title="" class="fa fa-fw fa-question-circle fa-1 helper " data-original-title="' +
                        'Enter testing route">' +
                        '</i>' +
                        '<sup><i data-toggle="tooltip" data-html="true" data-placement="top"' +
                        ' title="" style="font-size: 7px;" class="fa fa-fw fa-star fa-1 helper text-danger" ' +
                        'data-original-title="Required"></i></sup>' +
                        '<input type="text" class="form-control form-control-sm rounded-0" id="' + sectionId + '-devmodetools-route" name="' + sectionId +
                        '-devmodetools-route" placeholder="TESTING ROUTE">' +
                    '</div>' +
                    '<button type="button" class="btn bg-orange m-1" id="' + sectionId + '-devmodetools-get-button">' +
                        '<span class="text-uppercase">Test Get</span>' +
                    '</button>' +
                    '<button type="button" class="btn bg-purple m-1" id="' + sectionId + '-devmodetools-post-button">' +
                        '<span class="text-uppercase">Test Post</span>' +
                    '</button>' +
                    '</div></div><hr>' +
                    '<div class="row"><div class="col">' +
                    '<button type="button" class="btn bg-info m-1" id="' + sectionId + '-devmodetools-component-info">' +
                        '<span class="text-uppercase">Component Info</span>' +
                    '</button><br>' +
                    '<span class="text-danger">NOTE: Running Dev Mode. All fields are enabled and visible.</span><br>' +
                    '<span class="text-info">HOW-TO (Test): <br>' +
                    '1) Click on COMPONENT INFO button to get details regarding this Component, it\'s packages and Input to provide and output to expect during tests.<br>' +
                    '2) Enter route parameter example: account/view OR account/edit. Route information is available in COMPONENT INFO.<br>' +
                    '3) Select checkboxes that are before the fields to sent that fields data to the above given route.<br>' +
                    '4) Click "TEST GET" to test get method and "TEST POST" to test post method' +
                    '</span>' +
                    '</div>' +
                    '</div>'
                );
                $('#' + sectionId + '-devmodetools-get-button').click(function(e) {
                    e.preventDefault();
                    componentId = $(this).parents('.component')[0].id;
                    sectionId = $(this).parents('.sectionWithForm')[0].id;
                    doAjax('get', componentId, sectionId);
                });
                $('#' + sectionId + '-devmodetools-post-button').click(function(e) {
                    e.preventDefault();
                    componentId = $(this).parents('.component')[0].id;
                    sectionId = $(this).parents('.sectionWithForm')[0].id;
                    doAjax('post', componentId, sectionId);
                });
                $('#' + sectionId + '-devmodetools-component-info').click(function() {
                    var currentRouteArr = dataCollection.env.currentRoute.split('/');
                    var componentName, componentXMLUrl;
                    for (var i = 0 ; i < currentRouteArr.length ; i++){
                        currentRouteArr[i] = currentRouteArr[i].charAt(0).toUpperCase() + currentRouteArr[i].substr(1);
                    }
                    currentRouteArr.pop();
                    componentName = currentRouteArr.join('/');
                    componentXMLUrl = dataCollection.env.rootPath + '/application/Dashboard/Install/' + componentName + '/component.xml';
                    $.ajax({
                        url         : componentXMLUrl,
                        method      : 'get',
                        success     : function(componentResponse) {
                                        var packagesName = [];
                                        $.each($(componentResponse).children().children('package').children('name'), function() {
                                            packagesName.push($(this).html());
                                        });
                                        $('#devmodetools-modal .modal-title').addClass('text-uppercase').html('Component Information');
                                        var modalBody =
                                            '<div class="row">' +
                                                '<div class="col">' +
                                                    '<label class="text-bold">Component Name: </label><span> ' + $(componentResponse).children().children("name").html() + '</span><br>' +
                                                    '<label class="text-bold">Component Version: </label><span> ' + $(componentResponse).children().children("version").html() + '</span><br>' +
                                                '</div>' +
                                            '<div class="col">' +
                                                '<label class="text-bold">Component Description: </label><span> ' + $(componentResponse).children().children("description").html() + '</span><br>';

                                        if (packagesName.length > 0) {
                                            modalBody +=
                                                        '<label class="text-bold">Packages: </label><span> ' + packagesName.join(",") + '</span><br>' +
                                                    '</div>' +
                                                '</div>' +
                                                '<div class="row">' +
                                                    '<div class="col">' +
                                                        '<div class="form-group">' +
                                                            '<label class="text-bold">Select Package:</label>' +
                                                            '<select id="' + sectionId + '-devmodetools-packages-select" class="form-control"><option></option></select>' +
                                                        '</div>' +
                                                    '</div>' +
                                                    '<div class="col">' +
                                                        '<div class="form-group">' +
                                                            '<label class="text-bold">Select Package Action:</label>' +
                                                            '<select id="' + sectionId + '-devmodetools-actions-select" class="form-control"></select>' +
                                                        '</div>' +
                                                    '</div>' +
                                                '</div>' +
                                                '<div class="row">' +
                                                    '<div class="col">' +
                                                        '<div id="' + sectionId + '-devmodetools-package-info-data"></div>' +
                                                    '</div>' +
                                                '</div>' +
                                                '<div class="row">' +
                                                    '<div class="col">' +
                                                        '<div id="' + sectionId + '-devmodetools-package-action-data"></div>' +
                                                    '</div>' +
                                                '</div>';
                                        } else {
                                            modalBody +=
                                                        '<label class="text-bold">Packages: </label><span> None</span><br>' +
                                                    '</div>' +
                                                '</div>';
                                        }

                                        $('#devmodetools-modal .modal-body').empty().append(modalBody);
                                        var packagesLocation = { };
                                        $.each(packagesName, function(index, packageName) {
                                            $('#' + sectionId + '-devmodetools-packages-select').append(
                                                '<option value="' + index + '">' + packageName + '</option>'
                                            );
                                            var packageNameArr = packageName.split('\\');
                                            packagesLocation[index] = packageNameArr.join('/');
                                        });

                                        $('#' + sectionId + '-devmodetools-packages-select').change(function() {
                                            getPackageInfo(packagesLocation[$(this).children("option:selected").val()]);
                                            getPackageActionsList($(componentResponse).children().children("name").html(), packagesLocation[$(this).children("option:selected").val()]);
                                        });
                                        $('#devmodetools-modal').modal('show');

                                    },
                        error       : function(data) {
                                        PNotify.removeAll();
                                        PNotify.error({
                                            title   : 'Error',
                                            text    : data.status + ' : ' + data.statusText
                                        });
                                    }
                    });
                });

                function getPackageInfo(packageLocation) {
                    var packageXMLUrl = dataCollection.env.rootPath + '/system/' + packageLocation + '/Install/';
                    $.ajax({
                        url         : packageXMLUrl + 'package.xml',
                        method      : 'get',
                        success     : function(packageResponse) {
                                        $('#' + sectionId + '-devmodetools-package-info-data').empty().append(
                                            '<div class="row">' +
                                                '<div class="col">' +
                                                    '<label class="text-bold">Package Name: </label><span> ' + $(packageResponse).children().children("name").html() + '</span><br>' +
                                                    '<label class="text-bold">Package Version: </label><span> ' + $(packageResponse).children().children("version").html() + '</span><br>' +
                                                '</div>' +
                                                '<div class="col">' +
                                                    '<div class="row">' +
                                                        '<div class="col" id="' + sectionId + '-devmodetools-package-info-dependencies"></div>' +
                                                    '</div>' +
                                                    '<div class="row">' +
                                                        '<div class="col" id="' + sectionId + '-devmodetools-package-testing-route"></div>' +
                                                    '</div>' +
                                                '</div>' +
                                            '</div>'
                                        );
                                        var dependencies = [];
                                        $.each($(packageResponse).children().children('dependency'), function() {
                                            dependencies.push($(this).children('name').html());
                                        });
                                        if (dependencies.length > 0) {
                                            $('#' + sectionId + '-devmodetools-package-info-dependencies').empty().append(
                                                '<label class="text-bold">Package Dependencies: </label><span> ' + dependencies.join(', ') + '</span><br>'
                                            );
                                        } else {
                                            $('#' + sectionId + '-devmodetools-package-info-dependencies').empty().append(
                                                '<label class="text-bold">Package Dependencies: </label><span> None</span><br>'
                                            );
                                        }
                                    },
                        error       : function(data) {
                                        PNotify.removeAll();
                                        PNotify.error({
                                            title   : 'Error',
                                            text    : data.status + ' : ' + data.statusText
                                        });
                                    }
                    });
                }

                function getPackageActionsList(component,packageLocation) {
                    var componentArr = component.split(':');
                    componentArr.splice(0,1); //Remove Dashboard
                    var routePath = componentArr.join('/');
                    var packageXMLUrl = dataCollection.env.rootPath + '/system/' + packageLocation + '/Install/';
                    $('#' + sectionId + '-devmodetools-actions-select').empty();
                    $('#' + sectionId + '-devmodetools-actions-select').append('<option></option>');
                    $('#' + sectionId + '-devmodetools-package-action-data').empty();
                    $.ajax({
                        url         : packageXMLUrl + 'sdk.xml',
                        method      : 'get',
                        success     : function(sdkResponse) {
                                        $.each($(sdkResponse).children().children(), function(index,child) {
                                            $('#' + sectionId + '-devmodetools-actions-select').append(
                                                '<option value="' + index + '">' + child.innerHTML + '</option>'
                                            );
                                        });
                                        $('#' + sectionId + '-devmodetools-actions-select').off();
                                        $('#' + sectionId + '-devmodetools-actions-select').change(function() {
                                            parseSdkData(packageXMLUrl, $(this).children("option:selected").html());
                                            var actionStr = $(this).children("option:selected").html();
                                            var firstSplit = actionStr.split('/');
                                            var secondSplit = firstSplit[1].split('.');
                                            var methodName;
                                            if (secondSplit[0].search('Get') !== -1) {
                                                methodName = 'view';
                                            } else if (secondSplit[0].search('Update') !== -1) {
                                                methodName = 'edit';
                                            } else {
                                                methodName = secondSplit[0].toLowerCase();
                                            }
                                            $('#' + sectionId + '-devmodetools-package-testing-route').empty().append(
                                                '<label class="text-bold">Testing Route: </label><span> ' + routePath + '/' + methodName + '</span><br>'
                                            );
                                        });
                                    },
                        error       : function(data) {
                                        PNotify.removeAll();
                                        PNotify.error({
                                            title   : 'Error',
                                            text    : data.status + ' : ' + data.statusText
                                        });
                                    }
                    });
                }

                function parseSdkData(packageXMLUrl, sdk) {
                    $.ajax({
                        url         : packageXMLUrl + '/' + sdk,
                        method      : 'get',
                        success     : function(sdkResponse) {
                                        $('#' + sectionId + '-devmodetools-package-action-data').empty().append(
                                            '<table id="' + sectionId + '-devmodetools-package-action-data-table" class="table table-striped">' +
                                                '<thead>' +
                                                    '<tr>' +
                                                        '<th>INPUT</th>' +
                                                        '<th>OUTPUT</th>' +
                                                    '</tr>' +
                                                '</thead>' +
                                                '<tbody>' +
                                                '</tbody>' +
                                                '</table>'
                                        );

                                        var row;
                                        $.each($(sdkResponse).children().children('hook'), function() {
                                            row = '<tr>';
                                            row += '<td style="width:50%;">';
                                            $.each($(this).children(), function() {
                                                if ($(this)[0].tagName === 'input') {
                                                    $.each($(this).children(), function() {
                                                        if ($(this)[0].tagName === 'name') {
                                                            row += '<div class="row"><div class="col">' +
                                                                    '<label class="text-bold">Name: </label><span> ' +
                                                                        $(this).html() + '</span>' +
                                                                '</div>';
                                                        } else if ($(this)[0].tagName === 'value') {
                                                            row += '<div class="col">' +
                                                                '<label class="text-bold">Value: </label><span> ' +
                                                                    $(this).html() + '</span>' +
                                                            '</div></div>';
                                                        }
                                                    });
                                                }
                                            });
                                            row += '</td>';
                                            row += '<td style="width:50%;">';
                                            $.each($(this).children(), function() {
                                                if ($(this)[0].tagName === 'output') {
                                                    row += '<label class="text-bold">Status: </label><span> ' + $(this).children().children().html() + '</span>';
                                                    $.each($(this).children().children().children(), function() {
                                                        if ($(this)[0].tagName === 'name') {
                                                            row += '<div class="row"><div class="col">' +
                                                                    '<label class="text-bold">Name: </label><span> ' +
                                                                        $(this).html() + '</span>' +
                                                                '</div>';
                                                        } else if ($(this)[0].tagName === 'value') {
                                                            row += '<div class="col">' +
                                                                '<label class="text-bold">Value: </label><span> ' +
                                                                    $(this).html() + '</span>' +
                                                            '</div></div>';
                                                        }
                                                    });
                                                }
                                            });
                                            row += '</td>';
                                            row += '</tr>';
                                            $('#' + sectionId + '-devmodetools-package-action-data-table tbody').append(row);
                                        });
                                    },
                        error       : function(data) {
                                        PNotify.removeAll();
                                        PNotify.error({
                                            title   : 'Error',
                                            text    : data.status + ' : ' + data.statusText
                                        });
                                    }
                    });
                }

                function doAjax(method, componentId, sectionId) {
                    PNotify.removeAll();
                    var route = $('#' + sectionId + '-devmodetools-route').val().trim();
                    if (route === '') {
                        $('#' + sectionId + '-devmodetools-route').parents('.form-group').children('.help-block').remove();
                        $('#' + sectionId + '-devmodetools-route').parents('.form-group').append(
                            '<span class="help-block text-uppercase text-danger text-xs">Route cannot be empty!</span>'
                        );
                        $('#' + sectionId + '-devmodetools-route').focus(function() {
                            $('#' + sectionId + '-devmodetools-route').parents('.form-group').children('.help-block').remove();
                        });
                    } else {
                        route = dataCollection.env.rootPath + 'index.php?route=' + route;
                        $('#' + sectionId).BazContentSectionWithForm({'task' : 'sectionToObj'});
                        var devData = { };
                        var extractComponentId;
                        var extractComponentIdCount = componentId.split('-').length;
                        if ($('#' + sectionId + ' .devModeTools').is(':checked')) {
                            $('#' + sectionId + ' .devModeTools').each(function(i,devmodetools) {
                                extractComponentId = $(devmodetools)[0].id.split('-', extractComponentIdCount + 1);
                                extractComponentId = extractComponentId[extractComponentId.length - 1];
                                if ($(devmodetools).is(':checked')) {
                                        devData[extractComponentId] =
                                        dataCollection[componentId][sectionId]['data'][extractComponentId];
                                }
                            });
                            var data = $.param(devData);
                            $.ajax({
                                url         : route,
                                data        : data,
                                method      : method,
                                dataType    : 'json',
                                success     : function(response) {
                                                $('#devmodetools-modal .modal-body').empty().append(
                                                '<label>URL: ' + route + '</label><br>' +
                                                '<label>Data Sent: ' + data + '</label><br>' +
                                                '<label>Response: </label><br>'
                                                );
                                                var html = BazHelpers.createHtmlList({obj: response});
                                                $('#devmodetools-modal .modal-title').addClass('text-uppercase').html(method + ' Response');
                                                $('#devmodetools-modal .modal-body').append(html);
                                                $('#devmodetools-modal').modal('show');
                                            },
                                error       : function(data) {
                                                PNotify.removeAll();
                                                PNotify.error({
                                                    title   : 'Error',
                                                    text    : data.status + ' : ' + data.statusText
                                                });
                                            }
                            });
                        } else {
                            PNotify.removeAll();
                            PNotify.error({
                                title: 'Checkbox not selected!',
                                text: 'No include field data checkbox checked',
                                type: 'error'
                            });
                            pnotifySound.play();
                        }
                    }
                }
            };

            BazContentDevModeTools._jQueryInterface = function _jQueryInterface(options) {
                dataCollection = window['dataCollection'];
                componentId = $(this).parents('.component')[0].id;
                sectionId = $(this)[0].id;
                dataCollection[componentId][sectionId]['BazContentDevModeTools'] = $(this).data(DATA_KEY);
                options = $.extend({}, Default, options);

                if (!dataCollection[componentId][sectionId]['BazContentDevModeTools']) {
                    dataCollection[componentId][sectionId]['BazContentDevModeTools'] = new BazContentDevModeTools($(this), options);
                    $(this).data(DATA_KEY, typeof options === 'string' ? 'options need to be an object and not string' : options);
                    dataCollection[componentId][sectionId]['BazContentDevModeTools']._init(options);
                } else {
                    delete dataCollection[componentId][sectionId]['BazContentDevModeTools'];
                    dataCollection[componentId][sectionId]['BazContentDevModeTools'] = new BazContentDevModeTools($(this), options);
                    $(this).data(DATA_KEY, typeof options === 'string' ? 'options need to be an object and not string' : options);
                    dataCollection[componentId][sectionId]['BazContentDevModeTools']._init(options);
                }
            };

        return BazContentDevModeTools;

        }();

    $(document).on('libsLoadComplete bazContentLoaderAjaxComplete bazContentLoaderModalComplete bazContentWizardAjaxComplete', function() {
        var bazDevMode = localStorage.getItem('bazDevMode');
        var sdkButton = '<button class="btn btn-xs bg-maroon text-uppercase mr-2" id="devmodetools-sdk-generator">SDK Generator</button>';
        var serverInfoButton;
        if (window.location.hostname === 'ind-alpha.bazaari.com.au') {
            serverInfoButton =
                '<button class="btn btn-xs btn-danger text-uppercase mr-2" id="devmodetools-open-template-issue">Open Template Issue</button>' +
                '<button class="btn btn-xs btn-info text-uppercase mr-2" id="devmodetools-server-info">Server Info</button>';
        } else {
            serverInfoButton = '';
        }
        if ($('#devModeToolsButtons').length === 0) {
            var devButtons =
                '<div id="devModeToolsButtons" class="float-right">' +
                    sdkButton +
                    serverInfoButton +
                    '<div class="btn-group btn-group-toggle btn-group-xs" data-toggle="buttons">' +
                    '    <label class="btn btn-xs btn-outline-warning" style="cursor: pointer;">' +
                    '        <input type="radio" name="options" id="devon" autocomplete="off" data-value="">DEV ON' +
                    '    </label>' +
                    '    <label class="btn btn-xs btn-outline-success" style="cursor: pointer;">' +
                    '        <input type="radio" name="options" id="devoff" autocomplete="off" data-value="">DEV OFF' +
                    '    </label>' +
                    '</div>' +
                '</div>';
            $('.main-footer').append(devButtons);
        }

        if ($('.devmodetoolsmodal').length > 0) {
            $('.devmodetoolsmodal').each(function() {
                $(this).remove();
                buildModal();
            });
        } else {
            buildModal();
        }

        function buildModal() {
            BazHelpers.modal({
                'modalId'                   : 'devmodetools',
                'modalWidth'                : '90%',
                'modalAdditionalClasses'    : 'devmodetoolsmodal',
                'modalType'                 : 'primary',
                'modalBackdrop'             : 'static',
                'modalEscClose'             : 'true',
                'modalHeader'               : true,
                'modalScrollable'           : true,
                'modalBodyAdditionalClasses': '',
                'modalFooter'               : true,
                'modalButtons'              : {
                    'close'                 : true
                },
            });
        }

        if (bazDevMode === 'true') {
            $('#devon').attr('checked', true);
            $('#devon').parents('label').addClass('active focus');
            if ($('.sectionWithForm').data('bazdevmodetools') === 'false' ||
                $('.sectionWithForm').data('bazdevmodetools') === false) {
                $('.sectionWithForm').before(
                    '<div class="devmodetoolswarning p-2 bg-danger"><h5 class="mb-0">DEV MODE : ON, DEVMODETOOLS : FALSE. CANNOT TEST THIS COMPONENT</h5></div>');
            } else {
                $('.sectionWithForm').before(
                    '<div class="devmodetoolswarning p-2 bg-warning"><h5 class="mb-0">DEV MODE : ON.</h5></div>');
            }
            $('.sectionWithForm').each(function() {
                if ($(this).data('bazdevmodetools') === 'true' ||
                    $(this).data('bazdevmodetools') === true) {
                    BazContentDevModeTools._jQueryInterface.call($(this));
                }
            });
        } else {
            $('#devoff').attr('checked', true);
            $('#devoff').parents('label').addClass('active focus');
            $('.sectionWithForm').each(function() {
                $(this).data('bazdevmodetools', false);
            });
        }

        $('#devon').parents('label').off();
        $('#devon').parents('label').click(function() {
            bazDevMode = true;
            localStorage.setItem('bazDevMode', true);
            window.location.reload(true);
        });
        $('#devoff').parents('label').off();
        $('#devoff').parents('label').click(function() {
            bazDevMode = false;
            localStorage.setItem('bazDevMode', false);
            window.location.reload(true);
        });

        // Server Info Button next to devmodetools toggle switch
        $('#devmodetools-server-info').click(function() {
            $('#devmodetools-modal .modal-title').addClass('text-uppercase').html('Server Information');
            $('#devmodetools-modal .modal-body').empty().append(
                '<div class="row vdivide">' +
                    '<div class="col">' +
                        '<div class="form-group">' +
                            '<label class="text-uppercase">Install Component(s)</label> ' +
                            '<i data-toggle="tooltip" data-html="true" data-placement="right"' +
                            ' title="" class="fa fa-fw fa-question-circle fa-1 helper " data-original-title="' +
                            'Enter component(s) name, separated by comma Example: dashboard:account,dashboard:home">' +
                            '</i>' +
                            '<sup><i data-toggle="tooltip" data-html="true" data-placement="right"' +
                            ' title="" style="font-size: 7px;" class="fa fa-fw fa-star fa-1 helper text-danger" ' +
                            'data-original-title="Required"></i></sup>' +
                            '<input type="text" class="form-control form-control-sm rounded-0" id="devmodetools-server-info-install-params" name="' + sectionId +
                            '-devmodetools-server-info-install-params" placeholder="INSTALL COMPONENT(S)">' +
                        '</div>' +
                        '<button type="button" class="btn bg-orange m-1" id="devmodetools-server-install">' +
                            '<span class="text-uppercase">Install</span>' +
                        '</button>' +
                    '</div>' +
                    '<div class="col">' +
                        '<div class="row">' +
                            '<div class="col">' +
                                '<label class="text-uppercase">Server Update Options</label><br>' +
                                '<div id="server-update-radio" class="btn-group btn-group-toggle btn-group-xs" data-toggle="buttons">' +
                                '    <label class="btn btn-xs btn-outline-primary active focus" style="cursor: pointer;">' +
                                '    <input type="radio" name="options" id="server-update-all" autocomplete="off" data-value="ALL" checked="checked">ALL' +
                                '    </label>' +
                                '    <label class="btn btn-xs btn-outline-primary" style="cursor: pointer;">' +
                                '    <input type="radio" name="options" id="server-update-component" autocomplete="off" data-value="COMPONENT">COMPONENT ONLY' +
                                '    </label>' +
                                '    <label class="btn btn-xs btn-outline-primary" style="cursor: pointer;">' +
                                '    <input type="radio" name="options" id="server-update-template" autocomplete="off" data-value="TEMPLATE">TEMPLATE ONLY' +
                                '    </label>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                        '<div class="row">' +
                            '<div class="col">' +
                                '<button type="button" class="btn bg-purple mt-4" id="devmodetools-server-update">' +
                                    '<span class="text-uppercase">Update Server</span>' +
                                '</button>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div><hr>' +
                '<div class="row text-center">' +
                    '<div class="col">' +
                        '<button type="button" class="btn bg-teal" id="devmodetools-server-install-lastinfo">' +
                            '<span class="text-uppercase">Last Update Info</span>' +
                        '</button>' +
                    '</div>' +
                    '<div class="col">' +
                        '<button type="button" class="btn bg-teal" id="devmodetools-server-current-status">' +
                            '<span class="text-uppercase">Current Status</span>' +
                        '</button>' +
                    '</div>' +
                '</div>' +
                '<div class="row text-center" id="devmodetools-server-loader" hidden>' +
                    '<div class="col">' +
                        '<div class="fa-2x">' +
                            '<i class="fa fa-cog fa-spin"></i> LOADING PLEASE WAIT...' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<div id="devmodetools-server-info-details">' +
                '</div>'
            );

            $('#devmodetools-modal').modal('show');
            $('[data-toggle="tooltip"]').tooltip({container:'body'});
            $('[data-toggle="popover"]').popover({container:'body', trigger: 'focus'});

            function getServerLastInstallInfo() {
                var serverLastInstall = dataCollection.env.rootPath + 'lastinstallwithparams.xml';
                $.ajax({
                    url         : serverLastInstall,
                    method      : 'get',
                    success     : function(serverResponse) {
                                    $('#devmodetools-server-loader').addClass('d-none');
                                    $('#devmodetools-server-info-details').html(
                                        '<div class="row">' +
                                            '<div class="col">' +
                                                '<label class="text-bold">Last Updated on: </label><pre> ' + $(serverResponse).children().children('lastupdate').html().trim() + '</pre>' +
                                            '</div>' +
                                        '</div>' +
                                        '<div class="row">' +
                                            '<div class="col">' +
                                                '<label class="text-bold">Status: </label><br><pre> ' + $(serverResponse).children().children('status').html().trim() + '</pre>' +
                                            '</div>' +
                                        '</div>'
                                    );
                                    enableButtons();
                    },
                    error       : function(data) {
                                    PNotify.removeAll();
                                    PNotify.error({
                                        title   : 'Error:',
                                        text    : data.status + ' : ' + data.statusText
                                    });
                                }
                });
            }

            function getServerCurrentStatus() {
                var currentStatus = dataCollection.env.rootPath + 'currentstatus.php';
                $.ajax({
                    url         : currentStatus,
                    method      : 'get',
                    success     : function() {

                    },
                    error       : function(data) {
                                    PNotify.removeAll();
                                    PNotify.error({
                                        title   : 'Error:',
                                        text    : data.status + ' : ' + data.statusText
                                    });
                    },
                    complete    : function() {
                                    var loadCurrentStatus = dataCollection.env.rootPath + 'currentstatus.xml';
                                    $.ajax({
                                        url         : loadCurrentStatus,
                                        method      : 'get',
                                        success     : function(serverResponse) {
                                                        $('#devmodetools-server-loader').addClass('d-none');
                                                        $('#devmodetools-server-info-details').html(
                                                            '<div class="row">' +
                                                                '<div class="col">' +
                                                                    '<label class="text-bold">Status checked on: </label><pre> ' + $(serverResponse).children().children('lastupdate').html().trim() + '</pre>' +
                                                                '</div>' +
                                                            '</div>' +
                                                            '<div class="row">' +
                                                                '<div class="col">' +
                                                                    '<label class="text-bold">Status: </label><br><pre> ' + $(serverResponse).children().children('status').html().trim() + '</pre>' +
                                                                '</div>' +
                                                            '</div>'
                                                        );
                                                        enableButtons();
                                        },
                                        error       : function(data) {
                                                        PNotify.removeAll();
                                                        PNotify.error({
                                                            title   : 'Error:',
                                                            text    : data.status + ' : ' + data.statusText
                                                        });
                                        },
                                    });
                    }
                });
            }

            function checkIfCanRun(task) {
                var canRun;
                var currentProcessStatus = dataCollection.env.rootPath + 'installrunning.xml';

                $.ajax({
                    url         : currentProcessStatus,
                    method      : 'get',
                    success     : function(serverResponse) {
                                    if ($(serverResponse).children().html() === 'NO') {
                                        canRun = true;
                                    } else if ($(serverResponse).children().html() === 'YES') {
                                        canRun = false;
                                    }

                                    if (canRun) {
                                        disableButtons();
                                        $('#devmodetools-server-info-details').empty();
                                        $('#devmodetools-server-loader').removeClass('d-none');
                                        if (task === 'update') {
                                            var updateParams;
                                            var updateChecked = $('#server-update-radio').find('input[type=radio]:checked').data('value');
                                            if (updateChecked === 'ALL') {
                                                updateParams = {'installWithParams' : ''}
                                            } else if (updateChecked === 'COMPONENT') {
                                                updateParams = {'installWithParams' : 'component-only'}
                                            } else if (updateChecked === 'TEMPLATE') {
                                                updateParams = {'installWithParams' : 'template-only'}
                                            }
                                            runUpdateInstallAjax(updateParams);
                                        } else if (task === 'install') {
                                            var installParams = $('#devmodetools-server-info-install-params').val().trim();
                                            if (installParams === '') {
                                                $('#devmodetools-server-info-install-params').parents('.form-group').children('.help-block').remove();
                                                $('#devmodetools-server-info-install-params').parents('.form-group').append(
                                                    '<span class="help-block text-uppercase text-danger text-xs">Install Parameters cannot be empty!</span>'
                                                );
                                                $('#devmodetools-server-info-install-params').focus(function() {
                                                    $('#devmodetools-server-info-install-params').parents('.form-group').children('.help-block').remove();
                                                });
                                            } else {
                                                $('#devmodetools-server-info-details').empty();
                                                runUpdateInstallAjax({'installWithParams' : 'component=' + installParams});
                                            }
                                        } else if (task === 'getlastinstallinfo') {
                                            getServerLastInstallInfo();
                                        }
                                    } else {
                                        PNotify.removeAll();
                                        PNotify.error({
                                            title   : 'Error:',
                                            text    : 'Cannot execute as another install/update process is currently running. Please try again in couple of minutes.'
                                        });
                                    }
                    },
                    error       : function(data) {
                                    PNotify.removeAll();
                                    PNotify.error({
                                        title   : 'Error:',
                                        text    : data.status + ' : ' + data.statusText
                                    });
                                    canRun = false;
                                }
                });
            }

            function runUpdateInstallAjax(params) {
                $.ajax({
                    url         : dataCollection.env.rootPath + 'installwithparams.php',
                    method      : 'POST',
                    data        : params,
                    success     : function() {
                    },
                    error       : function(data) {
                                    PNotify.removeAll();
                                    PNotify.error({
                                        title   : 'Error:',
                                        text    : data.status + ' : ' + data.statusText
                                    });
                    },
                    complete    : function() {
                                    getServerLastInstallInfo();
                                    enableButtons();
                    }
                });
            }

            function disableButtons() {
                $('#devmodetools-server-update').attr('disabled', true);
                $('#devmodetools-server-install').attr('disabled', true);
                $('#devmodetools-server-install-lastinfo').attr('disabled', true);
                $('#devmodetools-server-current-status').attr('disabled', true);
                $('#server-update-radio label').each(function() {
                    $(this).children('input').attr('disabled', true);
                    $(this).addClass('disabled');
                    $(this).css({'cursor': 'not-allowed'});
                    if ($(this).hasClass('active')) {
                        $(this).addClass('bg-primary');
                    }
                });
            }

            function enableButtons() {
                $('#devmodetools-server-update').attr('disabled', false);
                $('#devmodetools-server-install').attr('disabled', false);
                $('#devmodetools-server-install-lastinfo').attr('disabled', false);
                $('#devmodetools-server-current-status').attr('disabled', false);
                $('#server-update-radio label').each(function() {
                    $(this).children('input').attr('disabled', false);
                    $(this).removeClass('disabled');
                    $(this).css({'cursor': 'pointer'});
                    if ($(this).hasClass('active')) {
                        $(this).removeClass('bg-primary');
                    }
                });
            }

            $('#devmodetools-server-install-lastinfo').click(function() {
                checkIfCanRun('getlastinstallinfo');
            });

            $('#devmodetools-server-current-status').click(function() {
                $('#devmodetools-server-loader').removeClass('d-none');
                getServerCurrentStatus();
            });

            $('#devmodetools-server-update').click(function() {
                checkIfCanRun('update');
            });

            $('#devmodetools-server-install').click(function() {
                checkIfCanRun('install');
            });
        });

        if (window.location.hostname === 'ind-alpha.bazaari.com.au') {
            // Open Template Issue
            $.ajax({
                url         : 'http://projects.bazaari.com.au:8080/s/0cb5c4e1c93476061f285066dd7b68b8-T/l0lk6p/803002/' +
                                'f10222311c901db76695e748d4ff0aab/3.0.10/_/download/batch/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector-embededjs/' +
                                'com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector-embededjs.js?locale=en-AU&collectorId=e551b3b3',
                type        : 'get',
                cache       : true,
                dataType    : 'script'
            });

            window.ATL_JQ_PAGE_PROPS = {
                "triggerFunction": function(showCollectorDialog) {
                    $("#devmodetools-open-template-issue").click(function(e) {
                        e.preventDefault();
                        showCollectorDialog();
                    });
                }
            };
        }

        // SDK Generator Button next to devmodetools toggle switch
        $('#devmodetools-sdk-generator').click(function() {
            $('#devmodetools-modal .modal-title').addClass('text-uppercase').html('Sdk Data Generator');
            $('#devmodetools-modal .modal-body').empty().append(
                '<div class="row">' +
                '    <div class="col">' +
                '        <label style="display:block;">SELECT SDK GENERATOR TYPE</label>' +
                '        <div class="btn-group btn-group-toggle btn-group-sm" data-toggle="buttons">' +
                '            <label class="btn btn-sm  btn-outline-primary active focus" style="cursor: pointer;">' +
                '                <input type="radio" name="options" id="devmodetools-sdk-generator-component-selector" autocomplete="off" data-value="" checked="">COMPONENT' +
                '            </label>' +
                '            <label class="btn btn-sm  btn-outline-primary" style="cursor: pointer;">' +
                '                <input type="radio" name="options" id="devmodetools-sdk-generator-simple-listing-selector" autocomplete="off" data-value="">SIMPLE LISTING' +
                '            </label>' +
                '            <label class="btn btn-sm  btn-outline-primary" style="cursor: pointer;">' +
                '                <input type="radio" name="options" id="devmodetools-sdk-generator-datatable-listing-selector" autocomplete="off" data-value="">DATATABLE LISTING' +
                '            </label>' +
                '        </div>' +
                '    </div>' +
                '</div>' +
                '<hr>' +
                '<div id="devmodetools-sdk-generator-form">' +
                '    <legend>HOOKS</legend>' +
                '    <div class="row">' +
                '        <div class="col">' +
                '            <div class="form-group">' +
                '                <label>METHOD</label>' +
                '                <a style="cursor: pointer;" tabindex="0" data-toggle="popover" data-html="true" data-trigger="focus" title="" data-content="Input data method" class="fa fa-fw fa-question-circle fa-1 helper" data-original-title="METHOD"></a>' +
                '                   <span><sup><i data-toggle="tooltip" data-html="true" data-placement="auto" title="" style="font-size: 7px;" class="fas fa-fw fa-star fa-1 helper text-danger" data-original-title="Required"></i></sup></span>' +
                '                <input data-bazpostoncreate="true" data-bazpostonupdate="true" data-bazscantype="input" type="text" class="form-control rounded-0" id="devmodetools-sdk-generator-method" name="devmodetools-sdk-generator-method" placeholder="METHOD" minlength="1" maxlength="45" value="">' +
                '             </div>' +
                '        </div>' +
                '        <div class="col">' +
                '            <div class="form-group">' +
                '                <label>STATUS</label>' +
                '                <a style="cursor: pointer;" tabindex="0" data-toggle="popover" data-html="true" data-trigger="focus" title="" data-content="Status of ouput data" class="fa fa-fw fa-question-circle fa-1 helper" data-original-title="STATUS"></a>' +
                '                <span>' +
                '                    <sup>' +
                '                        <i data-toggle="tooltip" data-html="true" data-placement="auto" title="" style="font-size: 7px;" class="fas fa-fw fa-star fa-1 helper text-danger" data-original-title="Required"></i>' +
                '                    </sup>' +
                '                </span>' +
                '                <select class="form-control" id="devmodetools-sdk-generator-status" name="devmodetools-sdk-generator-status">' +
                '                    <option data-value="0" value="0"></option>' +
                '                    <option data-value="APIE_NONE" value="1">APIE_NONE</option>' +
                '                    <option data-value="APIE_UNKNOWN" value="2">APIE_UNKNOWN</option>' +
                '                    <option data-value="APIE_INPUT_INVALID_DATA" value="3">APIE_INPUT_INVALID_DATA</option>' +
                '                    <option data-value="APIE_DUPLICATE" value="4">APIE_DUPLICATE</option>' +
                '                    <option data-value="APIE_NOT_FOUND" value="5">APIE_NOT_FOUND</option>' +
                '                    <option data-value="APIE_FOREIGN" value="6">APIE_FOREIGN</option>' +
                '                    <option data-value="APIE_PROCESSING_DATA" value="7">APIE_PROCESSING_DATA</option>' +
                '                    <option data-value="APIE_UNAUTHORIZED" value="8">APIE_UNAUTHORIZED</option>' +
                '                    <option data-value="APIE_UNSUPPORTED" value="9">APIE_UNSUPPORTED</option>' +
                '                    <option data-value="APIE_MUTEX" value="10">APIE_MUTEX</option>' +
                '                    <option data-value="APIE_REFERENCE_ERROR" value="11">APIE_REFERENCE_ERROR</option>' +
                '                </select>' +
                '            </div>' +
                '        </div>' +
                '    </div>' +
                '    <div class="row">' +
                '        <div class="col" id="devmodetools-sdk-generator-input-col">' +
                '            <div class="form-group">' +
                '                <label>INPUT DATA</label>' +
                '                <a style="cursor: pointer;" tabindex="0" data-toggle="popover" data-html="true" data-trigger="focus" title="" data-content="1 entry per line. Defined as key=value, Example:id=1 or name=user" class="fa fa-fw fa-question-circle fa-1 helper" data-original-title="INPUT DATA"></a>' +
                '                <span><sup><i data-toggle="tooltip" data-html="true" data-placement="auto" title="" style="font-size: 7px;" class="fas fa-fw fa-star fa-1 helper text-danger" data-original-title="Required"></i></sup></span>' +
                '                <textarea data-bazpostoncreate="true" data-bazpostonupdate="true" data-bazscantype="input" class="form-control rounded-0" id="devmodetools-sdk-generator-input" placeholder="INPUT DATA" value="" rows="4"></textarea>' +
                '            </div>' +
                '        </div>' +
                '        <div class="col" id="devmodetools-sdk-generator-output-col">' +
                '            <div class="form-group">' +
                '                <label>OUTPUT DATA</label>' +
                '                <a style="cursor: pointer;" tabindex="0" data-toggle="popover" data-html="true" data-trigger="focus" title="" data-content="1 entry per line. Defined as key=value, Example: id=1 or name=user. For Listing each row data is separated by a colon (:). Example: id=1:id=2"  class="fa fa-fw fa-question-circle fa-1 helper" data-original-title="OUTPUT DATA"></a>' +
                '                <span><sup><i data-toggle="tooltip" data-html="true" data-placement="auto" title="" style="font-size: 7px;" class="fas fa-fw fa-star fa-1 helper text-danger" data-original-title="Required"></i></sup></span>' +
                '                <textarea data-bazpostoncreate="true" data-bazpostonupdate="true" data-bazscantype="input" class="form-control rounded-0" id="devmodetools-sdk-generator-output" placeholder="OUTPUT DATA" value="" rows="4"></textarea>' +
                '            </div>' +
                '        </div>' +
                '    </div>' +
                '    <div class="row">' +
                '        <div class="col">' +
                '            <div class="form-group">' +
                '                <label>HOOK NAME</label>' +
                '                <a style="cursor: pointer;" tabindex="0" data-toggle="popover" data-html="true" data-trigger="focus" title="" data-content="Name of the hook, only for this tool. It will not be copied to SDK" class="fa fa-fw fa-question-circle fa-1 helper" data-original-title="HOOK NAME"></a>' +
                '                   <span><sup><i data-toggle="tooltip" data-html="true" data-placement="auto" title="" style="font-size: 7px;" class="fas fa-fw fa-star fa-1 helper text-danger" data-original-title="Required"></i></sup></span>' +
                '                <input data-bazpostoncreate="true" data-bazpostonupdate="true" data-bazscantype="input" type="text" class="form-control rounded-0" id="devmodetools-sdk-generator-hook-name" name="devmodetools-sdk-generator-hook-name" placeholder="HOOK NAME" minlength="1" maxlength="45" value="">' +
                '            </div>' +
                '        </div>' +
                '        <div class="col">' +
                '            <div class="form-group">' +
                '                <label>AVAILABLE HOOKS</label>' +
                '                <a style="cursor: pointer;" tabindex="0" data-toggle="popover" data-html="true" data-trigger="focus" title="" data-content="Hooks that are added" class="fa fa-fw fa-question-circle fa-1 helper" data-original-title="AVAILABLE HOOKS"></a>' +
                '                <select data-bazpostoncreate="true" data-bazpostonupdate="true" data-bazscantype="select2" class="form-control" id="devmodetools-sdk-generator-hooks" name="devmodetools-sdk-generator-hooks" style="width:100%;">' +
                '                    <option></option>' +
                '                </select>' +
                '            </div>' +
                '        </div>' +
                '    </div>' +
                '    <div class="row">' +
                '        <div class="col">' +
                '            <button type="button" class="btn btn-sm btn-orange float-left text-white bg-purple" id="devmodetools-sdk-generator-add-update-hook">' +
                '                <i class="fas fa-fw fa-candy-cane"></i> ADD HOOK' +
                '            </button>' +
                '        </div>' +
                '        <div class="col">' +
                '            <button type="button" class="btn btn-sm btn-warning mr-1 float-left text-white" id="devmodetools-sdk-generator-edit-hook" disabled="">' +
                '                <i class="fas fa-fw fa-edit"></i> EDIT HOOK' +
                '            </button>' +
                '            <button type="button" class="btn btn-sm btn-info mr-1 float-left text-white" id="devmodetools-sdk-generator-copy-hook" disabled="">' +
                '                <i class="fas fa-fw fa-copy"></i> COPY HOOK' +
                '            </button>' +
                '            <button type="button" class="btn btn-sm btn-danger mr-1 float-right" id="devmodetools-sdk-generator-delete-hook" disabled="">' +
                '                <i class="fas fa-fw fa-trash"></i> DELETE HOOK' +
                '            </button>' +
                '        </div>' +
                '    </div>' +
                '    <hr>' +
                '    <div class="row">' +
                '        <div class="col">' +
                '            <button type="button" class="btn btn-sm btn-primary text-white float-right bg-maroon" id="devmodetools-sdk-generator-generate-sdk" disabled="">' +
                '                <i class="fas fa-fw fa-magic"></i> GENERATE COMPONENT SDK' +
                '            </button>' +
                '        </div>' +
                '    </div>' +
                '</div>' +
                '<hr>' +
                '<div class="row">' +
                '    <div class="col">' +
                '        <div class="form-group">' +
                '            <label>SDK OUTPUT</label>' +
                '            <textarea data-bazpostoncreate="true" data-bazpostonupdate="true" data-bazscantype="input" class="form-control rounded-0" id="devmodetools-sdk-generator-sdk-output" placeholder="SDK OUTPUT" value="" rows="4"></textarea>' +
                '        </div>' +
                '    </div>' +
                '</div>'
            );

            window['dataCollection']['devmodetools-sdk-generator'] = { };
            window['dataCollection']['devmodetools-sdk-generator']['component'] = { };
            window['dataCollection']['devmodetools-sdk-generator']['component']['hooks'] = { };
            window['dataCollection']['devmodetools-sdk-generator']['listing'] = { };
            window['dataCollection']['devmodetools-sdk-generator']['listing']['hooks'] = { };
            window['dataCollection']['devmodetools-sdk-generator']['datatable'] = { };
            window['dataCollection']['devmodetools-sdk-generator']['datatable']['hooks'] = { };
            var hooksData = window['dataCollection']['devmodetools-sdk-generator']['component']['hooks'];
            var generatorType = 'component';

            $('#devmodetools-modal').modal('show');
            $('[data-toggle="tooltip"]').tooltip({container:'body'});
            $('[data-toggle="popover"]').popover({container:'body', trigger: 'focus'});

            function addUpdateHook(id) {
                var enteredMethod = $('#devmodetools-sdk-generator-method').val();
                if ($('#devmodetools-sdk-generator-component-selector').is('input:checked')) {
                    generatorType = 'component';
                } else if ($('#devmodetools-sdk-generator-simple-listing-selector').is('input:checked')) {
                    generatorType = 'listing';
                } else if ($('#devmodetools-sdk-generator-datatable-listing-selector').is('input:checked')) {
                    generatorType = 'datatable';
                }
                if (!enteredMethod) {
                    $('#devmodetools-sdk-generator-method').parents('.form-group').children('.help-block').remove();
                    $('#devmodetools-sdk-generator-method').parents('.form-group').append(
                        '<span class="help-block text-uppercase text-danger text-xs">Method cannot be empty!</span>'
                    );
                    $('#devmodetools-sdk-generator-method').focus(function() {
                        $('#devmodetools-sdk-generator-method').parents('.form-group').children('.help-block').remove();
                    });
                }

                if (generatorType === 'component' || generatorType === 'datatable') {
                    var enteredInput = $('#devmodetools-sdk-generator-input').val();
                    if (enteredInput) {
                        var enteredInputArr = enteredInput.split('\n');
                        var inputArr = [];
                        $.each(enteredInputArr, function(index,arr) {
                            var enteredInputArrObject = { };
                            var array = arr.split('=');
                            enteredInputArrObject['name'] = array[0];
                            enteredInputArrObject['value'] = array[1];
                            inputArr.push(enteredInputArrObject);
                        });
                    } else {
                        $('#devmodetools-sdk-generator-input').parents('.form-group').children('.help-block').remove();
                        $('#devmodetools-sdk-generator-input').parents('.form-group').append(
                            '<span class="help-block text-uppercase text-danger text-xs">Input data cannot be empty!</span>'
                        );
                        $('#devmodetools-sdk-generator-input').focus(function() {
                            $('#devmodetools-sdk-generator-input').parents('.form-group').children('.help-block').remove();
                        });
                    }
                }

                // if (generatorType === 'component' || generatorType === 'listing') {
                    var enteredOutput = $('#devmodetools-sdk-generator-output').val();

                    var outputArr;
                    if (generatorType === 'component') {
                        if (enteredOutput) {
                            var enteredOutputArr = enteredOutput.split('\n');
                            outputArr = [];
                            $.each(enteredOutputArr, function(index,arr) {
                                var enteredOutputArrObject = { };
                                var array = arr.split('=');
                                enteredOutputArrObject['name'] = array[0];
                                enteredOutputArrObject['value'] = array[1];
                                outputArr.push(enteredOutputArrObject);
                            });
                        } else {
                            $('#devmodetools-sdk-generator-output').parents('.form-group').children('.help-block').remove();
                            $('#devmodetools-sdk-generator-output').parents('.form-group').append(
                                '<span class="help-block text-uppercase text-danger text-xs">Output data cannot be empty!</span>'
                            );
                            $('#devmodetools-sdk-generator-output').focus(function() {
                                $('#devmodetools-sdk-generator-output').parents('.form-group').children('.help-block').remove();
                            });
                        }
                    } else if (generatorType === 'listing' || generatorType === 'datatable') {
                        if (enteredOutput) {
                            var rows = enteredOutput.split(':');
                            var columns = [];
                            $.each(rows, function(index,row) {
                                var eachColumn = row.split('\n');
                                var column = [];
                                $.each(eachColumn, function(index,arr) {
                                    if (arr !== '') {
                                        column.push(arr);
                                    }
                                });
                                columns.push(column);
                            });
                            outputArr = [];
                            $.each(columns, function(index,column) {
                                var outputColumnArr = [];
                                $.each(column, function(index,field){
                                    var enteredOutputArrObject = { };
                                    var array = field.split('=');
                                    enteredOutputArrObject['name'] = array[0];
                                    enteredOutputArrObject['value'] = array[1];
                                    outputColumnArr.push(enteredOutputArrObject);
                                });
                                outputArr.push(outputColumnArr);
                            });
                        } else {
                            $('#devmodetools-sdk-generator-output').parents('.form-group').children('.help-block').remove();
                            $('#devmodetools-sdk-generator-output').parents('.form-group').append(
                                '<span class="help-block text-uppercase text-danger text-xs">Output data cannot be empty!</span>'
                            );
                            $('#devmodetools-sdk-generator-output').focus(function() {
                                $('#devmodetools-sdk-generator-output').parents('.form-group').children('.help-block').remove();
                            });
                        }
                    }

                var statusVal = $('#devmodetools-sdk-generator-status').val();
                var statusValue = $('#devmodetools-sdk-generator-status').children("option:selected").data('value');
                if (statusVal === '0') {
                    $('#devmodetools-sdk-generator-status').parents('.form-group').children('.help-block').remove();
                    $('#devmodetools-sdk-generator-status').parents('.form-group').append(
                        '<span class="help-block text-uppercase text-danger text-xs">Please select status!</span>'
                    );
                    $('#devmodetools-sdk-generator-status').change(function() {
                        $('#devmodetools-sdk-generator-status').parents('.form-group').children('.help-block').remove();
                    });
                }

                var hookName = $('#devmodetools-sdk-generator-hook-name').val();
                if (!hookName) {
                    $('#devmodetools-sdk-generator-hook-name').parents('.form-group').children('.help-block').remove();
                    $('#devmodetools-sdk-generator-hook-name').parents('.form-group').append(
                        '<span class="help-block text-uppercase text-danger text-xs">Hook name cannot be empty!</span>'
                    );
                    $('#devmodetools-sdk-generator-hook-name').focus(function() {
                        $('#devmodetools-sdk-generator-hook-name').parents('.form-group').children('.help-block').remove();
                    });
                }

                if ($('#devmodetools-sdk-generator-form').find('.help-block').length === 0) {
                    hooksData[hookName] = { };
                    hooksData[hookName]['hookName'] = hookName;
                    hooksData[hookName]['enteredMethod'] = enteredMethod;
                    if (generatorType === 'component' || generatorType === 'datatable') {
                        hooksData[hookName]['inputArr'] = inputArr;
                    }
                    hooksData[hookName]['outputArr'] = outputArr;

                    hooksData[hookName]['statusVal'] = statusVal;
                    hooksData[hookName]['statusValue'] = statusValue;

                    var data = {
                        id: hookName,
                        text: hookName
                    };
                    if ($('#devmodetools-sdk-generator-hooks').find("option[value='" + data.id + "']").length) {
                        $('#devmodetools-sdk-generator-hooks').val('').trigger('change');
                    } else {
                        var newOption = new Option(data.text, data.id, false, false);
                        $('#devmodetools-sdk-generator-hooks').append(newOption).trigger('change');
                    }

                    $('#devmodetools-sdk-generator-method').val('');
                    $('#devmodetools-sdk-generator-status').val('').trigger('change');
                    $('#devmodetools-sdk-generator-input').val('');
                    $('#devmodetools-sdk-generator-output').val('');
                    $('#devmodetools-sdk-generator-hook-name').val('');
                    $('#devmodetools-sdk-generator-hooks').val('').trigger('change');
                    $('#devmodetools-sdk-generator-generate-sdk').attr('disabled', false);

                    if (id === 'devmodetools-sdk-generator-edit-hook') {
                        $('#devmodetools-sdk-generator-add-update-hook').attr('disabled', false);
                        $('#devmodetools-sdk-generator-edit-hook').attr('disabled', true);
                        $('#devmodetools-sdk-generator-copy-hook').attr('disabled', true);
                        $('#devmodetools-sdk-generator-delete-hook').attr('disabled', true);
                    }

                    PNotify.removeAll();
                    PNotify.success({
                        title: 'Hook Added',
                        text: 'Now you can select hook to edit/delete it',
                    });
                }
            }

            function editCopyHook(id) {
                var selected = $('#devmodetools-sdk-generator-hooks').children("option:selected").val();
                if (id === 'devmodetools-sdk-generator-copy-hook') {
                    $('#devmodetools-sdk-generator-hook-name').val(hooksData[selected]['hookName'] + ' (COPY)');
                } else if (id === 'devmodetools-sdk-generator-edit-hook') {
                    $('#devmodetools-sdk-generator-hook-name').val(hooksData[selected]['hookName']);
                }
                $('#devmodetools-sdk-generator-method').val(hooksData[selected]['enteredMethod']);
                $('#devmodetools-sdk-generator-status').val(hooksData[selected]['statusVal']).trigger('change');

                if (generatorType === 'component' || generatorType === 'datatable') {
                    var rebuildInputArr = [];
                    $.each(hooksData[selected]['inputArr'], function() {
                        rebuildInputArr.push(this.name + '=' + this.value);
                    });
                    $('#devmodetools-sdk-generator-input').val(rebuildInputArr.join('\n'));

                    var rebuildOutputArr = [];
                    $.each(hooksData[selected]['outputArr'], function() {
                        rebuildOutputArr.push(this.name + '=' + this.value);
                    });
                    $('#devmodetools-sdk-generator-output').val(rebuildOutputArr.join('\n'));
                }
                if (generatorType === 'listing' || generatorType === 'datatable') {
                    var rebuildOutputListingArr = [];
                    $.each(hooksData[selected]['outputArr'], function(index,row) {
                        var rowData = [];
                        $.each(row, function() {
                            rowData.push(this.name + '=' + this.value);
                        });
                        rebuildOutputListingArr.push(rowData.join('\n'));
                    });
                    $('#devmodetools-sdk-generator-output').val(rebuildOutputListingArr.join('\n:\n'));
                }

                $('#devmodetools-sdk-generator-add-update-hook').attr('disabled', false);
            }

            function deleteHook() {
                var selected = $('#devmodetools-sdk-generator-hooks').children("option:selected").val();
                if (selected !== '') {
                    $('#devmodetools-sdk-generator-method').val('');
                    $('#devmodetools-sdk-generator-status').val('').trigger('change');
                    $('#devmodetools-sdk-generator-input').val('');
                    $('#devmodetools-sdk-generator-output').val('');
                    $('#devmodetools-sdk-generator-hook-name').val('');
                    $('#devmodetools-sdk-generator-hooks option[value="' + selected + '"]').remove().trigger('change');
                    delete hooksData[selected];
                    $('#devmodetools-sdk-generator-sdk-output').val('Hook deleted, please regenerate SDK');
                    if ($('#devmodetools-sdk-generator-hooks option').length === 1) {
                        $('#devmodetools-sdk-generator-generate-sdk').attr('disabled', true);
                    }
                    $('#devmodetools-sdk-generator-add-update-hook').attr('disabled', false);
                    $('#devmodetools-sdk-generator-edit-hook').attr('disabled', true);
                    $('#devmodetools-sdk-generator-copy-hook').attr('disabled', true);
                    $('#devmodetools-sdk-generator-delete-hook').attr('disabled', true);
                }
            }

            function objToXML(objectValue) {

                var xml = '';
                xml +=
                '   <hook>\n' +
                '       <component>APPLICATION</component>\n' +
                '       <method>' + objectValue.enteredMethod + '</method>\n';

                if (generatorType === 'component') {
                    for (var input in objectValue.inputArr) {
                        xml +=
                            '       <input>\n' +
                            '            <name>' + objectValue.inputArr[input]['name'] + '</name>\n' +
                            '            <value>' + objectValue.inputArr[input]['value'] + '</value>\n' +
                            '       </input>\n';
                    }

                    xml +=
                        '       <output>\n' +
                        '           <api>\n' +
                        '               <status>' + objectValue.statusValue + '</status>\n';


                    for (var output in objectValue.outputArr) {
                        xml +=
                            '               <array>\n' +
                            '                   <name>' + objectValue.outputArr[output]['name'] + '</name>\n' +
                            '                   <value>' + objectValue.outputArr[output]['value'] + '</value>\n' +
                            '               </array>\n';
                    }

                    xml +=
                        '           </api>\n' +
                        '       </output>\n' +
                        '   </hook>\n';
                }

                if (generatorType === 'listing') {
                    xml +=
                        '       <output>\n' +
                        '           <api>\n' +
                        '               <status>' + objectValue.statusValue + '</status>\n';

                    for (var rows in objectValue.outputArr) {
                        xml +=
                            '               <array>\n';
                            for (var row in objectValue.outputArr[rows]) {
                                xml +=
                                    '                   <array>\n' +
                                    '                       <name>' + objectValue.outputArr[rows][row]['name'] + '</name>\n' +
                                    '                       <value>' + objectValue.outputArr[rows][row]['value'] + '</value>\n' +
                                    '                   </array>\n';
                            }
                        xml +=
                            '               </array>\n';
                    }

                    xml +=
                        '           </api>\n' +
                        '       </output>\n' +
                        '   </hook>\n';
                }

                if (generatorType === 'datatable') {
                    for (var datatableInput in objectValue.inputArr) {
                        xml +=
                            '       <input>\n' +
                            '            <name>' + objectValue.inputArr[datatableInput]['name'] + '</name>\n' +
                            '            <value>' + objectValue.inputArr[datatableInput]['value'] + '</value>\n' +
                            '       </input>\n';
                    }

                    xml +=
                        '       <output>\n' +
                        '           <api>\n' +
                        '               <status>' + objectValue.statusValue + '</status>\n' +
                        '               <listingdata>\n';

                    for (var datatableRows in objectValue.outputArr) {
                        xml +=
                            '                   <rows>\n';
                            for (var datatableRow in objectValue.outputArr[datatableRows]) {
                                xml +=
                                    '                       <array>\n' +
                                    '                           <name>' + objectValue.outputArr[datatableRows][datatableRow]['name'] + '</name>\n' +
                                    '                           <value>' + objectValue.outputArr[datatableRows][datatableRow]['value'] + '</value>\n' +
                                    '                       </array>\n';
                            }
                        xml +=
                            '                   </rows>\n';
                    }

                    xml +=
                        '                    <pagination>\n' +
                        '                        <next>\n' +
                        '                            <id>ENTER YOUR PAGINATION NEXT ID</id>\n' +
                        '                        </next>\n' +
                        '                        <prev>\n' +
                        '                            <id>ENTER YOUR PAGNIATION PREV ID</id>\n' +
                        '                        </prev>\n' +
                        '                    </pagination>\n' +
                        '                </listingdata>\n' +
                        '           </api>\n' +
                        '       </output>\n' +
                        '   </hook>\n';
                }
                return xml;
            }

            $('#devmodetools-sdk-generator-hooks').change(function() {
                var selected = $(this).children("option:selected").val();
                if (selected !== '') {
                    $('#devmodetools-sdk-generator-add-update-hook').attr('disabled', true);
                    $('#devmodetools-sdk-generator-edit-hook').attr('disabled', false);
                    $('#devmodetools-sdk-generator-copy-hook').attr('disabled', false);
                    $('#devmodetools-sdk-generator-delete-hook').attr('disabled', false);
                } else {
                    $('#devmodetools-sdk-generator-edit-hook').attr('disabled', true);
                    $('#devmodetools-sdk-generator-copy-hook').attr('disabled', true);
                    $('#devmodetools-sdk-generator-delete-hook').attr('disabled', true);
                }
            });

            //Component Click
            $('#devmodetools-sdk-generator-component-selector').parent().click(function() {
                $('#devmodetools-sdk-generator-generate-sdk').html('<i class="fas fa-fw fa-magic"></i> GENERATE COMPONENT SDK');
                hooksData = window['dataCollection']['devmodetools-sdk-generator']['component']['hooks'];
                $('#devmodetools-sdk-generator-input-col').removeClass('d-none');
                $('#devmodetools-sdk-generator-input').attr('disabled', false);
                $('#devmodetools-sdk-generator-input').val('');
                $('#devmodetools-sdk-generator-output-col').removeClass('d-none');
                $('#devmodetools-sdk-generator-output').attr('disabled', false);
                $('#devmodetools-sdk-generator-status').val('0').attr('disabled', false);
                $('#devmodetools-sdk-generator-form').find('.help-block').remove();
            });
            // Simple Listing Click
            $('#devmodetools-sdk-generator-simple-listing-selector').parent().click(function() {
                $('#devmodetools-sdk-generator-generate-sdk').html('<i class="fas fa-fw fa-magic"></i> GENERATE SIMPLE LISTING SDK');
                hooksData = window['dataCollection']['devmodetools-sdk-generator']['listing']['hooks'];
                $('#devmodetools-sdk-generator-input-col').removeClass('d-none');
                $('#devmodetools-sdk-generator-input').attr('disabled', true);
                $('#devmodetools-sdk-generator-input').val('');
                $('#devmodetools-sdk-generator-output-col').removeClass('d-none');
                $('#devmodetools-sdk-generator-output').attr('disabled', false);
                $('#devmodetools-sdk-generator-form').find('.help-block').remove();
            });
            // Datatable Listing Click
            $('#devmodetools-sdk-generator-datatable-listing-selector').parent().click(function() {
                $('#devmodetools-sdk-generator-generate-sdk').html('<i class="fas fa-fw fa-magic"></i> GENERATE DATATABLE LISTING SDK');
                hooksData = window['dataCollection']['devmodetools-sdk-generator']['datatable']['hooks'];
                $('#devmodetools-sdk-generator-input-col').removeClass('d-none');
                $('#devmodetools-sdk-generator-input').attr('disabled', false);
                $('#devmodetools-sdk-generator-input').val('operation=load\nresults=20');
                $('#devmodetools-sdk-generator-output-col').removeClass('d-none');
                $('#devmodetools-sdk-generator-output').attr('disabled', false);
                $('#devmodetools-sdk-generator-form').find('.help-block').remove();
            });

            $('#devmodetools-sdk-generator-add-update-hook').click(function() {
                $('#devmodetools-sdk-generator-add-update-hook').html('<i class="fas fa-fw fa-candy-cane"></i> ADD HOOK');
                addUpdateHook($(this)[0].id);
                $('#devmodetools-sdk-generator-sdk-output').val('Hook Added/Updated, please regenerate SDK');
            });

            $('#devmodetools-sdk-generator-edit-hook').click(function() {
                $('#devmodetools-sdk-generator-add-update-hook').html('<i class="fas fa-fw fa-candy-cane"></i> UPDATE HOOK');
                $('#devmodetools-sdk-generator-copy-hook').attr('disabled', true);
                editCopyHook($(this)[0].id);
            });

            $('#devmodetools-sdk-generator-copy-hook').click(function() {
                $('#devmodetools-sdk-generator-edit-hook').attr('disabled', true);
                editCopyHook($(this)[0].id);
            });

            $('#devmodetools-sdk-generator-delete-hook').click(function() {
                deleteHook();
            });

            $('#devmodetools-sdk-generator-generate-sdk').click(function() {
                var sdk =
                        '<?xml version="1.0" encoding="UTF-8"?>\n' +
                        '<sdk>\n';

                if (generatorType === 'datatable') {
                    sdk +=
                    '   <hook>\n' +
                    '       <component>APPLICATION</component>\n' +
                    '       <method>ENTER YOUR LISTING METHOD NAME</method>\n' +
                    '        <output>\n' +
                    '            <api>\n' +
                    '                <status>APIE_NONE</status>\n' +
                    '                <listing>\n' +
                    '                    <debug>1</debug>\n' +
                    '                    <ajax>\n' +
                    '                        <url>test</url>\n' +
                    '                    </ajax>\n' +
                    '                    <style>\n' +
                    '                        <table>baztable</table>\n' +
                    '                    </style>\n' +
                    '                    <html>\n' +
                    '                        <empty>\n' +
                    '                            <html><![CDATA[<center>No data</center>]]></html>\n' +
                    '                        </empty>\n' +
                    '                    </html>\n' +
                    '                    <refs>id</refs>\n' +
                    '                    <row>\n' +
                    '                        <checkbox>0</checkbox>\n' +
                    '                        <control>0</control>\n' +
                    '                    </row>\n' +
                    '                    <pagination>\n' +
                    '                        <show>1</show>\n' +
                    '                    </pagination>\n' +
                    '                    <runtime>\n' +
                    '                        <columns>\n' +
                    '                            <name>username</name>\n' +
                    '                            <value>username</value>\n' +
                    '                            <style>\n' +
                    '                                <width>40%</width>\n' +
                    '                                <responsive>\n' +
                    '                                    <xs>true</xs>\n' +
                    '                                </responsive>\n' +
                    '                            </style>\n' +
                    '                            <sort>\n' +
                    '                                <enabled>0</enabled>\n' +
                    '                            </sort>\n' +
                    '                        </columns>\n' +
                    '                        <columns>\n' +
                    '                            <name>status</name>\n' +
                    '                            <value>status</value>\n' +
                    '                            <style>\n' +
                    '                                <width>40%</width>\n' +
                    '                                <responsive>\n' +
                    '                                    <xs>true</xs>\n' +
                    '                                </responsive>\n' +
                    '                            </style>\n' +
                    '                            <sort>\n' +
                    '                                <enabled>0</enabled>\n' +
                    '                            </sort>\n' +
                    '                        </columns>\n' +
                    '                    </runtime>\n' +
                    '                </listing>\n' +
                    '            </api>\n' +
                    '        </output>\n' +
                    '    </hook>\n';
                }

                // Loop through hooks
                for (var hook in hooksData) {
                    sdk += objToXML(hooksData[hook]);
                }
                sdk += '</sdk>';
                $('#devmodetools-sdk-generator-sdk-output').val(sdk);
            });
        });
    });

    $.fn[NAME] = BazContentDevModeTools._jQueryInterface;
    $.fn[NAME].Constructor = BazContentDevModeTools;

    $.fn[NAME].noConflict = function () {
        $.fn[NAME] = JQUERY_NO_CONFLICT;
        return BazContentDevModeTools._jQueryInterface;
    };

    return BazContentDevModeTools;
}(jQuery);

exports.BazContentDevModeTools = BazContentDevModeTools;

Object.defineProperty(exports, '__esModule', { value: true });

}));
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

var _extends = Object.assign || function (target) { 'use strict'; for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
// var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var BazHelpers = function() {
    'use strict';
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
                output += '<li class="text-uppercase">' + k + ' => ';
                output += bazCreateHtmlList(obj[k]);
                output += '</li>';
            } else {
                output += '<li>' + k + ' => ' + obj[k] + '</li>'; 
            }
        });
        output += '</ul>';
        return output;
    }    

    function bazHelpersConstructor() {
        // if something needs to be constructed
        return null;
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
    }

    setup(bazHelpersConstructor);

    return bazHelpersConstructor;
}();
/* exported BazContentSection */
/* globals */
/*
* @title                    : BazContentSection
* @description              : Baz Core Lib
* @developer                : guru@bazaari.com.au
* @usage                    : BazContentSection._function_(_options_);
* @functions                : BazHeader, BazFooter, BazUpdateBreadcrumb
* @options                  :
*/

var _extends = Object.assign || function (target) { 'use strict'; for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
// var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var BazContentSection = function() {
    'use strict';
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
        $('#' + sectionId).BazContentFields();
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
    'use strict';
    if ($('.section').length > 0) {
        $('.section').each(function() {
            BazContentSection.init({'sectionId' : $(this)[0].id});
        });
    }
});

/* globals define exports BazContentFieldsValidator PNotify Pace BazCore BazContentLoader */
/*
* @title                    : BazContentSectionWithForm
* @description              : Baz Lib for Content (Sections With Form)
* @developer                : guru@bazaari.com.au
* @usage                    : ('#'+ sectionId).BazContentSectionWithForm;
* @functions                :
* @options                  :
*/
(function (global, factory) {
    'use strict';
    typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports) :
    typeof define === 'function' && define.amd ? define(['exports'], factory) :
    (global = global || self, factory(global.BazLibs = {}));
}(this, function (exports) {
    'use strict';

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
            thatV;

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

                $(this._element).BazContentFields();
                BazContentFieldsValidator.initValidator({
                    'componentId'   : componentId,
                    'sectionId'     : sectionId,
                    'on'            : 'section'
                });
                this._initSectionButtonsAndActions();
                if (options.task === 'validateForm') {
                    this._validateForm(options.buttonId);
                }
                if (options.task === 'sectionToObj') {
                    this._sectionToObj();
                }
            };

            _proto._validateForm = function _validateForm(thisButtonId) {
                var validated = BazContentFieldsValidator.validateForm({
                    'componentId'     : $(thisButtonId).parents('.component')[0].id,
                    'sectionId'       : $(thisButtonId).parents('.sectionWithForm')[0].id,
                    'task'            : 'validateForm',
                    'onSuccess'       : false,
                    'type'            : 'sections',
                    'preValidated'    : false,
                    'formId'          : null
                });
                return validated;
            };

            _proto._initSectionButtonsAndActions = function _initSectionButtonsAndActions() {
                $('#' + sectionId + ' .card-footer button.methodPost').each(function(index,button) {
                    $(button).click(function(e) {
                        e.preventDefault();
                        if (that._validateForm(this)) {
                            $(this).attr('disabled', true);
                            that._runAjax(this, $(this).attr('actionurl'), $.param(that._sectionToObj()));
                        }
                    });
                });
            };

            _proto._runAjax = function _runAjax(thisButtonId, url, dataToSubmit) {
                $.ajax({
                    'url'           : url,
                    'data'          : dataToSubmit,
                    'method'        : 'post',
                    'dataType'      : 'json',
                    'success'       : function(data) {
                                        if (data.status === 0) {
                                            PNotify.removeAll();
                                            PNotify.success({
                                                title   : decodeURI($(thisButtonId).data('notificationtitle')),
                                                text    : decodeURI($(thisButtonId).data('notificationmessage'))
                                            });
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
                                                $(thisButtonId).parent().siblings('.card-body').load($(thisButtonId).attr('href'),data);
                                                $(thisButtonId).attr('disabled', false);
                                            } else if (!$(thisButtonId).data('actiontarget') || $(thisButtonId).data('actiontarget') === '') {
                                                $(thisButtonId).attr('disabled', false);
                                            }
                                        } else {
                                            PNotify.removeAll();
                                            // Instead of error, something like contact BAZ link can be shown which can be diverted to form.
                                            PNotify.error({
                                                title   : 'Error!',
                                                text    : 'Contact Administrator'
                                            });
                                        }
                                    }
                });
            }
            _proto._sectionToObj = function _sectionToObj() {
                componentId = $(this._element).parents('.component')[0].id;
                sectionId = $(this._element)[0].id;
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
                                if ($(thatV).data('multiple')) {
                                    dataCollection[componentId][sectionId]['data'][extractComponentId] = [];
                                    $($(bazScanField)[0].selectedOptions).each(function(i,v){
                                        var thisSelectId = $(v)[0].value;
                                        var thisSelectName = $(v)[0].text;
                                        var thisSelectObject = { };
                                        thisSelectObject[thisSelectId] = thisSelectName;
                                        dataCollection[componentId][sectionId]['data'][extractComponentId].push(thisSelectObject);
                                    });
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
                                dataCollection[componentId][sectionId]['data'][extractComponentId] = [];
                                $(bazScanField).find('span').each(function(i,v) {
                                    var thisCounterId = $(v).parent('div')[0].id;
                                    var counterObject = { };
                                    counterObject[thisCounterId] = $(v).html();
                                    dataCollection[componentId][sectionId]['data'][extractComponentId].push(counterObject);
                                });
                            }
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
                if (dataCollection[componentId][sectionId].data.id === '') {//Create
                    $('#' + sectionId).find('[data-bazpostoncreate=true]').each(function() {
                        stripComponentId = $(this)[0].id.split('-');
                        stripComponentId = stripComponentId[stripComponentId.length - 1];
                        dataCollection[componentId][sectionId]['dataToSubmit'][stripComponentId] = dataCollection[componentId][sectionId].data[stripComponentId];
                    });
                } else {//Edit
                    $('#' + sectionId).find('[data-bazpostonupdate=true]').each(function() {
                        stripComponentId = $(this)[0].id.split('-');
                        stripComponentId = stripComponentId[stripComponentId.length - 1];
                        dataCollection[componentId][sectionId]['dataToSubmit'][stripComponentId] = dataCollection[componentId][sectionId].data[stripComponentId];
                    });
                }
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
            if ($(this).data('bazdevmodetools') === 'false' ||
                $(this).data('bazdevmodetools') === false) {
                BazContentSectionWithForm._jQueryInterface.call($(this));
            }
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
/* globals define exports BazContentFieldsValidator BazContentLoader Swal PNotify */
/*
* @title                    : BazContentSectionsList
* @description              : Baz Lib for Content (Sections With Form)
* @developer                : guru@bazaari.com.au
* @usage                    : ('#'+ sectionId).BazContentSectionsList;
* @functions                :
* @options                  :
*/
(function (global, factory) {
    'use strict';
    typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports) :
    typeof define === 'function' && define.amd ? define(['exports'], factory) :
    (global = global || self, factory(global.BazLibs = {}));
}(this, function (exports) {
    'use strict';

    var BazContentSectionsList = function ($) {

        var NAME                    = 'BazContentSectionsList';
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
            task                    : null
        };
        var dataCollection,
            componentId,
            sectionId,
            pnotifySound,
            swalSound;
        var listColumns = { };
            // that;

        var BazContentSectionsList = function () {
            function BazContentSectionsList(element, settings) {
                // that = this;
                this._element = element;
                this._settings = settings;
            }

            var _proto = BazContentSectionsList.prototype;

            _proto._error = function _error(message) {
                throw new Error(message);
            };

            _proto._init = function _init(options) {
                componentId = $(this._element).parents('.component')[0].id;
                sectionId = $(this._element)[0].id;

                dataCollection = window['dataCollection'];
                pnotifySound = new Audio(dataCollection.env.soundPath + 'pnotify.mp3');
                swalSound = new Audio(dataCollection.env.soundPath + 'swal.mp3');

                if (!dataCollection[componentId]) {
                    dataCollection[componentId] = { };
                }
                if (!dataCollection[componentId][sectionId]) {
                    dataCollection[componentId][sectionId] = { };
                }

                if ($(this._element).is('.sectionWithListingFilter')) {
                    $(this._element).BazContentFields();

                    BazContentFieldsValidator.initValidator({
                        'componentId'   : componentId,
                        'sectionId'     : sectionId,
                        'on'            : 'section'
                    });
                }

                if ($(this._element).is('.sectionWithListingDatatable')) {
                    this._buildListDatatable(options);
                }
            };

            //Build list datatable
            _proto._buildListDatatable = function() {
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
                var thisOptions = dataCollection[componentId][sectionId][sectionId + '-table'];
                listColumns[thisOptions.listOptions.tableName] = [];
                var datatableOptions = thisOptions.listOptions.datatable;
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
                if (datatableOptions.hasIdColumn === 'true' || datatableOptions.hasIdColumn === '1') {
                    if (!datatableOptions.columns.find(({name}) => name === 'id')) {
                        listColumns[thisOptions.listOptions.tableName].push({
                            data    : 'id',
                            title   : 'ID'
                        });
                    }
                }
                // All Columns (except ID and __control and replacedColumns)
                $.each(datatableOptions.columns, function(index,column) {
                    var disabled;
                    // disable column ordering
                    var disableColumnOrdering = datatableOptions.disableColumnsOrdering.includes(column.name);
                    if (disableColumnOrdering) {
                        disabled = false;
                    } else {
                        disabled = true;
                    }

                    listColumns[thisOptions.listOptions.tableName].push({
                        data            : column.name,
                        title           : column.value.toUpperCase(),
                        orderable       : disabled,
                        className       : 'data-' + column.name
                    });
                });

                // Hide Columns
                var hideColumns = [];
                if (datatableOptions.hideIdColumn === 'true' || datatableOptions.hideIdColumn === '1') {
                    hideColumns.push(0);
                }
                if (datatableOptions.columns.length > datatableOptions.NoOfColumnsToShow) {
                    var colDiff = datatableOptions.columns.length - datatableOptions.NoOfColumnsToShow;
                    for (var i = 1; i <= colDiff; i++) {
                        hideColumns.push(datatableOptions.columns.length - i);
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
                            className       : 'btn-secondary',
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
                            className       : '',
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
                                        infoEmpty       : '',
                                        searchPlaceholder: 'Search ' + thisOptions.listOptions.componentName + '...',
                                        select          : {
                                            rows    : {
                                                    _   : 'Selected %d rows. Click the selected row again to deselect',
                                                    0   : '<i class="fas fa-fw fa-info-circle"></i>Click a row to select it',
                                                    1   : 'Selected 1 row. Click the selected row again to deselect'
                                                }
                                        },
                                        buttons         : {
                                            selectAll   : '<i class="fas fa-fw fa-xs fa-check-double"></i>',
                                            selectNone  : '<span class="fa-stack">' +
                                                          '<i class="fas fa-fw fa-xs fa-check-double fa-stack-1x"></i>' +
                                                          '<i class="fas fa-fw fa-sm fa-ban fa-stack-2x text-danger"></i>' +
                                                          '</span>'
                                        }
                                    },
                    initComplete    : function() {
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
                                        drawCallback();
                                    }
                });

                if (thisOptions.listOptions.postURL) {
                    runDatatableAjax(thisOptions.listOptions.postParams);
                } else {
                    // Enable paging if data is more than 10 on static datatable
                    if (datatableOptions.data.length > 10) {
                        $.extend(thisOptions.listOptions.datatable, {
                            paging : true,
                        });
                    }
                    $('#list-data-loader').hide();
                    tableInit(false);
                    registerEvents();
                }

                function runDatatableAjax(postData, reDraw) {
                    var url = dataCollection.env.rootPath + 'index.php?route=' + thisOptions.listOptions.postURL;
                    $.ajax({
                        url         : url,
                        method      : 'post',
                        dataType    : 'html',
                        data        : postData,
                        success     : function(data) {
                            $('#list-data-loader').hide();
                            $('#' + sectionId + '-table').append(data);
                        }
                    }).done(function() {
                        tableInit(reDraw);
                        registerEvents();
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
                function tableInit(reDraw) {
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
                        if (datatableOptions.pagination) {
                            $.extend(thisOptions.listOptions.datatable, {
                                paging : true,
                                pagingType : 'simple',
                            });
                            datatableOptions['language']['zeroRecords'] = '<i class="fas fa-cog fa-spin"></i> Loading...';
                        }

                        // Control Column
                        if (datatableOptions.rowControls) {
                            listColumns[thisOptions.listOptions.tableName].push({
                                data        : '__control',
                                title       : 'ACTIONS',
                                orderable   : false
                            });
                        }
                        if (thisOptions.customFunctions.beforeTableInit) {
                            thisOptions.customFunctions.beforeTableInit();
                        }
                        thisOptions['datatable'] = $('#' + thisOptions.listOptions.tableName).DataTable(datatableOptions);
                        if (thisOptions.customFunctions.afterTableInit) {
                            thisOptions.customFunctions.afterTableInit();
                        }
                    } else { //redraw used on pagination prev and next
                        if (thisOptions.customFunctions.beforeRedraw) {
                            thisOptions.customFunctions.beforeRedraw();
                        }
                        thisOptions['datatable'].rows.add(datatableOptions.data).draw();
                        if (thisOptions.customFunctions.afterRedraw) {
                            thisOptions.customFunctions.afterRedraw();
                        }

                    }

                    if (datatableOptions.rowControls) {
                        BazContentLoader.init({});
                    }
                }

                //Register __control(Action buttons)
                function registerEvents() {
                    // customSwitch Toggle Function
                    $('#' + sectionId + '-table .custom-switch input').each(function(index,rowSwitchInput) {
                        $(rowSwitchInput).click(function() {
                            var rowSwitchInputId = $(rowSwitchInput)[0].id;
                            var url = dataCollection.env.rootPath + 'index.php?route=' + $(rowSwitchInput).data('switchactionurl');
                            var columnId = $(rowSwitchInput).data('columnid');
                            var checked = $(rowSwitchInput).is('[checked]');
                            var columnsDataToInclude = $(rowSwitchInput).data('switchactionincludecolumnsdata').split(',');
                            var rowData;
                            rowData = thisOptions['datatable'].row($(this).parents('tr')).data();
                            if (checked) {
                                rowData[columnId] = 0;
                                $(rowSwitchInput).attr('checked', false);
                                document.getElementById(rowSwitchInputId).checked = false;
                            } else {
                                rowData[columnId] = 1;
                                $(rowSwitchInput).attr('checked', true);
                                document.getElementById(rowSwitchInputId).checked = true;
                            }
                            var name = $(rowSwitchInput).parents('td').siblings('.data-' + $(rowSwitchInput).data('notificationtextfromcolumn')).html();
                            var switchOnText = name + ' enabled';
                            var switchOffText = name + ' disabled';
                            if (checked) {
                                PNotify.removeAll();
                                Swal.fire({
                                    title                       : '<i class="fa text-danger fa-lg fa-question-circle m-2"></i>' +
                                                                  ' <span style="font-size:40px;" class="text-danger"> Disable ' +
                                                                   name + '?</span>',
                                    width                       : '100%',
                                    background                  : 'rgba(0,0,0,.8)',
                                    backdrop                    : 'rgba(0,0,0,.6)',
                                    animation                   : false,
                                    customClass                 : 'rounded-0 animated fadeIn',
                                    buttonsStyling              : false,
                                    confirmButtonClass          : 'btn btn-danger text-uppercase',
                                    confirmButtonText           : 'Disable',
                                    cancelButtonClass           : 'ml-2 btn btn-default text-uppercase',
                                    showCancelButton            : true,
                                    keydownListenerCapture      : true,
                                    allowOutsideClick           : false,
                                    allowEscapeKey              : false,
                                    allowEnterKey               : false,
                                    onOpen                      : function() {
                                        swalSound.play();
                                    }
                                }).then((result) => {
                                    if (result.value) {
                                        runAjax(false, switchOffText);
                                    } else {
                                        $(rowSwitchInput).attr('checked', true);
                                        document.getElementById(rowSwitchInputId).checked = true;
                                    }
                                });
                            } else {
                                runAjax(true, switchOnText);
                            }
                            function runAjax(status, notificationText) {
                                var dataToSubmit = { };
                                for (var data in rowData) {
                                    if (columnsDataToInclude.includes(data)) {
                                        dataToSubmit[data] = rowData[data];
                                    }
                                }
                                $.ajax({
                                    url         : url,
                                    method      : 'post',
                                    data        : dataToSubmit,
                                    dataType    : 'json',
                                    success     : function(response) {
                                        if (response.status === 0) {
                                            PNotify.removeAll();
                                            PNotify.success({
                                                title           : notificationText,
                                                cornerClass     : 'ui-pnotify-sharp'
                                            });
                                            $(rowSwitchInput).attr('checked', status);
                                            document.getElementById(rowSwitchInputId).checked = true;
                                        } else {
                                            PNotify.removeAll();
                                            PNotify.error({
                                                title           : 'Error!',
                                                cornerClass     : 'ui-pnotify-sharp'
                                            });
                                            $(rowSwitchInput).attr('checked', false);
                                            document.getElementById(rowSwitchInputId).checked = false;
                                        }
                                        pnotifySound.play();
                                    }
                                });
                            }
                        });
                    });

                    // RadioButtons
                    $('#' + sectionId + '-table .btn-group-toggle label').each(function(index,radioButtonsLabel) {
                        $(radioButtonsLabel).click(function() {
                            var currentCheckedId, currentCheckedLabel;
                            $(this).siblings('label').children('input').each(function(index,sibling) {
                                if (sibling.checked) {
                                    currentCheckedId = sibling.id;
                                    currentCheckedLabel = sibling.parentElement;
                                } else if (sibling.defaultChecked) {
                                    currentCheckedId = sibling.id;
                                    currentCheckedLabel = sibling.parentElement;
                                }
                            });
                            var thisId = $(this).children('input')[0].id;
                            var url = dataCollection.env.rootPath + 'index.php?route=' + $(this).children('input').data('radiobuttonsactionurl');
                            var columnId = $(this).children('input').data('columnid');
                            var dataValue = $(this).children('input').data('value');
                            var checked = false;
                            if ($(this).children('input').is('[checked]') || $(this).children('input')[0].defaultChecked) {
                                checked = true;
                            }
                            var radioChangeText = $(this).parents('td').siblings('.data-' + $(this).children('input').data('notificationtextfromcolumn')).html() + ' ' +
                                                    $(this).children('input').data('columnid') + ' changed';
                            if (!checked) {
                                PNotify.removeAll();
                                Swal.fire({
                                    title                       : '<i class="fa text-danger fa-lg fa-question-circle m-2"></i>' +
                                                                    ' <span style="font-size:40px;" class="text-danger"> Change ' +
                                                                    $(this).parents('td').siblings('.data-' +
                                                                        $(this).children('input').data('notificationtextfromcolumn')).html() + ' ' +
                                                                    $(this).children('input').data('columnid') +
                                                                    '?</span>',
                                    width                       : '100%',
                                    background                  : 'rgba(0,0,0,.8)',
                                    backdrop                    : 'rgba(0,0,0,.6)',
                                    animation                   : false,
                                    customClass                 : 'rounded-0 animated fadeIn',
                                    buttonsStyling              : false,
                                    confirmButtonClass          : 'btn btn-danger text-uppercase',
                                    confirmButtonText           : 'Change',
                                    cancelButtonClass           : 'ml-2 btn btn-default text-uppercase',
                                    showCancelButton            : true,
                                    keydownListenerCapture      : true,
                                    allowOutsideClick           : false,
                                    allowEscapeKey              : false,
                                    allowEnterKey               : false,
                                    onOpen                      : function() {
                                        swalSound.play();
                                    }
                                }).then((result) => {
                                    if (result.value) {
                                        runAjax(false, radioChangeText);
                                    } else {
                                        $(this).removeClass('focus active');
                                        $('#' + currentCheckedId).attr('checked', true);
                                        document.getElementById(currentCheckedId).checked = true;
                                        $(currentCheckedLabel).addClass('focus active');
                                    }
                                });
                            }

                            function runAjax(status, notificationText) {
                                var columnsDataToInclude = $('#' + thisId).data('radiobuttonsactionincludecolumnsdata').split(',');
                                var rowData = thisOptions['datatable'].row($('#' + thisId).parents('tr')).data();
                                var dataToSubmit = { };
                                for (var data in rowData) {
                                    if (columnsDataToInclude.includes(data)) {
                                        if (columnId === data) {
                                            dataToSubmit[data] = dataValue;
                                        } else {
                                            dataToSubmit[data] = rowData[data];
                                        }
                                    }
                                }
                                $.ajax({
                                    url         : url,
                                    method      : 'post',
                                    data        : dataToSubmit,
                                    dataType    : 'json',
                                    success     : function(response) {
                                        if (response.status === 1) {
                                            PNotify.removeAll()
                                            PNotify.success({
                                                title           : notificationText,
                                                cornerClass     : 'ui-pnotify-sharp'
                                            });
                                            $('#' + currentCheckedId).attr('checked', false);
                                            document.getElementById(currentCheckedId).checked = false;
                                            $('#' + thisId).attr('checked', true);
                                            document.getElementById(thisId).checked = true;
                                        } else {
                                            PNotify.removeAll();
                                            PNotify.error({
                                                title           : 'Error!',
                                                cornerClass     : 'ui-pnotify-sharp'
                                            });
                                            $('#' + thisId).parent('label').removeClass('focus active');
                                            $('#' + thisId).attr('checked', false);
                                            document.getElementById(thisId).checked = false;
                                            $('#' + currentCheckedId).attr('checked', true);
                                            document.getElementById(currentCheckedId).checked = true;
                                            $(currentCheckedLabel).addClass('focus active');
                                        }
                                        pnotifySound.play();
                                    }
                                });
                            }
                        });
                    });

                    // Deleting Row (element .rowRemove)
                    $('#' + sectionId + '-table .rowRemove').each(function(index,rowRemove) {
                        $(rowRemove).click(function(e) {
                            e.preventDefault();
                            var thisButton = this;
                            var url = $(this).attr('href');
                            var deleteText = $(this).parents('td').siblings('.data-' + $(this).data('notificationtextfromcolumn')).html();
                            var dataToSend = { };
                            dataToSend.id = thisOptions['datatable'].row($(thisButton).parents('tr')).id();
                            Swal.fire({
                                title                       : '<i class="fa text-danger fa-lg fa-question-circle m-2">' +
                                                              '</i> <span style="font-size:40px;" class="text-danger"> Delete ' +
                                                              deleteText + '?</span>',
                                width                       : '100%',
                                background                  : 'rgba(0,0,0,.8)',
                                backdrop                    : 'rgba(0,0,0,.6)',
                                animation                   : false,
                                customClass                 : 'rounded-0 animated fadeIn',
                                buttonsStyling              : false,
                                confirmButtonClass          : 'btn btn-danger text-uppercase',
                                confirmButtonText           : 'Delete',
                                cancelButtonClass           : 'ml-2 btn btn-default text-uppercase',
                                showCancelButton            : true,
                                keydownListenerCapture      : true,
                                allowOutsideClick           : false,
                                allowEscapeKey              : false,
                                allowEnterKey               : false,
                                onOpen                      : function() {
                                    swalSound.play();
                                }
                            }).then((result) => {
                                if (result.value) {
                                    if (datatableOptions.sendConfirmRemove === 'true' || datatableOptions.sendConfirmRemove === '1') {
                                        dataToSend.confirm = '1';
                                    }
                                    $.ajax({
                                        url         : url,
                                        method      : 'post',
                                        dataType    : 'json',
                                        data        : dataToSend,
                                        success     : function(response) {
                                            if (response.status === 0) {
                                                PNotify.removeAll();
                                                PNotify.success({
                                                    title           : deleteText + ' deleted.',
                                                    cornerClass     : 'ui-pnotify-sharp'
                                                });
                                                // remove row on success
                                                thisOptions['datatable'].row($(thisButton).parents('tr')).remove().draw();
                                            } else {
                                                PNotify.removeAll();
                                                PNotify.error({
                                                    title           : 'Error!',
                                                    cornerClass     : 'ui-pnotify-sharp'
                                                });
                                            }
                                            pnotifySound.play();
                                        }
                                    });
                                }
                            });
                        });
                    });

                    // Datatable Events
                    thisOptions['datatable'].on('draw responsive-resize responsive-display', function() {
                        BazContentLoader.init({});
                    });
                }

                function drawCallback() {
                    if (datatableOptions.pagination) {
                        if (datatableOptions.pagination.prev) {
                            $('.paginate_button.previous').removeClass('disabled');
                            $('.paginate_button.previous').click(function() {
                                thisOptions['datatable'].rows().clear().draw();
                                runDatatableAjax({
                                    'operation' : 'navigation',
                                    'results'   : thisOptions.listOptions.postParams.results,
                                    'dir'       : 'prev',
                                    'id'        : datatableOptions.pagination.prev.id
                                }, true)
                            });
                        } else if (datatableOptions.pagination.next) {
                            $('.paginate_button.next').removeClass('disabled');
                            $('.paginate_button.next').click(function() {
                                thisOptions['datatable'].rows().clear().draw();
                                runDatatableAjax({
                                    'operation' : 'navigation',
                                    'results'   : thisOptions.listOptions.postParams.results,
                                    'dir'       : 'next',
                                    'id'        : datatableOptions.pagination.next.id
                                }, true)
                            });
                        }
                    }
                }
            };

            BazContentSectionsList._jQueryInterface = function _jQueryInterface(options) {
                dataCollection = window['dataCollection'];
                componentId = $(this).parents('.component')[0].id;
                sectionId = $(this)[0].id;
                dataCollection[componentId][sectionId]['BazContentSectionsList'] = $(this).data(DATA_KEY);
                options = $.extend({}, Default, options);

                if (!dataCollection[componentId][sectionId]['BazContentSectionsList']) {
                    dataCollection[componentId][sectionId]['BazContentSectionsList'] = new BazContentSectionsList($(this), options);
                    $(this).data(DATA_KEY, typeof options === 'string' ? 'options need to be an object and not string' : options);
                    dataCollection[componentId][sectionId]['BazContentSectionsList']._init(options);
                } else {
                    delete dataCollection[componentId][sectionId]['BazContentSectionsList'];
                    dataCollection[componentId][sectionId]['BazContentSectionsList'] = new BazContentSectionsList($(this), options);
                    $(this).data(DATA_KEY, typeof options === 'string' ? 'options need to be an object and not string' : options);
                    dataCollection[componentId][sectionId]['BazContentSectionsList']._init(options);
                }
            };

        return BazContentSectionsList;

        }();

    $(document).on('libsLoadComplete bazContentLoaderAjaxComplete bazContentWizardAjaxComplete', function() {
        if ($('.sectionWithListingFilter').length > 0) {
            $('.sectionWithListingFilter').each(function() {
                BazContentSectionsList._jQueryInterface.call($(this));
            });
        }
        if ($('.sectionWithListingDatatable').length > 0) {
            $('.sectionWithListingDatatable').each(function() {
                BazContentSectionsList._jQueryInterface.call($(this));
            });
        }
    });

    $.fn[NAME] = BazContentSectionsList._jQueryInterface;
    $.fn[NAME].Constructor = BazContentSectionsList;

    $.fn[NAME].noConflict = function () {
        $.fn[NAME] = JQUERY_NO_CONFLICT;
        return BazContentSectionsList._jQueryInterface;
    };

    return BazContentSectionsList;
}(jQuery);

exports.BazContentSectionsList = BazContentSectionsList;

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

var _extends = Object.assign || function (target) { 'use strict'; for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
// var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var BazContentSectionWithStorage = function() {
    'use strict';
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
    'use strict';
    if ($('.sectionWithStorage').length > 0) {
        $('.sectionWithStorage').each(function() {
            BazContentSectionWithStorage.init({'storageId' : $(this)});
        });
    }
});

/* exported BazContentSectionWithWizard */
/* globals */
/*
* @title                    : BazContentSectionWithWizard
* @description              : Baz Core Lib
* @developer                : guru@bazaari.com.au
* @usage                    : BazContentSectionWithWizard._function_(_options_);
* @functions                : BazHeader, BazFooter, BazUpdateBreadcrumb
* @options                  :
*/

var _extends = Object.assign || function (target) { 'use strict'; for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
// var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var BazContentSectionWithWizard = function() {
    'use strict';
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
        wizardOptions['currentStep'] = 0;
        lastStep = wizardOptions.steps.length - 1;

        // ReviewDiv
        if (wizardOptions.showReview) {
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

            if (steps[step]['ajax']) {
                $('#' + sectionId + '-' + step + '-data').load(dataCollection.env.rootPath + 'index.php?route=' + steps[step]['ajax']);
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
        }

        // Make all contentAjaxLink to contentModalLink if section is Datatable
        // $('#' + sectionId + '-data .contentAjaxLink').addClass('contentModalLink').removeClass('contentAjaxLink');
        // Change Modal Size to xl
        // BazContentLoader.init({'modalSize' : 'xl'});

        $('#' + sectionId + '-0-step').addClass('current');
        if ($('#' + sectionId + '-0-description').length > 0) {
            $('#' + sectionId + '-0-description').attr('hidden', false);
        }
        $('#' + sectionId + '-0-data').attr('hidden', false);
        originalTitle = $('#' + componentId + ' div.card-header span.text-bold').first().html();

        updateTitle();
        initWizardStepsButtons();
        hideHeaderFooter();

        var runFirstTime = true;
        $(document).ajaxComplete(function(e, xhr, settings) {
            //eslint-disable-next-line
            console.log(runFirstTime);
            if (runFirstTime) {
                for (var ajaxStep in steps) {
                    var reviewBeforeId, reviewAfterId;
                    var url = dataCollection.env.rootPath + 'index.php?route=' + steps[ajaxStep]['ajax'];
                    if (url === settings.url) {
                        //eslint-disable-next-line
                        console.log(ajaxStep);
                        reviewBeforeId = Number(ajaxStep) - 1;
                        reviewAfterId = Number(ajaxStep) + 1;
                        if ($('#' + sectionId + '-' + ajaxStep + '-data .component').length > 0) {
                            steps[ajaxStep]['componentId'] = $('#' + sectionId + '-' + ajaxStep + '-data .component')[0].id;
                        }
                        if ($('#' + sectionId + '-' + ajaxStep + '-data .sectionWithForm').length > 0) {
                            steps[ajaxStep]['sectionId'] = $('#' + sectionId + '-' + ajaxStep + '-data .sectionWithForm')[0].id;
                            steps[ajaxStep]['type'] = 'form';
                            steps[ajaxStep]['validate'] = true;
                            if (wizardOptions.showReview) {
                                buildReview(ajaxStep);
                                if ($('#' + sectionId + '-' + reviewBeforeId + '-review').length > 0) {
                                    $('#' + sectionId + '-' + reviewBeforeId + '-review').after(review);
                                } else if ($('#' + sectionId + '-' + reviewAfterId + '-review').length > 0) {
                                    $('#' + sectionId + '-' + reviewAfterId + '-review').before(review);
                                }
                            }
                        }
                        hideHeaderFooter();
                        $('body').trigger('bazContentWizardAjaxComplete');
                    }
                }
                runFirstTime = false;
            }
        });
    }

    function updateTitle() {
        $('#' + componentId + ' div.card-header span.text-bold').addClass('text-uppercase');
        var title = originalTitle + ' : ' + steps[wizardOptions['currentStep']].title;
        $('#' + componentId + ' div.card-header span.text-bold').first().html(title);
    }

    function hideHeaderFooter() {
        $('#' + sectionId + '-data .card-header').each(function(){
            if (!$(this).parents().hasClass('accordion')) {
                $(this).attr('hidden', true);
            }
        });
        $('#' + sectionId + '-data .card-footer').each(function(){
            if (!$(this).parents().hasClass('accordion')) {
                $(this).attr('hidden', true);
            }
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

        if (wizardOptions.canCancel) {
            $('#' + sectionId + '-cancel').attr('hidden', false);
        }
        if (wizardOptions['currentStep'] === 0) {
            $('#' + sectionId + '-previous').attr('hidden', true);
            $('#' + sectionId + '-next').attr('hidden', false);
            $('#' + sectionId + '-done').attr('hidden', true);
            $('#' + sectionId + '-submit').attr('hidden', true);
        } else if (wizardOptions['currentStep'] === lastStep) {
            $('#' + sectionId + '-previous').attr('hidden', false);
            $('#' + sectionId + '-next').attr('hidden', true);
        } else {
            $('#' + sectionId + '-previous').attr('hidden', false);
            $('#' + sectionId + '-next').attr('hidden', false);
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
            // Validate form & extract data on successful validation
            if (steps[wizardOptions['currentStep']]['validate']) {
                $('#' + steps[wizardOptions['currentStep']]['sectionId']).BazContentSectionWithForm({
                    'task'      : 'validateForm',
                    'buttonId'  : $('#' + steps[wizardOptions['currentStep']]['sectionId'] + '-create')
                });
                // Extract data
                if ($('#' + steps[wizardOptions['currentStep']]['sectionId'] + '-alert').length === 0) {
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
                            //eslint-disable-next-line
                            console.log(dataCollection[steps[wizardOptions['currentStep']]['componentId']]);
                            fields += dataCollection[steps[wizardOptions['currentStep']]['componentId']][steps[wizardOptions['currentStep']]['sectionId']]['data'][field.id];
                        }
                        fields += '</div></div>';
                    });
                    $('#' + sectionId + '-' + wizardOptions['currentStep'] + '-accordioncard .card-body').html(fields);

                    // Submit form if submitOnNext
                    if (steps[wizardOptions['currentStep']]['submitOnNext']) {
                        if ($('#' + steps[wizardOptions['currentStep']]['sectionId'] + '-create').length > 0) {
                            doAjax(
                                $('#' + steps[wizardOptions['currentStep']]['sectionId'] + '-create').attr('actionurl'),
                                steps[wizardOptions['currentStep']]['componentId'],
                                steps[wizardOptions['currentStep']]['sectionId']
                                );
                        } else if ($('#' + steps[wizardOptions['currentStep']]['sectionId'] + '-edit').length > 0) {
                            doAjax(
                                $('#' + steps[wizardOptions['currentStep']]['sectionId'] + '-exit').attr('actionurl'),
                                steps[wizardOptions['currentStep']]['componentId'],
                                steps[wizardOptions['currentStep']]['sectionId']
                                );
                        }
                    } else {
                        goNext();
                    }
                }
            } else {
                goNext();
            }
        });
    }

    function goNext() {
        var nextStep = wizardOptions['currentStep'] + 1;
        $('#' + sectionId + '-' + wizardOptions['currentStep'] + '-step').addClass('visited').removeClass('current');
        $('#' + sectionId + '-' + nextStep + '-step').addClass('current');
        $('#' + sectionId + '-' + wizardOptions['currentStep'] + '-description').attr('hidden', true);
        $('#' + sectionId + '-' + wizardOptions['currentStep'] + '-data').attr('hidden', true);
        $('#' + sectionId + '-' + nextStep + '-description').attr('hidden', false);
        $('#' + sectionId + '-' + nextStep + '-data').attr('hidden', false);
        $('#' + sectionId + '-previous').attr('hidden', false);
        if (wizardOptions['steps'][wizardOptions['currentStep']]['onNext']) {
            wizardOptions['steps'][wizardOptions['currentStep']]['onNext']();
        }
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
                                doAjax(
                                    dataCollection.env.rootPath + 'index.php?route=' + steps[step].route,
                                    steps[step].componentId,
                                    steps[step].sectionId,
                                    step,
                                    true
                                    );
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
        $.ajax({
            'url'           : formUrl,
            'data'          : $.param(dataCollection[formComponentId][formSectionId].dataToSubmit),
            'method'        : 'post',
            'dataType'      : 'json',
            'success'       : function(data) {
                                if (data.status === 0) {
                                    $('#' + sectionId + '-' + step + '-accordioncard-header').removeClass('bg-danger').addClass('bg-success');
                                } else {
                                    $('#' + sectionId + '-' + step + '-accordioncard-header').removeClass('bg-success').addClass('bg-danger');
                                }
                            },
            'complete'      : function() {
                                if (lastStep) {
                                    if ($('#' + sectionId + '-review-accordion .bg-danger').length === 0) {
                                        $('#' + sectionId + '-submit').off();
                                        $('#' + sectionId + '-previous').attr('hidden', true);
                                        $('#' + sectionId + '-submit').attr('hidden', true);
                                        $('#' + sectionId + '-done').attr('hidden', false);
                                    }
                                }
            }
        });
        if (!lastStep) {
            goNext();
        }
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
    'use strict';
    if ($('.sectionWithWizard').length > 0) {
        $('.sectionWithWizard').each(function() {
            BazContentSectionWithWizard.init({'wizardId' : $(this)});
        });
    }
});

/* globals define exports PNotify */
/*
* @title                    : BazContentFields
* @description              : Baz Lib for Content (Sections With Form)
* @developer                : guru@bazaari.com.au
* @usage                    : ('#'+ section/componentID).BazContentFields;
* @functions                :
* @options                  :
*/
(function (global, factory) {
    'use strict';
    typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports) :
    typeof define === 'function' && define.amd ? define(['exports'], factory) :
    (global = global || self, factory(global.BazLibs = {}));
}(this, function (exports) {
    'use strict';

    var BazContentFields = function ($) {

        var NAME                    = 'BazContentFields';
        var DATA_KEY                = 'baz.contentfields';
        // var EVENT_KEY               = "." + DATA_KEY;
        var JQUERY_NO_CONFLICT      = $.fn[NAME];
        // var Event = {
        // };
        // var ClassName = {
        // };
        // var Selector = {
        // };
        var Default = {
        };
        var componentId,
            pnotifySound,
            dataCollection,
            sectionId,
            that;

        var BazContentFields = function () {
            function BazContentFields(element, settings) {
                that = this;
                this._element = element;
                this._settings = $.extend({}, Default, settings);

                if ($('body').find('.flatpickr-calendar').length > 0) {
                    $('body').find('.flatpickr-calendar').remove();
                }
                if ($('body').find('.dz-hidden-input').length > 0) {
                    $('body').find('.dz-hidden-input').remove();
                }

                this._init(this._settings);
                this._bazInitFields(this._settings);
            }

            var _proto = BazContentFields.prototype;

            _proto._error = function(message) {
                throw new Error(message);
            };

            _proto._init = function _init() {
                componentId = $(this._element).parents('.component')[0].id;
                sectionId = $(this._element)[0].id;
                dataCollection = window['dataCollection'];
                pnotifySound = new Audio(dataCollection.env.soundPath + 'pnotify.mp3'); //Notification sound
                // Grab Components HTML Code (future use)
                // if (!dataCollection[componentId].html){
                //     dataCollection[componentId].html = $('#' + componentId).parents('.container-fluid').html();
                // }
                // dataCollection[componentId][sectionId].html = $('#' + sectionId).html();

                // TODO Decide what to do with section without any fields.
                // I can only think of tabs being made available via section, which needs to be initialized.
                // ALSO TABS CAN HAVE OPTION TO ENABLE A PARTICULAR TABID or FIRST TAB ID
            };

            _proto._bazInitFields = function _bazInitFields() {
                // tableData[sectionId] = { };//building object used during save
                var minValText, maxValText, minLengthText, maxLengthText, thisFieldId;

                // Iterate through the component
                $('#' + sectionId).find('[data-bazscantype]').each(function(index,bazScanField) {
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
                            } else if (bazScanField.dataset.bazscantype === 'textarea') {
                                initTextarea(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
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
                            that._error('Individual sections parameters missing for ' + bazScanField.id);
                        }
                    // }
                });
                dataCollection[componentId][sectionId]['initFields'] = true;

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
                        options.beforeInit(dataCollection);
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
                    maxLength(thisFieldId, options);
                    if (options.afterInit) {
                        options.afterInit(dataCollection);
                    }
                }

                function initSelect2(fieldId, sectionId, options) {
                    if (options.beforeInit) {
                        options.beforeInit(dataCollection);
                    }
                    thisFieldId = fieldId;
                    fieldId = document.getElementById(fieldId);
                    options = $.extend({
                        placeholder: 'MISSING PLACEHOLDER'
                    }, options);
                    dataCollection[componentId][sectionId][thisFieldId]['select2'] = $(fieldId).select2(options);
                    // validation
                    if (dataCollection[componentId][sectionId][sectionId + '-form'] &&
                        dataCollection[componentId][sectionId][sectionId + '-form'].rules[thisFieldId] === 'required') {
                        $(fieldId).on('change.select2', function() {
                            $(this).valid();
                        });
                    }
                    if (options.afterInit) {
                        options.afterInit(dataCollection);
                    }
                }

                function initCheckbox(fieldId, options) {
                    if (options.beforeInit) {
                        options.beforeInit(dataCollection);
                    }
                    thisFieldId = fieldId;
                    fieldId = document.getElementById(fieldId);
                    if (options.afterInit) {
                        options.afterInit(dataCollection);
                    }
                }

                function initRadio(fieldId, options) {
                    if (options.beforeInit) {
                        options.beforeInit(dataCollection);
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
                        options.afterInit(dataCollection);
                    }
                }

                function initFlatpickr(fieldId, options) {
                    if (options.beforeInit) {
                        options.beforeInit();
                    }
                    thisFieldId = fieldId;
                    fieldId = $('#' + fieldId).parent();
                    options = $.extend({
                        wrap            : true,
                        enableTime      : true,
                        dateFormat      : 'd/m/Y h:i K',
                        minuteIncrement : 1
                    }, options);
                    if ($(fieldId).data('flatpickr-mode') === 'multiple') {
                        options = $.extend({
                            mode : 'multiple'
                        }, options);
                    }
                    if ($(fieldId).data('flatpickr-mode') === 'range') {
                        options = $.extend({
                            mode : 'range'
                        }, options);
                    }
                    dataCollection[componentId][sectionId][thisFieldId]['flatpickr'] = $(fieldId).flatpickr(options);
                    if ($(fieldId).find('#' + thisFieldId + '-clear').length > 0) {
                        $('#' + thisFieldId + '-clear').click(function() {
                            dataCollection[componentId][sectionId][thisFieldId]['flatpickr'].clear();
                            dataCollection[componentId][sectionId][thisFieldId]['flatpickr'].close();
                        });
                    }
                    if (options.afterInit) {
                        options.afterInit(dataCollection);
                    }
                }

                function initTextarea(fieldId, options) {
                    if (options.beforeInit) {
                        options.beforeInit(dataCollection);
                    }
                    thisFieldId = fieldId;
                    fieldId = document.getElementById(fieldId);
                    maxLength(fieldId, options);
                    if (options.afterInit) {
                        options.afterInit(dataCollection);
                    }
                }

                function initTrumbowyg(fieldId, options) {
                    if (options.beforeInit) {
                        options.beforeInit(dataCollection);
                    }
                    thisFieldId = fieldId;
                    fieldId = document.getElementById(fieldId);
                    options = $.extend({
                        imageWidthModalEdit: true,
                        urlProtocol: true,
                        tagsToRemove: ['script', 'link'],
                        btnsDef: {
                            image: {
                                dropdown: ['insertImage', 'base64', 'upload', 'noembed'],
                                ico: 'insertImage'
                            }
                        },
                        btns: [
                            ['viewHTML', 'formatting', 'historyUndo', 'historyRedo'],
                            ['fontfamily', 'fontsize', 'superscript', 'subscript'],
                            ['strong', 'em', 'del', 'lineheight', 'preformatted', 'specialChars'],
                            ['foreColor', 'backColor', 'link', 'image'],
                            ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                            ['unorderedList', 'orderedList', 'table', 'horizontalRule'],
                            ['removeformat', 'fullscreen']
                        ],
                        plugins: {
                            table: {
                                rows: 4,
                                columns: 4
                            }
                        }
                    }, options);
                    dataCollection[componentId][sectionId][thisFieldId]['trumbowyg'] =
                        $(fieldId).trumbowyg(options);
                    if (options.afterInit) {
                        options.afterInit(dataCollection);
                    }
                }

                function initCounters(fieldId, options) {
                    if (options.beforeInit) {
                        options.beforeInit(dataCollection);
                    }
                    thisFieldId = fieldId;
                    fieldId = document.getElementById(fieldId);
                    if (options.afterInit) {
                        options.afterInit(dataCollection);
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
                    // Search
                    if (options.bazJstreeOptions.search == null || options.bazJstreeOptions.search) {
                        $('#' + thisFieldId + '-tree-search-input').parents('.form-group').first().attr('hidden', false);
                        $('#' + thisFieldId + '-tree-search-input').on('keyup', function() {
                            $(fieldId).jstree(true).search($(this).val());
                        });
                    }
                    var selectedNode;
                    // Add New Node
                    if (options.bazJstreeOptions.add == null || options.bazJstreeOptions.add) {
                        $('#' + thisFieldId + '-tools-add').attr('hidden', false);
                        $('#' + thisFieldId + '-tools-add').click(function(e) {
                            e.preventDefault();
                            selectedNode = $(fieldId).jstree('get_selected', true);
                            // Check if node are selected and only 1 is selected
                            if ($(selectedNode).length !== 1) {
                                PNotify.removeAll();
                                PNotify.notice({
                                    title: 'None or Multiple ' + options.bazJstreeOptions.treeName + ' selected!',
                                    text: 'Please select only 1 ' + options.bazJstreeOptions.treeName + ' to create a new node under it'
                                });
                                pnotifySound.play();
                                return false;
                            } else {
                                $('#' + thisFieldId + '-tree-search-input').parents('.form-group').first().attr('hidden', true);
                                $('#' + thisFieldId + '-tree-edit-input').parents('.form-group').first().attr('hidden', true);
                                $('#' + thisFieldId + '-tree-add-input').parents('.form-group').first().attr('hidden', false);
                                $('#' + thisFieldId + '-tree-add-input').focus();
                                $('#' + thisFieldId + '-tree-add-input-cancel').click(function() {
                                    $('#' + thisFieldId + '-tree-add-input').val(null);
                                    $('#' + thisFieldId + '-tree-search-input').parents('.form-group').first().attr('hidden', false);
                                    $('#' + thisFieldId + '-tree-add-input').parents('.form-group').first().attr('hidden', true);
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
                        $('#' + thisFieldId + '-tools-edit').click(function() {
                        selectedNode = $(fieldId).jstree('get_selected', true);
                        // Check if node are selected and only 1 is selected
                            if ($(selectedNode).length !== 1) {
                                PNotify.removeAll();
                                PNotify.notice({
                                    title: 'None or Multiple ' + options.bazJstreeOptions.treeName + ' selected!',
                                    text: 'Please select only 1 ' + options.bazJstreeOptions.treeName + ' to rename',
                                });
                                pnotifySound.play();
                                return false;
                            } else {
                                $('#' + thisFieldId + '-tree-search-input').parents('.form-group').first().attr('hidden', true);
                                $('#' + thisFieldId + '-tree-edit-input').parents('.form-group').first().attr('hidden', false);
                                $('#' + thisFieldId + '-tree-edit-input').val(selectedNode[0].text).focus();
                                $('#' + thisFieldId + '-tree-edit-input-cancel').click(function() {
                                    $('#' + thisFieldId + '-tree-edit-input').val(null);
                                    $('#' + thisFieldId + '-tree-search-input').parents('.form-group').first().attr('hidden', false);
                                    $('#' + thisFieldId + '-tree-edit-input').parents('.form-group').first().attr('hidden', true);
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
                        $('#' + thisFieldId + '-tools-collapse').click(function(e) {
                            e.preventDefault();
                            $(fieldId).jstree('deselect_all');
                            $(fieldId).jstree('close_all');
                        });
                    }
                    // Expand all Nodes
                    if (options.bazJstreeOptions.expand == null || options.bazJstreeOptions.expand) {
                        $('#' + thisFieldId + '-tools-expand').attr('hidden', false);
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
                        if (task === 'addNode') {
                            tree.jstree('create_node',
                                $('#' + selectedNode[0].id),
                                $('#' + optionsId + '-tree-add-input').val(),
                                'last',
                                function() {
                                    tree.jstree('open_node', $('#' + selectedNode[0].id));
                                }
                            );
                            $('#' + thisFieldId + '-tree-search-input').parents('.form-group').first().attr('hidden', false);
                            $('#' + thisFieldId + '-tree-add-input').parents('.form-group').first().attr('hidden', true);
                            $('#' + optionsId + '-tree-add-input').val(null);
                            $(elthis).off();
                            $(elthat).off();
                            runFunction();
                        } else if (task === 'editNode') {
                            tree.jstree('rename_node',
                                $('#' + selectedNode[0].id),
                                $('#' + optionsId + '-tree-edit-input').val()
                            );
                            $('#' + thisFieldId + '-tree-search-input').parents('.form-group').first().attr('hidden', false);
                            $('#' + thisFieldId + '-tree-edit-input').parents('.form-group').first().attr('hidden', true);
                            $('#' + optionsId + '-tree-edit-input').val(null);
                            $(elthis).off();
                            $(elthat).off();
                            runFunction();
                        }
                    }
                    if (options.afterInit) {
                        options.afterInit(dataCollection);
                    }
                }

                function initHTML(fieldId, options) {
                    if (options.beforeInit) {
                        options.beforeInit(dataCollection);
                    }
                    if (options.afterInit) {
                        options.afterInit(dataCollection);
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
                        options.afterInit(dataCollection);
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
                                that._error('Datatable ' + datatableTable + ' is defined, but no configuration assigned to it!')
                            }
                        }
                        // this._fieldsToDatatable(fieldId);
                    } else {
                        that._error('Tables not assigned to ' + thisFieldId + '. They need to be assigned in an array, please see documentation');
                    }
                    if (options.afterInit) {
                        options.afterInit(dataCollection);
                    }
                }
            };

            BazContentFields._jQueryInterface = function _jQueryInterface(options) {
                var data = $(this).data(DATA_KEY);

                var _options = $.extend({}, Default, options);

                if (!data) {
                    data = new BazContentFields($(this), _options);
                    $(this).data(DATA_KEY, typeof _options === 'string' ? data : _options);
                }
            };

        return BazContentFields;

        }();

    $.fn[NAME] = BazContentFields._jQueryInterface;
    $.fn[NAME].Constructor = BazContentFields;

    $.fn[NAME].noConflict = function () {
        $.fn[NAME] = JQUERY_NO_CONFLICT;
        return BazContentFields._jQueryInterface;
    };

    return BazContentFields;
}(jQuery);

exports.BazContentFields = BazContentFields;

Object.defineProperty(exports, '__esModule', { value: true });

}));
/* exported BazContentFieldsValidator */
/* globals BazContentFields */
/*
* @title                    : BazContentFieldsValidator
* @description              : Baz Content Fields Validator Lib
* @developer                : guru@bazaari.com.au
* @usage                    : BazContentFieldsValidator._function_(_options_);
* @functions                :
* @options                  :
*/

var _extends = Object.assign || function (target) { 'use strict'; for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
// var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var BazContentFieldsValidator = function() {
    'use strict';
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
        sectionsJsTreeSelector;
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
        errorSound = new Audio(dataCollection.env.soundPath + 'swal.mp3'); //Error Sound for Swal
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
                    ignore: ":submit, :reset, :image, :disabled",
                    onkeyup: false,
                    onclick: false,
                    submitHandler: function() { },
                    focusInvalid: false
                });
                validateOptions = {
                    errorElement: 'div',
                    errorPlacement: function ( error, element ) {
                        element.parents('.form-group').append(error);
                        error.addClass('text-uppercase text-danger text-xs help-block');
                        $(element).closest('.form-group').addClass('has-feedback');
                    },
                    highlight: function (element) {
                        $(element).closest('.form-group').addClass('has-error');
                    },
                    // unhighlight: function (element) { },
                    success: function (element) {
                        var type = $(element).parents('form').data('validateon');
                        var formId = $(element).parents('form')[0].id;
                        componentId = $(element).parents('.component')[0].id;
                        sectionId = $(element).parents('.sectionWithForm')[0].id;
                        $(element).closest('.form-group').removeClass('has-error');
                        $(element).closest('.help-block').remove();
                        validateForm(componentId, sectionId, true, type, true, formId);
                    }
                };
                if (dataCollection[componentId][sectionId][sectionId + '-form']) {
                    validateOptions = _extends(validateOptions, dataCollection[componentId][sectionId][sectionId + '-form']);
                }

                dataCollection[componentId][sectionId]['formValidator'] = $(form).validate(validateOptions);//init validate form

                if ($(form).data('validateon') === 'sections') {
                    validateForms[componentId][sectionId].push(formId);
                }
                if ($(form).data('validateon') === 'datatable') {
                    validateFormsOnDatatable.push(formId);
                }
            });
            if ($('div[data-validateon="sections"]').length !== 0) {
                $('div[data-validateon="sections"]').each(function (index, datatable) {
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
            if (type === 'component') {
                formLocation = componentId;
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
            } else if (type === 'sections') {
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
                if (type === 'component') {
                    if (sectionsJsTreeSelector) {
                        // BazContentFields.fixHeight('fixedHeight');
                        $(sectionsJsTreeSelector).jstree(true).settings.search.search_callback = function(str, node) {
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
                        $(sectionsJsTreeSelector).jstree(true).refresh();
                        $('#' + formLocation + '-sections-tree').children('.card').removeClass('box-primary').addClass('box-danger');
                        $('#' + formLocation + '-sections-tree').find('.card-header').children('strong').html(' Errors');
                        $('#' + formLocation + '-sections-tree').find('.card-tools').addClass('hidden');
                        $('#' + formLocation + '-sections-tree').find('.widget-icon').children('i').removeClass('fa-bars').addClass('fa-ban');
                        $(sectionsJsTreeSelector).jstree(true).search(hasError.toString());
                        $('#' + formLocation + '-sections-jstree').find('.jstree-anchor').addClass('text-danger').css("text-transform", 'uppercase');
                        $('#' + formLocation + '-sections-fields-search').val(hasError.toString());
                        $('#' + formLocation + '-sections-fields-search').siblings('.input-group-addon').addClass('hidden');
                        $('#' + formLocation + '-sections-fields-search').siblings('.input-group-btn').removeClass('hidden');
                        $('#' + formLocation + '-sections-fields-search').attr('disabled', true);
                        $('#' + formLocation + '-sections-fields-search-cancel').click(function() {
                            cancelValidatingForm(type, formLocation, false, formId);
                        });
                    }
                    $('#' + formLocation + '-alert-dismiss').click(function() {
                        cancelValidatingForm(type, formLocation, false, formId);
                    });
                    return false;
                } else if (type === 'sections') {
                    $('#' + formLocation + '-alert-dismiss').click(function() {
                        formLocation = $(this).parent().siblings('.sectionWithForm')[0].id;
                        cancelValidatingForm(type, formLocation, false, formId);
                    });
                    return false;
                } else if (type === 'datatable') {
                    $('#' + formLocation + '-alert-dismiss').click(function() {
                        formLocation = $(this).parent().siblings('.sectionWithForm')[0].id;
                        cancelValidatingForm(type, formLocation, false, formId);
                    });
                    return false;
                }
            } else {
                if (type === 'datatable') {
                    return true;
                }
                return true;
            }
        } else {
            if (type === 'component') {
                hasErrorCount = $('#' + formLocation).find('.has-error').length;
                hasError = [];
                $('#' + formLocation).find('.has-error').each(function(index,errorId) {
                    var id = $(errorId).children('label').html();
                    hasError.push(id.toUpperCase());
                });
                if (hasErrorCount > 0) {
                    $('#' + formLocation + '-alert').find('strong').html(hasErrorCount);
                    if (sectionsJsTreeSelector) {
                        $(sectionsJsTreeSelector).jstree(true).search(hasError.toString());
                        $('#' + formLocation + '-sections-fields-search').val(hasError.toString());
                        $('#' + formLocation + '-sections-jstree').find('.jstree-anchor').addClass('text-danger').css("text-transform", 'uppercase');
                    }
                    return false;
                } else {
                    if (!onSuccess) {
                        cancelValidatingForm(type, formLocation, false, formId);
                    } else {
                        cancelValidatingForm(type, formLocation, true, formId);
                    }
                    return true;
                }
            } else if (type === 'sections') {
                hasErrorCount = $('#' + sectionId).find('.has-error').length;
                hasError = [];
                $('#' + sectionId).find('.has-error').each(function(index,errorId) {
                    var id = $(errorId).children('label').html();
                    hasError.push(id.toUpperCase());
                });
                if (hasErrorCount > 0) {
                    $('#' + sectionId + '-alert').find('strong').html(hasErrorCount);
                    return false;
                } else {
                    if (!onSuccess) {
                        cancelValidatingForm(type, sectionId, false, formId);
                    } else {
                        cancelValidatingForm(type, sectionId, true, formId);
                    }
                    return true;
                }
            } else if (type === 'datatable') {
                if (hasErrorCount > 0) {
                    $('#' + formLocation + '-alert').find('strong').html(hasErrorCount);
                    return false;
                } else {
                    cancelValidatingForm(type, formLocation, false, formId);
                    return true;
                }
            }
        }
    }

    //Cancel validating form
    function cancelValidatingForm(type, formLocation, jstreeRefresh, formId) {
        $('#' + formLocation + '-alert').remove();
        if (type === 'component') {
            if (sectionsJsTreeSelector) {
                // BazContentFields.fixHeight('fixedHeight');
                $('#' + formLocation + '-sections-tree').children('.card').removeClass('box-danger').addClass('box-primary');
                $('#' + formLocation + '-sections-tree').find('.card-header').children('strong').html(' Sections');
                $('#' + formLocation + '-sections-tree').find('.card-tools').removeClass('hidden');
                $('#' + formLocation + '-sections-tree').find('.widget-icon').children('i').removeClass('fa-ban').addClass('fa-bars');
                $('#' + formLocation + '-sections-jstree').find('.jstree-anchor').css("text-transform", 'uppercase');
                $('#' + formLocation + '-sections-fields-search').val('');
                $(sectionsJsTreeSelector).jstree(true).search('');
                $('#' + formLocation + '-sections-fields-search').attr('disabled', false);
                $('#' + formLocation + '-sections-fields-search').siblings('.input-group-addon').removeClass('hidden');
                $('#' + formLocation + '-sections-fields-search').siblings('.input-group-btn').addClass('hidden');
                $(sectionsJsTreeSelector).jstree(true).settings.search.search_callback = function(str, node) {
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
                if (!jstreeRefresh && formId !== null) {
                    BazContentFields.redoSectionsJsTree();
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
                    $('#' + datatable + '-table-data').removeClass('border-danger').addClass('border-default');
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
        BazContentFieldsValidator.defaults = { };
        BazContentFieldsValidator.initValidator = function(options) {
            init(_extends(BazContentFieldsValidator.defaults, options));
            initValidator();
        }
        BazContentFieldsValidator.validateForm = function(options) {
            init(_extends(BazContentFieldsValidator.defaults, options));
            var validate = validateForm(options.componentId, options.sectionId, options.onSuccess, options.type, options.preValidated, options.formId);
            return validate;
        }
        BazContentFieldsValidator.cancelValidatingForm = function(options) {
            init(_extends(BazContentFieldsValidator.defaults, options));
            cancelValidatingForm(options.type, options.formLocation, options.jstreeRefresh, options.formId);
        }


    }

    setup(bazContentFieldsValidatorConstructor);

    return bazContentFieldsValidatorConstructor;
}();




// (function ($) {
//     'use strict';

//     var that,
//         thisOptions,
//         hasErrorCount, //Error counts to show during validation.
//         formLocation, //Location of form, either in section or in datatable.
//         validateFormsOnSections, //Validation of form on section submit
//         validateDatatableOnSections, //Validation of datatable on section submit
//         validateFormsOnDatatable, //Validate datatable form on datable submit
//         rootPath,
//         soundPath,
//         errorSound,
//         dataCollection,
//         componentId,
//         sectionsJsTreeSelector;

//     var hasError = []; //Validation, list of fields that has errors
//     var tableData = { }; //Datatable Data
//     var DataKey = 'bb.bazvalidator';

//     var Default = { };

//     // BazValidator Class Definition
//     // =========================
//     var BazValidator = function (element, options) {
//         thisOptions = options;
//         componentId = element[0].id;
//         that = this;
//         dataCollection = window['dataCollection'];
//         rootPath = dataCollection.rootPath;
//         soundPath = 'assets/application/dashboard/default/sounds/';
//         errorSound = new Audio(rootPath + soundPath + 'swal.mp3'); //Error Sound for Swal

//         this.initValidator(componentId, thisOptions);

//     };

//     //Throw error
//     BazValidator.prototype.error = function(errorMsg) {
//         throw new Error(errorMsg);
//     };

//     //Init validator on form
//     BazValidator.prototype.initValidator = function(componentId) {
//         //eslint-disable-next-line
//         console.log('validator Init');
//         var formId, validateOptions;
//         validateFormsOnSections = [];
//         validateDatatableOnSections = { };
//         validateFormsOnDatatable = [];
//         validateFormsOnSections = [];
//         if (!$.fn.validate) {
//             that.error('Validator not found!');
//         } else {
//             $('#' + componentId).find('form').each(function(index,form) {
//                 formId = $(form)[0].id;
//                 $.validator.setDefaults({
//                     debug: false,
//                     ignore: ":submit, :reset, :image, :disabled",
//                     onkeyup: false,
//                     onclick: false,
//                     submitHandler: function() { },
//                     focusInvalid: false
//                 });
//                 validateOptions = {
//                     errorElement: 'div',
//                     errorPlacement: function ( error, element ) {
//                         element.parents('.form-group').append(error);
//                         error.addClass('help-block');
//                         $(element).closest('.form-group').addClass('has-feedback');
//                     },
//                     highlight: function (element) {
//                         $(element).closest('.form-group').addClass('has-error');
//                     },
//                     // unhighlight: function (element) { },
//                     success: function (element) {
//                         var type = $('#' + element[0].id).parents('form').data('validateon');
//                         var formId = $('#' + element[0].id).parents('form')[0].id;
//                         $(element).closest('.form-group').removeClass('has-error');
//                         $(element).closest('.help-block').remove();
//                         that.validateForm(true, type, true, formId);
//                     }
//                 };
//                 if (dataCollection[componentId].form) {
//                     validateOptions = $.extend(validateOptions, dataCollection[componentId].form);
//                 }
//                 $(form).validate(validateOptions);//init validate form
//                 if ($(form).data('validateon') === 'sections') {
//                     validateFormsOnSections.push(formId);
//                 }
//                 if ($(form).data('validateon') === 'datatable') {
//                     validateFormsOnDatatable.push(formId);
//                 }
//             });
//             if ($('div[data-validateon="sections"]').length !== 0) {
//                 $('div[data-validateon="sections"]').each(function (index, datatable) {
//                     if (!validateDatatableOnSections[$(datatable).parents('section')[0].id]) {
//                         validateDatatableOnSections[$(datatable).parents('section')[0].id] = [ ];
//                         validateDatatableOnSections[$(datatable).parents('section')[0].id].push(datatable.id);
//                     } else {
//                         validateDatatableOnSections[$(datatable).parents('section')[0].id].push(datatable.id);
//                     }
//                 });
//             }
//         }
//     };

//     //Validate Sections on Submit
//     BazValidator.prototype.validateForm = function(onSuccess, type, preValidated, formId) {
//         if (type === 'sections' || !type) {
//             formLocation = componentId;
//         } else if (type === 'datatable') {
//             formLocation = formId;
//         }
//         if (!preValidated) {
//             if (type === 'sections') {
//                 $.each(validateFormsOnSections, function(index, form) {
//                     $('#' + form).submit();
//                 });

//                 if (!($.isEmptyObject(validateDatatableOnSections))) {
//                     //Validating datatable if empty, throw error
//                     for (var sections in validateDatatableOnSections) {
//                         if (validateDatatableOnSections[sections].length > 0) {
//                             $.each(validateDatatableOnSections[sections], function(index, datatable) {
//                                 if (!tableData[sections][datatable].data().any()) {
//                                     $('#' + datatable + '-table-div').addClass('form-group has-error has-feedback');
//                                     $('#' + datatable + '-table-data').removeClass('border-default').addClass('border-danger');
//                                     $('#' + datatable + '-table-error').remove();
//                                     $('#' + datatable).append(
//                                         '<div id="' + datatable + '-table-error" class="text-danger help-block">Table cannot be empty!</div>'
//                                     );
//                                 }
//                             });
//                         }
//                     }
//                 }
//             } else if (type === 'datatable') {
//                 $('#' + formId).submit();
//             }
//             hasError = [];
//             $('#' + formLocation).find('.has-error').each(function(index,errorId) {
//                 var id = $(errorId).find('label').html();
//                 hasError.push(id.toUpperCase());
//             });
//             hasErrorCount = hasError.length;
//             if (!preValidated && hasErrorCount > 0) {
//                 $('#' + formLocation + '-alert').remove();
//                 $('#' + formLocation).before(
//                 '<div id="' + formLocation + '-alert" class="alert alert-danger alert-dismissible animated fadeIn">' +
//                 '   <button id="' + formLocation + '-alert-dismiss" type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' +
//                 '   <i class="icon fa fa-ban"></i>You have <strong>'+ hasErrorCount + '</strong> errors! ' +
//                 '   Please fix these errors before submitting the data' +
//                 '<div>'
//                 );
//                 errorSound.play();
//                 if (type === 'sections') {
//                     if (sectionsJsTreeSelector) {
//                         that.fixHeight('fixedHeight');
//                         $(sectionsJsTreeSelector).jstree(true).settings.search.search_callback = function(str, node) {
//                             var word, words = [];
//                             var searchFor = str.toUpperCase().replace(/^\s+/g, '').replace(/\s+$/g, '');
//                             if (searchFor.indexOf(',') >= 0) {
//                                 words = searchFor.split(',');
//                             } else {
//                                 words = [searchFor];
//                             }
//                             for (var i = 0; i < words.length; i++) {
//                                 word = words[i];
//                                 if ((node.text || "").indexOf(word) >= 0) {
//                                     if (node.text === word) {
//                                         return true;
//                                     }
//                                 }
//                             }
//                             return false;
//                         }
//                         $(sectionsJsTreeSelector).jstree(true).refresh();
//                         $('#' + formLocation + '-sections-tree').children('.box').removeClass('box-primary').addClass('box-danger');
//                         $('#' + formLocation + '-sections-tree').find('.box-header').children('strong').html(' Errors');
//                         $('#' + formLocation + '-sections-tree').find('.box-tools').addClass('hidden');
//                         $('#' + formLocation + '-sections-tree').find('.widget-icon').children('i').removeClass('fa-bars').addClass('fa-ban');
//                         $(sectionsJsTreeSelector).jstree(true).search(hasError.toString());
//                         $('#' + formLocation + '-sections-jstree').find('.jstree-anchor').addClass('text-danger').css("text-transform", 'uppercase');
//                         $('#' + formLocation + '-sections-fields-search').val(hasError.toString());
//                         $('#' + formLocation + '-sections-fields-search').siblings('.input-group-addon').addClass('hidden');
//                         $('#' + formLocation + '-sections-fields-search').siblings('.input-group-btn').removeClass('hidden');
//                         $('#' + formLocation + '-sections-fields-search').attr('disabled', true);
//                         $('#' + formLocation + '-sections-fields-search-cancel').click(function() {
//                             that.cancelValidatingForm(type, formLocation, false, formId);
//                         });
//                     }
//                     $('#' + formLocation + '-alert-dismiss').click(function() {
//                         that.cancelValidatingForm(type, formLocation, false, formId);
//                     });
//                 } else if (type === 'datatable') {
//                     $('#' + formLocation + '-alert-dismiss').click(function() {
//                         that.cancelValidatingForm(type, formLocation, false, formId);
//                     });
//                     return false;
//                 }
//             } else {
//                 if (type === 'datatable') {
//                     return true;
//                 }
//                 return true;
//             }
//         } else {
//             hasErrorCount = $('#' + formLocation).find('.has-error').length;
//             if (type === 'sections' || !type) {
//                 hasError = [];
//                 $('#' + formLocation).find('.has-error').each(function(index,errorId) {
//                     var id = $(errorId).children('label').html();
//                     hasError.push(id.toUpperCase());
//                 });
//                 if (hasErrorCount > 0) {
//                     $('#' + formLocation + '-alert').find('strong').html(hasErrorCount);
//                     if (sectionsJsTreeSelector) {
//                         $(sectionsJsTreeSelector).jstree(true).search(hasError.toString());
//                         $('#' + formLocation + '-sections-fields-search').val(hasError.toString());
//                         $('#' + formLocation + '-sections-jstree').find('.jstree-anchor').addClass('text-danger').css("text-transform", 'uppercase');
//                     }
//                 } else {
//                     if (!onSuccess) {
//                         that.cancelValidatingForm(type, formLocation, false, formId);
//                     } else {
//                         that.cancelValidatingForm(type, formLocation, true, formId);
//                     }
//                 }
//             } else if (type === 'datatable') {
//                 if (hasErrorCount > 0) {
//                     $('#' + formLocation + '-alert').find('strong').html(hasErrorCount);
//                     return false;
//                 } else {
//                     that.cancelValidatingForm(type, formLocation, false, formId);
//                     return true;
//                 }
//             }
//         }
//     };

//     //Cancel validating form
//     BazValidator.prototype.cancelValidatingForm = function (type, formLocation, jstreeRefresh, formId) {
//         $('#' + formLocation + '-alert').remove();
//         if (!type || type === 'sections') {
//             if (sectionsJsTreeSelector) {
//                 that.fixHeight('fixedHeight');
//                 $('#' + formLocation + '-sections-tree').children('.box').removeClass('box-danger').addClass('box-primary');
//                 $('#' + formLocation + '-sections-tree').find('.box-header').children('strong').html(' Sections');
//                 $('#' + formLocation + '-sections-tree').find('.box-tools').removeClass('hidden');
//                 $('#' + formLocation + '-sections-tree').find('.widget-icon').children('i').removeClass('fa-ban').addClass('fa-bars');
//                 $('#' + formLocation + '-sections-jstree').find('.jstree-anchor').css("text-transform", 'uppercase');
//                 $('#' + formLocation + '-sections-fields-search').val('');
//                 $(sectionsJsTreeSelector).jstree(true).search('');
//                 $('#' + formLocation + '-sections-fields-search').attr('disabled', false);
//                 $('#' + formLocation + '-sections-fields-search').siblings('.input-group-addon').removeClass('hidden');
//                 $('#' + formLocation + '-sections-fields-search').siblings('.input-group-btn').addClass('hidden');
//                 $(sectionsJsTreeSelector).jstree(true).settings.search.search_callback = function(str, node) {
//                     var word, words = [];
//                     var searchFor = str.toUpperCase().replace(/^\s+/g, '').replace(/\s+$/g, '');
//                     if (searchFor.indexOf(',') >= 0) {
//                         words = searchFor.split(',');
//                     } else {
//                         words = [searchFor];
//                     }
//                     for (var i = 0; i < words.length; i++) {
//                         word = words[i];
//                         if ((node.text || "").indexOf(word) >= 0) {
//                             return true;
//                         }
//                     }
//                     return false;
//                 }
//                 if (!jstreeRefresh && formId !== null) {
//                     that.redoSectionsJsTree();
//                 }
//             }
//         } else if (type === 'datatable') {
//             if ($('#' + formLocation).find('div').is('[data-bazscantype="jstree"]')) {
//                 $('#' + formLocation).find('[data-bazscantype="jstree"]').removeClass('border-danger').addClass('border-default');
//                 $('#' + formLocation).find('[type="search"]').removeClass('border-danger');
//                 $('#' + formLocation).find('[type="search"]').siblings('.input-group-addon').removeClass('bg-danger').addClass('bg-default');
//             }
//         }
//         $('#' + formLocation).find('.form-group').each(function(i,v) {
//             $(v).removeClass('has-error has-feedback');
//         });
//         $('#' + formLocation).find('.help-block').each(function(i,v) {
//             $(v).remove();
//         });
//         //Cancel Validating datatable
//         for (var sections in validateDatatableOnSections) {
//             if (validateDatatableOnSections[sections].length > 0) {
//                 $.each(validateDatatableOnSections[sections], function(index, datatable) {
//                     $('#' + datatable + '-table-data').removeClass('border-danger').addClass('border-default');
//                 });
//             }
//         }
//     };

//     // Plugin Definition
//     // =================
//     function Plugin(option) {
//         return this.each(function () {
//             var $this = $(this);
//             var data  = $this.data(DataKey);

//             if (!data) {
//                 var options = $.extend({}, Default, $this.data(), typeof option === 'object' && option);
//                 $this.data(DataKey, (data = new BazValidator($this, options)));
//             }

//             if (typeof data === 'string') {
//                 if (typeof data[option] === 'undefined') {
//                     throw new Error('Option for bazValidator needs to be object and not string');
//                 }
//                 data[option]();
//             }
//         });
//     }

//     var old = $.fn.bazValidator;

//     $.fn.bazValidator             = Plugin;
//     $.fn.bazValidator.Constructor = BazValidator;

//     // No Conflict Mode
//     // ================
//     $.fn.bazValidator.noConflict = function () {
//         $.fn.bazValidator = old;
//         return this;
//     };

// })(jQuery);
