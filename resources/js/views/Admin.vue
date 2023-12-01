<!-- 管理画面フレーム -->
<script setup lang="ts">
import { User } from '/@/js/models/User';
interface Props {
  loginUser: User;
}
defineProps<Props>();
</script>

<template>
  <router-view></router-view>
</template>
