<template>
  <div
    class="fixed w-full bg-assist-grad z-50 h-12 flex border-b px-3 py-1 items-center"
  >
    <div class="absolute font-bold text-xl text-gray-600 cursor-pointer">
      <h1 class="text-white" @click="goHome()">assistタイムライン</h1>
    </div>
    <div class="flex h-full w-full justify-center">
      <!-- <div
        class="cursor-pointer font-semibold px-2 py-1 self-center border-r border-cyan-600"
      >
        <span class="text-gray-50 border-b-2 border-cyan-50 pb-1"
          >タイムライン</span
        >
      </div>
      <div
        class="cursor-pointer text-gray-300 font-semibold pl-3 pr-2 py-1 self-center hover:text-white border-r border-cyan-600"
      >
        お知らせ
      </div> -->
    </div>
    <div class="absolute right-3 flex h-full">
      <a
        href="https://ha-all-employee.zendesk.com/hc/ja/categories/10485521418265-%E3%82%A2%E3%82%B7%E3%82%B9%E3%83%88%E3%82%BF%E3%82%A4%E3%83%A0%E3%83%A9%E3%82%A4%E3%83%B3%E3%81%AB%E3%81%A4%E3%81%84%E3%81%A6"
        title="操作でわからないことがあれば、こちらをクリック！"
        target="_blank"
        class="flex mr-4 w-7 h-7 bg-white/50 hover:bg-white/75 rounded-full text-gray-500 hover:text-gray-700 self-center justify-center"
      >
        <fa icon="question" class="text-l self-center" />
      </a>
      <img
        class="w-8 h-8 rounded-full border self-center object-cover"
        :src="`/api/users/getProfileIcon/${String(loginUser.human_id)}`"
        alt="loginuser"
        loading="lazy"
      />
      <div class="text-gray-50 border-cyan-50 self-center ml-2">
        {{ loginUser.user_name }}
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router';
import axios from 'axios';
import { onMounted } from 'vue';
import { User } from '../models/User';

interface Props {
  loginUser: User;
}

defineProps<Props>();

const router = useRouter();

const goHome = () => {
  router.push({ name: 'Home' });
};

// onMounted(() => {
//   axios.get("/api/loginuser").then((res) => {
//     console.log(res);
//   });
// });
</script>

<style scoped></style>
