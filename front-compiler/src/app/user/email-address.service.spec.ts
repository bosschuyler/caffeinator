import { TestBed } from '@angular/core/testing';

import { EmailAddressService } from './email-address.service';

describe('EmailAddressService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: EmailAddressService = TestBed.get(EmailAddressService);
    expect(service).toBeTruthy();
  });
});
