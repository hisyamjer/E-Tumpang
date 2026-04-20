<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Student;
use App\Models\User;


class AdminController extends Controller
{
    public function index(){
        return Inertia::render('Admin/index');
    }

    public function users()
    {
        $students = Student::query()
            ->with(['user'])
            ->get()
            ->map(function (Student $student) {
                return [
                    'studentID' => $student->studentID,
                    'name' => $student->name,
                    'email' => $student->email,
                    'phone_number' => $student->phone_number,
                    'is_blocked' => (bool) $student->is_blocked,
                    'role' => $student->user?->role,
                    'plate_number' => $student->user?->plate_number,
                    'model' => $student->user?->model,
                ];
            })
            ->values();

        return Inertia::render('Admin/user', [
            'students' => $students,
        ]);
    }

    public function createUser()
    {
        return Inertia::render('Admin/create');
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'studentID' => ['required', 'string', 'max:255', 'unique:student,studentID'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:student,email'],
            // Stored as plain text in this project.
            'password' => ['required', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:255'],
            'role' => ['nullable', 'in:driver,passenger'],
            'plate_number' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $student = Student::create([
            'studentID' => $validated['studentID'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'phone_number' => $validated['phone_number'] ?? null,
        ]);

        $hasAnyUserRowData = ($validated['role'] ?? null)
            || ($validated['plate_number'] ?? null)
            || ($validated['model'] ?? null)
            || array_key_exists('is_default', $validated);

        if ($hasAnyUserRowData) {
            User::create([
                'studentID' => $student->studentID,
                'role' => $validated['role'] ?? null,
                'plate_number' => $validated['plate_number'] ?? null,
                'model' => $validated['model'] ?? null,
                'is_default' => (bool) ($validated['is_default'] ?? false),
            ]);
        }

        return redirect()
            ->route('admin.users')
            ->with('message', 'Student created successfully.');
    }

    public function blockStudent(Student $student)
    {
        $student->update(['is_blocked' => true]);

        return redirect()
            ->route('admin.users')
            ->with('message', 'Student blocked.');
    }

    public function unblockStudent(Student $student)
    {
        $student->update(['is_blocked' => false]);

        return redirect()
            ->route('admin.users')
            ->with('message', 'Student unblocked.');
    }
}
