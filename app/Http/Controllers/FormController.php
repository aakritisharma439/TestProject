<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Form;

class FormController extends Controller
{

    //to display form on page
    public function form()
    {
        $data = Form::orderBy('id', 'desc')->get();
        return view('form', compact('data'));
    }

    //to store dynamic form data
    public function submitForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
           'name.*' => 'required|string|min:2|max:20',
           'email.*' => 'required|email',
           'mobile.*' => 'required|digits:10',
           'gender.*' => 'required',
       ]);

        if ($validator->fails()) {
            return response()->json([ 'status' => 'error',  'errors' => $validator->errors()], 422);
        }
        
        foreach ($request->name as $index => $name) {
           Form::create([
               'name' => $name,
               'email' => $request->email[$index],
               'mobile' => $request->mobile[$index],
               'gender' => $request->gender[$index]
           ]);
       }
         // fetch updated data after insertion
         $data = Form::orderBy('id', 'desc')->get();
        return response()->json([
            'status' => 'success',
            'message' => 'Form submitted successfully',
        ]);
    }
 
    // to fetch the latest data dynamically
    public function fetchData() {
        $data = Form::orderBy('id', 'desc')->get();
        return response()->json(['data' => $data]);
    }
}
