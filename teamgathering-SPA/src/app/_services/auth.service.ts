import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { map } from 'rxjs/operators';
import { JwtHelperService } from '@auth0/angular-jwt';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  baseUrl = 'http://localhost:5001/teamtest/teamgathering.API/';
  name = '';
  profile_id = '';
  jwtHelper = new JwtHelperService();
  decodedToken: any = {};

  constructor(private http: HttpClient) { }

  login(model: any){

    return this.http.post(this.baseUrl + '_authorization/do_login.php', model).pipe(
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

  register(model: any){
    return this.http.post(this.baseUrl + 'crud_profile/do_createProfile.php', model);
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
