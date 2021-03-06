/*globals jQuery, htDMSinternalAPI, htDMS*/
jQuery(document).ready(function( $ ) {

    /**
     * Containers to use paginated views for
     *
     * Should be all views that are not single item views.
     *
     * @since 0.0.2
     */
    var paginatedViews = [
        '#users_groups',
        '#public_groups',
        '#users_organizations',
        '#users_notifications',
        '#decision-blocked',
        '#decision-new',
        '#decision-passed'
    ];

    //loop through paginatedViews running each one, if we have that div already.
    $.each( paginatedViews, function( index, containerID ){
        if ( $( containerID ).length ) {
            var spinner = containerID + "-spinner.spinner";
            $( spinner ).show();
            extraArg = 0;
            if ( "#users_notifications" === containerID ) {
                extraArg = 1;
            }

            htDMSinternalAPI.paginate.request( containerID, 1, extraArg );

        };
    });



    function ht_dms_paginate( container, page ) {
        return htDMSinternalAPI.paginate.request( container, page );
    }

    window.ht_dms_paginate = ht_dms_paginate;

    /**
     * Put possible result of actions into variables
     */
    var select_field = '#fld_738259_1';

    function result( select_field, consensusPossibilities ) {
        if ( undefined != consensusPossibilities && undefined != consensusPossibilities.possible_results[0]) {
            var p0 = consensusPossibilities.possible_results[0];
            var p1 = consensusPossibilities.possible_results[1];
            var p2 = consensusPossibilities.possible_results[2];

            var selected_action = $( select_field ) .val();

            var result = false;
            if ( selected_action === 'accept' ) {
                var result = 'Decision will be ' + p1 + '.';
            }

            if (selected_action === 'object') {
                var result = 'Decision will be ' + p2 + '.';
            }

            //@todo translation-friendliness!
            if (selected_action === 'propose-modify') {
                var result = 'You will be able to propose a new version of this decision to consider.';
            }

            if ( selected_action === 'respond') {
                var result = 'You will be able to respond to this decision';
            }

            if ( false != result ) {
                $( '#dms-action-result').hide();
                result = 'If you make this choice: ' + result;
                $( '#dms-action-result').empty();
                $( '#dms-action-result' ).append( result).show();
            }

        }
    }

    result( select_field );
    $( select_field ).change( function() {
        result( select_field, consensusPossibilities );
    });

    $( document ).ajaxComplete(function( event, xhr, settings ) {
        tabDisplayFix();
        $('.notification-mark' ).each(function(i, el) {


            nRead = $( el ).attr( 'viewed' );
            if ( 1 == nRead  ) {
                $( el ).html( 'Mark Not Viewed' );

            }

            if ( 0 == nRead ) {
                $( el ).html( 'Mark Viewed' );
            }


        });

        var mark = '.notification-mark';
        $( mark ).click(function () {

            htDMSinternalAPI.markNotification.request( $( this ).attr('nid' ), $( this ).attr( 'viewed' ) );
        });

        $( '#notification-single-close' ).click(function () {

            htDMSinternalAPI.markNotification.request( $( this ).attr('nid' ) );

        });

        var allViewToggle = $( "#notification-all-view-toggle" );
        $( allViewToggle ).click( function() {
            container = '#users_notifications';
            state = $( allViewToggle ).attr( 'state' );
            state  ^= true;

            if ( state ) {
                text = htDMSinternalAPIvars.messages.showAll;
            }
            else {
                text = htDMSinternalAPIvars.messages.showNew;
            }

            $( allViewToggle ).html( text );
            $( allViewToggle ).attr( 'state', state );
            htDMSinternalAPI.paginate.request( container, $( container ).attr( 'page' ), state );

        });



    });

    /**
     * Consensus reload via ajax
     *
     * @since 0.0.3
     */
    function reloadConsensus() {

        return htDMSinternalAPI.reloadConsensus.request();

    }

    window.reloadConsensus = reloadConsensus;



    function loadUsers ( users, container, templateID  ) {

        $.each(users, function( i, val ) {
            var user = new wp.api.models.User( { ID: val } );
            user.fetch().done(function () {
                loadUser( user, container, templateID );
            });

        });
    }


    function loadUser( user, container, templateID  ) {

        var name = user.attributes.name;
        var avatar = user.attributes.avatar;
        var ID = user.attributes.ID;

        var source   = $( templateID ).html();

        var data = {
            name: name,
            avatar: avatar,
            ID: ID
        };

        if ( container == 'return' ) {
            return data;
        }

        var template    = Handlebars.compile( source );
        var html        = template( data );


        $( container ).append( html );

    }



    //init foundation
    $( document ).foundation();

    function idCheck( id ) {
        return htDMSinternalAPI.idCheck( id );
    }


    $( '#ht-sub-menu-button' ).click(function() {
        $( this ).toggleClass( 'expanded' ).siblings( 'div' ).slideToggle();
    });


    /**
     * Consensus Visualization
     *
     * @since 0.0.3
     */
    $( document).ready( function()  {
        htDMSinternalAPI.consensusView();
    });


    /**
     * Open the discussion Modal
     *
     * @since 0.0.3
     */
    function openCommentModal() {
        $( '#discussion-modal' ).foundation('reveal', 'open');
    }

    /**
     * If respond is chosen for the decision action form, open the modal
     *
     * @since 0.0.3
     */
    $( ".CF5411fb087123d" ).submit(function( event ) {
        if ( $( '#fld_738259_1').val() == 'respond' ) {
            event.preventDefault();
            openCommentModal();
        }

    });

    $( ".CF5411fb087123d" ).submit(function( event ) {
        if ( $( '#fld_738259_1').val() == 'propose-modify' ) {
            event.preventDefault();
            document.location = htDMS.proposeModifyURL;
        }

    });

    function loadDiscussion( id ) {
        return htDMSinternalAPI.discussion( id );
    }

    Handlebars.registerHelper( '55char', function(str) {
        if ( undefined != str && str.length > 55 ) {
            return str.substring( 0, 55 ) + '...';
        }

        return str;
    });

    /**
     * Handlebars helper for Use Previews
     *
     * @since 0.2.0
     */
    Handlebars.registerHelper( 'userPreviewLoop',
        function( users, showName, miniMode ) {
            var str  = '';


            $.each( users, function( i, user ) {

                str += '<li id="user-' +
                    user.ID +
                    '" class="user user-view';
                if ( true === miniMode ) {
                    str += ' mini-mode';
                }
                str += '">' +
                '<span class="avatar">' +
                user.avatar +
                '</span>';
                if ( true ===   showName ) {
                    str += '<p class="name text-center">' +
                    user.name  +'</p>';
                }
                str += '</li>';


            });


            return new Handlebars.SafeString( str );
        }
    );

    /**
     * Handlebars helper for organization links.
     *
     * @since 0.2.0
     */
    Handlebars.registerHelper( 'organizationLink', function( url, id, name, button ) {
        return link( url,  id,  name, 'organization', button );
    });

    /**
     * Handlebars helper for group links.
     *
     * @since 0.2.0
     */
    Handlebars.registerHelper( 'groupLink', function( url, id, name, button ) {
        return link( url,  id,  name, 'group', button );

    });

    /**
     * Handlebars helper for decision links.
     *
     * @since 0.2.0
     */
    Handlebars.registerHelper( 'decisionLink', function( url, id, name, button ) {
        return link( url,  id,  name, 'decision', button );
    });

    /**
     * Create links inside Handlebars helpers.
     *
     * @since 0.2.0
     *
     * @param url URL to link to.
     * @param postID ID of post to link to.
     * @param name Name of post to link to.
     * @param type Post type of post to link to.
     * @param button Whether to use as a button or not.
     * @returns {string}
     */
    function link( url, postID, name, type, button ) {
        if ( undefined == button ) {
            button = false;
        }

        idAttr = type + '-' + 'link-' + postID;
        classAttr =  'ht-dms-link ht-dms-link-internal ' + type + '-link';

        if ( button ) {
            classAttr += ' button';
        }

        icons = htDMS.icons;

        icon = icons[type];

        return new Handlebars.SafeString( '<a id="' + idAttr + '" href="' + url + '" class="' + classAttr + '" ' + type + '="' + postID + '" internal-link="true" title="View ' + ucwords( type ) + '">' + icon + name + '</a>' );

    }

    /**
     * Make first letter of each word capitalized, just like the php function of the same name.
     *
     * @since 0.2.0
     */
    function ucwords(str) {
        return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
    }


    /**
     * Callback after membership form is submitted.
     *
     * Wrapper for htDMSinternalAPI.reloadMembership.request
     *
     * @since 0.1.0
     * 
     * @returns {*}
     */
    function reloadMembership() {
        return htDMSinternalAPI.reloadMembership.request();
    }

    /**
     * AJAX Callback that runs after decision action form is processed.
     *
     * @since 0.1.0
     */
    function postDecisionAction() {
        $( '#dms-action-result' ).hide();
        reloadMembership();
    }

    window.ht_dms_post_decision_action = postDecisionAction;


    /**
     * Stupid Hack To Fix 2nd Tab Having display:block
     *
     * @see https://github.com/HoloTree/ht_dms/issues/129
     */
    function tabDisplayFix() {
        var panels = $( ".tabs-content" ).children();
        $.each( panels, function( i, div ) {
            badStyle = 'display: block;';
            el = document.getElementById( div.id);
            if ( el.hasAttribute( 'style' ) )  {
                elStyle = el.getAttribute( 'style' );
                if ( elStyle.indexOf( badStyle ) > -1 ) {
                    newStyle = elStyle.replace( badStyle, '');
                    el.removeAttribute("style");
                    el.setAttribute( 'style', newStyle );

                }
            }
        });
    }

    tabDisplayFix();

    /**
     * Reinitialized Baldrick on ajaxComplete
     *
     * @since 0.3.0
     */
    $( document ).ajaxComplete(function( event, xhr, settings ) {
        $( '.'+baldrick_wp_front_end.className ).baldrick({
            request     : baldrick_wp_front_end.ajaxURL,
            method      : baldrick_wp_front_end.transportMethod
        });
    });


    /**
     * Callback used to open the consensus preview
     *
     * @since 0.3.0
     *
     * @param response AJAX response form consensus_details endpoint of intenral API
     */
    function bldrk_consensus_details_cb( response ) {

        var dID= response.data.did;
        var details = response.data.details;
        var headers = response.data.headers;

        var html = htDMSinternalAPI.consensusView( details, dID, headers );
        var selector = ".consensus-view[did='"+dID+"']";
        var button_selector = '.consensus-view-button';
        var close_button_selector = '#' + 'decision-' + dID + ' .close';
        $( selector ).html( html ).slideDown();
        $( button_selector ).css( 'display', 'none' );
        $( close_button_selector ).css( 'display', 'block');


        //this is copypasta, but it works, didn't without
        $( '#consensus-views-chooser li a' ).click( function () {
            var cst = $( this ).first().attr( 'cst' );
            $( '#consensus-views-by-status' ).children().fadeOut();
            var container = '#' + cst;
            $( container ).fadeIn();
        } );

        $( close_button_selector ).on( 'click', function( event ) {
            $( selector ).slideUp();
            $( button_selector ).css( 'display', 'inline' );
            $( close_button_selector ).css( 'display', 'none');
        });




    }

    window.bldrk_consensus_details_cb = bldrk_consensus_details_cb;

});
