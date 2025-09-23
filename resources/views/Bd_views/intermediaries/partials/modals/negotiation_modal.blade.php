<!-- Negotiation Stage Modal -->
<div id="negotiationModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">🤝 Negotiation Stage Management</h3>
            <span class="close" onclick="closeModal('negotiationModal')">&times;</span>
        </div>
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Negotiation Status</label>
                    <select class="form-control">
                        <option>Initial Review</option>
                        <option>Terms Discussion</option>
                        <option>Premium Negotiation</option>
                        <option>Final Review</option>
                        <option>Pending Approval</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Last Contact Date</label>
                    <input type="date" class="form-control" value="2025-09-18" />
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Revised Premium (KES)</label>
                    <input type="number" class="form-control" placeholder="5800000" step="0.01" />
                </div>
                <div class="form-group">
                    <label class="form-label">Revised Commission (%)</label>
                    <input type="number" class="form-control" placeholder="17.50" step="0.01" />
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Key Negotiation Points</label>
                <textarea class="form-control" rows="3"
                    placeholder="Record key discussion points, concessions made, pending items..."></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Next Action Required</label>
                <select class="form-control">
                    <option>Await Client Response</option>
                    <option>Prepare Revised Quote</option>
                    <option>Schedule Follow-up Meeting</option>
                    <option>Submit to Underwriting</option>
                    <option>Legal Review Required</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Expected Decision Date</label>
                <input type="date" class="form-control" />
            </div>
        </div>
        <div class="modal-actions">
            <button class="btn btn-secondary" onclick="closeModal('negotiationModal')">
                Cancel
            </button>
            <button class="btn btn-primary" onclick="updateStage('negotiation')">
                Update Negotiation
            </button>
        </div>
    </div>
</div>


