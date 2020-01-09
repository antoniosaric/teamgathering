import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, CanDeactivate, RouterStateSnapshot } from '@angular/router';
import { Observable } from 'rxjs';
import { ProfileEditComponent } from '../profiles/profile-edit/profile-edit.component';

@Injectable({providedIn: 'root'})
export class PreventUnsavedChanges implements CanDeactivate<ProfileEditComponent> {
    canDeactivate(
        component: ProfileEditComponent,
        currentRoute: ActivatedRouteSnapshot, 
        currentState: RouterStateSnapshot
    ): Observable<boolean>|Promise<boolean>|boolean {
        if(component.editForm.dirty){
            return confirm('Are you sure you want to continue? Any Unsaved changes will be discarded');
        }
        return true;
    }
}