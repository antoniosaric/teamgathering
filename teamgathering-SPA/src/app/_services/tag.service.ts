import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { HttpClient } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class TagService {

constructor(private http: HttpClient) { }

  getTags( token: any, parameters: any ){
    const params = {...token, ...parameters };
    return this.http.post( environment.apiUrl + 'crud_tag/do_readTag.php', params )
  }

  addTag( token: any, tag_object: any ){
    const params = {...token, ...tag_object }
    console.log(params)
    return this.http.post( environment.apiUrl + 'crud_tag/do_createProfileTag.php', params )
  }

  deleteTag( token: any, tag_object: any ){
    const params = {...token, ...tag_object }
    console.log(params)
    return this.http.post( environment.apiUrl + 'crud_tag/do_deleteProfileTag.php', params )
  }

}
