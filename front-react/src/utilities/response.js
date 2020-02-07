export default class Response {
    response = null;
    constructor(response) {
        this.response=response;
    }  

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

    getParam(key) {
        var data = this.getData();
        return data[key] || null;
    }
}