import { Component, OnInit } from '@angular/core';
import { RouterModule, Router }   from '@angular/router';
import { ApiMethodService } from '../../model/api-method.service';

import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';

@Component({
  selector: 'app-event',
  templateUrl: '../../view/event/event.component.html',
  styleUrls: ['../../assets/css/event/event.component.css']
})
export class EventComponent implements OnInit {

	constructor(private router:Router, public apiService:ApiMethodService) { }

  ngOnInit() {
  	this.eventDeafault();
  }

  eventDeafault(){
		var ref = this;
		this.apiService.eventApi(function(res){
			console.log("this is event api response"+ JSON.stringify(res));			
		});
	}

}
