import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { AuthService } from './auth.service';

@Injectable({
  providedIn: 'root'
})
export class StatusService {
  token: string;
  status: any;
  // status = {message: true, request: true}

  constructor( private http: HttpClient, private authService: AuthService ) { }

  getMesReqStatus( token: any ){
    return this.http.post( environment.apiUrl  + 'main/do_returnMesReqStatus.php', token );

  }

  searchStatus(){
    if( !!this.authService.checkTokenExists() ){
      this.getMesReqStatus( { 'token': localStorage.getItem('token') }).subscribe(next => {
        this.setStatus(next);
      })
    }
  }

  setStatus(data){
    console.log('*******')
    console.log(data)
    this.status = data.status; 
  }


}
