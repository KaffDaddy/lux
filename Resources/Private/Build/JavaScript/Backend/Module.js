define(['jquery'], function($) {
  'use strict';

  /**
   * LuxBackend functions
   *
   * @class LuxBackend
   */
  function LuxBackend($) {
    'use strict';

    /**
     * @type {LuxBackend}
     */
    var that = this;

    /**
     * Initialize
     *
     * @returns {void}
     */
    this.initialize = function() {
      addLeadListDetailViewListener();
      addAnalysisContentDetailPageViewListener();
      addAnalysisNewsDetailPageViewListener();
      addAnalysisUtmDetailPageViewListener();
      addAnalysisSearchDetailPageViewListener();
      addAnalysisContentDetailDownloadViewListener();
      addAnalysisLinkListenerDetailViewListener();
      addWorkflowDetailViewListener();
      addAbTestingDetailViewListener();
      addWorkflowUrlShortenerDetailViewListener();
      addDescriptionListener();
      addLinkMockListener();
      addConfirmListeners();
      asynchronousImageLoading();
      addToggleListener();
    };

    /**
     * Add listener for lead/list detail ajax view
     *
     * @returns {void}
     */
    var addLeadListDetailViewListener = function() {
      var elements = document.querySelectorAll('[data-lux-action-leadlistdetail]');
      for (var i = 0; i < elements.length; i++) {
        var element = elements[i];
        element.addEventListener('click', function() {
          removeClassFromElements(elements, 'lux-action-detail');
          this.classList.add('lux-action-detail');
          var visitor = this.getAttribute('data-lux-action-leadlistdetail');

          ajaxConnection(TYPO3.settings.ajaxUrls['/lux/leadlistdetail'], {
            visitor: visitor
          }, 'generalDetailCallback');
        });
      }
    };

    /**
     * Add listener for analysis/content (page) detail ajax view
     *
     * @returns {void}
     */
    var addAnalysisContentDetailPageViewListener = function() {
      var elements = document.querySelectorAll('[data-lux-action-analysiscontentdetailpage]');
      for (var i = 0; i < elements.length; i++) {
        var element = elements[i];
        element.addEventListener('click', function() {
          removeClassFromElements(elements, 'lux-action-detail');
          this.classList.add('lux-action-detail');
          var page = this.getAttribute('data-lux-action-analysiscontentdetailpage');

          ajaxConnection(TYPO3.settings.ajaxUrls['/lux/analysiscontentdetailpage'], {
            page: page
          }, 'generalDetailCallback');
        });
      }
    };

    /**
     * Add listener for analysis/content (news) detail ajax view
     *
     * @returns {void}
     */
    var addAnalysisNewsDetailPageViewListener = function() {
      var elements = document.querySelectorAll('[data-lux-action-analysisnewsdetailpage]');
      for (var i = 0; i < elements.length; i++) {
        var element = elements[i];
        element.addEventListener('click', function() {
          removeClassFromElements(elements, 'lux-action-detail');
          this.classList.add('lux-action-detail');
          var news = this.getAttribute('data-lux-action-analysisnewsdetailpage');

          ajaxConnection(TYPO3.settings.ajaxUrls['/lux/analysisnewsdetailpage'], {
            news: news
          }, 'generalDetailCallback');
        });
      }
    };

    /**
     * Add listener for analysis/utm detail ajax view
     *
     * @returns {void}
     */
    var addAnalysisUtmDetailPageViewListener = function() {
      var elements = document.querySelectorAll('[data-lux-action-analysisutmdetailpage]');
      for (var i = 0; i < elements.length; i++) {
        var element = elements[i];
        element.addEventListener('click', function() {
          removeClassFromElements(elements, 'lux-action-detail');
          this.classList.add('lux-action-detail');
          var visitor = this.getAttribute('data-lux-action-analysisutmdetailpage');

          ajaxConnection(TYPO3.settings.ajaxUrls['/lux/analysisutmdetailpage'], {
            visitor: visitor
          }, 'generalDetailCallback');
        });
      }
    };

    /**
     * Add listener for analysis/content (search) detail ajax view
     *
     * @returns {void}
     */
    var addAnalysisSearchDetailPageViewListener = function() {
      var elements = document.querySelectorAll('[data-lux-action-analysissearchdetailpage]');
      for (var i = 0; i < elements.length; i++) {
        var element = elements[i];
        element.addEventListener('click', function() {
          removeClassFromElements(elements, 'lux-action-detail');
          this.classList.add('lux-action-detail');
          var searchterm = this.getAttribute('data-lux-action-analysissearchdetailpage');

          ajaxConnection(TYPO3.settings.ajaxUrls['/lux/analysissearchdetailpage'], {
            searchterm: searchterm
          }, 'generalDetailCallback');
        });
      }
    };

    /**
     * Add listener for analysis/content (download) detail ajax view
     *
     * @returns {void}
     */
    var addAnalysisContentDetailDownloadViewListener = function() {
      var elements = document.querySelectorAll('[data-lux-action-analysiscontentdetaildownload]');
      for (var i = 0; i < elements.length; i++) {
        var element = elements[i];
        element.addEventListener('click', function() {
          removeClassFromElements(elements, 'lux-action-detail');
          this.classList.add('lux-action-detail');
          var download = this.getAttribute('data-lux-action-analysiscontentdetaildownload');

          ajaxConnection(TYPO3.settings.ajaxUrls['/lux/analysiscontentdetaildownload'], {
            download: download
          }, 'generalDetailCallback');
        });
      }
    };

    /**
     * Add listener for analysis/linklistener detail ajax view
     *
     * @returns {void}
     */
    var addAnalysisLinkListenerDetailViewListener = function() {
      var elements = document.querySelectorAll('[data-lux-action-analysislinklistenerdetail]');
      for (var i = 0; i < elements.length; i++) {
        var element = elements[i];
        element.addEventListener('click', function() {
          removeClassFromElements(elements, 'lux-action-detail');
          this.classList.add('lux-action-detail');
          var linkListener = this.getAttribute('data-lux-action-analysislinklistenerdetail');

          ajaxConnection(TYPO3.settings.ajaxUrls['/lux/analysislinklistenerdetail'], {
            linkListener: linkListener
          }, 'generalDetailCallback');
        });
      }
    };

    /**
     * Add listener for workflow/list detail ajax view
     *
     * @returns {void}
     */
    var addWorkflowDetailViewListener = function() {
      var elements = document.querySelectorAll('[data-lux-action-workflowdetail]');
      for (var i = 0; i < elements.length; i++) {
        var element = elements[i];
        element.addEventListener('click', function() {
          removeClassFromElements(elements, 'lux-action-detail');
          this.classList.add('lux-action-detail');
          var workflow = this.getAttribute('data-lux-action-workflowdetail');

          ajaxConnection(TYPO3.settings.ajaxUrls['/luxenterprise/workflowdetail'], {
            workflow: workflow
          }, 'generalDetailCallback');
        });
      }
    };

    /**
     * Add listener for abtesting/list detail ajax view
     *
     * @returns {void}
     */
    var addAbTestingDetailViewListener = function() {
      var elements = document.querySelectorAll('[data-lux-action-abtestingdetail]');
      for (var i = 0; i < elements.length; i++) {
        var element = elements[i];
        element.addEventListener('click', function() {
          removeClassFromElements(elements, 'lux-action-detail');
          this.classList.add('lux-action-detail');
          var abTesting = this.getAttribute('data-lux-action-abtestingdetail');

          ajaxConnection(TYPO3.settings.ajaxUrls['/luxenterprise/abtestingdetail'], {
            abTesting: abTesting
          }, 'generalDetailCallback');
        });
      }
    };

    /**
     * Add listener for workflow/urlshortener detail ajax view
     *
     * @returns {void}
     */
    var addWorkflowUrlShortenerDetailViewListener = function() {
      var elements = document.querySelectorAll('[data-lux-action-workflowurlshortenerdetail]');
      for (var i = 0; i < elements.length; i++) {
        var element = elements[i];
        element.addEventListener('click', function() {
          removeClassFromElements(elements, 'lux-action-detail');
          this.classList.add('lux-action-detail');
          var urlShortener = this.getAttribute('data-lux-action-workflowurlshortenerdetail');

          ajaxConnection(TYPO3.settings.ajaxUrls['/luxenterprise/workflowurlshortenerdetail'], {
            urlShortener: urlShortener
          }, 'generalDetailCallback');
        });
      }
    };

    /**
     * @returns {void}
     */
    var addDescriptionListener = function() {
      var container = document.querySelector('[data-lux-container="detail"]');
      if (container !== null) {
        container.addEventListener('click', function(event) {
          var clickedElement = event.target;
          if (clickedElement.getAttribute('data-lux-visitor-description') > 0) {
            if (clickedElement.classList.contains('lux-textarea__default')) {
              clickedElement.classList.remove('lux-textarea__default');
              clickedElement.value = '';
            }
            var visitor = clickedElement.getAttribute('data-lux-visitor-description');
            clickedElement.addEventListener('blur', function() {
              ajaxConnection(TYPO3.settings.ajaxUrls['/lux/visitordescription'], {
                visitor: visitor,
                value: this.value
              }, null);
            });
          }
        });
      }
    };

    /**
     * @returns {void}
     */
    var addLinkMockListener = function() {
      var container = document.querySelector('[data-lux-container="detail"]');
      if (container !== null) {
        container.addEventListener('click', function(event) {
          var clickedElement = event.target;
          if (clickedElement.getAttribute('data-lux-linkmock-event') !== null) {
            var name = clickedElement.getAttribute('data-lux-linkmock-event');
            var target = document.querySelector('[data-lux-linkmock-link="' + name + '"]');
            if (target !== null) {
              target.click();
            }
          }
        });
      }
    };

    /**
     * @params {Json} response
     */
    this.generalDetailCallback = function(response) {
      document.querySelector('[data-lux-container="detail"]').innerHTML = response.html;
      window.LuxDiagramObject.initialize();
    };

    /**
     * @returns {void}
     */
    var addConfirmListeners = function() {
      var elements = document.querySelectorAll('[data-lux-confirm]');
      for (var i = 0; i < elements.length; i++) {
        elements[i].addEventListener('click', function(event) {
          var message = event.currentTarget.getAttribute('data-lux-confirm');
          if (confirm(message) === false) {
            event.preventDefault();
          }
        });
      }
    };

    /**
     * This allows to get visitor images (maybe from google or gravatar) as asynchronous request, to not block page
     * rendering.
     * This function is used in LUX backend modules and also in PageOverview.html
     *
     * @returns {void}
     */
    const asynchronousImageLoading = function() {
      const elements = document.querySelectorAll('[data-lux-asynchronous-image]');
      for (let i = 0; i < elements.length; i++) {
        let visitorIdentifier = elements[i].getAttribute('data-lux-asynchronous-image');
        if (visitorIdentifier > 0) {
          ajaxConnection(TYPO3.settings.ajaxUrls['/lux/visitorimage'], {
            visitor: visitorIdentifier
          }, 'asynchronousImageLoadingCallback', {element: elements[i]});
        }
      }
    };

    /**
     * @params {Json} response
     */
    this.asynchronousImageLoadingCallback = function(response, callbackArguments) {
      if (callbackArguments.element instanceof HTMLImageElement) {
        callbackArguments.element.setAttribute('src', response.url)
      }
    };


    /**
     * Toggle elements
     *
     * Switches with [data-lux-action-toggleaction="anything"]
     * Toggles all targets with [data-lux-action-togglecontainer="anything"]
     *
     * @returns {void}
     */
    const addToggleListener = function() {
      const elements = document.querySelectorAll('[data-lux-action-toggleaction]');
      elements.forEach(function(element) {
        element.addEventListener('click', function(event) {
          let identifier = event.currentTarget.getAttribute('data-lux-action-toggleaction');
          let targetElements = document.querySelectorAll('[data-lux-action-togglecontainer="' + identifier + '"]');
          targetElements.forEach(function(targetElement) {
            targetElement.classList.toggle('hidden');
          });
        });
      });
    };

    /**
     * @param {string} elements
     * @param {string} className
     * @returns {void}
     */
    var removeClassFromElements = function(elements, className) {
      for (var i = 0; i < elements.length; i++) {
        elements[i].classList.remove(className);
      }
    };

    /**
     * @params {string} uri
     * @params {object} parameters
     * @params {string} callback function name
     * @returns {void}
     */
    var ajaxConnection = function(uri, parameters, callback, callbackArguments) {
      callbackArguments = callbackArguments || {};
      if (uri !== undefined && uri !== '') {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
          if (this.readyState === 4 && this.status === 200) {
            if (callback !== null) {
              that[callback](JSON.parse(this.responseText), callbackArguments);
            }
          }
        };
        xhttp.open('POST', mergeUriWithParameters(uri, parameters), true);
        xhttp.send();
      } else {
        console.log('No ajax URI given!');
      }
    };

    /**
     * Build an uri string for an ajax call together with params from an object
     *    {
     * 			'x': 123,
     * 			'y': 'abc'
     * 		}
     *
     *    =>
     *
     *    "?x=123&y=abc"
     *
     * @params {string} uri
     * @params {object} parameters
     * @returns {string} e.g. "index.php?id=123&type=123&x=123&y=abc"
     */
    var mergeUriWithParameters = function(uri, parameters) {
      for (var key in parameters) {
        if (parameters.hasOwnProperty(key)) {
          if (uri.indexOf('?') !== -1) {
            uri += '&';
          } else {
            uri += '?';
          }
          uri += key + '=' + encodeURIComponent(parameters[key]);
        }
      }
      return uri;
    };
  }


  /**
   * Init
   */
  $(document).ready(function() {
    var LuxBackendObject = new LuxBackend($);
    LuxBackendObject.initialize();
  })
});
