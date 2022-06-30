let dataTableOptions = {
    tableFooterProps: {
        showFirstLastPage: true,
        itemsPerPageText: 'Elementos por página',
        itemsPerPageOptions: [15, 20, 25, 50, -1]
    },
    tableOptions: {
        itemsPerPage: 15
    },
    createColor: 'green lighten-2',
    readColor: 'blue lighten-2',
    updateColor: 'orange lighten-2',
    deleteColor: 'red lighten-2',
}

export {dataTableOptions};
