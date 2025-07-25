/******/ (() => { // webpackBootstrap
/*!******************************************************************!*\
  !*** ./platform/plugins/real-estate/resources/js/bulk-import.js ***!
  \******************************************************************/
function _slicedToArray(r, e) { return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _iterableToArrayLimit(r, l) { var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (null != t) { var e, n, i, u, a = [], f = !0, o = !1; try { if (i = (t = t.call(r)).next, 0 === l) { if (Object(t) !== t) return; f = !1; } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0); } catch (r) { o = !0, n = r; } finally { try { if (!f && null != t["return"] && (u = t["return"](), Object(u) !== u)) return; } finally { if (o) throw n; } } return a; } }
function _arrayWithHoles(r) { if (Array.isArray(r)) return r; }
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _toConsumableArray(r) { return _arrayWithoutHoles(r) || _iterableToArray(r) || _unsupportedIterableToArray(r) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _iterableToArray(r) { if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r); }
function _arrayWithoutHoles(r) { if (Array.isArray(r)) return _arrayLikeToArray(r); }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
$(function () {
  var container = $(document).find('#bulk-import');
  var $form = container.find('.form-import-data');
  var $button = container.find('.btn-import');
  var failedRows = [];
  var totalRows = 0;
  $(document).on('click', '.btn-import', function (event) {
    event.preventDefault();
    if (dropzone.getQueuedFiles().length > 0) {
      Botble.showButtonLoading($button);
      container.find('.show-errors').hide();
      totalRows = 0;
      failedRows = [];
      dropzone.processQueue();
    }
    dropzone.on('sending', function () {
      container.find('.bulk-import-message').removeClass('alert-success').addClass('alert-info').text($button.data('uploading-text')).show();
    });
    dropzone.on('error', function (file, message) {
      Botble.showError(message.message);
    });
  });
  var _validateData = function validateData(file) {
    var offset = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 0;
    var limit = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 1000;
    if (offset === 0) {
      container.find('.bulk-import-message').text($button.data('validating-text'));
    }
    $httpClient.make().post($form.data('validate-url'), {
      file: file,
      offset: offset,
      limit: limit,
      update_existing: $('#update-existing').is(':checked') ? 1 : 0
    }).then(function (_ref) {
      var response = _ref.data;
      var data = response.data,
        message = response.message;

      // Check if there are any validation errors in the current batch
      if (data && data.failed && Object.keys(data.failed).length > 0) {
        // We have validation errors, stop the validation process
        failedRows = [].concat(_toConsumableArray(failedRows), _toConsumableArray(data.failed));
        totalRows += data.count;

        // Display the errors immediately
        var $listing = container.find('#imported-listing');
        var $show = container.find('.show-errors');
        var failureTemplate = $(document).find('#failure-template').html();
        var result = '';
        failedRows.forEach(function (val, index) {
          // If row is undefined, use the index + 1 as the row number
          var rowNumber = val.row !== undefined ? val.row : index + 1;
          // Format the error messages
          var errorMessages = [];

          // Log the error structure for debugging
          console.log('Error data structure:', val);

          // Handle different error formats
          if (Array.isArray(val.errors)) {
            // Direct array of error messages
            errorMessages = val.errors;
          } else if (_typeof(val.errors) === 'object' && val.errors !== null) {
            // Object with field names as keys and arrays of error messages as values
            Object.entries(val.errors).forEach(function (_ref2) {
              var _ref3 = _slicedToArray(_ref2, 2),
                field = _ref3[0],
                messages = _ref3[1];
              if (Array.isArray(messages)) {
                // Add field name to each message
                messages.forEach(function (message) {
                  errorMessages.push("".concat(field, ": ").concat(message));
                });
              } else if (typeof messages === 'string') {
                errorMessages.push("".concat(field, ": ").concat(messages));
              }
            });
          }

          // If errorMessages is still empty, try to extract errors directly from the object
          if (errorMessages.length === 0 && _typeof(val) === 'object' && val !== null) {
            Object.entries(val).forEach(function (_ref4) {
              var _ref5 = _slicedToArray(_ref4, 2),
                field = _ref5[0],
                value = _ref5[1];
              if (field !== 'row' && Array.isArray(value)) {
                value.forEach(function (message) {
                  errorMessages.push("".concat(field, ": ").concat(message));
                });
              } else if (field !== 'row' && typeof value === 'string') {
                errorMessages.push("".concat(field, ": ").concat(value));
              }
            });
          }
          result += failureTemplate.replace('__row__', rowNumber).replace('__errors__', errorMessages.join(', '));
        });
        $show.show();
        container.find('.main-form-message').show();
        $listing.show().html(result);
        failedRows = [];
        totalRows = 0;
        Botble.hideButtonLoading($button);
        dropzone.removeAllFiles();
        container.find('.bulk-import-message').hide();
      } else if (data && data.count > 0) {
        // No errors in this batch, continue validation
        container.find('.bulk-import-message').show();
        container.find('.bulk-import-message').text(message);
        _validateData(file, data.offset);
        failedRows = [].concat(_toConsumableArray(failedRows), _toConsumableArray(data.failed));
        totalRows += data.count;
      } else {
        // No errors and no more data to validate, proceed to import
        _importData(file);
      }
    });
  };
  var _importData = function importData(file) {
    var offset = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 0;
    var limit = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 10;
    if (offset === 0) {
      container.find('.bulk-import-message').text($button.data('importing-text'));
      Botble.showButtonLoading($button);
    }
    $httpClient.make().post($form.data('import-url'), {
      file: file,
      offset: offset,
      limit: limit,
      update_existing: $('#update-existing').is(':checked') ? 1 : 0
    }).then(function (_ref6) {
      var response = _ref6.data;
      var data = response.data,
        message = response.message;
      var processing = container.find('.processing');
      var process = processing.find('.process');
      if (data && data.count > 0) {
        processing.show();
        _importData(file, data.offset);
        process.css('width', data.offset / totalRows * 100 + '%');
        container.find('.bulk-import-message').html(message);
      } else {
        Botble.showSuccess(message);
        if (data.total_message) {
          container.find('.main-form-message').show();
          container.find('.bulk-import-message').removeClass('alert-info').addClass('alert-success').text(data.total_message).show();
          dropzone.removeAllFiles();
          processing.hide();
          totalRows = 0;
          Botble.hideButtonLoading($button);
        }
      }
    });
  };
  var dropzone = new Dropzone('.import-dropzone', {
    url: $form.data('upload-url'),
    method: 'post',
    headers: {
      'X-CSRF-TOKEN': $form.find('input[name=_token]').val()
    },
    previewTemplate: $(document).find('#preview-template').html(),
    autoProcessQueue: false,
    chunking: true,
    chunkSize: 1048576,
    acceptedFiles: $form.find('.import-properties-dropzone').data('mimetypes'),
    maxFiles: 1,
    maxfilesexceeded: function maxfilesexceeded(file) {
      this.removeFile(file);
    },
    success: function success(file, response) {
      var data = response.data,
        message = response.message;
      if (data && data.file_path) {
        _validateData(data.file_path);
      }
    }
  });
  var isDownloadingTemplate = false;
  $(document).on('click', '.download-template', function (event) {
    event.preventDefault();
    if (isDownloadingTemplate) {
      return;
    }
    var $this = $(event.currentTarget);
    var extension = $this.data('extension');
    var $content = $this.html();
    $this.html($this.data('downloading'));
    $this.addClass('text-secondary');
    isDownloadingTemplate = true;
    $httpClient.make().withResponseType('blob').post($this.data('url'), {
      extension: extension
    }).then(function (_ref7) {
      var data = _ref7.data;
      var a = document.createElement('a');
      var url = window.URL.createObjectURL(data);
      a.href = url;
      a.download = $this.data('filename');
      document.body.append(a);
      a.click();
      a.remove();
      window.URL.revokeObjectURL(url);
    })["finally"](function () {
      setTimeout(function () {
        $this.html($content);
        $this.removeClass('text-secondary');
        isDownloadingTemplate = false;
      }, 2000);
    });
  });
});
/******/ })()
;
//# sourceMappingURL=bulk-import.js.map