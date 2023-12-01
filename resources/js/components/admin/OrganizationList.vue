<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import useAxios from '/@/js/compositions/UseAxios';
import SideMenu from '/@/js/components/admin/SideMenu.vue';
import LaravelPaginate, { IPaginate } from '/@/js/models/admin/Paginate';

const { axiosErrorHandling, errorToast } = useAxios();

const route = useRoute();
const router = useRouter();

const organizations = reactive<any[]>([]);
const searchName = ref<string>('');
const isActive = ref<number | null>(null);
const paginate = ref<IPaginate>({} as IPaginate);
const listInitFlag = ref<boolean>(true);

onMounted(async () => {
  await getOrganizations();
  listInitFlag.value = false;
});

const getOrganizations = async () => {
  await axios
    .get('/api/organization/list', {
      params: {
        name: route.query.name,
        page: route.query.page,
        isActive: route.query.isActive,
      },
    })
    .then((res) => {
      listInit();
      organizations.push(...res.data.data);
      paginate.value = new LaravelPaginate(res.data);
    })
    .catch((err) => {
      const result = axiosErrorHandling(err);
      errorToast(result.code, result.message);
    });
};

const listInit = () => {
  organizations.length = 0;
};

const toCreateForm = () => {
  router.push({ name: 'OrganizationCreate' });
};

const toEditForm = (id: number) => {
  router.push({
    name: 'OrganizationEdit',
    params: {
      id: id,
    },
  });
};

const searchForm = async () => {
  await router.push({
    name: 'OrganizationList',
    query: { name: searchName.value, isActive: isActive.value },
  });
  await getOrganizations();
};

const resetForm = async () => {
  searchName.value = '';
  await router.push({ name: 'OrganizationList' });
  await getOrganizations();
};

const movePage = async (page: number) => {
  await router.push({
    name: 'OrganizationList',
    query: {
      page: page,
      name: searchName.value,
      isActive: isActive.value,
    },
  });
  await getOrganizations();
};

const displayIsActive = (is_active: number) => {
  return is_active ? '有効' : '無効';
};
</script>

<template>
  <!-- <div class="modal">モーダル</div> -->
  <div class="main">
    <SideMenu />
    <div class="content">
      <div class="flex-content">
        <h1>部門リスト</h1>
        <div class="inner_content">
          <div class="main_header">
            <div class="search_flex">
              <div class="name_input">
                <input v-model="searchName" type="text" placeholder="法人名" />
              </div>

              <div class="select_wrap">
                <select v-model="isActive">
                  <option value="null">ステータス</option>
                  <option value="1">有効</option>
                  <option value="0">無効</option>
                </select>
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
            <button class="yellowbtn btn" @click="toCreateForm">
              <i class="fa fa-plus fa_blank" aria-hidden="true"></i>
              新規作成
            </button>
          </div>

          <div
            v-if="organizations.length === 0 && listInitFlag == false"
            class="main_area"
          >
            表示できる項目がありません。
          </div>
          <div v-else class="main_area">
            <table>
              <tr>
                <th>No.</th>
                <th>部門コード</th>
                <th>部門名</th>
                <th>親部門</th>
                <th>ステータス</th>
                <th>作成日</th>
                <th>更新日</th>
              </tr>
              <tr v-for="(organization, i) in organizations" :key="`form${i}`">
                <td>{{ organization.id }}</td>
                <td>{{ organization.divisionCd }}</td>
                <td>
                  <a @click="toEditForm(organization.id)">
                    {{ organization.organizationName }}
                  </a>
                </td>
                <td>{{ organization.parentDivisionCd }}</td>
                <td>{{ displayIsActive(organization.isActive) }}</td>
                <td>{{ organization.createdAt }}</td>
                <td>{{ organization.updatedAt }}</td>
              </tr>
            </table>
            <div class="pager">
              <ul class="pagination">
                <template
                  v-for="(link, i) in paginate.links"
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
                      :class="link.active ? 'active' : ''"
                      @click="movePage(Number(link.label))"
                    >
                      <span>{{ link.label }}</span>
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
