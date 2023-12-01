<script setup lang="ts">
import { useRouter } from 'vue-router';
// interface Props {
//   updateDivisionCodes: (cds: number[]) => void;
//   updateHomeMode: (flg: boolean) => void;
// } defineProps<Props>();
const router = useRouter();

const homeClicked = () => {
  router.push({ name: 'SpHome' });
};
</script>

<template>
  <div class="inner upper bg-assist-grad">
    <div class="header_blank" @click="homeClicked">
      <i class="fa fa-home"></i>
    </div>
    <h1 class="logo">assistタイムライン</h1>
    <div class="header_blank"></div>
  </div>
</template>
