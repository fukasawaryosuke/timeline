<!-- NOTE: 画像取得APIが作成されたら各human_id等でsrcを設定 -->
<template>
  <div class="flex justify-end mb-6 relative">
    <div>
      <div class="flex items-center justify-end pr-2 sp_read_icons">
        <img
          class="history-image inline rounded-full border object-cover"
          v-for="(b, i) in browsFirstFiveList"
          :key="String(`browshistory${i}`)"
          :src="`/api/users/getProfileIcon/${String(b.human_id)}`"
          :title="b.name"
          loading="lazy"
          decoding="async"
        />
      </div>
      <div class="flex justify-end items-center text-gray-500 sp-flex">
        <label @click="toggleHistory(diaryId)">
          {{ browsCount }}人が閲覧しました。
          <fa
            icon="fa-clock-rotate-left"
            class="cursor-pointer text-gray-400 hover:text-gray-700 mr-2"
          />
        </label>
      </div>
    </div>
    <div
      v-if="isShow"
      class="history-popup whitespace-nowrap bg-gray-200 border rounded py-1 absolute top-12 right-0 before:content-[''] before:absolute before:right-1 before:-top-4 before:border-8 before:border-transparent before:border-b-gray-200 z-20 sp-bg"
    >
      <div class="my-2 px-2 flex justify-between sp-between">
        <span class="text-xs">閲覧({{ total }})</span>
        <fa
          icon="fa-xmark"
          class="cursor-pointer text-xs font-semibold text-gray-400 hover:text-gray-600"
          @click="closeHistory(diaryId)"
        />
      </div>
      <ul class="max-h-40 overflow-y-scroll">
        <li
          v-for="(history, idx) in historyList.contents"
          :key="idx"
          class="py-2 pl-2 pr-4 border-b border-gray-300 last:border-0 flex justify-between w-96 sp-between sp-border"
        >
          <div class="flex sp-flex">
            <img
              class="history-image rounded-full border object-cover"
              :key="String(`browshistory${idx}`)"
              :src="`/api/users/getProfileIcon/${String(history.human_id)}`"
              :title="history.name"
              loading="lazy"
              decoding="async"
            />
            <span class="font-bold text-sm ml-2 sp-ml">{{ history.name }}</span>
          </div>
          <span class="text-xs sp-ml">{{ history.browsed_at }}</span>
        </li>
        <InfiniteLoading
          v-if="loadingFlag"
          class="w-full flex justify-center pt-10"
          :identifier="infiniteId"
          @infinite="loadData(diaryId)"
        />
      </ul>
    </div>
  </div>
</template>
<script setup lang="ts">
import axios from 'axios';
import { ref, onMounted, reactive, computed } from 'vue';
import InfiniteLoading from 'v3-infinite-loading';
import 'v3-infinite-loading/lib/style.css';
import { HistoryModel, HistoryListModel } from '../../../models/History';
import { BrowsHistory } from '../../../models/BrowsHistory';
import useHistory from '../../../compositions/UseHistory';
import dayjs from 'dayjs';

interface Props {
  isShow: boolean;
  diaryId: number;
  browsHistory: string;
  toggleHistory: (id: number) => void;
  closeHistory: (id: number) => void;
}
const props = defineProps<Props>();
const browsList: BrowsHistory[] = props.browsHistory
  ? JSON.parse(props.browsHistory)
  : [];
const browsCount = computed(() => {
  return browsList ? String(browsList.length) : '0';
});
const { loadingFlag, infiniteId, historyList, total, loadData } = useHistory();

const browsFirstFiveList = computed(() => {
  return browsList ? browsList.reverse().slice(0, 5) : [];
});
</script>
<style scoped>
.history-popup {
  min-width: 10rem;
}
.history-image {
  width: 20px;
  height: 20px;
  object-fit: cover;
}
@media screen and (max-width: 767px) {
  .sp_read_icons {
    display: flex;
    justify-content: flex-end;
    align-items: flex-end;
    width: 100%;
  }
  .sp_read_icons img {
    margin-left: 5px;
  }
  .history-image {
    border: rgb(180, 176, 176) 1px solid;
    border-radius: 100%;
    object-fit: cover;
  }
  .sp-flex {
    display: flex;
    justify-content: flex-end;
    margin: 2px 0;
  }
  .sp-between {
    display: flex;
    justify-content: space-between;
    padding: 6px;
  }
  .sp-ml {
    margin-left: 5px;
  }
  .sp-bg {
    background-color: rgb(229 231 235);
    border-radius: 4px;
  }
  .sp-border {
    padding: 6px;
    border-bottom: 1px solid rgb(215, 216, 218);
  }
  ul li:last-child {
    border-bottom: none;
  }
}
</style>
