<template>
  <div class="pt-2 w-screen pl-14 pr-14">
    <div class="flex flex-wrap items-start gap-6">
      <template v-for="(dairy, index) in indexdata" :key="index">
        <div
          v-if="isDateChange(dairy.created_at)"
          class="bg-stone-50 flex w-full pt-4 pb-2 sticky top-36 z-10"
        >
          <div class="border-t-2 border-cyan-500 w-5/12" />
          <span
            class="leading-4 -mt-2 px-4 w-2/12 text-sm text-cyan-500 font-bold text-center"
          >
            {{ scrollDateString(dairy.created_at) }}
          </span>
          <div class="border-t-2 border-cyan-500 w-5/12" />
        </div>
        <div class="bg-white w-72 border rounded-lg border-gray-400">
          <div class="flex items-center pt-4 pl-4 break-all">
            <img
              class="w-11 h-11 rounded-full border object-cover"
              :src="`/api/users/getProfileIcon/${String(dairy.human_id)}`"
              alt="iconImage"
              loading="lazy"
              decoding="async"
            />
            <div class="pl-3 w-[225px]">
              <div
                class="text-slate-600 text-base border-b-[1.5px] border-b-gray-300 font-semibold mb-1 mr-1"
              >
                <span>
                  {{ dairy?.in_house_system?.system_name }}
                </span>
              </div>
              <span
                class="text-gray-500 font-semibold cursor-pointer"
                @click="
                  () => {
                    selectedUser(dairy.user_name);
                    selectedUserOnlyDiary(dairy.created_by);
                  }
                "
              >
                {{ dairy.user_name }}
              </span>
              <div>
                <span class="align-top text-gray-400 text-xs">
                  {{ dairy.created_at }}
                </span>
              </div>
            </div>
          </div>
          <!-- 削除モーダル 作成ユーザーにのみ表示 -->
          <div
            v-if="dairy.created_by === loginUser.id"
            class="flex justify-end pl-4 pr-6"
          >
            <fa
              icon="fa-pen"
              class="w-4 h-4 cursor-pointer font-semibold hover:text-gray-600 mr-4 text-gray-400"
              @click="gotoEditDiary(dairy.id)"
            />
            <fa
              icon="fa-trash"
              class="w-4 h-4 cursor-pointer font-semibold hover:text-gray-600 text-gray-400"
              @click="deleteModalOpen(dairy.id, index)"
            />
          </div>
          <div
            v-for="(content, index) in getContentForCard(dairy.diary.contents)"
            :key="index"
            class="pl-4 pr-6 overflow-hidden"
          >
            <div
              v-if="
                (content.contentType === REPORT_CONTENT_TYPE.TEXT ||
                  content.contentType === REPORT_CONTENT_TYPE.TEXTAREA ||
                  content.contentType === REPORT_CONTENT_TYPE.DATETIME) &&
                content.content !== null
              "
              class="pt-4"
            >
              <h1 class="font-bold text-gray-700">
                {{ content.title }}
              </h1>
              <div class="pt-0.5 max-h-12">
                <p class="text-sm text-slate-600 break-words card-content" v-html="convertToLinks(content.content)">
                </p>
              </div>
            </div>
            <div
              v-if="content.contentType === REPORT_CONTENT_TYPE.TENKI"
              class="pt-4"
            >
              <h1 class="text-lg font-bold text-gray-700">
                {{ content.title }}
              </h1>
              <WeatherResult :diary="content.content" />
            </div>
            <div
              v-if="
                content.contentType === REPORT_CONTENT_TYPE.IMAGE &&
                content.path !== null
              "
              class="pt-4"
              @click="modalOpen(content.path)"
            >
              <h1 class="text-lg font-bold text-gray-700">
                {{ content.title }}
              </h1>
              <img
                class="max-h-24"
                :src="content.path"
                loading="lazy"
                decoding="async"
              />
            </div>
            <div
              v-if="content.contentType === REPORT_CONTENT_TYPE.MOVIE"
              class="pt-4 font-semibold mr-4 text-gray-400"
            >
              <h1 class="text-lg font-bold text-gray-700">
                {{ content.title }}
              </h1>
              <label
                v-if="content.path !== null"
                class="cursor-pointer hover:text-gray-600"
                @click="movieModalOpen(content.path)"
              >
                <fa icon="fa-video" class="w-4 h-4 mr-2" />
                <span>{{ content.content }}</span>
              </label>
              <div v-else>投稿なし</div>
            </div>
            <div
              v-if="content.contentType === REPORT_CONTENT_TYPE.CHOICE"
              class="pt-4"
            >
              <h1 class="font-bold text-gray-700">
                {{ content.title }}
              </h1>
              <div class="pt-0.5 max-h-12">
                <p class="text-sm text-slate-600 break-words card-content">
                  {{ getCheckedTitle(content) }}
                </p>
              </div>
            </div>
            <div
              v-if="content.contentType === REPORT_CONTENT_TYPE.SELECT"
              class="pt-4"
            >
              <h1 class="font-bold text-gray-700">
                {{ content.title }}
              </h1>
              <div class="pt-0.5 max-h-12">
                <p class="text-sm text-slate-600 break-words card-content">
                  {{ getSelectTitle(content) }}
                </p>
              </div>
            </div>
          </div>
          <div class="flex justify-between items-center">
            <div
              class="py-4 pl-4 text-left font-semibold text-blue-400 cursor-pointer"
              @click="detailModalOpen(dairy)"
            >
              詳細を見る
            </div>
            <div
              v-if="dairy.comment.length > 0"
              class="py-4 pr-6 text-slate-600"
            >
              コメント{{ commentCountLabel(dairy.comment.length) }}件
            </div>
          </div>
        </div>
      </template>
      <InfiniteLoading
        v-if="createdBy && loadingFlag"
        class="w-full flex justify-center pt-10"
        :identifier="infiniteId"
        @infinite="loadData(isHome, createdBy, divisionCodes, searchDate)"
      />
      <InfiniteLoading
        v-else-if="divisionCodes && loadingFlag"
        class="w-full flex justify-center pt-10"
        :identifier="infiniteId"
        @infinite="loadData(isHome, loginUser.id, divisionCodes, searchDate)"
      />
    </div>
    <div
      v-if="!loadingFlag"
      class="w-full flex justify-center pt-10 text-gray-400 font-bold"
    >
      これ以上投稿がありません
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, computed, onMounted } from 'vue';
import InfiniteLoading from 'v3-infinite-loading';
import 'v3-infinite-loading/lib/style.css';
import { LoginUser } from '../../../models/User';
import { REPORT_CONTENT_TYPE } from '/@/js/contarcts/ReportContentType';
import WeatherResult from '/@/js/components/home/common/WeatherResult.vue';

