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

        /*display_next_piece: function () {
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
            console.log('reveal piece', id);

            if ($('#piece-'+id).length == 0) {
                console.log('Piece not found : #piece-' + id);
            }
            else {
                $('#piece-'+id).css({fill : 'transparent', transition: "1.2s"});

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
        }*/
    };
    return badges;
});

