const $ = require('jquery');
window.jQuery = window.$ = $;

$.expr[':'].regex = function(elem, index, match) {
    let matchParams = match[3].split(','),
        validLabels = /^(data|css):/,
        attr = {
            method: matchParams[0].match(validLabels) ?
                matchParams[0].split(':')[0] : 'attr',
            property: matchParams.shift().replace(validLabels,'')
        },
        regexFlags = 'ig',
        regex = new RegExp(matchParams.join('').replace(/^\s+|\s+$/g,''), regexFlags);
    return regex.test(jQuery(elem)[attr.method](attr.property));
};

const routes = require('../../public/js/fos_js_routes.json');
const Routing = require('../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js');
Routing.setRoutingData(routes);
window.Routing = Routing;

require('jquery-form');
require('bootstrap');
require('./project_edit');
require('./modal_links_config');
require('./comments');

require('../css/app.less');
