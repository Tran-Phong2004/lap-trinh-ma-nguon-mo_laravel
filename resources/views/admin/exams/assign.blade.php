<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gán bài thi cho học sinh</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f5f7fa;
            color: #333;
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 260px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 0;
            position: fixed;
            display: flex;
            flex-direction: column;
            justify-content: flex-start; /* mặc định */
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 0 30px 30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header h2 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        .sidebar-menu {
            padding: 30px 0;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .sidebar-logout {
            margin-top: auto; /* đẩy xuống cuối */
            padding: 15px 30px;
        }
        .sidebar-logout button {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 15px;
            background: none;
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        .sidebar-logout button:hover {
            background: rgba(255,255,255,0.1);
        }

        .menu-item {
            padding: 15px 30px;
            display: flex;
            align-items: center;
            gap: 15px;
            color: white;
            text-decoration: none;
            transition: background 0.3s;
        }

        .menu-item:hover,
        .menu-item.active {
            background: rgba(255, 255, 255, 0.1);
        }

        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 30px;
        }

        .top-bar {
            background: white;
            padding: 20px 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .top-bar h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .breadcrumb {
            color: #6b7280;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
        }

        .breadcrumb a {
            color: #667eea;
            text-decoration: none;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .content-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .card-header {
            padding: 25px 30px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h2 {
            font-size: 18px;
        }

        .badge {
            background: #667eea;
            color: white;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .search-bar {
            padding: 20px 30px;
            border-bottom: 1px solid #e5e7eb;
        }

        .search-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
        }

        .search-input:focus {
            outline: none;
            border-color: #667eea;
        }

        .student-list {
            max-height: 500px;
            overflow-y: auto;
        }

        .student-item {
            padding: 20px 30px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: background 0.2s;
            cursor: pointer;
        }

        .student-item:hover {
            background: #f9fafb;
        }

        .student-item input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .student-info {
            flex: 1;
        }

        .student-name {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .student-email {
            font-size: 13px;
            color: #6b7280;
        }

        .assigned-student {
            padding: 20px 30px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-success {
            background: #10b981;
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .action-bar {
            padding: 20px 30px;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .empty-state {
            padding: 60px 30px;
            text-align: center;
            color: #6b7280;
        }

        .empty-state i {
            font-size: 48px;
            color: #d1d5db;
            margin-bottom: 15px;
        }

        .select-all {
            padding: 15px 30px;
            border-bottom: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .select-all label {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            font-weight: 600;
        }

        .select-all input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        @media (max-width: 1024px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-graduation-cap"></i> Admin</h2>
                <p>Web Thi Tiếng Anh</p>
            </div>
            <nav class="sidebar-menu">
                <a href="#" class="menu-item">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="menu-item">
                    <i class="fas fa-users"></i>
                    <span>Quản lý người dùng</span>
                </a>
                <a href="{{ route('admin.exams.index') }}" class="menu-item active">
                    <i class="fas fa-book"></i>
                    <span>Quản lý bài thi</span>
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-chart-bar"></i>
                    <span>Báo cáo</span>
                </a>
            </nav>
             <div class="sidebar-logout">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Đăng xuất</span>
                    </button>
                </form>
            </div>
        </aside>

        <main class="main-content">
            <div class="top-bar">
                <h1>Gán bài thi cho học sinh</h1>
                <div class="breadcrumb">
                    <a href="{{ route('admin.exams.index') }}">Danh sách bài thi</a>
                    <i class="fas fa-chevron-right"></i>
                    <span>{{ $exam->exam_name }}</span>
                    <i class="fas fa-chevron-right"></i>
                    <span>Gán học sinh</span>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            <div class="content-grid">
                <!-- Danh sách học sinh chưa gán -->
                <div class="content-card">
                    <div class="card-header">
                        <h2>Học sinh khả dụng</h2>
                        <span class="badge">{{ $availableStudents->count() }} học sinh</span>
                    </div>

                    <div class="search-bar">
                        <input type="text" class="search-input" placeholder="Tìm kiếm học sinh..." id="searchAvailable">
                    </div>

                    <form id="assignForm" method="POST" action="{{ route('admin.exams.assign.store', $exam->id) }}">
                        @csrf
                        
                        @if($availableStudents->count() > 0)
                            <div class="select-all">
                                <label>
                                    <input type="checkbox" id="selectAll">
                                    <span>Chọn tất cả</span>
                                </label>
                            </div>

                            <div class="student-list" id="availableList">
                                @foreach($availableStudents as $student)
                                <div class="student-item" data-student-name="{{ strtolower($student->name) }}" data-student-email="{{ strtolower($student->email) }}">
                                    <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="student-checkbox">
                                    <div class="student-info">
                                        <div class="student-name">{{ $student->name }}</div>
                                        <div class="student-email">{{ $student->email }}</div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <div class="action-bar">
                                <span id="selectedCount">Chưa chọn học sinh nào</span>
                                <button type="submit" class="btn btn-success" id="assignBtn" disabled>
                                    <i class="fas fa-user-plus"></i>
                                    Gán bài thi
                                </button>
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="fas fa-users-slash"></i>
                                <p>Tất cả học sinh đã được gán bài thi này</p>
                            </div>
                        @endif
                    </form>
                </div>

                <!-- Danh sách học sinh đã gán -->
                <div class="content-card">
                    <div class="card-header">
                        <h2>Học sinh đã được gán</h2>
                        <span class="badge">{{ $assignedStudents->count() }} học sinh</span>
                    </div>

                    <div class="search-bar">
                        <input type="text" class="search-input" placeholder="Tìm kiếm học sinh..." id="searchAssigned">
                    </div>

                    @if($assignedStudents->count() > 0)
                        <div class="student-list" id="assignedList">
                            @foreach($assignedStudents as $student)
                            <div class="assigned-student" data-student-name="{{ strtolower($student->name) }}" data-student-email="{{ strtolower($student->email) }}">
                                <div class="student-info">
                                    <div class="student-name">{{ $student->name }}</div>
                                    <div class="student-email">{{ $student->email }}</div>
                                </div>
                                <span style="color: #10b981;">
                                    <i class="fas fa-check-circle"></i> Đã gán
                                </span>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-user-clock"></i>
                            <p>Chưa có học sinh nào được gán bài thi này</p>
                        </div>
                    @endif

                    <div class="action-bar">
                        <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                            Quay lại
                        </a>
                        <a href="{{ route('admin.exams.show', $exam->id) }}" class="btn btn-primary">
                            <i class="fas fa-eye"></i>
                            Xem bài thi
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Select All functionality
        const selectAllCheckbox = document.getElementById('selectAll');
        const studentCheckboxes = document.querySelectorAll('.student-checkbox');
        const selectedCount = document.getElementById('selectedCount');
        const assignBtn = document.getElementById('assignBtn');

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                const visibleCheckboxes = Array.from(studentCheckboxes).filter(cb => {
                    return cb.closest('.student-item').style.display !== 'none';
                });
                
                visibleCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateSelectedCount();
            });
        }

        studentCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCount);
        });

        function updateSelectedCount() {
            const checkedCount = document.querySelectorAll('.student-checkbox:checked').length;
            
            if (checkedCount === 0) {
                selectedCount.textContent = 'Chưa chọn học sinh nào';
                assignBtn.disabled = true;
            } else {
                selectedCount.textContent = `Đã chọn ${checkedCount} học sinh`;
                assignBtn.disabled = false;
            }

            // Update select all checkbox state
            if (selectAllCheckbox) {
                const visibleCheckboxes = Array.from(studentCheckboxes).filter(cb => {
                    return cb.closest('.student-item').style.display !== 'none';
                });
                const visibleCheckedCount = visibleCheckboxes.filter(cb => cb.checked).length;
                selectAllCheckbox.checked = visibleCheckedCount === visibleCheckboxes.length && visibleCheckboxes.length > 0;
            }
        }

        // Search functionality for available students
        const searchAvailable = document.getElementById('searchAvailable');
        if (searchAvailable) {
            searchAvailable.addEventListener('input', function() {
                const searchValue = this.value.toLowerCase();
                const studentItems = document.querySelectorAll('#availableList .student-item');

                studentItems.forEach(item => {
                    const name = item.getAttribute('data-student-name');
                    const email = item.getAttribute('data-student-email');
                    
                    if (name.includes(searchValue) || email.includes(searchValue)) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });

                updateSelectedCount();
            });
        }

        // Search functionality for assigned students
        const searchAssigned = document.getElementById('searchAssigned');
        if (searchAssigned) {
            searchAssigned.addEventListener('input', function() {
                const searchValue = this.value.toLowerCase();
                const studentItems = document.querySelectorAll('#assignedList .assigned-student');

                studentItems.forEach(item => {
                    const name = item.getAttribute('data-student-name');
                    const email = item.getAttribute('data-student-email');
                    
                    if (name.includes(searchValue) || email.includes(searchValue)) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }

        // Form submission confirmation
        const assignForm = document.getElementById('assignForm');
        if (assignForm) {
            assignForm.addEventListener('submit', function(e) {
                const checkedCount = document.querySelectorAll('.student-checkbox:checked').length;
                if (checkedCount === 0) {
                    e.preventDefault();
                    alert('Vui lòng chọn ít nhất một học sinh!');
                    return false;
                }

                const confirmed = confirm(`Bạn có chắc muốn gán bài thi cho ${checkedCount} học sinh đã chọn?`);
                if (!confirmed) {
                    e.preventDefault();
                    return false;
                }
            });
        }

        // Click on student item to toggle checkbox
        document.querySelectorAll('.student-item').forEach(item => {
            item.addEventListener('click', function(e) {
                if (e.target.type !== 'checkbox') {
                    const checkbox = this.querySelector('.student-checkbox');
                    checkbox.checked = !checkbox.checked;
                    updateSelectedCount();
                }
            });
        });
    </script>
</body>
</html>