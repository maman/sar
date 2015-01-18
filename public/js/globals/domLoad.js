/**
 * domLoad Class for SAR Application
 *
 * this file contains routines that runs when DOM is loaded.
 *
 * LICENSE: This source file is subject to version 2 of the GNU General Public
 * License that is avalaible in the LICENSE file on the project root directory.
 * If you did not receive a copy of the LICENSE file, please send a note to
 * 321110001@student.machung.ac.id so I can mail you a copy immidiately.
 *
 * @package globals
 * @author Achmad Mahardi <321110001@student.machung.ac.id>
 * @copyright 2014 Achmad Mahardi
 */

/* jslint node: true */
/* global window, document, define, $ */

'use strict';

var fun = require('./globalFunction'),
    pjax = require('jquery-pjax');

var domLoad = function() {
  $(window).bind('load resize pjax:end', function() {
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
      fun.sidebarCollapse(width, topOffset);
      if ($(window).height() >= 320) {
        fun.adjustModal();
      }
      if (height < 1) height = 1;
      if (height > topOffset) {
          $('#framewrap').css('min-height', (height) + 'px');
          $('#page-wrapper').css('min-height', (height) + 'px');
          $('[data-mailbox-layout]').css('min-height', (height - 165) + 'px');
      }
  });
  if ($('.sidebar[data-mini]').length) {
    $('.sidebar[data-mini]').hover(function() {
      $(this).toggleClass('sidebar-mini');
      $('#page-wrapper').toggleClass('full-page');
      $('[data-toggle=sidebar-right]').toggleClass('collapsed');
    });
  }
  $(document).on('change', 'select[data-pjax]', function() {
    $.pjax({
      container: '#page-wrapper',
      fragment: '#page-wrapper',
      url: $('option:selected', this).data('url')
    });
  });
  if (window.domReady) {
    $(document).on('pjax:end', function() {
      window.domReady();
    });
  }
};

module.exports = domLoad();
