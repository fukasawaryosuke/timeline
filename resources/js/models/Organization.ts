import dayjs from 'dayjs';

/**
 * 組織単体
 */
export interface OrganizationInterface {
  id?: number;
  organization_name: string;
  level: number;
  display_order: number;
  division_cd: number;
  parent_division_cd: number;
  is_last_level: boolean;
  in_house_system_id: number;
  synced_at: string;
  created_at: string;
  updated_at: string;
}

export class OrganizationModel implements OrganizationInterface {
  public readonly id: number | undefined;
  public readonly organization_name: string;
  public readonly level: number;
  public readonly display_order: number;
  public readonly division_cd: number;
  public readonly parent_division_cd: number;
  public readonly is_last_level: boolean;
  public readonly in_house_system_id: number;
  public readonly synced_at: string;
  public readonly created_at: string;
  public readonly updated_at: string;

  constructor(data: Partial<OrganizationModel>) {
    this.id = data.id;
    this.organization_name = data.organization_name || '';
    this.level = data.level || 0;
    this.display_order = data.display_order || 0;
    this.division_cd = data.division_cd || 0;
    this.parent_division_cd = data.parent_division_cd || 0;
    this.is_last_level = data.is_last_level || false;
    this.in_house_system_id = data.in_house_system_id || 0;
    this.synced_at = data.synced_at
      ? dayjs(data.synced_at).format('YYYY/MM/DD HH:mm:ss')
      : '';
    this.created_at = data.created_at
      ? dayjs(data.created_at).format('YYYY/MM/DD HH:mm:ss')
      : '';
    this.updated_at = data.updated_at
      ? dayjs(data.updated_at).format('YYYY/MM/DD HH:mm:ss')
      : '';
  }
}

/**
 * 組織のリスト
 */
export interface OrganizationListInterface {
  list: OrganizationInterface[];
}

export type OrganizationListType = Array<OrganizationModel> | [];

export class OrganizationListModel implements OrganizationListInterface {
  public readonly list: OrganizationInterface[];

  constructor() {
    this.list = [];
  }

  init(data: OrganizationListType) {
    this.clear();
    this.list.push(
      ...data.map((d: OrganizationModel) => {
        return new OrganizationModel(d);
      })
    );
  }

  clear() {
    this.list.splice(0);
  }
}

/**
 * 組織のツリー
 */
export interface OrganizationTreeInterface extends OrganizationInterface {
  under_organisations: OrganizationTreeListModel;
}
export type OrganizationTreeType = {
  id: number;
  organization_name: string;
  level: number;
  display_order: number;
  division_cd: number;
  parent_division_cd: number;
  is_last_level: boolean;
  in_house_system_id: number;
  synced_at: string;
  created_at: string;
  updated_at: string;
  under_organisations: OrganizationTreeListType;
};

export class OrganizationTreeModel implements OrganizationTreeInterface {
  public readonly id: number | undefined;
  public readonly organization_name: string;
  public readonly level: number;
  public readonly display_order: number;
  public readonly division_cd: number;
  public readonly parent_division_cd: number;
  public readonly is_last_level: boolean;
  public readonly in_house_system_id: number;
  public readonly synced_at: string;
  public readonly created_at: string;
  public readonly updated_at: string;
  public readonly under_organisations: OrganizationTreeListModel;

  constructor(data: Partial<OrganizationTreeType>) {
    this.id = data.id;
    this.organization_name = data.organization_name || '';
    this.level = data.level || 0;
    this.display_order = data.display_order || 0;
    this.division_cd = data.division_cd || 0;
    this.parent_division_cd = data.parent_division_cd || 0;
    this.is_last_level = data.is_last_level || false;
    this.in_house_system_id = data.in_house_system_id || 0;
    this.synced_at = data.synced_at
      ? dayjs(data.synced_at).format('YYYY/MM/DD HH:mm:ss')
      : '';
    this.created_at = data.created_at
      ? dayjs(data.created_at).format('YYYY/MM/DD HH:mm:ss')
      : '';
    this.updated_at = data.updated_at
      ? dayjs(data.updated_at).format('YYYY/MM/DD HH:mm:ss')
      : '';
    this.under_organisations = new OrganizationTreeListModel();
    if (data.under_organisations) {
      this.under_organisations.init(data.under_organisations);
    }
  }
}

/**
 * 組織のツリーのリスト
 */
export interface OrganizationTreeListInterface {
  list: OrganizationTreeInterface[];
}

export type OrganizationTreeListType = Array<OrganizationTreeType> | [];

export class OrganizationTreeListModel implements OrganizationTreeListInterface {
  public readonly list: OrganizationTreeInterface[];

  constructor() {
    this.list = [];
  }

  init(data: OrganizationTreeListType) {
    this.clear();
    this.list.push(
      ...data.map((d: OrganizationTreeType) => {
        return new OrganizationTreeModel(d);
      })
    );
  }

  getOrganizationName(division_cd: number | null) {
    let result = '';
    for (let i = 0; i < this.list.length; i++) {
      if (this.list[i].division_cd === division_cd) {
        result = this.list[i].organization_name;
        break;
      } else {
        result =
          this.list[i].under_organisations.getOrganizationName(division_cd);
        if (result) {
          result = this.list[i].organization_name + ' ＞ ' + result;
          break;
        }
      }
    }
    return result;
  }

  hasChildren(division_cd: number | undefined) {
    let result = false;
    for (let i = 0; i < this.list.length; i++) {
      if (this.list[i].parent_division_cd === division_cd) {
        result = true;
        break;
      } else {
        result =
          this.list[i].under_organisations.hasChildren(division_cd) || result;
      }
    }
    return result;
  }

  clear() {
    this.list.splice(0);
  }
}
