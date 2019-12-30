import { Project } from './project';
import { Team } from './team';

export interface Profile {
    
    profile_id: number;
    email: string;
    first_name: string;
    last_name: string;
    image: string;
    created_date: Date;
    projects?: Project[];
    teams?: Team[];
    
}
