<script>
    $(document).ready(function() {
        if (typeof $.fn.DataTable === 'undefined') {
            return;
        }

        const table = $('#opportunities_table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            pageLength: 12,
            lengthMenu: [
                [12, 25, 50, 100],
                [12, 25, 50, 100]
            ],
            searching: false,
            dom: '<"table-responsive"t><"d-flex justify-content-between align-items-center mt-3"<"d-flex align-items-center"l><"d-flex align-items-center"ip>>',
            ajax: {
                url: "{{ route('leads.get') }}",
                type: "GET",
                data: function(d) {
                    d.status = $('#statusFilter').val();
                    d.class = $('#classFilter').val();
                    d.priority = $('#priorityFilter').val();
                },
                error: function(xhr, error, code) {
                    $('#opportunities_table_wrapper').prepend(
                        '<div class="alert alert-danger">Error loading data. Please refresh the page.</div>'
                    );
                }
            },
            columns: [{
                    data: 'opportunity_id',
                    name: 'opportunity_id',
                    title: 'Opportunity ID',
                    // className: 'border-left-primary fw-bold',
                    render: function(data, type, row) {
                        return data || 'N/A';
                    }
                },
                {
                    data: 'client_category',
                    name: 'client_category',
                    title: 'Client Category',
                    render: function(data, type, row) {
                        return data || 'N/A';
                    }
                },
                {
                    data: 'priority_badge',
                    name: 'priority',
                    title: 'Priority',
                    orderable: true,
                    searchable: false,
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return data ||
                                '<span class="priority-badge priority-medium">Normal</span>';
                        }
                        return data;
                    }
                },
                {
                    data: 'insured_name',
                    name: 'insured_name',
                    title: 'Insured Name',
                    render: function(data, type, row) {
                        return data || 'N/A';
                    }
                },
                {
                    data: 'class_of_business',
                    name: 'class_of_business',
                    title: 'Class of Business',
                    render: function(data, type, row) {
                        return data || 'N/A';
                    }
                },
                {
                    data: 'status_badge',
                    name: 'status',
                    title: 'Status',
                    orderable: true,
                    searchable: false,
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return data || '<span class="badge bg-secondary">Unknown</span>';
                        }
                        return data;
                    }
                },
                {
                    data: 'formatted_premium',
                    name: 'cede_premium',
                    title: 'Premium',
                    className: 'text-end',
                    orderable: true,
                    searchable: false,
                    render: function(data, type, row) {
                        return data || '-';
                    }
                },
                {
                    data: 'commission_percentage',
                    name: 'commission_percentage',
                    title: 'Commission %',
                    className: 'text-end',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return data && !isNaN(data) ? parseFloat(data).toFixed(2) + '%' :
                                '-';
                        }
                        return data || 0;
                    }
                },
                {
                    data: 'effective_date',
                    name: 'effective_date',
                    title: 'Effective Date',
                    render: function(data, type, row) {
                        if (type === 'display' && data) {
                            return typeof moment !== 'undefined' ?
                                moment(data).format('MMM DD, YYYY') :
                                new Date(data).toLocaleDateString();
                        }
                        return data || '-';
                    }
                },
                {
                    data: 'expiry_date',
                    name: 'expiry_date',
                    title: 'Expiry Date',
                    render: function(data, type, row) {
                        if (type === 'display' && data) {
                            return typeof moment !== 'undefined' ?
                                moment(data).format('MMM DD, YYYY') :
                                new Date(data).toLocaleDateString();
                        }
                        return data || '-';
                    }
                },
                {
                    data: 'account_executive.name',
                    name: 'account_executive.name',
                    title: 'Prospect Lead',
                    defaultContent: '-',
                    render: function(data, type, row) {
                        return data || '-';
                    }
                },
                {
                    data: 'action',
                    name: 'action',
                    title: 'Actions',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return data || '-';
                    }
                }
            ],
            createdRow: function(row, data, dataIndex) {
                if (data.priority) {
                    const priorityClass = `row-priority-${data.priority.toLowerCase()}`;
                    $(row).addClass(priorityClass);
                }

                if (data.urgency_class) {
                    $(row).addClass(data.urgency_class);
                }

                $(row).attr('data-id', data.opportunity_id);
                $(row).addClass('table-row-hover');

                // if (data.urgency_class) {
                //     $(row).addClass(data.urgency_class);
                // }
                // $(row).attr('data-id', data.opportunity_id);
            },
            drawCallback: function(settings) {
                if (typeof $.fn.tooltip !== 'undefined') {
                    $('[data-bs-toggle="tooltip"]').tooltip();
                }
            },
            language: {
                processing: "Loading opportunities...",
                emptyTable: "No opportunities found",
                zeroRecords: "No matching opportunities found",
                lengthMenu: "Show _MENU_ opportunities per page",
                info: "Showing _START_ to _END_ of _TOTAL_ opportunities",
                infoEmpty: "Showing 0 to 0 of 0 opportunities",
                infoFiltered: "(filtered from _MAX_ total opportunities)"
            }
        });

        let filterTimeout;
        $('#statusFilter, #classFilter, #priorityFilter').on('change', function() {
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(function() {
                try {
                    table.ajax.reload();
                } catch (error) {
                    console.error('Error reloading table with filters:', error);
                }
            }, 300);
        });

        let searchTimeout;
        $('#globalSearch').on('keyup', function() {
            clearTimeout(searchTimeout);
            const searchTerm = this.value;

            searchTimeout = setTimeout(function() {
                try {
                    table.search(searchTerm).draw();
                    console.log('Search applied:', searchTerm);
                } catch (error) {
                    console.error('Error performing search:', error);
                }
            }, 300);
        });

        setInterval(function() {
            try {
                table.ajax.reload(null, false);
                console.log('Auto-refresh completed at', new Date().toLocaleTimeString());
            } catch (error) {
                console.error('Error during auto-refresh:', error);
            }
        }, 300000);

        $('#refreshTable').on('click', function() {
            table.ajax.reload();
            $(this).prop('disabled', true);
            setTimeout(() => {
                $(this).prop('disabled', false);
            }, 2000);
        });

        table.on("click", ".send_to_sales", function(e) {
            e.preventDefault();
            const data = $(this).data();
            sendOpportunityToSales(data.prospect_id)
        });
    });

    function sendOpportunityToSales(id) {
        if (!id) {
            console.error('No opportunity ID provided');
            return;
        }

        Swal.fire({
            title: "Warning!",
            html: "Are You Sure You Want to add this prospect to Sales Management",
            icon: "warning",
            confirmButtonText: "Yes",
            showCancelButton: true
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: "{!! route('prospect.add.pipeline') !!}",
                    data: {
                        'prospect': id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status == 1) {
                            toastr.success(response.message, {
                                timeOut: 5000
                            });
                        }

                        $('#opportunities_table').DataTable().ajax.reload();
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                            title: "Error",
                            text: textStatus,
                            icon: "error"
                        });
                    }
                });
            }
        })
    }

    function editOpportunity(id) {
        if (!id) {
            console.error('No opportunity ID provided');
            return;
        }
        console.log('Edit opportunity:', id);
    }

    function updatePipeline(id) {
        if (!id) {
            console.error('No opportunity ID provided');
            return;
        }
        console.log('Update pipeline:', id);
    }

    function onboardProspect() {
        window.location.href = "{{ route('leads.onboarding') }}";
    }
</script>
