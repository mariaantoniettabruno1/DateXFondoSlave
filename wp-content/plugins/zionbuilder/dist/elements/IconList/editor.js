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
    name: "icon_list",
    props: ["options", "element", "api"],
    computed: {
      iconListConfig() {
        return this.options.icons ? this.options.icons : [];
      }
    }
  };
  const _hoisted_1 = ["innerHTML"];
  function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_ElementIcon = vue.resolveComponent("ElementIcon");
    return vue.openBlock(), vue.createElementBlock("div", null, [
      vue.renderSlot(_ctx.$slots, "start"),
      (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList($options.iconListConfig, (item, index) => {
        return vue.openBlock(), vue.createBlock(vue.resolveDynamicComponent(item.link && item.link.link ? "a" : "span"), vue.mergeProps({
          key: index,
          class: ["zb-el-iconList__item", [`zb-el-iconList__item--${index} `, $props.api.getStyleClasses("item_styles")]]
        }, $props.api.getAttributesForTag("item_styles")), {
          default: vue.withCtx(() => [
            vue.createVNode(_component_ElementIcon, vue.mergeProps({
              class: ["zb-el-iconList__itemIcon", $props.api.getStyleClasses("icon_styles")]
            }, $props.api.getAttributesForTag("icon_styles"), {
              iconConfig: item.icon
            }), null, 16, ["class", "iconConfig"]),
            item.text ? (vue.openBlock(), vue.createElementBlock("span", vue.mergeProps({
              key: 0,
              class: ["zb-el-iconList__itemText", $props.api.getStyleClasses("text_styles")]
            }, $props.api.getAttributesForTag("text_styles"), {
              innerHTML: item.text
            }), null, 16, _hoisted_1)) : vue.createCommentVNode("", true)
          ]),
          _: 2
        }, 1040, ["class"]);
      }), 128)),
      vue.renderSlot(_ctx.$slots, "end")
    ]);
  }
  var IconList = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render]]);
  window.zb.editor.registerElementComponent({
    elementType: "icon_list",
    component: IconList
  });
})(zb.vue);