@push('script')
    <script>
        // $(document).ready(function() {
        //     $.ajaxSetup({
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         }
        //     });

        //     // Initialize DataTable
        //     const table = $('#reinsurersTable').DataTable({
        //         responsive: true,
        //         pageLength: 10,
        //         paging: false,
        //         searching: false,
        //         info: false,
        //         order: [
        //             [0, 'asc']
        //         ],
        //         language: {
        //             search: "Search reinsurers:",
        //             lengthMenu: "Show _MENU_ reinsurers per page",
        //             info: "Showing _START_ to _END_ of _TOTAL_ reinsurers",
        //             infoEmpty: "No reinsurers available",
        //             infoFiltered: "(filtered from _MAX_ total reinsurers)",
        //             zeroRecords: "No matching reinsurers found",
        //             emptyTable: "No reinsurers selected yet. Add reinsurers using the form above."
        //         },
        //         columnDefs: [{
        //                 targets: -1,
        //                 orderable: false,
        //                 searchable: false,
        //                 className: 'text-center'
        //             },
        //             {
        //                 targets: [2, 3, 4],
        //                 className: 'text-end'
        //             }
        //         ]
        //     });

        //     let selectedReinsurers = new Set();

        //     $('#addReinsurer').click(function() {
        //         const selectedOption = $('#availableReinsurers option:selected');
        //         const reinsurerShare = parseFloat($('#reinsurerShare').val());
        //         const reinsurerCommission = parseFloat($('#reinsurerCommission').val());

        //         if (!selectedOption.val()) {
        //             Swal.fire({
        //                 icon: 'warning',
        //                 title: 'Select Reinsurer',
        //                 text: 'Please select a reinsurer from the dropdown.',
        //                 confirmButtonColor: '#3085d6'
        //             });
        //             return;
        //         }

        //         if (!reinsurerShare || reinsurerShare <= 0 || reinsurerShare > 100) {
        //             Swal.fire({
        //                 icon: 'error',
        //                 title: 'Invalid Share',
        //                 text: 'Please enter a valid share percentage between 0.01% and 100%.',
        //                 confirmButtonColor: '#3085d6'
        //             }).then(() => {
        //                 $('#reinsurerShare').focus();
        //             });
        //             return;
        //         }

        //         if (isNaN(reinsurerCommission) || reinsurerCommission < 0 || reinsurerCommission > 50) {
        //             Swal.fire({
        //                 icon: 'error',
        //                 title: 'Invalid Commission',
        //                 text: 'Please enter a valid commission percentage between 0% and 50%.',
        //                 confirmButtonColor: '#3085d6'
        //             }).then(() => {
        //                 $('#reinsurerCommission').focus();
        //             });
        //             return;
        //         }

        //         // Check if reinsurer already selected
        //         if (selectedReinsurers.has(selectedOption.val())) {
        //             Swal.fire({
        //                 icon: 'info',
        //                 title: 'Already Selected',
        //                 text: 'This reinsurer has already been added to the list.',
        //                 confirmButtonColor: '#3085d6'
        //             });
        //             return;
        //         }

        //         const reinsurerData = {
        //             id: selectedOption.val(),
        //             name: selectedOption.data('name'),
        //             rating: selectedOption.data('rating'),
        //             capacity: selectedOption.data('capacity'),
        //             country: selectedOption.data('country'),
        //             share: reinsurerShare,
        //             commission: reinsurerCommission
        //         };

        //         const totalPremium = getTotalPremium(); // You'll need to implement this function
        //         const premiumAmount = (totalPremium * reinsurerShare / 100);

        //         const rowHtml = `
    //             <tr data-reinsurer-id="${reinsurerData.id}">
    //                 <td>
    //                     <div class="d-flex align-items-center">
    //                         <div>
    //                             <div class="fw-medium">${reinsurerData.name}</div>
    //                             <small class="text-muted">${reinsurerData.country}</small>
    //                         </div>
    //                     </div>
    //                 </td>
    //                 <td class="text-end">${reinsurerData.share.toFixed(2)}%</td>
    //                 <td class="text-end">${reinsurerData.commission.toFixed(2)}%</td>
    //                 <td class="text-end">$${premiumAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
    //                 <td class="text-center">
    //                     <button type="button" class="btn btn-danger btn-sm remove-reinsurer"
    //                             data-reinsurer-id="${reinsurerData.id}"
    //                             title="Remove Reinsurer">
    //                         <i class="bx bx-trash"></i>
    //                     </button>
    //                 </td>
    //             </tr>
    //         `;

        //         table.row.add($(rowHtml)).draw();

        //         // Add to selected reinsurers set
        //         selectedReinsurers.add(reinsurerData.id);

        //         // Update reinsurer count
        //         updateReinsurerCount();

        //         // Reset form
        //         resetForm();

        //         // Show success message
        //         Swal.fire({
        //             icon: 'success',
        //             title: 'Reinsurer Added!',
        //             text: `${reinsurerData.name} has been successfully added to the list.`,
        //             timer: 2000,
        //             showConfirmButton: false,
        //             toast: true,
        //             position: 'top-end'
        //         });

        //         // Update total shares display
        //         updateTotalShares();
        //     });

        //     // Remove reinsurer functionality
        //     $(document).on('click', '.remove-reinsurer', function() {
        //         const reinsurerID = $(this).data('reinsurer-id');
        //         const row = $(this).closest('tr');
        //         const reinsurerName = row.find('td:first .fw-medium').text();

        //         // Confirm deletion with SweetAlert
        //         Swal.fire({
        //             title: 'Remove Reinsurer?',
        //             text: `Are you sure you want to remove ${reinsurerName} from the list?`,
        //             icon: 'question',
        //             showCancelButton: true,
        //             confirmButtonColor: '#d33',
        //             cancelButtonColor: '#3085d6',
        //             confirmButtonText: 'Yes, remove it!',
        //             cancelButtonText: 'Cancel'
        //         }).then((result) => {
        //             if (result.isConfirmed) {
        //                 // Remove from DataTable
        //                 table.row(row).remove().draw();

        //                 // Remove from selected reinsurers set
        //                 selectedReinsurers.delete(reinsurerID.toString());

        //                 // Update reinsurer count
        //                 updateReinsurerCount();

        //                 // Update total shares display
        //                 updateTotalShares();

        //                 // Show success message
        //                 Swal.fire({
        //                     icon: 'info',
        //                     title: 'Removed!',
        //                     text: `${reinsurerName} has been removed from the list.`,
        //                     timer: 2000,
        //                     showConfirmButton: false,
        //                     toast: true,
        //                     position: 'top-end'
        //                 });
        //             }
        //         });
        //     });

        //     // Helper functions
        //     function updateReinsurerCount() {
        //         const count = selectedReinsurers.size;
        //         $('#reinsurerCount').text(count);
        //     }

        //     function resetForm() {
        //         $('#availableReinsurers').val('');
        //         $('#reinsurerShare').val('');
        //         $('#reinsurerCommission').val('25'); // Reset to default
        //     }

        //     function showAlert(message, type = 'info') {
        //         // Using SweetAlert2 for better UX
        //         let icon = 'info';
        //         let title = 'Information';

        //         switch (type) {
        //             case 'success':
        //                 icon = 'success';
        //                 title = 'Success';
        //                 break;
        //             case 'warning':
        //                 icon = 'warning';
        //                 title = 'Warning';
        //                 break;
        //             case 'error':
        //                 icon = 'error';
        //                 title = 'Error';
        //                 break;
        //         }

        //         Swal.fire({
        //             icon: icon,
        //             title: title,
        //             text: message,
        //             timer: 3000,
        //             showConfirmButton: false,
        //             toast: true,
        //             position: 'top-end'
        //         });
        //     }

        //     function getTotalPremium() {
        //         // This should return the total premium amount for the policy
        //         // You'll need to implement this based on your application logic
        //         // For now, returning a placeholder value
        //         const totalPremiumInput = $('#totalPremium'); // Assuming you have this field
        //         if (totalPremiumInput.length && totalPremiumInput.val()) {
        //             return parseFloat(totalPremiumInput.val());
        //         }
        //         return 1000000; // Default placeholder value
        //     }

        //     function updateTotalShares() {
        //         let totalShares = 0;

        //         // Calculate total shares from all selected reinsurers
        //         table.rows().every(function() {
        //             const rowData = $(this.node());
        //             const shareText = rowData.find('td:nth-child(3)').text();
        //             const share = parseFloat(shareText.replace('%', ''));
        //             if (!isNaN(share)) {
        //                 totalShares += share;
        //             }
        //         });

        //         // Update display (you might want to add a total shares display element)
        //         updateTotalSharesDisplay(totalShares);

        //         // Validate total shares don't exceed 100%
        //         if (totalShares > 100) {
        //             Swal.fire({
        //                 icon: 'warning',
        //                 title: 'Share Limit Exceeded',
        //                 text: `Total shares (${totalShares.toFixed(2)}%) exceed 100%. Please review the allocation.`,
        //                 confirmButtonColor: '#f39c12'
        //             });
        //         }
        //     }

        //     function updateTotalSharesDisplay(totalShares) {
        //         // Find or create total shares display
        //         let totalSharesDisplay = $('.total-shares-display');
        //         if (totalSharesDisplay.length === 0) {
        //             const displayHtml = `
    //         <div class="total-shares-display mt-2">
    //             <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
    //                 <span class="fw-medium">Total Written Shares:</span>
    //                 <span class="badge bg-primary total-shares-value">0.00%</span>
    //             </div>
    //         </div>
    //     `;
        //             $('.selected-reinsurers-section').append(displayHtml);
        //             totalSharesDisplay = $('.total-shares-display');
        //         }

        //         // Update the value
        //         const badgeClass = totalShares > 100 ? 'bg-danger' : totalShares === 100 ? 'bg-success' :
        //             'bg-primary';
        //         totalSharesDisplay.find('.total-shares-value')
        //             .removeClass('bg-primary bg-success bg-danger')
        //             .addClass(badgeClass)
        //             .text(`${totalShares.toFixed(2)}%`);
        //     }

        //     // Validate share input to prevent exceeding remaining capacity
        //     $('#reinsurerShare').on('input', function() {
        //         const currentValue = parseFloat($(this).val());
        //         if (isNaN(currentValue)) return;

        //         // Calculate remaining capacity
        //         let totalShares = 0;
        //         table.rows().every(function() {
        //             const rowData = $(this.node());
        //             const shareText = rowData.find('td:nth-child(3)').text();
        //             const share = parseFloat(shareText.replace('%', ''));
        //             if (!isNaN(share)) {
        //                 totalShares += share;
        //             }
        //         });

        //         const remainingCapacity = 100 - totalShares;

        //         if (currentValue > remainingCapacity) {
        //             Swal.fire({
        //                 icon: 'warning',
        //                 title: 'Insufficient Capacity',
        //                 text: `Maximum available share is ${remainingCapacity.toFixed(2)}%. The value has been adjusted.`,
        //                 timer: 3000,
        //                 showConfirmButton: false,
        //                 toast: true,
        //                 position: 'top-end'
        //             });
        //             $(this).val(remainingCapacity.toFixed(2));
        //         }
        //     });

        //     const $categorySelect = $('#category_type');
        //     const $updateCategoryTypeModal = $('#updateCategoryTypeModal');
        //     const $updateCategorySubmitBtn = $('#updateCategorySubmitBtn');

        //     $('#updateCategoryForm').on('submit', function(e) {
        //         e.preventDefault();

        //         if (!$categorySelect.val()) {
        //             $categorySelect.addClass('is-invalid');
        //             $categorySelect.focus();
        //             return false;
        //         } else {
        //             $categorySelect.removeClass('is-invalid');
        //         }

        //         $updateCategorySubmitBtn.addClass('btn-loading');
        //         $updateCategorySubmitBtn.html('<span class="">Updating...</span>');
        //         $updateCategorySubmitBtn.prop('disabled', true);

        //         const formData = new FormData(this);
        //         const actionUrl = $(this).attr('action');

        //         $.ajax({
        //             url: actionUrl,
        //             method: 'POST',
        //             data: formData,
        //             processData: false,
        //             contentType: false,
        //             headers: {
        //                 'X-Requested-With': 'XMLHttpRequest'
        //             },
        //             success: function(response) {
        //                 if (response.success) {
        //                     Swal.fire({
        //                         icon: 'success',
        //                         title: 'Success',
        //                         text: 'Category type updated successfully!',
        //                         timer: 2000,
        //                         showConfirmButton: false,
        //                         toast: true,
        //                         position: 'top-end'
        //                     }).then(() => {
        //                         location.reload();
        //                     });
        //                 }
        //                 $updateCategoryTypeModal.modal('hide');

        //             },
        //             error: function(xhr, status, error) {
        //                 let errorMessage = 'An error occurred while updating the category.';

        //                 if (xhr.responseJSON && xhr.responseJSON.message) {
        //                     errorMessage = xhr.responseJSON.message;
        //                 } else if (xhr.responseJSON && xhr.responseJSON.errors) {
        //                     const errors = xhr.responseJSON.errors;
        //                     errorMessage = Object.values(errors).flat().join('<br>');
        //                 }

        //                 toastr.error(errorMessage, 'error');
        //             },
        //             complete: function() {
        //                 $updateCategorySubmitBtn.removeClass('btn-loading');
        //                 $updateCategorySubmitBtn.html(
        //                     '<i class="bi bi-check-circle me-1"></i>Update Category');
        //                 $updateCategorySubmitBtn.prop('disabled', false);
        //             }
        //         });
        //     });

        //     $updateCategoryTypeModal.on('hidden.bs.modal', function() {
        //         $('#updateCategoryForm')[0].reset();
        //         $updateCategorySubmitBtn.removeClass('btn-loading');
        //         $updateCategorySubmitBtn.html('<i class="bi bi-check-circle me-1"></i>Update Category');
        //         $updateCategorySubmitBtn.prop('disabled', false);
        //         $categorySelect.removeClass('is-invalid');
        //     });
        // });
    </script>
@endpush
