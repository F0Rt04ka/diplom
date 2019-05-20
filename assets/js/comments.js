$(document).ready(function () {
    $('#comments_dropdown').on('show.bs.dropdown', function () {
        $.ajax({
            'url': Routing.generate('project_comment_api', {'identifier': PROJECT_IDENTIFIER}),
            'type': 'GET',
            'success': function (data) {
                //TODO
                console.log(data);
            }
        });
    });
});