interface Props {
  divisionCodes: number[];
  loginUser: LoginUser;
  isHome: boolean;
  searchDate: string;
  imageModalOpen: (path: string) => void;
  imageModalClose: () => void;
  movieModalOpen: (path: string) => void;
  movieModalClose: () => void;
  cardReportModalOpen: (diary: any) => void;
  cardReportModalClose: () => void;
  gotoEditDiary: (diaryId: number) => void;
  deleteModalOpen: (diaryId: number, diaryIndex: number) => void;
  selectedUser: (path: string) => void;
  indexdata: never | any;
  selectedUserOnlyDiary: (index: number) => void;
  createdBy: number;
  loadingFlag: boolean;
  loadData: (
    isHome: boolean,
    userId: number,
    divisionCodes: number[],
    searchDate: string
  ) => void;
  initLoading: () => void;
  selectedIhsId: number;
}
const props = defineProps<Props>();
const infiniteId = ref<number>(0);

const detailModalOpen = (dairy: any) => {
  props.cardReportModalOpen(dairy);
};
console.log(props.indexdata);

let reportScrollDate = -1;
const isDateChange = (created_at: string) => {
  let d = new Date(created_at).getDate();
  if (reportScrollDate != d) {
    reportScrollDate = d;
    return true;
  }
  return false;
};
const scrollDateString = (created_at: string) => {
  let d = new Date(created_at);
  let dStr = 'YYYY/MM/DD';
  dStr = dStr.replace('YYYY', ('0000' + d.getFullYear()).slice(-4));
  dStr = dStr.replace('MM', ('00' + (d.getMonth() + 1)).slice(-2));
  dStr = dStr.replace('DD', ('00' + d.getDate()).slice(-2));
  return dStr;
};

const getContentForCard = (contents: any[]) => {
  if (!Array.isArray(contents)) {
    return [];
  }
  let resultContents = contents.filter((c) => {
    return c.reportVisible;
  });
  return resultContents;
};

const modalOpen = (src: string) => {
  props.imageModalOpen(src);
};
const movieModalOpen = (src: string) => {
  props.movieModalOpen(src);
};

watch(
  () => props.divisionCodes,
  () => {
    // if (props.divisionCodes.length > 0) {
    // 部署コードが変わったのでリストを作り直す
    props.initLoading();
    // }
  },
  { deep: true }
);
watch(
  () => props.isHome,
  (isHome) => {
    if (isHome) {
      // 所属部署を指定したのでリストを作り直す
      props.initLoading();
    }
  }
);
watch(
  () => props.searchDate,
  () => {
    // 表示日付を選択したのでリストを作り直す
    reportScrollDate = new Date(props.searchDate).getDate();
    props.initLoading();
  }
);

onMounted(() => {
  infiniteId.value = infiniteId.value + 1;
});

const commentCountLabel = (cnt: number): string => {
  if (cnt > 99) {
    return '99+';
  }
  return String(cnt);
};
const getCheckedTitle = computed(() => (diary) => {
  const notSelect = '選択していない';
  if (diary.checked.length < 1) return notSelect;
  const checkedTitle: Array<string> = [];
  diary.choices?.forEach((c) => {
    if (diary.checked?.includes(c.value)) {
      checkedTitle.push(c.title);
    }
  });
  return checkedTitle.join(', ');
});
const getSelectTitle = computed(() => (diary) => {
  const notSelect = '選択していない';
  if (!diary.content) return notSelect; // NOTE: この関数を呼んでいる時点で選択肢がないことはあり得ないので非考慮でいい
  for (let i = 0; i < diary.choices.length; i++) {
    if (diary.choices[i].value == diary.content) {
      return diary.choices[i].title;
    }
  }
  return notSelect;
});

const convertToLinks = (text: string): string => {
  const urlRegex = /(https?:\/\/[^\s]+)/g;
  return text.replace(urlRegex, (url) => {
    return `<a href="${url}" target="_blank" style="color: blue; text-decoration: underline;">${url}</a>`;
  });
};
</script>

<style scoped>
.card-content {
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 2;
  display: -webkit-box;
}
</style>
