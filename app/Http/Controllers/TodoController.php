<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
    public function load()
    {
        $todos = Todo::where('user_id', Auth::id())->get();
        return response()->json(['todos' => $todos]);
    }

    public function save(Request $request)
    {
        // Todo::where('user_id', Auth::id())->delete();

        // $todos = $request->input('todos', []);

        logger($request->all());

        // foreach ($todos as $todoData) {
        //     Todo::create([
        //         'text' => $todoData['text'],
        //         'completed' => $todoData['completed'],
        //         'user_id' => Auth::id(),
        //     ]);
        // }

        return response()->json(['success' => true, 'message' => 'Todos saved successfully'], 200);
    }

    public function add(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:255',
        ]);

        logger($request->all());


        $todo = Todo::create([
            'text' => $request->text,
            'completed' => false,
            'user_id' => Auth::id(),
        ]);

        return response()->json(['success' => true, 'todo' => $todo]);
    }

    public function update(Request $request, Todo $todo)
    {
        if ($todo->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        logger($request->all());

        $todo->update([
            'completed' => $request->completed,
        ]);

        return response()->json(['success' => true, 'todo' => $todo]);
    }

    public function destroy(Todo $todo)
    {
        // Check ownership
        if ($todo->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $todo->delete();
        return response()->json(['success' => true]);
    }
}
