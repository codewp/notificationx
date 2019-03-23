window.addEventListener('DOMContentLoaded', function( event ) {

	var notificationX_pressbar_active = 0;

	function notificationX_initialize(){
		notificationX_pressbar();
		notificationX_conversion();
	}

	function notificationX_pressbar(){
		var bars = document.querySelectorAll('.nx-bar');
		if ( bars.length > 0 ) {
		
			bars.forEach(function( bar ){
				var id = bar.dataset.press_id,
					duration = bar.dataset.hide_after,
					auto_hide = bar.dataset.auto_hide,
					countdown_wrapper = bar.querySelector('.nx-countdown');
				
				if( countdown_wrapper != null ) {
					var countdown_time = JSON.parse( countdown_wrapper.dataset.countdown );

					
					if ( 'undefined' !== typeof countdown_time ) {
						// Get current date and time.
						var date             = new Date(),
							year             = date.getYear() + 1900,
							month            = date.getMonth() + 1,
							days             = ( parseInt( date.getDate() ) + parseInt( countdown_time.days ) ),
							hours            = ( parseInt( date.getHours() ) + parseInt( countdown_time.hours ) ),
							minutes          = ( parseInt( date.getMinutes() ) + parseInt( countdown_time.minutes ) ),
							seconds          = ( parseInt( date.getSeconds() ) + parseInt( countdown_time.seconds ) ),
							new_date         = new Date( year, parseInt( month, 10 ) - 1, days, hours, minutes, seconds ),
							countdown_cookie = '',
							countdown_string = countdown_time.days + ', ' + countdown_time.hours + ', ' + countdown_time.minutes + ', ' + countdown_time.seconds;
	
						// Convert countdown time to miliseconds and add it to current date.
						date.setTime( date.getTime() + ( parseInt( countdown_time.days ) * 24 * 60 * 60 * 1000)
													 + ( parseInt( countdown_time.hours )  * 60 * 60 * 1000)
													 + ( parseInt( countdown_time.minutes ) * 60 * 1000)
													 + ( parseInt( countdown_time.seconds ) * 1000) );

						if( Cookies.get( 'nx_bar_countdown_old' ) !== countdown_string ){
							Cookies.set( 'nx_bar_countdown_old',  countdown_string, { expires: date, path: '/' } );
							Cookies.clear('nx_bar_countdown');
						}

						countdown_cookie = Cookies.get( 'nx_bar_countdown' );

						
						if ( typeof countdown_cookie == 'undefined' || isNaN( countdown_cookie ) ){
							Cookies.set( 'nx_bar_countdown',  new_date.getTime(), { expires: date, path: '/' } );
							Cookies.set( 'nx_bar_countdown_old',  countdown_string, { expires: date, path: '/' } );
							countdown_cookie = Cookies.get( 'nx_bar_countdown' );
						}
						
						var countdown_interval = setInterval(function() {
							var now         = new Date().getTime(),
								difference  = countdown_cookie - now,
								days        = Math.floor( difference / ( 1000 * 60 * 60 * 24 ) ),
								hours       = Math.floor( ( difference % ( 1000 * 60 * 60 * 24 ) ) / ( 1000 * 60 * 60 ) ),
								minutes     = Math.floor( ( difference % ( 1000 * 60 * 60 ) ) / ( 1000 * 60 ) ),
								seconds     = Math.floor( ( difference % ( 1000 * 60 )) / 1000 );



							bar.querySelector('.nx-days').innerHTML = days;
							bar.querySelector('.nx-hours').innerHTML = hours;
							bar.querySelector('.nx-minutes').innerHTML = minutes;
							bar.querySelector('.nx-seconds').innerHTML = seconds;
							if ( difference < 0 ) {
								clearInterval( countdown_interval );
								bar.querySelector('.nx-countdown').classList.add('nx-expired');
							}
						}, 1000);
					}
				}

				notificationX_showBar( bar , id );

				if ( ( '' !== duration || undefined !== duration ) && parseInt( auto_hide ) ) {
                    setTimeout(function() {
                        notificationX_hideBar( 'nx-bar-' + id );
                    }, parseInt(duration) * 1000);
				}
				
			});
			
		}

	}

	function notificationX_showBar( bar, bar_id ){
		if( bar === '' ) {
			bar = document.querySelector( '.nx-bar ' + '.nx-bar-' + bar_id );
		}

		var delay     = parseInt( bar.dataset.initial_delay ),
			barHeight = bar.querySelector('.nx-bar-inner').offsetHeight,
			xAdminBar = document.querySelector('#wpadminbar'),
			xAdminBarHeight = xAdminBar != null ? xAdminBar.offsetHeight : 0;

		if( delay === '' || isNaN( delay ) ) {
			delay = 0;
		}

		setTimeout(function(){
			var html = document.querySelector( 'html' );
				html.classList.add( 'nx-bar-active' );
			if( bar.classList.contains( 'nx-position-top' ) ) {
				html.animate([
					{ paddingTop: 0, }, 
					{ paddingTop: barHeight + 'px' }, 
				], { duration: 300 });
				html.style.paddingTop = barHeight + 'px';
				bar.animate([
					{ top: 0 + 'px' }, 
					{ top: xAdminBarHeight + 'px' }, 
				], { duration: 300 });
				bar.style.top = xAdminBarHeight + 'px';
			}
			bar.classList.add( 'nx-bar-visible' );
			notificationX_pressbar_active = 1;
		}, delay * 1000);
	}

	function notificationX_hideBar( id ){
		var bar = document.querySelector( '.nx-bar#' + id ),
			html = document.querySelector( 'html' );
		html.classList.remove( 'nx-bar-active' );
		html.style.paddingTop = 0;
		bar.animate([
			{ height: bar.offsetHeight + 'px' }, 
			{ height:  0 + 'px' }, 
		], { duration: 300 });
		// bar.style.height = 0;
		bar.classList.remove( 'nx-bar-visible' );
		notificationX_pressbar_active = 0;
	}

	function notificationX_conversion(){
		if ( 'undefined' === typeof notificationx ) {
			return;
		}
		window.localStorage.removeItem('nx_notifications');

		if ( notificationx.conversions.length > 0 ) {
			notificationX_process( notificationx.conversions[0] );
		}
		
		if ( notificationx.comments.length > 0 ) {
			notificationX_process( notificationx.comments[0] );
		}

		if( notificationx.pro_ext.length > 0 ) {
			notificationx.pro_ext.map(function( item, i ){
				notificationX_process( item[0] );
			});
		}
	}

	function notificationX_process( ids ){
		var node = document.createElement('div'),
			notificationHTML = '';
			node.classList.add('notificationx-conversions');

		fetch( notificationx.ajaxurl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: new Headers({'Content-Type': 'application/x-www-form-urlencoded'}),
			body: 'action=nx_get_conversions&nonce=' + notificationx.nonce + '&ids=' + ids,
		})
		.then(function( response ){
			return response.json();
		})
		.then(function( response ){
			if( response ) {
				node.innerHTML = response.content;
				notificationX_render(response.config, node);
			}
		})
		.catch(function( err ){
			// console.log('AJAX error, Something went wrong! Please, Contact support team.')
		});

	}

	function notificationX_render( configuration, html ) {
		var count = 0,
			notifications = html.querySelectorAll( '.notificationx-' + configuration.id ),
			delayBetween = configuration.delay_between,
			last = notificationX_last( configuration.id, false );

		if( last >= 0 ) {
			count = last + 1;
		}

		if( configuration.loop === 0 && notifications.length === 1 ) {
			count = 0;
		}

		setTimeout(function(){
			notificationX_show( notifications[ count ], configuration, count );

			setTimeout(function(){
				notificationX_hide( notifications[ count ] );
				count++;
				var nextNotification = setInterval(function(){ 
					notificationX_show( notifications[ count ], configuration, count );

					setTimeout(function() {
						notificationX_hide( notifications[ count ] );
						if ( count >= notifications.length - 1 ) {
							count = 0;
							if ( configuration.loop === 0 ) {
								clearInterval( nextNotification );
							}
						} else {
							count++;
						}
					}, delayBetween + configuration.display_for);
				}, configuration.display_for );
			}, configuration.delay_before);
		});
	}

	function notificationX_show( notification, configuration, count ){
		if ( 'undefined' === typeof notification || 0 === notification.length ) {
			return;
		}

		var body = document.querySelector('body');

		body.append( notification );
		notification.animate([
			{
				bottom : '0px',
				opacity : 0
			},
			{
				bottom : '30px',
				opacity : 1
			},
		], { duration : 500 })
		notification.style.bottom = '30px';
		notification.style.opacity = 1;
		notificationX_save( configuration.id, count );
	}

	function notificationX_hide( notification ){
		
		notification.animate([
			{ opacity : 1 },
			{ opacity : 0 }
		], { duration : 300 });

		notificationX_remove( notification );	
	}

	function notificationX_remove( notification ){
		notification.remove();
	}

	function notificationX_save( id, rank ){
		if ( window.localStorage ) {
			var lastOne = notificationX_last(id, true);
			if ( 'object' === typeof lastOne ) {
				lastOne[id] = rank;
			} else {
				lastOne = new Object;
				lastOne[id] = rank;
			}
			window.localStorage.setItem('nx_notifications', JSON.stringify(lastOne));
		} else {
			console.log('Browser does not support localStorage!');
		}
	}

	function notificationX_last( id, elem ){
		var last = -1;
		if ( window.localStorage ) {
			var notifications = window.localStorage.getItem('nx_notifications');
			if ( null !== notifications ) {
				notifications = JSON.parse(notifications);
				if ( undefined !== notifications[id] ) {
					if ( elem ) {
						return notifications;
					}
					last = notifications[id];
				}
			}
		} else {
			console.log('Browser does not support localStorage!');
		}
		return last;
	}

	/**
	 * Initialize NotificationX
	 */
	notificationX_initialize();

});