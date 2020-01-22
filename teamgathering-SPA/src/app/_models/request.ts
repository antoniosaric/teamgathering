import { Profile } from './profile';
import { Project } from './project';

export interface Request {
    request_id: number;
    requestee_id?: Profile[];
    requester_id?: Profile[];
    project_id?: Project[];
    project_name?: Project[];
    request_status?: string;
	created_date?: Date;
}