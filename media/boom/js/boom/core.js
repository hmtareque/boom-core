/**
@fileOverview Core CMS functionality.
*/
/**
@namespace
@name $
*/
$.extend({
	/**
	Namespace for core boom classes and functions.
	@static
	@class
	@name $.boom
	*/
	boom :
		/** @lends $.boom */
		{

		options: {},

		/**
		Boom bootstrap/setup. Initialises boom.config and does some browser detection.
		*/
		setup: function(){

			$.extend(this, { config: window.boomConfig });

			// reference boom from the site window too.
			top.$.boom = $.boom;
		},

		/**
		Initialise boom classes. Create top bar and UI.
		@param type FIXME: who knows?
		@param {Object} options Boom options. Extends and overrides defaults in boom.config.
		*/
		init: function( type, options ){

			this.type = type;

			( options ) && $.extend( this.config, options );
			this.options = options;

			var classes = $.boom.data.boomInit;

			if ( $.isArray(classes) ) {
				$.each(classes, function(){
					$.boom[this].init();
				});
			}

			$.boom.loader.init();

			$('#b-topbar').exists(function(){
				if ( $.boom.cookie.contains( 'navmenu' ) ) {
					$( '#boom-nav' ).hide();
				}
				$( '#boom-page-menu' ).on( 'click', function(){
					$( '#boom-nav' ).toggle();
					$.boom.cookie.toggle( 'navmenu');
				});

				var user_menu = {
					"Profile" : function(){
						var url = '/cms/profile';

						$.boom.dialog.open({
							'url': url,
							'title': 'User profile',
							callback: function() {
								data = $('#b-people-profile').serialize();
								data = data + '&avatar_id=' + $('.b-people-edit-avatar img').attr('data-asset-id');

								$.post(url, data)
									.done(function() {
										$.boom.growl.show('Profile updated');
									});
							},
							open: function() {
								$('.b-people-edit-avatar').on('click', function() {
									var avatar = $(this).find('img');

									$.boom.assets
										.picker({
											asset_rid : avatar.attr('data-asset-id'),
										})
										.done( function( rid ){
											avatar
												.attr('data-asset-id', rid)
												.attr('src', '/asset/view/'+rid+'/80/80');
										});
								});
							}
						});
					},
					"Logout" : function(){
						top.location = '/cms/logout';
					}
				};

				$('#boom-page-user-menu')
					.splitbutton({
						items: user_menu,
						width: 'auto',
						menuPosition: 'left',
						split: false
					});
			});

			if ( options === undefined || options.ui === undefined || options.ui ) {

				$('body').ui();
			}
		}
	}
});

$.extend($.boom,
	/** @lends $.boom */
	{
	/**
	Boom data storage.
	@class
	*/
	data: {
		benchmarks: {
			boom_setup_before_classes_load: (new Date).getTime()
		}
	}
});

$.extend($.boom,
	/** @lends $.boom */
	{
	/**
	@class
	*/
	editor: {
		state : function(state) {
			$.boom.loader.show();

			$.post('/cms/editor/state', {state: state}, function() {
				top.location.reload();
				$.boom.loader.hide();
			});
		}
	}
});

