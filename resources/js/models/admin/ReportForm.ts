import { REPORT_CONTENT_TYPE } from '/@/js/contarcts/ReportContentType';

/**
 * 報告フォームモデル
 */

/**
 * 報告フォーム
 */
export interface IReportForm {
  id: number;
  name: string;
  diary: IReportContents;
  required: boolean;
}

export class ReportForm implements IReportForm {
  public readonly id: number;
  public name: string;
  public diary: IReportContents;
  public required: boolean;
  public static readonly TYPE_TEXT: string = REPORT_CONTENT_TYPE.TEXT;
  public static readonly TYPE_TEXTAREA: string = REPORT_CONTENT_TYPE.TEXTAREA;
  public static readonly TYPE_CHOICE: string = REPORT_CONTENT_TYPE.CHOICE;
  public static readonly TYPE_SELECT: string = REPORT_CONTENT_TYPE.SELECT;
  public static readonly TYPE_DATE: string = REPORT_CONTENT_TYPE.DATETIME;
  public static readonly TYPE_TENKI: string = REPORT_CONTENT_TYPE.TENKI;
  public static readonly TYPE_IMAGE: string = REPORT_CONTENT_TYPE.IMAGE;
  public static readonly TYPE_MOVIE: string = REPORT_CONTENT_TYPE.MOVIE;
  constructor(res: IReportForm) {
    this.id = res.id || 0;
    this.name = res.name || '';
    this.diary = new ReportContents(res.diary || []);
    this.required = res.required ? true : false;
  }
  static getInstance() {
    return new ReportForm({
      id: 0,
      name: '',
      required: false,
      diary: ReportContents.getInstance(),
    });
  }
}

/**
 * 報告フォームコンテンツ
 */
export interface IReportContentsField {
  contents: Array<IReportContentsItem>;
  contentsIndex: number; // 質問識別用の値
}
export interface IReportContents extends IReportContentsField {
  addContents: (newContents: IReportContentsItem) => void;
  deleteContents: (index: number) => void;
  hasTitle: (title: string) => boolean;
  hasContents: () => boolean;
}

export class ReportContents implements IReportContents {
  public contents: Array<IReportContentsItem>;
  public contentsIndex: number;

  constructor(res: IReportContentsField) {
    this.contents = res.contents.map((r) => {
      return new ReportContentsItem(r);
    });
    this.contentsIndex = res.contentsIndex;
  }
  // コンテンツ(選択肢)を追加
  addContents(newContents: IReportContentsItem) {
    newContents.key = this.contentsIndex;
    this.contents.push(newContents);
    this.contentsIndex++;
  }
  // コンテンツ(選択肢)を削除
  deleteContents(index: number) {
    this.contents.splice(index, 1);
  }
  // コンテンツの中に同じタイトルのものがあるかどうか
  hasTitle(title: string) {
    const result = this.contents.filter((c) => c.title === title);
    return result.length > 1; // 2以上であれば重複あり
  }
  // コンテンツを持っている
  hasContents() {
    return this.contents.length > 0;
  }
  static getInstance() {
    return new ReportContents({
      contentsIndex: 0,
      contents: [],
    });
  }
}

/**
 * 報告フォームコンテンツアイテム
 */
export interface IReportContentsItemField {
  key: number; // 統計用のキー
  choicesIndex: number; // 選択肢識別用の値
  title: string;
  time?: Date;
  content?: string;
  checked?: Array<string>;
  path?: string | File;
  require: boolean;
  contentType: string;
  choices?: Array<IChoices>;
  reportVisible: boolean;
}

export interface IReportContentsItem extends IReportContentsItemField {
  convertContentType: () => string;
  addChoice: () => void;
  updateChoice: (index: number, value: string) => void;
  deleteChoice: (index: number) => void;
  reset: () => void;
  getCheckedTitle: () => string;
  getSelectTitle: () => string;
  hasSelected: () => boolean;
}
// 固定選択肢
interface contentsTypeMaster {
  contentType: string;
  title: string;
}
export interface IChoices {
  title: string;
  value: string;
}

