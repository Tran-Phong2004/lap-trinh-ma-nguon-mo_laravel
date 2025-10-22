<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExamController extends Controller
{
       public function show(Request $request, $examSessionId, $hash)
    {
        $user = Auth::user();
        
        // Validate exam session
        $examSession = ExamSession::with('exam')->findOrFail($examSessionId);
        
        // Get exam participation
        $examParticipation = ExamParticipation::where('exam_session_id', $examSessionId)
            ->where('user_id', $user->id)
            ->where('hash', $hash)
            ->firstOrFail();

        // Check if exam is completed
        if ($examParticipation->status === 'completed') {
            return redirect()->route('home')->with('error', 'Bài thi đã hoàn thành');
        }

        // Get all parts with questions and previous answers
        $parts = Part::where('exam_id', $examSession->exam_id)
            ->with([
                'skill',
                'questions.answerOptions'
            ])
            ->orderBy('order')
            ->get()
            ->map(function ($part) use ($examParticipation) {
                return [
                    'partId' => $part->id,
                    'name' => $part->name,
                    'instructions' => $part->instructions,
                    'passage' => $part->passage,
                    'audioUrl' => $part->audio_url ? Storage::url($part->audio_url) : null,
                    'timeLimitSeconds' => $part->time_limit_seconds,
                    'skill' => [
                        'name' => $part->skill->name,
                        'totalTimeSeconds' => $part->skill->total_time_seconds,
                    ],
                    'questions' => $part->questions->map(function ($question) use ($examParticipation) {
                        // Get previous answer if exists
                        $previousAnswer = Answer::where('exam_participation_id', $examParticipation->id)
                            ->where('question_id', $question->id)
                            ->first();

                        return [
                            'questionId' => $question->id,
                            'questionText' => $question->question_text,
                            'type' => $question->type,
                            'answerOptions' => $question->answerOptions->map(function ($option) {
                                return [
                                    'answerOptionId' => $option->id,
                                    'answerText' => $option->answer_text,
                                ];
                            }),
                            // Include previous answer
                            'previousAnswer' => $previousAnswer ? [
                                'selectedOptionId' => $previousAnswer->selected_option_id,
                                'answerText' => $previousAnswer->answer_text,
                                'hasRecording' => $previousAnswer->has_recording,
                                'recordingUrl' => $previousAnswer->audioRecording 
                                    ? Storage::url($previousAnswer->audioRecording->file_path) 
                                    : null,
                            ] : null,
                        ];
                    }),
                ];
            });

        return view('exam.take', [
            'user' => $user,
            'examSession' => $examSession,
            'parts' => $parts,
            'examParticipationId' => $examParticipation->id,
            'examSessionId' => $examSessionId,
            'hash' => $hash,
        ]);
    }

    /**
     * Save exam progress (không nộp bài)
     */
    public function saveProgress(Request $request)
    {
        return $this->processSubmission($request, false);
    }

    /**
     * Submit exam (nộp bài cuối cùng)
     */
    public function submit(Request $request)
    {
        return $this->processSubmission($request, true);
    }

    /**
     * Process exam submission (shared logic)
     */
    private function processSubmission(Request $request, bool $isFinalSubmit)
    {
        $validated = $request->validate([
            'exam_participation_id' => 'required|integer',
            'exam_session_id' => 'required|integer',
            'answers' => 'required|json',
        ]);

        $user = Auth::user();
        $examParticipation = ExamParticipation::findOrFail($validated['exam_participation_id']);

        // Verify ownership
        if ($examParticipation->user_id !== $user->id) {
            return back()->with('error', 'Bạn không có quyền truy cập bài thi này');
        }

        $answers = json_decode($validated['answers'], true);

        try {
            DB::beginTransaction();

            // Save all answers
            foreach ($answers as $answerData) {
                $answer = Answer::updateOrCreate(
                    [
                        'exam_participation_id' => $examParticipation->id,
                        'question_id' => $answerData['questionId'],
                    ],
                    [
                        'part_id' => $answerData['partId'],
                        'skill_name' => $answerData['skillName'],
                        'answer_type' => $answerData['type'],
                        'selected_option_id' => $answerData['selectedOptionId'] ?? null,
                        'answer_text' => $answerData['answerText'] ?? null,
                        'has_recording' => $answerData['hasRecording'] ?? false,
                        'recording_duration' => $answerData['duration'] ?? 0,
                    ]
                );

                // Handle audio recording upload
                if (isset($answerData['hasRecording']) && $answerData['hasRecording']) {
                    $recordingKey = 'recording_' . $answerData['questionId'];
                    
                    if ($request->hasFile($recordingKey)) {
                        $file = $request->file($recordingKey);
                        
                        // Delete old recording if exists
                        $oldRecording = AudioRecording::where('answer_id', $answer->id)->first();
                        if ($oldRecording && Storage::disk('public')->exists($oldRecording->file_path)) {
                            Storage::disk('public')->delete($oldRecording->file_path);
                        }

                        // Store new recording
                        $path = $file->store('recordings/' . $examParticipation->id, 'public');

                        AudioRecording::updateOrCreate(
                            ['answer_id' => $answer->id],
                            [
                                'file_path' => $path,
                                'duration' => $answerData['duration'] ?? 0,
                                'mime_type' => $file->getMimeType(),
                                'file_size' => $file->getSize(),
                            ]
                        );
                    }
                }
            }

            // Update exam participation status
            if ($isFinalSubmit) {
                $examParticipation->status = 'completed';
                $examParticipation->completed_at = now();
                $examParticipation->save();

                DB::commit();

                return redirect()->route('home')
                    ->with('success', 'Nộp bài thành công! Cảm ơn bạn đã hoàn thành bài thi.');
            } else {
                $examParticipation->status = 'in_progress';
                $examParticipation->last_saved_at = now();
                $examParticipation->save();

                DB::commit();

                return back()->with('success', 'Lưu bài thành công! Bạn có thể tiếp tục làm bài.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Exam submission error: ' . $e->getMessage());
            
            return back()
                ->with('error', 'Có lỗi xảy ra khi lưu bài: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Logout from exam
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('info', 'Bạn đã đăng xuất khỏi hệ thống');
    }
}
