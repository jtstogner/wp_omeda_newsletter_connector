wp.domReady(() => {
  if (nglue_backend.is_allowed_post_type) {
    if (nglue_backend.core_post_type === 'no') {
      wp.blocks.setDefaultBlockName('newsletterglue/text');
    }
  }
});

window.onload = function () {
  setTimeout(function () {
    if (!wp.data.select('core/editor').isEditorPanelOpened('ng-newsletter-plugin/ng-newsletter-panel')) {
      wp.data.dispatch('core/editor').toggleEditorPanelOpened('ng-newsletter-plugin/ng-newsletter-panel');
    }
    if (!wp.data.select('core/editor').isEditorPanelOpened('ng-settings-plugin/ng-settings-panel')) {
      wp.data.dispatch('core/editor').toggleEditorPanelOpened('ng-settings-plugin/ng-settings-panel');
    }
  }, 1000);
}