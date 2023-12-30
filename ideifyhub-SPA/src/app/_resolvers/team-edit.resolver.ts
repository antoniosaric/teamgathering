import { Injectable } from '@angular/core';
import { Team } from '../_models/team';
import { Resolve, Router, ActivatedRouteSnapshot } from '@angular/router';
import { TeamService } from '../_services/team.service';
import { Observable, of } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { AlertifyService } from '../_services/alertify.service';
import { AuthService } from '../_services/auth.service';

@Injectable()
export class TeamEditResolver implements Resolve<Team> {
    constructor(
        private teamService: TeamService, 
        private authService: AuthService, 
        private router: Router, 
        private alertify: AlertifyService 
    ){}

    resolve(route: ActivatedRouteSnapshot): Observable<Team>{
        return this.teamService.getTeam( { 'token': localStorage.getItem('token') }, {'team_id': route.params['id']} ).pipe(
            catchError(error => {
                this.alertify.error('Problem retrieving your data');
                this.router.navigate(['/home']);
                return of(null);
            })
        );
    }
}