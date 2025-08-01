@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Create New Schedule</h4>
                    </div>
                    <div class="card-body">
                        <!-- Create form -->
                        {{ html()->form('POST', route('bd.schedule.data.store'))->id('form_add_schedule')->open() }}
                        
                        <div class="form-group">
                            <label for="class">Class</label>
                            <input type="text" name="class" id="class" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="sub_limit">Sub Limit</label>
                            <input type="text" name="sub_limit" id="sub_limit" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="unqualified_assistant_employees">Unqualified Assistant Employees</label>
                            <input type="number" name="unqualified_assistant_employees" id="unqualified_assistant_employees" class="form-control" required>
                        </div>

                        <!-- Add more fields here as needed -->

                        <button type="submit" class="btn btn-primary mt-3">Create Schedule</button>
                        
                        {{ html()->form()->close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
