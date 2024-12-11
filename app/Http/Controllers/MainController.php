<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Main;


class MainController extends Controller
{
    //1. Accept a key(string) and value(some JSON blob/string) and store them.
    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string',
            'value' => 'required',
        ]);

        $main = Main::create([
            'key' => $validated['key'],
            'value' => $validated['value'],
        ]);

        return response()->json(['message' => 'Key-value pair saved successfully.', 'data' => $main], 201);
    }

    //2. Accept a key and return the corresponding latest value
    public function getLatest(Request $request, $mykey)
    {
        $main = Main::where('key', $mykey)->latest()->first();

        if (!$main) {
            return response()->json(['message' => 'Key not found.'], 404);
        }

        return response()->json($main);
    }

    //3. When given a key AND a timestamp, return whatever the value of the key at the time was.
    public function getValueAt(Request $request, $mykey, $timestamp)
    {
        if (!is_numeric($timestamp)) {
            return response()->json(['message' => 'Invalid timestamp.'], 400);
        }
        
        $main = Main::where('key', $mykey)
                    ->where('created_at', '<=', date('Y-m-d H:i:s', $timestamp))
                    ->latest()
                    ->first();

        if (!$main) {
            return response()->json(['message' => 'No value found for the specified key and timestamp.'], 404);
        }

        return response()->json($main);
    }

    //4. Displays all values currently stored in the database
    public function getAll()
    {
        $main = Main::all();

        return response()->json($main);
    }
}
