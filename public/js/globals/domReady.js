/**
 * domReady Class for SAR Application
 *
 * this file contains routines that runs when DOM is ready.
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

var metisMenu = require('metisMenu'),
    pjax = require('jquery-pjax');

var domReady = function() {
  $(document).on('ready pjax:end', function() {
    $('#side-menu').metisMenu();
    if($('[data-multiline]').data('multiline') == 'disable') {
      $(this).keypress(function(e) {
        if(e.keyCode == 13) {
          e.preventDefault();
        }
      });
    }
    if ($('[data-load-background]').length) {
      var $proxy = $('[data-load-background]').data('proxyUrl'),
          addr = 'http://www.bing.com/HPImageArchive.aspx?';
      $.getJSON($proxy + addr, {
        format: 'js',
        idx: 0,
        n: 1
      }).done(function(data){
        $.each(data.images, function(key, val) {
          $('[data-load-background]').css('background-image', 'url(http://bing.com' + val.urlbase + '_1366x768.jpg)');
        });
        $('[data-load-background]').addClass('bg-loaded');
      });
    }
    $(document).pjax('a[data-pjax]', '#page-wrapper', {
      timeout: 10000,
      fragment: '#page-wrapper'
    });
    $(document).pjax('a[data-pjax-full]', '#wrapper', {
      timeout: 10000,
      fragment: '#wrapper'
    });
  });
};

module.exports = domReady();
