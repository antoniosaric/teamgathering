import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { map } from 'rxjs/operators';
import { JwtHelperService } from '@auth0/angular-jwt';
import { environment } from '../../environments/environment';
import { Profile } from '../_models/profile';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  name = '';
  profile_id = '';
  jwtHelper = new JwtHelperService();
  decodedToken: any = {};

  constructor(private http: HttpClient) { }

  login(profile_info: Profile){
    return this.http.post(environment.apiUrl + '_authorization/do_login.php', profile_info).pipe(
      map((response: any) => {
        if( !!response.JWT ){
          const user = response;
          if(user){
            localStorage.setItem('token', user.JWT);
            this.decodedToken = this.jwtHelper.decodeToken(user.JWT);
            this.name = !!this.decodedToken.data.first_name ? this.decodedToken.data.first_name : 'user';
            this.profile_id = !!this.decodedToken.data.profile_id ? this.decodedToken.data.profile_id : '';
          }
        }
      })
    )
  }

  register(profile_info: Profile){
    return this.http.post( environment.apiUrl  + 'crud_profile/do_createProfile.php', profile_info);
  }

  loggedIn(){
    const token = !!localStorage.getItem('token') ? localStorage.getItem('token') : null;
    if(this.jwtHelper.isTokenExpired(token)){
      localStorage.removeItem('token');
      // this.router.navigate(['/home']);
      return;
    }
    return ( !!token && token != null ) ? !this.jwtHelper.isTokenExpired(token) : false;
    // ;
  }



}
