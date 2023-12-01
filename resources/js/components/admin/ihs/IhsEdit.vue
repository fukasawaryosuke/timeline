<script lang="ts" setup>
import { onMounted, reactive, ref } from 'vue';
import { IIhsForm } from '/@/js/models/admin/IhsForm';
import ConfirmModal from '/@/js/components/modal/ConfirmModal.vue';
import OrganizationModal from '../modal/OrganizationModal.vue';
import { OrganizationTreeListModel } from '../../../models/Organization';
import axios from 'axios';
import useAxios from '/@/js/compositions/UseAxios';

interface Props {
  ihs: IIhsForm;
  formList: any[];
  edit: boolean;
  btnStr: string;
}
const props = defineProps<Props>();
interface Emits {
  (e: 'changeForm', event: any): void;
  (e: 'updateIhsData', event: any, key: string): void;
  (e: 'updateStatus', value: number): void;
  (e: 'uploadFile', event: any): void;
  (e: 'deleteFile'): void;
  (e: 'confirmIhs'): void;
  (e: 'deleteIhs'): void;
}
defineEmits<Emits>();

const clickInputFile = () => {
  document.getElementById('inputfile')?.click();
};

const deleteModalVisible = ref<boolean>(false);

const deleteModalOpen = () => {
  deleteModalVisible.value = !deleteModalVisible.value;
};
const deleteModalClose = () => {
  deleteModalVisible.value = false;
};
const organizations = reactive<OrganizationTreeListModel>(
  new OrganizationTreeListModel()
);
const orgzModalVisible = ref<boolean>(false);
const { axiosErrorHandling, errorToast } = useAxios();
const orgzModalOpen = () => {
  orgzModalVisible.value = true;
};

const orgzModalClose = () => {
  orgzModalVisible.value = false;
};

const parents_flg = ref<boolean>(false);
const selectOrg = (org: any) => {
  const existingDivisions = props.ihs.div_cd.map((item: any) => item.division_cd);
  if (!existingDivisions.includes(org)) {
  axios.get('/api/organization_div'
  , {
      params: {
        division_cd: org,
        select_division_cd: props.ihs.div_cd
      }
    })
    .then((res) => {
      console.log(res.data);
      props.ihs.div_cd = res.data;
      parents_flg.value = parentsCheck(props.ihs.div_cd);
    })
  }else{
    console.log('重複');
  }
};

const parentsCheck = (orgLists:any): boolean=>{
  // console.log(orgLists);

  // 親子関係で選択しているか
  let parents_flg = false;

  // 選択した組織のリストを２重でループして親子関係をチェックする
  for(let i = 0; i < orgLists.length; i++){

    // 対象の組織
    const targetOrg = orgLists[i];
    targetOrg.parents_flg = false;

    for (let j = 0; j < orgLists.length; j++) {

      // 対象の組織と比較する組織
      let checkOrg = orgLists[j];

      if (targetOrg.division_cd === checkOrg.division_cd) {
        // 同じ組織は比較しない
        continue;
      }

      while (checkOrg.upper_organisation) {
        // 親子関係にある場合はフラグを立てる
        if (checkOrg.upper_organisation.division_cd === targetOrg.division_cd) {
          // チェックの結果
          parents_flg = true;
          // 組織に設定されているフラグ
          targetOrg.parents_flg = true;
        }
        checkOrg = checkOrg.upper_organisation;
      }
    }
  }
  return parents_flg;
}

const orgDelete = (divCd: number) => {
  props.ihs.div_cd = props.ihs.div_cd.filter((org) => org.division_cd !== divCd);
  parents_flg.value = parentsCheck(props.ihs.div_cd);
}

const selectOrgLabel = (org: any) => {
  let result = [] as any[];
  let orgForLabel = org;
  while (orgForLabel.upper_organisation !== null) {
    result.push(orgForLabel.organization_name);
    orgForLabel = orgForLabel.upper_organisation;
  }
  return result.reverse().join(" ＞ ");
};

onMounted(() => {
  axios
    .get('/api/organization')
    .then((res) => {
      organizations.init(res.data);
    })
    .catch((err) => {
      const result = axiosErrorHandling(err);
      errorToast(result.code, result.message);
    });

})
</script>

