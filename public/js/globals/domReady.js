/* jslint node: true */
/* global window, document, define */

'use strict';

var $ = require('jquery'),
    metisMenu = require('metisMenu');

var domReady = function() {
  $(document).ready(function() {
    $('#side-menu').metisMenu();
    if($('[data-multiline]').data('multiline') == 'disable') {
      $(this).keypress(function(e) {
        if(e.keyCode == 13) {
          e.preventDefault();
        }
      });
    }
  });
};

module.exports = domReady();
