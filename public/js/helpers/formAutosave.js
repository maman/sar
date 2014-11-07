/* jslint node: true */
/* global window, document, define */

'use strict';

var $ = require('jquery'),
    phoenix = require('phoenix');

window.formAutoSave = function() {
  if($('data-input-autosave').length) {
    $('data-input-autosave').phoenix({
      maxItems: 100,
      saveInterval: 5000,
      clearOnSubmit:'[data-form-autosave]',
      keyAttributes: ['tagName', 'id', 'name']
    });
    $('data-input-autosave').phoenix('start');
  }
};

module.exports = window.formAutoSave();
