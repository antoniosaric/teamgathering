/* tslint:disable:no-unused-variable */

import { TestBed, async, inject } from '@angular/core/testing';
import { ImageService } from './image.service';

describe('Service: ImageService', () => {
  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [ImageService]
    });
  });

  it('should ...', inject([ImageService], (service: ImageService) => {
    expect(service).toBeTruthy();
  }));
});
