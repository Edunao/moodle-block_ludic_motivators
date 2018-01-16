define(['jquery', 'core/tree'], function ($, Tree) {
    var avatar = {
        init: function (params) {
            console.log('Avatar init', params);

            var that = this;

            this.params = params;

            this.obtained = this.params.obtained;
            this.newly_obtained = this.params.newly_obtained;

            // Convert SVG (<img>) in to raw SVG code (<svg>)
            this.convert_svg('img.svg.avatar');

            // Set visible the layer (items) that are previously and newly obtained
            $.each(this.obtained, function( index, value ) {
                layer = $('#avatar-picture #'+value);
                layer.css('visibility', 'visible');
            });

            $('#avatar-div').css('display', 'block')

            // Set visible the layer (items) that are newly obtained
            $.each(this.newly_obtained, function( index, value ) {
                layer = $('#element-picture #'+value);
                layer.css('visibility', 'visible');
            });

            $('#element-div').css('display', 'block')
        },

        /*
         * Convert SVG (<img>) in to raw SVG code (<svg>)
         */
        convert_svg : function(selector) {

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
