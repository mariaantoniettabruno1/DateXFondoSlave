(function(vue) {
  "use strict";
  var Modal_vue_vue_type_style_index_0_lang = "";
  var _export_sfc = (sfc, props) => {
    const target = sfc.__vccOpts || sfc;
    for (const [key, val] of props) {
      target[key] = val;
    }
    return target;
  };
  const _sfc_main = {
    name: "ModalElement",
    props: ["options", "api", "element"],
    setup(props) {
      const root = vue.ref(null);
      const inlineClass = vue.computed(() => {
        if (props.options.modal_state) {
          switch (props.options.modal_state) {
            case "open":
              return "zb-modal--open";
            case "inline":
              return "zb-modal--inline";
            default:
              return "zb-modal--inline";
          }
        }
        return "zb-modal--inline";
      });
      return {
        inlineClass,
        root
      };
    }
  };
  function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_SortableContent = vue.resolveComponent("SortableContent");
    return vue.openBlock(), vue.createElementBlock("div", {
      ref: "root",
      class: vue.normalizeClass(["zb-modal", $setup.inlineClass])
    }, [
      vue.renderSlot(_ctx.$slots, "start"),
      vue.createVNode(_component_SortableContent, vue.mergeProps({
        element: $props.element,
        class: "zb-modalContent"
      }, $props.api.getAttributesForTag("modal_content")), {
        end: vue.withCtx(() => [
          vue.createElementVNode("div", vue.mergeProps({ class: "zb-modalClose" }, $props.api.getAttributesForTag("close_button")), null, 16)
        ]),
        _: 1
      }, 16, ["element"]),
      vue.renderSlot(_ctx.$slots, "end")
    ], 2);
  }
  var Modal = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render]]);
  window.zb.editor.registerElementComponent({
    elementType: "modal",
    component: Modal
  });
})(zb.vue);
