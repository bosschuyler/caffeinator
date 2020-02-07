import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { NavbarComponent } from 'src/app/navbar/navbar.component';
import { RegisterComponent } from '../register/register.component';
import { UserService } from '../user.service';
import { Response } from 'src/app/http/response';

@Component({
  selector: 'app-logout',
  template: ''
})
export class LogoutComponent implements OnInit {
  constructor(private router: Router, private userService: UserService) {
    
  }

  ngOnInit() {
    this.userService.logout().subscribe((response: Response) => {
        localStorage.removeItem('token');    
        this.router.navigateByUrl('/login');
    });
  }
}
