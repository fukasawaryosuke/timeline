<template>
  <div class="menu" id="menu_main">
    <div class="menu-item">
      <template v-for="(dairy, index) in indexdata" :key="index">
        <div
          v-if="isDateChange(dairy.created_at)"
          class="menu-item-btn"
          :style="{ top: headerHeight }"
        >
          <div class="article_li_date" />
          <div class="article_li_date floating">
            {{ scrollDateString(dairy.created_at) }}
          </div>
        </div>
        <div class="sp_report" @click="detailModalOpen(dairy)">
          <div>
            <div class="single_control">
              <div class="single_title">
                <img
                  class="home_icons"
                  :src="`/api/users/getProfileIcon/${String(dairy.human_id)}`"
                  alt="iconImage"
                  loading="lazy"
                  decoding="async"
                  @click.stop=""
                />
                <div
                  id="title"
                  @click.stop="
                    () => {
                      selectedUser(dairy.user_name);
                      selectedUserOnlyDiary(dairy.created_by);
                    }
                  "
                >
                  <p class="ihs_name">
                    {{ dairy.in_house_system?.system_name }}
                  </p>
                  <p>{{ dairy.user_name }}</p>
                </div>
              </div>
              <div
                v-if="dairy.created_by === loginUser.id"
                class="single_btn_area"
              >
                <i
                  class="fa fa-pencil-square-o fa_blank"
                  @click.stop="gotoEditDiary(dairy.id)"
                ></i>
                <i
                  class="fa fa-trash fa_blank"
                  @click.stop="deleteModalOpen(dairy.id, index)"
                ></i>
              </div>
            </div>
            <div class="single_description single_description_flex">
              <div class="seido_date">
                <div class="sp_reportTitle" id="title">
                  {{ reportTitle(dairy?.title) }}
                </div>
                <div>
                  <span>
                    {{ dairy.created_at }}
                  </span>
                </div>
              </div>
              <div class="histudoku">
                <div v-if="dairy.must_read" class="check_label">必読</div>
              </div>
            </div>
          </div>
        </div>
      </template>
    </div>
    <InfiniteLoading
      v-if="createdBy && loadingFlag"
      :identifier="infiniteId"
      @infinite="loadData(isHome, createdBy, divisionCodes, searchDate)"
    />
    <InfiniteLoading
      v-else-if="divisionCodes && loadingFlag"
      :identifier="infiniteId"
      @infinite="loadData(isHome, loginUser.id, divisionCodes, searchDate)"
    />
  </div>
  <div v-if="!loadingFlag" class="non-post">これ以上投稿がありません</div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue';
import InfiniteLoading from 'v3-infinite-loading';
import 'v3-infinite-loading/lib/style.css';
import { LoginUser } from '/@/js/models/User';
import { MustReadListModel } from '/@/js/models/MustRead';

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
  getDiary: (id: number) => void;
  mustReadList: MustReadListModel;
  headerHeight: string;
  goHome: () => void;
}
const props = defineProps<Props>();
const infiniteId = ref<number>(0);

const detailModalOpen = (dairy: any) => {
  props.cardReportModalOpen(dairy);
};

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
  let resultContents = contents.filter((c) => {
    return c.reportVisible;
  });
  return resultContents;
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
  document.addEventListener('DOMContentLoaded', () => {
    props.goHome();
  });
});

const reportTitle = (title?: string) => {
  if (title != null) {
    if (title.length > 21) {
      return title.substring(0, 21) + '...';
    } else {
      return title;
    }
  } else {
    return 'タイトルなし';
  }
};
</script>

<style scoped>
.sp_report {
  margin: 4px;
  height: 115px;
  border: 1px solid #ccc;
  background-color: #fff;
  padding: 5px 10px;
}
.non-post {
  padding-top: 30px;
  padding-bottom: 80px;
  color: rgb(156 163 175);
  display: flex;
  justify-content: center;
}
.single_description_flex {
  display: flex;
  align-items: center;
}
.sp_reportTitle {
  font-size: 13px;
  padding: 7px 0;
}
.ihs_name {
  font-size: 12px;
}
</style>
