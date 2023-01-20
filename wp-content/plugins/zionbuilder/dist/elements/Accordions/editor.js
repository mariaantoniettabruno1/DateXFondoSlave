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
  const _sfc_main$1 = {
    name: "AccordionItem",
    props: ["options", "element", "api"],
    setup(props) {
      let renderedContent = vue.computed(() => {
        return props.options.content ? props.options.content : "accordion content";
      });
      let activeByDefault = vue.computed(() => {
        return props.options.active_by_default ? props.options.active_by_default : false;
      });
      const accordionApi = vue.inject("accordionsApi");
      const titleTag = vue.computed(() => {
        return props.options.title_tag || accordionApi.options.value.title_tag || "div";
      });
      return {
        titleTag,
        renderedContent,
        activeByDefault,
        accordionApi
      };
    }
  };
  const _hoisted_1 = /* @__PURE__ */ vue.createElementVNode("span", { class: "zb-el-accordions-accordionIcon" }, null, -1);
  const _hoisted_2 = ["innerHTML"];
  function _sfc_render$1(_ctx, _cache, $props, $setup, $data, $options) {
    return vue.openBlock(), vue.createElementBlock("div", {
      class: vue.normalizeClass(["zb-el-accordions-accordionWrapper", { "zb-el-accordions--active": $setup.activeByDefault }])
    }, [
      vue.renderSlot(_ctx.$slots, "start"),
      (vue.openBlock(), vue.createBlock(vue.resolveDynamicComponent($setup.titleTag), vue.mergeProps({
        class: ["zb-el-accordions-accordionTitle", $setup.accordionApi.getStyleClasses("inner_content_styles_title")]
      }, $setup.accordionApi.getAttributesForTag("inner_content_styles_title")), {
        default: vue.withCtx(() => [
          vue.createTextVNode(vue.toDisplayString($props.options.title) + " ", 1),
          _hoisted_1
        ]),
        _: 1
      }, 16, ["class"])),
      vue.createElementVNode("div", vue.mergeProps({
        class: ["zb-el-accordions-accordionContent", $setup.accordionApi.getStyleClasses("inner_content_styles_content")]
      }, $setup.accordionApi.getAttributesForTag("inner_content_styles_content")), [
        vue.createElementVNode("div", {
          class: "zb-el-accordions-accordionContent__inner",
          innerHTML: $setup.renderedContent
        }, null, 8, _hoisted_2)
      ], 16),
      vue.renderSlot(_ctx.$slots, "end")
    ], 2);
  }
  var accordionItem = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["render", _sfc_render$1]]);
  const _sfc_main = {
    name: "Accordions",
    props: ["options", "element", "api"],
    setup(props) {
      if (props.element.content.length === 0 && props.options.items) {
        props.element.addChildren(props.options.items);
      }
      const computedOptions = vue.computed(() => props.options);
      vue.provide("accordionsApi", __spreadProps(__spreadValues({}, props.api), {
        options: computedOptions
      }));
    }
  };
  function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_SortableContent = vue.resolveComponent("SortableContent");
    return vue.openBlock(), vue.createBlock(_component_SortableContent, { element: $props.element }, {
      start: vue.withCtx(() => [
        vue.renderSlot(_ctx.$slots, "start")
      ]),
      end: vue.withCtx(() => [
        vue.renderSlot(_ctx.$slots, "end")
      ]),
      _: 3
    }, 8, ["element"]);
  }
  var Accordions = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render]]);
  window.zb.editor.registerElementComponent({
    elementType: "accordions",
    component: Accordions
  });
  window.zb.editor.registerElementComponent({
    elementType: "accordion_item",
    component: accordionItem
  });
})(zb.vue);
