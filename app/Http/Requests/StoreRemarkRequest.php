<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreRemarkRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if user has access to the report
        $report = \App\Models\Report::find($this->report_id);
        
        if (!$report) {
            return false;
        }

        // Department users can only add remarks to their assigned reports
        if (Auth::guard('department')->check()) {
            $department = Auth::guard('department')->user();
            return $report->handling_department_id === $department->id;
        }

        // UCUA officers and admins can add remarks to any report
        if (Auth::guard('ucua')->check() || 
            (Auth::check() && Auth::user()->hasRole(['admin', 'ucua_officer']))) {
            return true;
        }

        // Regular users can only add remarks to their own reports
        if (Auth::check()) {
            return $report->user_id === Auth::id();
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'report_id' => 'required|exists:reports,id',
            'content' => 'required|string|min:10|max:1000',
            'remarks' => 'required|string|min:10|max:1000', // Alternative field name for departments
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'content.required' => 'Remark content is required.',
            'content.min' => 'Remark must be at least 10 characters long.',
            'content.max' => 'Remark cannot exceed 1000 characters.',
            'remarks.required' => 'Remark content is required.',
            'remarks.min' => 'Remark must be at least 10 characters long.',
            'remarks.max' => 'Remark cannot exceed 1000 characters.',
            'report_id.required' => 'Report ID is required.',
            'report_id.exists' => 'The selected report does not exist.',
        ];
    }

    /**
     * Get the remark content from either 'content' or 'remarks' field
     */
    public function getRemarkContent(): string
    {
        return $this->input('content') ?? $this->input('remarks');
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Normalize the content field
        if ($this->has('remarks') && !$this->has('content')) {
            $this->merge(['content' => $this->input('remarks')]);
        }
    }
}