$.extend($.boom,
	/** @lends $.boom */
	{
	/**
	Boom cookie management.
	@class
	*/
	cookie : {

		/**
		@property ids
		*/
		ids: {},

		/** @function */
		init : function(){

			$.boom.log('Init cookie');

			this.config = $.boom.config.cookie;
			this.ids[ this.config.name ] = this.get( this.config.name ).split( this.config.delimiter );
		},

		/** @function */
		_set : function(name, val, expiredays){

			expiredays = expiredays || this.config.expiredays;

			var expiredate = new Date();

			expiredate.setDate(expiredate.getDate() + expiredays);

			document.cookie = name + '=' + escape(val) + ((expiredays == null) ? '' : ';expires=' + expiredate.toGMTString()) + ';path=' + this.config.path;

			$.boom.log('Set cookie var: ' + name + ' => ' + val);
		},

		/** @function */
		get : function(name){

			if (document.cookie.length){

				var start = document.cookie.indexOf(name + '=');

				if (start === -1) return '';

				start = start + name.length + 1;

				var end = document.cookie.indexOf(';', start);

				if (end === -1) end = document.cookie.length;

				return unescape(document.cookie.substring(start, end));
			}
			return '';
		},

		/** @function */
		add : function(id, name){

			var name = name ? name : this.config.name;

			var ids = ( this.ids[ name ] ) ? this.ids[ name ] : this.get( name ).split( this.config.delimiter );

			if ( $.inArray( id, ids ) !== -1 ) return;

			ids.push(id);

			this.ids[ name ] = ids;
			this._set( name || this.config.name, ids.join( this.config.delimiter ) );
		},

		/** @function */
		remove : function(id, name){

			if (!id) return;

			var name = name ? name : this.config.name;

			var ids = ( this.ids[ name ] ) ? this.ids[ name ] : this.get( name ).split( this.config.delimiter );

			for(var i in ids) ( ids[i] == id ) && ids.splice( i, 1 );

			this.ids[ name ] = ids;
			this._set( name || this.config.name, ids.join( this.config.delimiter ) );
		},

		/** @function */
		toggle : function(id, name){

			if (!id) return;

			var name = name ? name : this.config.name;

			var ids = ( this.ids[ name ] ) ? this.ids[ name ] : this.get( name ).split( this.config.delimiter );

			if ( $.inArray( id, ids ) !== -1 ) {

				this.remove( id, name );
			} else {
				this.add( id, name );
			};
		},

		/** @function */
		contains : function(id, name){

			if (!id) return;

			var name = name ? name : this.config.name;

			var ids = ( this.ids[ name ] ) ? this.ids[ name ] : this.get( name ).split( this.config.delimiter );

			if ( $.inArray( id, ids ) !== -1 ) {

				return true;
			} else {
				return false;
			};
		}
	}
});

(!window.console) && function(){
	window.console = /** @ignore */ { log: function(){}, debug: function(){}, error: function(){}, warning: function(){}, info: function(){} };
}();

$.extend($.boom,
	/** @lends $.boom */
	{
	/**
	Boom logging. Extends console logging.
	*/
	log : function(type, msg){

		if (!$.boom.config.logs.show) return;

		if (msg === undefined) {
			msg = type;
			type = 'info';
		}

		if ($.boom.config.logs.showTimes){

			$.boom.config.logs.times.push((new Date).getTime());
			var time = $.boom.config.logs.times[$.boom.config.logs.times.length - 1] - $.boom.config.logs.times[$.boom.config.logs.times.length - 2];

			if (time) $.boom.config.logs.totalTime += parseInt( time, 10 );
			else time = 0;

			msg += ' : ' + time + 'ms : ' + ($.boom.config.logs.totalTime) + 'ms';
		}

		var log;

		switch(type.toLowerCase()) {
			case 'debug': log = window.console.debug; break;
			case 'error': log = window.console.error; break;
			case 'warning': log = window.console.warning; break;
			case 'info': log = window.console.info; break;
			default: log = window.console.log;
		}

		// FIXME for webkit
		//log.apply(this, [msg]);
		console.log(msg);
	}

});

