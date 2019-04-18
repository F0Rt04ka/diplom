const $ = require('jquery');
require('jquery-form');
window.jQuery = window.$ = $;

const routes = require('../../public/js/fos_js_routes.json');
const Routing = require('../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js');
Routing.setRoutingData(routes);
window.Routing = Routing;

require('../css/app.css');
require('bootstrap');
require('./project_edit');
require('./modal_links_config');


$(document).ready(function() {
    $('#project-save-btn').click(function () {
        $('form[name="project_edit"]').submit();
    });
    $('form[name="project_link_collection"]')
        .ajaxForm({'url': Routing.generate('project_api', {'identifier': $('#project-links-save-btn').data('projectIdentifier')})});
    // $('#project-links-save-btn').click(function () {
    //     $('form[name="project_link_collection"]').submit();
    // });
});