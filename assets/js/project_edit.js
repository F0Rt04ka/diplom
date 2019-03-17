import $ from 'jquery';

var $collectionHolder;

var $addPageButton = $('#project-add_page-btn');

$(document).ready(function() {
    $collectionHolder = $('#project_edit_pages');
    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addPageButton.on('click', function() {
        var index = $collectionHolder.data('index');
        var newForm = $collectionHolder.data('prototype').replace(/__name__/g, index);
        $collectionHolder.data('index', index + 1);
        $collectionHolder.append(newForm);

        $('[id^="project_edit_pages_"][id$="_number"]').each(function (i, elem) {
            $(elem).val(i + 1);
        });
    });
});