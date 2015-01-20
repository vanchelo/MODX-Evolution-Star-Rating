$(document).ready(function () {
    var rating = $('.star-rating-container');

    rating.on('click', 'a', function (e) {
        e.preventDefault();

        var vote = $(this).data('vote'),
            id = $(this).parents('ul').data('pid');

        $.ajax({
            url: 'assets/snippets/star_rating/connector.php',
            type: 'GET',
            data: {
                id: id,
                vote: vote
            },
            success: function (data) {
                if (data) {
                    rating.find('.msg').remove();
                    if (data.success === true && !data.error) {
                        rating.html(data.html);
                    }
                    rating.append('<div class="msg">' + data.message + '</div>');
                    rating.find('.mask').fadeOut(100, function () {
                        $(this).remove();
                    });
                    setTimeout(function () {
                        rating.find('.msg').fadeOut(1000)
                    }, 1500)
                } else {
                    alert('Неизвестная ошибка, повторите попытку еще раз');
                }
            },
            beforeSend: function () {
                rating.append('<div class="mask" />');
            }
        })
    });
});
