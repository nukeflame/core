<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class UploadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('settings.uploads.upload_docs', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), []);
            if ($validator) {
                // Validate the uploaded file
                $request->validate([
                    'file' => 'required|max:2048', // Add more mime types if needed
                ]);

                // Store the file
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                if ($file->storeAs('assets/documents/', $fileName)) { // 'assets/documents/' is the directory name
                    return redirect('/uploads/upload-docs')->with('success', 'Document uploaded successfully');
                }
            } else {
                Session::flash('error', 'some field are missing');
                return [
                    'code' => -1,
                    'msg' => $validator->errors(),
                ];
            }
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
