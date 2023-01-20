(function() {
  "use strict";
  var editPage = "";
  const $ = window.jQuery;
  const wp = window.wp;
  class EditPage {
    constructor(args) {
      this.isEditorEnabled = args.is_editor_enabled;
      this.postId = args.post_id;
      this.l10n = args.l10n;
      this.isProcessingAction = false;
      this.cacheDom();
      this.attachEvents();
      this.$document.on("heartbeat-error", this.onHearBeatError.bind(this));
      this.$document.on("heartbeat-tick.autosave", this.onHearBeatReceived.bind(this));
    }
    cacheDom() {
      this.$document = $(document);
      this.$window = $(window);
      this.$body = $("body");
      this.$postTitle = $("#title");
      this.$editorActivateButton = $(".znpb-admin-post__edit-button--activate");
      this.$editorDeactivateButton = $(".znpb-admin-post__edit-button--deactivate");
      this.$buttonsWrapper = $(".znpb-admin-post__edit-buttons-wrapper");
    }
    attachEvents() {
      this.$editorActivateButton.on("click", this.onEditButtonPress.bind(this));
      this.$editorDeactivateButton.on("click", this.onDisableButtonPress.bind(this));
    }
    getTranslatedString(stringId) {
      if (typeof this.l10n[stringId] !== "undefined") {
        return this.l10n[stringId];
      }
    }
    onHearBeatError() {
      this.isProcessingAction = false;
      this.$editorActivateButton.removeClass("znpb-admin-post__edit-button--loading");
      this.$editorDeactivateButton.removeClass("znpb-admin-post__edit-button--loading");
    }
    onHearBeatReceived(event, data) {
      if (typeof data.zion_builder_status !== "undefined") {
        this.setEditorStatus(data.zion_builder_status);
        if (this.isEditorEnabled) {
          this.$window.off("beforeunload.edit-post");
          window.history.replaceState({ id: this.postId }, "Post " + this.postId, this.getPostEditURL());
          location.href = this.$editorActivateButton.attr("href");
        }
      }
      this.isProcessingAction = false;
      this.$editorActivateButton.removeClass("znpb-admin-post__edit-button--loading");
      this.$editorDeactivateButton.removeClass("znpb-admin-post__edit-button--loading");
    }
    getPostEditURL() {
      return `post.php?post=${this.postId}&action=edit`;
    }
    setEditorStatus(status) {
      this.isEditorEnabled = status;
      this.updateUi(status);
    }
    updateUi(status) {
      if (status) {
        this.$body.addClass("znpb-admin-post-editor--active");
      } else {
        this.$body.removeClass("znpb-admin-post-editor--active");
      }
    }
    onEditButtonPress(event) {
      if (!this.isEditorEnabled) {
        event.preventDefault();
        if (!this.$postTitle.val()) {
          this.$postTitle.val(`ZionBuilder #${this.postId}`).trigger("input");
        }
        if (wp.autosave) {
          this.$window.off("beforeunload.edit-post");
          this.$editorActivateButton.addClass("znpb-admin-post__edit-button--loading");
          this.saveEditorStatus(true);
        } else {
          alert(this.getTranslatedString("wp_heartbeat_disabled"));
        }
      }
    }
    saveEditorStatus(status) {
      if (this.isProcessingAction) {
        return;
      }
      const postId = this.postId;
      this.isProcessingAction = true;
      $(document).on("heartbeat-send.autosave", function(event, data) {
        data.zion_builder_status = status;
        data.zion_builder_post_id = postId;
      });
      wp.autosave.server.triggerSave();
    }
    onDisableButtonPress(event) {
      event.preventDefault();
      if (this.isEditorEnabled) {
        this.$editorDeactivateButton.addClass("znpb-admin-post__edit-button--loading");
        this.saveEditorStatus(false);
      }
    }
  }
  new EditPage(window.ZnPbEditPostData.data);
})();
