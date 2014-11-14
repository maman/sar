/* jslint node: true */
/* global $, window, document, define */

'use strict';

require('bootstrap');
require('jquery-cycle2');

require('./globals/domLoad');
require('./globals/domReady');

require('./helpers/formAutosave');
require('./helpers/layoutMini');
require('./helpers/listToggler');
require('./helpers/multitags');
require('./helpers/formMultiTarget');
require('./helpers/formSubmit');

require('./pages/agenda.js');

console.log('ready to dispatch.');
