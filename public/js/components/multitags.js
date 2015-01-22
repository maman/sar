/**
 * multitags Class for SAR Application
 *
 * this file defines multitags components for SAR Application.
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

var select2 = require('select2');

function format(tags) {
  var originalOption = tags.element;
  if ($(originalOption).data('tagText')) {
    return $(originalOption).data('tagText') + ' (' + tags.text + ')';
  } else {
    return tags.text;
  }
}

window.multitags = function() {
  if($('[data-form-multitag]').length) {
    $('[data-form-multitag]').select2({
      formatResult: format,
      escapeMarkup: function(m) { return m; }
    });
  }
};

module.exports = window.multitags();
