/* jslint node: true */
/* global window, document, define, $ */

'use strict';

window.triggerLayoutMini = function() {
  if ($('.sidebar[data-mini]').length) {
    setTimeout(function() {
      $('.sidebar[data-mini]').addClass('sidebar-mini');
      $('#page-wrapper').addClass('full-page');
      $('.sidebar-right').removeClass('collapsed');
    }, 4500);
  }
};

module.exports = window.triggerLayoutMini();
