import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { HttpClient, HttpHeaders } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class ImageService {

constructor(private http: HttpClient) { }

  saveProfileImage( token: any, image: any ){
    const params = {...token, ...image };
    return this.http.post( environment.apiUrl + 'crud_image/do_addUpdateProfileImage.php', params )
  }

  saveProjectImage( token: any, image: any, project_id: any ){
    const params = {...token, ...image, ...project_id };
    return this.http.post( environment.apiUrl + 'crud_image/do_addUpdateProjectImage.php', params )
  }

  deleteProfileImage(token: any){
    const params = {...token };
    return this.http.post( environment.apiUrl + 'crud_image/do_deleteProfileImage.php', params )
  }

  deleteProjectImage(token: any, project_id: any ){
    const params = {...token, ...project_id };
    console.log(params)
    return this.http.post( environment.apiUrl + 'crud_image/do_deleteProjectImage.php', params )
  }


}
