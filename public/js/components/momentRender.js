/**
 * momentRender Class for SAR Application
 *
 * this file defines momentRender components for SAR Application.
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

var moment = require('moment');

$(document).ready(function() {
  moment.locale('id');
  $('[data-moment-render]').each(function(index){
    var $this = $(this);
    var $text = $this.text();
    var $time = moment($text);

    if ($time > moment().add(6, 'months')) {
      $this.text($time.format('Do MMMM'));
    } else if ($time > moment().add(3, 'days')) {
      $this.text($time.format('dddd, Do MMMM'));
    } else {
      $this.text($time.fromNow());
    }
  });
});
