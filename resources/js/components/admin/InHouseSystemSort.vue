<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

import useAxios from '/@/js/compositions/UseAxios';
import SideMenu from '/@/js/components/admin/SideMenu.vue';
import LaravelPaginate, { IPaginate } from '/@/js/models/admin/Paginate';
const route = useRoute();
const router = useRouter();
const { axiosErrorHandling, errorToast } = useAxios();

const ihsList = reactive<any[]>([]);
const paginate = ref<IPaginate>({} as IPaginate);
const listInitFlag = ref<boolean>(true);

onMounted(async () => {
  await getIhs();
  listInitFlag.value = false;
});
/** method */
const getIhs = async () => {
  await axios
    .get('/api/ihs/all')
    .then((res) => {
      listInit();
      ihsList.push(...res.data.data);
      paginate.value = new LaravelPaginate(res.data);
    })
    .catch((err) => {
      const result = axiosErrorHandling(err);
      errorToast(result.code, result.message);
    });
};
const listInit = () => {
  ihsList.length = 0;
};
const updateSort = async () => {
  await axios
    .put('/api/ihs/sort', { ihsList: ihsList })
    .then(() => {
      gotoListIhs();
    })
    .catch((err) => {
      const result = axiosErrorHandling(err);
      errorToast(result.code, result.message);
    });
};
const gotoListIhs = () => {
  router.push({
    name: 'InHouseSystemList',
  });
};
// drag
const dragStart = (e, id, index) => {
  e.dataTransfer.effectAllowed = 'move';
  e.dataTransfer.dropEffect = 'move';
  e.dataTransfer.setData('index', index);
  e.target.classList.add('onGrab');
};
const dropIhs = (e, id, index) => {
  e.preventDefault();
  let targetIndex = Number(e.dataTransfer.getData('index'));
  // const target = document.getElementById(`form${targetId}`);
  // e.parentNode.insertBefore(target, e);

  const deleteList = ihsList.splice(targetIndex, 1);
  ihsList.splice(index, 0, deleteList[0]);
  ihsList.forEach((ih, ihIndex) => {
    ih.sequence = ihIndex + 1;
  });
  const targetTr = document.getElementsByClassName('mouseDown');
  for (var i = 0; i < targetTr.length; i++) {
    targetTr[i].classList.remove('onGrab');
  }
};
</script>
<template>
  <div class="modal">モーダル</div>
  <div class="main">
    <SideMenu />
    <div class="content">
      <div class="flex-content">
        <h1>カテゴリ並び替え</h1>
        <div class="inner_content">
          <div class="main_header">
            <div class="search_flex">
              <!-- <div class="name_input">
                <input v-model="searchName" type="text" placeholder="制度名" />
              </div> -->
              <button class="redbtn btn" @click="gotoListIhs">
                <i class="fa fa-times fa_blank" aria-hidden="true"></i>
                戻る
              </button>
            </div>
            <button class="yellowbtn btn" @click="updateSort">
              <i class="fa fa-list" aria-hidden="true"></i>
              更新
            </button>
          </div>
          <div
            v-if="ihsList.length === 0 && listInitFlag == false"
            class="main_area"
          >
            表示できる項目がありません。
          </div>
          <div v-else class="main_area">
            <table>
              <tr>
                <th>並び順</th>
                <th>No.</th>
                <th>名前</th>
                <th>作成日</th>
                <th>更新日</th>
              </tr>
              <tr
                v-for="(ihs, i) in ihsList"
                :id="`form${ihs.id}`"
                :key="`form${i}`"
                draggable="true"
                @dragstart="dragStart($event, ihs.id, i)"
                @drop="dropIhs($event, ihs.id, i)"
                @dragover.prevent
                @dragenter.prevent
                class="mouseDown"
              >
                <td>{{ ihs.sequence }}</td>
                <td>{{ ihs.id }}</td>
                <td>{{ ihs.system_name }}</td>
                <td>{{ ihs.created_at }}</td>
                <td>{{ ihs.updated_at }}</td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<style>
.mouseDown {
  cursor: -moz-grab;
  cursor: -webkit-grab;
  cursor: grab;
}
.mouseDown.onGrab {
  cursor: -moz-grabbing;
  cursor: -webkit-grabbing;
  cursor: grabbing;
}
</style>
