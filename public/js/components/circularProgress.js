/**
 * Circular Progress Bar for SAR Application
 *
 * this file defines circularProgress components for SAR Application.
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

require('jquery-circle-progress');

function renderCircle() {
  $('[data-render-circle]').each(function(index) {
    var $this = $(this);
    var $color = $this.data('color');
    var $size = $this.data('size');
    var $value = $this.data('percent')/100;

    $this.circleProgress({
      value: $value,
      size: $size,
      animation: true,
      fill: $color
    });
  });
}

$(document).on('ready pjax:end', function() {
  renderCircle();
});
