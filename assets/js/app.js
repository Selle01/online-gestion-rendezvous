
import "@babel/polyfill";

/** css */
require('../css/app.css');
require('../css/style.css');
require('../css/components.css');
import 'datatables.net-bs4/css/dataTables.bootstrap4.min.css';
import 'datatables.net-select-bs4/css/select.bootstrap4.min.css';
import 'fullcalendar/dist/fullcalendar.min.css';


/** libraries */
import popper from 'popper.js';
require('bootstrap');
var $ = require('jquery');

/** config */
global.$ = global.jQuery = $;
global.popper = global.Popper = popper;
global.moment = require('moment');


/** template libraries */
require('./utils/stisla.js');
require('./utils/scripts.js');
import 'fullcalendar/dist/fullcalendar.js';
import 'datatables.net-bs4';
import 'datatables.net-select-bs4';
import 'datatables.net-responsive-bs4/css/responsive.bootstrap4.css';
import jsZip from 'jszip';
import 'pdfmake/build/pdfmake.js';
import * as pdfFonts from 'pdfmake/build/vfs_fonts';
import 'datatables.net-responsive-bs4/js/responsive.bootstrap4.js';
import 'datatables.net-buttons/js/buttons.colVis.min';
import 'datatables.net-buttons/js/dataTables.buttons.min';
import 'datatables.net-buttons/js/buttons.flash.min';
import 'datatables.net-buttons/js/buttons.html5.min';
import 'datatables.net-buttons/js/buttons.print.min';
window.JSZip = jsZip;
pdfMake.vfs = pdfFonts.pdfMake.vfs;

/** admin */
require('./admin/service.js');
require('./admin/specialty.js');
require('./admin/medecin.js');
require('./admin/secretary.js');
require('./admin/patient.js');
require('./admin/rendezVous.js');

$(document).ready(function () {
    $("[data-toggle='tooltip']").tooltip();
    $('[data-toggle="popover"]').popover({
        container: 'body'
    });
});
