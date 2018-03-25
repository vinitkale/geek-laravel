import { Component, OnInit } from '@angular/core';
import { RouterModule, Router }   from '@angular/router';


@Component({
  selector: 'app-login',
  templateUrl: '../../view/login/login.component.html',
  styleUrls: ['../../assets/css/login/login.component.css']
})
export class LoginComponent implements OnInit {

  constructor(private router: Router) { }

  ngOnInit() {
  }

  redirectToHome(){
  	console.log("this is login click");
  	this.router.navigate(['/index']);
  }

}
