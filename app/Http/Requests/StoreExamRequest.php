<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'exam_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'is_active' => 'boolean',

            // Questions
            'questions' => 'required|array|min:1',
            'questions.*.question_type_id' => 'required|exists:question_types,id',
            'questions.*.question_text' => 'required|string',
            'questions.*.explanation' => 'nullable|string',
            'questions.*.image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'questions.*.is_active' => 'boolean',

            // Answer options
            'questions.*.answers' => 'required|array|min:1',
            'questions.*.answers.*.answer_text' => 'required|string',
            'questions.*.answers.*.is_correct' => 'nullable|boolean',
            'questions.*.correct_answer' => 'nullable|integer',
            // Students for exam sessions
            'students' => 'nullable|array',
            'students.*' => 'exists:users,id',
            'max_attempts' => 'nullable|integer|min:1'
        ];
    }

    public function messages(): array
    {
        return [
            'exam_name.required' => 'Tên bài thi là bắt buộc',
            'duration_minutes.required' => 'Thời gian thi là bắt buộc',
            'questions.required' => 'Bài thi phải có ít nhất 1 câu hỏi',
            'start_time.required' => 'Thời gian bắt đầu bài thi là bắt buộc',
            'end_time.required' => 'Thời gian kết thúc bài thi là bắt buộc',
            'questions.*.question_text.required' => 'Nội dung câu hỏi là bắt buộc',
            'questions.*.answers.required' => 'Câu hỏi phải có ít nhất 2 đáp án',
            'questions.*.answers.*.answer_text.required' => 'Nội dung đáp án là bắt buộc',
            'questions.*.image.image' => 'Tệp tải lên phải là hình ảnh hợp lệ.',
            'questions.*.image.mimes' => 'Ảnh phải có định dạng jpeg, png hoặc jpg.',
            'questions.*.image.max' => 'Ảnh không được vượt quá 2MB.',
        ];
    }
}