define('{@nameHyphen}:views/admin/my-settings', ['views/settings/record/edit'], function (Dep) {

    return Dep.extend({

        detailLayout: [
            {
                rows: [
                    [
                        {
                            name: 'myParameter'
                        },
                        false
                    ]
                ]
            }
        ],

        // Dynamic logic can de defined.
        dynamicLogicDefs: {},

        setup: function () {
            Dep.prototype.setup.call(this);

            // Some custom logic can be written here.
        },
    });
});
