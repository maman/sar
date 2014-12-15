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

var metisMenu = require('metisMenu');

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
  });
};

module.exports = domReady();
