import { Injectable } from '@angular/core';
import { AuthService } from './auth.service';
import { environment } from 'src/environments/environment';
import { Observable } from 'rxjs';
import { HttpClient } from '@angular/common/http';
import { Team } from '../_models/team';
import { map } from 'rxjs/operators';

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

  getTeam(token: any, parameters: any ): Observable<Team> {
    const params = { ...token, ...parameters }
    return this.http.post(environment.apiUrl + 'crud_team/do_getTeam.php', params).pipe(
      map((response: any) => {
        if( !!response.team ){
          return response.team;
        }else{
          return false;
        }
      })
    )
  }

  updateTeam(token: any, parameters: any ): Observable<Team> {
    const params = { ...token, ...parameters }
    return this.http.post(environment.apiUrl + 'crud_team/do_updateTeam.php', params).pipe(
      map((response: any) => {
        if( !!response ){
          return response;
        }else{
          return false;
        }
      })
    )
  }

  updateTeamProfile(token: any, parameters: any ){
    const params = { ...token, ...parameters }
    return this.http.post(environment.apiUrl + 'crud_team/do_updateProfilesTeam.php', params).pipe(
      map((response: any) => {
        if( !!response.teams ){
          return response.teams;
        }else{
          return false;
        }
      })
    )
  }

  deleteTeam(token: any, parameters: any ){
    const params = { ...token, ...parameters }
    return this.http.post(environment.apiUrl + 'crud_team/do_deleteTeam.php', params).pipe(
      map((response: any) => {
        if( !!response.teams ){
          return response.teams;
        }else{
          return false;
        }
      })
    )
  }

  deleteProfileFromTeam(token: any, parameters: any ){
    const params = {...token, ...parameters }
    console.log(params)
    return this.http.post( environment.apiUrl + 'crud_profiles_team/do_deleteProfilesTeam.php', params )
  }

}
