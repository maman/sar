/* jslint node: true */
/* global window, document, define, $, json */

'use strict';

var serializeJSON = require('jquery.serializeJSON');

$(document).on('click.sar.form-submit-complex', '[data-form-submit-complex]', function(e) {
  var $this = $(this);
  var $target = $($this.data('target'));

  if ($this.is('a')) e.preventDefault();

  console.dir($target.serializeJSON());
  // $.ajax({
  //   url: $target.attr('action'),
  //   type: $target.attr('method'),
  //   data: $target.serializeJSON()
  // }).done(function(msg) {
  //   console.dir(msg);
  // });
});
