(function(vue) {
  "use strict";
  var _export_sfc = (sfc, props) => {
    const target = sfc.__vccOpts || sfc;
    for (const [key, val] of props) {
      target[key] = val;
    }
    return target;
  };
  const _sfc_main$2 = {
    name: "Tablink",
    props: ["title", "active"]
  };
  function _sfc_render$2(_ctx, _cache, $props, $setup, $data, $options) {
    return vue.openBlock(), vue.createElementBlock("li", {
      class: vue.normalizeClass(["zb-el-tabs-nav-title", { "zb-el-tabs-nav--active": $props.active }])
    }, vue.toDisplayString($props.title), 3);
  }
  var TabLink = /* @__PURE__ */ _export_sfc(_sfc_main$2, [["render", _sfc_render$2]]);
  const _sfc_main$1 = {
    name: "Tabs",
    components: {
      TabLink
    },
    props: ["options", "element", "api"],
    setup(props) {
      const activeTab = vue.ref(null);
      if (props.element.content.length === 0 && props.options.tabs) {
        props.element.addChildren(props.options.tabs);
      }
      const children = vue.computed(() => {
        return props.element.content.map((childUID) => {
          const contentStore = window.zb.editor.useContentStore();
          return contentStore.getElement(childUID);
        });
      });
      const tabs = vue.computed(() => {
        return props.element.content.map((childUID) => {
          const contentStore = window.zb.editor.useContentStore();
          const element = contentStore.getElement(childUID);
          return {
            title: element.options.title,
            uid: element.uid
          };
        });
      });
      return {
        tabs,
        activeTab,
        children
      };
    }
  };
  const _hoisted_1$1 = { class: "zb-el-tabs-nav" };
  function _sfc_render$1(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_TabLink = vue.resolveComponent("TabLink");
    const _component_Element = vue.resolveComponent("Element");
    return vue.openBlock(), vue.createElementBlock("div", null, [
      vue.renderSlot(_ctx.$slots, "start"),
      vue.createElementVNode("ul", _hoisted_1$1, [
        (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList($setup.tabs, (tab, i) => {
          return vue.openBlock(), vue.createBlock(_component_TabLink, vue.mergeProps({
            key: tab.uid,
            title: tab.title,
            active: $setup.activeTab ? tab.uid === $setup.activeTab : i === 0
          }, $props.api.getAttributesForTag("inner_content_styles_title"), {
            class: $props.api.getStyleClasses("inner_content_styles_title"),
            onClick: ($event) => $setup.activeTab = tab.uid
          }), null, 16, ["title", "active", "class", "onClick"]);
        }), 128))
      ]),
      vue.createElementVNode("div", vue.mergeProps({ class: "zb-el-tabs-content" }, $props.api.getAttributesForTag("inner_content_styles_content"), {
        class: $props.api.getStyleClasses("inner_content_styles_content")
      }), [
        (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList($setup.children, (childElement, i) => {
          return vue.openBlock(), vue.createBlock(_component_Element, {
            key: childElement.uid,
            element: childElement,
            class: vue.normalizeClass({ "zb-el-tabs-nav--active": $setup.activeTab ? childElement.uid === $setup.activeTab : i === 0 })
          }, null, 8, ["element", "class"]);
        }), 128))
      ], 16),
      vue.renderSlot(_ctx.$slots, "end")
    ]);
  }
  var Tabs = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["render", _sfc_render$1]]);
  const _sfc_main = {
    name: "TabsItem",
    props: ["options", "element", "api"]
  };
  const _hoisted_1 = ["innerHTML"];
  function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
    return vue.openBlock(), vue.createElementBlock("div", null, [
      vue.renderSlot(_ctx.$slots, "start"),
      vue.createElementVNode("div", {
        innerHTML: $props.options.content
      }, null, 8, _hoisted_1),
      vue.renderSlot(_ctx.$slots, "end")
    ]);
  }
  var TabsItem = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render]]);
  window.zb.editor.registerElementComponent({
    elementType: "tabs_item",
    component: TabsItem
  });
  window.zb.editor.registerElementComponent({
    elementType: "tabs",
    component: Tabs
  });
})(zb.vue);
