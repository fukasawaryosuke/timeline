/**
 * 制度フォームモデル
 */

/**
 * 制度フォーム
 */
export interface IIhsFormField {
  id: number;
  system_name: string;
  description: string;
  icon_path: string;
  report_form_id: number;
  release_status: number;
  notification_setting: any[];
  div_cd: any[];
}
export interface IIhsForm extends IIhsFormField {
  getFormTitle: (formList: any[]) => string;
  getStatusStr: () => string;
}

class IhsForm implements IIhsForm {
  public readonly id: number;
  public system_name: string;
  public description: string;
  public icon_path: string;
  public report_form_id: number;
  public release_status: number;
  public div_cd: any[];
  public notification_setting: any[];

  constructor(res: IIhsFormField) {
    this.id = res.id;
    this.system_name = res.system_name;
    this.description = res.description;
    this.icon_path = res.icon_path;
    this.report_form_id = res.report_form_id;
    this.release_status = res.release_status;
    this.notification_setting = res.notification_setting;
    this.div_cd = [];
    for (let i = 0; i < this.notification_setting.length; i++) {
      this.div_cd.push(this.notification_setting[i].organization);
    }
  }

  getFormTitle(formList: any[]) {
    const target = formList.filter((f) => this.report_form_id == f.id);
    return target[0]?.name ?? '投稿フォームが選択されていません。';
  }

  getStatusStr() {
    return this.release_status === 1 ? '公開' : '非公開';
  }

  static getInstance() {
    return new IhsForm({
      id: 0,
      system_name: '',
      description: '',
      icon_path: '',
      report_form_id: 0,
      release_status: 0,
      div_cd: [],
      notification_setting: [],
    });
  }
}

export default IhsForm;
