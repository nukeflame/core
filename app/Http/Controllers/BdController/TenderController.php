<?php

namespace App\Http\Controllers\BdController;

use App\Http\Controllers\Controller;
use App\Jobs\ApprovalJob;
use App\Jobs\SendTenderEmail;
use App\Models\Bd\CustomerContact;
use App\Models\Bd\Leads\TenderApproval;
use App\Models\Bd\PipelineOpportunity;
use App\Models\Bd\Tender;
use App\Models\Bd\TenderDocParam;
use App\Models\Bd\TenderSubcategory;
use App\Models\Bd\TenderToc;
use App\Models\Company;
use App\Models\Customer;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Log;
use Str;
use Symfony\Component\HttpFoundation\Response;

class TenderController extends Controller
{

    public function listTenders(Request $request)
    {
        $prospect_id = $request->opportunity_id;
        if (!empty($prospect_id)) {
            $cedant = PipelineOpportunity::where('opportunity_id', $prospect_id)->first();
            $tenders = Tender::where('prospect_id', $prospect_id)->paginate(10);
            return view('Bd_views.tenders.tenders_list', [
                'tenders' => $tenders,
                'prospect_id' => $prospect_id,
                'cedant' => $cedant,
            ]);
        } else {
            $tender_ids = TenderApproval::whereJsonContains('approver_id', [(string) auth()->id()])
                ->where('status', '0')
                ->pluck('tender_id')
                ->toArray();

            $tenders = Tender::whereIn('id', $tender_ids)->paginate(10);

            return view('Bd_views.tenders.tenders_list', [
                'tenders' => $tenders,
                'approver_review' => 1
            ]);
        }
    }

    public function AddTender(Request $request)
    {


        $validatedData = $request->validate([
            'tender_no' => 'required',
            'client_name' => 'required',
            // 'proposal_category' => 'required',
            'proposal_nature' => 'required',
            'tender_name' => 'required',
            'tender_description' => 'required',
            'closing_date' => 'required',
            'prospect_id' => 'required|exists:pipeline_opportunities,opportunity_id',
        ]);

        if (count(DB::table('tenders')->where('tender_no', "=", $request->tender_no)->get()) === 0) {
            // Create a new tender
            $tender = new Tender();
            $tender->tender_no = $validatedData['tender_no'];
            $tender->tender_name = $validatedData['tender_name'];
            $tender->client_name = $validatedData['client_name'];
            // $tender->tender_category = $validatedData['proposal_category'];
            $tender->tender_nature = $validatedData['proposal_nature'];
            $tender->tender_description = $validatedData['tender_description'];
            $tender->closing_date = $validatedData['closing_date'];
            $tender->tender_status = 'PENDING';
            $tender->prospect_id = $validatedData['prospect_id'];
            $tender->created_by = Auth::user()->username;
            $tender->updated_by = Auth::user()->username;
            $tender->save();

            //  create activity
            $desc = 'created ' . $validatedData['tender_name'] . ' of tender number  ' . $validatedData['tender_no'] . ' for client ' . $validatedData['client_name'];
            $process = 'add-proposal';

            // Return a success response
            $status = 200;
            $message = 'Your tender has been saved successfully!';
            return response(compact('message', 'status'));
        } else {
            $status = 201;
            $message = 'Tender number exists!';
            return response(compact('message', 'status'));
        }
    }

    public function editTender(Request $request)
    {

        // Validate the incoming request data
        $validatedData = $request->validate([
            'tender_no' => 'required',
            'client_name' => 'required',
            // 'proposal_category' => 'required',
            'proposal_nature' => 'required',
            'tender_name' => 'required',
            'tender_description' => 'required',
            'closing_date' => 'required',
            'prospect_id' => 'required|exists:pipeline_opportunities,opportunity_id',
        ]);

        // Create a new tender
        $tender = Tender::where('tender_no', $validatedData['tender_no'])->update([
            'tender_name' => $validatedData['tender_name'],
            'client_name' => $validatedData['client_name'],
            // 'tender_category' => $validatedData['proposal_category'],
            'tender_nature' => $validatedData['proposal_nature'],
            'tender_description' => $validatedData['tender_description'],
            'closing_date' => $validatedData['closing_date'],
            'updated_by' => Auth::user()->username,
        ]);

        //  create activity
        $desc = 'updated ' . $validatedData['tender_name'] . ' of tender number  ' . $validatedData['tender_no'] . ' for client ' . $validatedData['client_name'];
        $process = 'add-proposal';

        return redirect()->route('tender.tenderdetails')->with("success", "Editing Successfull");
    }

