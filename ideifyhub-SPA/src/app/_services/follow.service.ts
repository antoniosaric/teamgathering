import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { HttpClient } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class FollowService {

constructor( private http: HttpClient ) { }

addFollow( token: any, follow_object: any ){
  const params = {...token, ...follow_object };
  return this.http.post( environment.apiUrl + 'crud_follow/do_createFollow.php', params )
}

deleteFollow( token: any, follow_object: any ){
  const params = {...token, ...follow_object };
  return this.http.post( environment.apiUrl + 'crud_follow/do_deleteFollow.php', params )
}

}
