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
    name: "ZionText",
    props: ["options", "element", "api"]
  };
  function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_RenderValue = vue.resolveComponent("RenderValue");
    return vue.openBlock(), vue.createElementBlock("div", null, [
      vue.renderSlot(_ctx.$slots, "start"),
      vue.createVNode(_component_RenderValue, { option: "content" }),
      vue.renderSlot(_ctx.$slots, "end")
    ]);
  }
  var Text = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render]]);
  window.zb.editor.registerElementComponent({
    elementType: "zion_text",
    component: Text
  });
})(zb.vue);
