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
    if($('[data-wizard]').length) {
      $('[data-wizard]').on('cycle-bootstrap cycle-next cycle-prev cycle-pager-activated', function(event, optionHash, API) {
        $('[data-wizard-step-title]').text($('[data-wizard-pages].active').data('stepTitle'));
        $('[data-wizard-step-explanation]').text($('[data-wizard-pages].active').data('stepExplanation'));
      });
    }
    if($('[data-input-autosave]').length) {
      $('[data-input-autosave]').phoenix('load');
      $('[data-form-autosave]').submit(function(e) {
        $('[data-input-autosave]').phoenix('remove');
      });
      $('[data-form-save]').click(function(e) {
        e.preventDefault();
        $('[data-input-autosave]').phoenix('save');
      });
      $('[data-form-destroy]').click(function(e) {
        e.preventDefault();
        $('[data-input-autosave]').phoenix('remove');
        $('[data-input-autosave]').val('');
      });
    }
  });
};

module.exports = domReady();
