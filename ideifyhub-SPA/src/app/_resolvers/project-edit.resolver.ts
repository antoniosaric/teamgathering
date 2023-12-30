import { Injectable } from '@angular/core';
import { Project } from '../_models/project';
import { Resolve, Router, ActivatedRouteSnapshot } from '@angular/router';
import { ProjectService } from '../_services/project.service';
import { Observable, of } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { AlertifyService } from '../_services/alertify.service';
import { AuthService } from '../_services/auth.service';

@Injectable()
export class ProjectEditResolver implements Resolve<Project> {
    constructor(
        private projectService: ProjectService, 
        private authService: AuthService, 
        private router: Router, 
        private alertify: AlertifyService 
    ){}

    resolve(route: ActivatedRouteSnapshot): Observable<Project>{
        return this.projectService.getProject( {'project_id': route.params['id']}, { 'token': localStorage.getItem('token') } ).pipe(
            catchError(error => {
                this.alertify.error('Problem retrieving your data');
                this.router.navigate(['/home']);
                return of(null);
            })
        );
    }
}


