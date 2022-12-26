$(function () {
    $('.file-small').on('click', function () {
        $('.file-small.active').removeClass('active');
        var currFile = $(this);
        $.ajax({
            url: '/ajax/file-info',
            data: 'id=' + currFile.attr('data-file'),
            method: 'POST',
            success: function (msg) {
                currFile.addClass('active');
                $('.file-info-block').html(msg);
            }
        });

    });

    $('.file-info-block').on('click', '.change-access', function () {
        var currFile = $(this);
        $.ajax({
            url: '/ajax/file-share',
            data: 'id=' + currFile.attr('file-id'),
            method: 'POST',
            success: function (msg) {
                $('.file-info-block').html(msg);
            }
        });
return false;
    });
});