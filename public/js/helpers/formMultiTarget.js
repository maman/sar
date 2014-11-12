/* jslint node: true */
/* global window, document, define, $ */

'use strict';

var select2 = require('select2');

$(document).on('click.sar.form-multi-target', '[data-multi-target-btn]', function(e) {
  var $this = $(this);
  var $target = $($this.data('target')).first();

  if ($this.is('a')) e.preventDefault();

  if ($($target).children('[data-form-multitag]')) {
    $($target)
      .children('[data-form-multitag]')
      .select2('destroy')
      .end();
    $target.clone().appendTo($this.data('multiTargetContainer'));
    window.multitags();
  } else {
    $target.clone().appendTo($this.data('multiTargetContainer'));
  }
});

$(document).on('click.sar.form-multi-target-close', '[data-multi-target-close]', function(e) {
  var $this = $(this);
  var $target = $this.parent();

  if ($this.is('a')) e.preventDefault();

  if ($($target).children('[data-form-multitag]')) {
    $($target)
      .children('[data-form-multitag]')
      .select2('destroy')
      .end();
  }
  $target.remove();
});
