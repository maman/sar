/* jslint node: true */
/* global window, document, define */

'use strict';

var $ = require('jquery');

var domLoad = function() {
  var sidebarCollapse = function(width, topOffset) {
    if (width < 768) {
          $('div.navbar-collapse').addClass('collapse');
          topOffset = 100;
      } else {
          $('div.navbar-collapse').removeClass('collapse');
      }
  };
  $(window).bind('load resize', function() {
      var topOffset = 51;
      var width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
      var height = (this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height;
      height = height - topOffset;
      if (height < 600) {
        if (height < 400) {
          height = height + 275;
        } else {
          height = height + 180;
        }
      }
      sidebarCollapse(width, topOffset);
      if (height < 1) height = 1;
      if (height > topOffset) {
          $('#page-wrapper').css('min-height', (height) + 'px');
      }
  });
};

module.exports = domLoad();
