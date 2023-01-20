var __defProp = Object.defineProperty;
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
(function() {
  "use strict";
  const createI18n = (initialStrings = {}) => {
    let strings = {};
    const addStrings2 = (newStrings) => {
      strings = __spreadValues(__spreadValues({}, strings), newStrings);
    };
    const translate2 = (stringId) => {
      if (strings[stringId] !== void 0) {
        return strings[stringId];
      }
      console.error(`String with id ${stringId} was not found.`);
      return "";
    };
    if (initialStrings) {
      addStrings2(initialStrings);
    }
    return {
      addStrings: addStrings2,
      translate: translate2
    };
  };
  const i18n = createI18n();
  const install = (app, strings) => {
    i18n.addStrings(strings);
    app.config.globalProperties.$translate = (string) => {
      return i18n.translate(string);
    };
  };
  const { addStrings, translate } = i18n;
  window.zb = window.zb || {};
  window.zb.i18n = {
    install,
    addStrings,
    translate
  };
})();
