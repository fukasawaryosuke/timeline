export type User = {
  id: number;
  human_id: string;
  user_name: string;
  organization: Organization;
};

export type InHouseSystem = {
  id: number;
  system_name: string;
  description: string;
  organization: Organization[];
};

export type Organization = {
  id: number;
  organization_name: string;
  division_cd: number;
  company_code: string;
};

export type LoginUser = {
  id: number;
  human_id: string;
  user_name: string;
  mailadd: string;
  system_role_id: number;
  organization: Organization;
  is_initial_password: number;
};
