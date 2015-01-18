/**
 * datePicker Class for SAR Application
 *
 * this file defines datePicker components for SAR Application.
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

var datepicker = require('bootstrap-datepicker');

function renderDatePicker() {
  $('[data-render-datepicker]').each(function(index) {
    var $this = $(this),
        $format = $this.data('format'),
        $endDate = $this.data('endDate');
    $this.datepicker({
      'language': 'id',
      'format': $format,
      'endDate': $endDate
    });
  });
}

$(document).on('ready pjax:end', function() {
  renderDatePicker();
});
