/*!
 * ButtonAction v1.0.0
 * Copyright 2022 TirekWeed95
 */
(function (global, factory) {
    if (typeof exports === "object" && typeof module !== "undefined") {
        factory(exports, require("jquery"));
    } else if (typeof define === "function" && define.amd) {
        define(["exports", "jquery"], factory);
    } else {
        factory((global.postform = {}), global.jQuery);
    }
})(this, function (exports, $) {
    "use strict";

    function _interopDefaultLegacy(e) {
        return e && typeof e === "object" && "default" in e
            ? e
            : { default: e };
    }

    var $_default = _interopDefaultLegacy($);

    var ELEMENT = "button.btn-lazy-control";
    var X_CSR_TOKEN = 'meta[name="csrf-token"]';
    var DATA_ACTION = "action";
    var DATA_SOURCE = "source";
    var DATA_INTENT = "intent";
    var DATA_METHOD = "method";
    var DATA_DIAL = "dial";
    var Defaults = {
        action: null,
        source: null,
        intent: null,
        method: "redirect",
        dial: null,
    };
    var Static = {
        content: "content",
        redirect : "redirect",
        view : "view",
        download : "download",
        modal : "modal",
        table : "table",
        database : "database",
        form : "form"
    };

    var ButtonAction = (function () {
        function ButtonAction(element, settings) {
            this._element = element;
            this._settings = $_default["default"].extend(
                {},
                Defaults,
                settings
            );
        }

        var _proto = ButtonAction.prototype;

        _proto._redirect = function _redirect() {
            window.location.href = this._settings.action;
        };

        _proto._view = function _view() {
            if(this._settings.source == null) {
                throw new Error(
                    "Data source was not defined. Please specify a action in your button data-source option."
                );
            }

            var data = this.populateData();

            if(this._settings.intent == null) {
                this._settings.intent = this.createModal();
            }

            if(this._settings.dial === Static.form) {
                $_default['default'](this._settings.intent).find(this._settings.dial).data(DATA_ACTION, this._settings.action)
            }

            $_default["default"](this._settings.intent).find(".modal-body").html(data);

            $_default["default"](this._settings.intent).modal({
                backdrop: "static",
                keyboard: false,
            });
        };

        _proto.createModal = function createModal() {
            var modal = dialog = content = header = body = footer = $_default["default"]("<div>");
            var title = $_default["default"]("<h4>");
            var close = $_default["default"]('<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">');

            modal.addClass("modal fade");
            modal.attr("id", "viewModal");

            dialog.addClass("modal-dialog");
            modal.append(dialog);

            content.addClass("modal-content");
            dialog.append(content);

            header.addClass("modal-header p-2");
            title.addClass("modal-title");
            title.text("Lihat Detil Data");
            header.append(title);

            content.append(header);
            
            body.addClass("modal-body p-2");
            body.append($_default["default"]('<div class="row">'));
            content.append(body);

            footer.addClass("modal-footer p-2");
            close.text("Tutup");
            footer.append(close);
            content.append(footer);

            $_default["default"]("body").append(modal);

            return modal.attr("id");
        };

        _proto.populateData = function populateData() {
            var data = "";

            if(this._settings.source == Static.table) {
                data = this.tableData();
            } else if(this._settings.source == Static.database) {
                data = this.baseData();
            } else {
                throw new Error(
                    "Data source not supported. supported data source [table, database]."
                );
            }

            return data;
        };

        _proto.tableData = function tableData() {
            var _tr = this._element.closest("tr");
            var _table = _tr.parent().parent();
            var _columnLength = _table.find("tr th").length;
            var _rows = "";
            for (var i = 1; i < _columnLength - 1; i++) {
                var _label = _table.find("thead tr th:eq(" + i + ")").text();
                var _value = _tr.find("td:eq(" + i + ")").text();
                if (this._settings.method === "view") {
                    _rows +=
                        '<div class="col-sm-6 text-right">' +
                        _label +
                        '</div><div class="col-sm-6 text-left">: ' +
                        _value +
                        "</div>";
                }
            }

            return _rows;
        };

        _proto.baseData = function baseData() {
            var _card = this._element.closest("div.card");
            var _body = _card.find('.card-body').clone();

            return _body;
        }


        _proto._init = function _init() {
            if (this._settings.method == Static.redirect) {
                this._redirect();
            }

            if (this._settings.method == Static.view) {
                this._view();
            }

            if (this._settings.method == Static.download) {
                //this._download();
            }
        };

        ButtonAction._jQueryInterface = function _jQueryInterface() {
            var options = {};

            if (
                $_default["default"](this).data(DATA_ACTION) !==
                "undefined"
            ) {
                options.action = $_default["default"](this).data(DATA_ACTION);
            } else {
                options.action = $_default["default"](location).attr("href");
            }

            if (
                typeof $_default["default"](this).data(DATA_METHOD) !== "undefined"
            ) {
                options.method = $_default["default"](this).data(DATA_METHOD);
            }

            if (
                typeof $_default["default"](this).data(DATA_SOURCE) !== "undefined"
            ) {
                options.source = $_default["default"](this).data(DATA_SOURCE);
            }

            if (
                typeof $_default["default"](this).data(DATA_INTENT) !==
                "undefined"
            ) {
                options.intent = $_default["default"](this).data(DATA_INTENT);
            }

            if (
                typeof $_default["default"](this).data(DATA_DIAL) !==
                "undefined"
            ) {
                options.dial = $_default["default"](this).data(DATA_DIAL);
            }

            var _options = $_default["default"].extend({}, Defaults, options);
            var data = new ButtonAction($_default["default"](this), _options);
            data._init();

        };

        return ButtonAction;
    })();

    $_default["default"](ELEMENT).on("click", function (event) {
        if (event) {
            event.preventDefault();
        }

        ButtonAction._jQueryInterface.call($_default["default"](this));
    });

    $_default["default"].fn["ButtonAction"] = ButtonAction._jQueryInterface;
    $_default["default"].fn["ButtonAction"].Constructor = ButtonAction;

    exports.ButtonAction = ButtonAction;
    Object.defineProperty(exports, "__esModule", { value: true });
});
