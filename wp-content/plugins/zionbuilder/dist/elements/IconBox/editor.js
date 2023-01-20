(function(vue) {
  "use strict";
  var _export_sfc = (sfc, props) => {
    const target = sfc.__vccOpts || sfc;
    for (const [key, val] of props) {
      target[key] = val;
    }
    return target;
  };
  const _sfc_main = {
    name: "icon_box",
    props: ["options", "element", "api"],
    computed: {
      titleTag() {
        return this.options.title_tag || "h3";
      }
    }
  };
  const _hoisted_1 = { class: "zb-el-iconBox" };
  const _hoisted_2 = {
    key: 0,
    class: "zb-el-iconBox-iconWrapper"
  };
  const _hoisted_3 = { class: "zb-el-iconBox-text" };
  function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_ElementIcon = vue.resolveComponent("ElementIcon");
    const _component_RenderValue = vue.resolveComponent("RenderValue");
    return vue.openBlock(), vue.createElementBlock("div", _hoisted_1, [
      vue.renderSlot(_ctx.$slots, "start"),
      $props.options.icon ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_2, [
        vue.createVNode(_component_ElementIcon, vue.mergeProps({
          class: ["zb-el-iconBox-icon", $props.api.getStyleClasses("icon_styles")],
          iconConfig: $props.options.icon
        }, $props.api.getAttributesForTag("icon_styles")), null, 16, ["class", "iconConfig"])
      ])) : vue.createCommentVNode("", true),
      vue.createElementVNode("span", vue.mergeProps({ class: "zb-el-iconBox-spacer" }, $props.api.getAttributesForTag("spacer")), null, 16),
      vue.createElementVNode("div", _hoisted_3, [
        $props.options.title ? (vue.openBlock(), vue.createBlock(vue.resolveDynamicComponent($options.titleTag), vue.mergeProps({
          key: 0,
          class: ["zb-el-iconBox-title", $props.api.getStyleClasses("title_styles")]
        }, $props.api.getAttributesForTag("title_styles"), {
          innerHTML: $props.options.title
        }), null, 16, ["class", "innerHTML"])) : vue.createCommentVNode("", true),
        $props.options.description ? (vue.openBlock(), vue.createElementBlock("div", vue.mergeProps({
          key: 1,
          class: ["zb-el-iconBox-description", $props.api.getStyleClasses("description_styles")]
        }, $props.api.getAttributesForTag("description_styles")), [
          vue.createVNode(_component_RenderValue, { option: "description" })
        ], 16)) : vue.createCommentVNode("", true)
      ]),
      vue.renderSlot(_ctx.$slots, "end")
    ]);
  }
  var iconBox = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render]]);
  window.zb.editor.registerElementComponent({
    elementType: "icon_box",
    component: iconBox
  });
})(zb.vue);
