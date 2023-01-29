const mix = require("laravel-mix");
const plugins = "resources/plugins/";

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.styles(
    [
        plugins + "fontawesome-free-6/css/all.min.css",
        plugins + "sweetalert2-theme-bootstrap-4/bootstrap-4.min.css",
        plugins + "datatables-bs4/css/dataTables.bootstrap4.min.css",
        plugins + "datatables-responsive/css/responsive.bootstrap4.min.css",
        plugins + "select2/css/select2.min.css",
        plugins + "select2-bootstrap4-theme/select2-bootstrap4.min.css",
        plugins + "adminlte/dist/css/adminlte.min.css",
        "resources/css/app.css",
    ],
    "public/assets/css/app.css"
).version();

mix.scripts(
    [
        plugins + "jquery/jquery.min.js",
        plugins + "bootstrap/js/bootstrap.bundle.min.js",
        plugins + "jquery-validation/jquery.validate.min.js",
        plugins + "jquery-validation/additional-methods.min.js",
        plugins + "jquery-validation/localization/messages_id.js",
        plugins + "sweetalert2/sweetalert2.min.js",
        plugins + "datatables/jquery.dataTables.min.js",
        plugins + "datatables-bs4/js/dataTables.bootstrap4.min.js",
        plugins + "datatables-responsive/js/dataTables.responsive.min.js",
        plugins + "datatables-responsive/js/responsive.bootstrap4.min.js",
        plugins + "select2/js/select2.full.min.js",
        plugins + "chart.js/dist/chart.min.js",
        plugins + "adminlte/dist/js/adminlte.min.js",
        plugins + "crypto-js/crypto-js.min.js",
        plugins + "bs-custom-file-input/bs-custom-file-input.min.js",
        plugins + "lazy-control/postform.js",
        plugins + "lazy-control/buttonaction.js",
        "resources/js/app.js",
    ],
    "public/assets/js/app.js"
).version();
