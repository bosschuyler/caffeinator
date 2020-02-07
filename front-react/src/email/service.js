import axios from 'axios';
import Response from '../utilities/response';
import { flatMap } from 'rxjs/operators';
import environment from '../config/environment';

export class EmailService {
    checkUrl = environment.api_host + '/api/user/register/email-check';

    check = (email) => {
        return axios.get(this.checkUrl+"?email="+email).then((res) => {
            return new Response(res.data);
        });
    }

}