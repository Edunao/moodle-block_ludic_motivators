/*define(['jquery', 'core/tree'], function ($, Tree) {
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
});*/

define(['jquery', 'core/tree'], function ($, Tree) {
    var avatar = {
        init: function (params) {
            console.log('Progress init', params);

            var that = this;

            this.params = params;

            this.revealed_layers = this.params.revealed_layers;

            // Convert SVG (<img>) in to raw SVG code (<svg>)
            this.convert_svg('img.svg.avatar');

            // Set visible the layer (items) that are newly obtained
            $.each(this.revealed_layers, function( index, value ) {
                layer = $('#branch-picture #'+value);
                layer.css('visibility', 'visible');
            });

            $('#branch-div').css('display', 'block')
        },

        convert_svg : function(selector) {
            //convert svg pictures
            $(selector).each(function () {
                var $img = $(this);
                var imgID = $img.attr('id');
                var imgClass = $img.attr('class');
                var imgURL = $img.attr('src');

                $.get({url : imgURL, async : false}, function (data) {
                    var $svg = $(data).find('svg');
                    if (typeof imgID !== 'undefined') {
                        $svg = $svg.attr('id', imgID);
                    }
                    if (typeof imgClass !== 'undefined') {
                        $svg = $svg.attr('class', imgClass + ' replaced-svg');
                    }
                    $svg = $svg.removeAttr('xmlns:a');
                    $img.replaceWith($svg);
                });
            });
        },
    };

    return avatar;
});

