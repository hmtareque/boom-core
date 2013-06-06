/**
@fileOverview Link editor dialog
*/
$.extend( $.boom,
	/** @lends $.boom */
	{
	/**
	Link manager
	@namespace
	*/
	links : {

		/**
		@function
		@example
		var link = {
			url : existing_link.href,
			rid : existing_link.rel,
			title : ( existing_link.textContent || existinglink.innerText )
		};
		opts.link = link;
		$.boom.links
			.picker( opts )
			.done( function( link ){
				existing_link.href = link.url;
				existing_link.rel = link.rid;
			});
		*/
		picker: function( opts ) {

			opts = ( opts ) ? opts : {};

			var complete = new $.Deferred();

			var link_manager_url =
				( opts.page_rid && typeof opts.page_rid != 'undefined' ) ?
				'/cms/chunk/insert_url/' + opts.page_rid :
				'/cms/chunk/insert_url';

			var default_opts =
			/** @ignore */ {
				title: 'Edit link',
				url: link_manager_url,
				deferred: complete,
				onLoad: function(){
					var self = this;

					if ( opts.link.rid == -1 || opts.link.rid == "" ) {
						$( '#boom-chunk-linkset-addlink-external-url' ).val( opts.link.url );
					}

					$.boom.util.page_tree( this.find( '.boom-tree' ) )
						.progress( function( page ) {
							link = page;

							complete.resolve( page );

							$.boom.dialog.destroy( self );
						});
				},
				callback: function(){

					if ( link.rid == -1 ) {
						var link_text = $( '#boom-chunk-linkset-addlink-external-url' ).val();
						link.url = link_text;
						link.title = link_text;
					}

					complete.resolve( link );
				},
				link : {
					title: '',
					rid: -1,
					url: ''
				}
			};

			opts = $.extend( default_opts, opts );

			var link = opts.link;

			$.boom.dialog.open( opts );

			return complete;
		}
	}
});