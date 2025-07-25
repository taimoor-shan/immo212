/******/ (() => { // webpackBootstrap
/*!***************************************************************************!*\
  !*** ./platform/plugins/real-estate/resources/js/global-custom-fields.js ***!
  \***************************************************************************/
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _defineProperty(e, r, t) { return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, { value: t, enumerable: !0, configurable: !0, writable: !0 }) : e[r] = t, e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
$(document).ready(function () {
  var CustomField = /*#__PURE__*/function () {
    function CustomField() {
      _classCallCheck(this, CustomField);
      _defineProperty(this, "$customFieldOptions", $('#custom-field-options'));
      _defineProperty(this, "$customFieldsBox", $('#custom_fields_box'));
    }
    return _createClass(CustomField, [{
      key: "init",
      value: function init() {
        this.sortable();
        this.handleType();
        this.addNewRow();
        this.removeRow();
      }
    }, {
      key: "handleType",
      value: function handleType() {
        var $customFieldsBox = this.$customFieldsBox;
        var $type = $('.custom-field-type');
        if ($type.val() === 'dropdown') {
          this.$customFieldsBox.show();
        } else {
          this.$customFieldsBox.hide();
        }
        $type.change(function () {
          if ($(this).val() === 'dropdown') {
            $customFieldsBox.show();
            return;
          }
          $customFieldsBox.hide();
        });
      }
    }, {
      key: "sortable",
      value: function sortable() {
        $('.option-sortable').sortable({
          stop: function stop() {
            $('.option-sortable').sortable('toArray', {
              attribute: 'data-index'
            }).map(function (id, index) {
              $(".option-row[data-index=\"".concat(id, "\"]")).find('.option-order').val(index);
            });
          }
        });
      }
    }, {
      key: "addNewRow",
      value: function addNewRow() {
        this.$customFieldsBox.on('click', '#add-new-row', function () {
          var table = $(this).closest('.card').find('table tbody');
          var tr = table.find('tr').last().clone();
          var label = "options[".concat(table.find('tr').length, "][label]");
          var value = "options[".concat(table.find('tr').length, "][value]");
          tr.find('.option-label').val('').attr('name', label);
          tr.find('.option-value').val('').attr('name', value);
          table.append(tr);
        });
      }
    }, {
      key: "removeRow",
      value: function removeRow() {
        this.$customFieldOptions.on('click', '.remove-row', function () {
          var self = $(this).parent().parent();
          var parent = self.parent();
          var tr = parent.find('tr');
          if (tr.length <= 1) {
            tr.find('.option-label').val('');
            tr.find('.option-value').val('');
            return;
          }
          self.remove();
        });
      }
    }]);
  }();
  new CustomField().init();
});
/******/ })()
;
//# sourceMappingURL=global-custom-fields.js.map