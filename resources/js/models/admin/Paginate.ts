// NOTE: Laravelのpaginateフィールドがスネークケースで書かれているのでそれに合わせる
export interface IPaginate {
  current_page: number; // 現在のページ番号
  data?: any; // APIの結果
  first_page_url: string; // 最初のページURL
  from: number; // 遷移してきたページ番号
  last_page: number; // 最後のページ番号
  last_page_url: string; // 最後のページURL
  links: Array<IPaginateLinks>; // NOTE: 使う必要があった場合は定義する
  next_page_url: string; // 次のページURL
  path: string; // ホスト
  per_page: number; // 1ページ内での表示数
  prev_page_url: string; // 前のページURL
  to: number; // TODO: 不明 後で調べる
  total: number; // 検索結果総数
}

class LaravelPaginate implements IPaginate {
  public readonly current_page: number;
  public readonly data?: any;
  public readonly first_page_url: string;
  public readonly from: number;
  public readonly last_page: number;
  public readonly last_page_url: string;
  public readonly links: Array<IPaginateLinks>;
  public readonly next_page_url: string;
  public readonly path: string;
  public readonly per_page: number;
  public readonly prev_page_url: string;
  public readonly to: number;
  public readonly total: number;

  constructor(res: Partial<IPaginate>) {
    this.current_page = res.current_page || 0;
    this.first_page_url = res.first_page_url || '';
    this.from = res.from || 0;
    this.last_page = res.last_page || 0;
    this.last_page_url = res.last_page_url || '';
    this.next_page_url = res.next_page_url || '';
    this.path = res.path || '';
    this.per_page = res.per_page || 0;
    this.prev_page_url = res.prev_page_url || '';
    this.to = res.to || 0;
    this.total = res.total || 0;
    this.links = res.links
      ? res.links.map((l) => {
          return new LaravelPaginateLinks(l);
        })
      : [];
  }
}

export interface IPaginateLinks {
  active: boolean; // 現在のページか否か
  label: string; // ページネーションに表示する文字列
  url: string; // 現在のページURL
}

class LaravelPaginateLinks {
  public readonly active: boolean;
  public readonly label: string;
  public readonly url: string;
  constructor(res: IPaginateLinks) {
    this.active = res.active;
    this.label = res.label;
    this.url = res.url;
  }
}

export default LaravelPaginate;
