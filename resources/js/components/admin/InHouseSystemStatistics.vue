<script setup lang="ts">
import SideMenu from '/@/js/components/admin/SideMenu.vue';
import axios from 'axios';
import Datepicker from 'vue3-datepicker';
import { ref, reactive, computed, onMounted } from 'vue';
import { Chart, registerables, ChartOptions } from 'chart.js';
import { BarChart } from 'vue-chart-3';
import { useToast } from 'vue-toastification';
import useAxios from '/@/js/compositions/UseAxios';
import dayjs, { Dayjs } from 'dayjs';
Chart.register(...registerables);
const toast = useToast();
const { asyncMethodLock, axiosErrorHandling, errorToast } = useAxios();

const startAt = ref(undefined);
const endAt = ref(undefined);
const systemValue = ref('');
const filter = ref('');
const ihsList = reactive<any[]>([]);
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
    systemValue.value === '' ||
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
const sendDate = () => {
  if (inputErrToast()) return false;
  const setJstStart = setJst(startAt.value);
  const setJstEnd = setJst(endAt.value);
  axios
    .get('/api/diary/inHouseSystem', {
      params: {
        ihsId: systemValue.value,
        startAt: setJstStart,
        endAt: setJstEnd,
        filter: filter.value,
      },
    })
    .then((res) => {
      totalPost.value = res.data.data.posts;
      dateLabel.value = res.data.data.labels;
    })
    .catch((err) => {
      const result = axiosErrorHandling(err);
      errorToast(result.code, result.message);
    });
};
const csvDownload = () => {
  if (inputErrToast()) return false;
  const setJstStart = setJst(startAt.value);
  const setJstEnd = setJst(endAt.value);
  axios
    .get('/api/diary/csvDownload', {
      params: {
        ihsId: systemValue.value,
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
    });
};

const totalPost = ref();
const dateLabel = ref();

const options: ChartOptions<'bar'> = {
  scales: {
    y: {
      display: true,
      suggestedMin: 10,
      beginAtZero: true,
      ticks: {
        stepSize: 1,
      },
    },
  },
};
const inHouseSystemChart = computed(() => {
  return {
    labels: dateLabel.value,
    datasets: [
      {
        data: totalPost.value,
        label: 'カテゴリ別投稿数',
        backgroundColor: ['#77CEFF'],
        maxBarThickness: 50,
        animation: {
          duration: 700,
        },
      },
    ],
  };
});
const reset = () => {
  systemValue.value = '';
  startAt.value = undefined;
  endAt.value = undefined;
  filter.value = '';
  dateLabel.value = [];
  totalPost.value = [];
};

// 制度一覧を取得
onMounted(async () => {
  await asyncMethodLock(async () => {
    await axios
      .get('/api/ihs/list')
      .then((res) => {
        ihsList.length = 0;
        ihsList.push(...res.data);
      })
      .catch((err) => {
        const result = axiosErrorHandling(err);
        errorToast(result.code, result.message);
      });
  });
});
</script>
<template>
  <div class="modal">モーダル</div>
  <div class="main">
    <SideMenu />
    <div class="content">
      <div class="flex-content">
        <h1>カテゴリ別投稿数</h1>
        <div class="inner_content">
          <div class="main_header mb30">
            <div class="search_flex align_center">
              <div class="select_wrap">
                <select v-model="systemValue" required>
                  <option value="">カテゴリを選択</option>
                  <option
                    v-for="i in ihsList"
                    :key="`ihsList${i.id}`"
                    :value="i.id"
                  >
                    {{ i.system_name }}
                  </option>
                  <!-- <option value="1">５分日報</option>
                  <option value="2">制度２</option>
                  <option value="3">制度３</option> -->
                </select>
              </div>
              <div class="flex_2col align_center mb0">
                <Datepicker
                  id=""
                  v-model="startAt"
                  type="text"
                  placeholder="集計期間（開始）"
                />
                <span style="margin: 0 5px">～</span>
                <Datepicker
                  id=""
                  v-model="endAt"
                  type="text"
                  placeholder="集計期間（終了) "
                />
              </div>
              <div class="select_wrap">
                <select v-model="filter" required>
                  <option value="">期間を選択</option>
                  <option value="year">年集計</option>
                  <option value="month">月集計</option>
                  <option value="day">日集計</option>
                </select>
              </div>
              <button class="deepbluebtn btn" href="#" @click="csvDownload">
                <i class="fa fa-download fa_blank" aria-hidden="true"></i>
                CSV出力
              </button>
              <button class="bluebtn btn" href="#" @click="sendDate">
                <!-- <i class="fa fa-search fa_blank" aria-hidden="true"></i> -->
                <i class="fa fa-chart-bar" aria-hidden="true"></i>
                グラフ表示
              </button>
              <button class="redbtn btn" href="#" @click="reset">
                <i class="fa fa-times fa_blank" aria-hidden="true"></i>
                リセット
              </button>
            </div>
          </div>
          <!-- <div class="search_flex align_center">
            <button class="graybtn current_btn btn" href="#" value="">1日</button>
            有効な時は .current_btn クラスを付与
            <button class="graybtn btn" href="#" value="month">1ヶ月</button>
            <button class="graybtn btn" href="#">4半期</button>
            <button class="graybtn btn" href="#">半期</button>
            <button class="graybtn btn" href="#" value="year">1年</button>
          </div> -->
          <div class="mt-120">
            <BarChart :chart-data="inHouseSystemChart" :options="options" />
          </div>
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
