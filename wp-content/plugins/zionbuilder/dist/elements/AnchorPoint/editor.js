(function(vue) {
  "use strict";
  var anchorPoint_vue_vue_type_style_index_0_lang = "";
  var _export_sfc = (sfc, props) => {
    const target = sfc.__vccOpts || sfc;
    for (const [key, val] of props) {
      target[key] = val;
    }
    return target;
  };
  const _sfc_main = {
    name: "anchor_point",
    props: ["options", "element", "api"],
    computed: {
      getCssID() {
        return (this.options._advanced_options || {})._element_id || this.element.uid;
      }
    }
  };
  const _hoisted_1 = { class: "zb-anchorPoint" };
  const _hoisted_2 = ["innerHTML"];
  function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_Icon = vue.resolveComponent("Icon");
    return vue.openBlock(), vue.createElementBlock("div", _hoisted_1, [
      vue.renderSlot(_ctx.$slots, "start"),
      vue.createElementVNode("span", {
        innerHTML: `#${$options.getCssID}`
      }, null, 8, _hoisted_2),
      vue.createVNode(_component_Icon, {
        icon: "element-anchor-point",
        size: 30,
        color: "#B2B2B2"
      }),
      vue.renderSlot(_ctx.$slots, "end")
    ]);
  }
  var anchorPoint = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render]]);
  window.zb.editor.registerElementComponent({
    elementType: "anchor_point",
    component: anchorPoint
  });
})(zb.vue);
