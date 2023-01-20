(function() {
  "use strict";
  class ZionBuilderIntegration {
    constructor() {
      if (typeof window.YoastSEO === "undefined" || typeof window.YoastSEO.analysis === "undefined" || typeof window.YoastSEO.analysis.worker === "undefined") {
        return;
      }
      window.YoastSEO.app.registerPlugin("ZionBuilderIntegration", { status: "ready" });
      this.registerModifications();
    }
    registerModifications() {
      const callback = this.addContent.bind(this);
      window.YoastSEO.app.registerModification("content", callback, "ZionBuilderIntegration", 10);
    }
    addContent(data) {
      const { is_editor_enabled = false } = window.ZnPbEditPostData ? window.ZnPbEditPostData.data : {};
      if (is_editor_enabled && window.zb_yoast_data && window.zb_yoast_data.page_content) {
        data += window.zb_yoast_data.page_content;
      }
      return data;
    }
  }
  if (typeof window.YoastSEO !== "undefined" && typeof window.YoastSEO.app !== "undefined") {
    new ZionBuilderIntegration();
  } else {
    window.jQuery(window).on("YoastSEO:ready", function() {
      new ZionBuilderIntegration();
    });
  }
})();
