/* jslint node: true */
/* global window, document, define */

'use strict';

var $ = require('jquery');

window.listToggler = function() {
  if($('[data-list-toggle]').length) {
    $('[data-list-toggle-target]').click(function(e) {
      e.stopPropagation();
      e.stopImmediatePropagation();
      e.preventDefault();
      $(this).toggleClass('collapsed');
      $('html, body').animate({
        scrollTop: $(this).offset().top
      });
    });
  }
};

module.exports = window.listToggler();
