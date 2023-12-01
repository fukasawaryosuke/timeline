<script setup lang="ts">
import { ref } from 'vue';
import { InHouseSystem, LoginUser } from '/@/js/models/User';
import { OrganizationTreeListModel } from '/@/js/models/Organization';
import useOrganization from '/@/js/compositions/UseOrganization';
import SpOrganizationTree from '/@/js/components/home/main/sp/main/SpOrganizationTree.vue';

interface Props {
  loginUser: LoginUser;
  ihs: InHouseSystem[];
  orgzTreeList: OrganizationTreeListModel;
  selectedIhsId: number;
  selectedDivision: number[];
  snsChange: (val: number) => void;
  updateDivisionCodes: (cds: number[]) => void;
  updateHomeMode: (flg: boolean) => void;
}
const props = defineProps<Props>();
const selectedDivisionCode = ref<number>(0);
const divisionCodeChanged = (division_cd: number) => {
  selectedDivisionCode.value = division_cd;
};
const filterWithDivisionCd = () => {
  props.updateHomeMode(false);
  if (selectedDivisionCode.value) {
    props.updateDivisionCodes([selectedDivisionCode.value]);
  } else {
    props.updateDivisionCodes([]);
  }
  props.snsChange(0);
  // emits('close');
};
const { transDivisionCode } = useOrganization(divisionCodeChanged);
type DivisionLabel = {
  text: string;
  ids: number[];
};
const homeClicked = () => {
  props.updateHomeMode(false);
  console.log(props.loginUser.organization);
  props.updateDivisionCodes([props.loginUser.organization?.division_cd]);
  props.snsChange(0);
};
// const divisionLabels = computed(() => {
//   const labels: DivisionLabel[] = [];
//   if (props.ihs.length <= 0) return labels;
//   const targetIndex = props.ihs.findIndex((i) => {
//     return i.id == props.selectedIhsId;
//   });
//   if (targetIndex <= -1) return labels;
//   props.ihs[targetIndex].organization.forEach((o) => {
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
//   return labels.sort((l1, l2) => {
//     if (l1.text == 'その他') {
//       return 1;
//     }
//     if (l2.text == 'その他') {
//       return -1;
//     }
//     return 0;
//   });
// });
// const isSelected = computed(() => (ids: number[]) => {
//   const divisionResult = props.selectedDivision.every((d) => ids.includes(d));
//   const idsResult = ids.every((d) => props.selectedDivision.includes(d));
//   return divisionResult && idsResult;
// });
const divisionClicked = (cds: number[]) => {
  props.updateHomeMode(false);
  props.updateDivisionCodes(cds);
};
const selectOrg = () => {
  props.snsChange(0);
};
</script>
<template>
  <div class="show_contents">
    <div class="user_filter">
      <div class="inner" @click="homeClicked">
        <i class="fa fa-building fa_blank" aria-hidden="true"></i>
        所属部門を選択
      </div>
    </div>
    <!-- <div class="single_article">
      <div class="org_filter">
        <h3>部門選択{{ selectedDivisionCode }}</h3>
       <div>
          <div
            v-for="orgzTree in orgzTreeList.list"
            :key="`divition_${String(orgzTree.division_cd)}`"
            class="org_filter_btn"
            @click="select(orgzTree.division_cd)"
          >
            <span class="font-semibold">
              {{ orgzTree.organization_name }}
            </span>
          </div>
        <div
            v-for="org in divisionLabels"
            :key="`divition_${String(org.ids)}`"
            class="org_filter_btn"
            :class="isSelected(org.ids) ? 'filter_active' : ''"
            @click="divisionClicked(org.ids)"
          >
            <span class="font-semibold">
              {{ org.text }}
            </span>
          </div>
       </div>
      </div>
    </div> -->
    <SpOrganizationTree
      :orgz-tree-list="orgzTreeList"
      :changed="transDivisionCode"
    />
    <!-- <div class="single_article">
      <div class="org_filter">
        <h3>拠点選択</h3>
        <div class="org_filter_btn filter_active">拠点01</div>
        <div class="org_filter_btn">拠点01拠点01拠点01拠点01拠点01拠点01</div>
        <div class="org_filter_btn">部門01</div>
        <div class="org_filter_btn">部門01部門01部門01部門01</div>
      </div>
    </div> -->
    <div class="search_btn_area">
      <button class="send_btn" @click="filterWithDivisionCd">表示</button>
    </div>
  </div>
</template>
