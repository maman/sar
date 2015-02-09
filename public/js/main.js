/**
 * main javascript code for SAR Application
 *
 * this file contains the main javascript files required to construct SAR Application.
 *
 * LICENSE: This source file is subject to version 2 of the GNU General Public
 * License that is avalaible in the LICENSE file on the project root directory.
 * If you did not receive a copy of the LICENSE file, please send a note to
 * 321110001@student.machung.ac.id so I can mail you a copy immidiately.
 *
 * @author Achmad Mahardi <321110001@student.machung.ac.id>
 * @copyright 2014 Achmad Mahardi
 */

/* jslint node: true */
/* global $, window, document, define */

'use strict';

require('bootstrap');
require('bootstrap-validator');
require('bootstrap-toggle');

require('./globals/globalFunction');
require('./globals/domLoad');
require('./globals/domReady');

require('./components/alertBox');
require('./components/circularProgress');
require('./components/dataTables');
require('./components/datePicker');
require('./components/listToggler');
require('./components/multitags');
require('./components/momentRender');
require('./components/selfAssestCheck');

window.onpageshow = function(event) {
    if (event.persisted) {
        window.location.reload()
    }
};

console.log('ready to dispatch.');
