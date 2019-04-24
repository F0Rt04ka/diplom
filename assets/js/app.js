const $ = require('jquery');
window.jQuery = window.$ = $;

const routes = require('../../public/js/fos_js_routes.json');
const Routing = require('../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js');
Routing.setRoutingData(routes);
window.Routing = Routing;

require('jquery-form');
require('bootstrap');
require('../css/app.css');
require('./project_edit');
require('./modal_links_config');
