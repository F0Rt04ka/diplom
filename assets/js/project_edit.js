import $ from 'jquery';

let $collectionHolder;

let $addPageButton = $('#project-add_page-btn');

function countIndexForCollection(i, item) {
    let $elem = $(item);
    let $collectionHolder = $('#' + $elem.data('blockId'));
    $collectionHolder.data('index', $collectionHolder.find('fieldset').length);
}

function clickHandlerAddCollectionItem() {
    let $btn = $(this);
    let blockId = $btn.data('blockId');
    let $collectionHolder = $('#' + blockId);
    let index = $btn.data('newIndex');
    let newCollectionItem = $collectionHolder.data('prototype')
        .replace(new RegExp($btn.data('prototypeLabel'), 'g'), '')
        .replace(new RegExp($btn.data('prototypeName'), 'g'), index);
    $btn.data('newIndex', index + 1);
    $collectionHolder.append(newCollectionItem);

    $collectionHolder
        .find('.collection_add_button[data-block-id!="' + blockId + '"]')
        .each(countIndexForCollection)
        .on('click', clickHandlerAddCollectionItem);
}

function clickHandlerDeleteCollectionItem() {
    let $btn = $(this);
    $btn.parent('div').remove()
}

$(document).ready(function () {
    $collectionHolder = $('#project_edit_pages');
    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addPageButton.on('click', function () {
        let index = $collectionHolder.data('index');
        let newForm = $collectionHolder.data('prototype').replace(/__name__/g, index);
        $collectionHolder.data('index', index + 1);
        $collectionHolder.append(newForm);

        $('[id^="project_edit_pages_"][id$="_number"]').each(function (i, elem) {
            $(elem).val(i + 1);
        });
    });

    $('.collection_add_button').on('click', clickHandlerAddCollectionItem);
    $('.collection_delete_button').on('click', clickHandlerDeleteCollectionItem);
    $('#project-save-btn').click(function () {
        $('form[name="project_edit"]').submit();s
    });
    $('#project-save-comments-btn').click(function () {
        $('form[name="comments"]').submit();
    });
});