define(['jquery', 'core/tree'], function ($, Tree) {
    return {
        init: function (motivator, params) {
            require(['../../../../blocks/ludic_motivators/classes/motivators/'+motivator+'/amd/src/'+motivator], function(motivator) {
                motivator.init(params);
            });
        }
    };
});
