(function() {
  "use strict";
  var editor = "";
  window.zb.hooks.addAction("zionbuilder/server_component/rendered", function(html, element, options) {
    if (element.element_type === "menu") {
      window.zbScripts.menu(html);
    }
  });
})();
