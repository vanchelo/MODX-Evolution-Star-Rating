$(document).ready(function () {
    $('.star-rating-container').on('click', 'a', function (e) {
        e.preventDefault();

        var link = $(this),
            container = link.closest('.star-rating-container'),
            vote = link.data('vote'),
            id = link.parents('ul').data('pid');

        $.ajax({
            url: 'assets/snippets/star_rating/connector.php',
            type: 'GET',
            data: {
                id: id,
                vote: vote
            },
            success: function (data) {
                if (data) {
                    container.find('.msg').remove();
                    if (data.success === true && !data.error) {
                        container.html(data.html);
                    }
                    container.append('<div class="msg">' + data.message + '</div>');
                    container.find('.mask').fadeOut(100, function () {
                        $(this).remove();
                    });
                    setTimeout(function () {
                        container.find('.msg').fadeOut(1000)
                    }, 1500)
                } else {
                    alert('Неизвестная ошибка, повторите попытку еще раз');
                }
            },
            beforeSend: function () {
                container.append('<div class="mask" />');
            }
        })
    });
});