export class ReportContentsItem implements IReportContentsItem {
  public readonly key: number;
  public choicesIndex: number;
  public title: string;
  public time?: Date;
  public content?: string;
  public checked?: Array<string>;
  public path?: string | File;
  public require: boolean;
  public contentType: string;
  public choices?: Array<IChoices>;
  public reportVisible: boolean;
  public static readonly CONTENTS_TYPE_MASTER: Array<contentsTypeMaster> = [
    {
      contentType: REPORT_CONTENT_TYPE.TEXT,
      title: 'テキスト',
    },
    {
      contentType: REPORT_CONTENT_TYPE.CHOICE,
      title: '複数選択',
    },
    {
      contentType: REPORT_CONTENT_TYPE.SELECT,
      title: '単数選択',
    },
    {
      contentType: REPORT_CONTENT_TYPE.DATETIME,
      title: '日付',
    },
    {
      contentType: REPORT_CONTENT_TYPE.TENKI,
      title: '天気',
    },
    {
      contentType: REPORT_CONTENT_TYPE.TEXTAREA,
      title: 'テキストエリア',
    },
    {
      contentType: REPORT_CONTENT_TYPE.IMAGE,
      title: '写真',
    },
    {
      contentType: REPORT_CONTENT_TYPE.MOVIE,
      title: '動画',
    },
  ]; // フォーム編集時のselect要素 TODO: DBでマスター持つ?

  constructor(res: IReportContentsItemField) {
    this.key = res.key;
    this.choicesIndex = res.choicesIndex;
    this.title = res.title;
    this.time = res.time ?? undefined;
    this.require = res.require;
    this.contentType = res.contentType;
    this.content = res.content ?? '';
    this.checked = res.checked ?? [];
    this.path = res.path ?? '';
    this.choices = res.choices ?? [];
    this.reportVisible = res.reportVisible;
  }
  convertContentType() {
    return ReportContentsItem.CONTENTS_TYPE_MASTER.filter(
      (c) => c.contentType === this.contentType
    )[0].title;
  }
  addChoice() {
    this.choices?.push({ title: '', value: String(this.choicesIndex) });
    this.choicesIndex++;
  }
  //
  updateChoice(index: number, data: string) {
    // "対象 &&"を先頭に置くことでundefinedではないことを明示
    if (this.choices && this.choices[index]) {
      this.choices[index].title = data;
    }
  }
  deleteChoice(index: number) {
    // "対象 &&"を先頭に置くことでundefinedではないことを明示
    if (this.choices && this.choices[index]) {
      this.choices.splice(index, 1);
    }
  }
  reset() {
    this.choices = [];
  }
  // 複数選択式の回答の各タイトルを返却
  getCheckedTitle() {
    const notSelect = '選択していない';
    if (this.checked!.length < 1) return notSelect;
    const checkedTitle: Array<string> = [];
    this.choices?.forEach((c) => {
      if (this.checked?.includes(c.value)) {
        checkedTitle.push(c.title);
      }
    });
    return checkedTitle.join(', ');
  }
  // 単一選択式の回答のタイトルを返却
  getSelectTitle() {
    const notSelect = '選択していない';
    if (!this.choices) return notSelect; // NOTE: この関数を呼んでいる時点で選択肢がないことはあり得ないので非考慮でいい
    for (let i = 0; i < this.choices.length; i++) {
      if (this.choices[i].value == this.content) {
        return this.choices[i].title;
      }
    }
    return notSelect;
  }
  //
  hasSelected() {
    // 単一選択・複数選択か
    if (
      this.contentType === REPORT_CONTENT_TYPE.CHOICE ||
      this.contentType === REPORT_CONTENT_TYPE.SELECT
    ) {
      // 選択肢が1以上ある
      if (this.choices && this.choices.length > 0) {
        const result = this.choices.filter((c) => {
          return c.title && c.title.trim() != '';
        });
        // 選択肢タイトルに入力がある
        if (result.length === this.choices.length) {
          return true;
        } else {
          return false;
        }
      } else {
        // 単一選択・複数選択だが選択肢が入力されていないのでfalse
        return false;
      }
    } else {
      // 単一選択・複数選択でないのでtrueを返却
      return true;
    }
  }
  static getInstance(contentsKey: number) {
    return new ReportContentsItem({
      key: contentsKey,
      choicesIndex: 0,
      title: '',
      require: false,
      contentType: '',
      content: '',
      path: '',
      reportVisible: false,
    });
  }
}

export default ReportForm;
