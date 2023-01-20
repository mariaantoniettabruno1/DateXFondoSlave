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
    name: "ZionColumn",
    props: ["options", "api", "element"],
    computed: {
      htmlTag() {
        if (this.options.link && this.options.link.link) {
          return "a";
        }
        return /^[a-z0-9]+$/i.test(this.options.tag) ? this.options.tag : "div";
      },
      extraAttributes() {
        return window.zb.utils.getLinkAttributes(this.options.link);
      }
    }
  };
  function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_SortableContent = vue.resolveComponent("SortableContent");
    return vue.openBlock(), vue.createBlock(_component_SortableContent, vue.mergeProps({
      element: $props.element,
      tag: $options.htmlTag
    }, $options.extraAttributes), {
      start: vue.withCtx(() => [
        vue.renderSlot(_ctx.$slots, "start")
      ]),
      end: vue.withCtx(() => [
        vue.renderSlot(_ctx.$slots, "end")
      ]),
      _: 3
    }, 16, ["element", "tag"]);
  }
  var Container = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render]]);
  window.zb.editor.registerElementComponent({
    elementType: "container",
    component: Container
  });
})(zb.vue);
