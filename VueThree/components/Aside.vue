<script setup>
import {reactive, ref} from 'vue';

import Menu from 'primevue/menu';

const height = ref(window.innerHeight)

const items = ref([
  {
    label: 'Saas Vue 3',
    items: [
      {
        label: 'Dashboard',
        icon: 'fa-regular fa-chart-bar',
        route: "/"
      },
        {
            label: 'Tenant',
            icon: 'fa-regular fa-chart-bar',
            route: "/tenantsv3"
        },
    ]
  },
]);

</script>
<template>

  <div v-if="height" class="card mr-2 flex justify-center">
    <Menu :model="items">
      <template #item="{ item, props }">
        <router-link v-if="item.route" v-slot="{ href, navigate }" :to="item.route" custom>
          <a v-ripple :href="href" v-bind="props.action" @click="navigate">
            <span :class="item.icon" />
            <span>{{ item.label }}</span>
          </a>
        </router-link>
        <a v-else v-ripple :href="item.url" :target="item.target" v-bind="props.action">
          <span :class="item.icon" />
          <span>{{ item.label }}</span>
        </a>
      </template>
    </Menu>
  </div>

</template>


