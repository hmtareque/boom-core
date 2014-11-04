$.widget('ui.chunkAsset', $.ui.chunk, {
	editAssetOnly : function() {
		var chunkAsset = this;

		new boomAssetPicker(this.asset.assetId)
		.done(function(assetId) {
			chunkAsset.save({
				asset_id : assetId
			});
		})
		.fail(function() {
			chunkAsset.destory();
		});
	},

	editAllElements : function() {
		var chunkAsset = this;

		new boomChunkAssetEditor(this.options.page, this.options.name, {
			caption : this.elements.caption.length > 0,
			link : this.elements.link.length > 0,
			title : this.elements.title.length
		})
		.done(function(chunkData) {
			chunkAsset.save(chunkData);
		});
	},

	/**
	 @function
	 */
	 getElements: function() {
		var assetId = this.element.attr('data-boom-target');
		var elements = {};

		var img = this.element.find('img');
		var a = this.element.find('a');

		var regExp = new RegExp("asset\/(thumb|view|download)\/" + assetId);

		elements.asset = this.element.find('.asset-target');
		elements.link = this.element.hasClass('asset-link')? this.element : this.element.find('.asset-link');
		elements.caption = this.element.find('.asset-caption');
		elements.title = this.element.find('.asset-title');

		if (! elements.asset.length) {
			if (img.length && regExp.test(img.attr('src'))) {
				elements.asset = img;
			}
			else if (a.length && regExp.test(a.attr('href'))) {
				elements.asset = a;
			}

			if ( ! elements.asset.length) {
				elements.asset = this.element;
			}
		}

		if ( ! elements.link.length && a.length && elements.asset != a && a.attr('href') && a.attr('href') != '#' && ! regExp.test(a.attr('href'))) {
			elements.link = a;
		}

		return elements;
	 },

	edit : function() {
		this.elements = this.getElements();

		if (this.hasMetadata()) {
			this.editAllElements();
		} else {
			this.editAssetOnly();
		}
	},

	hasMetadata : function() {
		return (this.elements.caption.length || this.elements.link.length || this.elements.title.length);
	},

	save : function(data) {
		this._save(data);
		this.destroy();
	},
	
	_update_html : function(html) {
		var $html = $(html),
			$replacement = $($html[0]);

		if (this.elements.asset === this.element) {
			if (this.element.is('img')) {
				this.element.attr('src', $replacement.attr('src'));
			} else if ($replacement.css('background-image')) {
				this.element.css('background-image', $replacement.css('background-image'));
			} else {
				this.element.css('background', $replacement.css('background'));
			}
		}

		this.element.html($html.html());
		this.bind();
	}
});