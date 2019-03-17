const $ = require('jquery');

$(document).ready(function() {
    $('#project-save-btn').click(function () {
        $('form[name="project_edit"]').submit();
    });
});

require('../css/app.css');
require('bootstrap');
require('./project_edit');