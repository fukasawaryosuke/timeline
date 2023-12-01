import { ref } from 'vue';
import { OrganizationTreeListModel } from '/@/js/models/Organization';

const useOrganization = (changed: (cd: number) => void) => {
  // 選択している部門コード
  const selectedCode = ref<number>(0);

  // 部門選択時
  const select = (divCd: number | undefined) => {
    if (!divCd) {
      selectedCode.value = 0;
    } else if (selectedCode.value == divCd) {
      selectedCode.value = 0;
    } else {
      selectedCode.value = divCd;
    }
    // 変更を通知
    changed(selectedCode.value);
  };

  // 選択部門の変更を通知
  const transDivisionCode = (divCd: number) => {
    if (divCd) {
      changed(divCd);
    } else {
      changed(selectedCode.value);
    }
  };

  // 選択している部門を取得
  const getSelectedOrganization = (orgzTreeList: OrganizationTreeListModel) => {
    if (!selectedCode.value) {
      return;
    }
    const foundOrgz = orgzTreeList.list.find((orgzTree) => {
      return orgzTree.division_cd == selectedCode.value;
    });

    if (!foundOrgz) {
      selectedCode.value = 0;
    }

    return foundOrgz;
  };

  // 部門コードから部門を取得
  const getOrganization = (
    orgzTreeList: OrganizationTreeListModel,
    code: number
  ) => {
    const foundOrgz = orgzTreeList.list.find((orgzTree) => {
      return orgzTree.division_cd == code;
    });

    if (!foundOrgz) {
      selectedCode.value = 0;
    }
    console.log(code);
    console.log(orgzTreeList);
    return foundOrgz;
  };

  const isSelected = (divCd: number) => {
    return divCd == selectedCode.value;
  };

  // 下部組織があるか
  const hasUnderOrganizations = (orgzTreeList: OrganizationTreeListModel) => {
    const orgz = getSelectedOrganization(orgzTreeList);
    if (!orgz) {
      return false;
    }
    return orgz.under_organisations.list.length > 0;
  };

  // 下部組織を取得
  const getUnderOrganizations = (orgzTreeList: OrganizationTreeListModel) => {
    const orgz = getSelectedOrganization(orgzTreeList);
    if (!orgz) {
      return new OrganizationTreeListModel();
    }
    return orgz.under_organisations;
  };

  // 組織階層のラベルを取得
  const getLevelLavel = (parentLevel?: number) => {
    if (!parentLevel) {
      return '第1階層';
    }
    return '第' + parentLevel + '階層';
  };


  return {
    selectedCode,
    select,
    transDivisionCode,
    isSelected,
    getSelectedOrganization,
    hasUnderOrganizations,
    getUnderOrganizations,
    getLevelLavel,
    getOrganization,
  };
};

export default useOrganization;
