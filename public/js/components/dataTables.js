/**
 * dataTables Class for SAR Application
 *
 * this file defines dataTables components for SAR Application.
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

var tables = require('datatables-bootstrap3-plugin');

$(document).ready(function() {
  $('[data-render-table]').each(function(index) {
    var $this = $(this);
    $this.DataTable({
      'pagingType': 'simple_numbers'
    });
  });
});
