<script src="https://cdn.tailwindcss.com"></script>
<div class="container mx-auto px-4 py-8">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Thông tin bài thi -->
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-3xl font-bold mb-2">{{ $exam->exam_name }}</h1>
                <span class="inline-block px-3 py-1 text-sm rounded {{ $exam->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ $exam->is_active ? 'Đang kích hoạt' : 'Không kích hoạt' }}
                </span>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.exams.edit', $exam) }}" 
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Chỉnh sửa
                </a>
                <form action="{{ route('admin.exams.destroy', $exam) }}" method="POST" 
                    onsubmit="return confirm('Bạn có chắc muốn xóa bài thi này?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Xóa
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <p class="text-gray-600 text-sm">Mô tả</p>
                <p class="font-semibold">{{ $exam->description ?: 'Không có mô tả' }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Thời gian thi</p>
                <p class="font-semibold">{{ $exam->duration_minutes }} phút</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Thời gian bắt đầu</p>
                <p class="font-semibold">{{ $exam->start_time ? $exam->start_time->format('d/m/Y H:i') : 'Không giới hạn' }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Thời gian kết thúc</p>
                <p class="font-semibold">{{ $exam->end_time ? $exam->end_time->format('d/m/Y H:i') : 'Không giới hạn' }}</p>
            </div>
        </div>

        <div class="border-t pt-4">
            <p class="text-gray-600 text-sm">Tổng số câu hỏi</p>
            <p class="text-2xl font-bold text-blue-600">{{ $exam->questions->count() }}</p>
        </div>
    </div>

    <!-- Danh sách câu hỏi -->
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-6">
        <h2 class="text-2xl font-bold mb-4">Câu Hỏi</h2>

        @foreach($exam->questions as $index => $question)
            <div class="border border-gray-300 rounded p-4 mb-4">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex-1">
                        <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-2">
                            {{ $question->type->name }}
                        </span>
                        <h3 class="text-lg font-bold inline">Câu {{ $index + 1 }}</h3>
                    </div>
                    <span class="text-sm text-gray-600">Thứ tự: {{ $question->pivot->order }}</span>
                </div>

                <p class="mb-3 text-gray-800">{{ $question->question_text }}</p>

                @if($question->image)
                    <img src="{{ Storage::url($question->image) }}" 
                        alt="Question image" class="mb-3 max-w-md rounded">
                @endif

                <div class="mb-3">
                    <p class="font-semibold text-sm mb-2">Đáp án:</p>
                    @foreach($question->answerOptions as $answer)
                        <div class="flex items-center mb-1 {{ $answer->is_correct ? 'bg-green-50 border-l-4 border-green-500 pl-3' : 'pl-3' }} py-1">
                            <span class="mr-2">{{ chr(65 + $loop->index) }}.</span>
                            <span>{{ $answer->answer_text }}</span>
                            @if($answer->is_correct)
                                <span class="ml-2 text-green-600 font-bold">✓</span>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if($question->explanation)
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 mt-3">
                        <p class="text-sm font-semibold mb-1">Giải thích:</p>
                        <p class="text-sm">{{ $question->explanation }}</p>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Danh sách học sinh -->
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-6">
        @php
            $studentCount = $exam->sessions->pluck('student')->filter()->unique('id')->count();
        @endphp
        <h2 class="text-2xl font-bold mb-4">Học Sinh Được Gán ({{ $studentCount }})</h2>

        @if($exam->sessions->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left">Tên học sinh</th>
                            <th class="px-4 py-2 text-left">Email</th>
                            <th class="px-4 py-2 text-left">Thời gian</th>
                            <th class="px-4 py-2 text-left">Số lần thi</th>
                            <th class="px-4 py-2 text-left">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($exam->sessions as $session)
                            <tr class="border-b hover:bg-gray-50">
                                @if ($session->student)
                                     <td class="px-4 py-2">{{ $session->student->name }}</td>
                                     <td class="px-4 py-2">{{ $session->student->email }}</td>
                                @else
                                    <td class="px-4 py-2 w-100 text-center" colspan="2">Chưa có thí sinh</td>
                                @endif
                                <td class="px-4 py-2 text-sm">
                                    {{ $exam->start_time->format('d/m/Y H:i') }}<br>
                                    đến {{ $exam->end_time->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-2">{{ $session->max_attempts }}</td>
                                <td class="px-4 py-2">
                                    @if($session->status == 'not_started')
                                        <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">
                                            Chưa làm
                                        </span>
                                    @else
                                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">
                                            Đã hoàn thành
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-600">Chưa có học sinh nào được gán vào bài thi này.</p>
        @endif
    </div>

    <div class="flex justify-between">
        <a href="{{ route('admin.exams.index') }}" 
            class="text-blue-600 hover:text-blue-800">
            ← Quay lại danh sách
        </a>
    </div>
</div>