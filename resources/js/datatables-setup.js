// File: public/js/datatables-setup.js

$(document).ready(function() {
    /**
     * Common DataTable initialization function
     * @param {string} tableId - ID of the table element
     * @param {object} options - Override options for specific tables
     */
    window.initDataTable = function(tableId, options = {}) {
        // Default options
        const defaultOptions = {
            processing: true,
            serverSide: true,
            responsive: true,
            stateSave: true,
            autoWidth: false,
            dom: 'Blfrtip',
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print',
                {
                    text: 'Reload',
                    action: function(e, dt) {
                        dt.ajax.reload();
                    }
                }
            ],
            language: {
                search: '',
                searchPlaceholder: 'Search...',
                processing: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>',
                lengthMenu: '_MENU_ records per page',
                info: 'Showing _START_ to _END_ of _TOTAL_ records',
                infoEmpty: 'Showing 0 to 0 of 0 records',
                emptyTable: 'No records available'
            },
            // Handle global search
            initComplete: function() {
                const api = this.api();
                
                // Apply the search
                $('#' + tableId + '_filter input').unbind();
                $('#' + tableId + '_filter input').bind('keyup', function(e) {
                    if(e.keyCode === 13) {
                        api.search(this.value).draw();
                    }
                });
                
                // Add filter form handling
                $('.datatable-filters').on('submit', function(e) {
                    e.preventDefault();
                    api.ajax.reload();
                });
                
                // Reset filters
                $('.datatable-filters-reset').on('click', function(e) {
                    e.preventDefault();
                    $('.datatable-filters')[0].reset();
                    api.ajax.reload();
                });
            }
        };
        
        // Merge with custom options
        const finalOptions = $.extend(true, {}, defaultOptions, options);
        
        // Initialize the datatable
        const dataTable = $('#' + tableId).DataTable(finalOptions);
        
        // Handle delete button actions
        $(document).on('click', '.delete-record', function(e) {
            e.preventDefault();
            
            const button = $(this);
            const url = button.data('route');
            const id = button.data('id');
            const name = button.data('name') || 'record';
            
            Swal.fire({
                title: 'Delete Confirmation',
                text: `Are you sure you want to delete this ${name}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send delete request
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            id: id
                        },
                        success: function(response) {
                            Swal.fire(
                                'Deleted!',
                                response.message || 'Record has been deleted.',
                                'success'
                            );
                            
                            // Reload the datatable
                            dataTable.ajax.reload();
                        },
                        error: function(xhr) {
                            let errorMessage = 'Something went wrong!';
                            
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            
                            Swal.fire(
                                'Error!',
                                errorMessage,
                                'error'
                            );
                        }
                    });
                }
            });
        });
        
        // Handle bulk actions if available
        $('.datatable-bulk-action').on('click', function(e) {
            e.preventDefault();
            
            const action = $(this).data('action');
            const selectedIds = [];
            
            // Get all checked checkboxes
            $('.datatable-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });
            
            if (selectedIds.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'No Selection',
                    text: 'Please select at least one record.'
                });
                return;
            }
            
            // Process bulk action
            processBulkAction(action, selectedIds, dataTable);
        });
        
        return dataTable;
    };
    
    /**
     * Process bulk actions
     */
    function processBulkAction(action, ids, dataTable) {
        const actionMap = {
            'delete': {
                title: 'Delete Selected Records',
                text: 'Are you sure you want to delete all selected records?',
                icon: 'warning',
                confirmButtonText: 'Yes, delete them!',
                url: window.routes.bulkDelete
            },
            'activate': {
                title: 'Activate Selected Records',
                text: 'Are you sure you want to activate all selected records?',
                icon: 'question',
                confirmButtonText: 'Yes, activate them!',
                url: window.routes.bulkActivate
            },
            'deactivate': {
                title: 'Deactivate Selected Records',
                text: 'Are you sure you want to deactivate all selected records?',
                icon: 'question',
                confirmButtonText: 'Yes, deactivate them!',
                url: window.routes.bulkDeactivate
            }
        };
        
        const actionConfig = actionMap[action];
        
        if (!actionConfig) {
            console.error('Action not supported:', action);
            return;
        }
        
        Swal.fire({
            title: actionConfig.title,
            text: actionConfig.text,
            icon: actionConfig.icon,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: actionConfig.confirmButtonText
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: actionConfig.url,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        ids: ids,
                        action: action
                    },
                    success: function(response) {
                        Swal.fire(
                            'Success!',
                            response.message || 'Operation completed successfully.',
                            'success'
                        );
                        
                        // Reload the datatable
                        dataTable.ajax.reload();
                        
                        // Uncheck the "select all" checkbox
                        $('.datatable-select-all').prop('checked', false);
                    },
                    error: function(xhr) {
                        let errorMessage = 'Something went wrong!';
                        
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        Swal.fire(
                            'Error!',
                            errorMessage,
                            'error'
                        );
                    }
                });
            }
        });
    }
    
    // Select all checkbox functionality
    $(document).on('click', '.datatable-select-all', function() {
        const isChecked = $(this).prop('checked');
        $('.datatable-checkbox').prop('checked', isChecked);
    });
});