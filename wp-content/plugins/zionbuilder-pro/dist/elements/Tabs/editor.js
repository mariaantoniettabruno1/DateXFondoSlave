(function(vue) {
  "use strict";
  var TabsItem_vue_vue_type_style_index_0_lang = "";
  var _export_sfc = (sfc, props) => {
    const target = sfc.__vccOpts || sfc;
    for (const [key, val] of props) {
      target[key] = val;
    }
    return target;
  };
  const _sfc_main = {
    name: "TabsItem",
    props: ["options", "element", "api"],
    setup(props) {
      if (props.element.content && props.element.content.length === 0) {
        props.element.addChild({
          element_type: "zion_text"
        });
      }
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
  var TabsItem = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render]]);
  window.zb.editor.registerElementComponent({
    elementType: "tabs_item",
    component: TabsItem
  });
})(zb.vue);
