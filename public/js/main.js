/* jslint node: true */
/* global $, window, document */

'use strict';

require('bootstrap');
require('metisMenu');

$('#side-menu').metisMenu();

$(window).bind('load resize', function() {
    var topOffset = 50;
    var width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
    var height = (this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height;
    if (width < 768) {
        $('div.navbar-collapse').addClass('collapse');
        topOffset = 100;
    } else {
        $('div.navbar-collapse').removeClass('collapse');
    }
    height = height - topOffset;
    if (height < 1) height = 1;
    if (height > topOffset) {
        $('#page-wrapper').css('min-height', (height) + 'px');
    }
});
