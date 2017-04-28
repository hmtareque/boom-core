(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager.AllAlbums = Backbone.View.extend({
        initialize: function(options) {
            this.albums = options.albums;

            this.template = _.template($('#b-assets-all-albums-template').html());

            this.listenTo(this.albums, 'change add remove', this.render);
        },

        render: function() {
            this.$el.html($(this.template({
                albums: this.albums
            })));

            return this;
        }
    });
}(jQuery, Backbone, BoomCMS));
