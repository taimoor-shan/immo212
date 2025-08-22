/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/*!********************************************************************!*\
  !*** ./platform/plugins/announcement/resources/js/announcement.js ***!
  \********************************************************************/


function _toConsumableArray(r) { return _arrayWithoutHoles(r) || _iterableToArray(r) || _unsupportedIterableToArray(r) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _iterableToArray(r) { if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r); }
function _arrayWithoutHoles(r) { if (Array.isArray(r)) return _arrayLikeToArray(r); }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
var setCookie = function setCookie(name, value, days) {
  var expires = '';
  if (days) {
    var date = new Date();
    date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
    expires = "; expires=".concat(date.toUTCString());
  }
  document.cookie = "".concat(name, "=").concat(value || '').concat(expires, "; path=/");
};
var getCookie = function getCookie(name) {
  var nameEQ = name + '=';
  var ca = document.cookie.split(';');
  for (var i = 0; i < ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) === ' ') c = c.substring(1, c.length);
    if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
  }
  return null;
};
document.addEventListener('DOMContentLoaded', function () {
  var init = function init() {
    var wrapper = document.querySelector('.ae-anno-announcement-wrapper');
    if (!wrapper) {
      return;
    }
    var announcements = wrapper.querySelectorAll('.ae-anno-announcement');
    var nextBtn = document.querySelector('.ae-anno-announcement__next-button');
    var prevBtn = document.querySelector('.ae-anno-announcement__previous-button');
    var dismissButton = document.querySelector('.ae-anno-announcement__dismiss-button');
    var autoplay = wrapper.getAttribute('data-announcement-autoplay') !== null;
    var autoplayDelay = parseInt(wrapper.getAttribute('data-announcement-autoplay-delay') || 5000);
    var dismissedAnnouncements = JSON.parse(getCookie('ae-anno-dismissed-announcements') || '[]');
    var currentIndex = 1;
    var autoplayInterval = null;
    var autoplayAnnouncement = function autoplayAnnouncement() {
      if (autoplay && autoplayDelay) {
        if (autoplayInterval) {
          clearInterval(autoplayInterval);
        }
        autoplayInterval = setInterval(function () {
          currentIndex++;
          showAnnouncement(currentIndex);
        }, autoplayDelay);
      }
    };
    var showAnnouncement = function showAnnouncement() {
      if (currentIndex > announcements.length) {
        currentIndex = 1;
      } else if (currentIndex < 1) {
        currentIndex = announcements.length;
      }
      announcements.forEach(function (announcement) {
        announcement.style.display = 'none';
      });
      announcements[currentIndex - 1].style.display = 'block';
      autoplayAnnouncement();
    };
    showAnnouncement(currentIndex);
    if (nextBtn) {
      nextBtn.addEventListener('click', function () {
        currentIndex++;
        showAnnouncement(currentIndex);
      });
    }
    if (prevBtn) {
      prevBtn.addEventListener('click', function () {
        showAnnouncement(currentIndex--);
      });
    }
    if (dismissButton) {
      dismissButton.addEventListener('click', function () {
        var ids = JSON.parse(dismissButton.getAttribute('data-announcement-ids'));
        dismissedAnnouncements.push.apply(dismissedAnnouncements, _toConsumableArray(ids));
        setCookie('ae-anno-dismissed-announcements', JSON.stringify(dismissedAnnouncements), 365);
        wrapper.parentNode.removeChild(wrapper);
      });
    }
    autoplayAnnouncement();
  };
  var lazyLoading = $('[data-bb-toggle="announcement-lazy-loading"]');
  if (lazyLoading.length) {
    fetch(lazyLoading.data('url'), {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        // Sending JSON
        'Accept': 'application/json' // Requesting JSON response
      }
    }).then(function (response) {
      // Check if the response is okay and parse it as JSON
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();
    }).then(function (_ref) {
      var data = _ref.data;
      // Replace the content of lazyLoading with the fetched data
      lazyLoading.replaceWith(data);

      // Call the init function after replacing the content
      init();
    })["catch"](function (error) {
      console.error('Fetch error:', error);
    });
  } else {
    init();
  }
});
/******/ })()
;
//# sourceMappingURL=announcement.js.map