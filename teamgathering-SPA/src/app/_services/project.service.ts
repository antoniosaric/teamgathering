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

  getHomepage(token: any): Observable<Project[]> {
    const params = { ...token }
    console.log('%%%%%%%%%')
    console.log(params)
    return this.http.post<Project[]>( environment.apiUrl + 'main/homepage.php', params )
  }


  getProject(token: any, parameters: any ): Observable<Project> {
    const params = {...token, ...parameters };
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

  updateProject( token: any, project_info: Project ){
    const params = {...token, ...project_info }
    return this.http.post( environment.apiUrl + 'crud_project/do_updateProject.php', params )
  }

  addProject( token: any, project_info: Project ){
    const params = {...token, ...project_info }
    return this.http.post( environment.apiUrl + 'crud_project/do_createProject.php', params )
  }

  getProjectsList(token: any){
    return this.http.post( environment.apiUrl + 'crud_project/do_getProjects.php', token )
  }

}
