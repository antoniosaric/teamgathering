import { Injectable } from '@angular/core';
import { AuthService } from './auth.service';
import { environment } from 'src/environments/environment';
import { HttpClient } from '@angular/common/http';


@Injectable({
  providedIn: 'root'
})
export class RequestService {

constructor(
  private authService: AuthService,
  private http: HttpClient
) { }

  addRequest(token: any, parameters: any){
    const params = {...token, ...parameters }
    return this.http.post( environment.apiUrl + 'crud_request/do_createRequest.php', params );
  }

  updateRequest(token: any, parameters: any){
    const params = {...token, ...parameters };
    return this.http.post( environment.apiUrl + 'crud_request/do_updateRequest.php', params );
  }

  getRequests(token: any){
    const params = {...token }
    return this.http.post( environment.apiUrl + 'crud_request/do_getRequests.php', params );
  }

  deleteRequest(token: any, parameters: any){
    const params = {...token, ...parameters }
    return this.http.post( environment.apiUrl + 'crud_request/do_deleteRequests.php', params );
  }


}
