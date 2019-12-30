export interface Project {
    project_id: number;
	project_name: string;
	description?: string;
	short_description?: string;
	project_status?: string;
	image: string;
    owner_id: number;
	created_date: Date;
}
