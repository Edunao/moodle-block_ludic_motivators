define(['jquery', 'core/tree'], function ($, Tree) {
    var score = {
        init: function (params) {
            console.log('Score init', params);

            var that = this;

            this.params = params;

            this.previous_score = this.params.previous_score;
            this.new_score = this.params.new_score;

            $({someValue: this.previous_score}).animate({someValue: this.new_score}, {
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
