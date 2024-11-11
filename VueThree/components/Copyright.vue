<template>
  <div v-if="root && root.assets">


    <p class="has-text-centered">
      {{root.assets.module.name}} v{{root.assets.module.version}}

    </p>


    <p>
      <small>
        App v{{root.assets.versions.app_version}}
        | Laravel v{{root.assets.versions.laravel_version}}
        | PHP v{{root.assets.versions.php_version}}
        |
        <a :href="root.assets.vaahcms.website" target="_blank">{{root.assets.vaahcms.name}}</a>
        v{{root.assets.versions.vaahcms_version}}
        | <a :href="root.assets.vaahcms.docs" target="_blank">Documentation</a>

      </small>
    </p>

  </div>
</template>

<script setup>

import {useRootStore} from '../stores/root.js'

const root = useRootStore();

</script>
