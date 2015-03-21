jQuery(document).ready(function ($) {
    $('.star-rating').raty({
        path: function() { return this.dataset.path || '/assets/snippets/star_rating/assets/img/' },
        starOn: function() { return this.dataset.on || 'star-on.png' },
        starOff: function() { return this.dataset.off || 'star-off.png' },
        starHalf: function() { return this.dataset.half || 'star-half.png' },
        number: function() { return this.dataset.stars || 5 },
        score: function() { return this.dataset.rating || 0 },
        readOnly: function() { return this.dataset.disabled == 1 },
        starType: function() { return this.dataset.type || 'img' },
        click: function (vote) {
            var rating = $(this),
                container = rating.closest('.star-rating-container'),
                id = this.dataset.id;

            $.ajax({
                url: window.location.href,
                type: 'get',
                data: {rid: id, vote: vote},
                success: function (data) {
                    if (data) {
                        container.find('.msg').remove();
                        if (data.success === true && !data.error) {
                            container.find('.star-rating-votes').text(data.votes);
                            container.find('.star-rating-rating').text(data.rating);
                            rating.raty('score', data.rating);
                            rating.raty('readOnly', true);
                        } else {
                            rating.raty('reload');
                        }
                        rating.append('<div class="msg">' + data.message + '</div>');
                        rating.find('.mask').fadeOut(100, function () {
                            $(this).remove();
                        });
                        setTimeout(function () {
                            container.find('.msg').fadeOut(1000)
                        }, 2000)
                    } else {
                        alert('Unknown error. Try again later');
                    }
                },
                beforeSend: function () {
                    rating.append('<div class="mask" />');
                }
            })
        }
    });
});
