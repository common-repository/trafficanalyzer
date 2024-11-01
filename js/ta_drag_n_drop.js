jQuery(function() {
		jQuery( ".column" ).sortable({
			connectWith: ".column"
		});

		
		jQuery( ".portlet" ).addClass( "ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" )
			.find( ".portlet-header" )
				.addClass( "ui-widget-header ui-corner-all" )
				.prepend( "<span class='ui-icon ui-icon-minusthick'></span>")
				.end()
			.find( ".portlet-content" );

		jQuery( ".portlet-header .ui-icon" ).click(function() {
			jQuery( this ).toggleClass( "ui-icon-minusthick" ).toggleClass( "ui-icon-plusthick" );
			jQuery( this ).parents( ".portlet:first" ).find( ".portlet-content" ).toggle();
		});

		//jQuery( ".column" ).disableSelection();
		
		jQuery( ".column" ).sortable( "option", "cursor", 'crosshair' );		
		jQuery( ".column" ).sortable( "option", "handle", '.portlet-header' );
		
	});
