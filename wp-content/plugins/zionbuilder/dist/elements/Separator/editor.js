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
    name: "zion_separator",
    props: ["options", "element", "api"],
    computed: {
      iconConfig() {
        return this.options.icon || {
          "family": "Font Awesome 5 Free Regular",
          "name": "star",
          "unicode": "uf005"
        };
      }
    }
  };
  const _hoisted_1 = {
    key: 1,
    class: "zb-el-zionSeparator-item-icon zb-el-zionSeparator-item--size"
  };
  const _hoisted_2 = /* @__PURE__ */ vue.createElementVNode("span", { class: "zb-el-zionSeparator-item zb-el-zionSeparator-icon-line zb-el-zionSeparator-icon-line-one" }, null, -1);
  const _hoisted_3 = /* @__PURE__ */ vue.createElementVNode("span", { class: "zb-el-zionSeparator-item zb-el-zionSeparator-icon-line zb-el-zionSeparator-icon-line-two" }, null, -1);
  function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_ElementIcon = vue.resolveComponent("ElementIcon");
    return vue.openBlock(), vue.createElementBlock("div", null, [
      vue.renderSlot(_ctx.$slots, "start"),
      !$props.options.use_icon ? (vue.openBlock(), vue.createElementBlock("div", vue.mergeProps({
        key: 0,
        class: "zb-el-zionSeparator-item zb-el-zionSeparator-item--size"
      }, $props.api.getAttributesForTag("separator_item")), null, 16)) : (vue.openBlock(), vue.createElementBlock("div", _hoisted_1, [
        _hoisted_2,
        vue.createVNode(_component_ElementIcon, {
          class: "zb-el-zionSeparator-icon",
          iconConfig: $options.iconConfig
        }, null, 8, ["iconConfig"]),
        _hoisted_3
      ])),
      vue.renderSlot(_ctx.$slots, "end")
    ]);
  }
  var Separator = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render]]);
  window.zb.editor.registerElementComponent({
    elementType: "zion_separator",
    component: Separator
  });
})(zb.vue);
