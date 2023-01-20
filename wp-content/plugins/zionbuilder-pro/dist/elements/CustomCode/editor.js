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
    name: "CustomHtml",
    props: ["options", "element", "api"],
    setup(props) {
      const phpMarkup = vue.ref("");
      const phpError = vue.ref("");
      const content = vue.computed(() => {
        return props.options.content + phpMarkup.value;
      });
      function onApplyPHPCode() {
        if (props.options.php && props.options.php.length > 0) {
          window.zb.editor.serverRequest.request(
            {
              type: "parse_php",
              config: props.options.php
            },
            (response) => {
              if (response && response.error) {
                phpError.value = response.message;
                phpMarkup.value = "";
              } else {
                phpMarkup.value = response;
                phpError.value = "";
              }
            },
            function(message) {
              console.log("server Request fail", message);
            }
          );
        }
      }
      props.element.on("apply_php_code", onApplyPHPCode);
      vue.onMounted(onApplyPHPCode);
      vue.onBeforeUnmount(() => props.element.off("apply_php_code", onApplyPHPCode));
      return {
        content,
        phpError
      };
    }
  };
  const _hoisted_1 = ["innerHTML"];
  const _hoisted_2 = ["innerHTML"];
  function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
    return vue.openBlock(), vue.createElementBlock("div", null, [
      vue.renderSlot(_ctx.$slots, "start"),
      vue.createElementVNode("div", { innerHTML: $setup.content }, null, 8, _hoisted_1),
      $setup.phpError.length > 0 ? (vue.openBlock(), vue.createElementBlock("div", {
        key: 0,
        class: "znpb-notice znpb-notice--error",
        innerHTML: $setup.phpError
      }, null, 8, _hoisted_2)) : vue.createCommentVNode("", true),
      vue.renderSlot(_ctx.$slots, "end")
    ]);
  }
  var customCode = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render]]);
  window.zb.editor.registerElementComponent({
    elementType: "custom_html",
    component: customCode
  });
})(zb.vue);
