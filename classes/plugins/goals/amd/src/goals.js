define(['jquery', 'jqueryui', 'core/tree'], function ($, ui, Tree) {
    var goals = {
        init: function () {
            console.log('Goals init');
            var that = this;

            $('#add-goal').on('click', function () {
                $('<div>Description de l\'objectif:<textarea id="new-goal"></textarea></div>').dialog({
                    title : 'Ajouter un objectif',
                    resizable: false,
                    height: 230,
                    width: 550,
                    modal: true,
                    buttons: {
                        "Ajouter l\'objectif": function () {
                            var new_goal = $('#new-goal').val();
                            $('#goals').append('<li><label><input type="checkbox" />'+new_goal+'</label></li>');
                            $(this).dialog("close");
                        },
                        "Annuler": function () {
                            $(this).dialog("close");
                        }
                    }
                });
            });

        }
    };
    return goals;
});
