<template>
  <div
    id="header"
    class="w-full h-12 bg-assist-grad flex border-b py-1 px-3 items-center justify-between"
  >
    <h1 class="text-white font-bold text-xl cursor-pointer" @click="goHome()">
      assistタイムライン
    </h1>
    <div class="flex h-full justify-center">
      <div
        id="ihs"
        class="closest-ihs cursor-pointer font-semibold self-center border-r border-cyan-600 relative px-6"
        @click="emit('showIhs')"
      >
        <HeaderNavSpan str="カテゴリ" />
        <div
          :style="{
            top: noticeTop,
          }"
          class="absolute overflow-hidden ease-in-out duration-300"
          :class="mode === 2 ? 'max-h-96' : 'max-h-0'"
        >
          <HomeIhsList :ihs-list="ihsList" @select-ihs="selectIhs" />
        </div>
      </div>
      <div
        id="notice"
        class="closest-notice cursor-pointer font-semibold self-center relative px-6"
        :class="{ 'text-gray-300': selectTimeLine }"
        @click="emit('showNotice')"
      >
        <HeaderNavSpan str="お知らせ" />
        <span
          v-if="noticeList.newNoticeCount() !== 0"
          class="bg-red-500 rounded-full text-white min-w-[20px] h-5 text-center leading-5 absolute pr-1 pl-1 -top-1 right-1 text-xs z-50"
        >
          {{ String(noticeList.newNoticeCount()) }}
        </span>
        <div
          :style="{
            top: noticeTop,
          }"
          class="absolute overflow-hidden ease-in-out duration-300"
          :class="mode === 1 ? 'max-h-96' : 'max-h-0'"
        >
          <HomeNotice
            :notice-list="noticeList"
            @select-notice-diary="getDiary"
          />
        </div>
      </div>
      <div
        id="must"
        class="closest-must cursor-pointer font-semibold self-center border-l border-cyan-600 relative px-6"
        :class="{ 'text-gray-300': selectTimeLine }"
        @click="emit('showMustRead')"
      >
        <HeaderNavSpan str="必読投稿" />
        <span
          v-if="mustReadList.latestCount !== 0"
          class="bg-red-500 rounded-full text-white min-w-[20px] h-5 text-center leading-5 absolute pr-1 pl-1 -top-1 right-1 text-xs z-50"
        >
          {{ String(mustReadList.latestCount) }}
        </span>
        <div
          :style="{
            top: noticeTop,
          }"
          class="absolute overflow-hidden ease-in-out duration-300"
          :class="mode === 3 ? 'max-h-96' : 'max-h-0'"
        >
          <HomeMustRead :must-read-list="mustReadList" :get-diary="getDiary" />
        </div>
      </div>
      <div
        v-if="subscriptionUrl"
        id="subscription"
        class="closest-notice cursor-pointer font-semibold self-center border-l border-cyan-600 px-6"
        @click="goSubscription"
      >
        <HeaderNavSpan str="まなびアシスト" />
      </div>

      <div
        v-if="loginUser.system_role_id == 2"
        id="admin"
        class="closest-notice cursor-pointer font-semibold self-center border-l border-cyan-600 px-6"
        @click="goAdmin"
      >
        <HeaderNavSpan str="管理画面" />
      </div>
    </div>

    <div class="right-2 flex h-full">
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
import { ref, computed, onMounted } from 'vue';

import HeaderNavSpan from './HeaderNavSpan.vue';
import HomeNotice from './/home/main/HomeNotice.vue';
import { moduleExpression } from '@babel/types';
import { InHouseSystem, LoginUser } from '../models/User';
import { NoticeListModel } from '/@/js/models/Notice';
import HomeIhsList from './home/main/HomeIhsList.vue';
import HomeMustRead from './home/main/HomeMustRead.vue';
import { MustReadListModel } from '../models/MustRead';

interface Props {
  loginUser: LoginUser;
  mode?: number;
  noticeList: NoticeListModel;
  noticeTop: string;
  getDiary: (diary_id: number) => void;
  // newNoticeCount?: number;
  ihsList: InHouseSystem[];
  updateIhs: (id: number, name: string) => void;
  mustReadList: MustReadListModel;
}
interface Emits {
  (e: 'modeChange', mode: number): void;
  (e: 'showNotice'): void;
  (e: 'showIhs'): void;
  (e: 'showMustRead'): void;
  (e: 'selectNoticeDiary', diaryId: number): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();
const router = useRouter();
const subscriptionUrl = process.env.MIX_SUBSCRIPTION_URL;

const goHome = () => {
  router.push({ name: 'Home' });
};
const goSubscription = () => {
  window.open(subscriptionUrl, '_blank');
};
const goAdmin = () => {
  window.open('/admin', '_blank');
};

const selectIhs = (ihsId: number, ihsName: string) => {
  emit('modeChange', 0);
  props.updateIhs(ihsId, ihsName);
};

const selectTimeLine = computed(() => {
  return props.mode === 0;
});

const selectNotice = computed(() => {
  return props.mode === 1;
});
</script>

<style scoped></style>

<style>
.invisible-scroll-bar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.invisible-scroll-bar::-webkit-scrollbar {
  display: none;
}
</style>
