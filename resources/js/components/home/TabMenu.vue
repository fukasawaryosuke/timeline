<template>
  <!-- 所属 -->
  <div class="w-full border-b bg-stone-50">
    <div class="flex flex-wrap w-full">
      <div
        class="w-full h-12 px-10 py-1 self-center items-center flex flex-shrink-0"
      >
        <span
          class="w-32 text-center font-semibold text-gray-400 hover:text-gray-600 cursor-pointer"
          @click="myDiaryClicked"
        >
          <fa
            icon="fa-user"
            class="text-gray-500 pl-3 hover:text-gray-700 w-4 h-4"
          />
          自分の投稿
        </span>
        <!-- ヒューマンアカデミー株式会社の5分日報の場合 -->
        <span
          v-if="selectedIhsId === firstIhsId"
          class="w-32 text-center font-semibold text-gray-400 ml-4 border-l-4 border-gray-500 hover:text-gray-600 cursor-pointer"
          @click="homeClicked"
        >
          <fa
            icon="fa-house"
            class="text-gray-500 pl-3 hover:text-gray-700 w-4 h-4"
          />
          所属部門
        </span>
        <!-- ヒューマンアカデミー株式会社以外の法人の場合 -->
        <span
          v-else-if="HUMAN_ACADEMY_COMPANY_CODE !== props.loginUser.organization?.company_code && props.loginUser.organization?.company_code != ''"
          class="w-32 text-center font-semibold text-gray-400 ml-4 border-l-4 border-gray-500 hover:text-gray-600 cursor-pointer"
          @click="homeClicked(); displayDivisionName()"
        >
          <fa
            icon="fa-house"
            class="text-gray-500 pl-3 hover:text-gray-700 w-4 h-4"
          />
          所属部門
        </span>
        <!-- ヒューマンアカデミー株式会社の5分日報の場合 -->
        <span
          v-if="selectedIhsId === firstIhsId"
          class="w-32 text-center font-semibold text-gray-400 ml-4 border-l-4 border-gray-500 hover:text-gray-600 cursor-pointer"
          @click="orgzModalOpen"
        >
          <fa icon="fa-building" class="text-gray-500 pl-3 w-4 h-4" />
          部門で表示
        </span>
        <!-- ヒューマンアカデミー株式会社以外の法人の場合 -->
        <span
          v-else-if="HUMAN_ACADEMY_COMPANY_CODE !== props.loginUser.organization?.company_code && props.loginUser.organization?.company_code != ''"
          class="w-32 text-center font-semibold text-gray-400 ml-4 border-l-4 border-gray-500 hover:text-gray-600 cursor-pointer"
          @click="orgzModalOpen"
        >
          <fa icon="fa-building" class="text-gray-500 pl-3 w-4 h-4" />
          部門で表示
        </span>
        <div class="search-user ml-auto">
          <div class="search-frame">
            <input type="text" placeholder="ユーザー名" class="border inp_area" v-model="searchKeyword" @input="keywordCount" @keydown="handleKeyDown">
            <fa icon="fa-search" class="search-button" @click="selectedUser(searchKeyword)"></fa>
          </div>
          <ul id="searchResults" class="search-results" v-if="search_flg" ref="userList">
            <li class="name" v-for="(user, index) in filteredItems" :key="user.id" :class="{ selected: index === selectedItem }" @click="clickName(user.user_name)">{{ user.user_name }}</li>
          </ul>
        </div>
      </div>

      <HomeMenu
        :view-mode="swtichReportView"
        :selected-user-name="selectedUserName"
        :home-clicked="homeClicked"
        :division-labels="divisionLabels"
        :selected-ihs-id="selectedIhsId"
        :selected-ihs-reset="selectedIhsReset"
        :system-name="systemName"
        :selected-division-name="selectedDivisionName"
        :selected-division-reset="selectedDivisionReset"
        :selection-display-division-name="selectionDisplayDivisionName"
        :selection-display-division-reset="selectionDisplayDivisionReset"
        :watch-division-check-flg="watchDivisionCheckFlg"
        @change-view="changeView"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue';
import { LoginUser, InHouseSystem, Organization } from '../../models/User';
import HomeMenu from '/@/js/components/home/main/HomeMenu.vue';
import { OrganizationTreeListModel } from '../../models/Organization';
import axios from 'axios';
import useAxios from '/@/js/compositions/UseAxios';

