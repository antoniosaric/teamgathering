import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Profile } from '../_models/profile';
import { map } from 'rxjs/operators';

const httpOptions = {
  headers: new HttpHeaders({
    'Authorization': 'Bearer ' + localStorage.getItem('token')
  })
}

@Injectable({
  providedIn: 'root'
})
export class ProfileService {

constructor( private http: HttpClient ) { }

  getProfile(params): Observable<Profile> {
    // console.log(model);
    return this.http.post(environment.apiUrl + 'main/get_profile.php', params).pipe(
      map((response: any) => {
        console.log(response);
        if( !!response.profile ){
          const profile = response.profile;
          if(profile){
            return profile;
          }else{
            return false;
          }
        }
      })
    )




    // return this.http.get<Profile>( environment.apiUrl + 'main/get_profile.php/' + model )
  }

  getUserProfile(id): Observable<Profile> {
    return this.http.get<Profile>( environment.apiUrl + 'main/get_profile.php/' + id, httpOptions )
  }
}
