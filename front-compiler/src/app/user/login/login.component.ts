import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { NavbarComponent } from 'src/app/navbar/navbar.component';
import { RegisterComponent } from '../register/register.component';


@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss'],
  providers: [NavbarComponent, RegisterComponent]
})
export class LoginComponent implements OnInit {
  constructor() {
    
  }

  ngOnInit() {
     
  }
}
