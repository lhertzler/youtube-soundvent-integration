(function ( $ ) {
    'use strict';

    /**
     * All of the code for your public-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
	 *
	 * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
	 *
	 * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */
     jQuery( '.youtubesubmit' ).click( function ( e ) {


            ajaxindicatorstart( 'Connecting with YouTube...' );
            var inputvalue = jQuery( '.youtubeusername' ).val();
            //console.log( inputvalue );
            jQuery.ajax( {
                type: "post",
                url: myAjax.ajaxurl,
                data: {
                    action: "my_listing_data",
                    inputvalue: inputvalue
                },
                success: function ( response ) {
                    console.log( response );
                    if ( '' !== response ) {
                        //var htmldata = jQuery.trim(response);
                        //jQuery('.content-youtube-feed').html('');
                        //jQuery('#youtube_result').html(htmldata);
                        // similar behavior as an HTTP redirect
												alert("Success!");
                        window.location.reload();
                    }
                    else {
                        alert( "There was an error connecting with the channel.  Please check you have the correct YouTube ID and try again." );
                    }
                    ajaxindicatorstop();
                }
            } );
            return false;
        } );

     jQuery( document ).on( 'click', '#load-more-videos', function ( e ) {

            ajaxindicatorstart( 'Loading...' );
            var nextToken = jQuery( this ).attr( 'data-nextpagetoken' );
            console.log( nextToken );
            var channel_name_data = jQuery( this ).data( 'channel_name' );
            var button = jQuery( this );

            jQuery.ajax( {
                type: "post",
                //async : false,
                url: myAjax.ajaxurl,
                data: {
                    action: "load_more_video",
                    nextToken: nextToken,
                    channel_name: channel_name_data
                },
                success: function ( res ) {


                    var data = JSON.parse( res );

                    if ( '' !== data.html ) {

                        jQuery( '.video_container' ).append( data.html );
                    }
                    if ( data.next != '' ) {
                        button.attr( 'data-nextpagetoken', data.next );
                    } else if ( data.next == '0' ) {
                        button.hide();
                    }
                    ajaxindicatorstop();
                }
            } );
        } );


    function ajaxindicatorstart( text ) {
        if ( jQuery( 'body' ).find( '#resultLoading' ).attr( 'id' ) != 'resultLoading' ) {
            jQuery( 'body' ).append( '<div id="resultLoading" style="display:none"><div><div>' + text + '</div></div><div class="bg"></div></div>' );
        }

        jQuery( '#resultLoading' ).css( {
            'width': '100%',
            'height': '100%',
            'position': 'fixed',
            'z-index': '10000000',
            'top': '0',
            'left': '0',
            'right': '0',
            'bottom': '0',
            'margin': 'auto'
        } );

        jQuery( '#resultLoading .bg' ).css( {
            'background': '#000000',
            'opacity': '0.7',
            'width': '100%',
            'height': '100%',
            'position': 'absolute',
            'top': '0'
        } );

        jQuery( '#resultLoading>div:first' ).css( {
            'width': '250px',
            'height': '75px',
            'text-align': 'center',
            'position': 'fixed',
            'top': '0',
            'left': '0',
            'right': '0',
            'bottom': '0',
            'margin': 'auto',
            'font-size': '16px',
            'z-index': '10',
            'color': '#ffffff'

        } );

        jQuery( '#resultLoading .bg' ).height( '100%' );
        jQuery( '#resultLoading' ).fadeIn( 300 );
        jQuery( 'body' ).css( 'cursor', 'wait' );
    }

    function ajaxindicatorstop() {
        jQuery( '#resultLoading .bg' ).height( '100%' );
        jQuery( '#resultLoading' ).fadeOut( 300 );
        jQuery( 'body' ).css( 'cursor', 'default' );
    }

})( jQuery );
