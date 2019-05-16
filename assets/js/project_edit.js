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

    handleCVSectionItemsFields();
}

function clickHandlerDeleteCollectionItem() {
    let $btn = $(this);
    $btn.parent('div').remove()
}

function handleCVSectionItemsFields()
{
    let typesWithCountFieldsMap = {
        item: ['Title', 'Info'],
        entry: ['year -- year', 'Degree', 'Institution', 'City', 'Grade', 'Description'],
        listitem: ['Title'],
        itemwithcomment: ['Title', 'Info', 'Comment'],
        listdoubleitem: ['Title 1', 'Ttile 2'],
        doubleitem: ['Title 1', 'Info 1', 'Title 2', 'Info 2']
    };
    $('div:regex(id, ^project_edit_main_page_sections_\\d+_items_\\d+$)').each(function (i, elem) {
        let $elem = $(elem);
        let selectNode = $elem.find('select[id$=type]');
        selectNode.on('change', handleCVSectionItemsFields);
        let fieldsInfo = typesWithCountFieldsMap[selectNode.val()];
        if (!fieldsInfo) {
            return;
        }
        let fields = $elem.find('input:regex(id, field\\d+$)');
        fields.each(function (index, input) {
            let $input = $(input);
            if (fieldsInfo[index] !== undefined) {
                $input.parent().css('display', 'unset');
                $('label[for=' + $input.attr('id') + ']').text(fieldsInfo[index]);
            } else {
                $input.parent().css('display', 'none');
                $input.val('');
            }
        });
    });

}

$(document).ready(function () {
    $('.collection_add_button').on('click', clickHandlerAddCollectionItem);
    $('.collection_delete_button').on('click', clickHandlerDeleteCollectionItem);
    $('#project-save-btn').click(function () {
        $('form[name="project_edit"]').submit();
    });
    $('#project-save-comments-btn').click(function () {
        $('form[name="comments"]').submit();
    });

    handleCVSectionItemsFields();
});