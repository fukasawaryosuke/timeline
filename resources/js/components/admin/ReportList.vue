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

const searchName = ref<string>('');
const formList = reactive<any[]>([]);
const paginate = ref<IPaginate>({} as IPaginate);
const listInitFlag = ref<boolean>(true);

onMounted(async () => {
  await getReportForm();
  listInitFlag.value = false;
});
/** method */
const getReportForm = async () => {
  await axios
    .get('/api/reportForm/show', {
      params: { name: route.query.name, page: route.query.page },
    })
    .then((res) => {
      listInit();
      formList.push(...res.data.data);
      paginate.value = new LaravelPaginate(res.data);
    })
    .catch((err) => {
      const result = axiosErrorHandling(err);
      errorToast(result.code, result.message);
    });
};
const listInit = () => {
  formList.length = 0;
};
const gotoCreateForm = () => {
  router.push({
    name: 'ReportFormCreate',
  });
};
const gotEditForm = (id: number) => {
  router.push({
    name: 'ReportFormEdit',
    params: {
      id: id,
    },
  });
};
const movePage = async (page: number) => {
  await router.push({
    name: 'ReportFormList',
    query: {
      page: page,
      name: searchName.value,
    },
  });
  await getReportForm();
};
const searchForm = async () => {
  await router.push({
    name: 'ReportFormList',
    query: {
      // page: 1,
      name: searchName.value,
    },
  });
  await getReportForm();
};
const resetForm = async () => {
  searchName.value = '';
  await router.push({
    name: 'ReportFormList',
  });
  await getReportForm();
};
</script>

<template>
  <div class="modal">モーダル</div>
  <div class="main">
    <SideMenu />
    <div class="content">
      <div class="flex-content">
        <h1>投稿フォームリスト</h1>
        <div class="inner_content">
          <div class="main_header">
            <div class="search_flex">
              <div class="name_input">
                <input
                  v-model="searchName"
                  type="text"
                  placeholder="投稿フォーム名"
                />
              </div>
              <button class="bluebtn btn" @click="searchForm">
                <i class="fa fa-search fa_blank" aria-hidden="true"></i>
                検索
              </button>
              <button class="redbtn btn" @click="resetForm">
                <i class="fa fa-times fa_blank" aria-hidden="true"></i>
                リセット
              </button>
            </div>
            <button class="yellowbtn btn" @click="gotoCreateForm">
              <i class="fa fa-plus fa_blank" aria-hidden="true"></i>
              新規作成
            </button>
          </div>
          <div
            v-if="formList.length === 0 && listInitFlag == false"
            class="main_area"
          >
            表示できる項目がありません。
          </div>
          <div v-else class="main_area">
            <table>
              <tr>
                <th>No.</th>
                <th>投稿フォーム名</th>
                <th>作成日</th>
                <th>更新日</th>
              </tr>
              <tr v-for="(f, i) in formList" :key="`form${i}`">
                <td>{{ f.id }}</td>
                <td>
                  <a @click="gotEditForm(f.id)">{{ f.name }}</a>
                </td>
                <td>{{ f.created_at }}</td>
                <td>{{ f.updated_at }}</td>
              </tr>
            </table>
            <div class="pager">
              <ul class="pagination">
                <template
                  v-for="(l, i) in paginate.links"
                  :key="`paginate${i}`"
                >
                  <li v-if="i === 0" class="pre">
                    <a
                      v-if="paginate.current_page !== 1"
                      @click="movePage(paginate.current_page - 1)"
                    >
                      <span>«</span>
                    </a>
                  </li>
                  <li v-else-if="i === paginate.links.length - 1" class="next">
                    <a
                      v-if="paginate.last_page !== paginate.current_page"
                      @click="movePage(paginate.current_page + 1)"
                    >
                      <span>»</span>
                    </a>
                  </li>
                  <li v-else>
                    <a
                      :class="l.active ? 'active' : ''"
                      @click="movePage(Number(l.label))"
                    >
                      <span>{{ l.label }}</span>
                    </a>
                  </li>
                </template>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
