import { Project } from './project';
import { Team } from './team';
import { Tag } from './tag';
import { Follow } from './follow';

export interface Profile {
    
    profile_id: number;
    email?: string;
    first_name?: string;
    last_name?: string;
    zip_code?: string;
    city?: string;
    state?: string;
    image?: string;
    description?: string;
    created_date?: Date;
    joined_date?: Date;
    projects?: Project[];
    teams?: Team[];
    tags?: Tag[];
    follows?: Follow[];
    role?: string;
    team_name?: string;
    team_id?: number;
    project_name?: string;
    project_id?: number;
    profile_team_status?: string;
    
}
