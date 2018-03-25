import { Component, OnInit } from '@angular/core';
import { ApiMethodService } from '../../model/api-method.service';
import { RouterModule, Router }   from '@angular/router';

import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';

@Component({
  selector: 'app-groups',
  templateUrl: '../../view/groups/groups.component.html',
  styleUrls: ['../../assets/css/groups/groups.component.css']
})
export class GroupsComponent implements OnInit {
getToken:any;
  constructor(private router: Router,public apiService:ApiMethodService) { }

  ngOnInit() {
  	this.getToken = this.apiService.getLoginToken();
		if(!(this.getToken)){
			this.router.navigate(['/index']);
		}
  }

}
