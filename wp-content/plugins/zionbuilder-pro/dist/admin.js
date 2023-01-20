var __defProp = Object.defineProperty;
var __defProps = Object.defineProperties;
var __getOwnPropDescs = Object.getOwnPropertyDescriptors;
var __getOwnPropSymbols = Object.getOwnPropertySymbols;
var __hasOwnProp = Object.prototype.hasOwnProperty;
var __propIsEnum = Object.prototype.propertyIsEnumerable;
var __defNormalProp = (obj, key, value) => key in obj ? __defProp(obj, key, { enumerable: true, configurable: true, writable: true, value }) : obj[key] = value;
var __spreadValues = (a, b) => {
  for (var prop in b || (b = {}))
    if (__hasOwnProp.call(b, prop))
      __defNormalProp(a, prop, b[prop]);
  if (__getOwnPropSymbols)
    for (var prop of __getOwnPropSymbols(b)) {
      if (__propIsEnum.call(b, prop))
        __defNormalProp(a, prop, b[prop]);
    }
  return a;
};
var __spreadProps = (a, b) => __defProps(a, __getOwnPropDescs(b));
(function(vue) {
  "use strict";
  var _export_sfc = (sfc, props) => {
    const target = sfc.__vccOpts || sfc;
    for (const [key, val] of props) {
      target[key] = val;
    }
    return target;
  };
  const _sfc_main$a = {
    name: "CustomFontModalContent",
    props: {
      fontConfig: {
        type: Object,
        required: false,
        default: () => {
          return {
            font_family: "",
            weight: "400",
            woff: "",
            woff2: "",
            ttf: "",
            svg: "",
            eot: ""
          };
        }
      }
    },
    setup(props, { emit }) {
      const localFontConfig = vue.ref(props.fontConfig);
      const fontWeightOptions = [
        {
          name: "100",
          id: "100"
        },
        {
          name: "200",
          id: "200"
        },
        {
          name: "300",
          id: "300"
        },
        {
          name: "400",
          id: "400"
        },
        {
          name: "500",
          id: "500"
        },
        {
          name: "600",
          id: "600"
        },
        {
          name: "700",
          id: "700"
        },
        {
          name: "800",
          id: "800"
        },
        {
          name: "900",
          id: "900"
        }
      ];
      function saveFont() {
        emit("save-font", localFontConfig.value);
      }
      function updateValue(type, url) {
        localFontConfig.value = __spreadProps(__spreadValues({}, localFontConfig.value), {
          [type]: url
        });
      }
      return {
        fontWeightOptions,
        saveFont,
        localFontConfig,
        updateValue
      };
    }
  };
  function _sfc_render$a(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_BaseInput = vue.resolveComponent("BaseInput");
    const _component_ModalTwoColTemplate = vue.resolveComponent("ModalTwoColTemplate");
    const _component_InputSelect = vue.resolveComponent("InputSelect");
    const _component_InputFile = vue.resolveComponent("InputFile");
    const _component_ModalTemplateSaveButton = vue.resolveComponent("ModalTemplateSaveButton");
    return vue.openBlock(), vue.createBlock(_component_ModalTemplateSaveButton, { onSaveModal: $setup.saveFont }, {
      default: vue.withCtx(() => [
        vue.createVNode(_component_ModalTwoColTemplate, {
          title: _ctx.$translate("font_name"),
          desc: _ctx.$translate("custom_font_name")
        }, {
          default: vue.withCtx(() => [
            vue.createVNode(_component_BaseInput, {
              "model-value": $setup.localFontConfig.font_family,
              "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => ($setup.localFontConfig.font_family = $event, _ctx.$emit("set-title", $event)))
            }, null, 8, ["model-value"])
          ]),
          _: 1
        }, 8, ["title", "desc"]),
        vue.createVNode(_component_ModalTwoColTemplate, {
          title: _ctx.$translate("font_weight"),
          desc: _ctx.$translate("custom_font_weight")
        }, {
          default: vue.withCtx(() => [
            vue.createVNode(_component_InputSelect, {
              "model-value": $setup.localFontConfig.weight || "",
              options: $setup.fontWeightOptions,
              "onUpdate:modelValue": _cache[1] || (_cache[1] = ($event) => $setup.updateValue("weight", $event))
            }, null, 8, ["model-value", "options"])
          ]),
          _: 1
        }, 8, ["title", "desc"]),
        vue.createVNode(_component_ModalTwoColTemplate, {
          title: _ctx.$translate("woff_file"),
          desc: _ctx.$translate("custom_font_woff")
        }, {
          default: vue.withCtx(() => [
            vue.createVNode(_component_InputFile, {
              "model-value": $setup.localFontConfig.woff,
              type: ".woff",
              "onUpdate:modelValue": _cache[2] || (_cache[2] = ($event) => $setup.updateValue("woff", $event))
            }, null, 8, ["model-value"])
          ]),
          _: 1
        }, 8, ["title", "desc"]),
        vue.createVNode(_component_ModalTwoColTemplate, {
          title: _ctx.$translate("woff2_file"),
          desc: _ctx.$translate("custom_font_woff2")
        }, {
          default: vue.withCtx(() => [
            vue.createVNode(_component_InputFile, {
              "model-value": $setup.localFontConfig.woff2,
              type: ".woff2",
              "onUpdate:modelValue": _cache[3] || (_cache[3] = ($event) => $setup.updateValue("woff2", $event))
            }, null, 8, ["model-value"])
          ]),
          _: 1
        }, 8, ["title", "desc"]),
        vue.createVNode(_component_ModalTwoColTemplate, {
          title: _ctx.$translate("ttf_file"),
          desc: _ctx.$translate("custom_font_ttf")
        }, {
          default: vue.withCtx(() => [
            vue.createVNode(_component_InputFile, {
              "model-value": $setup.localFontConfig.ttf,
              type: ".ttf",
              "onUpdate:modelValue": _cache[4] || (_cache[4] = ($event) => $setup.localFontConfig.ttf = $event)
            }, null, 8, ["model-value"])
          ]),
          _: 1
        }, 8, ["title", "desc"]),
        vue.createVNode(_component_ModalTwoColTemplate, {
          title: _ctx.$translate("svg_file"),
          desc: _ctx.$translate("custom_font_svg")
        }, {
          default: vue.withCtx(() => [
            vue.createVNode(_component_InputFile, {
              "model-value": $setup.localFontConfig.svg,
              type: ".svg",
              "onUpdate:modelValue": _cache[5] || (_cache[5] = ($event) => $setup.localFontConfig.svg = $event)
            }, null, 8, ["model-value"])
          ]),
          _: 1
        }, 8, ["title", "desc"]),
        vue.createVNode(_component_ModalTwoColTemplate, {
          title: _ctx.$translate("eot_file"),
          desc: _ctx.$translate("custom_font_eot")
        }, {
          default: vue.withCtx(() => [
            vue.createVNode(_component_InputFile, {
              "model-value": $setup.localFontConfig.eot,
              type: ".eot",
              "onUpdate:modelValue": _cache[6] || (_cache[6] = ($event) => $setup.localFontConfig.eot = $event)
            }, null, 8, ["model-value"])
          ]),
          _: 1
        }, 8, ["title", "desc"])
      ]),
      _: 1
    }, 8, ["onSaveModal"]);
  }
  var CustomFontModalContent = /* @__PURE__ */ _export_sfc(_sfc_main$a, [["render", _sfc_render$a]]);
  var CustomFont_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$9 = {
    name: "CustomFont",
    props: {
      font: {
        type: Object,
        required: true
      }
    },
    data() {
      return {
        showModal: false
      };
    },
    components: {
      CustomFontModalContent
    }
  };
  const _hoisted_1$8 = { class: "znpb-admin__google-font-tab" };
  const _hoisted_2$7 = { class: "znpb-admin__google-font-tab-title" };
  const _hoisted_3$6 = { class: "znpb-admin__google-font-tab-variants" };
  const _hoisted_4$4 = { class: "znpb-admin__google-font-tab-actions" };
  function _sfc_render$9(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_Icon = vue.resolveComponent("Icon");
    const _component_Tooltip = vue.resolveComponent("Tooltip");
    const _component_CustomFontModalContent = vue.resolveComponent("CustomFontModalContent");
    const _component_modal = vue.resolveComponent("modal");
    return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$8, [
      vue.createElementVNode("div", _hoisted_2$7, vue.toDisplayString($props.font.font_family), 1),
      vue.createElementVNode("div", _hoisted_3$6, vue.toDisplayString($props.font.weight), 1),
      vue.createElementVNode("div", _hoisted_4$4, [
        vue.createVNode(_component_Tooltip, {
          class: "znpb-actions-popup-icons",
          content: _ctx.$translate("click_to_edit_font"),
          "append-to": "body",
          modifiers: [{ name: "offset", options: { offset: [0, 15] } }],
          positionFixed: true
        }, {
          default: vue.withCtx(() => [
            vue.createVNode(_component_Icon, {
              icon: "edit",
              onClick: _cache[0] || (_cache[0] = ($event) => $data.showModal = true)
            })
          ]),
          _: 1
        }, 8, ["content"]),
        vue.createVNode(_component_Tooltip, {
          class: "znpb-actions-popup-icons",
          content: _ctx.$translate("click_to_delete_font"),
          "append-to": "body",
          modifiers: [{ name: "offset", options: { offset: [0, 15] } }],
          positionFixed: true
        }, {
          default: vue.withCtx(() => [
            vue.createVNode(_component_Icon, {
              icon: "delete",
              onClick: _cache[1] || (_cache[1] = ($event) => _ctx.$emit("delete", $props.font))
            })
          ]),
          _: 1
        }, 8, ["content"])
      ]),
      vue.createVNode(_component_modal, {
        show: $data.showModal,
        "onUpdate:show": _cache[3] || (_cache[3] = ($event) => $data.showModal = $event),
        width: 570,
        title: $props.font.font_family,
        "append-to": "#znpb-admin",
        "show-maximize": false
      }, {
        default: vue.withCtx(() => [
          vue.createVNode(_component_CustomFontModalContent, {
            fontConfig: $props.font,
            onSaveFont: _cache[2] || (_cache[2] = ($event) => (_ctx.$emit("font-updated", $event), $data.showModal = false))
          }, null, 8, ["fontConfig"])
        ]),
        _: 1
      }, 8, ["show", "title"])
    ]);
  }
  var CustomFont = /* @__PURE__ */ _export_sfc(_sfc_main$9, [["render", _sfc_render$9]]);
  const _sfc_main$8 = {
    name: "CustomFonts",
    components: {
      CustomFont,
      CustomFontModalContent
    },
    setup(props) {
      const builderOptionsStore = window.zb.components.useBuilderOptionsStore();
      const showModal = vue.ref(false);
      let customFonts = vue.computed(() => {
        return builderOptionsStore.getOptionValue("custom_fonts");
      });
      function onFontDelete(font) {
        builderOptionsStore.deleteCustomFont(font.font_family);
      }
      function onFontUpdated({ font, value: newValue }) {
        builderOptionsStore.updateCustomFont(font.font_family, newValue);
      }
      function onCustomFontAdded(font) {
        builderOptionsStore.addCustomFont(__spreadValues({
          font_family: font.family,
          font_variants: ["regular"],
          font_subset: ["latin"]
        }, font));
        showModal.value = false;
      }
      function onFontRemoved(font) {
        builderOptionsStore.deleteCustomFont(font.font_family);
        showModal.value = false;
      }
      return {
        customFonts,
        onCustomFontAdded,
        onFontRemoved,
        onFontUpdated,
        onFontDelete,
        showModal
      };
    }
  };
  const _hoisted_1$7 = {
    key: 0,
    class: "znpb-admin__google-font-tab znpb-admin__google-font-tab--titles"
  };
  const _hoisted_2$6 = { class: "znpb-admin__google-font-tab-title" };
  const _hoisted_3$5 = { class: "znpb-admin__google-font-tab-variants" };
  const _hoisted_4$3 = { class: "znpb-admin__google-font-tab-actions" };
  const _hoisted_5$3 = { key: 2 };
  const _hoisted_6$3 = { class: "znpb-admin-google-fonts-actions" };
  const _hoisted_7$3 = { class: "znpb-admin-info-p" };
  function _sfc_render$8(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_EmptyList = vue.resolveComponent("EmptyList");
    const _component_Tooltip = vue.resolveComponent("Tooltip");
    const _component_CustomFont = vue.resolveComponent("CustomFont");
    const _component_ListAnimation = vue.resolveComponent("ListAnimation");
    const _component_Icon = vue.resolveComponent("Icon");
    const _component_Button = vue.resolveComponent("Button");
    const _component_CustomFontModalContent = vue.resolveComponent("CustomFontModalContent");
    const _component_modal = vue.resolveComponent("modal");
    const _component_PageTemplate = vue.resolveComponent("PageTemplate");
    return vue.openBlock(), vue.createBlock(_component_PageTemplate, null, {
      right: vue.withCtx(() => [
        vue.createElementVNode("p", _hoisted_7$3, vue.toDisplayString(_ctx.$translate("upload_custom_fonts")), 1)
      ]),
      default: vue.withCtx(() => [
        vue.createElementVNode("h3", null, vue.toDisplayString(_ctx.$translate("custom_fonts")), 1),
        $setup.customFonts.length > 0 ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_1$7, [
          vue.createElementVNode("div", _hoisted_2$6, vue.toDisplayString(_ctx.$translate("font_name")), 1),
          vue.createElementVNode("div", _hoisted_3$5, vue.toDisplayString(_ctx.$translate("variants")), 1),
          vue.createElementVNode("div", _hoisted_4$3, vue.toDisplayString(_ctx.$translate("actions")), 1)
        ])) : vue.createCommentVNode("", true),
        $setup.customFonts.length === 0 ? (vue.openBlock(), vue.createBlock(_component_Tooltip, {
          key: 1,
          content: _ctx.$translate("click_me_to_add_font")
        }, {
          default: vue.withCtx(() => [
            vue.createVNode(_component_EmptyList, {
              onClick: _cache[0] || (_cache[0] = ($event) => $setup.showModal = true)
            }, {
              default: vue.withCtx(() => [
                vue.createTextVNode(vue.toDisplayString(_ctx.$translate("no_custom_fonts")), 1)
              ]),
              _: 1
            })
          ]),
          _: 1
        }, 8, ["content"])) : vue.createCommentVNode("", true),
        $setup.customFonts.length > 0 ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_5$3, [
          vue.createVNode(_component_ListAnimation, { tag: "div" }, {
            default: vue.withCtx(() => [
              (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList($setup.customFonts, (font, i) => {
                return vue.openBlock(), vue.createBlock(_component_CustomFont, {
                  key: i,
                  class: "znpb-admin-tab",
                  font,
                  onDelete: $setup.onFontDelete,
                  onFontUpdated: ($event) => $setup.onFontUpdated({
                    font,
                    value: $event
                  })
                }, null, 8, ["font", "onDelete", "onFontUpdated"]);
              }), 128))
            ]),
            _: 1
          })
        ])) : vue.createCommentVNode("", true),
        vue.createElementVNode("div", _hoisted_6$3, [
          vue.createVNode(_component_Button, {
            type: "secondary",
            onClick: _cache[1] || (_cache[1] = ($event) => $setup.showModal = true)
          }, {
            default: vue.withCtx(() => [
              vue.createVNode(_component_Icon, { icon: "plus" }),
              vue.createTextVNode(" " + vue.toDisplayString(_ctx.$translate("add_font")), 1)
            ]),
            _: 1
          })
        ]),
        vue.createVNode(_component_modal, {
          show: $setup.showModal,
          "onUpdate:show": _cache[3] || (_cache[3] = ($event) => $setup.showModal = $event),
          width: 570,
          title: _ctx.$translate("custom_fonts"),
          "append-to": "#znpb-admin",
          "show-maximize": false
        }, {
          default: vue.withCtx(() => [
            vue.createVNode(_component_CustomFontModalContent, {
              onSaveFont: _cache[2] || (_cache[2] = ($event) => $setup.onCustomFontAdded($event))
            })
          ]),
          _: 1
        }, 8, ["show", "title"])
      ]),
      _: 1
    });
  }
  var CustomFonts = /* @__PURE__ */ _export_sfc(_sfc_main$8, [["render", _sfc_render$8]]);
  var AdobeFontsTab_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$7 = {
    name: "TypekitTab",
    props: {
      font: {
        type: Object,
        required: true
      }
    },
    setup(props) {
      const builderOptionsStore = window.zb.components.useBuilderOptionsStore();
      let isActive = vue.computed({
        get: () => {
          return builderOptionsStore.getOptionValue("typekit_fonts").includes(props.font.id);
        },
        set: (val) => {
          if (val) {
            builderOptionsStore.addFontProject(props.font.id);
          } else {
            builderOptionsStore.removeFontProject(props.font.id);
          }
        }
      });
      return {
        isActive,
        font: props.font
      };
    },
    data() {
      return {};
    }
  };
  const _hoisted_1$6 = { class: "znpb-admin__typekit-font-tab-title" };
  function _sfc_render$7(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_InputCheckbox = vue.resolveComponent("InputCheckbox");
    const _component_Tooltip = vue.resolveComponent("Tooltip");
    return vue.openBlock(), vue.createElementBlock("div", {
      class: vue.normalizeClass(["znpb-admin__typekit-font-tab", { "znpb-admin__typekit-font-tab--active": $setup.isActive }])
    }, [
      vue.createElementVNode("span", _hoisted_1$6, vue.toDisplayString($setup.font.name), 1),
      vue.createVNode(_component_Tooltip, {
        content: _ctx.$translate("active_typekit_deactivate")
      }, {
        default: vue.withCtx(() => [
          vue.createVNode(_component_InputCheckbox, {
            modelValue: $setup.isActive,
            "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => $setup.isActive = $event),
            rounded: true
          }, null, 8, ["modelValue"])
        ]),
        _: 1
      }, 8, ["content"])
    ], 2);
  }
  var AdobeFontsTab = /* @__PURE__ */ _export_sfc(_sfc_main$7, [["render", _sfc_render$7]]);
  var commonjsGlobal = typeof globalThis !== "undefined" ? globalThis : typeof window !== "undefined" ? window : typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : {};
  function commonjsRequire(path) {
    throw new Error('Could not dynamically require "' + path + '". Please configure the dynamicRequireTargets or/and ignoreDynamicRequires option of @rollup/plugin-commonjs appropriately for this require call to work.');
  }
  var axios$2 = { exports: {} };
  var bind$2 = function bind2(fn, thisArg) {
    return function wrap() {
      var args = new Array(arguments.length);
      for (var i = 0; i < args.length; i++) {
        args[i] = arguments[i];
      }
      return fn.apply(thisArg, args);
    };
  };
  var bind$1 = bind$2;
  var toString = Object.prototype.toString;
  var kindOf = function(cache) {
    return function(thing) {
      var str = toString.call(thing);
      return cache[str] || (cache[str] = str.slice(8, -1).toLowerCase());
    };
  }(/* @__PURE__ */ Object.create(null));
  function kindOfTest(type) {
    type = type.toLowerCase();
    return function isKindOf(thing) {
      return kindOf(thing) === type;
    };
  }
  function isArray(val) {
    return Array.isArray(val);
  }
  function isUndefined(val) {
    return typeof val === "undefined";
  }
  function isBuffer(val) {
    return val !== null && !isUndefined(val) && val.constructor !== null && !isUndefined(val.constructor) && typeof val.constructor.isBuffer === "function" && val.constructor.isBuffer(val);
  }
  var isArrayBuffer = kindOfTest("ArrayBuffer");
  function isArrayBufferView(val) {
    var result;
    if (typeof ArrayBuffer !== "undefined" && ArrayBuffer.isView) {
      result = ArrayBuffer.isView(val);
    } else {
      result = val && val.buffer && isArrayBuffer(val.buffer);
    }
    return result;
  }
  function isString(val) {
    return typeof val === "string";
  }
  function isNumber(val) {
    return typeof val === "number";
  }
  function isObject$1(val) {
    return val !== null && typeof val === "object";
  }
  function isPlainObject(val) {
    if (kindOf(val) !== "object") {
      return false;
    }
    var prototype2 = Object.getPrototypeOf(val);
    return prototype2 === null || prototype2 === Object.prototype;
  }
  var isDate = kindOfTest("Date");
  var isFile = kindOfTest("File");
  var isBlob = kindOfTest("Blob");
  var isFileList = kindOfTest("FileList");
  function isFunction(val) {
    return toString.call(val) === "[object Function]";
  }
  function isStream(val) {
    return isObject$1(val) && isFunction(val.pipe);
  }
  function isFormData(thing) {
    var pattern = "[object FormData]";
    return thing && (typeof FormData === "function" && thing instanceof FormData || toString.call(thing) === pattern || isFunction(thing.toString) && thing.toString() === pattern);
  }
  var isURLSearchParams = kindOfTest("URLSearchParams");
  function trim(str) {
    return str.trim ? str.trim() : str.replace(/^\s+|\s+$/g, "");
  }
  function isStandardBrowserEnv() {
    if (typeof navigator !== "undefined" && (navigator.product === "ReactNative" || navigator.product === "NativeScript" || navigator.product === "NS")) {
      return false;
    }
    return typeof window !== "undefined" && typeof document !== "undefined";
  }
  function forEach(obj, fn) {
    if (obj === null || typeof obj === "undefined") {
      return;
    }
    if (typeof obj !== "object") {
      obj = [obj];
    }
    if (isArray(obj)) {
      for (var i = 0, l = obj.length; i < l; i++) {
        fn.call(null, obj[i], i, obj);
      }
    } else {
      for (var key in obj) {
        if (Object.prototype.hasOwnProperty.call(obj, key)) {
          fn.call(null, obj[key], key, obj);
        }
      }
    }
  }
  function merge() {
    var result = {};
    function assignValue(val, key) {
      if (isPlainObject(result[key]) && isPlainObject(val)) {
        result[key] = merge(result[key], val);
      } else if (isPlainObject(val)) {
        result[key] = merge({}, val);
      } else if (isArray(val)) {
        result[key] = val.slice();
      } else {
        result[key] = val;
      }
    }
    for (var i = 0, l = arguments.length; i < l; i++) {
      forEach(arguments[i], assignValue);
    }
    return result;
  }
  function extend(a, b, thisArg) {
    forEach(b, function assignValue(val, key) {
      if (thisArg && typeof val === "function") {
        a[key] = bind$1(val, thisArg);
      } else {
        a[key] = val;
      }
    });
    return a;
  }
  function stripBOM(content) {
    if (content.charCodeAt(0) === 65279) {
      content = content.slice(1);
    }
    return content;
  }
  function inherits(constructor, superConstructor, props, descriptors2) {
    constructor.prototype = Object.create(superConstructor.prototype, descriptors2);
    constructor.prototype.constructor = constructor;
    props && Object.assign(constructor.prototype, props);
  }
  function toFlatObject(sourceObj, destObj, filter) {
    var props;
    var i;
    var prop;
    var merged = {};
    destObj = destObj || {};
    do {
      props = Object.getOwnPropertyNames(sourceObj);
      i = props.length;
      while (i-- > 0) {
        prop = props[i];
        if (!merged[prop]) {
          destObj[prop] = sourceObj[prop];
          merged[prop] = true;
        }
      }
      sourceObj = Object.getPrototypeOf(sourceObj);
    } while (sourceObj && (!filter || filter(sourceObj, destObj)) && sourceObj !== Object.prototype);
    return destObj;
  }
  function endsWith(str, searchString, position) {
    str = String(str);
    if (position === void 0 || position > str.length) {
      position = str.length;
    }
    position -= searchString.length;
    var lastIndex = str.indexOf(searchString, position);
    return lastIndex !== -1 && lastIndex === position;
  }
  function toArray(thing) {
    if (!thing)
      return null;
    var i = thing.length;
    if (isUndefined(i))
      return null;
    var arr = new Array(i);
    while (i-- > 0) {
      arr[i] = thing[i];
    }
    return arr;
  }
  var isTypedArray = function(TypedArray) {
    return function(thing) {
      return TypedArray && thing instanceof TypedArray;
    };
  }(typeof Uint8Array !== "undefined" && Object.getPrototypeOf(Uint8Array));
  var utils$h = {
    isArray,
    isArrayBuffer,
    isBuffer,
    isFormData,
    isArrayBufferView,
    isString,
    isNumber,
    isObject: isObject$1,
    isPlainObject,
    isUndefined,
    isDate,
    isFile,
    isBlob,
    isFunction,
    isStream,
    isURLSearchParams,
    isStandardBrowserEnv,
    forEach,
    merge,
    extend,
    trim,
    stripBOM,
    inherits,
    toFlatObject,
    kindOf,
    kindOfTest,
    endsWith,
    toArray,
    isTypedArray,
    isFileList
  };
  var utils$g = utils$h;
  function encode(val) {
    return encodeURIComponent(val).replace(/%3A/gi, ":").replace(/%24/g, "$").replace(/%2C/gi, ",").replace(/%20/g, "+").replace(/%5B/gi, "[").replace(/%5D/gi, "]");
  }
  var buildURL$2 = function buildURL2(url, params, paramsSerializer) {
    if (!params) {
      return url;
    }
    var serializedParams;
    if (paramsSerializer) {
      serializedParams = paramsSerializer(params);
    } else if (utils$g.isURLSearchParams(params)) {
      serializedParams = params.toString();
    } else {
      var parts = [];
      utils$g.forEach(params, function serialize(val, key) {
        if (val === null || typeof val === "undefined") {
          return;
        }
        if (utils$g.isArray(val)) {
          key = key + "[]";
        } else {
          val = [val];
        }
        utils$g.forEach(val, function parseValue(v) {
          if (utils$g.isDate(v)) {
            v = v.toISOString();
          } else if (utils$g.isObject(v)) {
            v = JSON.stringify(v);
          }
          parts.push(encode(key) + "=" + encode(v));
        });
      });
      serializedParams = parts.join("&");
    }
    if (serializedParams) {
      var hashmarkIndex = url.indexOf("#");
      if (hashmarkIndex !== -1) {
        url = url.slice(0, hashmarkIndex);
      }
      url += (url.indexOf("?") === -1 ? "?" : "&") + serializedParams;
    }
    return url;
  };
  var utils$f = utils$h;
  function InterceptorManager$1() {
    this.handlers = [];
  }
  InterceptorManager$1.prototype.use = function use(fulfilled, rejected, options) {
    this.handlers.push({
      fulfilled,
      rejected,
      synchronous: options ? options.synchronous : false,
      runWhen: options ? options.runWhen : null
    });
    return this.handlers.length - 1;
  };
  InterceptorManager$1.prototype.eject = function eject(id) {
    if (this.handlers[id]) {
      this.handlers[id] = null;
    }
  };
  InterceptorManager$1.prototype.forEach = function forEach2(fn) {
    utils$f.forEach(this.handlers, function forEachHandler(h) {
      if (h !== null) {
        fn(h);
      }
    });
  };
  var InterceptorManager_1 = InterceptorManager$1;
  var utils$e = utils$h;
  var normalizeHeaderName$1 = function normalizeHeaderName2(headers, normalizedName) {
    utils$e.forEach(headers, function processHeader(value, name) {
      if (name !== normalizedName && name.toUpperCase() === normalizedName.toUpperCase()) {
        headers[normalizedName] = value;
        delete headers[name];
      }
    });
  };
  var utils$d = utils$h;
  function AxiosError$5(message, code, config, request, response) {
    Error.call(this);
    this.message = message;
    this.name = "AxiosError";
    code && (this.code = code);
    config && (this.config = config);
    request && (this.request = request);
    response && (this.response = response);
  }
  utils$d.inherits(AxiosError$5, Error, {
    toJSON: function toJSON() {
      return {
        message: this.message,
        name: this.name,
        description: this.description,
        number: this.number,
        fileName: this.fileName,
        lineNumber: this.lineNumber,
        columnNumber: this.columnNumber,
        stack: this.stack,
        config: this.config,
        code: this.code,
        status: this.response && this.response.status ? this.response.status : null
      };
    }
  });
  var prototype = AxiosError$5.prototype;
  var descriptors = {};
  [
    "ERR_BAD_OPTION_VALUE",
    "ERR_BAD_OPTION",
    "ECONNABORTED",
    "ETIMEDOUT",
    "ERR_NETWORK",
    "ERR_FR_TOO_MANY_REDIRECTS",
    "ERR_DEPRECATED",
    "ERR_BAD_RESPONSE",
    "ERR_BAD_REQUEST",
    "ERR_CANCELED"
  ].forEach(function(code) {
    descriptors[code] = { value: code };
  });
  Object.defineProperties(AxiosError$5, descriptors);
  Object.defineProperty(prototype, "isAxiosError", { value: true });
  AxiosError$5.from = function(error, code, config, request, response, customProps) {
    var axiosError = Object.create(prototype);
    utils$d.toFlatObject(error, axiosError, function filter(obj) {
      return obj !== Error.prototype;
    });
    AxiosError$5.call(axiosError, error.message, code, config, request, response);
    axiosError.name = error.name;
    customProps && Object.assign(axiosError, customProps);
    return axiosError;
  };
  var AxiosError_1 = AxiosError$5;
  var transitional = {
    silentJSONParsing: true,
    forcedJSONParsing: true,
    clarifyTimeoutError: false
  };
  var utils$c = utils$h;
  function toFormData$1(obj, formData) {
    formData = formData || new FormData();
    var stack = [];
    function convertValue(value) {
      if (value === null)
        return "";
      if (utils$c.isDate(value)) {
        return value.toISOString();
      }
      if (utils$c.isArrayBuffer(value) || utils$c.isTypedArray(value)) {
        return typeof Blob === "function" ? new Blob([value]) : Buffer.from(value);
      }
      return value;
    }
    function build(data2, parentKey) {
      if (utils$c.isPlainObject(data2) || utils$c.isArray(data2)) {
        if (stack.indexOf(data2) !== -1) {
          throw Error("Circular reference detected in " + parentKey);
        }
        stack.push(data2);
        utils$c.forEach(data2, function each(value, key) {
          if (utils$c.isUndefined(value))
            return;
          var fullKey = parentKey ? parentKey + "." + key : key;
          var arr;
          if (value && !parentKey && typeof value === "object") {
            if (utils$c.endsWith(key, "{}")) {
              value = JSON.stringify(value);
            } else if (utils$c.endsWith(key, "[]") && (arr = utils$c.toArray(value))) {
              arr.forEach(function(el) {
                !utils$c.isUndefined(el) && formData.append(fullKey, convertValue(el));
              });
              return;
            }
          }
          build(value, fullKey);
        });
        stack.pop();
      } else {
        formData.append(parentKey, convertValue(data2));
      }
    }
    build(obj);
    return formData;
  }
  var toFormData_1 = toFormData$1;
  var AxiosError$4 = AxiosError_1;
  var settle$1 = function settle2(resolve, reject, response) {
    var validateStatus = response.config.validateStatus;
    if (!response.status || !validateStatus || validateStatus(response.status)) {
      resolve(response);
    } else {
      reject(new AxiosError$4(
        "Request failed with status code " + response.status,
        [AxiosError$4.ERR_BAD_REQUEST, AxiosError$4.ERR_BAD_RESPONSE][Math.floor(response.status / 100) - 4],
        response.config,
        response.request,
        response
      ));
    }
  };
  var utils$b = utils$h;
  var cookies$1 = utils$b.isStandardBrowserEnv() ? function standardBrowserEnv() {
    return {
      write: function write(name, value, expires, path, domain, secure) {
        var cookie = [];
        cookie.push(name + "=" + encodeURIComponent(value));
        if (utils$b.isNumber(expires)) {
          cookie.push("expires=" + new Date(expires).toGMTString());
        }
        if (utils$b.isString(path)) {
          cookie.push("path=" + path);
        }
        if (utils$b.isString(domain)) {
          cookie.push("domain=" + domain);
        }
        if (secure === true) {
          cookie.push("secure");
        }
        document.cookie = cookie.join("; ");
      },
      read: function read(name) {
        var match = document.cookie.match(new RegExp("(^|;\\s*)(" + name + ")=([^;]*)"));
        return match ? decodeURIComponent(match[3]) : null;
      },
      remove: function remove(name) {
        this.write(name, "", Date.now() - 864e5);
      }
    };
  }() : function nonStandardBrowserEnv() {
    return {
      write: function write() {
      },
      read: function read() {
        return null;
      },
      remove: function remove() {
      }
    };
  }();
  var isAbsoluteURL$1 = function isAbsoluteURL2(url) {
    return /^([a-z][a-z\d+\-.]*:)?\/\//i.test(url);
  };
  var combineURLs$1 = function combineURLs2(baseURL, relativeURL) {
    return relativeURL ? baseURL.replace(/\/+$/, "") + "/" + relativeURL.replace(/^\/+/, "") : baseURL;
  };
  var isAbsoluteURL = isAbsoluteURL$1;
  var combineURLs = combineURLs$1;
  var buildFullPath$2 = function buildFullPath2(baseURL, requestedURL) {
    if (baseURL && !isAbsoluteURL(requestedURL)) {
      return combineURLs(baseURL, requestedURL);
    }
    return requestedURL;
  };
  var utils$a = utils$h;
  var ignoreDuplicateOf = [
    "age",
    "authorization",
    "content-length",
    "content-type",
    "etag",
    "expires",
    "from",
    "host",
    "if-modified-since",
    "if-unmodified-since",
    "last-modified",
    "location",
    "max-forwards",
    "proxy-authorization",
    "referer",
    "retry-after",
    "user-agent"
  ];
  var parseHeaders$1 = function parseHeaders2(headers) {
    var parsed = {};
    var key;
    var val;
    var i;
    if (!headers) {
      return parsed;
    }
    utils$a.forEach(headers.split("\n"), function parser(line) {
      i = line.indexOf(":");
      key = utils$a.trim(line.substr(0, i)).toLowerCase();
      val = utils$a.trim(line.substr(i + 1));
      if (key) {
        if (parsed[key] && ignoreDuplicateOf.indexOf(key) >= 0) {
          return;
        }
        if (key === "set-cookie") {
          parsed[key] = (parsed[key] ? parsed[key] : []).concat([val]);
        } else {
          parsed[key] = parsed[key] ? parsed[key] + ", " + val : val;
        }
      }
    });
    return parsed;
  };
  var utils$9 = utils$h;
  var isURLSameOrigin$1 = utils$9.isStandardBrowserEnv() ? function standardBrowserEnv() {
    var msie = /(msie|trident)/i.test(navigator.userAgent);
    var urlParsingNode = document.createElement("a");
    var originURL;
    function resolveURL(url) {
      var href = url;
      if (msie) {
        urlParsingNode.setAttribute("href", href);
        href = urlParsingNode.href;
      }
      urlParsingNode.setAttribute("href", href);
      return {
        href: urlParsingNode.href,
        protocol: urlParsingNode.protocol ? urlParsingNode.protocol.replace(/:$/, "") : "",
        host: urlParsingNode.host,
        search: urlParsingNode.search ? urlParsingNode.search.replace(/^\?/, "") : "",
        hash: urlParsingNode.hash ? urlParsingNode.hash.replace(/^#/, "") : "",
        hostname: urlParsingNode.hostname,
        port: urlParsingNode.port,
        pathname: urlParsingNode.pathname.charAt(0) === "/" ? urlParsingNode.pathname : "/" + urlParsingNode.pathname
      };
    }
    originURL = resolveURL(window.location.href);
    return function isURLSameOrigin2(requestURL) {
      var parsed = utils$9.isString(requestURL) ? resolveURL(requestURL) : requestURL;
      return parsed.protocol === originURL.protocol && parsed.host === originURL.host;
    };
  }() : function nonStandardBrowserEnv() {
    return function isURLSameOrigin2() {
      return true;
    };
  }();
  var AxiosError$3 = AxiosError_1;
  var utils$8 = utils$h;
  function CanceledError$3(message) {
    AxiosError$3.call(this, message == null ? "canceled" : message, AxiosError$3.ERR_CANCELED);
    this.name = "CanceledError";
  }
  utils$8.inherits(CanceledError$3, AxiosError$3, {
    __CANCEL__: true
  });
  var CanceledError_1 = CanceledError$3;
  var parseProtocol$1 = function parseProtocol2(url) {
    var match = /^([-+\w]{1,25})(:?\/\/|:)/.exec(url);
    return match && match[1] || "";
  };
  var utils$7 = utils$h;
  var settle = settle$1;
  var cookies = cookies$1;
  var buildURL$1 = buildURL$2;
  var buildFullPath$1 = buildFullPath$2;
  var parseHeaders = parseHeaders$1;
  var isURLSameOrigin = isURLSameOrigin$1;
  var transitionalDefaults$1 = transitional;
  var AxiosError$2 = AxiosError_1;
  var CanceledError$2 = CanceledError_1;
  var parseProtocol = parseProtocol$1;
  var xhr = function xhrAdapter(config) {
    return new Promise(function dispatchXhrRequest(resolve, reject) {
      var requestData = config.data;
      var requestHeaders = config.headers;
      var responseType = config.responseType;
      var onCanceled;
      function done() {
        if (config.cancelToken) {
          config.cancelToken.unsubscribe(onCanceled);
        }
        if (config.signal) {
          config.signal.removeEventListener("abort", onCanceled);
        }
      }
      if (utils$7.isFormData(requestData) && utils$7.isStandardBrowserEnv()) {
        delete requestHeaders["Content-Type"];
      }
      var request = new XMLHttpRequest();
      if (config.auth) {
        var username = config.auth.username || "";
        var password = config.auth.password ? unescape(encodeURIComponent(config.auth.password)) : "";
        requestHeaders.Authorization = "Basic " + btoa(username + ":" + password);
      }
      var fullPath = buildFullPath$1(config.baseURL, config.url);
      request.open(config.method.toUpperCase(), buildURL$1(fullPath, config.params, config.paramsSerializer), true);
      request.timeout = config.timeout;
      function onloadend() {
        if (!request) {
          return;
        }
        var responseHeaders = "getAllResponseHeaders" in request ? parseHeaders(request.getAllResponseHeaders()) : null;
        var responseData = !responseType || responseType === "text" || responseType === "json" ? request.responseText : request.response;
        var response = {
          data: responseData,
          status: request.status,
          statusText: request.statusText,
          headers: responseHeaders,
          config,
          request
        };
        settle(function _resolve(value) {
          resolve(value);
          done();
        }, function _reject(err) {
          reject(err);
          done();
        }, response);
        request = null;
      }
      if ("onloadend" in request) {
        request.onloadend = onloadend;
      } else {
        request.onreadystatechange = function handleLoad() {
          if (!request || request.readyState !== 4) {
            return;
          }
          if (request.status === 0 && !(request.responseURL && request.responseURL.indexOf("file:") === 0)) {
            return;
          }
          setTimeout(onloadend);
        };
      }
      request.onabort = function handleAbort() {
        if (!request) {
          return;
        }
        reject(new AxiosError$2("Request aborted", AxiosError$2.ECONNABORTED, config, request));
        request = null;
      };
      request.onerror = function handleError() {
        reject(new AxiosError$2("Network Error", AxiosError$2.ERR_NETWORK, config, request, request));
        request = null;
      };
      request.ontimeout = function handleTimeout() {
        var timeoutErrorMessage = config.timeout ? "timeout of " + config.timeout + "ms exceeded" : "timeout exceeded";
        var transitional2 = config.transitional || transitionalDefaults$1;
        if (config.timeoutErrorMessage) {
          timeoutErrorMessage = config.timeoutErrorMessage;
        }
        reject(new AxiosError$2(
          timeoutErrorMessage,
          transitional2.clarifyTimeoutError ? AxiosError$2.ETIMEDOUT : AxiosError$2.ECONNABORTED,
          config,
          request
        ));
        request = null;
      };
      if (utils$7.isStandardBrowserEnv()) {
        var xsrfValue = (config.withCredentials || isURLSameOrigin(fullPath)) && config.xsrfCookieName ? cookies.read(config.xsrfCookieName) : void 0;
        if (xsrfValue) {
          requestHeaders[config.xsrfHeaderName] = xsrfValue;
        }
      }
      if ("setRequestHeader" in request) {
        utils$7.forEach(requestHeaders, function setRequestHeader(val, key) {
          if (typeof requestData === "undefined" && key.toLowerCase() === "content-type") {
            delete requestHeaders[key];
          } else {
            request.setRequestHeader(key, val);
          }
        });
      }
      if (!utils$7.isUndefined(config.withCredentials)) {
        request.withCredentials = !!config.withCredentials;
      }
      if (responseType && responseType !== "json") {
        request.responseType = config.responseType;
      }
      if (typeof config.onDownloadProgress === "function") {
        request.addEventListener("progress", config.onDownloadProgress);
      }
      if (typeof config.onUploadProgress === "function" && request.upload) {
        request.upload.addEventListener("progress", config.onUploadProgress);
      }
      if (config.cancelToken || config.signal) {
        onCanceled = function(cancel) {
          if (!request) {
            return;
          }
          reject(!cancel || cancel && cancel.type ? new CanceledError$2() : cancel);
          request.abort();
          request = null;
        };
        config.cancelToken && config.cancelToken.subscribe(onCanceled);
        if (config.signal) {
          config.signal.aborted ? onCanceled() : config.signal.addEventListener("abort", onCanceled);
        }
      }
      if (!requestData) {
        requestData = null;
      }
      var protocol = parseProtocol(fullPath);
      if (protocol && ["http", "https", "file"].indexOf(protocol) === -1) {
        reject(new AxiosError$2("Unsupported protocol " + protocol + ":", AxiosError$2.ERR_BAD_REQUEST, config));
        return;
      }
      request.send(requestData);
    });
  };
  var _null = null;
  var utils$6 = utils$h;
  var normalizeHeaderName = normalizeHeaderName$1;
  var AxiosError$1 = AxiosError_1;
  var transitionalDefaults = transitional;
  var toFormData = toFormData_1;
  var DEFAULT_CONTENT_TYPE = {
    "Content-Type": "application/x-www-form-urlencoded"
  };
  function setContentTypeIfUnset(headers, value) {
    if (!utils$6.isUndefined(headers) && utils$6.isUndefined(headers["Content-Type"])) {
      headers["Content-Type"] = value;
    }
  }
  function getDefaultAdapter() {
    var adapter;
    if (typeof XMLHttpRequest !== "undefined") {
      adapter = xhr;
    } else if (typeof process !== "undefined" && Object.prototype.toString.call(process) === "[object process]") {
      adapter = xhr;
    }
    return adapter;
  }
  function stringifySafely(rawValue, parser, encoder) {
    if (utils$6.isString(rawValue)) {
      try {
        (parser || JSON.parse)(rawValue);
        return utils$6.trim(rawValue);
      } catch (e) {
        if (e.name !== "SyntaxError") {
          throw e;
        }
      }
    }
    return (encoder || JSON.stringify)(rawValue);
  }
  var defaults$3 = {
    transitional: transitionalDefaults,
    adapter: getDefaultAdapter(),
    transformRequest: [function transformRequest(data2, headers) {
      normalizeHeaderName(headers, "Accept");
      normalizeHeaderName(headers, "Content-Type");
      if (utils$6.isFormData(data2) || utils$6.isArrayBuffer(data2) || utils$6.isBuffer(data2) || utils$6.isStream(data2) || utils$6.isFile(data2) || utils$6.isBlob(data2)) {
        return data2;
      }
      if (utils$6.isArrayBufferView(data2)) {
        return data2.buffer;
      }
      if (utils$6.isURLSearchParams(data2)) {
        setContentTypeIfUnset(headers, "application/x-www-form-urlencoded;charset=utf-8");
        return data2.toString();
      }
      var isObjectPayload = utils$6.isObject(data2);
      var contentType = headers && headers["Content-Type"];
      var isFileList2;
      if ((isFileList2 = utils$6.isFileList(data2)) || isObjectPayload && contentType === "multipart/form-data") {
        var _FormData = this.env && this.env.FormData;
        return toFormData(isFileList2 ? { "files[]": data2 } : data2, _FormData && new _FormData());
      } else if (isObjectPayload || contentType === "application/json") {
        setContentTypeIfUnset(headers, "application/json");
        return stringifySafely(data2);
      }
      return data2;
    }],
    transformResponse: [function transformResponse(data2) {
      var transitional2 = this.transitional || defaults$3.transitional;
      var silentJSONParsing = transitional2 && transitional2.silentJSONParsing;
      var forcedJSONParsing = transitional2 && transitional2.forcedJSONParsing;
      var strictJSONParsing = !silentJSONParsing && this.responseType === "json";
      if (strictJSONParsing || forcedJSONParsing && utils$6.isString(data2) && data2.length) {
        try {
          return JSON.parse(data2);
        } catch (e) {
          if (strictJSONParsing) {
            if (e.name === "SyntaxError") {
              throw AxiosError$1.from(e, AxiosError$1.ERR_BAD_RESPONSE, this, null, this.response);
            }
            throw e;
          }
        }
      }
      return data2;
    }],
    timeout: 0,
    xsrfCookieName: "XSRF-TOKEN",
    xsrfHeaderName: "X-XSRF-TOKEN",
    maxContentLength: -1,
    maxBodyLength: -1,
    env: {
      FormData: _null
    },
    validateStatus: function validateStatus(status) {
      return status >= 200 && status < 300;
    },
    headers: {
      common: {
        "Accept": "application/json, text/plain, */*"
      }
    }
  };
  utils$6.forEach(["delete", "get", "head"], function forEachMethodNoData(method) {
    defaults$3.headers[method] = {};
  });
  utils$6.forEach(["post", "put", "patch"], function forEachMethodWithData(method) {
    defaults$3.headers[method] = utils$6.merge(DEFAULT_CONTENT_TYPE);
  });
  var defaults_1 = defaults$3;
  var utils$5 = utils$h;
  var defaults$2 = defaults_1;
  var transformData$1 = function transformData2(data2, headers, fns) {
    var context = this || defaults$2;
    utils$5.forEach(fns, function transform(fn) {
      data2 = fn.call(context, data2, headers);
    });
    return data2;
  };
  var isCancel$1 = function isCancel2(value) {
    return !!(value && value.__CANCEL__);
  };
  var utils$4 = utils$h;
  var transformData = transformData$1;
  var isCancel = isCancel$1;
  var defaults$1 = defaults_1;
  var CanceledError$1 = CanceledError_1;
  function throwIfCancellationRequested(config) {
    if (config.cancelToken) {
      config.cancelToken.throwIfRequested();
    }
    if (config.signal && config.signal.aborted) {
      throw new CanceledError$1();
    }
  }
  var dispatchRequest$1 = function dispatchRequest2(config) {
    throwIfCancellationRequested(config);
    config.headers = config.headers || {};
    config.data = transformData.call(
      config,
      config.data,
      config.headers,
      config.transformRequest
    );
    config.headers = utils$4.merge(
      config.headers.common || {},
      config.headers[config.method] || {},
      config.headers
    );
    utils$4.forEach(
      ["delete", "get", "head", "post", "put", "patch", "common"],
      function cleanHeaderConfig(method) {
        delete config.headers[method];
      }
    );
    var adapter = config.adapter || defaults$1.adapter;
    return adapter(config).then(function onAdapterResolution(response) {
      throwIfCancellationRequested(config);
      response.data = transformData.call(
        config,
        response.data,
        response.headers,
        config.transformResponse
      );
      return response;
    }, function onAdapterRejection(reason) {
      if (!isCancel(reason)) {
        throwIfCancellationRequested(config);
        if (reason && reason.response) {
          reason.response.data = transformData.call(
            config,
            reason.response.data,
            reason.response.headers,
            config.transformResponse
          );
        }
      }
      return Promise.reject(reason);
    });
  };
  var utils$3 = utils$h;
  var mergeConfig$2 = function mergeConfig2(config1, config2) {
    config2 = config2 || {};
    var config = {};
    function getMergedValue(target, source) {
      if (utils$3.isPlainObject(target) && utils$3.isPlainObject(source)) {
        return utils$3.merge(target, source);
      } else if (utils$3.isPlainObject(source)) {
        return utils$3.merge({}, source);
      } else if (utils$3.isArray(source)) {
        return source.slice();
      }
      return source;
    }
    function mergeDeepProperties(prop) {
      if (!utils$3.isUndefined(config2[prop])) {
        return getMergedValue(config1[prop], config2[prop]);
      } else if (!utils$3.isUndefined(config1[prop])) {
        return getMergedValue(void 0, config1[prop]);
      }
    }
    function valueFromConfig2(prop) {
      if (!utils$3.isUndefined(config2[prop])) {
        return getMergedValue(void 0, config2[prop]);
      }
    }
    function defaultToConfig2(prop) {
      if (!utils$3.isUndefined(config2[prop])) {
        return getMergedValue(void 0, config2[prop]);
      } else if (!utils$3.isUndefined(config1[prop])) {
        return getMergedValue(void 0, config1[prop]);
      }
    }
    function mergeDirectKeys(prop) {
      if (prop in config2) {
        return getMergedValue(config1[prop], config2[prop]);
      } else if (prop in config1) {
        return getMergedValue(void 0, config1[prop]);
      }
    }
    var mergeMap = {
      "url": valueFromConfig2,
      "method": valueFromConfig2,
      "data": valueFromConfig2,
      "baseURL": defaultToConfig2,
      "transformRequest": defaultToConfig2,
      "transformResponse": defaultToConfig2,
      "paramsSerializer": defaultToConfig2,
      "timeout": defaultToConfig2,
      "timeoutMessage": defaultToConfig2,
      "withCredentials": defaultToConfig2,
      "adapter": defaultToConfig2,
      "responseType": defaultToConfig2,
      "xsrfCookieName": defaultToConfig2,
      "xsrfHeaderName": defaultToConfig2,
      "onUploadProgress": defaultToConfig2,
      "onDownloadProgress": defaultToConfig2,
      "decompress": defaultToConfig2,
      "maxContentLength": defaultToConfig2,
      "maxBodyLength": defaultToConfig2,
      "beforeRedirect": defaultToConfig2,
      "transport": defaultToConfig2,
      "httpAgent": defaultToConfig2,
      "httpsAgent": defaultToConfig2,
      "cancelToken": defaultToConfig2,
      "socketPath": defaultToConfig2,
      "responseEncoding": defaultToConfig2,
      "validateStatus": mergeDirectKeys
    };
    utils$3.forEach(Object.keys(config1).concat(Object.keys(config2)), function computeConfigValue(prop) {
      var merge2 = mergeMap[prop] || mergeDeepProperties;
      var configValue = merge2(prop);
      utils$3.isUndefined(configValue) && merge2 !== mergeDirectKeys || (config[prop] = configValue);
    });
    return config;
  };
  var data = {
    "version": "0.27.2"
  };
  var VERSION = data.version;
  var AxiosError = AxiosError_1;
  var validators$1 = {};
  ["object", "boolean", "number", "function", "string", "symbol"].forEach(function(type, i) {
    validators$1[type] = function validator2(thing) {
      return typeof thing === type || "a" + (i < 1 ? "n " : " ") + type;
    };
  });
  var deprecatedWarnings = {};
  validators$1.transitional = function transitional2(validator2, version, message) {
    function formatMessage(opt, desc) {
      return "[Axios v" + VERSION + "] Transitional option '" + opt + "'" + desc + (message ? ". " + message : "");
    }
    return function(value, opt, opts) {
      if (validator2 === false) {
        throw new AxiosError(
          formatMessage(opt, " has been removed" + (version ? " in " + version : "")),
          AxiosError.ERR_DEPRECATED
        );
      }
      if (version && !deprecatedWarnings[opt]) {
        deprecatedWarnings[opt] = true;
        console.warn(
          formatMessage(
            opt,
            " has been deprecated since v" + version + " and will be removed in the near future"
          )
        );
      }
      return validator2 ? validator2(value, opt, opts) : true;
    };
  };
  function assertOptions(options, schema, allowUnknown) {
    if (typeof options !== "object") {
      throw new AxiosError("options must be an object", AxiosError.ERR_BAD_OPTION_VALUE);
    }
    var keys = Object.keys(options);
    var i = keys.length;
    while (i-- > 0) {
      var opt = keys[i];
      var validator2 = schema[opt];
      if (validator2) {
        var value = options[opt];
        var result = value === void 0 || validator2(value, opt, options);
        if (result !== true) {
          throw new AxiosError("option " + opt + " must be " + result, AxiosError.ERR_BAD_OPTION_VALUE);
        }
        continue;
      }
      if (allowUnknown !== true) {
        throw new AxiosError("Unknown option " + opt, AxiosError.ERR_BAD_OPTION);
      }
    }
  }
  var validator$1 = {
    assertOptions,
    validators: validators$1
  };
  var utils$2 = utils$h;
  var buildURL = buildURL$2;
  var InterceptorManager = InterceptorManager_1;
  var dispatchRequest = dispatchRequest$1;
  var mergeConfig$1 = mergeConfig$2;
  var buildFullPath = buildFullPath$2;
  var validator = validator$1;
  var validators = validator.validators;
  function Axios$1(instanceConfig) {
    this.defaults = instanceConfig;
    this.interceptors = {
      request: new InterceptorManager(),
      response: new InterceptorManager()
    };
  }
  Axios$1.prototype.request = function request(configOrUrl, config) {
    if (typeof configOrUrl === "string") {
      config = config || {};
      config.url = configOrUrl;
    } else {
      config = configOrUrl || {};
    }
    config = mergeConfig$1(this.defaults, config);
    if (config.method) {
      config.method = config.method.toLowerCase();
    } else if (this.defaults.method) {
      config.method = this.defaults.method.toLowerCase();
    } else {
      config.method = "get";
    }
    var transitional2 = config.transitional;
    if (transitional2 !== void 0) {
      validator.assertOptions(transitional2, {
        silentJSONParsing: validators.transitional(validators.boolean),
        forcedJSONParsing: validators.transitional(validators.boolean),
        clarifyTimeoutError: validators.transitional(validators.boolean)
      }, false);
    }
    var requestInterceptorChain = [];
    var synchronousRequestInterceptors = true;
    this.interceptors.request.forEach(function unshiftRequestInterceptors(interceptor) {
      if (typeof interceptor.runWhen === "function" && interceptor.runWhen(config) === false) {
        return;
      }
      synchronousRequestInterceptors = synchronousRequestInterceptors && interceptor.synchronous;
      requestInterceptorChain.unshift(interceptor.fulfilled, interceptor.rejected);
    });
    var responseInterceptorChain = [];
    this.interceptors.response.forEach(function pushResponseInterceptors(interceptor) {
      responseInterceptorChain.push(interceptor.fulfilled, interceptor.rejected);
    });
    var promise;
    if (!synchronousRequestInterceptors) {
      var chain = [dispatchRequest, void 0];
      Array.prototype.unshift.apply(chain, requestInterceptorChain);
      chain = chain.concat(responseInterceptorChain);
      promise = Promise.resolve(config);
      while (chain.length) {
        promise = promise.then(chain.shift(), chain.shift());
      }
      return promise;
    }
    var newConfig = config;
    while (requestInterceptorChain.length) {
      var onFulfilled = requestInterceptorChain.shift();
      var onRejected = requestInterceptorChain.shift();
      try {
        newConfig = onFulfilled(newConfig);
      } catch (error) {
        onRejected(error);
        break;
      }
    }
    try {
      promise = dispatchRequest(newConfig);
    } catch (error) {
      return Promise.reject(error);
    }
    while (responseInterceptorChain.length) {
      promise = promise.then(responseInterceptorChain.shift(), responseInterceptorChain.shift());
    }
    return promise;
  };
  Axios$1.prototype.getUri = function getUri(config) {
    config = mergeConfig$1(this.defaults, config);
    var fullPath = buildFullPath(config.baseURL, config.url);
    return buildURL(fullPath, config.params, config.paramsSerializer);
  };
  utils$2.forEach(["delete", "get", "head", "options"], function forEachMethodNoData(method) {
    Axios$1.prototype[method] = function(url, config) {
      return this.request(mergeConfig$1(config || {}, {
        method,
        url,
        data: (config || {}).data
      }));
    };
  });
  utils$2.forEach(["post", "put", "patch"], function forEachMethodWithData(method) {
    function generateHTTPMethod(isForm) {
      return function httpMethod(url, data2, config) {
        return this.request(mergeConfig$1(config || {}, {
          method,
          headers: isForm ? {
            "Content-Type": "multipart/form-data"
          } : {},
          url,
          data: data2
        }));
      };
    }
    Axios$1.prototype[method] = generateHTTPMethod();
    Axios$1.prototype[method + "Form"] = generateHTTPMethod(true);
  });
  var Axios_1 = Axios$1;
  var CanceledError = CanceledError_1;
  function CancelToken(executor) {
    if (typeof executor !== "function") {
      throw new TypeError("executor must be a function.");
    }
    var resolvePromise;
    this.promise = new Promise(function promiseExecutor(resolve) {
      resolvePromise = resolve;
    });
    var token = this;
    this.promise.then(function(cancel) {
      if (!token._listeners)
        return;
      var i;
      var l = token._listeners.length;
      for (i = 0; i < l; i++) {
        token._listeners[i](cancel);
      }
      token._listeners = null;
    });
    this.promise.then = function(onfulfilled) {
      var _resolve;
      var promise = new Promise(function(resolve) {
        token.subscribe(resolve);
        _resolve = resolve;
      }).then(onfulfilled);
      promise.cancel = function reject() {
        token.unsubscribe(_resolve);
      };
      return promise;
    };
    executor(function cancel(message) {
      if (token.reason) {
        return;
      }
      token.reason = new CanceledError(message);
      resolvePromise(token.reason);
    });
  }
  CancelToken.prototype.throwIfRequested = function throwIfRequested() {
    if (this.reason) {
      throw this.reason;
    }
  };
  CancelToken.prototype.subscribe = function subscribe(listener) {
    if (this.reason) {
      listener(this.reason);
      return;
    }
    if (this._listeners) {
      this._listeners.push(listener);
    } else {
      this._listeners = [listener];
    }
  };
  CancelToken.prototype.unsubscribe = function unsubscribe(listener) {
    if (!this._listeners) {
      return;
    }
    var index = this._listeners.indexOf(listener);
    if (index !== -1) {
      this._listeners.splice(index, 1);
    }
  };
  CancelToken.source = function source() {
    var cancel;
    var token = new CancelToken(function executor(c) {
      cancel = c;
    });
    return {
      token,
      cancel
    };
  };
  var CancelToken_1 = CancelToken;
  var spread = function spread2(callback) {
    return function wrap(arr) {
      return callback.apply(null, arr);
    };
  };
  var utils$1 = utils$h;
  var isAxiosError = function isAxiosError2(payload) {
    return utils$1.isObject(payload) && payload.isAxiosError === true;
  };
  var utils = utils$h;
  var bind = bind$2;
  var Axios = Axios_1;
  var mergeConfig = mergeConfig$2;
  var defaults = defaults_1;
  function createInstance(defaultConfig) {
    var context = new Axios(defaultConfig);
    var instance = bind(Axios.prototype.request, context);
    utils.extend(instance, Axios.prototype, context);
    utils.extend(instance, context);
    instance.create = function create(instanceConfig) {
      return createInstance(mergeConfig(defaultConfig, instanceConfig));
    };
    return instance;
  }
  var axios$1 = createInstance(defaults);
  axios$1.Axios = Axios;
  axios$1.CanceledError = CanceledError_1;
  axios$1.CancelToken = CancelToken_1;
  axios$1.isCancel = isCancel$1;
  axios$1.VERSION = data.version;
  axios$1.toFormData = toFormData_1;
  axios$1.AxiosError = AxiosError_1;
  axios$1.Cancel = axios$1.CanceledError;
  axios$1.all = function all(promises) {
    return Promise.all(promises);
  };
  axios$1.spread = spread;
  axios$1.isAxiosError = isAxiosError;
  axios$2.exports = axios$1;
  axios$2.exports.default = axios$1;
  var axios = axios$2.exports;
  let restConfig = window.ZionProRestConfig;
  const ZionService = axios.create({
    baseURL: `${restConfig.rest_root}zionbuilder-pro/`,
    headers: {
      "X-WP-Nonce": restConfig.nonce,
      "Accept": "application/json",
      "Content-Type": "application/json"
    }
  });
  const getAdobeFonts = function(useCache = true) {
    return ZionService.get("v1/adobe-fonts");
  };
  const refreshAdobeFontsLists = function(useCache = true) {
    return ZionService.get("v1/adobe-fonts/refresh-kits");
  };
  const uploadIconsPackage = function(iconPack) {
    return ZionService.post(
      "v1/icons",
      iconPack,
      {
        headers: {
          "Content-Type": "multipart/form-data"
        }
      }
    );
  };
  const exportIconsPackage = function(name) {
    return ZionService.post(`v1/icons/export`, {
      icon_package: name
    }, {
      responseType: "arraybuffer"
    });
  };
  const deleteIconsPackage = function(name) {
    return ZionService.delete("v1/icons", {
      data: {
        icon_package: name
      }
    });
  };
  const connectApiKey = function(license) {
    return ZionService.post("v1/license/connect", {
      api_key: license
    });
  };
  const deleteLicense = function(license) {
    return ZionService.post("v1/license/disconnect");
  };
  const projects = vue.ref([]);
  const loaded = vue.ref(false);
  const useAdobeFonts = () => {
    const fetchTypekitFonts = (useCache = true) => {
      return new Promise((resolve, reject) => {
        if (loaded.value && useCache) {
          resolve();
        } else {
          const method = useCache ? getAdobeFonts : refreshAdobeFontsLists;
          method().then((response) => {
            projects.value = response.data;
            resolve(response.data);
          }).catch(function(error) {
            reject(error);
          });
        }
      });
    };
    const getAdobeProjects = vue.computed(() => {
      return projects.value;
    });
    const resetTypekitFonts = () => {
      projects.value = [];
    };
    return {
      fetchTypekitFonts,
      resetTypekitFonts,
      getAdobeProjects
    };
  };
  const initialData = window.ZionBuilderProInitialData;
  const license_key = vue.ref(initialData.license_key);
  const license_details = vue.ref(initialData.license_details);
  const useLicense = () => {
    const getKey = () => {
      return license_key.value;
    };
    const getKeyDetails = () => {
      return license_details.value;
    };
    const updateApiKey = (newValue) => {
      license_key.value = newValue;
    };
    const updateApiDetails = (newValue) => {
      license_details.value = newValue;
    };
    const deleteApiKey = () => {
      license_key.value = "";
      license_details.value = null;
    };
    return {
      getKey,
      getKeyDetails,
      updateApiKey,
      updateApiDetails,
      deleteApiKey
    };
  };
  var AdobeFonts_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$6 = {
    name: "Typekit",
    components: {
      AdobeFontsTab
    },
    setup(props) {
      const builderOptionsStore = window.zb.components.useBuilderOptionsStore();
      const loading = vue.ref(false);
      const { getAdobeProjects, resetTypekitFonts, fetchTypekitFonts } = useAdobeFonts();
      let token = vue.computed({
        get: () => {
          return builderOptionsStore.getOptionValue("typekit_token");
        },
        set: (val) => {
          if (val.length > 0) {
            loading.value = true;
            loadKits();
          } else {
            resetTypekitFonts();
          }
          builderOptionsStore.addTypeKitToken(val);
          builderOptionsStore.saveOptionsToDB().then(() => {
            if (val.length > 0) {
              return loadKits(false);
            }
          }).finally(() => {
            loading.value = false;
          });
        }
      });
      loadKits();
      function loadKits(useCache = true) {
        loading.value = true;
        return fetchTypekitFonts(useCache).finally(() => {
          loading.value = false;
        });
      }
      return {
        token,
        loadKits,
        loading,
        getAdobeProjects
      };
    }
  };
  const _hoisted_1$5 = { class: "znpb-admin-typekit-fonts__header" };
  const _hoisted_2$5 = { class: "" };
  const _hoisted_3$4 = ["innerHTML"];
  const _hoisted_4$2 = { class: "" };
  const _hoisted_5$2 = { class: "znpb-admin-typekit-fonts__content" };
  const _hoisted_6$2 = { key: 2 };
  const _hoisted_7$2 = { class: "znpb-admin-info-p" };
  function _sfc_render$6(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_Button = vue.resolveComponent("Button");
    const _component_BaseInput = vue.resolveComponent("BaseInput");
    const _component_Tooltip = vue.resolveComponent("Tooltip");
    const _component_EmptyList = vue.resolveComponent("EmptyList");
    const _component_Loader = vue.resolveComponent("Loader");
    const _component_AdobeFontsTab = vue.resolveComponent("AdobeFontsTab");
    const _component_Icon = vue.resolveComponent("Icon");
    const _component_PageTemplate = vue.resolveComponent("PageTemplate");
    return vue.openBlock(), vue.createBlock(_component_PageTemplate, null, {
      right: vue.withCtx(() => [
        vue.createElementVNode("p", _hoisted_7$2, vue.toDisplayString(_ctx.$translate("setup_typekit_fonts")), 1)
      ]),
      default: vue.withCtx(() => [
        vue.createElementVNode("h3", null, vue.toDisplayString(_ctx.$translate("typekit_fonts")), 1),
        vue.createElementVNode("div", _hoisted_1$5, [
          vue.createElementVNode("div", _hoisted_2$5, [
            vue.createElementVNode("h4", null, vue.toDisplayString(_ctx.$translate("typekit_api_key")), 1),
            vue.createElementVNode("div", {
              class: "znpb-admin-typekit-fonts__description",
              innerHTML: _ctx.$translate("typekit_api_description")
            }, null, 8, _hoisted_3$4)
          ]),
          vue.createElementVNode("div", _hoisted_4$2, [
            vue.createVNode(_component_Tooltip, {
              content: _ctx.$translate("paste_typekit_token")
            }, {
              default: vue.withCtx(() => [
                vue.createVNode(_component_BaseInput, {
                  modelValue: $setup.token,
                  "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => $setup.token = $event)
                }, vue.createSlots({ _: 2 }, [
                  !$setup.token.length ? {
                    name: "suffix",
                    fn: vue.withCtx(() => [
                      vue.createVNode(_component_Button, { type: "line" }, {
                        default: vue.withCtx(() => [
                          vue.createTextVNode(vue.toDisplayString(_ctx.$translate("submit")), 1)
                        ]),
                        _: 1
                      })
                    ]),
                    key: "0"
                  } : void 0
                ]), 1032, ["modelValue"])
              ]),
              _: 1
            }, 8, ["content"])
          ])
        ]),
        vue.createElementVNode("div", _hoisted_5$2, [
          $setup.token && !$setup.loading && $setup.getAdobeProjects.length === 0 ? (vue.openBlock(), vue.createBlock(_component_EmptyList, { key: 0 }, {
            default: vue.withCtx(() => [
              vue.createTextVNode(vue.toDisplayString(_ctx.$translate("no_typekit_fonts")), 1)
            ]),
            _: 1
          })) : vue.createCommentVNode("", true),
          $setup.loading ? (vue.openBlock(), vue.createBlock(_component_Loader, { key: 1 })) : vue.createCommentVNode("", true),
          !$setup.loading && $setup.getAdobeProjects.length > 0 ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_6$2, [
            (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList($setup.getAdobeProjects, (font, id) => {
              return vue.openBlock(), vue.createElementBlock("div", { key: id }, [
                (vue.openBlock(), vue.createBlock(_component_AdobeFontsTab, {
                  key: id,
                  font
                }, null, 8, ["font"]))
              ]);
            }), 128))
          ])) : vue.createCommentVNode("", true),
          $setup.token && !$setup.loading ? (vue.openBlock(), vue.createBlock(_component_Button, {
            key: 3,
            type: "secondary",
            onClick: _cache[1] || (_cache[1] = ($event) => $setup.loadKits(false))
          }, {
            default: vue.withCtx(() => [
              vue.createVNode(_component_Icon, { icon: "refresh" }),
              vue.createTextVNode(" " + vue.toDisplayString(_ctx.$translate("refresh_lists")), 1)
            ]),
            _: 1
          })) : vue.createCommentVNode("", true)
        ])
      ]),
      _: 1
    });
  }
  var AdobeFonts = /* @__PURE__ */ _export_sfc(_sfc_main$6, [["render", _sfc_render$6]]);
  var FileSaver_min = { exports: {} };
  (function(module, exports) {
    (function(a, b) {
      b();
    })(commonjsGlobal, function() {
      function b(a2, b2) {
        return "undefined" == typeof b2 ? b2 = { autoBom: false } : "object" != typeof b2 && (console.warn("Deprecated: Expected third argument to be a object"), b2 = { autoBom: !b2 }), b2.autoBom && /^\s*(?:text\/\S*|application\/xml|\S*\/\S*\+xml)\s*;.*charset\s*=\s*utf-8/i.test(a2.type) ? new Blob(["\uFEFF", a2], { type: a2.type }) : a2;
      }
      function c(a2, b2, c2) {
        var d2 = new XMLHttpRequest();
        d2.open("GET", a2), d2.responseType = "blob", d2.onload = function() {
          g(d2.response, b2, c2);
        }, d2.onerror = function() {
          console.error("could not download file");
        }, d2.send();
      }
      function d(a2) {
        var b2 = new XMLHttpRequest();
        b2.open("HEAD", a2, false);
        try {
          b2.send();
        } catch (a3) {
        }
        return 200 <= b2.status && 299 >= b2.status;
      }
      function e(a2) {
        try {
          a2.dispatchEvent(new MouseEvent("click"));
        } catch (c2) {
          var b2 = document.createEvent("MouseEvents");
          b2.initMouseEvent("click", true, true, window, 0, 0, 0, 80, 20, false, false, false, false, 0, null), a2.dispatchEvent(b2);
        }
      }
      var f = "object" == typeof window && window.window === window ? window : "object" == typeof self && self.self === self ? self : "object" == typeof commonjsGlobal && commonjsGlobal.global === commonjsGlobal ? commonjsGlobal : void 0, a = f.navigator && /Macintosh/.test(navigator.userAgent) && /AppleWebKit/.test(navigator.userAgent) && !/Safari/.test(navigator.userAgent), g = f.saveAs || ("object" != typeof window || window !== f ? function() {
      } : "download" in HTMLAnchorElement.prototype && !a ? function(b2, g2, h) {
        var i = f.URL || f.webkitURL, j = document.createElement("a");
        g2 = g2 || b2.name || "download", j.download = g2, j.rel = "noopener", "string" == typeof b2 ? (j.href = b2, j.origin === location.origin ? e(j) : d(j.href) ? c(b2, g2, h) : e(j, j.target = "_blank")) : (j.href = i.createObjectURL(b2), setTimeout(function() {
          i.revokeObjectURL(j.href);
        }, 4e4), setTimeout(function() {
          e(j);
        }, 0));
      } : "msSaveOrOpenBlob" in navigator ? function(f2, g2, h) {
        if (g2 = g2 || f2.name || "download", "string" != typeof f2)
          navigator.msSaveOrOpenBlob(b(f2, h), g2);
        else if (d(f2))
          c(f2, g2, h);
        else {
          var i = document.createElement("a");
          i.href = f2, i.target = "_blank", setTimeout(function() {
            e(i);
          });
        }
      } : function(b2, d2, e2, g2) {
        if (g2 = g2 || open("", "_blank"), g2 && (g2.document.title = g2.document.body.innerText = "downloading..."), "string" == typeof b2)
          return c(b2, d2, e2);
        var h = "application/octet-stream" === b2.type, i = /constructor/i.test(f.HTMLElement) || f.safari, j = /CriOS\/[\d]+/.test(navigator.userAgent);
        if ((j || h && i || a) && "undefined" != typeof FileReader) {
          var k = new FileReader();
          k.onloadend = function() {
            var a2 = k.result;
            a2 = j ? a2 : a2.replace(/^data:[^;]*;/, "data:attachment/file;"), g2 ? g2.location.href = a2 : location = a2, g2 = null;
          }, k.readAsDataURL(b2);
        } else {
          var l = f.URL || f.webkitURL, m = l.createObjectURL(b2);
          g2 ? g2.location = m : location.href = m, g2 = null, setTimeout(function() {
            l.revokeObjectURL(m);
          }, 4e4);
        }
      });
      f.saveAs = g.saveAs = g, module.exports = g;
    });
  })(FileSaver_min);
  var IconsPackModalContent_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$5 = {
    name: "IconsPackModalContent",
    props: {
      iconList: {
        type: Array,
        required: false
      },
      family: {
        type: String,
        required: false
      }
    },
    data() {
      return {
        keyword: ""
      };
    },
    computed: {
      searchModel: {
        get() {
          return this.keyword;
        },
        set(newVal) {
          this.keyword = newVal;
        }
      },
      filteredList() {
        if (this.keyword.length > 0) {
          let filtered = [];
          for (const icon of this.iconList) {
            if (icon.name.includes(this.keyword)) {
              filtered.push(icon);
            }
          }
          return filtered;
        } else
          return this.iconList;
      },
      getPlaceholder() {
        let a = `${this.$translate("search_for_icons")} ${this.getIconNumber} ${this.$translate("icons")}`;
        return a;
      },
      getIconNumber() {
        return this.iconList.length;
      }
    }
  };
  const _hoisted_1$4 = { class: "znpb-icon-pack-modal" };
  const _hoisted_2$4 = { class: "znpb-icon-pack-modal__search" };
  const _hoisted_3$3 = { class: "znpb-icon-pack-modal-scroll znpb-fancy-scrollbar" };
  function _sfc_render$5(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_BaseInput = vue.resolveComponent("BaseInput");
    const _component_IconPackGrid = vue.resolveComponent("IconPackGrid");
    return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$4, [
      vue.createElementVNode("div", _hoisted_2$4, [
        vue.createVNode(_component_BaseInput, {
          modelValue: $options.searchModel,
          "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => $options.searchModel = $event),
          placeholder: $options.getPlaceholder,
          clearable: true,
          icon: "search"
        }, null, 8, ["modelValue", "placeholder"])
      ]),
      vue.createElementVNode("div", _hoisted_3$3, [
        vue.createVNode(_component_IconPackGrid, {
          "icon-list": $options.filteredList,
          family: $props.family,
          "has-scroll": false
        }, null, 8, ["icon-list", "family"])
      ])
    ]);
  }
  var IconsPackModalContent = /* @__PURE__ */ _export_sfc(_sfc_main$5, [["render", _sfc_render$5]]);
  var IconTab_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$4 = {
    name: "IconTab",
    components: {
      IconsPackModalContent
    },
    props: {
      iconsSet: {
        type: Object,
        required: true
      }
    },
    setup(props) {
      const dataSetsStore = window.zb.components.useDataSetsStore();
      const showModalConfirm = vue.ref(false);
      const showModal = vue.ref(false);
      function downloadPack() {
        exportIconsPackage(props.iconsSet.id).then((response) => {
          var blob = new Blob([response.data], {
            type: "application/zip"
          });
          FileSaver_min.exports.saveAs(blob, `${props.iconsSet.name}.zip`);
        });
      }
      function deletePack() {
        showModalConfirm.value = false;
        deleteIconsPackage(props.iconsSet.id).then(() => {
          dataSetsStore.deleteIconSet(props.iconsSet.id);
        });
      }
      return {
        showModalConfirm,
        showModal,
        deletePack,
        downloadPack
      };
    }
  };
  const _hoisted_1$3 = { class: "znpb-admin__google-font-tab" };
  const _hoisted_2$3 = { class: "znpb-admin__google-font-tab-title" };
  const _hoisted_3$2 = { class: "znpb-admin__google-font-tab-actions" };
  function _sfc_render$4(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_Icon = vue.resolveComponent("Icon");
    const _component_Tooltip = vue.resolveComponent("Tooltip");
    const _component_ModalConfirm = vue.resolveComponent("ModalConfirm");
    const _component_IconsPackModalContent = vue.resolveComponent("IconsPackModalContent");
    const _component_modal = vue.resolveComponent("modal");
    return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$3, [
      vue.createElementVNode("div", _hoisted_2$3, vue.toDisplayString($props.iconsSet.name), 1),
      vue.createElementVNode("div", _hoisted_3$2, [
        vue.createVNode(_component_Tooltip, {
          content: _ctx.$translate("click_to_preview_icon"),
          class: "znpb-actions-popup-icons",
          modifiers: [{ name: "offset", options: { offset: [0, 15] } }],
          "append-to": "element",
          placement: "bottom"
        }, {
          default: vue.withCtx(() => [
            vue.createVNode(_component_Icon, {
              icon: "eye",
              onClick: _cache[0] || (_cache[0] = ($event) => $setup.showModal = true)
            })
          ]),
          _: 1
        }, 8, ["content"]),
        vue.createVNode(_component_Tooltip, {
          "append-to": "element",
          modifiers: [{ name: "offset", options: { offset: [0, 15] } }],
          placement: "bottom",
          content: _ctx.$translate("click_to_download_icon"),
          class: "znpb-actions-popup-icons"
        }, {
          default: vue.withCtx(() => [
            vue.createVNode(_component_Icon, {
              icon: "import",
              rotate: 180,
              onClick: $setup.downloadPack
            }, null, 8, ["onClick"])
          ]),
          _: 1
        }, 8, ["content"]),
        !$props.iconsSet.built_in ? (vue.openBlock(), vue.createBlock(_component_Tooltip, {
          key: 0,
          "append-to": "element",
          placement: "bottom",
          modifiers: [{ name: "offset", options: { offset: [0, 15] } }],
          class: "znpb-actions-popup-icons",
          content: _ctx.$translate("click_to_delete_icon")
        }, {
          default: vue.withCtx(() => [
            vue.createVNode(_component_Icon, {
              icon: "delete",
              onClick: _cache[1] || (_cache[1] = ($event) => $setup.showModalConfirm = true)
            })
          ]),
          _: 1
        }, 8, ["content"])) : vue.createCommentVNode("", true)
      ]),
      $setup.showModalConfirm ? (vue.openBlock(), vue.createBlock(_component_ModalConfirm, {
        key: 0,
        width: 530,
        "confirm-text": _ctx.$translate("icon_delete_confirm"),
        "cancel-text": _ctx.$translate("icon_delete_cancel"),
        onConfirm: $setup.deletePack,
        onCancel: _cache[2] || (_cache[2] = ($event) => $setup.showModalConfirm = false)
      }, {
        default: vue.withCtx(() => [
          vue.createTextVNode(vue.toDisplayString(_ctx.$translate("are_you_sure_icons_delete")), 1)
        ]),
        _: 1
      }, 8, ["confirm-text", "cancel-text", "onConfirm"])) : vue.createCommentVNode("", true),
      vue.createVNode(_component_modal, {
        show: $setup.showModal,
        "onUpdate:show": _cache[3] || (_cache[3] = ($event) => $setup.showModal = $event),
        width: 590,
        title: $props.iconsSet.name,
        fullscreen: false,
        "show-backdrop": false,
        "append-to": "#znpb-admin",
        "show-maximize": false
      }, {
        default: vue.withCtx(() => [
          vue.createVNode(_component_IconsPackModalContent, {
            "icon-list": $props.iconsSet.icons,
            family: $props.iconsSet.name
          }, null, 8, ["icon-list", "family"])
        ]),
        _: 1
      }, 8, ["show", "title"])
    ]);
  }
  var IconTab = /* @__PURE__ */ _export_sfc(_sfc_main$4, [["render", _sfc_render$4]]);
  var IconsManager_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$3 = {
    name: "IconsManager",
    components: {
      IconTab
    },
    setup() {
      const dataSetsStore = window.zb.components.useDataSetsStore();
      const loading = vue.ref(false);
      const isInitial = vue.ref(true);
      const isSaving = vue.ref(false);
      const value = vue.ref();
      let inputValue = vue.computed({
        get: () => {
          return value.value;
        },
        set: (file) => {
          if (!file) {
            return;
          }
          const formData = new FormData();
          formData.append("zip", file, file.name);
          uploadIconsPackage(formData).then((response) => {
            if (response.data.css) {
              const style = document.createElement("style");
              style.appendChild(document.createTextNode(response.data.css));
              document.head.appendChild(style);
            }
            dataSetsStore.addIconsSet(response.data);
          }).finally(() => {
            inputValue.value = null;
          });
        }
      });
      return {
        dataSetsStore,
        loading,
        isInitial,
        isSaving,
        inputValue
      };
    },
    data() {
      return {
        loading: false,
        isInitial: true,
        isSaving: false
      };
    }
  };
  const _hoisted_1$2 = {
    key: 0,
    class: "znpb-admin__google-font-tab znpb-admin__google-font-tab--titles"
  };
  const _hoisted_2$2 = { class: "znpb-admin__google-font-tab-title" };
  const _hoisted_3$1 = { class: "znpb-admin__google-font-tab-actions" };
  const _hoisted_4$1 = { class: "znpb-admin-icons-wrapper" };
  const _hoisted_5$1 = {
    key: 1,
    class: "znpb-admin-google-fonts-wrapper"
  };
  const _hoisted_6$1 = { class: "znpb-admin-google-fonts-actions znpb-admin-upload-icons" };
  const _hoisted_7$1 = ["value", "disabled"];
  const _hoisted_8$1 = { class: "znpb-admin-info-p" };
  function _sfc_render$3(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_EmptyList = vue.resolveComponent("EmptyList");
    const _component_Tooltip = vue.resolveComponent("Tooltip");
    const _component_IconTab = vue.resolveComponent("IconTab");
    const _component_ListAnimation = vue.resolveComponent("ListAnimation");
    const _component_Icon = vue.resolveComponent("Icon");
    const _component_Button = vue.resolveComponent("Button");
    const _component_PageTemplate = vue.resolveComponent("PageTemplate");
    return vue.openBlock(), vue.createBlock(_component_PageTemplate, null, {
      right: vue.withCtx(() => [
        vue.createElementVNode("p", _hoisted_8$1, vue.toDisplayString(_ctx.$translate("icons_info")), 1)
      ]),
      default: vue.withCtx(() => [
        vue.createElementVNode("h3", null, vue.toDisplayString(_ctx.$translate("custom_icons")), 1),
        $setup.dataSetsStore.dataSets.icons.length > 0 ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_1$2, [
          vue.createElementVNode("div", _hoisted_2$2, vue.toDisplayString(_ctx.$translate("icon_pack")), 1),
          vue.createElementVNode("div", _hoisted_3$1, vue.toDisplayString(_ctx.$translate("actions")), 1)
        ])) : vue.createCommentVNode("", true),
        vue.createElementVNode("div", _hoisted_4$1, [
          $setup.dataSetsStore.dataSets.icons.length === 0 ? (vue.openBlock(), vue.createBlock(_component_Tooltip, {
            key: 0,
            content: _ctx.$translate("click_me_to_add_icons")
          }, {
            default: vue.withCtx(() => [
              vue.createVNode(_component_EmptyList, null, {
                default: vue.withCtx(() => [
                  vue.createTextVNode(vue.toDisplayString(_ctx.$translate("no_icons")), 1)
                ]),
                _: 1
              })
            ]),
            _: 1
          }, 8, ["content"])) : vue.createCommentVNode("", true),
          $setup.dataSetsStore.dataSets.icons.length > 0 ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_5$1, [
            vue.createVNode(_component_ListAnimation, null, {
              default: vue.withCtx(() => [
                (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList($setup.dataSetsStore.dataSets.icons, (set, i) => {
                  return vue.openBlock(), vue.createBlock(_component_IconTab, {
                    key: i,
                    "icons-set": set,
                    class: "znpb-admin-tab"
                  }, null, 8, ["icons-set"]);
                }), 128))
              ]),
              _: 1
            })
          ])) : vue.createCommentVNode("", true),
          vue.createElementVNode("div", _hoisted_6$1, [
            vue.createElementVNode("input", {
              value: $setup.inputValue,
              type: "file",
              accept: "zip, application/octet-stream, application/zip, application/x-zip, application/x-zip-compressed",
              multiple: "",
              disabled: $data.isSaving,
              class: "znpb-library-input-file",
              onChange: _cache[0] || (_cache[0] = ($event) => $setup.inputValue = $event.target.files[0])
            }, null, 40, _hoisted_7$1),
            vue.createVNode(_component_Button, { type: "secondary" }, {
              default: vue.withCtx(() => [
                vue.createVNode(_component_Icon, { icon: "plus" }),
                vue.createTextVNode(" " + vue.toDisplayString(_ctx.$translate("add_icons")), 1)
              ]),
              _: 1
            })
          ])
        ])
      ]),
      _: 1
    });
  }
  var IconsManager = /* @__PURE__ */ _export_sfc(_sfc_main$3, [["render", _sfc_render$3]]);
  var ProLicense_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$2 = {
    name: "ProLicense",
    setup() {
      const { getKey, getKeyDetails, updateApiKey, updateApiDetails, deleteApiKey } = useLicense();
      return {
        getKey,
        getKeyDetails,
        updateApiKey,
        updateApiDetails,
        deleteApiKey
      };
    },
    data() {
      return {
        license_key: "",
        showLicenseInput: true,
        message: "",
        loading: false
      };
    },
    computed: {
      apiKeyModel: {
        get() {
          return this.license_key;
        },
        set(newValue) {
          this.license_key = newValue;
        }
      },
      hiddenLicensekey() {
        let lastFourPosition = this.license_key.length - 4;
        return `XXXXXXXXXXXXXXXXXXXXXXXXXXXX${this.license_key.substr(lastFourPosition)}`;
      }
    },
    created() {
      this.license_key = this.getKey();
      this.showLicenseInput = this.license_key.length > 0 ? false : true;
    },
    methods: {
      getValidDate() {
        let details = this.getKeyDetails();
        if (details && details.expires === "lifetime") {
          return "lifetime";
        } else {
          const validUntil = details ? details.expires : null;
          const date = new Date(Date.parse(validUntil));
          return date.toLocaleDateString("en-US");
        }
      },
      deleteKey() {
        this.loading = true;
        let that = this;
        deleteLicense().then(() => {
          that.showLicenseInput = true;
          that.license_key = "";
          that.deleteApiKey();
        }).finally(() => {
          this.loading = false;
        });
      },
      callCheckLicense(licenseKey) {
        if (licenseKey.length === 0) {
          this.message = this.$translate("no_license_input");
        }
        this.loading = true;
        let that = this;
        connectApiKey(this.license_key).then((response) => {
          that.showLicenseInput = false;
          this.updateApiKey(this.license_key);
          this.updateApiDetails(response.data);
          window.location.reload();
        }).catch((error) => {
          console.error(error);
        }).finally(() => {
          that.loading = false;
          that.license_key = this.getKey();
        });
      }
    }
  };
  const _hoisted_1$1 = { class: "znpb-admin-content-wrapper" };
  const _hoisted_2$1 = /* @__PURE__ */ vue.createElementVNode("div", { class: "znpb-admin-content znpb-admin-content--left znpb-admin-content--hiddenXs" }, null, -1);
  const _hoisted_3 = {
    key: 0,
    class: "znpb-admin-license-inputWrapper"
  };
  const _hoisted_4 = { key: 1 };
  const _hoisted_5 = { class: "znpb-admin-templates-titles" };
  const _hoisted_6 = { class: "znpb-admin-templates-titles__heading znpb-admin-templates-titles__heading--title" };
  const _hoisted_7 = { class: "znpb-admin-templates-titles__heading" };
  const _hoisted_8 = { class: "znpb-admin-templates-titles__heading znpb-admin-templates-titles__heading--actions" };
  const _hoisted_9 = { class: "znpb-admin-single-template" };
  const _hoisted_10 = ["innerHTML"];
  const _hoisted_11 = ["innerHTML"];
  const _hoisted_12 = { class: "znpb-admin-single-template__actions" };
  const _hoisted_13 = ["innerHTML"];
  const _hoisted_14 = { class: "znpb-admin-info-p" };
  const _hoisted_15 = { class: "znpb-admin-info-p" };
  function _sfc_render$2(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_BaseInput = vue.resolveComponent("BaseInput");
    const _component_Loader = vue.resolveComponent("Loader");
    const _component_Button = vue.resolveComponent("Button");
    const _component_Icon = vue.resolveComponent("Icon");
    const _component_Tooltip = vue.resolveComponent("Tooltip");
    const _component_PageTemplate = vue.resolveComponent("PageTemplate");
    return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$1, [
      _hoisted_2$1,
      vue.createVNode(_component_PageTemplate, null, {
        right: vue.withCtx(() => [
          vue.createElementVNode("div", null, [
            vue.createElementVNode("p", _hoisted_14, vue.toDisplayString(_ctx.$translate("pro_info")), 1),
            vue.createElementVNode("p", _hoisted_15, vue.toDisplayString(_ctx.$translate("pro_info_desc")), 1)
          ])
        ]),
        default: vue.withCtx(() => [
          vue.createElementVNode("h3", null, vue.toDisplayString(_ctx.$translate("pro_license_key")), 1),
          $data.showLicenseInput ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_3, [
            vue.createVNode(_component_BaseInput, {
              modelValue: $options.apiKeyModel,
              "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => $options.apiKeyModel = $event),
              placeholder: _ctx.$translate("key_example"),
              size: "narrow"
            }, null, 8, ["modelValue", "placeholder"]),
            vue.createVNode(_component_Button, {
              type: "line",
              onClick: _cache[1] || (_cache[1] = ($event) => $options.callCheckLicense($options.apiKeyModel))
            }, {
              default: vue.withCtx(() => [
                vue.createVNode(vue.Transition, {
                  name: "fade",
                  mode: "out-in"
                }, {
                  default: vue.withCtx(() => [
                    $data.loading ? (vue.openBlock(), vue.createBlock(_component_Loader, {
                      key: 0,
                      size: 13
                    })) : (vue.openBlock(), vue.createElementBlock("span", _hoisted_4, vue.toDisplayString(_ctx.$translate("add_license")), 1))
                  ]),
                  _: 1
                })
              ]),
              _: 1
            })
          ])) : (vue.openBlock(), vue.createElementBlock(vue.Fragment, { key: 1 }, [
            vue.createElementVNode("div", _hoisted_5, [
              vue.createElementVNode("h5", _hoisted_6, vue.toDisplayString(_ctx.$translate("key")), 1),
              vue.createElementVNode("h5", _hoisted_7, vue.toDisplayString(_ctx.$translate("valid_until")), 1),
              vue.createElementVNode("h5", _hoisted_8, vue.toDisplayString(_ctx.$translate("actions")), 1)
            ]),
            vue.createElementVNode("div", _hoisted_9, [
              vue.createElementVNode("span", {
                class: "znpb-admin-single-template__title",
                innerHTML: $options.hiddenLicensekey
              }, null, 8, _hoisted_10),
              vue.createElementVNode("span", {
                class: "znpb-admin-single-template__author",
                innerHTML: $options.getValidDate()
              }, null, 8, _hoisted_11),
              vue.createElementVNode("div", _hoisted_12, [
                !$data.loading ? (vue.openBlock(), vue.createBlock(_component_Tooltip, {
                  key: 0,
                  content: _ctx.$translate("delete_key"),
                  "append-to": "element",
                  class: "znpb-admin-single-template__action znpb-delete-icon-pop",
                  modifiers: [
                    {
                      name: "offset",
                      options: {
                        offset: [0, 15]
                      }
                    }
                  ],
                  "position-fixed": true
                }, {
                  default: vue.withCtx(() => [
                    vue.createVNode(_component_Icon, {
                      icon: "delete",
                      onClick: $options.deleteKey
                    }, null, 8, ["onClick"])
                  ]),
                  _: 1
                }, 8, ["content"])) : (vue.openBlock(), vue.createBlock(_component_Loader, {
                  key: 1,
                  size: 13
                }))
              ])
            ])
          ], 64)),
          $data.message.length ? (vue.openBlock(), vue.createElementBlock("p", {
            key: 2,
            class: "znpb-admin-license__error-message",
            innerHTML: $data.message
          }, null, 8, _hoisted_13)) : vue.createCommentVNode("", true)
        ]),
        _: 1
      })
    ]);
  }
  var ProLicense = /* @__PURE__ */ _export_sfc(_sfc_main$2, [["render", _sfc_render$2]]);
  var md5 = { exports: {} };
  var core = { exports: {} };
  (function(module, exports) {
    (function(root2, factory) {
      {
        module.exports = factory();
      }
    })(commonjsGlobal, function() {
      var CryptoJS = CryptoJS || function(Math2, undefined$1) {
        var crypto;
        if (typeof window !== "undefined" && window.crypto) {
          crypto = window.crypto;
        }
        if (typeof self !== "undefined" && self.crypto) {
          crypto = self.crypto;
        }
        if (typeof globalThis !== "undefined" && globalThis.crypto) {
          crypto = globalThis.crypto;
        }
        if (!crypto && typeof window !== "undefined" && window.msCrypto) {
          crypto = window.msCrypto;
        }
        if (!crypto && typeof commonjsGlobal !== "undefined" && commonjsGlobal.crypto) {
          crypto = commonjsGlobal.crypto;
        }
        if (!crypto && typeof commonjsRequire === "function") {
          try {
            crypto = require("crypto");
          } catch (err) {
          }
        }
        var cryptoSecureRandomInt = function() {
          if (crypto) {
            if (typeof crypto.getRandomValues === "function") {
              try {
                return crypto.getRandomValues(new Uint32Array(1))[0];
              } catch (err) {
              }
            }
            if (typeof crypto.randomBytes === "function") {
              try {
                return crypto.randomBytes(4).readInt32LE();
              } catch (err) {
              }
            }
          }
          throw new Error("Native crypto module could not be used to get secure random number.");
        };
        var create = Object.create || function() {
          function F() {
          }
          return function(obj) {
            var subtype;
            F.prototype = obj;
            subtype = new F();
            F.prototype = null;
            return subtype;
          };
        }();
        var C = {};
        var C_lib = C.lib = {};
        var Base = C_lib.Base = function() {
          return {
            extend: function(overrides) {
              var subtype = create(this);
              if (overrides) {
                subtype.mixIn(overrides);
              }
              if (!subtype.hasOwnProperty("init") || this.init === subtype.init) {
                subtype.init = function() {
                  subtype.$super.init.apply(this, arguments);
                };
              }
              subtype.init.prototype = subtype;
              subtype.$super = this;
              return subtype;
            },
            create: function() {
              var instance = this.extend();
              instance.init.apply(instance, arguments);
              return instance;
            },
            init: function() {
            },
            mixIn: function(properties) {
              for (var propertyName in properties) {
                if (properties.hasOwnProperty(propertyName)) {
                  this[propertyName] = properties[propertyName];
                }
              }
              if (properties.hasOwnProperty("toString")) {
                this.toString = properties.toString;
              }
            },
            clone: function() {
              return this.init.prototype.extend(this);
            }
          };
        }();
        var WordArray = C_lib.WordArray = Base.extend({
          init: function(words, sigBytes) {
            words = this.words = words || [];
            if (sigBytes != undefined$1) {
              this.sigBytes = sigBytes;
            } else {
              this.sigBytes = words.length * 4;
            }
          },
          toString: function(encoder) {
            return (encoder || Hex).stringify(this);
          },
          concat: function(wordArray) {
            var thisWords = this.words;
            var thatWords = wordArray.words;
            var thisSigBytes = this.sigBytes;
            var thatSigBytes = wordArray.sigBytes;
            this.clamp();
            if (thisSigBytes % 4) {
              for (var i = 0; i < thatSigBytes; i++) {
                var thatByte = thatWords[i >>> 2] >>> 24 - i % 4 * 8 & 255;
                thisWords[thisSigBytes + i >>> 2] |= thatByte << 24 - (thisSigBytes + i) % 4 * 8;
              }
            } else {
              for (var j = 0; j < thatSigBytes; j += 4) {
                thisWords[thisSigBytes + j >>> 2] = thatWords[j >>> 2];
              }
            }
            this.sigBytes += thatSigBytes;
            return this;
          },
          clamp: function() {
            var words = this.words;
            var sigBytes = this.sigBytes;
            words[sigBytes >>> 2] &= 4294967295 << 32 - sigBytes % 4 * 8;
            words.length = Math2.ceil(sigBytes / 4);
          },
          clone: function() {
            var clone = Base.clone.call(this);
            clone.words = this.words.slice(0);
            return clone;
          },
          random: function(nBytes) {
            var words = [];
            for (var i = 0; i < nBytes; i += 4) {
              words.push(cryptoSecureRandomInt());
            }
            return new WordArray.init(words, nBytes);
          }
        });
        var C_enc = C.enc = {};
        var Hex = C_enc.Hex = {
          stringify: function(wordArray) {
            var words = wordArray.words;
            var sigBytes = wordArray.sigBytes;
            var hexChars = [];
            for (var i = 0; i < sigBytes; i++) {
              var bite = words[i >>> 2] >>> 24 - i % 4 * 8 & 255;
              hexChars.push((bite >>> 4).toString(16));
              hexChars.push((bite & 15).toString(16));
            }
            return hexChars.join("");
          },
          parse: function(hexStr) {
            var hexStrLength = hexStr.length;
            var words = [];
            for (var i = 0; i < hexStrLength; i += 2) {
              words[i >>> 3] |= parseInt(hexStr.substr(i, 2), 16) << 24 - i % 8 * 4;
            }
            return new WordArray.init(words, hexStrLength / 2);
          }
        };
        var Latin1 = C_enc.Latin1 = {
          stringify: function(wordArray) {
            var words = wordArray.words;
            var sigBytes = wordArray.sigBytes;
            var latin1Chars = [];
            for (var i = 0; i < sigBytes; i++) {
              var bite = words[i >>> 2] >>> 24 - i % 4 * 8 & 255;
              latin1Chars.push(String.fromCharCode(bite));
            }
            return latin1Chars.join("");
          },
          parse: function(latin1Str) {
            var latin1StrLength = latin1Str.length;
            var words = [];
            for (var i = 0; i < latin1StrLength; i++) {
              words[i >>> 2] |= (latin1Str.charCodeAt(i) & 255) << 24 - i % 4 * 8;
            }
            return new WordArray.init(words, latin1StrLength);
          }
        };
        var Utf8 = C_enc.Utf8 = {
          stringify: function(wordArray) {
            try {
              return decodeURIComponent(escape(Latin1.stringify(wordArray)));
            } catch (e) {
              throw new Error("Malformed UTF-8 data");
            }
          },
          parse: function(utf8Str) {
            return Latin1.parse(unescape(encodeURIComponent(utf8Str)));
          }
        };
        var BufferedBlockAlgorithm = C_lib.BufferedBlockAlgorithm = Base.extend({
          reset: function() {
            this._data = new WordArray.init();
            this._nDataBytes = 0;
          },
          _append: function(data2) {
            if (typeof data2 == "string") {
              data2 = Utf8.parse(data2);
            }
            this._data.concat(data2);
            this._nDataBytes += data2.sigBytes;
          },
          _process: function(doFlush) {
            var processedWords;
            var data2 = this._data;
            var dataWords = data2.words;
            var dataSigBytes = data2.sigBytes;
            var blockSize = this.blockSize;
            var blockSizeBytes = blockSize * 4;
            var nBlocksReady = dataSigBytes / blockSizeBytes;
            if (doFlush) {
              nBlocksReady = Math2.ceil(nBlocksReady);
            } else {
              nBlocksReady = Math2.max((nBlocksReady | 0) - this._minBufferSize, 0);
            }
            var nWordsReady = nBlocksReady * blockSize;
            var nBytesReady = Math2.min(nWordsReady * 4, dataSigBytes);
            if (nWordsReady) {
              for (var offset = 0; offset < nWordsReady; offset += blockSize) {
                this._doProcessBlock(dataWords, offset);
              }
              processedWords = dataWords.splice(0, nWordsReady);
              data2.sigBytes -= nBytesReady;
            }
            return new WordArray.init(processedWords, nBytesReady);
          },
          clone: function() {
            var clone = Base.clone.call(this);
            clone._data = this._data.clone();
            return clone;
          },
          _minBufferSize: 0
        });
        C_lib.Hasher = BufferedBlockAlgorithm.extend({
          cfg: Base.extend(),
          init: function(cfg) {
            this.cfg = this.cfg.extend(cfg);
            this.reset();
          },
          reset: function() {
            BufferedBlockAlgorithm.reset.call(this);
            this._doReset();
          },
          update: function(messageUpdate) {
            this._append(messageUpdate);
            this._process();
            return this;
          },
          finalize: function(messageUpdate) {
            if (messageUpdate) {
              this._append(messageUpdate);
            }
            var hash = this._doFinalize();
            return hash;
          },
          blockSize: 512 / 32,
          _createHelper: function(hasher) {
            return function(message, cfg) {
              return new hasher.init(cfg).finalize(message);
            };
          },
          _createHmacHelper: function(hasher) {
            return function(message, key) {
              return new C_algo.HMAC.init(hasher, key).finalize(message);
            };
          }
        });
        var C_algo = C.algo = {};
        return C;
      }(Math);
      return CryptoJS;
    });
  })(core);
  (function(module, exports) {
    (function(root2, factory) {
      {
        module.exports = factory(core.exports);
      }
    })(commonjsGlobal, function(CryptoJS) {
      (function(Math2) {
        var C = CryptoJS;
        var C_lib = C.lib;
        var WordArray = C_lib.WordArray;
        var Hasher = C_lib.Hasher;
        var C_algo = C.algo;
        var T = [];
        (function() {
          for (var i = 0; i < 64; i++) {
            T[i] = Math2.abs(Math2.sin(i + 1)) * 4294967296 | 0;
          }
        })();
        var MD5 = C_algo.MD5 = Hasher.extend({
          _doReset: function() {
            this._hash = new WordArray.init([
              1732584193,
              4023233417,
              2562383102,
              271733878
            ]);
          },
          _doProcessBlock: function(M, offset) {
            for (var i = 0; i < 16; i++) {
              var offset_i = offset + i;
              var M_offset_i = M[offset_i];
              M[offset_i] = (M_offset_i << 8 | M_offset_i >>> 24) & 16711935 | (M_offset_i << 24 | M_offset_i >>> 8) & 4278255360;
            }
            var H = this._hash.words;
            var M_offset_0 = M[offset + 0];
            var M_offset_1 = M[offset + 1];
            var M_offset_2 = M[offset + 2];
            var M_offset_3 = M[offset + 3];
            var M_offset_4 = M[offset + 4];
            var M_offset_5 = M[offset + 5];
            var M_offset_6 = M[offset + 6];
            var M_offset_7 = M[offset + 7];
            var M_offset_8 = M[offset + 8];
            var M_offset_9 = M[offset + 9];
            var M_offset_10 = M[offset + 10];
            var M_offset_11 = M[offset + 11];
            var M_offset_12 = M[offset + 12];
            var M_offset_13 = M[offset + 13];
            var M_offset_14 = M[offset + 14];
            var M_offset_15 = M[offset + 15];
            var a = H[0];
            var b = H[1];
            var c = H[2];
            var d = H[3];
            a = FF(a, b, c, d, M_offset_0, 7, T[0]);
            d = FF(d, a, b, c, M_offset_1, 12, T[1]);
            c = FF(c, d, a, b, M_offset_2, 17, T[2]);
            b = FF(b, c, d, a, M_offset_3, 22, T[3]);
            a = FF(a, b, c, d, M_offset_4, 7, T[4]);
            d = FF(d, a, b, c, M_offset_5, 12, T[5]);
            c = FF(c, d, a, b, M_offset_6, 17, T[6]);
            b = FF(b, c, d, a, M_offset_7, 22, T[7]);
            a = FF(a, b, c, d, M_offset_8, 7, T[8]);
            d = FF(d, a, b, c, M_offset_9, 12, T[9]);
            c = FF(c, d, a, b, M_offset_10, 17, T[10]);
            b = FF(b, c, d, a, M_offset_11, 22, T[11]);
            a = FF(a, b, c, d, M_offset_12, 7, T[12]);
            d = FF(d, a, b, c, M_offset_13, 12, T[13]);
            c = FF(c, d, a, b, M_offset_14, 17, T[14]);
            b = FF(b, c, d, a, M_offset_15, 22, T[15]);
            a = GG(a, b, c, d, M_offset_1, 5, T[16]);
            d = GG(d, a, b, c, M_offset_6, 9, T[17]);
            c = GG(c, d, a, b, M_offset_11, 14, T[18]);
            b = GG(b, c, d, a, M_offset_0, 20, T[19]);
            a = GG(a, b, c, d, M_offset_5, 5, T[20]);
            d = GG(d, a, b, c, M_offset_10, 9, T[21]);
            c = GG(c, d, a, b, M_offset_15, 14, T[22]);
            b = GG(b, c, d, a, M_offset_4, 20, T[23]);
            a = GG(a, b, c, d, M_offset_9, 5, T[24]);
            d = GG(d, a, b, c, M_offset_14, 9, T[25]);
            c = GG(c, d, a, b, M_offset_3, 14, T[26]);
            b = GG(b, c, d, a, M_offset_8, 20, T[27]);
            a = GG(a, b, c, d, M_offset_13, 5, T[28]);
            d = GG(d, a, b, c, M_offset_2, 9, T[29]);
            c = GG(c, d, a, b, M_offset_7, 14, T[30]);
            b = GG(b, c, d, a, M_offset_12, 20, T[31]);
            a = HH(a, b, c, d, M_offset_5, 4, T[32]);
            d = HH(d, a, b, c, M_offset_8, 11, T[33]);
            c = HH(c, d, a, b, M_offset_11, 16, T[34]);
            b = HH(b, c, d, a, M_offset_14, 23, T[35]);
            a = HH(a, b, c, d, M_offset_1, 4, T[36]);
            d = HH(d, a, b, c, M_offset_4, 11, T[37]);
            c = HH(c, d, a, b, M_offset_7, 16, T[38]);
            b = HH(b, c, d, a, M_offset_10, 23, T[39]);
            a = HH(a, b, c, d, M_offset_13, 4, T[40]);
            d = HH(d, a, b, c, M_offset_0, 11, T[41]);
            c = HH(c, d, a, b, M_offset_3, 16, T[42]);
            b = HH(b, c, d, a, M_offset_6, 23, T[43]);
            a = HH(a, b, c, d, M_offset_9, 4, T[44]);
            d = HH(d, a, b, c, M_offset_12, 11, T[45]);
            c = HH(c, d, a, b, M_offset_15, 16, T[46]);
            b = HH(b, c, d, a, M_offset_2, 23, T[47]);
            a = II(a, b, c, d, M_offset_0, 6, T[48]);
            d = II(d, a, b, c, M_offset_7, 10, T[49]);
            c = II(c, d, a, b, M_offset_14, 15, T[50]);
            b = II(b, c, d, a, M_offset_5, 21, T[51]);
            a = II(a, b, c, d, M_offset_12, 6, T[52]);
            d = II(d, a, b, c, M_offset_3, 10, T[53]);
            c = II(c, d, a, b, M_offset_10, 15, T[54]);
            b = II(b, c, d, a, M_offset_1, 21, T[55]);
            a = II(a, b, c, d, M_offset_8, 6, T[56]);
            d = II(d, a, b, c, M_offset_15, 10, T[57]);
            c = II(c, d, a, b, M_offset_6, 15, T[58]);
            b = II(b, c, d, a, M_offset_13, 21, T[59]);
            a = II(a, b, c, d, M_offset_4, 6, T[60]);
            d = II(d, a, b, c, M_offset_11, 10, T[61]);
            c = II(c, d, a, b, M_offset_2, 15, T[62]);
            b = II(b, c, d, a, M_offset_9, 21, T[63]);
            H[0] = H[0] + a | 0;
            H[1] = H[1] + b | 0;
            H[2] = H[2] + c | 0;
            H[3] = H[3] + d | 0;
          },
          _doFinalize: function() {
            var data2 = this._data;
            var dataWords = data2.words;
            var nBitsTotal = this._nDataBytes * 8;
            var nBitsLeft = data2.sigBytes * 8;
            dataWords[nBitsLeft >>> 5] |= 128 << 24 - nBitsLeft % 32;
            var nBitsTotalH = Math2.floor(nBitsTotal / 4294967296);
            var nBitsTotalL = nBitsTotal;
            dataWords[(nBitsLeft + 64 >>> 9 << 4) + 15] = (nBitsTotalH << 8 | nBitsTotalH >>> 24) & 16711935 | (nBitsTotalH << 24 | nBitsTotalH >>> 8) & 4278255360;
            dataWords[(nBitsLeft + 64 >>> 9 << 4) + 14] = (nBitsTotalL << 8 | nBitsTotalL >>> 24) & 16711935 | (nBitsTotalL << 24 | nBitsTotalL >>> 8) & 4278255360;
            data2.sigBytes = (dataWords.length + 1) * 4;
            this._process();
            var hash = this._hash;
            var H = hash.words;
            for (var i = 0; i < 4; i++) {
              var H_i = H[i];
              H[i] = (H_i << 8 | H_i >>> 24) & 16711935 | (H_i << 24 | H_i >>> 8) & 4278255360;
            }
            return hash;
          },
          clone: function() {
            var clone = Hasher.clone.call(this);
            clone._hash = this._hash.clone();
            return clone;
          }
        });
        function FF(a, b, c, d, x, s, t) {
          var n = a + (b & c | ~b & d) + x + t;
          return (n << s | n >>> 32 - s) + b;
        }
        function GG(a, b, c, d, x, s, t) {
          var n = a + (b & d | c & ~d) + x + t;
          return (n << s | n >>> 32 - s) + b;
        }
        function HH(a, b, c, d, x, s, t) {
          var n = a + (b ^ c ^ d) + x + t;
          return (n << s | n >>> 32 - s) + b;
        }
        function II(a, b, c, d, x, s, t) {
          var n = a + (c ^ (b | ~d)) + x + t;
          return (n << s | n >>> 32 - s) + b;
        }
        C.MD5 = Hasher._createHelper(MD5);
        C.HmacMD5 = Hasher._createHmacHelper(MD5);
      })(Math);
      return CryptoJS.MD5;
    });
  })(md5);
  const generateUID = function(index, lastDateInSeconds) {
    const startDate = new Date("2019");
    return function() {
      const d = new Date();
      const n = d - startDate;
      if (lastDateInSeconds === false) {
        lastDateInSeconds = n;
      }
      if (lastDateInSeconds !== n) {
        index = 0;
      }
      lastDateInSeconds = n;
      index += 1;
      return "uid" + n + index;
    };
  }(0, false);
  ({
    isMac: window.navigator.userAgent.indexOf("Macintosh") >= 0
  });
  var LibraryPage_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$1 = {
    name: "LibraryPage",
    setup(props) {
      const builderOptionsStore = window.zb.components.useBuilderOptionsStore();
      const computedModel = vue.computed({
        get() {
          return builderOptionsStore.getOptionValue("library_share", {});
        },
        set(newValue) {
          if (newValue === null) {
            builderOptionsStore.updateOptionValue("library_share", {}, false);
          } else {
            const valuesWithIds = generateSourceIDs(newValue);
            builderOptionsStore.updateOptionValue("library_share", valuesWithIds, false);
          }
          builderOptionsStore.debouncedSaveOptions();
        }
      });
      const schema = window.ZionBuilderProInitialData.schemas.library_share;
      function generateSourceIDs(values) {
        if (typeof values.library_sources && Array.isArray(values.library_sources)) {
          values.library_sources.forEach((sourceConfig) => {
            if (typeof sourceConfig.id === "undefined") {
              sourceConfig.id = generateUID();
            }
          });
        }
        return values;
      }
      return {
        computedModel,
        schema
      };
    }
  };
  function _sfc_render$1(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_OptionsForm = vue.resolveComponent("OptionsForm");
    const _component_PageTemplate = vue.resolveComponent("PageTemplate");
    return vue.openBlock(), vue.createBlock(_component_PageTemplate, { class: "znpb-librarySourcesPage" }, {
      default: vue.withCtx(() => [
        vue.createElementVNode("h3", null, vue.toDisplayString(_ctx.$translate("library_share")), 1),
        vue.createVNode(_component_OptionsForm, {
          modelValue: $setup.computedModel,
          "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => $setup.computedModel = $event),
          schema: $setup.schema,
          class: "znpb-connectorForm"
        }, null, 8, ["modelValue", "schema"])
      ]),
      _: 1
    });
  }
  var LibraryPage = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["render", _sfc_render$1]]);
  class Admin {
    constructor(ZionInterface) {
      this.changeRoutes(ZionInterface.routes);
    }
    changeRoutes(routes) {
      const customFontsRoute = routes.getRouteConfig("settings.font-options.custom-fonts");
      if (customFontsRoute) {
        customFontsRoute.remove("label");
        customFontsRoute.set("component", CustomFonts);
      }
      const adobeFontsRoute = routes.getRouteConfig("settings.font-options.adobe-fonts");
      if (adobeFontsRoute) {
        adobeFontsRoute.remove("label");
        adobeFontsRoute.set("component", AdobeFonts);
      }
      const customIconsRoute = routes.getRouteConfig("settings.custom-icons");
      if (customIconsRoute) {
        customIconsRoute.remove("label");
        customIconsRoute.set("component", IconsManager);
      }
      const connectorRoute = routes.getRouteConfig("settings.library");
      if (connectorRoute) {
        connectorRoute.remove("label");
        connectorRoute.set("component", LibraryPage);
      }
      const proRoute = routes.addRoute("pro-license", {
        path: "/pro-license",
        component: ProLicense,
        title: "PRO license key"
      });
      proRoute.set("component", ProLicense);
    }
  }
  var freeGlobal = typeof global == "object" && global && global.Object === Object && global;
  var freeGlobal$1 = freeGlobal;
  var freeSelf = typeof self == "object" && self && self.Object === Object && self;
  var root = freeGlobal$1 || freeSelf || Function("return this")();
  var root$1 = root;
  var Symbol$1 = root$1.Symbol;
  var Symbol$2 = Symbol$1;
  var objectProto$1 = Object.prototype;
  var hasOwnProperty = objectProto$1.hasOwnProperty;
  var nativeObjectToString$1 = objectProto$1.toString;
  var symToStringTag$1 = Symbol$2 ? Symbol$2.toStringTag : void 0;
  function getRawTag(value) {
    var isOwn = hasOwnProperty.call(value, symToStringTag$1), tag = value[symToStringTag$1];
    try {
      value[symToStringTag$1] = void 0;
      var unmasked = true;
    } catch (e) {
    }
    var result = nativeObjectToString$1.call(value);
    if (unmasked) {
      if (isOwn) {
        value[symToStringTag$1] = tag;
      } else {
        delete value[symToStringTag$1];
      }
    }
    return result;
  }
  var objectProto = Object.prototype;
  var nativeObjectToString = objectProto.toString;
  function objectToString(value) {
    return nativeObjectToString.call(value);
  }
  var nullTag = "[object Null]", undefinedTag = "[object Undefined]";
  var symToStringTag = Symbol$2 ? Symbol$2.toStringTag : void 0;
  function baseGetTag(value) {
    if (value == null) {
      return value === void 0 ? undefinedTag : nullTag;
    }
    return symToStringTag && symToStringTag in Object(value) ? getRawTag(value) : objectToString(value);
  }
  function isObjectLike(value) {
    return value != null && typeof value == "object";
  }
  var symbolTag = "[object Symbol]";
  function isSymbol(value) {
    return typeof value == "symbol" || isObjectLike(value) && baseGetTag(value) == symbolTag;
  }
  var reWhitespace = /\s/;
  function trimmedEndIndex(string) {
    var index = string.length;
    while (index-- && reWhitespace.test(string.charAt(index))) {
    }
    return index;
  }
  var reTrimStart = /^\s+/;
  function baseTrim(string) {
    return string ? string.slice(0, trimmedEndIndex(string) + 1).replace(reTrimStart, "") : string;
  }
  function isObject(value) {
    var type = typeof value;
    return value != null && (type == "object" || type == "function");
  }
  var NAN = 0 / 0;
  var reIsBadHex = /^[-+]0x[0-9a-f]+$/i;
  var reIsBinary = /^0b[01]+$/i;
  var reIsOctal = /^0o[0-7]+$/i;
  var freeParseInt = parseInt;
  function toNumber(value) {
    if (typeof value == "number") {
      return value;
    }
    if (isSymbol(value)) {
      return NAN;
    }
    if (isObject(value)) {
      var other = typeof value.valueOf == "function" ? value.valueOf() : value;
      value = isObject(other) ? other + "" : other;
    }
    if (typeof value != "string") {
      return value === 0 ? value : +value;
    }
    value = baseTrim(value);
    var isBinary = reIsBinary.test(value);
    return isBinary || reIsOctal.test(value) ? freeParseInt(value.slice(2), isBinary ? 2 : 8) : reIsBadHex.test(value) ? NAN : +value;
  }
  var now = function() {
    return root$1.Date.now();
  };
  var now$1 = now;
  var FUNC_ERROR_TEXT = "Expected a function";
  var nativeMax = Math.max, nativeMin = Math.min;
  function debounce(func, wait, options) {
    var lastArgs, lastThis, maxWait, result, timerId, lastCallTime, lastInvokeTime = 0, leading = false, maxing = false, trailing = true;
    if (typeof func != "function") {
      throw new TypeError(FUNC_ERROR_TEXT);
    }
    wait = toNumber(wait) || 0;
    if (isObject(options)) {
      leading = !!options.leading;
      maxing = "maxWait" in options;
      maxWait = maxing ? nativeMax(toNumber(options.maxWait) || 0, wait) : maxWait;
      trailing = "trailing" in options ? !!options.trailing : trailing;
    }
    function invokeFunc(time) {
      var args = lastArgs, thisArg = lastThis;
      lastArgs = lastThis = void 0;
      lastInvokeTime = time;
      result = func.apply(thisArg, args);
      return result;
    }
    function leadingEdge(time) {
      lastInvokeTime = time;
      timerId = setTimeout(timerExpired, wait);
      return leading ? invokeFunc(time) : result;
    }
    function remainingWait(time) {
      var timeSinceLastCall = time - lastCallTime, timeSinceLastInvoke = time - lastInvokeTime, timeWaiting = wait - timeSinceLastCall;
      return maxing ? nativeMin(timeWaiting, maxWait - timeSinceLastInvoke) : timeWaiting;
    }
    function shouldInvoke(time) {
      var timeSinceLastCall = time - lastCallTime, timeSinceLastInvoke = time - lastInvokeTime;
      return lastCallTime === void 0 || timeSinceLastCall >= wait || timeSinceLastCall < 0 || maxing && timeSinceLastInvoke >= maxWait;
    }
    function timerExpired() {
      var time = now$1();
      if (shouldInvoke(time)) {
        return trailingEdge(time);
      }
      timerId = setTimeout(timerExpired, remainingWait(time));
    }
    function trailingEdge(time) {
      timerId = void 0;
      if (trailing && lastArgs) {
        return invokeFunc(time);
      }
      lastArgs = lastThis = void 0;
      return result;
    }
    function cancel() {
      if (timerId !== void 0) {
        clearTimeout(timerId);
      }
      lastInvokeTime = 0;
      lastArgs = lastCallTime = lastThis = timerId = void 0;
    }
    function flush() {
      return timerId === void 0 ? result : trailingEdge(now$1());
    }
    function debounced() {
      var time = now$1(), isInvoking = shouldInvoke(time);
      lastArgs = arguments;
      lastThis = this;
      lastCallTime = time;
      if (isInvoking) {
        if (timerId === void 0) {
          return leadingEdge(lastCallTime);
        }
        if (maxing) {
          clearTimeout(timerId);
          timerId = setTimeout(timerExpired, wait);
          return invokeFunc(lastCallTime);
        }
      }
      if (timerId === void 0) {
        timerId = setTimeout(timerExpired, wait);
      }
      return result;
    }
    debounced.cancel = cancel;
    debounced.flush = flush;
    return debounced;
  }
  const _sfc_main = {
    name: "WhiteLabel",
    props: {
      modelValue: {}
    },
    setup(props) {
      const builderOptionsStore = window.zb.components.useBuilderOptionsStore();
      const valueModel = vue.computed({
        get: () => builderOptionsStore.getOptionValue("white_label") || {},
        set: (newValue) => updateOptionValueThrottled("white_label", newValue)
      });
      const schema = window.ZionBuilderProInitialData.white_label_schema;
      const updateOptionValueThrottled = debounce(builderOptionsStore.updateOptionValue, 300);
      return {
        valueModel,
        schema
      };
    }
  };
  const _hoisted_1 = { class: "znpb-white-label" };
  const _hoisted_2 = { class: "znpb-admin-info-p" };
  function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_OptionsForm = vue.resolveComponent("OptionsForm");
    const _component_PageTemplate = vue.resolveComponent("PageTemplate");
    return vue.openBlock(), vue.createElementBlock("div", _hoisted_1, [
      vue.createVNode(_component_PageTemplate, null, {
        right: vue.withCtx(() => [
          vue.createElementVNode("p", _hoisted_2, vue.toDisplayString(_ctx.$translate("white_label_info")), 1)
        ]),
        default: vue.withCtx(() => [
          vue.createElementVNode("h3", null, vue.toDisplayString(_ctx.$translate("white-label")), 1),
          vue.createVNode(_component_OptionsForm, {
            modelValue: $setup.valueModel,
            "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => $setup.valueModel = $event),
            schema: $setup.schema,
            class: "znpb-white-label-form"
          }, null, 8, ["modelValue", "schema"])
        ]),
        _: 1
      })
    ]);
  }
  var WhiteLabel = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render]]);
  function getRouter(router) {
    router.beforeEach((to) => {
      if (to.path === "/settings/whitelabel" && !router.hasRoute("whitelabel")) {
        router.addRoute("settings", {
          path: "/settings/whitelabel",
          name: "whitelabel",
          component: WhiteLabel,
          title: window.zb.i18n.translate("white-label")
        });
        return to.fullPath;
      } else
        return true;
    });
    router.afterEach((to, from) => {
      if (from.path === "/settings/whitelabel") {
        router.removeRoute("whitelabel");
      }
    });
    return router;
  }
  window.addEventListener("zionbuilder/admin/init", function({ detail: Api }) {
    Api.interceptors.errorInterceptor(Api.notifications, ZionService);
    window.zb.hooks.addFilter("zionbuilder/router", getRouter);
    new Admin(Api);
  });
})(zb.vue);
