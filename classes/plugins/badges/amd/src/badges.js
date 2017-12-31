define(['jquery', 'core/tree'], function ($, Tree) {
    var badges = {
        init: function () {
            console.log('Badges init');
            var that = this;

            $('.ludic_motivators-badge:last-child img').each(function() {
                $(this).fadeIn();
            });
        }
    };
    return badges;
});
