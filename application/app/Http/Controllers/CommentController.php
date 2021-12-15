<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Project $project, Task $task)
    {
        $request->validate([
            'text' => 'string|max:1000',
        ]);

        if (Comment::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'text' => $request->text,
        ])) {
            $flash = ['success' => __('Comment created successfully.')];
        } else {
            $flash = ['error' => __('Failed to create the comment.')];
        }

        return redirect()
            ->route('tasks.edit', ['project' => $project, 'task' => $task])
            ->with($flash);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project, Task $task, Comment $comment)
    {
        if (Auth::id() !== $comment->user_id) {
            /* 自分のコメントではない */
            $flash = ['error' => __("You cannot delete the other user's comment.")];
        } else if ($comment->delete()) {
            $flash = ['success' => __('Comment deleted successfully.')];
        } else {
            $flash = ['error' => __('Failed to delete the comment.')];
        }

        return redirect()
            ->route('tasks.edit', ['project' => $project, 'task' => $task])
            ->with($flash);
    }
}
