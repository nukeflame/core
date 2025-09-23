<!-- Won Stage Modal -->
<div id="wonModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">🎉 Deal Won - Policy Binding</h3>
            <span class="close" onclick="closeModal('wonModal')">&times;</span>
        </div>
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Policy Number</label>
                    <input
                        type="text"
                        class="form-control"
                        placeholder="POL-2025-001625" />
                </div>
                <div class="form-group">
                    <label class="form-label">Binding Date</label>
                    <input type="date" class="form-control" value="2025-09-19" />
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Final Premium (KES)</label>
                    <input
                        type="number"
                        class="form-control"
                        value="5900000"
                        step="0.01" />
                </div>
                <div class="form-group">
                    <label class="form-label">Final Commission (%)</label>
                    <input
                        type="number"
                        class="form-control"
                        value="16.75"
                        step="0.01" />
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Leading Reinsurer</label>
                <select class="form-control">
                    <option>Lloyd's of London</option>
                    <option>Swiss Re</option>
                    <option>Munich Re</option>
                    <option>Hannover Re</option>
                    <option>SCOR</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Signing Completion Status</label>
                <div style="display: flex; gap: 1rem; margin-top: 0.5rem">
                    <label style="display: flex; align-items: center; gap: 0.5rem">
                        <input type="checkbox" checked /> Policy Documents Signed
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem">
                        <input type="checkbox" checked /> Premium Payment Confirmed
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem">
                        <input type="checkbox" /> Certificate Issued
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Success Notes</label>
                <textarea
                    class="form-control"
                    rows="3"
                    placeholder="Record key success factors, client satisfaction feedback, and lessons learned..."></textarea>
            </div>
        </div>
        <div class="modal-actions">
            <button class="btn btn-secondary" onclick="closeModal('wonModal')">
                Cancel
            </button>
            <button class="btn btn-primary" onclick="updateStage('won')">
                Mark as Won
            </button>
        </div>
    </div>
</div>