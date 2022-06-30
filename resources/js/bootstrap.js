//// JQUERY
import jQuery from 'jquery';
try {
    // window.$ = window.jQuery = require('jquery');
    window.$ = jQuery;
    window.jQuery = jQuery;
} catch (e) {
    console.log("error", e);
}

