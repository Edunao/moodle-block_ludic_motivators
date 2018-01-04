define(['jquery', 'core/tree'], function ($, Tree) {
    return {
        init: function (plugin, params) {
            require(['../../../../blocks/ludic_motivators/classes/motivators/'+plugin+'/amd/src/'+plugin], function(plugin) {
                plugin.init(params);
            });
        }
    };
});