const { axiosErrorHandling, errorToast } =useAxios();

interface Props {
  loginUser: LoginUser;
  ihs: InHouseSystem[];
  organizations: OrganizationTreeListModel;
  selectedDivision: number[];
  swtichReportView: number;
  selectedUserName: string;
  selectedIhsId: number;
  systemName: string;
  updateDivisionCodes: (cds: number[]) => void;
  updateHomeMode: (flg: boolean) => void;
  selectedUserIsMe: () => void;
  changeView: (flg: number) => void;
  updateIhs: (id: number, name: string) => void;
  resetIhs: () => void;
  resetDivision: () => void;
  orgzModalOpen: () => void;
  selectedUser: (path: string) => void;
  selectedUserOnlyDiary: (index: number) => void;
}
const props = defineProps<Props>();

const HUMAN_ACADEMY_COMPANY_CODE = process.env.MIX_HUMAN_ACADEMY_COMPANY_CODE;

/**
 * division_cdからorganization_name取得
 * @param selectDivisionCode 
 */
const getMappingDivisionName = async (selectDivisionCode: number[]) => {
  let mappingDivisionName = '';
  //division_cdからorganization_name取得
  await axios
    .get('/api/organization_name', {
      params: {
        division_cd: selectDivisionCode,
      },
    })
    .then((res) => {
      mappingDivisionName = res.data.organization_name ?? '';
    })
    .catch((err) => {
      const result = axiosErrorHandling(err);
      errorToast(result.code, result.message);
    });

  return mappingDivisionName;
};
// 初回マウントチェック用にフラグを生成、true = watch検知済みとして表示する・しないをチェック
const watchDivisionCheckFlg = ref<boolean>(false);
onMounted(() => {
  watchDivisionCheckFlg.value = false;
});

/**
 * 所属部門の表示名を更新する
 */
watch(
  () => props.selectedDivision,
  async () => {
    if (props.selectedDivision.length <= 0) {
      selectionDisplayDivisionName.value = '';
      return;
    }
    let mappingedDivisionName = await getMappingDivisionName(props.selectedDivision);
    selectionDisplayDivisionName.value = mappingedDivisionName;

    // 初回マウント時には空にするために下記の処理を追加
    if (!watchDivisionCheckFlg.value) {
      selectionDisplayDivisionName.value = '';
      watchDivisionCheckFlg.value = true;
    }
  },
  { deep: true},
);

type DivisionLabel = {
  text: string;
  ids: number[];
};
const selectedDivisionName = computed(() => {
  if (props.ihs.length <= 0) return '';
  const targetIndex = props.ihs.findIndex((i) => {
    return i.id == props.selectedIhsId;
  });
  if (targetIndex <= -1) return '';
  const orgz = props.ihs[targetIndex].organization.find((o) => {
    return props.selectedDivision.includes(o.division_cd);
  });
  if (orgz) {
    return orgz.organization_name;
  }
  return '';
});
const divisionLabels = computed(() => {
  const labels: DivisionLabel[] = [];
  if (props.ihs.length <= 0) return labels;
  const targetIndex = props.ihs.findIndex((i) => {
    return i.id == props.selectedIhsId;
  });
  if (targetIndex <= -1) return labels;
  props.ihs[targetIndex].organization.forEach((o) => {
    let i = labels.findIndex((l) => {
      return l.text == o.organization_name;
    });
    if (i > -1) {
      labels[i].ids.push(o.division_cd);
    } else {
      labels.push({
        text: o.organization_name,
        ids: [o.division_cd],
      });
    }
  });
  // const divisionLabels = computed(() => {
  //   const labels: DivisionLabel[] = [];
  //   props.organizations.forEach((o) => {
  //     let i = labels.findIndex((l) => {
  //       return l.text == o.organization_name;
  //     });
  //     if (i > -1) {
  //       labels[i].ids.push(o.division_cd);
  //     } else {
  //       labels.push({
  //         text: o.organization_name,
  //         ids: [o.division_cd],
  //       });
  //     }
  //   });

  return labels.sort((l1, l2) => {
    if (l1.text == 'その他') {
      return 1;
    }
    if (l2.text == 'その他') {
      return -1;
    }
    return 0;
  });
});

