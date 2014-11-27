/* jslint node: true */
/* global window, document, define, $ */

'use strict';

$(document).on('click.sar.list-toggle', '[data-list-toggle-target]', function(e) {
  var $this = $(this);

  $('[data-list-toggle-target]').not(this).each(function() {
    $(this).addClass('collapsed');
  });
  $this.toggleClass('collapsed');
  $('html, body').animate({
    scrollTop: $this.offset().top
  });
});
