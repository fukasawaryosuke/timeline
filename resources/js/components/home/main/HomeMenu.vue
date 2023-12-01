<template>
  <div class="w-full h-12 pl-12 pr-10 py-1 flex items-center">
    <div class="w-full flex font-semibold items-center text-gray-400">
      <div v-if="systemName" class="flex items-center flex-row">
        <span class="pl-3 mr-3">カテゴリ</span>
        <div
          class="rounded-2xl px-3 py-1 ml-1 first:ml-0 self-center flex-shrink-0 bg-cyan-500 text-white"
        >
          <span class="font-semibold">
            {{ systemName }}
            <fa
              icon="fa-xmark"
              class="ml-3 cursor-pointer hover:text-gray-600 text-white"
              @click="selectedIhsReset"
            />
          </span>
        </div>
      </div>
      <div v-if="selectedUserName" class="flex items-center flex-row">
        <span class="pl-3 mr-3">選択したユーザー</span>
        <div
          class="rounded-2xl px-3 py-1 ml-1 first:ml-0 self-center flex-shrink-0 bg-cyan-500 text-white"
        >
          <span class="font-semibold">
            {{ selectedUserName }}
            <fa
              icon="fa-xmark"
              class="ml-3 cursor-pointer hover:text-gray-600 text-white"
              @click="homeClicked"
            />
          </span>
        </div>
      </div>
      <div v-if="selectedDivisionName" class="flex items-center flex-row">
        <span class="pl-3 mr-3">選択した部門</span>
        <div
          class="rounded-2xl px-3 py-1 ml-1 first:ml-0 self-center flex-shrink-0 bg-cyan-500 text-white"
        >
          <span class="font-semibold">
            {{ selectedDivisionName }}
            <fa
              icon="fa-xmark"
              class="ml-3 cursor-pointer hover:text-gray-600 text-white"
              @click="selectedDivisionReset"
            />
          </span>
        </div>
      </div>
      <!-- 所属部門押下or部門で表示で選択された場合 -->
      <div v-else-if="selectionDisplayDivisionName && watchDivisionCheckFlg" class="flex items-center flex-row">
        <span class="pl-3 mr-3">選択した部門</span>
        <div
          class="rounded-2xl px-3 py-1 ml-1 first:ml-0 self-center flex-shrink-0 bg-cyan-500 text-white"
        >
          <span class="font-semibold">
            {{ selectionDisplayDivisionName }}
            <fa
              icon="fa-xmark"
              class="ml-3 cursor-pointer hover:text-gray-600 text-white"
              @click="selectionDisplayDivisionReset"
            />
          </span>
        </div>
      </div>
    </div>
    <div class="w-16 flex justify-end items-center h-8">
      <div class="flex">
        <fa
          icon="clipboard-check"
          class="pr-2.5 hover:text-gray-700"
          :class="viewMode === 0 ? 'text-gray-700' : 'text-gray-400'"
          @click="__emitChangeView(0)"
        />
        <span class="text-lg text-gray-400 -mt-1.5">/</span>
        <fa
          icon="border-all"
          class="pl-2.5 hover:text-gray-700"
          :class="viewMode === 1 ? 'text-gray-700' : 'text-gray-400'"
          @click="__emitChangeView(1)"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  // searchDate: string;
  // next: () => void;
  // prev: () => void;
  selectedUserName: string;
  viewMode: number;
  selectedIhsId: number;
  systemName: string;
  selectedDivisionName: string;
  selectionDisplayDivisionName: string;
  watchDivisionCheckFlg: boolean;
  selectedIhsReset: () => void;
  selectedDivisionReset: () => void;
  selectionDisplayDivisionReset: () => void;
  // divisionLabels: () => any[];
  homeClicked: () => void;
}
const props = defineProps<Props>();

const emits = defineEmits(['changeView']);

const __emitChangeView = (viewNum: number) => {
  emits('changeView', viewNum);
};
</script>
