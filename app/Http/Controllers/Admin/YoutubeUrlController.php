<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\YoutubeUrl;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class YoutubeUrlController extends Controller
{
    /**
     * Shows the form to input the YouTube URLs for the walkthrough screens.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = YoutubeUrl::where('type', 'walkthrough')->get();

        $staticValues = [
            'screen1' => $data[0]->value,
            'screen2' => $data[1]->value,
            'screen3' => $data[2]->value,
            'screen4' => $data[3]->value,
            'screen5' => $data[4]->value,

            // Add more static values as needed
        ];
        return view('admin.youtubeurls.form', compact('staticValues'));
    }

    /**
     * Stores the YouTube URLs for the walkthrough screens.
     *
     * This function accepts a request containing YouTube URLs for different screens,
     * validates the URLs to ensure they are in the correct format, and updates the
     * database with the new URLs. If validation fails, it redirects back with error
     * messages. If the update is successful, it redirects to a success page; otherwise,
     * it returns an error message.
     *
     * @param Request $request The request object containing the YouTube URLs.
     * @return \Illuminate\Http\RedirectResponse
     */

    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [

            'screen1' => 'required|url|regex:/^(https?:\/\/)?(www\.)?youtube\.com\/watch\?v=[\w-]+/i',
            'screen2' => 'required|url|regex:/^(https?:\/\/)?(www\.)?youtube\.com\/watch\?v=[\w-]+/i',
            'screen3' => 'required|url|regex:/^(https?:\/\/)?(www\.)?youtube\.com\/watch\?v=[\w-]+/i',
            'screen4' => 'required|url|regex:/^(https?:\/\/)?(www\.)?youtube\.com\/watch\?v=[\w-]+/i',
            'screen5' => 'required|url|regex:/^(https?:\/\/)?(www\.)?youtube\.com\/watch\?v=[\w-]+/i',



            // Add more validation rules as needed
        ], [
            'screen1.required' => 'The YouTube URL field is required.',
            'screen1.url' => 'The YouTube URL must be a valid URL.',
            'screen1.regex' => 'The YouTube URL must be a valid YouTube URL.',
            'screen2.required' => 'The YouTube URL field is required.',
            'screen2.url' => 'The YouTube URL must be a valid URL.',
            'screen2.regex' => 'The YouTube URL must be a valid YouTube URL.',
            'screen3.required' => 'The YouTube URL field is required.',
            'screen3.url' => 'The YouTube URL must be a valid URL.',
            'screen3.regex' => 'The YouTube URL must be a valid YouTube URL.',
            'screen4.required' => 'The YouTube URL field is required.',
            'screen4.url' => 'The YouTube URL must be a valid URL.',
            'screen4.regex' => 'The YouTube URL must be a valid YouTube URL.',
            'screen5.required' => 'The YouTube URL field is required.',
            'screen5.url' => 'The YouTube URL must be a valid URL.',
            'screen5.regex' => 'The YouTube URL must be a valid YouTube URL.',

        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $validator = $request->all();
        $staticKeys = ['screen1', 'screen2', 'screen3', 'screen4', 'screen5'];
        foreach ($data as $key => $value) {
            if ($key !== '_token') { // Skip the CSRF token
                echo $newValue = $request->input($key);

                $result = YoutubeUrl::where('key', $key)->update(['value' => $newValue]);
            }
        }

        if ($result) {
            // Update was successful, redirect to a success page or another route
            return redirect()->route('youtubeurls')
                ->with('success', 'Urls updated successfully');
        } else {
            // Update failed, display an error message
            return redirect()->route('youtubeurls')
                ->with('error', 'Failed to update data');
        }
    }
}
