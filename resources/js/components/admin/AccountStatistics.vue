<script setup lang="ts">
import axios from 'axios';
import SideMenu from '/@/js/components/admin/SideMenu.vue';
import useAxios from '/@/js/compositions/UseAxios';
import { ref, reactive, onMounted } from 'vue';
import { useToast } from 'vue-toastification';
import Datepicker from 'vue3-datepicker';
import dayjs from 'dayjs';

const { axiosErrorHandling, asyncMethodLock, errorToast } = useAxios();
const toast = useToast();
const startAt = ref(undefined);
const endAt = ref(undefined);
const filter = ref('');
const userValue = ref('');
const userList = reactive<any[]>([]);

//jst変換
const setJst = (targetDate) => {
  if (targetDate === startAt.value) {
    return dayjs(startAt.value).format('YYYY-MM-DD 00:00:00');
  } else if (targetDate === endAt.value) {
    return dayjs(endAt.value).format('YYYY-MM-DD 23:59:59');
  }
};
const inputErrToast = () => {
  if (
    filter.value === '' ||
    startAt.value === undefined ||
    endAt.value === undefined
  ) {
    toast.error('必須項目の入力がありません');
    return true;
  }
  if (startAt.value > endAt.value) {
    toast.error('集計期間が正しくありません');
    return true;
  }
  return false;
};

// csvダウンロード
const downloadCsv = async () => {
  if (inputErrToast()) return false;
  const setJstStart = setJst(startAt.value);
  const setJstEnd = setJst(endAt.value);
  await asyncMethodLock(async () => {
    await axios
      .get('/api/comment/csvDownload', {
        params: {
          user_id: userValue.value,
          startAt: setJstStart,
          endAt: setJstEnd,
          filter: filter.value,
        },
        responseType: 'blob',
      })
      .then((res) => {
        let mineType = res.headers['content-type'];
        const name = res.headers['content-disposition'].split('=')[1];
        const blob = new Blob([res.data], { type: mineType });
        const a = document.createElement('a');
        const url = (window.URL || window.webkitURL).createObjectURL(blob);
        a.href = url;
        a.download = name;
        a.click();
        a.remove();
        (window.URL || window.webkitURL).revokeObjectURL(url);
      })
      .catch((error) => {
        const result = axiosErrorHandling(error);
        errorToast(result.code, result.message);
      });
  });
};

onMounted(async () => {
  await axios
    .get('/api/account/userlist')
    .then((res) => {
      userList.length = 0;
      userList.push(...res.data);
    })
    .catch((err) => {
      const result = axiosErrorHandling(err);
      errorToast(result.code, result.message);
    });
});
</script>

<template>
  <div class="modal">モーダル</div>
  <div class="main">
    <SideMenu />
    <div class="content">
      <div class="flex-content">
        <h1>CSV出力</h1>
        <div class="inner_content">
          <div class="main_header mb30">
            <div class="search_flex align_center">
              <div class="select_wrap">
                <select v-model="userValue" required>
                  <option value="">任意: アカウント選択</option>
                  <option
                    v-for="user in userList"
                    :key="`userList${user.id}`"
                    :value="user.id"
                  >
                    {{ user.user_name }}
                  </option>
                  <!-- <option value="">〇〇〇</option>
                    <option value="">〇〇〇</option>
                    <option value="">〇〇〇</option> -->
                </select>
              </div>
              <div class="flex_2col align_center mb0">
                <Datepicker
                  id=""
                  v-model="startAt"
                  placeholder="集計期間（開始）"
                ></Datepicker>
                <span style="margin: 0 5px">～</span>
                <Datepicker
                  id=""
                  v-model="endAt"
                  placeholder="集計期間（終了）"
                ></Datepicker>
              </div>
              <div class="select_wrap">
                <select v-model="filter" required>
                  <option value="">期間を選択</option>
                  <option value="year">年集計</option>
                  <option value="month">月集計</option>
                  <option value="day">日集計</option>
                </select>
              </div>
              <button class="deepbluebtn btn" @click="downloadCsv">
                <i class="fa fa-download fa_blank" aria-hidden="true"></i>
                CSV出力
              </button>
            </div>
          </div>
          <!-- </div>
          <div class="main_area"> -->
          <!--メインコンテンツエリア-->
          <!-- </div> -->
        </div>
      </div>
    </div>
  </div>
</template>
<style>
select:invalid {
  color: #888686;
}

.mt-80 {
  margin-top: 80px;
}
.mt-120 {
  margin-top: 120px;
  padding-right: 80px;
}
</style>
