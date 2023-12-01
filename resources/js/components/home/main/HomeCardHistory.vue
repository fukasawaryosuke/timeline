<template>
  <div class="flex justify-end pt-4 pr-4 relative">
    <div>
      <fa
        icon="fa-clock-rotate-left"
        class="cursor-pointer text-gray-400 hover:text-gray-700"
        @click="toggleHistory(diaryId)"
      />
    </div>
    <div
      v-if="isShow"
      class="history-popup whitespace-nowrap bg-gray-200 border rounded py-1 absolute top-11 right-3 before:content-[''] before:absolute before:right-1 before:-top-4 before:border-8 before:border-transparent before:border-b-gray-200"
    >
      <div class="my-2 px-2 flex justify-between">
        <span class="text-xs">既読({{ total }})</span>
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
          class="py-2 pl-2 pr-4 border-b border-gray-300 last:border-0 flex flex-wrap w-64"
        >
          <span class="font-bold text-sm w-full">{{ history.name }}</span>
          <span class="text-xs w-full">{{ history.browsed_at }}</span>
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
import axios from "axios";
import { ref, onMounted, reactive } from "vue";
import InfiniteLoading from "v3-infinite-loading";
import "v3-infinite-loading/lib/style.css";
import { HistoryModel, HistoryListModel } from "../../../models/History";
import useHistory from "../../../compositions/UseHistory";

interface Props {
  isShow: boolean;
  diaryId: number;
  toggleHistory: (id: number) => void;
  closeHistory: (id: number) => void;
}
const props = defineProps<Props>();
const { loadingFlag, infiniteId, historyList, total, loadData } = useHistory();
</script>
<style scoped>
.history-popup {
  min-width: 10rem;
}
</style>
