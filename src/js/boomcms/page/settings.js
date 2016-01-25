$.widget('boom.pageSettings', {
	bind: function() {
		var pageSettings = this;

		this.$menu
			.on('click', 'a', function(e) {
				e.preventDefault();
			})
			.on('click', '.b-page-settings-close', function() {
				pageSettings.close();
			})
			.on('click', 'a[data-b-page-setting]', function() {
				pageSettings.show($(this).attr('data-b-page-setting'));
			});
	},

	close: function() {
		if (typeof(this.options.close) === 'function') {
			this.options.close();
		} else {
			this.element.hide();
		}
	},

	_create: function() {
		this.page = this.options.page;
		this.$menu = this.element.find('.b-page-settings-menu');
		this.$content = this.element.find('.b-page-settings-content');

		this.bind();
	},

	getUrl: function(section) {
		switch (section) {
			case 'urls':
				return '/boomcms/page/' + this.page.id + '/urls';
			case 'relations':
				return '/boomcms/page/' + this.page.id + '/relations';
			case 'tags':
				return '/boomcms/page/' + this.page.id + '/tags';
			case 'template':
				return '/boomcms/page/' + this.page.id + '/version/template';
			case 'drafts':
				return '/boomcms/page/' + this.page.id + '/version/status';
			default:
				return '/boomcms/page/' + this.page.id + '/settings/' + section;
		}
	},

	show: function(section) {
		var pageSettings = this,
			$div = $('<div class="b-page-settings-content"></div>');

		this.$menu
			.find('li')
			.removeClass('selected')
			.end()
			.find('a[data-b-page-setting=' + section + ']')
			.parent('li')
			.addClass('selected');

		this.$content.replaceWith($div);
		this.$content = $div;

		this.$content.load(this.getUrl(section), function() {
			var widget = 'pageSettings' + section.ucfirst();

			pageSettings.$content.ui();

			if (typeof(pageSettings.$content[widget]) !== 'function') {
				widget = 'pageSettingsDefault';
			}

			pageSettings.$content[widget]({
				page: pageSettings.page,
				section: section,
				settings: pageSettings,
				done: function(event, data) {
					pageSettings._trigger('save', event, data);
					pageSettings._trigger(section + 'Save', event, data);
				}
			});
		});
	}
});