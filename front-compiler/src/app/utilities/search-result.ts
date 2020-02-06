export class SearchResult {
    // constructor(private response: object ) {}  

    items = [];
    pagination = {
        page: 0,
        items_per_page: 10,
        total: 0
    };

    getItems() {
        return this.items;
    }

    setItems(items: []) {
        this.items = items;
        return this;
    }

    setPage(page: number) {
        this.pagination.page = page;
        return this;
    }

    setTotal(total: number) {
        this.pagination.total = total;
        return this;
    }

    setItemsPerPage(items_per_page: number) {
        this.pagination.items_per_page = items_per_page;
        return this;
    }

    getTotalPages() {
        return Math.ceil(this.getTotal() / this.getItemsPerPage());
    }

    getPage() {
        return this.pagination.page;
    }

    getTotal() {
        return this.pagination.total;
    }

    getItemsPerPage() {
        return this.pagination.items_per_page;
    }
}