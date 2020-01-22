import { Injectable } from '@angular/core';
import { AuthService } from './auth.service';
import { environment } from 'src/environments/environment';
import { HttpClient } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class TeamService {

constructor(
  private http: HttpClient,
  private authService: AuthService
) { }

getTeams(token: any){
  const params = {...token }
  return this.http.post( environment.apiUrl + 'main/get_teams.php', params );
}



}
