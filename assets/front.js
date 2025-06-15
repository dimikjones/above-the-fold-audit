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

eval("(function ($) {\n  'use strict';\n\n  /**\n   * All of the code for your admin-facing JavaScript source\n   * should reside in this file.\n   *\n   * Note: It has been assumed you will write jQuery code here, so the\n   * $ function reference has been prepared for usage within the scope\n   * of this function.\n   *\n   * This enables you to define handlers, for when the DOM is ready:\n   *\n   * $(function() {\n   *\n   * });\n   *\n   * When the window is loaded:\n   *\n   * $( window ).load(function() {\n   *\n   * });\n   *\n   * ...and/or other possibilities.\n   *\n   * Ideally, it is not considered best practise to attach more than a\n   * single DOM-ready or window-load handler for a particular page.\n   * Although scripts in the WordPress core, Plugins and Themes may be\n   * practising this, we should strive to set a better example in our own work.\n   */\n  window.aboveFoldAudit = {};\n  document.addEventListener('DOMContentLoaded', () => {\n    // Run the analysis when the page loads.\n    homePageAnalysis.init();\n  });\n  window.addEventListener('resize', () => {\n    // Optionally Re-run the analysis if the window is resized or init function manually from the console after resize -> aboveFoldAudit.homePageAnalysis.init()\n    // homePageAnalysis.init();\n  });\n\n  /**\n   * This script detects the user's viewport size and identifies\n   * all hyperlinks (<a> tags) that are currently visible\n   * \"above the fold\" on the webpage.\n   *\n   * The results are logged to the browser's console.\n   * This script is designed to run automatically on page load for every visitor.\n   */\n  var homePageAnalysis = {\n    init: function () {\n      /**\n       * Init function to run the detection and log results.\n       * This function have to be called when the DOM is ready.\n       */\n      const viewport = homePageAnalysis.getViewportSize();\n      const aboveFoldLinks = homePageAnalysis.getVisibleAboveFoldHyperlinks();\n      console.log('+++ Homepage Analysis for Current User +++');\n      console.log('Visitor Screen (Viewport) Size:', `${viewport.width}px x ${viewport.height}px`);\n      console.log('Hyperlinks Visible Above the Fold:');\n      if (aboveFoldLinks.length > 0) {\n        aboveFoldLinks.forEach((link, index) => {\n          console.log(`  ${index + 1}. Text: \"${link.text}\", Href: \"${link.href}\", Position:`, link.position);\n        });\n      } else {\n        console.log('  No hyperlinks found visible above the fold.');\n      }\n      console.log('======================================');\n    },\n    isVisible: function (el) {\n      /**\n       * Checks if an element is currently visible on the page.\n       * It considers display, visibility, and opacity CSS properties.\n       *\n       * @param {HTMLElement} el - The element to check.\n       *\n       * @returns {boolean} True if the element is visible, false otherwise.\n       */\n      if (!(el instanceof HTMLElement)) {\n        return false;\n      }\n      const style = window.getComputedStyle(el);\n      return style.display !== 'none' && style.visibility !== 'hidden' && parseFloat(style.opacity) > 0 && el.offsetWidth > 0 &&\n      // Check for elements with zero width.\n      el.offsetHeight > 0;\n      // Check for elements with zero height.\n    },\n    getViewportSize: function () {\n      /**\n       * Detects the browser's viewport (above the fold) dimensions.\n       *\n       * @returns {object} An object containing the width and height of the viewport.\n       */\n      return {\n        width: window.innerWidth || document.documentElement.clientWidth,\n        height: window.innerHeight || document.documentElement.clientHeight\n      };\n    },\n    getVisibleAboveFoldHyperlinks: function () {\n      /**\n       * Identifies hyperlinks that are currently visible within the viewport (above the fold).\n       *\n       * @returns {Array<object>} An array of objects, each representing a visible hyperlink.\n       */\n      const {\n        width: viewportWidth,\n        height: viewportHeight\n      } = homePageAnalysis.getViewportSize();\n      const allLinks = document.querySelectorAll('a');\n      const visibleLinks = [];\n      allLinks.forEach(link => {\n        // First, check if the element is visually rendered (not display:none, visibility:hidden, opacity:0).\n        if (!homePageAnalysis.isVisible(link)) {\n          // Skip if not visually visible.\n          return;\n        }\n        const rect = link.getBoundingClientRect();\n        // Check if the link's bounding box is within the viewport.\n        // An element is above the fold if:\n        // 1. Its top edge is within or above the viewport (rect.top <= viewportHeight)\n        // 2. Its bottom edge is within or below the viewport (rect.bottom >= 0)\n        // 3. Its left edge is within or left of the viewport (rect.left <= viewportWidth)\n        // 4. Its right edge is within or right of the viewport (rect.right >= 0).\n        const isAboveFold = rect.top < viewportHeight && rect.bottom > 0 && rect.left < viewportWidth && rect.right > 0;\n        if (isAboveFold) {\n          visibleLinks.push({\n            text: link.textContent.trim() || 'No Text',\n            // Trim whitespace.\n            href: link.href,\n            position: {\n              top: rect.top,\n              left: rect.left,\n              width: rect.width,\n              height: rect.height\n            }\n          });\n        }\n      });\n      return visibleLinks;\n    }\n  };\n  aboveFoldAudit.homePageAnalysis = homePageAnalysis;\n})(jQuery);\n\n//# sourceURL=webpack://above-the-fold-audit/./assets/source/js/front/above-the-fold-audit.js?");

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