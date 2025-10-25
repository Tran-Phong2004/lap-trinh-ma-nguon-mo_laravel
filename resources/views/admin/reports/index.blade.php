<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Báo cáo kết quả thi</title>
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .stat-icon.blue {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .stat-icon.green {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .stat-icon.orange {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }

        .stat-icon.purple {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: white;
        }

        .stat-info h3 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-info p {
            color: #6b7280;
            font-size: 14px;
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

        .filter-section {
            padding: 20px 30px;
            border-bottom: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
        }

        .form-control {
            padding: 10px 12px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
        }

        .btn {
            padding: 10px 20px;
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

        .btn-success {
            background: #10b981;
            color: white;
        }

        .btn-info {
            background: #3b82f6;
            color: white;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f9fafb;
        }

        th {
            padding: 16px 30px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            color: #6b7280;
            text-transform: uppercase;
        }

        td {
            padding: 20px 30px;
            border-top: 1px solid #e5e7eb;
            font-size: 14px;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-completed {
            background: #d1fae5;
            color: #065f46;
        }

        .status-in-progress {
            background: #fef3c7;
            color: #92400e;
        }

        .status-not-started {
            background: #e5e7eb;
            color: #374151;
        }

        .score-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 16px;
        }

        .score-excellent {
            background: #d1fae5;
            color: #065f46;
        }

        .score-good {
            background: #dbeafe;
            color: #1e3a8a;
        }

        .score-average {
            background: #fef3c7;
            color: #92400e;
        }

        .score-poor {
            background: #fee2e2;
            color: #991b1b;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-icon {
            width: 35px;
            height: 35px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
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
                <a href="{{ route('admin.users.index') }}" class="menu-item">
                    <i class="fas fa-users"></i>
                    <span>Quản lý người dùng</span>
                </a>
                <a href="{{ route('admin.exams.index') }}" class="menu-item">
                    <i class="fas fa-book"></i>
                    <span>Quản lý bài thi</span>
                </a>
                <a href="{{ route('admin.reports.index') }}" class="menu-item active">
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
                <h1><i class="fas fa-chart-bar"></i> Báo cáo kết quả thi</h1>
            </div>

            <!-- Thống kê tổng quan -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ number_format($statistics['total_sessions']) }}</h3>
                        <p>Tổng số bài thi</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ number_format($statistics['completed_sessions']) }}</h3>
                        <p>Đã hoàn thành</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon orange">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ number_format($statistics['in_progress_sessions']) }}</h3>
                        <p>Đang làm bài</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon purple">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ number_format($statistics['average_score'], 1) }}</h3>
                        <p>Điểm trung bình</p>
                    </div>
                </div>
            </div>

            <!-- Bộ lọc -->
            <div class="content-card">
                <div class="card-header">
                    <h2>Danh sách kết quả</h2>
                    <!-- <div>
                        <button class="btn btn-success" onclick="exportReport()">
                            <i class="fas fa-file-excel"></i> Xuất Excel
                        </button>
                    </div> -->
                </div>

                <div class="filter-section">
                    <form method="GET" action="{{ route('admin.reports.index') }}">
                        <div class="filter-grid">
                            <div class="form-group">
                                <label>Bài thi</label>
                                <select name="exam_id" class="form-control">
                                    <option value="">Tất cả bài thi</option>
                                    @foreach($exams as $exam)
                                        <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                                            {{ $exam->exam_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Học sinh</label>
                                <select name="student_id" class="form-control">
                                    <option value="">Tất cả học sinh</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                            {{ $student->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Trạng thái</label>
                                <select name="status" class="form-control">
                                    <option value="">Tất cả</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Đã hoàn thành</option>
                                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Đang làm</option>
                                    <option value="not_started" {{ request('status') == 'not_started' ? 'selected' : '' }}>Chưa bắt đầu</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Từ ngày</label>
                                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                            </div>

                            <div class="form-group">
                                <label>Đến ngày</label>
                                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                            </div>

                            <div class="form-group" style="align-self: flex-end;">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Lọc
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Học sinh</th>
                                <th>Bài thi</th>
                                <th>Thời gian thi</th>
                                <th>Điểm số</th>
                                <th>Số câu đúng/sai</th>
                                <th>Trạng thái</th>
                                <!-- <th>Thao tác</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sessions as $session)
                                <tr>
                                    <td>
                                        <strong>{{ $session->student->name ?? 'N/A' }}</strong>
                                        <br>
                                        <small style="color: #6b7280;">{{ $session->student->email ?? '' }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $session->exam->exam_name }}</strong>
                                        <br>
                                        <small style="color: #6b7280;">
                                            <i class="fas fa-question-circle"></i> 
                                            {{ $session->exam->questions->count() }} câu
                                        </small>
                                    </td>
                                    <td>
                                        @if($session->results->first() && $session->results->first()->submitted_at)
                                            {{ \Carbon\Carbon::parse($session->results->first()->submitted_at)->format('d/m/Y H:i') }}
                                        @else
                                            <span style="color: #6b7280;">Chưa nộp bài</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($session->results->first())
                                            @php
                                                $score = $session->results->first()->score;
                                                $scoreClass = $score >= 8 ? 'excellent' : ($score >= 6.5 ? 'good' : ($score >= 5 ? 'average' : 'poor'));
                                            @endphp
                                            <span class="score-badge score-{{ $scoreClass }}">
                                                {{ number_format($score, 1) }}/10
                                            </span>
                                        @else
                                            <span style="color: #6b7280;">Chưa có điểm</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($session->results->first())
                                            <span style="color: #10b981; font-weight: 600;">
                                                <i class="fas fa-check"></i> {{ $session->results->first()->correct_answers }}
                                            </span>
                                            /
                                            <span style="color: #ef4444; font-weight: 600;">
                                                <i class="fas fa-times"></i> {{ $session->results->first()->wrong_answers }}
                                            </span>
                                        @else
                                            <span style="color: #6b7280;">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusMap = [
                                                'completed' => 'Đã hoàn thành',
                                                'in_progress' => 'Đang làm',
                                                'not_started' => 'Chưa bắt đầu'
                                            ];
                                        @endphp
                                        <span class="status-badge status-{{ $session->status }}">
                                            {{ $statusMap[$session->status] ?? $session->status }}
                                        </span>
                                    </td>
                                    <!-- <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.reports.show', $session->id) }}" 
                                               class="btn-icon btn-info" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td> -->
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 40px;">
                                        <i class="fas fa-inbox" style="font-size: 48px; color: #d1d5db;"></i>
                                        <p style="color: #6b7280; margin-top: 10px;">Chưa có dữ liệu</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="padding: 20px 30px;">
                    {{ $sessions->links() }}
                </div>
            </div>
        </main>
    </div>

    <script>
        function exportReport() {
            alert('Chức năng xuất Excel đang được phát triển');
        }
    </script>
</body>
</html>