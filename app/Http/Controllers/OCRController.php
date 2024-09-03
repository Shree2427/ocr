<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use App\Models\OcrResult; // For Eloquent ORM
use Illuminate\Support\Facades\DB; // For Query Builder
use Carbon\Carbon;

class OCRController extends Controller
{
    public function processImage(Request $request)
    {
        // Validate the request to ensure an image is provided
        $request->validate([
            'image' => 'required|image|max:10240', // 10MB max
        ]);

        // Store the uploaded image temporarily
        $imagePath = $request->file('image')->store('ocr_images', 'public');
        $fullImagePath = Storage::disk('public')->path($imagePath);

        // Prepare the Python script path and command
        $pythonScriptPath = base_path('python_scripts/ocr_api.py');

        // Use the full path to Python executable
        $pythonExecutable = 'C:\Users\SERVER\AppData\Local\Programs\Python\Python312\python.exe';

        // Execute the Python script using Symfony Process component
        $process = new Process([$pythonExecutable, $pythonScriptPath, $fullImagePath]);
        $process->run();

        // Handle errors in case the process fails
        if (!$process->isSuccessful()) {
            $errorOutput = $process->getErrorOutput();
            \Log::error("Python script failed with error: " . $errorOutput);
            return response()->json(['error' => 'Python script failed: ' . $errorOutput], 500);
        }
    

        // Get the output from the Python script
        $output = $process->getOutput();

        // Decode the output if it returns JSON or other structured data
        $result = json_decode($output, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['error' => 'Failed to decode JSON output from Python script'], 500);
        }

        // Optionally, delete the temporary image
        Storage::disk('public')->delete($imagePath);

        // Store the result
        $this->storeResult(new Request([
            'file_name' => basename($fullImagePath),
            'extracted_text' => $result['plain_text'] ?? 'No text extracted',
        ]));

        // Return the result as JSON
        return response()->json($result);
    }

    public function storeResult(Request $request)
    {
        // Validate and handle request data
        $request->validate([
            'file_name' => 'required|string',
            'extracted_text' => 'required|string',
        ]);

        // Prepare data
        $data = [
            'file_name' => $request->input('file_name'),
            'extracted_text' => $request->input('extracted_text'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];

        // Using Eloquent ORM
        // OcrResult::create($data);

        

        // OR Using Query Builder
        DB::table('ocr_results')->insert($data);

        return response()->json(['message' => 'Data inserted successfully']);
    }

    public function checkSession()
{
    if (session()->has('_token')) {
        return response()->json(['message' => 'Session is active']);
    } else {
        return response()->json(['message' => 'Session has expired'], 419);
    }
}
}