// const ihsClicked = (id: number) => {
//   props.updateIhs(id);
//   props.updateDivisionCodes([props.loginUser.organization?.division_cd]);
//   props.updateHomeMode(false);
// };
// const divisionClicked = (cds: number[]) => {
//   props.updateHomeMode(false);
//   props.updateDivisionCodes(cds);
// };
const selectionDisplayDivisionName = ref<string>('');
const displayDivisionName = () => {
  selectionDisplayDivisionName.value = props.loginUser.organization?.organization_name;
};
const selectionDisplayDivisionReset = () => {
  selectionDisplayDivisionName.value = '';
  selectedDivisionReset();
};
const homeClicked = () => {
  props.updateHomeMode(false);
  props.updateDivisionCodes([props.loginUser.organization?.division_cd]);
};
const myDiaryClicked = () => {
  props.updateDivisionCodes([]);
  props.updateHomeMode(true);
  props.selectedUserIsMe();
};
const selectedIhsReset = () => {
  props.resetIhs();
};
const selectedDivisionReset = () => {
  props.resetDivision();
};
// NOTE: 既存データで異なるidだが同じ所属というものがあるため配列の状態で管理
// 例 division_cd: [100, 103] 100は営業部, 103も営業部として扱うようなイメージ
const isSelectedIhs = computed(() => (id: number) => {
  return props.selectedIhsId === id;
});
const isSelected = computed(() => (ids: number[]) => {
  const divisionResult = props.selectedDivision.every((d) => ids.includes(d));
  const idsResult = ids.every((d) => props.selectedDivision.includes(d));
  return divisionResult && idsResult;
});

//５分日報
const firstIhsId = 1;

//selectedUser関数をHome.vueから受け取る

interface UserName {
  id: number;
  user_name: string;
}

const user_name_list = ref<UserName[]>([]);
  const search_flg = ref<boolean>(false);

onMounted(() => {
  axios.get('/api/users/user_name').then((res) => {
    user_name_list.value = res.data;
  });
});

const searchKeyword = ref<string>('');
const filteredItems = computed(() => {
  const filteredList = user_name_list.value.filter(item => item.user_name.includes(searchKeyword.value));
  return filteredList.slice(0, 5); // 最大5件に制限
});


const user_data = ref<UserName[]>([]);
const err_flg = ref<boolean>(false);

const selectedUser = (name: string) => {
  user_data.value = user_name_list.value.filter((item) => item.user_name == name);
  if (user_data.value.length == 0) {
    err_flg.value = true;
    return;
  }else{
    props.selectedUser(name);
    //idを送る
    props.selectedUserOnlyDiary(user_data.value[0].id);
    err_flg.value = false;
    search_flg.value = false;
    searchKeyword.value = '';
    selectedItem.value = -1;
  }

};

const clickName = (name:string)=>{0
  search_flg.value = false;
  searchKeyword.value = name;
  selectedUser(name);
  return
}

const keywordCount = () => {
  if (searchKeyword.value.length == 0) {
    search_flg.value = false;
  } else {
    search_flg.value = true;
  }
};

const selectedItem = ref<number>(-1);

const handleKeyDown = (event: KeyboardEvent) => {
  if (event.key === 'ArrowDown') {
    if (selectedItem.value < filteredItems.value.length - 1) {
      selectedItem.value++;
    }
  } else if (event.key === 'ArrowUp') {
    if (selectedItem.value > 0) {
      selectedItem.value--;
    }
  } else if (event.key === 'Enter') {
    if (selectedItem.value >= 0 && selectedItem.value < filteredItems.value.length) {
      const user = filteredItems.value[selectedItem.value];
      searchKeyword.value = user.user_name;
      selectedUser(searchKeyword.value);
    }
  }
};
</script>

<style scoped>
input::-webkit-calendar-picker-indicator{
  opacity:0;
}
.err_msg{
  color: red;
  font-size: 12px;
}

.search-results {
  position: absolute;
  width: 238px;
  background-color: white;
  border: 1px solid #ddd;
  border-radius: 4px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  display: block;
  z-index: 999;
}

.search-results li {
  list-style-type: none;
  padding-left: 4px;
  height: 28px;
}

.search-results li:hover {
  background-color: #f2f2f2;
  cursor: pointer;
}
.search-frame{
  position: relative;
  display: inline-block;
}
.search-button{
  width: 24px;
  height: 24px;
  position: absolute;
  right: 2px;
  top: 8px;
  cursor: pointer;
}
.inp_area{
  height: 40px;
  width: 240px;
  padding: 5px;
}

.selected {
  background-color: blue;
  color: white;
}
</style>

