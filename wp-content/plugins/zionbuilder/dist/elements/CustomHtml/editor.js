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
    name: "custom_html",
    props: ["options", "element", "api"]
  };
  const _hoisted_1 = ["innerHTML"];
  function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
    return vue.openBlock(), vue.createElementBlock("div", null, [
      vue.renderSlot(_ctx.$slots, "start"),
      vue.createElementVNode("div", {
        innerHTML: $props.options.content
      }, null, 8, _hoisted_1),
      vue.renderSlot(_ctx.$slots, "end")
    ]);
  }
  var customHtml = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render]]);
  window.zb.editor.registerElementComponent({
    elementType: "custom_html",
    component: customHtml
  });
})(zb.vue);
