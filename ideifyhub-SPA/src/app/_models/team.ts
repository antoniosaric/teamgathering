import { Profile } from './profile';

export interface Team {
    team_id: number;
	team_name: string;
	team_description?: string;
	project_id?: number;
	profiles?: Profile[];
	project_name?: string;
	created_date?: Date;
}
