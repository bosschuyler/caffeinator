import { Component, OnInit } from '@angular/core';
import { LocationStrategy, PathLocationStrategy } from '@angular/common';
import { Router } from '@angular/router';
import { Route } from '@angular/compiler/src/core';
import { UserService } from 'src/app/user/user.service';

@Component({
  selector: 'app-navbar',
  templateUrl: './navbar.component.html',
  styleUrls: ['./navbar.component.scss']
})
export class NavbarComponent implements OnInit {
  
  constructor(public userService: UserService, private router: Router) {
    
  }

  ngOnInit() {
    // console.log(this.router.url);
  }

}
