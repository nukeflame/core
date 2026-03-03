<script>
    $(document).ready(function() {
        if (typeof $.fn.DataTable === 'undefined') {
            return;
        }

        const selectedOpportunityIds = new Set();
        const $bulkDeleteBtn = $('#deleteSelectedOpportunitiesBtn');
        const $selectedCount = $('#selectedOpportunitiesCount');
        const $selectAll = $('#selectAllOpportunities');

        function updateSelectionUi() {
            const selectedCount = selectedOpportunityIds.size;
            if ($selectedCount.length) {
                $selectedCount.text(selectedCount);
            }
            if ($bulkDeleteBtn.length) {
                $bulkDeleteBtn.toggleClass('d-none', selectedCount === 0);
            }

            const $rowCheckboxes = $('#opportunities_table .opportunity-select');
            if (!$rowCheckboxes.length || !$selectAll.length) {
                return;
            }

            const allChecked = $rowCheckboxes.length > 0 && $rowCheckboxes.filter(':checked').length ===
                $rowCheckboxes.length;
            const anyChecked = $rowCheckboxes.filter(':checked').length > 0;

            $selectAll.prop('checked', allChecked);
            $selectAll.prop('indeterminate', anyChecked && !allChecked);
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
                    d.class_group = $('#classGroupFilter').val();
                    d.class = $('#classFilter').val();
                    d.priority = $('#priorityFilter').val();
                    d.global_search = $('#globalSearch').val().trim();
                },
                error: function(xhr, error, code) {
                    $('#opportunities_table_wrapper').prepend(
                        '<div class="alert alert-danger">Error loading data. Please refresh the page.</div>'
                    );
                }
            },
            columns: [{
                    data: null,
                    name: 'select',
                    title: '',
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function(data, type, row) {
                        const opportunityId = $('<div/>').html(row.opportunity_id || '').text()
                            .trim();
                        if (type !== 'display') {
                            return opportunityId;
                        }

                        const isChecked = selectedOpportunityIds.has(opportunityId) ?
                            'checked' : '';
                        return `<input type="checkbox" class="opportunity-select" value="${opportunityId}" ${isChecked} aria-label="Select opportunity ${opportunityId}">`;
                    }
                },
                {
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
                } else if (data.effective_date) {
                    const effectiveDate = typeof moment !== 'undefined' ?
                        moment(data.effective_date) :
                        new Date(data.effective_date);

                    if (effectiveDate && (typeof moment !== 'undefined' ? effectiveDate.isValid() :
                            !isNaN(
                                effectiveDate.getTime()))) {
                        const now = typeof moment !== 'undefined' ? moment().startOf('day') :
                            new Date();
                        const effective = typeof moment !== 'undefined' ? effectiveDate.startOf(
                                'day') :
                            new Date(effectiveDate.getFullYear(), effectiveDate.getMonth(),
                                effectiveDate
                                .getDate());
                        const daysToEffective = typeof moment !== 'undefined' ?
                            effective.diff(now, 'days') :
                            Math.floor((effective - new Date(now.getFullYear(), now.getMonth(), now
                                .getDate())) / 86400000);

                        if (daysToEffective <= 7) {
                            $(row).addClass('highlight-critical');
                        } else if (daysToEffective <= 14) {
                            $(row).addClass('highlight-urgent');
                        } else if (daysToEffective <= 30) {
                            $(row).addClass('highlight-upcoming');
                        } else {
                            $(row).addClass('highlight-normal');
                        }
                    }
                }

                const rowOpportunityId = $('<div/>').html(data.opportunity_id || '').text().trim();
                $(row).attr('data-id', rowOpportunityId);
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
                updateSelectionUi();
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
        $('#statusFilter, #classGroupFilter, #classFilter, #priorityFilter').on('change', function() {
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(function() {
                try {
                    table.ajax.reload();
                } catch (error) {
                    console.error('Error reloading table with filters:', error);
                }
            }, 300);
        });

        $('#applyFiltersBtn').on('click', function() {
            try {
                table.ajax.reload();
            } catch (error) {
                console.error('Error applying filters:', error);
            }
        });

        $('#globalSearch').on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                $('#applyFiltersBtn').trigger('click');
            }
        });

        $('#resetFiltersBtn').on('click', function() {
            $('#globalSearch').val('');
            $('#statusFilter').val('');
            $('#classGroupFilter').val('');
            $('#classFilter').val('');
            $('#priorityFilter').val('');

            try {
                table.ajax.reload();
            } catch (error) {
                console.error('Error resetting filters:', error);
            }
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

        $(document).on('change', '.opportunity-select', function() {
            const opportunityId = $(this).val();
            if (!opportunityId) {
                return;
            }

            if ($(this).is(':checked')) {
                selectedOpportunityIds.add(opportunityId);
            } else {
                selectedOpportunityIds.delete(opportunityId);
            }

            updateSelectionUi();
        });

        $selectAll.on('change', function() {
            const shouldSelect = $(this).is(':checked');
            $('#opportunities_table .opportunity-select').each(function() {
                const opportunityId = $(this).val();
                if (!opportunityId) {
                    return;
                }

                $(this).prop('checked', shouldSelect);
                if (shouldSelect) {
                    selectedOpportunityIds.add(opportunityId);
                } else {
                    selectedOpportunityIds.delete(opportunityId);
                }
            });

            updateSelectionUi();
        });

        $bulkDeleteBtn.on('click', function() {
            const ids = Array.from(selectedOpportunityIds);
            if (!ids.length) {
                return;
            }

            Swal.fire({
                title: 'Delete selected opportunities?',
                html: `You are about to delete <strong>${ids.length}</strong> opportunit${ids.length > 1 ? 'ies' : 'y'}. This action can be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc3545'
            }).then(function(result) {
                if (!result.isConfirmed) {
                    return;
                }

                $.ajax({
                    type: 'POST',
                    url: "{{ route('leads.bulk_delete') }}",
                    data: {
                        opportunity_ids: ids
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        selectedOpportunityIds.clear();
                        $selectAll.prop('checked', false).prop('indeterminate',
                            false);
                        updateSelectionUi();
                        table.ajax.reload(null, true);

                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message ||
                                'Selected opportunities deleted successfully.');
                        }
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON?.message ||
                            'Failed to delete selected opportunities.';
                        Swal.fire({
                            title: 'Error',
                            text: message,
                            icon: 'error'
                        });
                    }
                });
            });
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
    }

    function onboardProspect() {
        window.location.href = "{{ route('leads.onboarding') }}";
    }
</script>
