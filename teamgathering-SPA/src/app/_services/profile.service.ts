import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Profile } from '../_models/profile';
import { map } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class ProfileService {

constructor( private http: HttpClient ) { }

  getProfile(params: any): Observable<Profile> {
    return this.http.post(environment.apiUrl + 'main/get_profile.php', params).pipe(
      map((response: any) => {
        if( !!response.profile ){
          return response.profile;
        }else{
          return false;
        }
      })
    )
  }

  getAssociateProfiles(token: any): Observable<Profile[]> {
    return this.http.post(environment.apiUrl + 'main/get_associateProfiles.php', token).pipe(
      map((response: any) => {
        if( !!response.profiles ){
            return response.profiles;
        }
      })
    ) 
  }

  updateProfile( token: any, profile_info: Profile ){
    const params = {...token, ...profile_info };
    return this.http.post( environment.apiUrl + 'crud_profile/do_updateProfile.php', params )
  }

  getMessage(token: any, profile_info: any){
    const params = {...token, ...profile_info }
    return this.http.post( environment.apiUrl + 'crud_message/do_getThread.php', params )
  }

  getMessages(token: any){
    return this.http.post( environment.apiUrl + 'crud_message/do_getMessages.php', token )
  }

  postMessage( token: any, message_info: any ){
    const params = {...token, ...message_info };
    console.log(params)
    return this.http.post( environment.apiUrl + 'crud_message/do_createMessage.php', params )
  }

  getProfileProjects(token: any){
    return this.http.post(environment.apiUrl + 'main/get_profileProjects.php', token).pipe(
      map((response: any) => {
        if( !!response.projects ){
            return response.projects;
        }
      })
    ) 
  }



}
