/* jslint node: true */
/* global window, document, define, $ */

var backbone = require('backbone');
var _ = require('underscore');

var Agenda = backbone.Model.extend({
  defaults: {
    title: '',
    completed: false
  }
});
