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

            this.revealed_stages = this.params.revealed_pieces;

            // Initializing unrevealed_stages array
            this.nb_stages = 8;
            this.unrevealed_stages = [];
            for (var x = 1; x <= this.nb_stages; x++) {
                if (this.revealed_stages.indexOf('Etape'+x) === -1) {
                    this.unrevealed_stages.push('Etape'+x);
                }
            }

            // Convert svg pictures
            this.convert_svg('img.svg.avatar');

            for (var i in this.revealed_stages) {
                that.reveal_stage(this.revealed_stages[i]);
            }

            $('#avatar-picture').show();

            $('#next-stage').on('click', function() {
                that.display_next_piece();
            });
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

        display_next_piece: function () {
            var next = this.get_next_unrevealed_stage();

            if (next) {
                this.reveal_stage(next);
            }
            // Branch complete
            if (this.unrevealed_stages.length == 0) {
                $('#next-stage').hide();
                $('#congratulation').show();
            }
        },

        reveal_stage : function(id) {
            stageId = 'Branche1_'+id;
            console.log('reveal stage', stageId);

            if ($('#'+stageId).length == 0) {
                console.log('Stage not found : ' + stageId);
            }
            else {
                $('#'+stageId).css({visibility : 'visible', transition: "1.2s"});

                // Add stage to revealed stages
                var index = this.revealed_stages.indexOf(id);
                if (index === -1) {
                    this.revealed_stages.push(id);
                }

                // Remove stage from unrevealed stages
                var index = this.unrevealed_stages.indexOf(id);
                if (index !== -1) {
                    this.unrevealed_stages.splice(index, 1);
                }
            }
        },

        get_next_unrevealed_stage : function() {
            var nb_stages = this.unrevealed_stages.length;
            console.log(this.unrevealed_stages);
            if (nb_stages === 0) {
                return false;
            }
            //var next_index = Math.floor(Math.next() * nb_stages);
            return this.unrevealed_stages[0];
        }
    };
    return avatar;
});

