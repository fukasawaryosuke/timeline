import dayjs from "dayjs"

export interface InHouseSystemInterface {
  id: number;
  system_name: string;
  icon_path: string;
  release_status: number;
  description: string;
  report_form_id: number;
  parent_id: number;
  division_cd: number;
  created_at: string;
  update_at: string;
}

export class InHouseSystemModel implements InHouseSystemInterface {
  public readonly id: number;
  public readonly system_name: string;
  public readonly icon_path: string;
  public readonly release_status: number;
  public readonly description: string;
  public readonly report_form_id: number;
  public readonly parent_id: number;
  public readonly division_cd: number;
  public readonly created_at: string;
  public readonly update_at: string;
  
  constructor(data: Partial<InHouseSystemModel>) {
    this.id = data.id || 0;
    this.system_name = data.system_name || "";
    this.icon_path = data.icon_path || "";
    this.release_status = data.release_status || 0;
    this.description = data.description || "";
    this.report_form_id = data.report_form_id || 0;
    this.parent_id = data.parent_id || 0;
    this.division_cd = data.division_cd || 0;
    this.created_at = data.created_at ? dayjs(data.created_at).format("YYYY/MM/DD HH:mm:ss") : '';
    this.update_at = data.update_at ? dayjs(data.update_at).format("YYYY/MM/DD HH:mm:ss") : '';
  }
}