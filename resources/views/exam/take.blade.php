{{-- resources/views/exam/take.blade.php --}}
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bài Thi - {{ $examSession->exam->title ?? 'Exam' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: .5;
            }
        }

        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        .prose img {
            max-width: 100%;
            height: auto;
        }

        .prose p {
            margin: 0.5rem 0;
        }
    </style>
</head>

<body class="bg-gray-100">
    @include('partials.flash-messages')
    {{-- Hidden Audio Element --}}
    <audio id="partAudio" preload="metadata" crossorigin="anonymous"></audio>

    {{-- Header --}}
    <div class="bg-blue-800 text-white px-6 py-3 flex justify-between items-center">
        <div class="flex items-center space-x-3">
            <span class="font-medium">{{ $user->full_name ?? 'Thí sinh' }}</span>
        </div>

        <div class="flex items-center space-x-6">
            <div id="timerDisplay" class="px-3 py-1 rounded text-sm font-bold bg-red-600">
                00:00
            </div>
            <div class="text-sm">
                Đã chọn: <span class="text-red-400" id="answeredCount">0</span>/<span id="totalCount">0</span> câu
            </div>
            <button id="btnSubmit" onclick="showSubmitDialog()"
                class="bg-red-600 px-4 py-1 rounded text-sm font-bold hover:bg-red-700">
                NỘP BÀI
            </button>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="max-w-6xl mx-auto p-6 pb-32">
        <div id="partContainer" class="bg-white rounded-lg p-6 mb-6 shadow-sm">
            {{-- Content will be loaded by JavaScript --}}
        </div>
    </div>

    {{-- Bottom Navigation --}}
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t shadow-lg">
        <div class="flex">
            <div class="flex flex-1" id="partNavigation">
                {{-- Part buttons will be loaded here --}}
            </div>
            <div class="flex flex-shrink-0">
                <button id="btnContinue" onclick="handleContinue()"
                    class="bg-blue-600 text-white px-4 py-3 font-bold hover:bg-blue-700 text-sm">
                    TIẾP TỤC
                </button>
                <button onclick="handleSaveExam()"
                    class="bg-red-600 text-white px-4 py-3 font-bold hover:bg-red-700 text-sm">
                    LƯU BÀI
                </button>
            </div>
        </div>
    </div>

    {{-- Dialogs --}}
    {{-- Submit Dialog --}}
    <div id="submitDialog" class="hidden fixed inset-0 flex items-center justify-center z-50"
        style="background-color: rgba(0,0,0,0.3);">
        <div class="bg-white rounded-lg p-8 max-w-md mx-4 shadow-2xl border-2 border-red-300">
            <div class="text-center">
                <div class="mx-auto w-16 h-16 bg-red-500 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-file-arrow-down text-white text-3xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-red-600 mb-4">NỘP BÀI THI</h2>
                <div class="mb-6">
                    <p class="text-gray-700 mb-4 font-medium">BẠN ĐÃ HOÀN THÀNH BÀI THI VÀ MUỐN NỘP BÀI.</p>
                    <p class="text-gray-700 font-medium">HÃY CHẮC CHẮN VỀ ĐIỀU NÀY NẾU BẠN THỰC SỰ ĐÃ HOÀN THÀNH BÀI THI
                    </p>
                </div>
                <div class="flex justify-center space-x-4">
                    <button onclick="confirmSubmit()"
                        class="bg-red-500 text-white px-8 py-3 rounded-lg hover:bg-red-600 font-bold text-lg">
                        ĐỒNG Ý
                    </button>
                    <button onclick="hideSubmitDialog()"
                        class="bg-gray-300 text-gray-700 px-8 py-3 rounded-lg hover:bg-gray-400 font-bold text-lg border border-gray-400">
                        HỦY BỎ
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Save Success Dialog --}}
    <div id="saveSuccessDialog" class="hidden fixed inset-0 flex items-center justify-center z-50"
        style="background-color: rgba(0,0,0,0.3);">
        <div class="bg-white rounded-lg p-8 max-w-md mx-4 shadow-2xl border-2 border-green-300">
            <div class="text-center">
                <div class="mx-auto w-16 h-16 bg-green-500 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-check text-white text-3xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-green-600 mb-4">LƯU BÀI THÀNH CÔNG</h2>
                <div class="mb-6">
                    <p class="text-gray-700 mb-4 font-medium">Bài làm của bạn đã được lưu thành công!</p>
                    <p class="text-gray-700 font-medium">Bạn có thể tiếp tục làm bài hoặc nộp bài khi hoàn thành.</p>
                </div>
                <div class="flex justify-center">
                    <button onclick="hideSaveSuccessDialog()"
                        class="bg-green-500 text-white px-8 py-3 rounded-lg hover:bg-green-600 font-bold text-lg">
                        TIẾP TỤC
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Confirm Skill Change Dialog --}}
    <div id="confirmDialog" class="hidden fixed inset-0 flex items-center justify-center z-50"
        style="background-color: rgba(0,0,0,0.3);">
        <div class="bg-white rounded-lg p-6 max-w-lg mx-4 shadow-2xl border-2 border-gray-300">
            <div class="text-center">
                <h3 class="text-lg font-bold mb-4">THÔNG BÁO CHUYỂN SKILL</h3>
                <div class="bg-gray-100 p-4 rounded-lg mb-4">
                    <p class="text-sm text-gray-600 mb-2">
                        Kết quả skill hiện tại: <strong id="currentSkillName"></strong>
                    </p>
                    <div class="flex justify-center items-center space-x-2">
                        <span class="text-lg font-bold text-blue-600" id="currentSkillAnswered">0</span>
                        <span class="text-gray-500">/</span>
                        <span class="text-lg font-bold text-gray-700" id="currentSkillTotal">0</span>
                        <span class="text-sm text-gray-600">câu đã trả lời</span>
                    </div>
                </div>
                <p class="text-gray-700 mb-6">
                    Bạn sắp chuyển sang skill <strong id="nextSkillName"></strong>.
                    <br>Sau khi chuyển, skill này sẽ bị vô hiệu hóa.
                    <br><span class="text-red-600 font-semibold">Bạn có chắc chắn muốn tiếp tục?</span>
                </p>
                <div class="flex justify-center space-x-4">
                    <button onclick="confirmSkillChange()"
                        class="bg-red-500 text-white px-6 py-2 rounded hover:bg-red-600 font-bold">
                        CHUYỂN SKILL
                    </button>
                    <button onclick="cancelSkillChange()"
                        class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400 font-bold">
                        Ở LẠI
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Data from server
        const examData = @json($parts);
        const examParticipationId = {{ $examParticipationId }};
        const examSessionId = {{ $examSessionId }};
        const examHash = "{{ $hash }}";

        // State management
        let currentPartIndex = 0;
        let timeLeft = 0;
        let timerInterval = null;
        let selectedAnswers = {};
        let shortAnswers = {};
        let longAnswers = {};
        let recordings = {};
        let disabledSkills = [];
        let highestAccessedSkill = 'Listening';
        let pendingPartIndex = null;

        // Audio state
        let isPlaying = false;
        let audioCurrentTime = 0;
        let audioDuration = 0;
        let isDragging = false;
        let isRecording = false;
        let mediaRecorder = null;
        let audioChunks = [];

        // Initialize
        document.addEventListener('DOMContentLoaded', function () {
            if (examData.length > 0) {
                timeLeft = examData[0].skill.totalTimeSeconds;
                startTimer();
                renderPart();
                renderPartNavigation();
            }

            // Prevent page reload
            window.addEventListener('beforeunload', function (e) {
                e.preventDefault();
                e.returnValue = '';
                return '';
            });

            // Audio events
            const audio = document.getElementById('partAudio');
            audio.addEventListener('timeupdate', () => {
                audioCurrentTime = audio.currentTime;
                updateAudioProgress();
            });
            audio.addEventListener('loadedmetadata', () => {
                audioDuration = audio.duration || 0;
            });
            audio.addEventListener('ended', () => {
                isPlaying = false;
                updatePlayButton();
            });
        });

        // Timer functions
        function startTimer() {
            if (timerInterval) clearInterval(timerInterval);

            timerInterval = setInterval(() => {
                if (timeLeft > 0) {
                    timeLeft--;
                    updateTimerDisplay();

                    if (timeLeft === 0) {
                        handleTimeUp();
                    }
                }
            }, 1000);
        }

        function updateTimerDisplay() {
            const mins = Math.floor(timeLeft / 60);
            const secs = timeLeft % 60;
            const timeStr = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;

            const timerEl = document.getElementById('timerDisplay');
            timerEl.textContent = timeStr;

            if (timeLeft <= 60) {
                timerEl.classList.add('animate-pulse');
            } else {
                timerEl.classList.remove('animate-pulse');
            }
        }

        function handleTimeUp() {
            const currentSkill = examData[currentPartIndex].skill.name;
            const nextSkillIndex = findNextSkillIndex();

            if (nextSkillIndex !== -1) {
                if (!disabledSkills.includes(currentSkill)) {
                    disabledSkills.push(currentSkill);
                }
                switchToPart(nextSkillIndex);
            } else {
                alert('Hết giờ làm bài! Bài thi sẽ được tự động nộp.');
                submitExam(true);
            }
        }

        function findNextSkillIndex() {
            const currentSkill = examData[currentPartIndex].skill.name;
            const skillOrder = ['Listening', 'Reading', 'Writing', 'Speaking'];
            const currentIndex = skillOrder.indexOf(currentSkill);

            for (let i = currentIndex + 1; i < skillOrder.length; i++) {
                const nextSkill = skillOrder[i];
                const nextIndex = examData.findIndex(part => part.skill.name === nextSkill);

                if (nextIndex !== -1 && !disabledSkills.includes(nextSkill)) {
                    return nextIndex;
                }
            }
            return -1;
        }

        // Render functions
        function renderPart() {
            const part = examData[currentPartIndex];
            const container = document.getElementById('partContainer');

            let html = `
                <div class="text-center">
                    <strong>Directions:</strong> ${part.instructions}
                    <h2 class="text-xl font-bold mb-4 mt-4">${part.name}</h2>
                    <p class="text-gray-700 mb-6">Time limit: ${Math.floor(part.timeLimitSeconds / 60)} minutes</p>
                </div>
            `;

            // Audio player for Listening
            if (part.audioUrl && part.skill.name === 'Listening') {
                html += renderAudioPlayer();
            }

            // Questions
            if (part.passage) {
                html += `
                    <div class="grid grid-cols-2 gap-6 min-h-screen">
                        <div class="pr-4 border-r border-gray-300">
                            <div class="bg-gray-50 p-4 rounded-lg sticky top-4">
                                <div class="prose max-w-none text-sm leading-relaxed">${part.passage}</div>
                            </div>
                        </div>
                        <div class="pl-4">
                            ${part.questions.map(q => renderQuestion(q)).join('')}
                        </div>
                    </div>
                `;
            } else {
                html += `<div class="space-y-8">${part.questions.map(q => renderQuestion(q)).join('')}</div>`;
            }

            container.innerHTML = html;

            // Load audio if exists
            if (part.audioUrl) {
                const audio = document.getElementById('partAudio');
                audio.src = part.audioUrl;
                audio.load();
            }

            updateAnsweredCount();
            attachEventListeners();
        }

        function renderAudioPlayer() {
            return `
                <div class="bg-purple-900 rounded-lg p-4 mb-6">
                    <div class="flex items-center space-x-3">
                        <button onclick="handleRewind()" class="text-white hover:text-gray-300 bg-purple-700 p-2 rounded-full hover:bg-purple-800">
                            <i class="fas fa-backward"></i>
                        </button>
                        <button id="playBtn" onclick="toggleAudio()" class="text-white hover:text-gray-300 bg-purple-700 p-2 rounded-full hover:bg-purple-800">
                            <i class="fas fa-play"></i>
                        </button>
                        <button onclick="handleFastForward()" class="text-white hover:text-gray-300 bg-purple-700 p-2 rounded-full hover:bg-purple-800">
                            <i class="fas fa-forward"></i>
                        </button>
                        <span id="audioCurrentTime" class="text-white font-mono text-sm min-w-[40px]">00:00</span>
                        <div id="audioProgressBar" class="flex-1 bg-gray-700 h-3 rounded cursor-pointer hover:bg-gray-600 relative" onmousedown="handleProgressClick(event)">
                            <div id="audioProgress" class="bg-purple-400 h-3 rounded transition-all relative" style="width: 0%">
                                <div class="absolute right-0 top-1/2 transform -translate-y-1/2 w-4 h-4 bg-white rounded-full shadow-lg" style="right: -8px; cursor: grab"></div>
                            </div>
                        </div>
                        <span id="audioDuration" class="text-white font-mono text-sm min-w-[40px]">00:00</span>
                    </div>
                    <div class="mt-2 text-center">
                        <span class="text-purple-200 text-sm" id="audioStatus">Click play button to start audio</span>
                    </div>
                </div>
            `;
        }

        function renderQuestion(question) {
            switch (question.type) {
                case 'multiple-choice':
                    return `
                        <div class="border-b border-gray-200 pb-6">
                            <div class="prose mb-4">${question.questionText}</div>
                            <div class="space-y-3">
                                ${question.answerOptions.map(opt => `
                                    <label class="flex items-center space-x-3 cursor-pointer hover:bg-gray-50 p-2 rounded">
                                        <input type="radio" name="question-${question.questionId}" value="${opt.answerOptionId}" 
                                            ${selectedAnswers[question.questionId] == opt.answerOptionId ? 'checked' : ''}
                                            class="w-4 h-4 text-blue-600">
                                        <span class="text-gray-700">${opt.answerText}</span>
                                    </label>
                                `).join('')}
                            </div>
                        </div>
                    `;

                case 'short-answer':
                    return `
                        <div class="border-b border-gray-200 pb-6">
                            <div class="prose mb-4">${question.questionText}</div>
                            <input type="text" 
                                id="short-${question.questionId}" 
                                value="${shortAnswers[question.questionId] || ''}"
                                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Type your answer here...">
                        </div>
                    `;

                case 'long-answer':
                    return `
                        <div class="border-b border-gray-200 pb-6">
                            <div class="prose mb-4">${question.questionText}</div>
                            <textarea 
                                id="long-${question.questionId}" 
                                rows="10"
                                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Write your essay here...">${longAnswers[question.questionId] || ''}</textarea>
                        </div>
                    `;

                case 'audio-record':
                    const recording = recordings[question.questionId];
                    return `
                        <div class="border-b border-gray-200 pb-6">
                            <div class="prose mb-4">${question.questionText}</div>
                            <div class="bg-gray-100 p-6 rounded-lg">
                                <div class="text-center mb-4">
                                    <div class="w-16 h-16 ${isRecording ? 'bg-red-500 animate-pulse' : 'bg-gray-400'} rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-microphone text-white text-2xl"></i>
                                    </div>
                                    <p class="text-gray-600 mb-4">
                                        ${isRecording ? 'Recording in progress...' : 'Click to start recording'}
                                    </p>
                                </div>
                                
                                ${!recording && !isRecording ? `
                                    <div class="flex justify-center space-x-4 mb-4">
                                        <button onclick="startRecording(${question.questionId})" class="bg-red-500 text-white px-6 py-2 rounded-lg hover:bg-red-600 flex items-center space-x-2">
                                            <i class="fas fa-microphone"></i>
                                            <span>Start Recording</span>
                                        </button>
                                    </div>
                                ` : ''}
                                
                                ${isRecording ? `
                                    <div class="flex justify-center space-x-4 mb-4">
                                        <button onclick="stopRecording(${question.questionId})" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 flex items-center space-x-2">
                                            <i class="fas fa-stop-circle"></i>
                                            <span>Stop Recording</span>
                                        </button>
                                    </div>
                                ` : ''}
                                
                                ${recording ? `
                                    <div class="mt-4 p-4 bg-white rounded-lg">
                                        <p class="text-sm text-gray-600 mb-2">Your recording:</p>
                                        <audio controls class="w-full mb-3" src="${recording.url}"></audio>
                                        <div class="flex justify-center space-x-2">
                                            <button onclick="startRecording(${question.questionId})" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600 text-sm">
                                                Re-record
                                            </button>
                                        </div>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    `;

                default:
                    return '';
            }
        }

        function renderPartNavigation() {
            const nav = document.getElementById('partNavigation');
            const skills = ['Listening', 'Reading', 'Writing', 'Speaking'];
            const skillColors = {
                'Listening': 'bg-orange-500',
                'Reading': 'bg-gray-500',
                'Writing': 'bg-gray-500',
                'Speaking': 'bg-gray-500'
            };

            let html = '';

            skills.forEach(skillName => {
                const skillParts = examData.filter(part => part.skill.name === skillName);

                skillParts.forEach((part, skillIndex) => {
                    const globalIndex = examData.findIndex(p => p.partId === part.partId);
                    const isActive = globalIndex === currentPartIndex;
                    const canAccess = canAccessPart(globalIndex);
                    const isDisabled = disabledSkills.includes(skillName);

                    let btnClass = 'w-full px-2 py-3 text-center border-r border-gray-400 text-xs ';

                    if (isActive) {
                        btnClass += skillColors[skillName] + ' text-white';
                    } else if (canAccess) {
                        btnClass += isDisabled ? 'bg-red-200 text-red-800 hover:bg-red-300' : 'bg-gray-300 text-gray-700 hover:bg-gray-400';
                    } else {
                        btnClass += 'bg-gray-200 text-gray-400 cursor-not-allowed';
                    }

                    html += `
                        <div class="flex-1 min-w-0">
                            <button onclick="handlePartChange(${globalIndex})" ${!canAccess ? 'disabled' : ''} class="${btnClass}">
                                <div class="font-bold whitespace-nowrap">PART ${skillIndex + 1}</div>
                                <div class="whitespace-nowrap">${skillName} - ${Math.floor(part.timeLimitSeconds / 60)}</div>
                            </button>
                        </div>
                    `;
                });
            });

            nav.innerHTML = html;
        }

        // Event handlers
        function attachEventListeners() {
            // Multiple choice
            document.querySelectorAll('input[type="radio"]').forEach(radio => {
                radio.addEventListener('change', function () {
                    const questionId = parseInt(this.name.replace('question-', ''));
                    const optionId = parseInt(this.value);
                    selectedAnswers[questionId] = optionId;
                    updateAnsweredCount();
                });
            });

            // Short answers
            document.querySelectorAll('input[id^="short-"]').forEach(input => {
                input.addEventListener('input', function () {
                    const questionId = parseInt(this.id.replace('short-', ''));
                    shortAnswers[questionId] = this.value;
                    updateAnsweredCount();
                });
            });

            // Long answers
            document.querySelectorAll('textarea[id^="long-"]').forEach(textarea => {
                textarea.addEventListener('input', function () {
                    const questionId = parseInt(this.id.replace('long-', ''));
                    longAnswers[questionId] = this.value;
                    updateAnsweredCount();
                });
            });
        }

        function updateAnsweredCount() {
            const part = examData[currentPartIndex];
            let answered = 0;

            part.questions.forEach(q => {
                if (q.type === 'multiple-choice' && selectedAnswers[q.questionId]) {
                    answered++;
                } else if (q.type === 'short-answer' && shortAnswers[q.questionId]?.trim()) {
                    answered++;
                } else if (q.type === 'long-answer' && longAnswers[q.questionId]?.trim()) {
                    answered++;
                } else if (q.type === 'audio-record' && recordings[q.questionId]?.blob) {
                    answered++;
                }
            });

            document.getElementById('answeredCount').textContent = answered;
            document.getElementById('totalCount').textContent = part.questions.length;
        }

        // Audio controls
        function toggleAudio() {
            const audio = document.getElementById('partAudio');
            const btn = document.getElementById('playBtn');

            if (isPlaying) {
                audio.pause();
                isPlaying = false;
                btn.innerHTML = '<i class="fas fa-play"></i>';
            } else {
                audio.play();
                isPlaying = true;
                btn.innerHTML = '<i class="fas fa-pause"></i>';
            }
        }

        function handleRewind() {
            const audio = document.getElementById('partAudio');
            audio.currentTime = Math.max(0, audio.currentTime - 10);
        }

        function handleFastForward() {
            const audio = document.getElementById('partAudio');
            audio.currentTime = Math.min(audioDuration, audio.currentTime + 10);
        }

        function updateAudioProgress() {
            const progress = audioDuration > 0 ? (audioCurrentTime / audioDuration) * 100 : 0;
            document.getElementById('audioProgress').style.width = progress + '%';
            document.getElementById('audioCurrentTime').textContent = formatTime(Math.floor(audioCurrentTime));
            document.getElementById('audioDuration').textContent = formatTime(Math.floor(audioDuration));
        }

        function handleProgressClick(e) {
            const audio = document.getElementById('partAudio');
            const progressBar = e.currentTarget;
            const rect = progressBar.getBoundingClientRect();
            const clickX = e.clientX - rect.left;
            const percentage = clickX / rect.width;
            audio.currentTime = percentage * audioDuration;
        }

        function updatePlayButton() {
            const btn = document.getElementById('playBtn');
            if (btn) {
                btn.innerHTML = isPlaying ? '<i class="fas fa-pause"></i>' : '<i class="fas fa-play"></i>';
            }
        }

        // Recording functions
        async function startRecording(questionId) {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                mediaRecorder = new MediaRecorder(stream);
                audioChunks = [];

                mediaRecorder.ondataavailable = (event) => {
                    audioChunks.push(event.data);
                };

                mediaRecorder.onstop = () => {
                    const audioBlob = new Blob(audioChunks, { type: 'audio/wav' });
                    const audioUrl = URL.createObjectURL(audioBlob);

                    recordings[questionId] = {
                        blob: audioBlob,
                        url: audioUrl,
                        duration: 0
                    };

                    stream.getTracks().forEach(track => track.stop());
                    renderPart();
                };

                mediaRecorder.start();
                isRecording = true;
                renderPart();
            } catch (error) {
                console.error('Recording error:', error);
                alert('Could not access microphone. Please check permissions.');
            }
        }

        function stopRecording(questionId) {
            if (mediaRecorder && isRecording) {
                mediaRecorder.stop();
                isRecording = false;
            }
        }

        // Navigation functions
        function switchToPart(targetIndex) {
            const audio = document.getElementById('partAudio');
            audio.pause();
            isPlaying = false;

            const currentSkill = examData[currentPartIndex].skill.name;
            const targetSkill = examData[targetIndex].skill.name;

            if (currentSkill !== targetSkill) {
                timeLeft = examData[targetIndex].skill.totalTimeSeconds;

                const targetOrder = getSkillOrder(targetSkill);
                const currentHighest = getSkillOrder(highestAccessedSkill);

                if (targetOrder > currentHighest) {
                    highestAccessedSkill = targetSkill;
                }
            }

            currentPartIndex = targetIndex;
            renderPart();
            renderPartNavigation();
        }

        function canAccessPart(partIndex) {
            const targetPart = examData[partIndex];
            const currentSkill = examData[currentPartIndex].skill.name;
            const targetSkill = targetPart.skill.name;

            if (currentSkill === targetSkill) return true;
            if (disabledSkills.includes(targetSkill)) return false;

            const currentOrder = getSkillOrder(currentSkill);
            const targetOrder = getSkillOrder(targetSkill);

            if (targetOrder === currentOrder + 1) return true;

            const highestOrder = getSkillOrder(highestAccessedSkill);
            return targetOrder <= highestOrder;
        }

        function getSkillOrder(skillName) {
            const order = ['Listening', 'Reading', 'Writing', 'Speaking'];
            return order.indexOf(skillName);
        }

        function handlePartChange(targetIndex) {
            if (targetIndex === currentPartIndex) return;

            const currentSkill = examData[currentPartIndex].skill.name;
            const targetSkill = examData[targetIndex].skill.name;

            if (currentSkill !== targetSkill) {
                pendingPartIndex = targetIndex;
                showConfirmDialog(targetSkill);
            } else {
                switchToPart(targetIndex);
            }
        }

        function handleContinue() {
            if (currentPartIndex >= examData.length - 1) return;

            const nextIndex = currentPartIndex + 1;
            const currentSkill = examData[currentPartIndex].skill.name;
            const nextSkill = examData[nextIndex].skill.name;

            if (currentSkill !== nextSkill) {
                if (!disabledSkills.includes(currentSkill)) {
                    disabledSkills.push(currentSkill);
                }

                const nextOrder = getSkillOrder(nextSkill);
                const currentHighest = getSkillOrder(highestAccessedSkill);

                if (nextOrder > currentHighest) {
                    highestAccessedSkill = nextSkill;
                }

                pendingPartIndex = nextIndex;
                showConfirmDialog(nextSkill);
            } else {
                switchToPart(nextIndex);
            }
        }

        // Dialog functions
        function showSubmitDialog() {
            document.getElementById('submitDialog').classList.remove('hidden');
        }

        function hideSubmitDialog() {
            document.getElementById('submitDialog').classList.add('hidden');
        }

        function showConfirmDialog(targetSkill) {
            const currentSkill = examData[currentPartIndex].skill.name;
            const currentAnswered = getCurrentSkillAnswered(currentSkill);
            const currentTotal = getCurrentSkillTotal(currentSkill);

            document.getElementById('currentSkillName').textContent = currentSkill;
            document.getElementById('currentSkillAnswered').textContent = currentAnswered;
            document.getElementById('currentSkillTotal').textContent = currentTotal;
            document.getElementById('nextSkillName').textContent = targetSkill;
            document.getElementById('confirmDialog').classList.remove('hidden');
        }

        function hideConfirmDialog() {
            document.getElementById('confirmDialog').classList.add('hidden');
        }

        function hideSaveSuccessDialog() {
            document.getElementById('saveSuccessDialog').classList.remove('hidden');
        }

        function confirmSkillChange() {
            const currentSkill = examData[currentPartIndex].skill.name;

            if (!disabledSkills.includes(currentSkill)) {
                disabledSkills.push(currentSkill);
            }

            if (pendingPartIndex !== null) {
                switchToPart(pendingPartIndex);
                pendingPartIndex = null;
            }

            hideConfirmDialog();
        }

        function cancelSkillChange() {
            pendingPartIndex = null;
            hideConfirmDialog();
        }

        function getCurrentSkillAnswered(skillName) {
            const skillParts = examData.filter(part => part.skill.name === skillName);
            let total = 0;

            skillParts.forEach(part => {
                part.questions.forEach(q => {
                    if (q.type === 'multiple-choice' && selectedAnswers[q.questionId]) {
                        total++;
                    } else if (q.type === 'short-answer' && shortAnswers[q.questionId]?.trim()) {
                        total++;
                    } else if (q.type === 'long-answer' && longAnswers[q.questionId]?.trim()) {
                        total++;
                    } else if (q.type === 'audio-record' && recordings[q.questionId]?.blob) {
                        total++;
                    }
                });
            });

            return total;
        }

        function getCurrentSkillTotal(skillName) {
            const skillParts = examData.filter(part => part.skill.name === skillName);
            return skillParts.reduce((sum, part) => sum + part.questions.length, 0);
        }

        /**
 * Submit exam với form (MVC pattern)
 */
        async function submitExam(isLogout) {
            if (timerInterval) {
                clearInterval(timerInterval);
                timerInterval = null;
            }

            const formData = new FormData();
            formData.append('exam_participation_id', examParticipationId);
            formData.append('exam_session_id', examSessionId);

            // Collect all answers
            const allAnswers = [];
            examData.forEach(part => {
                part.questions.forEach(question => {
                    let answer = null;

                    switch (question.type) {
                        case 'multiple-choice':
                            answer = {
                                questionId: question.questionId,
                                type: 'multiple-choice',
                                selectedOptionId: selectedAnswers[question.questionId] || null,
                                partId: part.partId,
                                skillName: part.skill.name
                            };
                            break;

                        case 'short-answer':
                            answer = {
                                questionId: question.questionId,
                                type: 'short-answer',
                                answerText: shortAnswers[question.questionId]?.trim() || null,
                                partId: part.partId,
                                skillName: part.skill.name
                            };
                            break;

                        case 'long-answer':
                            answer = {
                                questionId: question.questionId,
                                type: 'long-answer',
                                answerText: longAnswers[question.questionId]?.trim() || null,
                                partId: part.partId,
                                skillName: part.skill.name
                            };
                            break;

                        case 'audio-record':
                            answer = {
                                questionId: question.questionId,
                                type: 'audio-record',
                                hasRecording: !!(recordings[question.questionId]?.blob),
                                duration: recordings[question.questionId]?.duration || 0,
                                partId: part.partId,
                                skillName: part.skill.name
                            };
                            break;
                    }

                    if (answer) {
                        allAnswers.push(answer);
                    }
                });
            });

            formData.append('answers', JSON.stringify(allAnswers));
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            // Append audio recordings
            Object.entries(recordings).forEach(([questionId, recording]) => {
                if (recording && recording.blob) {
                    formData.append(`recording_${questionId}`, recording.blob, `recording_${questionId}.wav`);
                }
            });

            try {
                // Tạo form ẩn để submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = isLogout ? '{{ route("exam.submit") }}' : '{{ route("exam.save") }}';

                // Append tất cả formData vào form
                for (let [key, value] of formData.entries()) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;

                    if (value instanceof Blob) {
                        // Skip blob - sẽ xử lý bằng fetch
                        continue;
                    }

                    input.value = value;
                    form.appendChild(input);
                }

                document.body.appendChild(form);

                // Nếu có recording, dùng fetch để upload
                if (Object.keys(recordings).length > 0) {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData
                    });

                    if (response.redirected) {
                        window.location.href = response.url;
                    } else {
                        const result = await response.json();
                        if (result.success) {
                            if (isLogout) {
                                window.location.href = '{{ route("home") }}';
                            } else {
                                document.getElementById('saveSuccessDialog').classList.remove('hidden');
                            }
                        }
                    }
                } else {
                    // Không có recording, submit form bình thường
                    form.submit();
                }
            } catch (error) {
                console.error('Submit error:', error);
                alert('Đã xảy ra lỗi khi nộp bài. Vui lòng thử lại.');

                // Restart timer nếu submit thất bại
                if (!isLogout) {
                    startTimer();
                }
            }
        }

        /**
         * Handle save exam (lưu tạm không logout)
         */
        async function handleSaveExam() {
            await submitExam(false);
        }

        /**
         * Confirm submit (nộp bài cuối cùng)
         */
        function confirmSubmit() {
            hideSubmitDialog();
            submitExam(true);
        }

        // Utility functions
        function formatTime(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        }
    </script>
</body>

</html>