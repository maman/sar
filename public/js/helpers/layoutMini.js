/* jslint node: true */
/* global window, document, define, $ */

'use strict';

window.triggerLayoutMini = function() {
  setTimeout(function() {
    $('.sidebar').addClass('sidebar-mini');
    $('#page-wrapper').addClass('full-page');
    $('.sidebar-right').removeClass('collapsed');
  }, 4500);
  $('.sidebar[data-mini]').hover(function() {
    $(this).toggleClass('sidebar-mini');
    $('#page-wrapper').toggleClass('full-page');
    $('[data-toggle=sidebar-right]').toggleClass('collapsed');
  });
};

module.exports = window.triggerLayoutMini();
