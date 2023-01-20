(function() {
  "use strict";
  var main = "";
  document.querySelectorAll(".zb-modal").forEach((domNode) => {
    const config = domNode.dataset.zionModalConfig;
    const closeButton = domNode.querySelector(".zb-modalClose");
    const modalInstance = window.ModalJS.createModal(domNode, JSON.parse(config));
    if (closeButton) {
      closeButton.addEventListener("click", () => modalInstance.close());
    }
  });
})();
