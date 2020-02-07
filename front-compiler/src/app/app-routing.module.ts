import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { LoginComponent } from './user/login/login.component';
import { LogoutComponent } from './user/logout/logout.component';
import { RegisterComponent } from './user/register/register.component';
import * as Beverage from './caffeinator/beverage/beverage.namespace';

const routes: Routes = [
    { path: '', redirectTo: '/beverage/calculator', 'pathMatch': 'full'},
    { path:'login', component: LoginComponent },
    { path:'logout', component: LogoutComponent },
    { path:'beverage/calculator', component: Beverage.Component },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
