define(['jquery', 'core/tree'], function ($, Tree) {
    var avatar = {
        init: function (params) {
            console.log('Avatar init', params);

            var that = this;

            this.params = params;

            this.previously_obtained = this.params.previously_obtained;
            this.newly_obtained = this.params.newly_obtained;

            // Convert SVG (<img>) in to raw SVG code (<svg>)
            this.convert_svg('img.svg.avatar');

            //$('#avatar-picture').find('#calque05').css("visibility", "visible");

            // Set visible the layer (items) that are previously obtained
            $.each(this.previously_obtained, function( index, value ) {
                layer = $('#avatar-picture #'+value);
                layer.css('visibility', 'visible');
            });

            $('#avatar-div').css('display', 'block')

            // Set visible the layer (items) that are newly obtained
            $.each(this.newly_obtained, function( index, value ) {
                console.log(value);
                layer = $('#element-picture #'+value);
                console.log(layer);
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

        display_next_piece: function () {
            var rand = this.get_random_unrevealed_piece();

            if (rand) {
                this.reveal_piece(rand);
            }
            //image complete
            if (this.unrevealed_pieces.length == 0) {
                $('#next-piece').hide();
                $('#congratulation').show();
            }
        },

        reveal_piece : function(id) {
            console.log('reveal layer', id);

            if ($(id).length == 0) {
                console.log('Layer not found : ' + id);
            }
            else {
                $(id).css({visibility : 'visible'});

                //add piece to revealed pieces
                var index = this.revealed_pieces.indexOf(id);
                if (index === -1) {
                    this.revealed_pieces.push(id);
                }

                //remove piece from unrevealed pieces
                var index = this.unrevealed_pieces.indexOf(id);
                if (index !== -1) {
                    this.unrevealed_pieces.splice(index, 1);
                }
            }
        },

        get_random_unrevealed_piece : function() {
            var nb_pieces = this.unrevealed_pieces.length;
            if (nb_pieces === 0) {
                return false;
            }
            var random_index = Math.floor(Math.random() * nb_pieces);
            return this.unrevealed_pieces[random_index];
        }
    };
    return avatar;
});
