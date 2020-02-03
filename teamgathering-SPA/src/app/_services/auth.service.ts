import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { map } from 'rxjs/operators';
import { JwtHelperService } from '@auth0/angular-jwt';
import { environment } from '../../environments/environment';
import { Profile } from '../_models/profile';
import { AlertifyService } from './alertify.service';
import { Router } from '@angular/router';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  name = '';
  profile_id = '';
  jwtHelper = new JwtHelperService();
  decodedToken: any = {};

  constructor(
    private http: HttpClient,
    private alertify: AlertifyService,
    private router: Router
    ) { }

  login(profile_info: Profile){
    return this.http.post(environment.apiUrl + '_authorization/do_login.php', profile_info).pipe(
      map((response: any) => {
        if( !!response.token ){
          const user = response;
          if(user){
            localStorage.setItem('token', user.token);
            this.decodedToken = this.jwtHelper.decodeToken(user.token);
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

  checkToken(){
    if(this.jwtHelper.isTokenExpired(localStorage.getItem('token')) || localStorage.getItem('token') == 'undefined' ){
      this.logout();
      this.alertify.success('session expired, please relog');
    }
  }

  checkTokenExists(){
    if( localStorage.hasOwnProperty('token') ){
      return localStorage.getItem('token');
    }else{
        return null;
    }
  }

  setProfileName(data){
    this.name = !!data.first_name ? data.first_name : 'user';
  }

  setToken(data){
    if( !!data.token && data != false ){
      localStorage.setItem('token', data.token);
    }else{
      this.logout();
      this.alertify.error('something went wrong, please log back in');
    }
  }

  logout(){
    localStorage.removeItem('token');
    this.name = '';
    this.profile_id = '';
    this.decodedToken = {};
    this.alertify.success('logged out');
    this.router.navigate(['/home']);
  }

  sendForgotPasswordEmail( parameters: any ){
    const params = { ...parameters }
    // return this.http.post( environment.apiUrl + 'crud_profile/do_deleteProfile.php', params )
    return params
  }

}
