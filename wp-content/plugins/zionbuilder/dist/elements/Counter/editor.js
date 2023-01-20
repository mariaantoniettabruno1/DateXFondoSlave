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
    name: "Counter",
    props: ["options", "element", "api"],
    setup(props) {
      const root = vue.ref(null);
      vue.onMounted(() => {
        runScript();
      });
      vue.watch(
        () => [props.options.start, props.options.end, props.options.duration].toString(),
        (newValue, oldValue) => {
          runScript();
        }
      );
      function runScript() {
        new window.zbScripts.counter(root.value);
      }
      return {
        root
      };
    }
  };
  const _hoisted_1 = {
    ref: "root",
    class: "zb-el-counter"
  };
  const _hoisted_2 = { class: "zb-el-counter__number" };
  function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
    return vue.openBlock(), vue.createElementBlock("div", _hoisted_1, [
      vue.renderSlot(_ctx.$slots, "start"),
      $props.options.before ? (vue.openBlock(), vue.createElementBlock("div", vue.mergeProps({
        key: 0,
        class: "zb-el-counter__before"
      }, $props.api.getAttributesForTag("before_text_styles"), {
        class: $props.api.getStyleClasses("before_text_styles")
      }), vue.toDisplayString($props.options.before), 17)) : vue.createCommentVNode("", true),
      vue.createElementVNode("div", _hoisted_2, vue.toDisplayString($props.options.start), 1),
      $props.options.after ? (vue.openBlock(), vue.createElementBlock("div", vue.mergeProps({
        key: 1,
        class: "zb-el-counter__after"
      }, $props.api.getAttributesForTag("after_text_styles"), {
        class: $props.api.getStyleClasses("after_text_styles")
      }), vue.toDisplayString($props.options.after), 17)) : vue.createCommentVNode("", true),
      vue.renderSlot(_ctx.$slots, "end")
    ], 512);
  }
  var Counter = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render]]);
  window.zb.editor.registerElementComponent({
    elementType: "counter",
    component: Counter
  });
})(zb.vue);
