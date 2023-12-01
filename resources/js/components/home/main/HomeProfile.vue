<template>
  <div class="w-80 border bg-white rounded-xl py-2">
    <div class="py-2 flex justify-center">
      <img
        class="w-20 h-20 rounded-full border object-cover"
        :src="`/api/users/getProfileIcon/${String(loginUser.human_id)}`"
        alt="iconImage"
        loading="lazy"
        decoding="async"
      />
    </div>
    <div class="pl-8 pr-4 py-2">
      <p class="text-gray-400 text-xs">名前</p>
      <p class="text-lg font-bold text-gray-700">{{ loginUser.user_name }}</p>
    </div>
    <div class="pl-8 pr-4 py-2">
      <p class="text-gray-400 text-xs">メールアドレス</p>
      <p class="text-lg font-bold text-gray-700">{{ loginUser.mailadd }}</p>
    </div>
    <div class="pl-8 pr-4 py-2">
      <p class="text-gray-400 text-xs">所属部門</p>
      <p class="text-lg font-bold text-gray-700">
        {{ loginUser.organization?.organization_name }}
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { LoginUser } from '../../../models/User';
interface Props {
  loginUser: LoginUser;
}
defineProps<Props>();
</script>
