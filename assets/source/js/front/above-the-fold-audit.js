(function ( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
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

	window.aboveFoldAudit = {};

	document.addEventListener(
		'DOMContentLoaded',
		() => {
			// Run the analysis when the page loads.
			homePageAnalysis.init();
		}
	);

	window.addEventListener(
		'resize',
		() => {
			// Optionally Re-run the analysis if the window is resized or init function manually from the console after resize -> aboveFoldAudit.homePageAnalysis.init()
			// homePageAnalysis.init();
		}
	);

	/**
	 * This script detects the user's viewport size and identifies
	 * all hyperlinks (<a> tags) that are currently visible
	 * "above the fold" on the webpage.
	 *
	 * The results are logged to the browser's console.
	 * This script is designed to run automatically on page load for every visitor.
	 */
	var homePageAnalysis = {
		init: function () {
			/**
			 * Init function to run the detection and log results.
			 * This function have to be called when the DOM is ready.
			 */
			const viewport 		 = homePageAnalysis.getViewportSize();
			const aboveFoldLinks = homePageAnalysis.getVisibleAboveFoldHyperlinks();

			if ( aboveFoldLinks.length > 0 ) {

				const dataToSend = {
					timestamp: new Date().toISOString(),
					pageUrl: window.location.href,
					viewportSize: viewport,
					visibleLinks: aboveFoldLinks
				};

				console.log( '+++ Homepage Analysis for Current User +++' );
				// Log the full data object.
				console.log( 'Data to be sent:', dataToSend );
				console.log( '======================================' );

				// Send the collected data to the WordPress endpoint.
				//homePageAnalysis.sendDataToWordPressEndpoint( dataToSend );
			} else {
				console.log( 'No hyperlinks found visible above the fold.' );
			}
		},
		isVisible: function ( el ) {
			/**
			 * Checks if an element is currently visible on the page.
			 * It considers display, visibility, and opacity CSS properties.
			 *
			 * @param {HTMLElement} el - The element to check.
			 *
			 * @returns {boolean} True if the element is visible, false otherwise.
			 */
			if ( ! ( el instanceof HTMLElement ) ) {
				return false;
			}
			const style = window.getComputedStyle( el );
			return style.display !== 'none' &&
				style.visibility !== 'hidden' &&
				parseFloat( style.opacity ) > 0 &&
				el.offsetWidth > 0 &&
			// Check for elements with zero width.
				el.offsetHeight > 0;
			// Check for elements with zero height.
		},
		getViewportSize: function () {
			/**
			 * Detects the browser's viewport (above the fold) dimensions.
			 *
			 * @returns {object} An object containing the width and height of the viewport.
			 */
			return {
				width: window.innerWidth || document.documentElement.clientWidth,
				height: window.innerHeight || document.documentElement.clientHeight
			};
		},
		getVisibleAboveFoldHyperlinks: function () {
			/**
			 * Identifies hyperlinks that are currently visible within the viewport (above the fold).
			 *
			 * @returns {Array<object>} An array of objects, each representing a visible hyperlink.
			 */
			const { width: viewportWidth, height: viewportHeight } = homePageAnalysis.getViewportSize();
			const allLinks 	   = document.querySelectorAll( 'a' );
			const visibleLinks = [];

			allLinks.forEach(
				link => {
					// First, check if the element is visually rendered (not display:none, visibility:hidden, opacity:0).
					if ( ! homePageAnalysis.isVisible( link ) ) {
						// Skip if not visually visible.
						return;
					}

					const rect = link.getBoundingClientRect();
                	// Check if the link's bounding box is within the viewport.
					// An element is above the fold if:
					// 1. Its top edge is within or above the viewport (rect.top <= viewportHeight)
					// 2. Its bottom edge is within or below the viewport (rect.bottom >= 0)
					// 3. Its left edge is within or left of the viewport (rect.left <= viewportWidth)
					// 4. Its right edge is within or right of the viewport (rect.right >= 0).
					const isAboveFold = (
					rect.top < viewportHeight &&
					rect.bottom > 0 &&
					rect.left < viewportWidth &&
					rect.right > 0
				);
				if ( isAboveFold ) {
					visibleLinks.push(
						{
							text: link.textContent.trim() || 'No Text',
							// Trim whitespace.
							href: link.href,
							position: {
								top: rect.top,
								left: rect.left,
								width: rect.width,
								height: rect.height
							}
						}
					);
				}
				}
			);

			return visibleLinks;
		},
		sendDataToWordPressEndpoint: async function ( data ) {
			/**
			 * Sends the collected data to a WordPress plugin endpoint.
			 * You will need to set up a corresponding endpoint in your WordPress plugin
			 * to receive and process this data.
			 *
			 * @param {object} data - The data object to send (viewport size and visible links).
			 */
			// IMPORTANT: Replace this URL with your actual WordPress plugin endpoint URL.
			// This URL MUST be correct and your WordPress backend MUST be configured to
			// accept POST requests at this endpoint, including handling CORS if necessary.
			// Example using WordPress REST API: 'https://yourwebsite.com/wp-json/your-plugin/v1/analytics'
			// Example using admin-ajax.php: 'https://yourwebsite.com/wp-admin/admin-ajax.php'.
			const endpointUrl = 'https://yourwebsite.com/wp-json/your-plugin/v1/analytics'; // <<< MAKE SURE TO REPLACE THIS URL!

			try {
				const response = await fetch(
					endpointUrl,
					{
						method: 'POST',
						headers: {
							'Content-Type': 'application/json',
							// If your WordPress endpoint requires a nonce for security (highly recommended for REST API),
							// you would enqueue a script in WordPress to expose the nonce globally, e.g.:
							// 'X-WP-Nonce': WordPressNonceVariable.
						},
						body: JSON.stringify( data )
					}
				);

				if ( response.ok ) {
					const result = await response.json();
					console.log( 'Data successfully sent to WordPress endpoint:', result );
				} else {
					// Log more details if the response is not OK.
					const errorResponseText = await response.text();
					console.error( `Failed to send data to WordPress endpoint. Status: ${response.status} ${response.statusText}. Response body:`, errorResponseText );
					console.error( 'Possible reasons: Incorrect URL, server-side error, or CORS policy blocking the request.' );
				}
			} catch (error) {
				console.error( 'Error sending data to WordPress endpoint: Failed to fetch.', error );
				console.error( 'This typically means the browser could not initiate or complete the network request.' );
				console.error( 'Check: 1. Network connectivity. 2. Correctness of the endpoint URL. 3. CORS configuration on your WordPress server.' );
			}
		}
	};

	aboveFoldAudit.homePageAnalysis = homePageAnalysis;

})( jQuery );
