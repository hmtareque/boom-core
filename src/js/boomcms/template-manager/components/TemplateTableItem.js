(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.TemplateManager.TemplateTableItem = Backbone.View.extend({
        tagName: 'tr',
        template: _.template($('#b-template-row').html()),

        events: {
            'click .delete': 'deleteTemplate',
            'change input': 'setAttribute',
            'keyup input': 'setAttribute',
            'paste input': 'setAttribute',
            'blur input': 'saveTemplate'
        },

        initialize: function() {
            this.listenTo(this.model, 'destroy', this.remove);
        },

        deleteTemplate: function(e) {
            e.preventDefault();

            var template = this.model;

            BoomCMS.Confirmation('Please confirm', 'Are you sure you want to delete the template \'' + template.getName() + '\'?')
                .done(function() {
                    template.destroy();    
                });
        },

        render: function() {
            this.$el
                .html(this.template({
                    template: this.model
                }));

            if (!this.model.fileExists()) {
                this.$el.addClass('b-templates-nofile');
            }

            return this;
        },

        saveTemplate: function() {
            if (this.model.needsSave === true) {
                this.model.save();
    
                BoomCMS.Notification('Template "' + this.model.getName() + '" has been saved');
            }
        },

        setAttribute: function(e) {
            var $el = $(e.target);

            this.model.set($el.attr('name'), $el.val());
        }
    });
}(jQuery, Backbone, BoomCMS));