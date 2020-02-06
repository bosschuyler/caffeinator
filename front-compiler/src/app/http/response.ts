export class Response {
    constructor(private response: object ) {}  

    getMessage() {
        return this.response['message'];
    }

    isSuccessful() {
        return this.response['status'] == 'success';
    }

    getData() {
        return this.response['data'];
    }

    getErrors() {
        return this.response['errors'];
    }

    getParam(key: string) {
        var data = this.getData();
        return data[key] || null;
    }
}