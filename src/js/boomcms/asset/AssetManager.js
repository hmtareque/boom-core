(function(Backbone, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager = Backbone.View.extend({
        el: '#b-assets-manager',

        activeAsset: null,
        assets: new BoomCMS.Collections.Assets(),
        selection: new BoomCMS.Collections.Assets(),
        selectedClass: 'selected',

        /**
         * When the assets in the collection change (e.g. the page or filters are changed)
         *
         * If an asset is being viewed which isn't in the collection
         * Then view the first asset in the collection instead
         *
         * @returns {undefined}
         */
        assetsChanged: function() {
            if (this.activeAsset !== null && this.assets.get(this.activeAsset) === undefined) {
                var first = this.assets.at(0);

                if (first) {
                    first.trigger('view', first);
                }
            }
        },

        assetsUploaded: function() {
            this.router.navigate('', {trigger: true});
            this.uploader.assetUploader('reset');
            this.getAssets();
        },

        bind: function() {
            var assetManager = this;

            this.selection
                .on('add', function(asset) {
                    setTimeout(function() {
                        assetManager.getThumb(asset).addClass(assetManager.selectedClass);
                    }, 0);
                })
                .on('remove', function(asset) {
                    setTimeout(function() {
                        assetManager.getThumb(asset).removeClass(assetManager.selectedClass);
                    }, 0);
                })
                .on('reset', function() {
                    assetManager.$('.b-assets-thumbnail').removeClass(assetManager.selectedClass);
                });

            this.$el
                .on('submit', '#b-assets-search form', function(e) {
                    e.preventDefault();

                    var search = $(this).serializeArray();

                    assetManager.router.goToSearchResults(search);
                })
                .on('click', '#b-assets-selection-delete', function() {
                    assetManager.router.updateSelection(assetManager.selection, 'delete', {trigger: true});
                })
                .on('click', '#b-assets-selection-download', function() {
                    assetManager.router.updateSelection(assetManager.selection, 'download', {trigger: true});
                })
                .on('click', '#b-assets-select-all', function(e) {
                    e.preventDefault();

                    assetManager.selectAll();

                    $(this).blur();
                })
                .on('click', '#b-assets-select-none', function(e) {
                    e.preventDefault();

                    assetManager.selectNone();

                    $(this).blur();
                })
                .on('click', '#b-assets-selection-tag', function() {
                    assetManager.viewSelection('tags');
                })
                .on('click', '#b-assets-upload', function() {
                    assetManager.router.navigate('upload', {trigger: true});
                })
                .on('click', '#b-assets-search', function() {
                    $('#b-assets-filters').toggleClass('visible');
                    $(this).toggleClass('open');
                })
                .on('click', '[data-view]', function(e) {
                    e.preventDefault();

                    assetManager.router.goTo($(this).attr('data-view'));
                })                      
                .on('keydown', '.thumb', function(e) {
                    if (e.which === $.ui.keyCode.DELETE || e.which === $.ui.keyCode.BACKSPACE) {
                        e.preventDefault();
                        e.stopPropagation();

                        $(this).parent().data('model').destroy();
                    }
                })
                .on('keydown', function(e) {
                    if ($(e.target).is('input')) {
                        return;
                    }

                    if (
                        (e.which === $.ui.keyCode.DELETE || e.which === $.ui.keyCode.BACKSPACE)
                        && assetManager.selection.models.length > 0
                    ) {
                        assetManager.viewSelection(assetManager.selection, 'delete');
                    }

                    if (e.metaKey || e.ctrlKey) {
                        switch (e.which) {
                            case 65:
                                e.preventDefault();

                                (assetManager.selection.length === assetManager.assets.length) ?
                                    assetManager.selectNone()
                                    : assetManager.selectAll();

                                break;
                            case 70:
                                e.preventDefault();
                                assetManager.toggleSearch();
                                break;
                        }
                    }
                });

            this.uploader
                .assetUploader({
                    uploadFinished: function(e, data) {
                        assetManager.assetsUploaded(data.result);
                    },
                    uploadFailed: function() {
                        // Update asset list even though an error occurred
                        // For situations where multiple files were uploaded but one caused an error.
                        assetManager.getAssets();
                    }
                });
        },

        bindRoutes: function() {
            var assetManager = this,
                assets = this.assets;

            this.router
                .on('selection', function(assetIds, section) {
                    assetManager.selection.reset();

                    for (var i = 0; i < assetIds.length; i++) {
                        var asset = assets.get(assetIds[i]);

                        if (asset === undefined) {
                            asset = new BoomCMS.Asset({id: assetIds[i]});
                            asset.fetch({
                                success: function() {
                                    assetManager.selection.add(asset);
                                }
                            });
                        } else {
                            assetManager.selection.add(asset);
                        }
                    }

                    assetManager.viewSelection(section);
                })
                .on('route:viewAlbum', function(albumId) {
                    var album = assetManager.albums.get(albumId);

                    if (album) {
                        assetManager.viewAlbum(album);
                    }
                })
                .on('route:home', function() {
                    assetManager.showAlbums();
                })
                .on('viewSearchResults', function(params) {
                    assetManager.viewSearchResults(params);
                })
                .on('route', function(section) {
                    assetManager.setView(section);
                });

            Backbone.history.start();
        },

        getThumb: function(asset) {
            return this.$el.find('.b-assets-thumbnail[data-asset="' + asset.getId() + '"]').addClass('hello');
        },

        initialize: function(options) {
            var assetManager = this;

            this.albums = options.albums;
            this.$content = this.$('#b-assets-content');
            this.$viewAssetContainer = this.$('#b-assets-view-asset-container');
            this.$viewSelectionContainer = this.$('#b-assets-view-selection-container');
            this.uploader = this.$content.find('> .b-assets-upload .b-assets-upload-form').eq(0);

            this.$el.assetSearch({
                assets: this.assets
            });

            this.router = new BoomCMS.AssetManager.Router({assets: this.assets}); 

            this.filmroll = new BoomCMS.AssetManager.Filmroll({
                assets: this.assets
            }).render();

            this.listenTo(this.assets, 'select', this.select);
            this.listenTo(this.assets, 'view', this.viewAsset);
            this.listenTo(this.assets, 'reset', this.assetsChanged);

            this.listenTo(this.assets, 'destroy', function() {
                assetManager.router.navigate('', {trigger: true});
                assetManager.selection.reset();
            });

            this.listenTo(this.selection, 'reset update', this.toggleButtons);

            this.bind();
            this.bindRoutes();
        },

        showAlbums: function() {
            var view = new BoomCMS.AssetManager.AllAlbums({
                albums: this.albums
            });

            this.$('#b-assets-all-albums-container').html(view.render().$el);
        },

        selectAll: function() {
            var assetManager = this;

            this.assets.each(function(asset) {
                assetManager.selection.add(asset);
            });
        },

        selectNone: function() {
            this.selection.reset();
        },


        select: function(asset) {
            var selection = this.selection,
                method = selection.findWhere({id: asset.getId()}) ? 'remove' : 'add';

            selection[method](asset);
        },

        setView: function(section) {
            if (section === 'home') {
                section = '';
            }

            this.$el.attr('data-view', section);

            this.$('button[data-view]')
                .removeClass('active')
                .filter('[data-view="' + section + '"]')
                .addClass('active');

            if (section !== 'asset') {
                this.activeAsset = null;
            }
        },

        toggleButtons: function() {
            var $buttons = this.$('.b-assets-multi');

            $buttons.prop('disabled', this.selection.length ? false : true);
        },

        viewAlbum: function(album) {
            this.$el
                .assetSearch('setFilters', {
                    album: album.getId(),
                    page: null // Don't paginate albums
                })
                .assetSearch('getAssets');

            var view = new BoomCMS.AssetManager.ViewAlbum({
                model: album
            });

            this.$('#b-assets-view-album-container').html(view.render().$el);
        },

        viewAsset: function(asset, section) {
            this.activeAsset = asset;

            if (section === undefined) {
                section = 'info';
            }

            var view = new BoomCMS.AssetManager.ViewAsset({
                model: asset,
                assets: this.assets,
                router: this.router
            });

            this.router.navigate('asset/' + asset.getId() + '/' + section, {trigger: true});

            this.filmroll.select(asset);

            this.$viewAssetContainer.html(view.render(section).$el);
        },

        viewSearchResults: function(params) {
            this.$el
                .assetSearch('setFilters', params)
                .assetSearch('getAssets');
        },

        viewSelection: function(section) {
            var view = new BoomCMS.AssetManager.ViewSelection({
                selection: this.selection,
                assets: this.assets,
                router: this.router
            });

            this.filmroll.select(this.selection.models[0]);

            this.$viewSelectionContainer.html(view.render(section).$el);
        }
    });
}(Backbone, BoomCMS));
