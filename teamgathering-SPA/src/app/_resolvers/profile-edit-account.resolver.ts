import { Injectable } from '@angular/core';
import { Profile } from '../_models/profile';
import { Resolve, Router, ActivatedRouteSnapshot } from '@angular/router';
import { ProfileService } from '../_services/profile.service';
import { Observable, of } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { AlertifyService } from '../_services/alertify.service';
import { AuthService } from '../_services/auth.service';

@Injectable()
export class ProfileEditAccountResolver implements Resolve<Profile> {
    constructor(private profileService: ProfileService, private authService: AuthService, private router: Router, private alertify: AlertifyService ){}

    resolve(route: ActivatedRouteSnapshot): Observable<Profile>{
        return this.profileService.getProfile({'profile_id': this.authService.decodedToken.data.profile_id}).pipe(
            catchError(error => {
                this.alertify.error('Problem retrieving your data');
                this.router.navigate(['/home']);
                return of(null);
            })
        );
    }
}