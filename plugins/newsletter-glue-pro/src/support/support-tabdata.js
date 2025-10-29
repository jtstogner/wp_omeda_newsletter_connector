import { __ } from '@wordpress/i18n';

export const tabdata = {
  formatting: {
    smallcase: 'email formatting',
    support_title: __('Your email formatting issue', 'newsletter-glue'),
    questions: [
      { name: 'How to build a newsletter template?', url: 'https://newsletterglue.com/docs/how-to-build-newsletter-templates/' },
      { name: 'Which blocks can I use to build my newsletters?', url: 'https://newsletterglue.com/docs/which-blocks-can-i-use-for-my-newsletters/' },
      { name: 'Does Newsletter Glue work with my page builder or blocks plugin?', url: 'https://newsletterglue.com/docs/newsletter-glue-doesnt-work-with-my-page-builder-or-blocks-plugin/' },
      { name: 'How to style my newsletter with CSS classes?', url: 'https://newsletterglue.com/docs/style-newsletter-with-css-classes/' },
    ],
  },
  sending: {
    smallcase: 'email sending',
    support_title: __('Your email sending issue', 'newsletter-glue'),
    questions: [
      { name: 'My email was successfully sent, but I haven’t received it. How come?', url: 'https://newsletterglue.com/docs/email-deliverability-my-email-was-successfully-sent-but-i-still-havent-received-it/' },
      { name: 'My email got stuck in drafts inside my email service provider. How come?', url: 'https://newsletterglue.com/docs/email-not-sent-i-published-my-newsletter-but-it-got-stuck-in-drafts-in-the-email-service-provider/' },
      { name: 'Why isn’t my email service connecting?', url: 'https://newsletterglue.com/docs/email-integration-my-email-service-isnt-connecting/' },
    ],
  },
  features: {
    smallcase: 'features & settings',
    support_title: __('Your features & settings issue', 'newsletter-glue'),
    questions: [
      { name: 'Learn about the newsletter editor', url: 'https://newsletterglue.com/docs/newsletter-editor-overview-of-the-newsletter-editor-custom-post-type/' },
      { name: 'Choosing your email defaults', url: 'https://newsletterglue.com/docs/email-defaults-choose-your-autofill-email-defaults/' },
      { name: 'Add a newsletter archive to your site', url: 'https://newsletterglue.com/docs/newsletter-archive-shortcodes-how-do-i-add-my-newsletter-archive-to-my-site/' },
      { name: 'How to use mergetags', url: 'https://newsletterglue.com/docs/mergetags/' },
    ],
  },
  others: {
    smallcase: 'others',
    support_title: __('What do you need help with?', 'newsletter-glue'),
    questions: [
      { name: 'Activate your license key', url: 'https://newsletterglue.com/docs/finding-and-activating-your-license-key/' },
      { name: 'How to upgrade your license', url: 'https://newsletterglue.com/docs/how-to-upgrade-license/' },
    ],
  },
};