define(['jquery', 'core/tree'], function ($, Tree) {
    var avatar = {
        init: function (params) {
            console.log('Avatar init', params);

            var that = this;

            this.params = params;

            this.revealed_pieces = this.params.revealed_pieces;

            this.nb_cols = 4;
            this.unrevealed_pieces = [];
            for (var x = 1; x <= this.nb_cols; x++) {
                for(var y = 1;y <= this.nb_cols; y++) {
                    if (this.revealed_pieces.indexOf(x+'-'+y) === -1) {
                        this.unrevealed_pieces.push(x+'-'+y);
                    }
                }
            }

            // Convert SVG (<img>) in to raw SVG code (<svg>)
            this.convert_svg('img.svg.avatar');

            for (var i in this.revealed_pieces) {
                that.reveal_piece(this.revealed_pieces[i]);
            }

            $('#avatar-picture').show();

            /*$('#next-piece').on('click', function() {
                that.display_next_piece();
            });*/
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
        }
    };
    return avatar;
});
