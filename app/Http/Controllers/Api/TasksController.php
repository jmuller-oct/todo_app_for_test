<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Task;
use Illuminate\Http\Request;

use App\Http\Resources\Task as TaskResource;
use App\Http\Requests\Task as TaskRequest;
use Illuminate\Support\Facades\Auth;


class TasksController extends Controller
{
    protected $task;

    public function __construct(Task $task) {
        $this->task = $task;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        if ($request->has('user_id')) {
            return $this->task->where('user_id', '=', $request->user_id)->orderBy('priority', 'asc')->get();
        }
        return $this->task->orderBy('priority', 'asc')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaskRequest $request) {
        if (!Auth::check()) {
            return response('Unathorized', 401);
        }

        $task = $this->task->create($request->all());

        return response()->json(new TaskResource($task), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Task $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task) {
        return new TaskResource($task);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Task $task
     * @return \Illuminate\Http\Response
     */
    public function update(TaskRequest $request, Task $task) {
        if (!Auth::check()) {
            return response('Unathorized', 401);
        }

        $task->update($request->all());

        return response()->json(new TaskResource($task));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Task $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task) {
        $task->delete();

        return response()->json(null, 204);
    }
}
