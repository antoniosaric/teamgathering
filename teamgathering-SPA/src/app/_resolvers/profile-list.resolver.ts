import { Injectable } from '@angular/core';
import { Profile } from '../_models/profile';
import { Resolve, Router, ActivatedRouteSnapshot } from '@angular/router';
import { ProfileService } from '../_services/profile.service';
import { Observable, of } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { AlertifyService } from '../_services/alertify.service';

@Injectable()
export class ProfileListResolver implements Resolve<Profile[]> {
    constructor(private profileService: ProfileService, private router: Router, private alertify: AlertifyService ){}

    resolve(route: ActivatedRouteSnapshot): Observable<Profile[]>{
        return this.profileService.getAssociateProfiles({ 'token': localStorage.getItem('token') }).pipe(
            catchError(error => {
                this.alertify.error('Problem retrieving data');
                this.router.navigate(['/home']);
                return of(null);
            })
        );
    }
}