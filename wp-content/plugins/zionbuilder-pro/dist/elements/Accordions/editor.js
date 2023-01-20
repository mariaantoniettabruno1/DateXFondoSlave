(function(vue) {
  "use strict";
  var accordionItem_vue_vue_type_style_index_0_lang = "";
  var _export_sfc = (sfc, props) => {
    const target = sfc.__vccOpts || sfc;
    for (const [key, val] of props) {
      target[key] = val;
    }
    return target;
  };
  const _sfc_main = {
    name: "AccordionItem",
    props: ["options", "api", "element"],
    setup(props) {
      const accordionApi = vue.inject("accordionsApi");
      const titleTag = vue.computed(() => {
        return props.options.title_tag || accordionApi.options.value.title_tag || "div";
      });
      if (props.element.content.length === 0) {
        props.element.addChild({
          element_type: "zion_text"
        });
      }
      return {
        accordionApi,
        titleTag
      };
    }
  };
  const _hoisted_1 = /* @__PURE__ */ vue.createElementVNode("span", { class: "zb-el-accordions-accordionIcon" }, null, -1);
  function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_SortableContent = vue.resolveComponent("SortableContent");
    return vue.openBlock(), vue.createElementBlock("div", {
      class: vue.normalizeClass(["zb-el-accordions-accordionWrapper", { "zb-el-accordions--active": $props.options.active_by_default }])
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
        vue.createVNode(_component_SortableContent, {
          element: $props.element,
          class: "zb-el-accordions-accordionContent__inner"
        }, {
          start: vue.withCtx(() => [
            vue.renderSlot(_ctx.$slots, "start")
          ]),
          end: vue.withCtx(() => [
            vue.renderSlot(_ctx.$slots, "end")
          ]),
          _: 3
        }, 8, ["element"])
      ], 16),
      vue.renderSlot(_ctx.$slots, "end")
    ], 2);
  }
  var accordionItem = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render]]);
  window.zb.editor.registerElementComponent({
    elementType: "accordion_item",
    component: accordionItem
  });
})(zb.vue);
