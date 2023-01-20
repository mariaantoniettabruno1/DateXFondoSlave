(function() {
  "use strict";
  var editPage = "";
  const wp = window.wp;
  function initGutenberg(args) {
    const isGuttenbergActive = wp.data !== "undefined";
    let isEditorEnabled = args.is_editor_enabled;
    const postId = args.post_id;
    let isProcessingAction = false;
    function init() {
      if (isGuttenbergActive) {
        wp.data.subscribe(() => {
          setTimeout(() => {
            attachButtons();
          }, 100);
        });
        attachEvents();
      }
    }
    function attachButtons() {
      var _a, _b;
      const buttonWrapperMarkup = (_a = document.getElementById("zionbuilder-gutenberg-buttons")) == null ? void 0 : _a.innerHTML;
      const editorBlockFrame = (_b = document.getElementById("zionbuilder-gutenberg-editor-block")) == null ? void 0 : _b.innerHTML;
      const editorHeader = document.querySelector(".edit-post-header-toolbar");
      let editorLayout = document.querySelector(".editor-block-list__layout");
      if (!editorLayout) {
        editorLayout = document.querySelector(".block-editor-block-list__layout");
      }
      if (!editorHeader || !editorLayout || !buttonWrapperMarkup || !editorBlockFrame) {
        return;
      }
      if (!editorHeader.querySelector(".znpb-admin-post__edit") && editorHeader) {
        editorHeader.insertAdjacentHTML("beforeend", buttonWrapperMarkup);
      }
      if (!editorLayout.querySelector(".znpb-admin-post__edit-block") && editorLayout) {
        editorLayout.insertAdjacentHTML("beforeend", editorBlockFrame);
      }
      updateUi();
    }
    function attachEvents() {
      document.addEventListener("click", onEditButtonPress);
      document.addEventListener("click", onDisableButtonPress);
    }
    function onEditButtonPress(event) {
      var _a;
      const target = event.target;
      if (target && !target.classList.contains("znpb-admin-post__edit-button--activate") && !target.closest(".znpb-admin-post__edit-button--activate")) {
        return;
      }
      if (!isEditorEnabled) {
        event.preventDefault();
        if (isProcessingAction) {
          return;
        }
        isProcessingAction = true;
        (_a = document.querySelector(".znpb-admin-post__edit-button--activate")) == null ? void 0 : _a.classList.add("znpb-admin-post__edit-button--loading");
        const pageTitle = wp.data.select("core/editor").getEditedPostAttribute("title");
        if (!pageTitle || pageTitle.length === 0) {
          wp.data.dispatch("core/editor").editPost({
            title: `ZionBuilder #${postId}`
          });
        }
        wp.data.dispatch("core/editor").editPost({
          zion_builder_status: true
        });
        savePost(function() {
          var _a2;
          const editURL = (_a2 = document.querySelector(".znpb-admin-post__edit-button--activate")) == null ? void 0 : _a2.getAttribute("href");
          if (editURL) {
            location.href = editURL;
          }
        });
      }
    }
    function performActionAfterSave(callback) {
      const saveInterval = setInterval(function() {
        var _a, _b;
        if (!wp.data.select("core/editor").isSavingPost()) {
          clearInterval(saveInterval);
          if (callback) {
            callback.call(null);
          }
          setEditorStatus();
          isProcessingAction = false;
          (_a = document.querySelector(".znpb-admin-post__edit-button--activate")) == null ? void 0 : _a.classList.remove("znpb-admin-post__edit-button--loading");
          (_b = document.querySelector(".znpb-admin-post__edit-button--deactivate")) == null ? void 0 : _b.classList.remove("znpb-admin-post__edit-button--loading");
        }
      }, 300);
    }
    function updateUi() {
      if (isEditorEnabled) {
        document.body.classList.add("znpb-admin-post-editor--active");
      } else {
        document.body.classList.remove("znpb-admin-post-editor--active");
      }
    }
    function setEditorStatus() {
      isEditorEnabled = wp.data.select("core/editor").getEditedPostAttribute("zion_builder_status");
      updateUi();
    }
    function savePost(callback) {
      wp.data.dispatch("core/editor").savePost();
      performActionAfterSave(callback);
    }
    function onDisableButtonPress(event) {
      var _a;
      const target = event.target;
      if (target && !target.classList.contains("znpb-admin-post__edit-button--deactivate") && !target.closest(".znpb-admin-post__edit-button--deactivate")) {
        return;
      }
      event.preventDefault();
      if (isEditorEnabled) {
        (_a = document.querySelector(".znpb-admin-post__edit-button--deactivate")) == null ? void 0 : _a.classList.add("znpb-admin-post__edit-button--loading");
        wp.data.dispatch("core/editor").editPost({
          zion_builder_status: false
        });
        savePost();
      }
    }
    return {
      init
    };
  }
  initGutenberg(window.ZnPbEditPostData.data).init();
})();