<template>
  <ConfirmModal
    :modal-open="deleteModalVisible"
    confirm-label="削除"
    message="カテゴリを削除します。よろしいですか？"
    sub-message="このカテゴリの日報は過去分含め全て削除されます。"
    @event="$emit('deleteIhs')"
    @close="deleteModalClose"
  />
  <!-- <div class="main_header flex_end">
    <button class="yellowbtn btn" @click="$emit('confirmIhs')">
      <i class="fa fa-floppy-o fa_blank" aria-hidden="true"></i>
      {{ btnStr }}
    </button>
  </div>
  <div class="main_area"> -->
  <div class="col2_50">
    <div class="input_box">
      <label>
        カテゴリ名
        <span class="redtxt smalltxt">（必須）</span>
        <input
          type="text"
          placeholder="カテゴリ"
          :value="ihs.system_name"
          maxlength="30"
          @input="$emit('updateIhsData', $event, 'system_name')"
        />
      </label>
    </div>
    <div class="input_box">
      <label>
        紹介文
        <textarea
          placeholder="紹介文"
          :value="ihs.description"
          @input="$emit('updateIhsData', $event, 'description')"
        ></textarea>
      </label>
    </div>
    <div class="input_box">
      <label>アイコン</label>
      <button class="bluebtn btn" @click="clickInputFile">
        <i class="fa fa-file-image fa_blank" aria-hidden="true"></i>
        ファイルを選択
      </button>
      <input
        id="inputfile"
        accept="image/*"
        type="file"
        @change="$emit('uploadFile', $event)"
      />
    </div>
    <div v-if="ihs.icon_path" class="input_box iconbox">
      <img class="iconimg" :src="ihs.icon_path" alt="icon" />
      <i
        class="fa fa-trash"
        aria-hidden="true"
        @click="$emit('deleteFile')"
      ></i>
    </div>
    <div class="input_box">
      <label>
        投稿フォーム
        <span class="redtxt smalltxt">（必須）</span>
      </label>
      <div class="select_wrap">
        <select
          :value="ihs.report_form_id"
          @change="$emit('changeForm', $event)"
        >
          <option
            v-for="(f, i) in formList"
            :key="`formList${String(i)}`"
            :value="f.id"
          >
            {{ f.name }}
          </option>
        </select>
      </div>
    </div>
    <div class="input_box">
      <label>ステータス</label>
      <form>
        <input
          type="radio"
          :value="1"
          :checked="ihs.release_status === 1"
          @change="$emit('updateStatus', 1)"
        />
        公開
        <input
          type="radio"
          :value="0"
          :checked="ihs.release_status === 0"
          @change="$emit('updateStatus', 0)"
        />
        非公開
      </form>
    </div>
    <div class="input_box">
      <label>組織選択</label>
      <button class="org_button" @click="orgzModalOpen">組織選択</button>
      <div class="selectOrgArea" v-if="props.ihs.div_cd.length > 0">
        <p v-if="parents_flg" class="note_message">※選択された組織より限定された組織が設定されています。<br>通知は限定された組織に送信されます。</p>
      <ul class="org_ul">
        <li class="org_list" :class="[selectOrg.parents_flg ? 'note' : '']" v-for="(selectOrg, index) in props.ihs.div_cd" :key="index" :value="selectOrg.division_cd">{{ selectOrgLabel(selectOrg) }}<fa icon="fa-xmark" class="xmark" @click="orgDelete(selectOrg.division_cd)"/></li>
      </ul>
    </div>
    </div>
    <div v-if="edit" class="main_header flex_end">
      <button class="redbtn btn" @click="deleteModalOpen">
        <i class="fa fa-trash fa_blank" aria-hidden="true"></i>
        削除
      </button>
    </div>
  </div>

<OrganizationModal
    :orgz-tree-list="organizations"
    :orgz-modal-open="orgzModalVisible"
    @close="orgzModalClose"
    @select="selectOrg"
/>

</template>

<style scoped>
#inputfile {
  display: none;
}
.iconbox {
  display: flex;
  flex-direction: row;
  align-items: center;
}
.iconbox i {
  cursor: pointer;
}
.iconimg {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  margin-right: 8px;
}

.org_button{
  color: #06B6D4;
  border: 1px solid #06B6D4;
  padding: 4px 17px;
  border-radius: 30px;
  background-color: white;
  margin-right: 15px;
}

.org_ul{
  display: flex;
  flex-wrap: wrap;
}

.org_list{
  border: 1px solid #00B0E3;
  border-radius: 45px;
  font-weight: initial;
  padding: 4px 14px;
  background: #00B0E3;
  color: white;
  padding-left: 0.75rem;
  padding-right: 0.75rem;
  padding-top: 0.25rem;
  padding-bottom: 0.25rem;
  margin-left: 0.25rem;
  margin-top: 0.75rem;
  margin-bottom: 0.75rem;
  cursor: default;
}
.xmark{
  margin-left: 0.5rem;
  cursor: pointer;
}
.selectOrgArea{
  width: auto;
  margin-top: 5px;
}
.note{
  border: 1px solid red;
  border-radius: 45px;
  font-weight: initial;
  padding: 4px 14px;
  background: red;
  color: white;
  padding-left: 0.75rem;
  padding-right: 0.75rem;
  padding-top: 0.25rem;
  padding-bottom: 0.25rem;
  margin-left: 0.25rem;
  margin-top: 0.75rem;
  margin-bottom: 0.75rem;
  cursor: default;
}

.note_message{
  color: red;
}
</style>
