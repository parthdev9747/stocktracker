$(document).ready(function() {
    deleteRecordByAjax = (url, moduleName, tableId) => {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            customClass: {
                confirmButton: 'btn btn-primary w-xs me-2 mt-2',
                cancelButton: 'btn btn-danger w-xs mt-2',
            },
            confirmButtonText: "Yes, delete it!",
            buttonsStyling: false,
            showCloseButton: true
        }).then((willDelete) => {
            if (willDelete.value) {
                axios.delete(url).then((response) => {
                    if (response.data.status) {
                        window.LaravelDataTables[tableId].ajax.reload(null, false)
                        if (response.data.type === 'warning') {
                            Toastify({
                                text: response.data.message,
                                duration: 3000,
                                close: true,
                                gravity: "top",
                                position: "right",
                                className: "bg-warning",
                            }).showToast();
                        } else {
                            Toastify({
                                text: response.data.message,
                                duration: 3000,
                                close: true,
                                gravity: "top",
                                position: "right",
                                className: "bg-success",
                            }).showToast();
                        }
                    } else {
                        Toastify({
                            text: response.data.message,
                            duration: 3000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            className: "bg-danger",
                        }).showToast();
                    }
                }).catch((error) => {
                    console.log(error);
                    
                    let data = error.response.data
                    Toastify({
                        text: data.message,
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        className: "bg-danger",
                    }).showToast();
                });
            }
        })
    }

    changeStatusByAjax = (url, tableId, id) => {
        axios.post(url, { id: id }).then((response) => {
            if (response.data.status) {
                if (response.data.type === 'warning') {
                    window.LaravelDataTables[tableId].ajax.reload(null, false)
                    Toastify({
                        text: response.data.message,
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        className: "bg-warning",
                    }).showToast();
                } else {
                    window.LaravelDataTables[tableId].ajax.reload(null, false)
                    Toastify({
                        text: response.data.message,
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        className: "bg-success",
                    }).showToast();
                }
            } else {
                window.LaravelDataTables[tableId].ajax.reload(null, false)
                Toastify({
                    text: response.data.message,
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    className: "bg-danger",
                }).showToast();
            }
        }).catch((error) => {
            window.LaravelDataTables[tableId].ajax.reload(null, false)
            console.log(error);
            
            let data = error.response.data
            Toastify({
                text: data.message,
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                className: "bg-danger",
            }).showToast();
        });
           
    }
});