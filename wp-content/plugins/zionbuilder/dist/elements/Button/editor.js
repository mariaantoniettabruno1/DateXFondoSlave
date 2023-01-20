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
    name: "zion_button",
    props: ["options", "api", "element"],
    computed: {
      iconConfig() {
        return this.options.icon;
      },
      getTag() {
        return this.options.link && this.options.link.link ? "a" : "div";
      },
      getButtonAttributes() {
        const attrs = {};
        if (this.options.link && this.options.link.link) {
          attrs.href = this.options.link.link;
          attrs.target = this.options.link.target;
          attrs.title = this.options.link.title;
        }
        return attrs;
      }
    }
  };
  const _hoisted_1 = {
    key: 1,
    class: "zb-el-button__text"
  };
  function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_ElementIcon = vue.resolveComponent("ElementIcon");
    return vue.openBlock(), vue.createElementBlock("div", null, [
      vue.renderSlot(_ctx.$slots, "start"),
      (vue.openBlock(), vue.createBlock(vue.resolveDynamicComponent($options.getTag), vue.mergeProps($props.api.getAttributesForTag("button_styles", $options.getButtonAttributes), {
        ref: "button",
        class: ["zb-el-button", [
          $props.api.getStyleClasses("button_styles"),
          { "zb-el-button--has-icon": $props.options.icon }
        ]]
      }), {
        default: vue.withCtx(() => [
          $props.options.icon ? (vue.openBlock(), vue.createBlock(_component_ElementIcon, vue.mergeProps({
            key: 0,
            class: "zb-el-button__icon"
          }, $props.api.getAttributesForTag("icon_styles"), {
            iconConfig: $options.iconConfig,
            class: $props.api.getStyleClasses("icon_styles")
          }), null, 16, ["iconConfig", "class"])) : vue.createCommentVNode("", true),
          $props.options.button_text ? (vue.openBlock(), vue.createElementBlock("span", _hoisted_1, vue.toDisplayString($props.options.button_text), 1)) : vue.createCommentVNode("", true)
        ]),
        _: 1
      }, 16, ["class"])),
      vue.renderSlot(_ctx.$slots, "end")
    ]);
  }
  var Button = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render]]);
  window.zb.editor.registerElementComponent({
    elementType: "zion_button",
    component: Button
  });
})(zb.vue);
