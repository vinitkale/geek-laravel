import { Component, OnInit } from '@angular/core';
import { ApiMethodService } from '../../model/api-method.service';
import { RouterModule, Router }   from '@angular/router';

import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';

@Component({
  selector: 'app-organization-add',
  templateUrl: '../../view/organization-add/organization-add.component.html',
  styleUrls: ['../../assets/css/organization-add/organization-add.component.css']
})
export class OrganizationAddComponent implements OnInit {
	getToken:any;

  constructor(private router: Router,public apiService:ApiMethodService) { }

  ngOnInit() {
  	this.getToken = this.apiService.getLoginToken();
		if(!(this.getToken)){
			this.router.navigate(['/index']);
		}
  }

}
