<?php
  include_once '../system/lib/bootstrapHAX.php';
  include_once $HAXCMS->configDirectory . '/config.php';
?>
<link rel="import" href="bower_components/polymer/polymer.html">
<link rel="import" href="bower_components/jwt-login/jwt-login.html">
<!--
`hax-cms-jwt`
a simple element to check for and fetch JWTs

@demo demo/index.html

@microcopy - the mental model for this element
- jwt - a json web token which is an encrypted security token to talk

-->

<dom-module id="hax-cms-jwt">
  <template>
    <style>
      :host {
        display: block;
      }
    </style>
    <jwt-login id="jwt" url="[[jwtLoginLocation]]" url-logout="[[jwtLogoutLocation]]" jwt="{{jwt}}"></jwt-login>
  </template>
  <script>
    Polymer.cmsSiteEditor = Polymer({
      is: 'hax-cms-jwt',
      properties: {
        /**
         * Location of what endpoint to hit for
         */
        jwtLoginLocation: {
          type: String,
          value: '<?php print $HAXCMS->basePath;?>system/login.php',
        },
        /**
         * Location of what endpoint to hit for logging out
         */
        jwtLogoutLocation: {
          type: String,
          value: '<?php print $HAXCMS->basePath;?>system/logout.php',
        },
        /**
         * JSON Web token, it'll come from a global call if it's available
         */
        jwt: {
          type: String,
          observer: '_jwtChanged',
        },
      },
      /**
       * Attached life cycle
       */
      ready: function () {
        this.jwt = localStorage.getItem('jwt');
      },
      /**
       * JWT changed so it's ready to go
       */
       _jwtChanged: function (newValue, oldValue) {
        if (newValue) {
          // attempt to dynamically import the hax cms site editor
          // which will appear to be injecting into the page
          // but because of this approach it should be non-blocking
          try {
            this.importHref(this.resolveUrl('hax-cms-site-editor.php'), (e) => {
              let haxCmsSiteEditorElement = document.createElement('hax-cms-site-editor');
              haxCmsSiteEditorElement.jwt = newValue;
              Polymer.cmsSiteEditor.instance.appendTarget.appendChild(haxCmsSiteEditorElement);
            }, (e) => {
              //import failed
            });
          }
          catch(err) {
            // error in the event this is a double registration
          }
        }
       },
    });
    // store reference to the instance as a global
    Polymer.cmsSiteEditor.instance = null;
    // self append if anyone calls us into action
    Polymer.cmsSiteEditor.requestAvailability = function (element = this, location = document.body, callback = null) {
      if (!Polymer.cmsSiteEditor.instance) {
        Polymer.cmsSiteEditor.instance = document.createElement('hax-cms-jwt');
        Polymer.cmsSiteEditor.instance.appRefreshCallback = callback;
        Polymer.cmsSiteEditor.instance.appElement = element;
        Polymer.cmsSiteEditor.instance.appendTarget = location;
      }
      document.body.appendChild(Polymer.cmsSiteEditor.instance);
    };
  </script>
</dom-module>