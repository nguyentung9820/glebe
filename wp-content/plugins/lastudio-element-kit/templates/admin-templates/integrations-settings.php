<div class="lastudio-kit-settings-page lastudio-kit-settings-page__integratios">
  <div class="cx-vui-subtitle cx-vui-subtitle--divider" v-html="'<?php _e('General', 'lastudio-kit'); ?>'"></div>
  <cx-vui-switcher name="svg-uploads"
    label="<?php _e('SVG images upload status', 'lastudio-kit'); ?>"
    description="<?php _e('Enable or disable SVG images uploading', 'lastudio-kit'); ?>"
    :wrapper-css="[ 'equalwidth' ]"
    return-true="enabled"
    return-false="disabled"
    v-model="pageOptions['svg-uploads'].value">
  </cx-vui-switcher>
  <div class="cx-vui-subtitle cx-vui-subtitle--divider" v-html="'<?php _e('Google Maps', 'lastudio-kit'); ?>'"></div>

  <cx-vui-input
    name="google-map-api-key"
    label="<?php _e('Google Map API Key', 'lastudio-kit'); ?>"
    description="<?php
    echo sprintf(esc_html__('Create own API key, more info %1$s', 'lastudio-kit'),
        htmlspecialchars('<a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">here</a>', ENT_QUOTES)
    );
    ?>"
    :wrapper-css="[ 'equalwidth' ]"
    size="fullwidth"
    v-model="pageOptions.gmap_api_key.value"></cx-vui-input>

    <cx-vui-input
      name="google-map-backemd-api-key"
      label="<?php _e('Google Map API Key (Backend)', 'lastudio-kit'); ?>"
      description="<?php
      echo sprintf(esc_html__('Create own API key, more info %1$s', 'lastudio-kit'),
          htmlspecialchars('<a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">here</a>', ENT_QUOTES)
      );
      ?>"
      :wrapper-css="[ 'equalwidth' ]"
      size="fullwidth"
      v-model="pageOptions.gmap_backend_api_key.value"></cx-vui-input>

  <cx-vui-switcher
    name="google-map-disable-api-js"
    label="<?php _e('Disable Google Maps API JS file', 'lastudio-kit'); ?>"
    description="<?php _e('Disable Google Maps API JS file, if it already included by another plugin or theme', 'lastudio-kit'); ?>"
    :wrapper-css="[ 'equalwidth' ]"
    return-true="true"
    return-false="false"
    v-model="pageOptions.disable_gmap_api_js.value">
  </cx-vui-switcher>

  <div
    class="cx-vui-subtitle cx-vui-subtitle--divider"
    v-html="'<?php _e('MailChimp', 'lastudio-kit'); ?>'"></div>

  <cx-vui-input
    name="mailchimp-api-key"
    label="<?php _e('MailChimp API key', 'lastudio-kit'); ?>"
    description="<?php
    echo sprintf(esc_html__('Input your MailChimp API key %1$s', 'lastudio-kit'),
        htmlspecialchars('<a href="http://kb.mailchimp.com/integrations/api-integrations/about-api-keys" target="_blank">About API Keys</a>', ENT_QUOTES)
    );
    ?>"
    :wrapper-css="[ 'equalwidth' ]"
    size="fullwidth"
    v-model="pageOptions['mailchimp-api-key'].value"></cx-vui-input>

  <cx-vui-input
    name="mailchimp-list-id"
    label="<?php _e('MailChimp list ID', 'lastudio-kit'); ?>"
    description="<?php
    echo sprintf(esc_html__('Input MailChimp list ID %1$s', 'lastudio-kit'),
        htmlspecialchars('<a href="http://kb.mailchimp.com/integrations/api-integrations/about-api-keys" target="_blank">About Mailchimp List Keys</a>', ENT_QUOTES)
    ); ?>"
    :wrapper-css="[ 'equalwidth' ]"
    size="fullwidth"
    v-model="pageOptions['mailchimp-list-id'].value"></cx-vui-input>

  <cx-vui-switcher
    name="mailchimp-double-opt-in"
    label="<?php _e('Double opt-in', 'lastudio-kit'); ?>"
    description="<?php _e('Send contacts an opt-in confirmation email when they subscribe to your list.', 'lastudio-kit'); ?>"
    :wrapper-css="[ 'equalwidth' ]"
    return-true="true"
    return-false="false"
    v-model="pageOptions['mailchimp-double-opt-in'].value">
  </cx-vui-switcher>

  <div
    class="cx-vui-subtitle cx-vui-subtitle--divider"
    v-html="'<?php _e('Instagram', 'lastudio-kit'); ?>'"></div>

  <cx-vui-input
    name="insta-access-token"
    label="<?php _e('Access Token', 'lastudio-kit'); ?>"
    description="<?php
    echo sprintf(esc_html__('Read more about how to get Instagram Access Token %1$s', 'lastudio-kit'),
        htmlspecialchars('<a href="https://la-studioweb.com/tip-trick/how-to-get-instagram-access-token/" target="_blank">here</a>', ENT_QUOTES)
    ); ?>"
    :wrapper-css="[ 'equalwidth' ]"
    size="fullwidth"
    v-model="pageOptions.insta_access_token.value"></cx-vui-input>

  <cx-vui-input
    name="insta-business-access-token"
    label="<?php _e('Business Access Token', 'lastudio-kit'); ?>"
    description="<?php
    echo sprintf(esc_html__('Read more about how to get Business Instagram Access Token %1$s', 'lastudio-kit'),
        htmlspecialchars('<a href="https://la-studioweb.com/tip-trick/lastudiokit-how-to-display-instagram-tagged-photos/" target="_blank">here</a>', ENT_QUOTES)
    ); ?>"
    :wrapper-css="[ 'equalwidth' ]"
    size="fullwidth"
    v-model="pageOptions.insta_business_access_token.value"></cx-vui-input>

  <cx-vui-input
    name="insta-business-user-id"
    label="<?php _e('Business User ID', 'lastudio-kit'); ?>"
    description="<?php
    echo sprintf(esc_html__('Read more about how to get Business User ID %1$s', 'lastudio-kit'),
        htmlspecialchars('<a href="https://la-studioweb.com/tip-trick/lastudiokit-how-to-display-instagram-tagged-photos/" target="_blank">here</a>', ENT_QUOTES)
    ); ?>"
    :wrapper-css="[ 'equalwidth' ]"
    size="fullwidth"
    v-model="pageOptions.insta_business_user_id.value"></cx-vui-input>

  <div
    class="cx-vui-subtitle cx-vui-subtitle--divider"
    v-html="'<?php _e('Weatherbit.io API', 'lastudio-kit'); ?>'"></div>

  <cx-vui-input
    name="weatherstack-api-key"
    label="<?php _e('Weatherbit.io API Key', 'lastudio-kit'); ?>"
    description="<?php
    echo sprintf(esc_html__('Create own Weatherbit.io API key, more info %1$s', 'lastudio-kit'),
        htmlspecialchars('<a href="https://www.weatherbit.io/" target="_blank">here</a>', ENT_QUOTES)
    ); ?>"
    :wrapper-css="[ 'equalwidth' ]"
    size="fullwidth"
    v-model="pageOptions.weather_api_key.value"></cx-vui-input>
</div>
