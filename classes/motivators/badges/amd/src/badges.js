define(['jquery', 'core/tree'], function ($, Tree) {
    var badges = {
        init: function (params) {
            console.log('Badges SVG init', params);

            var that = this;

            this.params = params;

            this.previously_obtained = this.params.previously_obtained;
            this.not_obtained = this.params.not_obtained;

            // Convert SVG (<img>) in to raw SVG code (<svg>)
            this.convert_svg('img.svg.avatar');

            //$('#avatar-picture').find('#calque00').css("visibility", "visible");

            // Set visible the layer (badges) that are previously obtained
            $.each(this.previously_obtained, function( index, value ) {
                layer = $('#avatar-picture #'+value);
                layer.css('visibility', 'visible');
            });

            // Set not visible the layer (badges) that are not obtained
            $.each(this.not_obtained, function( index, value ) {
                layer = $('#avatar-picture #'+value);
                layer.css('visibility', 'hidden');
            });

            $('#avatar-div').css('display', 'block');
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

    return badges;
});

