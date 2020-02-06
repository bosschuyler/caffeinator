import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { NavbarComponent } from 'src/app/navbar/navbar.component';
import { RegisterComponent } from '../register/register.component';

@Component({
  selector: 'app-logout',
  template: ''
})
export class LogoutComponent implements OnInit {
  constructor(private router: Router,) {
    
  }

  ngOnInit() {
    localStorage.removeItem('token');
    this.router.navigateByUrl('/login');
  }
}
