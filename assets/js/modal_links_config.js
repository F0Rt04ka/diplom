function insertRowInTable(linkData)
{
    let linksTable = $('#project-links-table');
    let dataRow = linksTable.data('prototype');
    $.each(linkData, function (property, value) {
        dataRow = dataRow.replace(new RegExp('__'+property+'__', 'g'), value)
    });
    linksTable.children('tbody:last').append(dataRow);

    $('.project-link-delete').click(function () {
        $.ajax({
            'url': Routing.generate('project_links_api', {'identifier': PROJECT_IDENTIFIER}),
            'type': 'DELETE',
            'data': {
                'linkIdentifier': $(this).data('linkIdentifier')
            },
            'success': function (data) {
                if (data) {
                    $('#project-link-'+data).remove();
                }
            }
        });
    });
}

$(document).ready(function () {
    $('#project_links_form').ajaxForm({
        'success': function (data) {
            if (data && data.result) {
                $.each(data.result, function (i, link) {
                    insertRowInTable(link);
                });
                $('#form_links').html('');
            }
        }
    });

    $('#modal-links-config').on('hide.bs.modal', function () {
        $('#project-links-table > tbody').html('');
        $('#form_links').html('');
    });

    $('#project-modal_links-btn'). click(function () {
        $('#modal-links-config').modal();
        $.ajax({
            'url': Routing.generate('project_links_api', {'identifier': PROJECT_IDENTIFIER}),
            'type': 'GET',
            'success': function (data) {
                $.each(data, function (i, link) {
                    insertRowInTable(link);
                });
            }
        });
    });
});