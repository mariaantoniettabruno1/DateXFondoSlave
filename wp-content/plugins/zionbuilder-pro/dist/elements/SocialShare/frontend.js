(function() {
  "use strict";
  var main = "";
  document.addEventListener("click", function(event) {
    if (!event.target) {
      return;
    }
    const domNode = event.target.closest(".zb-el-socialShare__item");
    if (domNode) {
      event.preventDefault();
      let linkTarget = domNode.href;
      let params = "toolbar=0,location=0,menubar=0, width=800,height=600";
      window.open(linkTarget, "_blank", params);
    }
  });
})();
