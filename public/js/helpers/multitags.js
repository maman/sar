/* jslint node: true */
/* global window, document, define */

'use strict';

var $ = require('jquery'),
    select2 = require('select2');

function format(tags) {
  var originalOption = tags.element;
  return $(originalOption).data('tagText') + ' (' + tags.text + ')';
}

window.multitags = function() {
  if($('[data-form-multitag]').length) {
    $('[data-form-multitag]').select2({
      formatResult: format,
      escapeMarkup: function(m) { return m; }
    });
    // $('[data-list-toggle-target]').click(function(e) {
    //   e.stopPropagation();
    //   e.stopImmediatePropagation();
    //   e.preventDefault();
    //   $(this).toggleClass('collapsed');
    //   $('html, body').animate({
    //     scrollTop: $(this).offset().top
    //   });
    // });
  }
};

module.exports = window.multitags();
