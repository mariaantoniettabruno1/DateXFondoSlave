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
    name: "zion_heading",
    props: ["options", "element", "api"],
    computed: {
      hasLink() {
        return this.options.link && this.options.link.link;
      }
    }
  };
  const _hoisted_1 = ["href", "title", "target"];
  function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_RenderValue = vue.resolveComponent("RenderValue");
    return vue.openBlock(), vue.createBlock(vue.resolveDynamicComponent($props.options.tag || "h1"), null, {
      default: vue.withCtx(() => [
        vue.renderSlot(_ctx.$slots, "start"),
        $options.hasLink ? (vue.openBlock(), vue.createElementBlock("a", {
          key: 0,
          onClick: _cache[0] || (_cache[0] = vue.withModifiers((e) => {
            e.preventDefault();
          }, ["prevent"])),
          href: $props.options.link.link,
          title: $props.options.link.title,
          target: $props.options.link.target
        }, [
          vue.createVNode(_component_RenderValue, {
            option: "content",
            "forced-root-node": false
          })
        ], 8, _hoisted_1)) : (vue.openBlock(), vue.createBlock(_component_RenderValue, {
          key: 1,
          option: "content",
          "forced-root-node": false
        })),
        vue.renderSlot(_ctx.$slots, "end")
      ]),
      _: 3
    });
  }
  var Heading = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render]]);
  window.zb.editor.registerElementComponent({
    elementType: "zion_heading",
    component: Heading
  });
})(zb.vue);
