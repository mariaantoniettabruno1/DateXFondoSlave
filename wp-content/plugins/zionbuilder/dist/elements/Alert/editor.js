(function(vue) {
  "use strict";
  var Alert_vue_vue_type_style_index_0_lang = "";
  var _export_sfc = (sfc, props) => {
    const target = sfc.__vccOpts || sfc;
    for (const [key, val] of props) {
      target[key] = val;
    }
    return target;
  };
  const _sfc_main = {
    name: "Alert",
    props: ["options", "element", "api"]
  };
  const _hoisted_1 = {
    key: 0,
    class: "zb-el-alert__closeIcon"
  };
  const _hoisted_2 = ["innerHTML"];
  function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_RenderValue = vue.resolveComponent("RenderValue");
    return vue.openBlock(), vue.createElementBlock("div", null, [
      vue.renderSlot(_ctx.$slots, "start"),
      $props.options.show_dismiss ? (vue.openBlock(), vue.createElementBlock("span", _hoisted_1)) : vue.createCommentVNode("", true),
      $props.options.title ? (vue.openBlock(), vue.createElementBlock("span", {
        key: 1,
        class: "zb-el-alert__title",
        innerHTML: $props.options.title
      }, null, 8, _hoisted_2)) : vue.createCommentVNode("", true),
      $props.options.description ? (vue.openBlock(), vue.createBlock(_component_RenderValue, {
        key: 2,
        "html-tag": "div",
        option: "description",
        class: "zb-el-alert__description"
      })) : vue.createCommentVNode("", true),
      vue.renderSlot(_ctx.$slots, "end")
    ]);
  }
  var Alert = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render]]);
  window.zb.editor.registerElementComponent({
    elementType: "alert",
    component: Alert
  });
})(zb.vue);
