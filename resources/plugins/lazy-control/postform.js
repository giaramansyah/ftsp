/*!
 * PostForm v1.0.0
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

    var ELEMENT = "form.form-lazy-control";
    var X_CSR_TOKEN = 'meta[name="csrf-token"]';
    var DATA_ACTION = "action";
    var DATA_METHOD = "method";
    var DATA_VALIDATE = "validate";
    var ERR_SPAN = "span";
    var ERR_SPAN_CLASS = "is-invalid";
    var ERR_CLASS = "invalid-feedback";
    var FORM_CONTROL = ".form-group";
    var FORM_CONTROL_GROUP = ".input-group";
    var FORM_CONTROL_ROW = ".form-group.row div";
    var FORM_LOADING = ".form-loading";
    var FORM_BUTTON = ".form-button";
    var Defaults = {
        action: null,
        json: null,
        data: null,
        validate: {},
        method: "post",
    };
    var Static = {
        content: "content",
        username: "username",
        password: "password",
        file: "file",
        post: "post",
        upload: "upload",
    };

    var PostForm = (function () {
        function PostForm(element, settings) {
            this._element = element;
            this._settings = $_default["default"].extend(
                {},
                Defaults,
                settings
            );
            this._toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
            });
        }

        var _proto = PostForm.prototype;

        _proto._generate = function _generate() {
            var _appender = [
                [0, 0, 1, 1, 0, 0, 0, 0],
                [0, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 1, 1, 0, 0, 0, 0],
                [0, 0, 1, 1, 0, 0, 0, 0],
            ];
            var _secret = [
                ["7", "5", "31"],
                ["7", "1", "27"],
                ["7", "5", "34"],
                ["6", "e", "48"],
                ["6", "9", "10"],
                ["5", "1", "17"],
                ["6", "9", "14"],
                ["3", "d", "50"],
                ["6", "5", "3"],
                ["6", "e", "16"],
                ["4", "6", "20"],
                ["7", "1", "24"],
                ["4", "e", "18"],
                ["6", "4", "21"],
                ["3", "6", "44"],
                ["3", "1", "26"],
                ["7", "5", "37"],
                ["7", "3", "2"],
                ["6", "8", "9"],
                ["7", "8", "35"],
                ["6", "f", "19"],
                ["4", "9", "28"],
                ["6", "1", "1"],
                ["3", "0", "15"],
                ["6", "6", "47"],
                ["6", "2", "0"],
                ["3", "4", "5"],
                ["7", "4", "13"],
                ["6", "c", "11"],
                ["3", "6", "4"],
                ["3", "5", "33"],
                ["3", "4", "32"],
                ["3", "a", "6"],
                ["7", "2", "25"],
                ["6", "2", "45"],
                ["5", "7", "22"],
                ["5", "a", "46"],
                ["6", "7", "39"],
                ["7", "6", "36"],
                ["6", "5", "29"],
                ["3", "3", "23"],
                ["6", "7", "49"],
                ["3", "4", "8"],
                ["4", "9", "41"],
                ["7", "6", "12"],
                ["4", "e", "38"],
                ["5", "a", "7"],
                ["3", "4", "43"],
                ["2", "f", "30"],
                ["6", "6", "42"],
                ["5", "1", "40"],
            ];

            var append =_appender
                .map((char) => {
                    var elem = char.join("");
                    return String.fromCharCode(parseInt(elem, 2));
                })
                .join("");
            var arr = [];
            for (var i = 0; i < _secret.length; i++) {
                var j = parseInt( _secret[i].at(2));
                _secret[i].pop();
                arr[j] = append + _secret[i].join("");
                arr[j] = String.fromCharCode(parseInt(arr[j], "16"));
            }

            return arr.join("").substring(7);
        };

        _proto._secure = function _secure(data) {
            let iv = CryptoJS.lib.WordArray.random(16),
                key = CryptoJS.enc.Base64.parse(this._generate());
            let options = {
                iv: iv,
                mode: CryptoJS.mode.CBC,
                padding: CryptoJS.pad.Pkcs7,
            };
            let encrypted = CryptoJS.AES.encrypt(data, key, options);
            encrypted = encrypted.toString();
            iv = CryptoJS.enc.Base64.stringify(iv);
            let result = {
                iv: iv,
                value: encrypted,
                mac: CryptoJS.HmacSHA256(iv + encrypted, key).toString(),
            };
            result = JSON.stringify(result);
            result = CryptoJS.enc.Utf8.parse(result);
            return CryptoJS.enc.Base64.stringify(result);
        };

        _proto._serializeObject = function _serializeObject() {
            var _object = {};
            var _origin = $_default["default"](this._element).serializeArray();
            $_default["default"].each(_origin, function () {
                if (
                    this.name.indexOf("[") >= 0 &&
                    this.name.indexOf("]") >= 0
                ) {
                    var split = this.name.split("[");
                    var newName = split[0];
                    var arrValue = split[1].replace("]", "");

                    if (!_object[newName]) {
                        _object[newName] = {};
                    }

                    if (_object[newName][arrValue]) {
                        _object[newName][arrValue].push(this.value);
                    } else {
                        _object[newName][arrValue] = [this.value];
                    }
                } else {
                    if (_object[this.name]) {
                        if (!_object[this.name].push) {
                            _object[this.name] = [_object[this.name]];
                        }
                        _object[this.name].push(this.value || "");
                    } else {
                        _object[this.name] = this.value || "";
                    }
                }
            });

            return this._secure(btoa(JSON.stringify(_object)));
        };

        _proto._ajaxFrom = function _ajaxForm() {
            var _this = this;

            $_default["default"].ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $_default["default"](X_CSR_TOKEN).attr(
                        Static.content
                    ),
                },
            });
            $_default["default"].ajax({
                type: "POST",
                url: _this._settings.action,
                data: { json: _this._settings.json },
                beforeSend: function () {
                    $_default["default"](_this._element)
                        .find(FORM_LOADING)
                        .show();
                    $_default["default"](_this._element)
                        .find(FORM_BUTTON)
                        .hide();
                },
                success: function (response) {
                    _this._toast.fire({
                        icon: response.alert,
                        text: response.message,
                        willClose: () => {
                            if (response.status) {
                                if (response.redirect.length) {
                                    window.location.href = response.redirect;
                                } else {
                                    $_default["default"](_this._element)
                                        .find(FORM_LOADING)
                                        .hide();
                                    $_default["default"](_this._element)
                                        .find(FORM_BUTTON)
                                        .show();
                                }
                            } else {
                                $_default["default"](_this._element)
                                    .find(FORM_LOADING)
                                    .hide();
                                $_default["default"](_this._element)
                                    .find(FORM_BUTTON)
                                    .show();
                            }
                        },
                    });
                },
                error: function () {
                    _this._toast.fire({
                        icon: "error",
                        text: "Internal Server Error",
                        willClose: () => {
                            $_default["default"](_this._element)
                                .find(FORM_LOADING)
                                .hide();
                            $_default["default"](_this._element)
                                .find(FORM_BUTTON)
                                .show();
                        },
                    });
                },
            });
        };

        _proto._ajaxUpload = function _ajaxUpload() {
            var _this = this;

            $_default["default"].ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $_default["default"](
                        'meta[name="csrf-token"]'
                    ).attr("content"),
                },
            });
            $_default["default"].ajax({
                type: "POST",
                url: _this._settings.action,
                data: _this._settings.data,
                processData: false,
                contentType: false,
                cache: false,
                beforeSend: function () {
                    $_default["default"](_this._element)
                        .find(FORM_LOADING)
                        .show();
                    $_default["default"](_this._element)
                        .find(FORM_BUTTON)
                        .hide();
                },
                success: function (response) {
                    _this._toast.fire({
                        icon: response.alert,
                        text: response.message,
                        willClose: () => {
                            $_default["default"](_this._element)
                                .find("p.text-center")
                                .removeClass("text-info");
                            if (response.status) {
                                if (response.redirect.length) {
                                    window.location.href = response.redirect;
                                } else {
                                    $_default["default"](_this._element)
                                        .find(FORM_LOADING)
                                        .hide();
                                    $_default["default"](_this._element)
                                        .find(FORM_BUTTON)
                                        .show();
                                    $_default["default"](_this._element)
                                        .find("input,select")
                                        .val("");
                                    $_default["default"](_this._element)
                                        .find("input,select")
                                        .trigger("change");
                                    $_default["default"](_this._element)
                                        .find(".custom-file-label")
                                        .text("");
                                }
                            } else {
                                $_default["default"](_this._element)
                                    .find(FORM_LOADING)
                                    .hide();
                                $_default["default"](_this._element)
                                    .find(FORM_BUTTON)
                                    .show();
                            }
                        },
                    });
                },
                error: function () {
                    _this._toast.fire({
                        icon: "error",
                        text: "Internal Server Error",
                        willClose: () => {
                            $_default["default"](_this._element)
                                .find(FORM_LOADING)
                                .hide();
                            $_default["default"](_this._element)
                                .find(FORM_BUTTON)
                                .show();
                        },
                    });
                },
            });
        };

        _proto._init = function _init() {
            var _this2 = this;
            var options = {
                lang: $_default["default"]("html").attr("lang"),
                errorElement: ERR_SPAN,
                errorPlacement: function (error, element) {
                    error.addClass(ERR_CLASS);
                    if (element.closest(FORM_CONTROL_ROW).length) {
                        element.closest(FORM_CONTROL_ROW).append(error);
                    } else if (element.closest(FORM_CONTROL_GROUP).length) {
                        element.closest(FORM_CONTROL_GROUP).append(error);
                    } else {
                        element.closest(FORM_CONTROL).append(error);
                    }
                },
                highlight: function (element, errorClass, validClass) {
                    $_default["default"](element).addClass(ERR_SPAN_CLASS);
                },
                unhighlight: function (element, errorClass, validClass) {
                    $_default["default"](element).removeClass(ERR_SPAN_CLASS);
                },
                submitHandler: function () {
                    if ($_default["default"](_this2._element).data(DATA_ACTION) !== "undefined") {
                        _this2._settings.action = $_default["default"](_this2._element).data(DATA_ACTION);
                    } else {
                        _this2._settings.action = $_default["default"](location).attr("href");
                    }

                    if (_this2._settings.method === Static.post) {
                        _this2._settings.json = _this2._serializeObject();
                        _this2._ajaxFrom();
                    }

                    if (_this2._settings.method === Static.upload) {
                        _this2._settings.data = new FormData(
                            $_default["default"](_this2._element)[0]
                        );
                        _this2._ajaxUpload();
                    }
                },
            };

            if (_this2._settings.validate.password) {
                options.rules = {
                    new_password: {
                        required: true,
                        minlength: 8,
                        alphanum: true,
                    },
                    confirm_password: {
                        required: true,
                        minlength: 8,
                        equalTo: "#new_password",
                        alphanum: true,
                    },
                };
            }

            if (_this2._settings.validate.username) {
                options.rules = {
                    username: {
                        required: true,
                        minlength: 6,
                        alphanum: true,
                    },
                };
            }

            if (_this2._settings.validate.file) {
                options.rules = {
                    file: {
                        required: true,
                        extension: "xls|xlsx|csv",
                    },
                };
            }

            _this2._element.validate(options);
        };

        $_default["default"].validator.addMethod("alphanum", function (value) {
            return /[a-zA-Z]/.test(value) && /\d/.test(value);
        });

        PostForm._jQueryInterface = function _jQueryInterface() {
            var options = {};

            if (
                typeof $_default["default"](this).data(DATA_METHOD) !==
                "undefined"
            ) {
                options.method = $_default["default"](this).data(DATA_METHOD);
            }

            if (
                typeof $_default["default"](this).data(DATA_VALIDATE) !==
                "undefined"
            ) {
                options.validate = {};

                if (
                    $_default["default"](this).data(DATA_VALIDATE) ==
                    Static.username
                ) {
                    options.validate.username = true;
                }

                if (
                    $_default["default"](this).data(DATA_VALIDATE) ==
                    Static.password
                ) {
                    options.validate.password = true;
                }

                if (
                    $_default["default"](this).data(DATA_VALIDATE) ==
                    Static.file
                ) {
                    options.validate.file = true;
                }
            }

            var _options = $_default["default"].extend({}, Defaults, options);
            var data = new PostForm($_default["default"](this), _options);
            data._init();
        };

        return PostForm;
    })();

    $_default["default"](ELEMENT).each(function () {
        $_default["default"](this).submit(function (event) {
            event.preventDefault();
        });
        PostForm._jQueryInterface.call($_default["default"](this));
    });

    $_default["default"].fn["PostForm"] = PostForm._jQueryInterface;
    $_default["default"].fn["PostForm"].Constructor = PostForm;

    exports.PostForm = PostForm;
    Object.defineProperty(exports, "__esModule", { value: true });
});
