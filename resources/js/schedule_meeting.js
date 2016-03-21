( function($) {
    $( document ).ready( function() {
        $( '#meeting-date' ).datepicker( {
            dateFormat: 'DD, MM d, yy'
        } );
        $( '#schedule-meeting' ).on( 'click', function() {
            $.post(
                zoom_ajax_schedule_meeting.ajaxurl,
                {
                    action:   'zoom_schedule_meeting',
                    host_id:  $( '#meeting-host-id' ).val(),
                    topic:    $( '#meeting-topic' ).val(),
                    date:     $( '#meeting-date' ).val(),
                    time:     $( '#meeting-time' ).val(),
                    timezone: $( '#meeting-timezone' ).find( ':selected' ).val(),
                    duration: $( '#meeting-duration' ).val(),
                    type:     $( '#meeting-type' ).val()
                },
                function( response ) {
                    var meeting = jQuery.parseJSON( response );
                    var response_container = $( '#schedule-meeting-response' );
                    $( '#schedule-meeting-form' ).fadeOut();
                    response_container
                        .hide()
                        .text( 'Meeting scheduled for ' + meeting.start_time )
                        .fadeIn();
                }
            );
            return false;
        } );
    });
} )( jQuery );