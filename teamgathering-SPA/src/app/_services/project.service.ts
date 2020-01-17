import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Project } from '../_models/project';
import { map } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class ProjectService {

constructor( private http: HttpClient ) { }

  getHomepage(): Observable<Project[]> {
    return this.http.get<Project[]>( environment.apiUrl + 'main/homepage.php' )
  }


  getProject(token: any, parameters: any ): Observable<Project> {
    const params = {...parameters, ...token }
    return this.http.post(environment.apiUrl + 'main/get_project.php', params).pipe(
      map((response: any) => {
        if( !!response.project ){
          return response.project;
        }else{
          return false;
        }
      })
    )
  }

}
