jQuery( document ).ready( function(){
    jQuery( "#posts-selector .post-container" ).each( function(){
        jQuery( this ).on( "click", function() {
            jQuery( this ).toggleClass( "active" );
        } );
    } );

    jQuery( "#send-button" ).on( "click", function(){
        posts = [];
        jQuery( "#posts-selector .active" ).each( function(){
            posts.push( jQuery( this ).attr( "id" ).split( "-" )[1] );
        } );

        if ( posts.length > 0 ) {
            if ( jQuery( "#promotator-composer #error" ).length ) { jQuery( "#promotator-composer #error" ).remove(); }

            if ( jQuery( "#receiving-role" ).val() !== undefined && jQuery( "#receiving-role" ).val().trim() != "" ) {
                if ( jQuery( "#promotator-composer #error" ).length ) { jQuery( "#promotator-composer #error" ).remove(); }

                if ( jQuery( "#email-template" ).val() !== undefined &&  jQuery( "#email-template" ).val().trim() != "" ) {
                    if ( jQuery( "#promotator-composer #error" ).length ) { jQuery( "#promotator-composer #error" ).remove(); }

                    if ( jQuery( "#subject" ).val() !== undefined && jQuery( "#subject" ).val().trim() != "" ) {
                        jQuery.ajax( {
                            url : ajaxurl,
                            type : "POST",
                            data : {
                                action : "prom_send_mailing",
                                receivers : jQuery( "#receiving-role" ).val().trim(),
                                template : jQuery( "#email-template" ).val().trim(),
                                posts : posts,
                                subject : jQuery( "#subject" ).val().trim()
                            },
                            success : function( response ){
                                response = JSON.parse( response );
                                if ( response == "sent" ) {
                                    if ( jQuery( "#promotator-composer #error" ).length ) { jQuery( "#promotator-composer #error" ).remove(); }
                                    if ( jQuery( "#promotator-composer #success" ).length ) { jQuery( "#promotator-composer #success" ).remove(); }
                                    jQuery( "#promotator-composer" ).append( "<div id='success'>It's sent!</div>" );

                                    setTimeout( function(){
                                        if ( jQuery( "#promotator-composer #success" ).length ) { jQuery( "#promotator-composer #success" ).remove(); }
                                    }, 2000 );
                                } else { console.log( response ); }
                            },
                            error : function( response ){}
                        } );
                    } else {
                        if ( jQuery( "#promotator-composer #error" ).length ) { jQuery( "#promotator-composer #error" ).remove(); }
                        jQuery( "#promotator-composer" ).append( "<div id='error'>Choose the subject</div>" );
                    }
                } else {
                    if ( jQuery( "#promotator-composer #error" ).length ) { jQuery( "#promotator-composer #error" ).remove(); }
                    jQuery( "#promotator-composer" ).append( "<div id='error'>Choose the template</div>" );
                }
            } else {
                if ( jQuery( "#promotator-composer #error" ).length ) { jQuery( "#promotator-composer #error" ).remove(); }
                jQuery( "#promotator-composer" ).append( "<div id='error'>Choose the receivers</div>" );
            }
        } else {
            if ( jQuery( "#promotator-composer #error" ).length ) { jQuery( "#promotator-composer #error" ).remove(); }
            jQuery( "#promotator-composer" ).append( "<div id='error'>Choose some posts firts</div>" );
        }
    } );
} );
