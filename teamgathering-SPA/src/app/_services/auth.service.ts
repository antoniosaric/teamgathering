import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { map } from 'rxjs/operators'

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  baseUrl = 'http://localhost:5001/teamtest/teamgathering.API/';
  name = true ? 'xxxx' : 'user';

  constructor(private http: HttpClient) { }

  login(model: any){
    console.log(model);
    return this.http.post(this.baseUrl + '_authorization/do_login.php', model).pipe(
      map((response: any) => {
        console.log(response);
        if( !!response.JWT ){
          const user = response;
          if(user){
            localStorage.setItem('token', user.JWT);
          }
        }else if( response.message == 'incorrect login info' ){
          console.log('try again');
        }else if( response.message == 'email specifications not met' ){

        }else if( response.message == 'assword specifications not met' ){
          
        }else{
          console.log('something went wrong');
        }
      })
    )
  }

  register(model: any){
    return this.http.post(this.baseUrl + 'crud_profile/do_createProfile.php', model);
  }

  loggedIn(){
    const token = !!localStorage.getItem('token') ? localStorage.getItem('token') : null;
    console.log(token);
    return ( !!token && token != null ) ? true : false;
    // return !this.jwtHelper.isTokenExpired(token);
  }

}
