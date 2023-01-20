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
      },
      topMask() {
        return this.shapes["top"];
      },
      bottomMask() {
        return this.shapes["bottom"];
      },
      shapes() {
        return this.options.shapes || {};
      }
    }
  };
  function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_SvgMask = vue.resolveComponent("SvgMask");
    const _component_SortableContent = vue.resolveComponent("SortableContent");
    return vue.openBlock(), vue.createBlock(_component_SortableContent, vue.mergeProps({
      class: "zb-column",
      element: $props.element,
      tag: $options.htmlTag
    }, $options.extraAttributes), {
      start: vue.withCtx(() => [
        vue.renderSlot(_ctx.$slots, "start"),
        $options.topMask !== void 0 && $options.topMask.shape ? (vue.openBlock(), vue.createBlock(_component_SvgMask, {
          key: 0,
          "shape-path": $options.topMask["shape"],
          color: $options.topMask["color"],
          flip: $options.topMask["flip"],
          position: "top"
        }, null, 8, ["shape-path", "color", "flip"])) : vue.createCommentVNode("", true),
        $options.bottomMask !== void 0 && $options.bottomMask.shape ? (vue.openBlock(), vue.createBlock(_component_SvgMask, {
          key: 1,
          "shape-path": $options.bottomMask["shape"],
          color: $options.bottomMask["color"],
          flip: $options.bottomMask["flip"],
          position: "bottom"
        }, null, 8, ["shape-path", "color", "flip"])) : vue.createCommentVNode("", true)
      ]),
      end: vue.withCtx(() => [
        vue.renderSlot(_ctx.$slots, "end")
      ]),
      _: 3
    }, 16, ["element", "tag"]);
  }
  var Column = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render]]);
  window.zb.editor.registerElementComponent({
    elementType: "zion_column",
    component: Column
  });
})(zb.vue);