    public function TenderDetails(Request $request)
    {
        $prospect_id = $request->prospect_id;
        if (empty($prospect_id)) {
            $prospect_id = Tender::where('tender_no', $request->tender_ref)->value('prospect_id');
        }
        $approval = TenderApproval::with('tender')->where('tender_no', $request->tender_ref)->first();



        $customer_id = PipelineOpportunity::where('opportunity_id', $prospect_id)
            ->value('customer_id');

        $tender = Tender::where('tender_no', $request->tender_ref)
            ->where('tender_name', $request->tender_namex)
            ->first();

        $tenderTocs = TenderToc::where('tender_no', $request->tender_ref)
            ->where('tender_name', $request->tender_namex)
            ->get()
            ->sortBy(function ($tenderToc) {
                return $tenderToc->toc_category === 'SECT' ? 0 : 1;
            })
            ->sortBy('sort_no');


        $tenderTocSecs = TenderToc::where('tender_no', $request->tender_ref)
            ->where('tender_name', $request->tender_namex)
            ->where('toc_category', 'SECT')
            ->get()
            ->sortBy('sort_no');

        $tenderTocItems = TenderToc::where('tender_no', $request->tender_ref)
            ->where('tender_name', $request->tender_namex)
            ->where('toc_category', 'ITEM')
            ->get()
            ->sortBy('sort_no');

        // Consider limiting this query if you don't need all records
        $tenderDocs = TenderDocParam::all();

        // Fix the type mismatch using Eloquent instead of Query Builder
        $tendersubcats = TenderSubcategory::with('tenderDocParam')
            ->where('tender_no', $request->tender_ref)
            ->get();
        $approvers = User::all();


        return view('Bd_views.tenders.tender_dtl', [
            'tender' => $tender,
            'tenderTocs' => $tenderTocs,
            'tenderTocSecs' => $tenderTocSecs,
            'tenderTocItems' => $tenderTocItems,
            'tenderDocs' => $tenderDocs,
            'tendersubcats' => $tendersubcats,
            'prospect_id' => $prospect_id,
            'customer_id' => $customer_id,
            'approvers' => $approvers,
            'approval' => $approval
        ]);
    }

    public function SearchTocCatgery(Request $request)
    {

        $query = $request->input('query');

        $categories = TenderToc::where('toc_description', 'like', '%' . strtoupper($request->details['query']) . '%')
            ->where('tender_no', "=", $request->details['tender_no'])
            ->take(3)
            ->get(['toc_description']);

        return response()->json($categories);
    }

