export interface CompanyData {
  id?: number | null;
  companyName: string;
  isActive: number;
  iconFile: File | null;
  companyIconPath: string;
  companyCode?: string;
}
