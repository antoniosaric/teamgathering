import { Team } from './team';

export interface Project {
    project_id: number;
	project_name: string;
	description?: string;
	short_description?: string;
	project_status?: string;
	image?: string;
    owner_id?: number;
	created_date?: Date;
	first_name?: string;
	last_name?: string;
	count?: number;
	teams?: Team[];
	view_tatus?: string;
	project_role?: string;
}
