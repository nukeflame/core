 <div class="card">
     <div class="card-body py-3 px-2">
         @if (in_array($coverReg->type_of_bus, ['TPR', 'TNP']) && in_array($coverReg->transaction_type, ['NEW', 'REN', 'EXT']))
         @else
             <a href="{{ route('docs.coverdebitnote', ['endorsement_no' => $coverReg->endorsement_no]) }}" target="_blank"
                 rel="noopener noreferrer" class="print-out-link pr-3" id="generateDebitNote">
                 <i class="bx bx-file me-1 align-middle"></i>Debit Note
             </a>
             <a href="{{ route('docs.coverdebitnote', ['endorsement_no' => $coverReg->endorsement_no]) }}"
                 id="generateCreditNote" rel="noopener noreferrer" data-endorsementno="{{ $coverReg->endorsement_no }}"
                 class="print-out-link pr-3">
                 <i class="bx bx-file me-1 align-middle"></i>Credit Notes
             </a>
         @endif
         <a href="{{ route('docs.coverslip', ['endorsement_no' => $coverReg->endorsement_no]) }}" target="_blank"
             rel="noopener noreferrer" class="print-out-link pr-3" id="generateCoverSlip"><i
                 class="bx bx-file me-1 align-middle"></i>Cover Slip
         </a>

         @if (count($endorsementNarration) > 0)
             <a href="{{ route('docs.endorsementslip', ['endorsement_no' => $coverReg->endorsement_no]) }}"
                 target="_blank" rel="noopener noreferrer" class="print-out-link" id="generateEndorsementSlip">
                 <i class="bx bx-file me-1 align-middle"></i> <span>Endorsement
                     Notice Slip</span></a>
         @endif
     </div>
 </div>
