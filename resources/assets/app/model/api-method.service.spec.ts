/* tslint:disable:no-unused-variable */

import { TestBed, async, inject } from '@angular/core/testing';
import { ApiMethodService } from './api-method.service';

describe('Service: ApiMethod', () => {
  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [ApiMethodService]
    });
  });

  it('should ...', inject([ApiMethodService], (service: ApiMethodService) => {
    expect(service).toBeTruthy();
  }));
});
