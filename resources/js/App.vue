<template>
  <router-view v-slot="{ Component, route }">
        <div :key="route.name">
            <Component :is="Component" />
        </div>
    </router-view>
  </template>

  <script>
  import Calendar from './Pages/Calendar.vue';

  export default {
    components: {
      Calendar,
    },
  };
  </script>
