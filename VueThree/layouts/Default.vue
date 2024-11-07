<script setup>
import {onMounted, reactive} from "vue";


import {useRootStore} from '@/stores/root'
import Aside from "@/components/Aside.vue";
import Footer from "@/components/Footer.vue";

const rootStore = useRootStore();


onMounted(async () => {
  await rootStore.getAssets();
});


</script>


<template>
  <div v-if="rootStore.assets">

    <div class="flex items-start">
      <Aside/>
      <RouterView/>
    </div>

    <Footer/>
  </div>

</template>
