import { Component, OnInit } from '@angular/core';
import { ApiMethodService } from '../../model/api-method.service';
import { RouterModule, Router }   from '@angular/router';

import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';

@Component({
  selector: 'app-changepassword',
  templateUrl: '../../view/changepassword/changepassword.component.html',
  styleUrls: ['../../assets/css/changepassword/changepassword.component.css']
})
export class ChangepasswordComponent implements OnInit {

  getToken:any;
  constructor(private router: Router,public apiService:ApiMethodService) { }

  ngOnInit() {
  	this.getToken = this.apiService.getLoginToken();
		if(!(this.getToken)){
			this.router.navigate(['/index']);
		}
  }

}
