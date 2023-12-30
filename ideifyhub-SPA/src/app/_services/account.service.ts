import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class AccountService {

constructor(
  private http: HttpClient,
) { }

  updateEmail( token: any, parameters: any ){
    const params = {...token, ...parameters }
    return this.http.post( environment.apiUrl + '_authorization/do_updateEmail.php', params )
  }

  updatePassword( token: any, parameters: any ){
    const params = {...token, ...parameters }
    return this.http.post( environment.apiUrl + '_authorization/do_updatePassword.php', params )
  }

  deleteAccount( token: any, parameters: any ){
    const params = {...token, ...parameters }
    return this.http.post( environment.apiUrl + 'crud_profile/do_deleteProfile.php', params )
  }

}
