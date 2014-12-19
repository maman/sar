/**
 * listToggler Class for SAR Application
 *
 * this file defines listToggler components for SAR Application.
 *
 * LICENSE: This source file is subject to version 2 of the GNU General Public
 * License that is avalaible in the LICENSE file on the project root directory.
 * If you did not receive a copy of the LICENSE file, please send a note to
 * 321110001@student.machung.ac.id so I can mail you a copy immidiately.
 *
 * @package components
 * @author Achmad Mahardi <321110001@student.machung.ac.id>
 * @copyright 2014 Achmad Mahardi
 */

/* jslint node: true */
/* global window, document, define, $ */

'use strict';

$(document).on('click.sar.list-toggle', '[data-list-toggle-target]', function(e) {
  var $this = $(this);

  $('[data-list-toggle-target]').not(this).each(function() {
    $(this).addClass('collapsed');
  });
  $this.toggleClass('collapsed');
  $('html, body').animate({
    scrollTop: $this.offset().top
  });
});
