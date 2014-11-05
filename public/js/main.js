/* jslint node: true, es6: true */
/* global $, window, document, define */

'use strict';

require('bootstrap');
require('metisMenu');
require('phoenix');
require('jquery-cycle2');

var moment = require('moment');

$('#side-menu').metisMenu();

var sidebarCollapse = function(width, topOffset) {
  if (width < 768) {
        $('div.navbar-collapse').addClass('collapse');
        topOffset = 100;
    } else {
        $('div.navbar-collapse').removeClass('collapse');
    }
};

$(window).bind('load resize', function() {
    var topOffset = 51;
    var width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
    var height = (this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height;
    height = height - topOffset;
    if (height < 600) {
      if (height < 400) {
        height = height + 275;
      } else {
        height = height + 180;
      }
    }
    sidebarCollapse(width, topOffset);
    if (height < 1) height = 1;
    if (height > topOffset) {
        $('#page-wrapper').css('min-height', (height) + 'px');
    }
});

$('.sidebar[data-mini]').hover(function() {
  $(this).toggleClass('sidebar-mini');
  $('#page-wrapper').toggleClass('full-page');
  $('[data-toggle=sidebar-right]').toggleClass('collapsed');
});

if($('data-input-autosave').length) {
  $('data-input-autosave').phoenix({
    maxItems: 100,
    saveInterval: 5000,
    clearOnSubmit:'[data-form-autosave]',
    keyAttributes: ['tagName', 'id', 'name']
  });
  $('data-input-autosave').phoenix('start');
}

$(document).ready(function() {
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

