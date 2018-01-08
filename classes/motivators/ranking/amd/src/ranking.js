define(['jquery', 'core/tree'], function ($, Tree) {
    var score = {
        init: function () {
            console.log('Score init');
            var that = this;

            $({someValue: 136}).animate({someValue: 143}, {
                duration: 1000,
                easing:'swing', // can be anything
                step: function() { // called on every steps
                    $('.score-number').text(Math.ceil(this.someValue));
                }
            });

        }
    };
    return score;
});
