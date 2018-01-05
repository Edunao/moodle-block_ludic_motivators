define(['jquery', 'core/tree'], function ($, Tree) {
    var progress = {
        init: function () {
            console.log('Progress init');
            var that = this;

            $({someValue: 136}).animate({someValue: 143}, {
                duration: 1000,
                easing:'swing', // can be anything
                step: function() { // called on every steps
                    $('.progress-number').text(Math.ceil(this.someValue));
                }
            });

        }
    };
    return progress;
});