    public function saveTendorColors(Request $request)
    {
        try {
            DB::table('tenders')->where('tender_no', "=", $request->tender_no)->update([
                'footer_color' => $request->footer_color,
                'footer_content' => $request->footer_content,
            ]);

            //  create activity
            $desc = ' added color to tender document footer color ' . $request->footer_color . ' and footer content ' . $request->footer_content;
            $process = 'document-settings';


            $status = 200;
            $message = 'Color saved successfully!';
            return response(compact('status', 'message'));
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function AddTenderToc(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'tender_no' => 'required|string|max:50',
                'tender_name' => 'required|string|max:200',
                'toc_category' => 'required|string|max:300',
                // 'tocDescription' => 'required|string',
                'toc_head' => 'required|string',
                // 'sortNo' => 'required|string',
            ]);

            $countToc = TenderToc::where('tender_no', $request->tender_no)
                ->where('tender_name', $request->tender_name)
                ->max('toc_no');
            // dd($request->all(), $countToc);
            // Create a new tender
            $tender = DB::table('tender_toc')->insert([
                'tender_no' => $validatedData['tender_no'],
                'tender_name' => $validatedData['tender_name'],
                'toc_no' => $countToc + 1,
                'toc_category' => $validatedData['toc_category'],
                'toc_section' => strtoupper($request->toc_section),
                'toc_head' => strtoupper($request->toc_head),
                // 'toc_description' => $validatedData['tocDescription'],
                'toc_description' => strtoupper($request->toc_head),
                // 'sort_no' => $validatedData['sortNo'],
                'created_by' => Auth::user()->username,
                'updated_by' => Auth::user()->username,
            ]);

            for ($i = 0; $i < count($request->subcat); $i++) {
                $max = DB::table('tender_subcategories')->max('subcat_id') + 1;
                DB::table('tender_subcategories')->insert([
                    'tender_no' => $validatedData['tender_no'],
                    'toc_no' => $countToc + 1,
                    'subcat_id' => $max,
                    'subcat_desc' => $request->subcat[$i],
                    'doc_id' => $request->subattach[$i],
                ]);
            }

            //  create activity
            $desc = ' created ' . $validatedData['tender_name'] . ' of tender TOC number ' . $validatedData['tender_no'];
            $process = 'add-toc-section';

            $status = 200;
            $message = 'Tender TOC saved successfully!';
            // Return a success response
            return response(compact('status', 'message'));
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function AddTenderTocItem(Request $request)
    {
        DB::beginTransaction();
        try {
            // First validate the data
            $validatedData = $request->validate([
                'tender_no' => 'required|string|max:50',
                'tender_name' => 'required|string|max:200',
                'toc_category' => 'required|string|max:300',
                'toc_section' => 'required',
                'tocDescription' => 'required|string',
            ]);

            // Update the tender TOC
            $updateResult = TenderToc::where('tender_no', $validatedData['tender_no'])
                ->where('toc_no', $validatedData['toc_section'])
                ->update([
                    'toc_head' => $request->toc_sec_head,
                    'toc_description' => $request->toc_sec_head,
                ]);

            // Process subcategories
            // Use the subcat array length since it contains all entries
            for ($i = 0; $i < count($request->subcat); $i++) {
                $subcatId = isset($request->subcatid[$i]) ? $request->subcatid[$i] : null;
                $attachId = isset($request->subattach[$i]) ? $request->subattach[$i] : null;

                // If subcatId exists, update the record
                if ($subcatId) {
                    DB::table('tender_subcategories')
                        ->where('tender_no', $validatedData['tender_no'])
                        ->where('toc_no', $validatedData['toc_section'])
                        ->where('subcat_id', $subcatId)
                        ->update([
                            'subcat_desc' => $request->subcat[$i],
                            'doc_id' => $attachId,
                        ]);
                } else {
                    // If subcatId doesn't exist, create a new record
                    $max = DB::table('tender_subcategories')->max('subcat_id') + 1;
                    DB::table('tender_subcategories')->insert([
                        'subcat_id' => $max,
                        'tender_no' => $validatedData['tender_no'],
                        'toc_no' => $validatedData['toc_section'],
                        'subcat_desc' => $request->subcat[$i],
                        'doc_id' => $attachId,
                    ]);
                }
            }

            //  create activity
            $desc = ' added tender item to tender number ' . $validatedData['tender_no'] . ' section ' . $validatedData['toc_section'];
            $process = 'add-proposal-subcategories';

            DB::commit();
            $status = 200;
            $message = 'Tender TOC Section Item saved successfully!';
            return response(compact('status', 'message'));
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th; // Uncomment to see the actual error message
        }
    }

    public function viewDocument($id)
    {

        $document = DB::table('tender_doc_param')->where('doc_id', $id)->first();
        return response()->file(public_path('uploads/' . $document->base64));
    }

    public function listTenderDocsParam(Request $request)
    {
        $tenderDocs = TenderDocParam::select('tender_doc_param.*')

            ->join(
                DB::raw('(SELECT doc_name, MAX(created_at) as latest_created_at FROM tender_doc_param GROUP BY doc_name) as latest'),
                'tender_doc_param.doc_name',
                '=',
                'latest.doc_name'
            )
            ->whereColumn('tender_doc_param.created_at', '=', 'latest.latest_created_at')
            ->orderByDesc('tender_doc_param.created_at')
            ->paginate(10);

        return view('Bd_views.tenders.tender_doc_param', [
            'tenderDocs' => $tenderDocs,
        ]);
    }

    public function getSubcatDoc(Request $request)
    {
        $subcat = $request->subcat;

        $filterWordLength = strlen($subcat);

        $results = TenderDocParam::select('*')
            ->selectRaw('
                    LENGTH(LOWER(doc_name)) - LENGTH(REPLACE(LOWER(doc_name), LOWER(?), \'\')) AS match_count
                ', [$subcat])
            ->whereRaw('
                    (
                        LENGTH(LOWER(doc_name)) - LENGTH(REPLACE(LOWER(doc_name), LOWER(?), \'\')) > ?
                    )
                ', [$subcat, (float) ($filterWordLength / 2)])
            ->orderByDesc('match_count')
            ->first();

        if (!is_null($results)) {
            $docid = $results->doc_id;
            return ['status' => 1, 'docid' => $docid];
        }
        return ['status' => 0];
    }

    public function previewDoc(Request $request)
    {
        $document = $request->doc;
        $mimetype = $document->getClientMimeType();
        $document = file_get_contents($document);
        $document = base64_encode($document);

        $encoded_doc = 'data:' . $mimetype . ';base64,' . $document;

        return $encoded_doc;
    }

    public function AddTenderDocParam(Request $request)
    {

        $uploadsPath = public_path('uploads');

        try {
            $validatedData = $request->validate([
                'docName' => 'required',
                'docDescription' => 'required',
                'renewableState' => 'required',
                'attachment' => 'required|file',
            ]);

            $file = $request->file('attachment');

            if ($file && $file->isValid()) {
                $mimetype = $file->getClientMimeType();

                $fileContent = file_get_contents($file);

                $originalNameWithoutExtension = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

                $filename = mt_rand() . '_' . $originalNameWithoutExtension . '.' . $file->getClientOriginalExtension();

                $file->move($uploadsPath, $filename);

                if ($request->tenderdocId === null) {

                    $tender = new TenderDocParam();

                    $tender->doc_name = $validatedData['docName'];
                    $tender->doc_description = $validatedData['docDescription'];
                    $tender->base64 = $filename;
                    $tender->doc_status = 'ACTIVE';
                    $tender->expiry_date = $request->expirydate;
                    $tender->renewable = $request->renewableState; //1 renewable, 2 not renewable
                    $tender->mimetype = $mimetype;
                    $tender->created_by = Auth::user()->username;
                    $tender->updated_by = Auth::user()->username;
                    $tender->save();

                    // updated tender_doc_id to have the new doc id
                    DB::table('tender_doc_param')->where('doc_id', "=", $tender->doc_id)->update([
                        'tender_doc_id' => $tender->doc_id,
                    ]);

                    $status = 200;
                    $message = "Tender document uploaded successfully !";
                    return response(compact('status', 'message'));
                } else if ($request->tenderStatus == 'U') {

                    $tender = TenderDocParam::where('tender_doc_id', $request->tenderdocId)->first();

                    if (!$tender) {
                        // Handle case where the record is not found
                        \Log::error('Tender document not found for update: ' . $request->tenderdocId);
                        return response()->json([
                            'status' => 404,
                            'message' => 'Tender document not found for update'
                        ], 404);
                    }

                    $tender->doc_name = $validatedData['docName'];
                    $tender->doc_description = $validatedData['docDescription'];
                    $tender->expiry_date = $request->expirydate;
                    $tender->renewable = $request->renewableState; //1 renewable, 2 not renewable
                    $tender->base64 = $filename;
                    $tender->doc_status = 'ACTIVE';
                    $tender->mimetype = $mimetype;
                    $tender->created_by = Auth::user()->username;
                    $tender->updated_by = Auth::user()->username;
                    $tender->save();

                    //  create activity
                    $desc = ' uploaded tender documents ' . $validatedData['docName'] . ' with description ' . $validatedData['docDescription'] . ' with expiry date of ' . $request->expirydate;
                    $process = 'update-tender-doc';

                    $status = 200;
                    $message = "Tender document uploaded successfully !";
                    return response(compact('status', 'message'));
                }
            }
        } catch (\Throwable $th) {

            $status = 400;
            $message = "Invalid file uploaded !";
            return response(compact('status', 'message'));
        }
    }

    public function viewDocumentDetails($docId)
    {
        try {
            $tenderDoc = TenderDocParam::where('doc_id', "=", $docId)->first();

            if ($tenderDoc) {
                return response()->json([
                    'tender_doc_id' => $tenderDoc->tender_doc_id,
                    'doc_name' => $tenderDoc->doc_name,
                    'doc_description' => $tenderDoc->doc_description,
                    'doc_status' => $tenderDoc->doc_status,
                ]);
            } else {
                $code = 404;
                $message = "Document detais not found";
                return response(compact('code', 'message'));
            }
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function TenderPrint(Request $request)
    {
        $tender_no = $request->print_tender_no;
        $tender_name = $request->print_tender_name;

        $tender = Tender::where('tender_no', $tender_no)->where('tender_name', $tender_name)->first();
        $tenderTocs = TenderToc::where('tender_no', $tender_no)->where('tender_name', $tender_name)->orderBy('sort_no')->get();
        $tenderTocSecs = TenderToc::where('tender_no', $tender_no)->where('tender_name', $tender_name)->where('toc_category', 'SECT')->get()->sortBy('sort_no');
        $tenderTocItems = TenderToc::where('tender_no', $tender_no)->where('tender_name', $tender_name)->where('toc_category', 'ITEM')->get()->sortBy('sort_no');
        $tenderDocs = TenderDocParam::get();
        $tendersubs = DB::table('tender_subcategories')->where('tender_no', $tender_no)->get();


        return response()->json([
            'tender' => $tender,
            'tenderTocs' => $tenderTocs,
            'tenderTocSecs' => $tenderTocSecs,
            'tenderTocItems' => $tenderTocItems,
            'tenderDocs' => $tenderDocs,
            'tendersubs' => $tendersubs,

        ]);
    }

    public function getTenderSectcheckeditems(Request $request)
    {
        // dd($request);

        $TocSecsChecked = DB::table('tender_subcategories')
            ->where('tender_no', $request->tender_no)
            ->where('toc_no', $request->tocSecNo)
            ->get();
        // $TocSecsChecked = TenderToc::where('tender_no',$request->tender_no)->where('tender_name',$request->tender_name)->where('toc_section',$request->tocSecNo)->where('toc_category','ITEM')->get();
        return response()->json(['TocSecsChecked' => $TocSecsChecked], 200);
    }

    public function incrementNumber($currentNumber, $format)
    {
        switch ($format) {
            case 'i':
                return strtolower($this->toRoman($this->fromRoman($currentNumber) + 1)); // Convert Roman numeral to integer, increment, then convert back to Roman numeral
            case '1':
                return $currentNumber + 1; // for integers
            case 'a':
                return chr(ord($currentNumber) + 1); // for lowercase letters
            case 'A':
                return chr(ord($currentNumber) + 1); // for uppercase letters
            default:
                return $currentNumber;
        }
    }

    public function fromRoman($roman)
    {
        $romans = array(
            'I' => 1,
            'V' => 5,
            'X' => 10,
            'L' => 50,
            'C' => 100,
            'D' => 500,
            'M' => 1000,
        );
        $result = 0;
        $previous = 0;
        $roman = strtoupper($roman);

        for ($i = strlen($roman) - 1; $i >= 0; $i--) {
            $current = $romans[$roman[$i]];
            if ($current >= $previous) {
                $result += $current;
            } else {
                $result -= $current;
            }
            $previous = $current;
        }
        return $result;
    }

    public function toRoman($number)
    {
        $map = array(
            'M' => 1000,
            'CM' => 900,
            'D' => 500,
            'CD' => 400,
            'C' => 100,
            'XC' => 90,
            'L' => 50,
            'XL' => 40,
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1,
        );
        $result = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if ($number >= $int) {
                    $number -= $int;
                    $result .= $roman;
                    break;
                }
            }
        }
        return $result;
    }

    //END


    public function editSubcat(Request $request)
    {
        try {
            $validated = $request->validate([
                'subcat_id' => 'required',
                'tender_no' => 'required',
                'tender_name' => 'required|string',
                'toc_no' => 'required|string',
                'subcat_desc' => 'required|string|max:200',
                'doc_id' => 'required',
            ]);

            // Perform update using query builder
            DB::table('tender_subcategories')
                ->where('subcat_id', $validated['subcat_id'])
                ->where('tender_no', $validated['tender_no'])
                ->where('toc_no', $validated['toc_no'])
                ->update([
                    'subcat_desc' => $validated['subcat_desc'],
                    'doc_id' => $validated['doc_id'] ?: null,
                ]);

            return response()->json([
                'status' => 200,
                'message' => 'Subcategory updated successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating subcategory: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Failed to update subcategory: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function editTocSection(Request $request)
    {
        try {
            $validated = $request->validate([
                'tender_no' => 'required|string',
                'toc_no' => 'required|string',
                'toc_head' => 'required|string|max:200',
            ]);



            // Update TOC section
            DB::table('tender_toc')
                ->where('tender_no', $validated['tender_no'])
                ->where('toc_no', $validated['toc_no'])->update([
                    'toc_description' => $validated['toc_head'],
                ]);

            return response()->json([
                'status' => 200,
                'message' => 'TOC section updated successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating TOC section: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Failed to update TOC section: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function sendTenderEmail(Request $request)
    {



        $stage_id = $request->stage_id;
        $id = $request->prospect_id;
        $email_dated = $request->email_dated;
        $commence_year = $request->commence_year;
        $mainEmail = $request->mainEmail;
        $ccEmails = $request->ccEmails;


        PipelineOpportunity::where('opportunity_id', $id)->Update([
            'stage' => $stage_id,
        ]);

        Tender::where('prospect_id', $id)->update([
            'commence_year' => $commence_year,
            'email_dated' => $email_dated
        ]);
        $customer_id = PipelineOpportunity::where('opportunity_id', $id)
            ->value('customer_id');

        $customer = Customer::where('customer_id', $customer_id)
            ->first();
        $contact_person = CustomerContact::where('customer_id', $customer_id)->value('contact_name');
        $doc_no = TenderToc::where('tender_no', $request->tender_no)
            ->pluck('toc_no');
        $doc_names = TenderDocParam::whereIn('doc_id', $doc_no)
            ->pluck('doc_name')
            ->toArray();




        $company = Company::first();


        try {
            // Validate request
            $validated = $request->validate([
                'prospect_id' => 'required',
                'stage_id' => 'required',
                'tender_no' => 'required',
                'tender_name' => 'required',
                'pdf_base64' => 'required',
                'remarks' => 'required',
                'status' => 'required'
            ]);

            DB::table('tender_approvals')->where('tender_no', $validated['tender_no'])->update([
                'remarks' => $validated['remarks'],
                'status' => $validated['status']

            ]);
            $view_path = 'printouts.';
            $view_name = $view_path . 'reply_cedant_document_requested';
            $pdfFolderPath = 'uploads/tender_letters';
            $pdfFilename = $request->tender_no . ' ' . 'Letter' . '_' . mt_rand() . '.pdf';


            $pdfPath = $pdfFolderPath . '/' . $pdfFilename;

            $data = [
                'tender_no' => $request->tender_no,
                'company' => $company,
                'customer' => $customer,
                'contact_person' => $contact_person,
                'doc_names' => $doc_names,
                'email_dated' => $email_dated,
                'commence_year' => $commence_year,
                'stage' => 2

            ];
            $pdf = Pdf::loadView($view_name, $data)
                ->setPaper('a4', 'portrait')
                ->setWarnings(false);

            $pdf->set_option('isHtml5ParserEnabled', true);
            $pdf->set_option('isPhpEnabled', true);
            $pdf->set_option('isRemoteEnabled', true);
            $pdf->render();
            try {
                Storage::disk('s3')->put($pdfPath, $pdf->output());

                // Check if the PDF was saved in S3
                if (!Storage::disk('s3')->exists($pdfPath)) {
                    return response()->json(['error' => 'Failed to save PDF to S3.'], 500);
                }
            } catch (\Exception $e) {
                return response()->json(['error' => 'S3 upload error: ' . $e->getMessage()], 500);
            }

            $tender = Tender::where('prospect_id', $request->prospect_id)
                ->where('tender_no', $request->tender_no)
                ->first();
            $approval = TenderApproval::where('tender_no', $validated['tender_no'])->first();

            ApprovalJob::dispatch($approval);

            // Dispatch the Job
            SendTenderEmail::dispatch($tender, $request->pdf_base64, $request->prospect_id, $pdfPath, $pdfFilename, $mainEmail, $ccEmails);

            return response()->json([
                'status' => 200,
                'message' => 'Email is being processed and will be sent shortly.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to queue email: ' . $e->getMessage()
            ], 500);
        }
    }


    // search_tender emails
    public function search(Request $request)
    {
        $customer_id = $request->customer_id;

        // Validate the search term
        $request->validate([
            'search_term' => 'required|string|min:2',
        ]);

        $searchTerm = strtolower(trim($request->input('search_term')));

        if ($searchTerm == 'cedant') {

            $contacts = CustomerContact::where('customer_id', $customer_id)->get();

            // Map contacts to the expected format
            $cedantContacts = $contacts->map(function ($contact) {
                return [
                    'id' => $contact->id,
                    'name' => $contact->contact_name,
                    'role' => $contact->role ?? 'N/A', // Fallback if role is null
                    'phone' => $contact->phone ?? 'N/A', // Fallback if phone is null
                    'email' => $contact->contact_email,
                    'type' => 'cedant',
                ];
            });
            if ($cedantContacts->isNotEmpty()) {
                return response()->json([
                    'type' => 'cedant',
                    'data' => $cedantContacts,
                ]);
            }
        } else if ($searchTerm == 'department') {

            // Search for departments (assuming you have a Department model)
            $departments = User::get();

            // Collect all personnel from matching departments
            $departmentPersonnel = $departments->map(function ($department) {

                return [
                    'id' => $department->id,
                    'name' => $department->name,
                    'email' => $department->email,
                    'type' => 'department',
                ];
            });

            if ($departmentPersonnel->isNotEmpty()) {
                return response()->json([
                    'type' => 'department',
                    'data' => $departmentPersonnel,
                ]);
            }

            // No results found
            return response()->json([
                'type' => null,
                'data' => [],
            ]);
        }
    }



    public function submitForApproval(Request $request)
    {


        try {
            $validated = $request->validate([

                'tender_id' => 'required|exists:tenders,id',
                'tender_no' => 'required',
                'stage_id' => 'required',
                'email_dated' => 'required|date',
                'commence_year' => 'required',
                'mainEmail' => 'required',
                'mainEmail.email' => 'required|email',
                'mainEmail.name' => 'required',
                'mainEmail.type' => 'required',
                'ccEmails' => 'nullable|array',
                'ccEmails.*.email' => 'email',
                'ccEmails.*.name' => 'required_with:ccEmails.*.email',
                'ccEmails.*.type' => 'required_with:ccEmails.*.email',
                'pdf_base64' => 'required',
                'approver_id' => 'required',
                'tender_invitation_letter.file_attachment' => 'required|string',
                'tender_invitation_letter.file_name' => 'required|string',
                'tender_invitation_letter.file_type' => 'required|string',
            ]);

            // Generate PDF filename and store in S3
            $pdfFilename = 'Tender_' . $validated['tender_id'] . '_' . Str::random(10) . '.pdf';
            $s3Path = 'Uploads/tender_letters/' . $pdfFilename;
            Storage::disk('s3')->put($s3Path, base64_decode($validated['pdf_base64']));

            // Verify S3 upload
            if (!Storage::disk('s3')->exists($s3Path)) {
                throw new \Exception('Failed to upload PDF to S3.');
            }

            if (isset($validated['tender_invitation_letter']['file_attachment'])) {
                $fileData = $validated['tender_invitation_letter'];

                // Remove the base64 prefix if present
                $base64 = preg_replace('#^data:.*;base64,#', '', $fileData['file_attachment']);

                // Decode
                $decoded = base64_decode($base64);

                // Generate a safe filename
                $filename = 'Invitation_letter_' . $validated['tender_id'];

                // Get extension from original file
                $extension = pathinfo($fileData['file_name'], PATHINFO_EXTENSION);
                $fullName = $filename . '.' . $extension;

                // Store on S3
                $path = "Uploads/tenders/letters/{$fullName}";
                Storage::disk('s3')->put($path, $decoded);

                // Make public (optional)
                Storage::disk('s3')->setVisibility($path, 'public');

                // (Optional) Get file URL

            }



            // Save approval request
            $approval = TenderApproval::where('id', $request->approval_id)->first();

            if ($approval) {
                $approval->update([
                    'tender_id' => $validated['tender_id'],
                    'tender_no' => $validated['tender_no'],
                    'stage_id' => $validated['stage_id'],
                    'email_dated' => $validated['email_dated'],
                    'commence_year' => $validated['commence_year'],
                    'main_email' => $validated['mainEmail'],
                    'cc_emails' => $validated['ccEmails'] ?? [],
                    'pdf_filename' => $pdfFilename,
                    'approver_id' => $validated['approver_id'],
                    'submitter_id' => auth()->id(),
                    'file' => $path
                ]);
            } else {
                $approval = TenderApproval::create([
                    'tender_id' => $validated['tender_id'],
                    'tender_no' => $validated['tender_no'],
                    'stage_id' => $validated['stage_id'],
                    'email_dated' => $validated['email_dated'],
                    'commence_year' => $validated['commence_year'],
                    'main_email' => $validated['mainEmail'],
                    'cc_emails' => $validated['ccEmails'] ?? [],
                    'pdf_filename' => $pdfFilename,
                    'approver_id' => $validated['approver_id'],
                    'submitter_id' => auth()->id(),
                    'file' => $path
                ]);
            }




            // Notify approver
            $approver = User::find($validated['approver_id']);
            // $approver->notify(new TenderApprovalNotification($approval));
            ApprovalJob::dispatch($approval);

            return response()->json([
                'status' => 200,
                'message' => 'Tender submitted for approval successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error submitting tender for approval: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Failed to submit tender for approval: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function rejectTender(Request $request)
    {

        try {
            $validated = $request->validate([
                'tender_no' => 'required',
                'remarks' => 'required',
                'status' => 'required'
            ]);

            DB::table('tender_approvals')->where('tender_no', $validated['tender_no'])->update([
                'remarks' => $validated['remarks'],
                'status' => $validated['status']

            ]);

            $approval = TenderApproval::where('tender_no', $validated['tender_no'])->first();

            ApprovalJob::dispatch($approval);
            return response()->json([
                'status' => 200,
                'message' => 'updated successfull'
            ]);
        } catch (\Exception $e) {
            Log::error('Error submitting tender rejection ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Failed to submit tender rejection: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function previewTenderLetter(Request $request)
    {
        try {

            $tender_no = $request->tender_no;
            $prospect_id = $request->prospect_id;
            $commence_year = $request->commence_year;
            $email_dated = $request->email_dated;

            $customer_id = PipelineOpportunity::where('opportunity_id', $prospect_id)->value('customer_id');
            $customer = Customer::where('customer_id', $customer_id)->first();
            $contact_person = CustomerContact::where('customer_id', $customer_id)->value('contact_name');
            $doc_no = TenderToc::where('tender_no', $tender_no)->pluck('toc_no');
            $doc_names = TenderDocParam::whereIn('doc_id', $doc_no)->pluck('doc_name')->toArray();
            $company = Company::first();

            $view_name = 'printouts.reply_cedant_document_requested';
            $data = [
                'tender_no' => $request->tender_no,
                'company' => $company,
                'customer' => $customer,
                'contact_person' => $contact_person,
                'doc_names' => $doc_names,
                'email_dated' => $email_dated,
                'commence_year' => $commence_year,
                'stage' => 2

            ];

            $pdf = Pdf::loadView($view_name, $data)->setPaper('a4', 'portrait')->setWarnings(false);

            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="preview_letter.pdf"');
        } catch (\Exception $e) {
            return response("Error: " . $e->getMessage(), 500);
        }
    }
}
