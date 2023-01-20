(function(vue) {
  "use strict";
  var _export_sfc = (sfc, props) => {
    const target = sfc.__vccOpts || sfc;
    for (const [key, val] of props) {
      target[key] = val;
    }
    return target;
  };
  const _sfc_main = {};
  function _sfc_render(_ctx, _cache) {
    const _component_SortableContent = vue.resolveComponent("SortableContent");
    return vue.openBlock(), vue.createBlock(_component_SortableContent, {
      class: "checkout woocommerce-checkout",
      element: _ctx.element,
      tag: "form",
      style: { "width": "100%" }
    }, {
      start: vue.withCtx(() => [
        vue.renderSlot(_ctx.$slots, "start")
      ]),
      end: vue.withCtx(() => [
        vue.renderSlot(_ctx.$slots, "end")
      ]),
      _: 3
    }, 8, ["element"]);
  }
  var CheckoutFormWrapperVue = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render]]);
  window.zb.editor.registerElementComponent({
    elementType: "woo-checkout-form-wrapper",
    component: CheckoutFormWrapperVue
  });
})(zb.vue);
