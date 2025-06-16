/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./assets/source/js/front/above-the-fold-audit.js":
/*!********************************************************!*\
  !*** ./assets/source/js/front/above-the-fold-audit.js ***!
  \********************************************************/
/***/ (() => {

eval("(function ($) {\n  'use strict';\n\n  /**\n   * All of the code for your admin-facing JavaScript source\n   * should reside in this file.\n   *\n   * Note: It has been assumed you will write jQuery code here, so the\n   * $ function reference has been prepared for usage within the scope\n   * of this function.\n   *\n   * This enables you to define handlers, for when the DOM is ready:\n   *\n   * $(function() {\n   *\n   * });\n   *\n   * When the window is loaded:\n   *\n   * $( window ).load(function() {\n   *\n   * });\n   *\n   * ...and/or other possibilities.\n   *\n   * Ideally, it is not considered best practise to attach more than a\n   * single DOM-ready or window-load handler for a particular page.\n   * Although scripts in the WordPress core, Plugins and Themes may be\n   * practising this, we should strive to set a better example in our own work.\n   */\n  window.aboveFoldAudit = {};\n  document.addEventListener('DOMContentLoaded', () => {\n    // Run the analysis when the page loads.\n    aboveFoldAuditHomePageAnalysis.init();\n  });\n  window.addEventListener('resize', () => {\n    // Optionally Re-run the analysis if the window is resized or init function manually from the console after resize -> aboveFoldAudit.aboveFoldAuditHomePageAnalysis.init()\n    // aboveFoldAuditHomePageAnalysis.init();\n  });\n\n  /**\n   * This script detects the user's viewport size and identifies\n   * all hyperlinks (<a> tags) that are currently visible\n   * \"above the fold\" on the webpage.\n   *\n   * The results are logged to the browser's console.\n   * This script is designed to run automatically on page load for every visitor.\n   */\n  var aboveFoldAuditHomePageAnalysis = {\n    init: function () {\n      /**\n       * Init function to run the detection and log results.\n       * This function have to be called when the DOM is ready.\n       */\n      const viewport = aboveFoldAuditHomePageAnalysis.getViewportSize();\n      const aboveFoldLinks = aboveFoldAuditHomePageAnalysis.getVisibleAboveFoldHyperlinks();\n      if (aboveFoldLinks.length > 0) {\n        const dataToSend = {\n          timestamp: new Date().toISOString(),\n          pageUrl: window.location.href,\n          viewportSize: viewport,\n          visibleLinks: aboveFoldLinks\n        };\n        console.log('+++ Homepage Analysis for Current User +++');\n        // Log the full data object.\n        console.log('Data to be sent:', dataToSend);\n        console.log('======================================');\n\n        // Send the collected data to the WordPress endpoint.\n        //aboveFoldAuditHomePageAnalysis.sendDataToWordPressEndpoint( dataToSend );\n      } else {\n        console.log('No hyperlinks found visible above the fold.');\n      }\n    },\n    isVisible: function (el) {\n      /**\n       * Checks if an element is currently visible on the page.\n       * It considers display, visibility, and opacity CSS properties.\n       *\n       * @param {HTMLElement} el - The element to check.\n       *\n       * @returns {boolean} True if the element is visible, false otherwise.\n       */\n      if (!(el instanceof HTMLElement)) {\n        return false;\n      }\n      const style = window.getComputedStyle(el);\n      return style.display !== 'none' && style.visibility !== 'hidden' && parseFloat(style.opacity) > 0 && el.offsetWidth > 0 &&\n      // Check for elements with zero width.\n      el.offsetHeight > 0;\n      // Check for elements with zero height.\n    },\n    getViewportSize: function () {\n      /**\n       * Detects the browser's viewport (above the fold) dimensions.\n       *\n       * @returns {object} An object containing the width and height of the viewport.\n       */\n      return {\n        width: window.innerWidth || document.documentElement.clientWidth,\n        height: window.innerHeight || document.documentElement.clientHeight\n      };\n    },\n    getVisibleAboveFoldHyperlinks: function () {\n      /**\n       * Identifies hyperlinks that are currently visible within the viewport (above the fold).\n       *\n       * @returns {Array<object>} An array of objects, each representing a visible hyperlink.\n       */\n      const {\n        width: viewportWidth,\n        height: viewportHeight\n      } = aboveFoldAuditHomePageAnalysis.getViewportSize();\n      const allLinks = document.querySelectorAll('a');\n      const visibleLinks = [];\n      allLinks.forEach(link => {\n        // First, check if the element is visually rendered (not display:none, visibility:hidden, opacity:0).\n        if (!aboveFoldAuditHomePageAnalysis.isVisible(link)) {\n          // Skip if not visually visible.\n          return;\n        }\n        const rect = link.getBoundingClientRect();\n        // Check if the link's bounding box is within the viewport.\n        // An element is above the fold if:\n        // 1. Its top edge is within or above the viewport (rect.top <= viewportHeight)\n        // 2. Its bottom edge is within or below the viewport (rect.bottom >= 0)\n        // 3. Its left edge is within or left of the viewport (rect.left <= viewportWidth)\n        // 4. Its right edge is within or right of the viewport (rect.right >= 0).\n        const isAboveFold = rect.top < viewportHeight && rect.bottom > 0 && rect.left < viewportWidth && rect.right > 0;\n        if (isAboveFold) {\n          visibleLinks.push({\n            text: link.textContent.trim() || 'No Text',\n            // Trim whitespace.\n            href: link.href,\n            position: {\n              top: rect.top,\n              left: rect.left,\n              width: rect.width,\n              height: rect.height\n            }\n          });\n        }\n      });\n      return visibleLinks;\n    },\n    sendDataToWordPressEndpoint: async function (data) {\n      /**\n       * Sends the collected data to a WordPress plugin endpoint.\n       * You will need to set up a corresponding endpoint in your WordPress plugin\n       * to receive and process this data.\n       *\n       * @param {object} data - The data object to send (viewport size and visible links).\n       */\n      // IMPORTANT: Replace this URL with your actual WordPress plugin endpoint URL.\n      // This URL MUST be correct and your WordPress backend MUST be configured to\n      // accept POST requests at this endpoint, including handling CORS if necessary.\n      // Example using WordPress REST API: 'https://yourwebsite.com/wp-json/your-plugin/v1/analytics'\n      // Example using admin-ajax.php: 'https://yourwebsite.com/wp-admin/admin-ajax.php'.\n      const endpointUrl = 'https://yourwebsite.com/wp-json/your-plugin/v1/analytics'; // <<< MAKE SURE TO REPLACE THIS URL!\n\n      try {\n        const response = await fetch(endpointUrl, {\n          method: 'POST',\n          headers: {\n            'Content-Type': 'application/json'\n            // If your WordPress endpoint requires a nonce for security (highly recommended for REST API),\n            // you would enqueue a script in WordPress to expose the nonce globally, e.g.:\n            // 'X-WP-Nonce': WordPressNonceVariable.\n          },\n          body: JSON.stringify(data)\n        });\n        if (response.ok) {\n          const result = await response.json();\n          console.log('Data successfully sent to WordPress endpoint:', result);\n        } else {\n          // Log more details if the response is not OK.\n          const errorResponseText = await response.text();\n          console.error(`Failed to send data to WordPress endpoint. Status: ${response.status} ${response.statusText}. Response body:`, errorResponseText);\n          console.error('Possible reasons: Incorrect URL, server-side error, or CORS policy blocking the request.');\n        }\n      } catch (error) {\n        console.error('Error sending data to WordPress endpoint: Failed to fetch.', error);\n        console.error('This typically means the browser could not initiate or complete the network request.');\n        console.error('Check: 1. Network connectivity. 2. Correctness of the endpoint URL. 3. CORS configuration on your WordPress server.');\n      }\n    }\n  };\n  aboveFoldAudit.aboveFoldAuditHomePageAnalysis = aboveFoldAuditHomePageAnalysis;\n})(jQuery);\n\n//# sourceURL=webpack://above-the-fold-audit/./assets/source/js/front/above-the-fold-audit.js?");

/***/ }),

/***/ "./assets/source/sass/front/above-the-fold-audit.scss":
/*!************************************************************!*\
  !*** ./assets/source/sass/front/above-the-fold-audit.scss ***!
  \************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n// extracted by mini-css-extract-plugin\n\n\n//# sourceURL=webpack://above-the-fold-audit/./assets/source/sass/front/above-the-fold-audit.scss?");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval devtool is used.
/******/ 	__webpack_modules__["./assets/source/js/front/above-the-fold-audit.js"](0, {}, __webpack_require__);
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./assets/source/sass/front/above-the-fold-audit.scss"](0, __webpack_exports__, __webpack_require__);
/******/ 	
/******/ })()
;