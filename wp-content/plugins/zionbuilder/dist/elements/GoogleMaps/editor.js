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
    name: "google_maps",
    props: ["options", "element", "api"],
    computed: {
      location() {
        return encodeURIComponent(this.options.location || "Chicago");
      },
      zoom() {
        return this.options.zoom || 15;
      },
      mapType() {
        return this.options.map_type === "terrain" ? "k" : "";
      }
    }
  };
  const _hoisted_1 = ["src"];
  function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
    return vue.openBlock(), vue.createElementBlock("div", null, [
      vue.renderSlot(_ctx.$slots, "start"),
      vue.createElementVNode("iframe", {
        src: `https://www.google.com/maps?api=1&q=${$options.location}&z=${$options.zoom}&output=embed&t=${$options.mapType}`,
        frameborder: "0",
        style: { "border": "0", "margin-bottom": "0" },
        allowfullscreen: ""
      }, null, 8, _hoisted_1),
      vue.renderSlot(_ctx.$slots, "end")
    ]);
  }
  var googleMaps = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render]]);
  window.zb.editor.registerElementComponent({
    elementType: "google_maps",
    component: googleMaps
  });
})(zb.vue);
