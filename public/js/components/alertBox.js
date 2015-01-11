/**
 * alertBox Class for SAR Application
 *
 * this file defines alertBox components for SAR Application.
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

var bootbox = require('bootbox');
var fun = require('../globals/globalFunction');

$(document).on('click.sar.alert-box', '[data-alert-box]', function(e) {
  var $this = $(this);
  var $type = $this.data('alertType');
  var $title = $this.data('alertTitle');
  var $template = $this.data('htmlTemplate');
  if ($this.is('a')) e.preventDefault();
  var box = bootbox.dialog({
    title: $title,
    message: $template,
    show: false
  });
  box.modal('show');
  fun.adjustModal();
});

$(document).on('click.sar.alert-box-single', '[data-alert-single]', function(e) {
  var $this = $(this);
  var $link = $this.data('link');
  var $msg = $this.data('alertMessage');
  if ($this.is('a')) e.preventDefault();
  bootbox.confirm($msg, function (result){
    if (result) {
      document.location=$link;
    }
  });
  fun.adjustModal();
});
