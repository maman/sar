/* jslint node: true */
/* global window, document, define, $ */

'use strict';

var select2 = require('select2');

$(document).on('click.sar.form-multi-target', '[data-multi-target-btn]', function(e) {
  var $this = $(this);
  var $target = $($this.data('target')).first();
  var $count = $($this.data('target')).length;
  var name = $target.find(':input:not(.select2-input)').attr('name');

  if ($this.is('a')) e.preventDefault();

  if ($($target).children('[data-form-multitag]')) {
    $($target)
      .children('[data-form-multitag]')
      .select2('destroy')
      .end();
    $target
      .clone()
      .appendTo($this.data('multiTargetContainer'))
      .find(':input:not(.select2-input)').attr('name', name + '-' + $count)
      .val('');
    window.multitags();
  } else {
    $target
      .clone()
      .appendTo($this.data('multiTargetContainer'))
      .find(':input:not(.select2-input)').attr('name', name + '-' + $count)
      .val('');
  }
});

$(document).on('click.sar.form-multi-target-close', '[data-multi-target-close]', function(e) {
  var $this = $(this);
  var $target = $this.parent();
  var $count = $target.parent().children().length;

  if ($this.is('a')) e.preventDefault();

  if ($($target).children('[data-form-multitag]')) {
    $($target)
      .children('[data-form-multitag]')
      .select2('destroy')
      .end();
  }
  $target.remove();
  console.log($count);
});
