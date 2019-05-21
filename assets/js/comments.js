function initCommentsDropdownBlock()
{
    $('#comments_dropdown_content_exist_list').html('');
    $('#comments_dropdown_spinner').show();
    $('#comments_dropdown_content_exist').hide();
    $('#comments_dropdown_content_empty').hide();
}

$(document).ready(function () {
    initCommentsDropdownBlock();

    $('#comments_dropdown')
        .on('show.bs.dropdown', function () {
            $.ajax({
                'url': Routing.generate('project_comment_api', {'identifier': PROJECT_IDENTIFIER}),
                'type': 'GET',
                'success': function (data) {
                    if (!data.result.length) {
                        $('#comments_dropdown_spinner').hide();
                        $('#comments_dropdown_content_empty').show();
                        return;
                    }

                    data.result.forEach(function (elem) {
                        let newElem = document.createElement('a');
                        newElem.dataset.identifier = elem.identifier;
                        newElem.className = 'dropdown-item';
                        newElem.href = Routing.generate('project_view', {'identifier': elem.identifier});
                        newElem.target = 'blank';
                        newElem.innerText = 'Комментарии для версии №' + elem.project_version;
                        $('#comments_dropdown_content_exist_list').append(newElem);
                    });

                    $('#comments_dropdown_spinner').hide();
                    $('#comments_dropdown_content_exist').show();

                    $('#comments_dropdown_content_exist_list').children('a').on('click', function () {
                        $.ajax({
                            'url': Routing.generate('project_comment_api', {'identifier': PROJECT_IDENTIFIER}),
                            'type': 'PUT',
                            'data': {'linkIdentifier': this.dataset.identifier}
                        });
                    });
                }
            });
        })
        .on('hidden.bs.dropdown', initCommentsDropdownBlock);
});