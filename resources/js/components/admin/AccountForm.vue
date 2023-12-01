<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import SideMenu from '/@/js/components/admin/SideMenu.vue';
import AccountEdit from '/@/js/components/admin/account/AccountEdit.vue';
import { OrganizationTreeListModel } from '/@/js/models/Organization';
import { AccountService } from '/@/js/utils/AccountService';
import { OrganizationService } from '/@/js/utils/OrganizationService';

const route = useRoute();
const router = useRouter();

const props = defineProps<{
  isHumanAcademy: boolean;
}>();

const accountId = Number(route.params.id);
const isEdit = ref<boolean>(accountId ? true : false);

const accountService = new AccountService();
const organizationService = new OrganizationService();

//NOTE:既存の実装を流用
const organizationTreeList = reactive<OrganizationTreeListModel>(
  new OrganizationTreeListModel()
);

onMounted(async () => {
  if (isEdit.value) {
    await getAccount(accountId);
  }

  await getOrganizationTreeList();
});

//アカウント取得
const getAccount = async (account_id: number): Promise<void> => {
  const user_id = account_id;

  await accountService.getAccount(user_id).catch(toAccountList);
};

// アカウント登録
const postAccount = async (formData: FormData): Promise<void> => {
  await accountService.postAccount(formData).then(toAccountList);
};

//アカウント更新
const putAccount = async (formData: FormData): Promise<void> => {
  await accountService.putAccount(formData).then(toAccountList);
};

//アカウント更新
const resetPassword = async (account_id: number): Promise<void> => {
  const userId = account_id;

  await accountService.resetPassword(userId).then(toAccountList);
};

//部門ツリー取得
const getOrganizationTreeList = async (): Promise<void> => {
  await organizationService
    .getOrganizationTreeList()
    .then(() =>
      organizationTreeList.init(organizationService.organizationTreeList)
    );
};

//画面遷移
const toAccountList = (): void => {
  router.push({ name: 'AccountList' });
};
</script>

<template>
  <div class="main">
    <SideMenu />
    <AccountEdit
      :account-data="accountService.accountData"
      :organization-tree-list="organizationTreeList"
      :is-edit="isEdit"
      :is-human-academy="props.isHumanAcademy"
      @put_account="putAccount"
      @post_account="postAccount"
      @reset_password="resetPassword"
    />
  </div>
</template>
