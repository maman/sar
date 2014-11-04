/* jslint node: true, es6: true */
/* global $, window, document, define */

'use strict';

require('bootstrap');
require('metisMenu');
// require('jquery.steps');
require('jquery.easyWizard');

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
    var topOffset = 50;
    var width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
    var height = (this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height;
    height = height - topOffset;
    sidebarCollapse(width, topOffset);
    if (height < 1) height = 1;
    if (height > topOffset) {
        $('#page-wrapper').css('min-height', (height) + 'px');
    }
});

$('[data-toggle=sidebar-mini]').hover(function() {
  $(this).toggleClass('sidebar-mini');
  $('#page-wrapper').toggleClass('full-page');
  $('[data-toggle=sidebar-right]').toggleClass('collapsed');
});

$(document).ready(function() {
  if($('[data-wizard]').length) {
    $('[data-wizard]').easyWizard({
      // showSteps: false,
      showButtons: false,
      submitButton: false,
      after: function(wizardObj, prevStepObj, currentStepObj) {
        if($('[data-step].current').data('step') == '1') {
          $('[data-wizard-toggle=wizard-prev]').addClass('disabled');
        } else {
          $('[data-wizard-toggle=wizard-prev]').removeClass('disabled');
        }
        if($('[data-step].current').data('step') == $('[data-step]').last().data('step')) {
          $('[data-wizard-toggle=wizard-next]').addClass('disabled');
        } else {
          $('[data-wizard-toggle=wizard-next]').removeClass('disabled');
        }
        // console.log(currentStepObj);
        $('[data-wizard-step-title]').text($('section[data-step].active').data('stepTitle'));
        $('[data-wizard-step-explanation]').text($('section[data-step].active').data('stepExplanation'));
      }
    });
    $('[data-wizard-toggle]').click(function(e) {
      e.preventDefault();
      switch($(this).data('wizardToggle')){
        case 'wizard-prev':
          $('[data-wizard]').easyWizard('prevStep');
        break;
        case 'wizard-next':
          $('[data-wizard]').easyWizard('nextStep');
        break;
      }
    });
  }
});
