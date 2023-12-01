import dayjs from "dayjs";

export interface HistoryInterface {
  human_id: string;
  name: string;
  browsed_at: string;
}

export interface HistoryListInterface {
  contents: HistoryInterface[];
}

export type HistoryListType = Array<HistoryModel> | [];

export class HistoryModel implements HistoryInterface {
  public readonly human_id: string;
  public readonly name: string;
  public readonly browsed_at: string;

  constructor(data: Partial<HistoryModel>) {
    this.human_id = data.human_id || "";
    this.name = data.name || "";
    this.browsed_at = data.browsed_at
      ? dayjs(data.browsed_at).format("YYYY/MM/DD HH:mm:ss")
      : "";
  }
}
export class HistoryListModel implements HistoryListInterface {
  public readonly contents: HistoryInterface[];

  constructor() {
    this.contents = [];
  }

  init(data: HistoryListType) {
    this.clear();
    this.contents.push(
      ...data.map((d: HistoryInterface) => {
        return new HistoryModel(d);
      })
    );
  }

  clear() {
    this.contents.splice(0);
  }
}