$.extend($.boom,
	/** @lends $.boom */
	{
	/**
	Boom history manager. Mostly used by the tag managers.
	* @class
	@static
	*/
	history : {

		_interval: 0,

		hashCallback : function( hash ) {

		},

		/**
		Initialise from boom.config.history
		*/
		init : function(){

			this.options = $.boom.config.history;
		},

		/**
		Route hash URLs to functions
		@param {Function} hashCallback Callback to invoke with current fragment identifier
		@param {Function} nohashCallback Callback to invoke if no current fragment identifier
		*/
		route : function(hashCallback, nohashCallback){

			this.hashCallback = hashCallback || function(){};
			this.nohashCallback = nohashCallback || function(){};

			this.current_hash = this._getHash();

			if (this.current_hash) {

				this.hashCallback(this.current_hash);
			}

			if (!this.current_hash) {
				this.nohashCallback();
			}

			this._checkHistory();
		},

		/**
		Invoke the hashCallback for the current fragment identifier.
		@param {String} hash fragment identifier from page URL
		@returns {Object} returns the hstory callback return value, to allow callback chaining.
		*/
		load : function(hash){

			this.current_hash = decodeURIComponent(hash.replace(/\?.*$/, ''));

			this._setHash(this.current_hash);

			var promise = this.hashCallback(this.current_hash);

			this._checkHistory();

			return promise;
		},

		/**
		Reload without changing the current fragment identifier.
		*/
		refresh : function(){

			this.load( this.current_hash );
		},

		/**
		Get the current fragment identifier.
		*/
		getHash : function(){
			return this._getHash();
		},

		/**
		Set the current fragment identifier.
		*/
		setHash : function(val){
			this._setHash(val);
		},


		/**
		Get the current fragment identifier from the window URL.
		*/
		_getHash : function(){
			return $.trim( top.location.hash.replace(/^.*#/, '') );
		},

		/**
		Set the current fragment identifier.
		@param {String} val A fragment identifier, without the # character.
		*/
		_setHash : function(val){

			top.location.hash = ( val == '' ) ? '' : '#' + val;
			$.boom.log( 'setting hash ' + top.location.hash );
		},

		/**
		FIXME: No idea what this does.
		*/
		_checkHistory : function(){

			var self = this;

			clearInterval(this.interval);

			this.interval = setInterval(function(){

				var current_hash = self._getHash();

				if(current_hash != self.current_hash) {
					self.current_hash = current_hash;
					self.hashCallback(current_hash.replace(/^#/, ''));
				}

			}, this.options.checkInterval);
		}
	}
});

$.fn.sload = function(url, successCallback) {

	if (!this.length) return this;

	var self = this;

	return $.ajax({
		type: 'GET',
		url: url,
		success: function(data, status, xhr){

			self.html( data );

			successCallback && successCallback.apply(self, [ xhr, status ]);
		}
	});
};

$.ajaxSetup({

	/**
	Default AJAX error handler.
	@function
	*/
	error: function(xhr, textStatus, error, callback) {

		// data is sent as a serialized string

		var showError = $.boom.config.errors.report, queryvar = /([^&=]+)=([^&]+)/g;

		while (match = queryvar.exec( decodeURIComponent( this.data ) )) {

			if ( match[1] == 'showAjaxError' && match[2] == 0 ) {

				showError = 0;
			}
		}

		setTimeout(function(){

			$.boom.loader.hide().hide('dialog');

			if ( showError ) {
				var errString;

				try
				{
					var error = $.parseJSON( xhr.responseText );
					var errString;

					if (error.type) {
						errString = error.type + ' : ';
					}
					errString = errorString + error.message;
				}
				catch (e) {}

				if (errString) {
					$.boom.dialog.alert('Error', 'Sorry, an unexpected error occured. Please try again.\n\n' + errString );
				}
			}

			(callback) && callback.apply();
		});
	}
});

$.extend($.boom,
	/** @lends $.boom */
	{
	/**
	Boom utils.
	@class
	@static
	*/
	util :
		/** @lends $.boom.util */
		{
		/** @function */
		cacheImages : function(images){

			if (!images) return;

			$.each(images, function(i){
				images[i] = new Image();
				images[i].src = this;
			});

			$.boom.log('Cache images : (' + images.length + ' total)');

			return this;
		},

		/**
		Create a tree widget for selecting pages.
		@function
		@returns {Promise} promise which notifies a page ID when a page is selected.
		*/
		page_tree : function( $element ){

			var self = this;
			var complete = new $.Deferred();

			var item_selected = function( $item ){

				$item
					.addClass( 'ui-state-active' )
					.parents( '.boom-tree' )
					.find( 'a.ui-state-active' )
					.not( $item )
					.removeClass( 'ui-state-active' );

			};

			var parent_treeConfig = $.extend({}, $.boom.config.tree, {
				toggleSelected: false,
				onClick: function( event ){

					event.preventDefault();

					var link = {};
					var $node = $(this);
					var uri = $node.attr('href');
					var page_rid = $node.attr('rel');

					link.title = $node.text();
					link.page_id = page_rid;
					link.url = uri;

					item_selected( $node );

					complete.notify( link );
				},
				onToggle: function( page_id ){
					$.boom.loader.show();

					var list_ready = $.Deferred();
					var children = $.ajax( {
						type: 'POST',
						url: '/page/children.json',
						data: {parent : page_id},
						dataType: 'json'
					} );
					children.done( function( data ) {

						var children = $('<ul></ul>');

						$( data ).each( function( i, item ){
							var li = $('<li></li>')
								.data( 'children', parseInt(item.has_children, 10) )
								.appendTo( children );
							$('<a></a>')
								.attr( 'id', 'page_' + item.id )
								.attr( 'href', item.url )
								.attr( 'rel', item.id )
								.text( item.title )
								.appendTo( li );
						});

						var parent_id = $( 'input[name=parent_id]' ).val();
						children.find( '#page_' + parent_id ).addClass( 'ui-state-active' );

						list_ready.resolve( { childList: children } );

						$.boom.loader.hide();
					});

					return list_ready;
				}
			});

			$element.tree('destroy').tree( parent_treeConfig );

			return complete;
		},

		/**
		@class
		@static
		@name $.boom.util.dom
		*/
		dom :
			/** @lends $.boom.util.dom */
			{
			/**
			Generate a unique ID for a DOM element
			@param {String} prefix Optional prefix. Defaults to 'boom-'
			*/
			uniqueId : function(prefix){

				prefix = prefix || 'boom-';
				var id;

				do {
					id = Math.floor(Math.random()*1000);
				} while($('#' + prefix + id).length);

				return (prefix + id);
			},
		},

		/**
		@class
		@static
		@name $.boom.util.url
		*/
		url :
			/** @lends $.boom.util.url */
			{
			/** @function */
			addQueryStringParams: function(data, returnURL){

				returnURL = returnURL || true;

				var match, params = {}, querystring = window.location.search.substring(1), queryparam = /([^&=]+)=([^&]+)/g;

				while (match = queryparam.exec(querystring)) {
					params[ decodeURIComponent( match[1] ) ] = match[2];
				}

				$.each(data, function(key, val){

					params[ key ] = val;
				});

				return !returnURL ? $.param( params ) : top.location.protocol + '//' + top.location.host + top.location.pathname + '?' + $.param( params );
			}
		}
	}
});

$.extend($.boom,
	/** @lends $.boom */
	{
	/**
	Boom growl notifications.
	@class
	@static
	*/
	growl : {

		/** @function */
		show : function(msg, sticky){

			$.jGrowl(msg, $.extend({}, $.boom.config.growl, {
				sticky: sticky,
				closer: false,
				open: function(elem, message){
					$(this).removeClass('ui-state-highlight').addClass('ui-state-default').find('.message').prepend('<span class="ui-icon ui-icon-check ui-helper-left" />');
				}
			}));
		},

		/** @function */
		hide : function(id){
			$('.jGrowl-notification').trigger('jGrowl.close');
		}

	}

});

$.extend($.boom,
	/** @lends $.boom */
	{
	/**
	Boom error reporting.
	@class
	@static
	*/
	errors : {

		/** @function */
		report : function(msg){

			var data = {
				location: window.location,
				browser: navigator.appName + ' ' + navigator.appVersion + ' ' + navigator.platform,
				useragent: navigator.userAgent,
				type: '404'
			};

			$.boom.dialog.open({
				url: '/get-error-report.php',
				title: 'Report an error',
				data: data,
				callback: function() {
					alert('clicked send!');
				}
			});
		}
	}
});

$.boom.setup();

$.extend($.boom.data, {
	boomInit: ['cookie', 'history']
});
