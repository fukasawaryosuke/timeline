<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import useAxios from '/@/js/compositions/UseAxios';
import SideMenu from '/@/js/components/admin/SideMenu.vue';
import LaravelPaginate, { IPaginate } from '/@/js/models/admin/Paginate';

const { axiosErrorHandling, asyncMethodLock, errorToast } = useAxios();

const route = useRoute();
const router = useRouter();

const accounts = reactive<any[]>([]);
const organizations = reactive<any[]>([]);
const searchVal = ref<string>('');
const isActive = ref<number | null>(null);
const division = ref<string>('');
const paginate = ref<IPaginate>({} as IPaginate);
const listInitFlag = ref<boolean>(true);

onMounted(async () => {
  await getAccounts();
  listInitFlag.value = false;
});

const getAccounts = async () => {
  await asyncMethodLock(async () => {
    await axios
      .get('/api/account/list', {
        params: {
          name: route.query.name,
          page: route.query.page,
          division: route.query.division,
          isActive: route.query.isActive,
        },
      })
      .then((res) => {
        listInit();
        accounts.push(...res.data.data.account.data);
        organizations.push(...res.data.data.organization);
        paginate.value = new LaravelPaginate(res.data.data.account);
      })
      .catch((err) => {
        const result = axiosErrorHandling(err);
        errorToast(result.code, result.message);
      });
  });
};

const updatePermission = async (e: any, id: number | string, index: number) => {
  await asyncMethodLock(async () => {
    await axios
      .put(`/api/account/permissoin/${id}`)
      .then((res) => {
        accounts[index].systemRoleId = res.data.data;
        e.target.checked = res.data.data === 2;
      })
      .catch((err) => {
        const result = axiosErrorHandling(err);
        errorToast(result.code, result.message);
        e.target.checked = !e.target.checked;
      });
  });
};

const searchoAccount = async () => {
  await router.push({
    name: 'AccountList',
    query: {
      page: 1,
      name: searchVal.value,
      division: division.value,
      isActive: isActive.value,
    },
  });
  await getAccounts();
};

const resetAccount = async () => {
  searchVal.value = '';
  division.value = '';
  isActive.value = null;
  await router.push({
    name: 'AccountList',
  });
  await getAccounts();
};

const movePage = async (page: number) => {
  await router.push({
    name: 'AccountList',
    query: {
      page: page,
      name: searchVal.value,
      division: division.value,
      isActive: isActive.value,
    },
  });
  await getAccounts();
};

const listInit = () => {
  accounts.length = 0;
  organizations.length = 0;
};

const toCreateForm = () => {
  router.push({ name: 'AccountCreate' });
};

const toEditForm = (id: number) => {
  router.push({
    name: 'AccountEdit',
    params: {
      id: id,
    },
  });
};

const displayIsActive = (is_active: number) => {
  return is_active ? '有効' : '無効';
};
</script>

<template>
  <div class="modal">モーダル</div>
  <div class="main">
    <SideMenu />
    <div class="content">
      <div class="flex-content">
        <h1>アカウント一覧</h1>
        <div class="inner_content">
          <div class="main_header">
            <div class="search_flex">
              <div class="name_input">
                <input
                  type="text"
                  :value="searchVal"
                  placeholder="氏名またはメールアドレス"
                  @input="searchVal = $event.target.value"
                />
              </div>

              <div class="select_wrap">
                <select v-model="isActive">
                  <option value="null">ステータス</option>
                  <option value="1">有効</option>
                  <option value="0">無効</option>
                </select>
              </div>

              <div class="select_wrap">
                <select v-model="division">
                  <option value="">所属部門</option>
                  <option
                    v-for="organization in organizations"
                    :key="organization.division_cd"
                    :value="organization.organization_name"
                  >
                    {{ organization.organization_name }}
                  </option>
                </select>
              </div>

              <button class="bluebtn btn" @click="searchoAccount">
                <i class="fa fa-search fa_blank" aria-hidden="true"></i>
                検索
              </button>
              <button class="redbtn btn" @click="resetAccount">
                <i class="fa fa-times fa_blank" aria-hidden="true"></i>
                リセット
              </button>
              <button class="yellowbtn btn" @click="toCreateForm">
                <i class="fa fa-plus fa_blank" aria-hidden="true"></i>
                新規作成
              </button>
            </div>
          </div>
          <div
            v-if="accounts.length === 0 && listInitFlag == false"
            class="main_area"
          >
            表示できる項目がありません。
          </div>
          <div v-else class="main_area">
            <table>
              <tbody>
                <tr>
                  <th>No.</th>
                  <th>氏名</th>
                  <th>メールアドレス</th>
                  <th>所属部門</th>
                  <th>ステータス</th>
                  <th>管理画面アクセス</th>
                </tr>
                <tr v-for="(account, i) in accounts" :key="`account${i}`">
                  <td>{{ account.id }}</td>
                  <td>
                    <a @click="toEditForm(account.id)">
                      {{ account.userName }}
                    </a>
                  </td>
                  <td>{{ account.mailadd }}</td>
                  <td>
                    {{ account.organization?.organization_name ?? 'nothing' }}
                  </td>
                  <td>{{ displayIsActive(account.isActive) }}</td>
                  <td>
                    <input
                      type="checkbox"
                      :value="account.systemRoleId"
                      :checked="account.systemRoleId === 2"
                      :disabled="account.isInitialUser === 1"
                      @change="updatePermission($event, account.id, i)"
                    />
                  </td>
                </tr>
              </tbody>
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
