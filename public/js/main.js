/* jslint node: true */
/* global $, window, document, define */

'use strict';

require('bootstrap');
require('bootstrap-validator');
require('bootstrap-toggle');

require('./globals/domLoad');
require('./globals/domReady');

require('./helpers/layoutMini');
require('./helpers/listToggler');
require('./helpers/multitags');
require('./helpers/momentRender');

console.log('ready to dispatch.');
