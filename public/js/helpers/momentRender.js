/* jslint node: true */
/* global window, document, define, $ */

'use strict';

var moment = require('moment');

$(document).ready(function() {
  moment.locale('id');
  $('[data-moment-render]').each(function(index){
    var $this = $(this);
    var $text = $this.text();
    var $time = moment($text);

    if ($time > moment().add(6, 'months')) {
      $this.text($time.format('Do MMMM'));
    } else if ($time > moment().add(3, 'days')) {
      $this.text($time.format('dddd, Do MMMM'));
    } else {
      $this.text($time.fromNow());
    }
  });
});
