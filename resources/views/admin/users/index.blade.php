<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quản lý người dùng - Admin</title>
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

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 0;
            position: fixed;
            display: flex;
            flex-direction: column;
            justify-content: flex-start; 
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

        .sidebar-header p {
            font-size: 14px;
            opacity: 0.8;
        }

        .sidebar-menu {
            padding: 30px 0;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .sidebar-logout {
            margin-top: auto; 
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

        .menu-item i {
            width: 20px;
            font-size: 18px;
        }

        /* Main Content */
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .top-bar h1 {
            font-size: 28px;
            color: #333;
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
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-success {
            background: #10b981;
            color: white;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-warning {
            background: #f59e0b;
            color: white;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
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
            font-size: 20px;
            color: #333;
        }

        .search-bar {
            padding: 20px 30px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            gap: 15px;
        }

        .search-input {
            flex: 1;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
        }

        .search-input:focus {
            border-color: #667eea;
        }

        .select-filter {
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            cursor: pointer;
        }

        /* Table */
        .table-container {
            overflow-x: auto;
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
            letter-spacing: 0.5px;
        }

        td {
            padding: 20px 30px;
            border-top: 1px solid #e5e7eb;
            font-size: 14px;
        }

        tbody tr:hover {
            background: #f9fafb;
        }

        .role-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .role-admin {
            background: #fef3c7;
            color: #92400e;
        }

        .role-teacher {
            background: #dbeafe;
            color: #1e40af;
        }

        .role-student {
            background: #d1fae5;
            color: #065f46;
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

        .btn-icon:hover {
            transform: translateY(-2px);
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 15px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            animation: modalSlideIn 0.3s;
        }

        @keyframes modalSlideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            padding: 25px 30px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            font-size: 20px;
            color: #333;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #6b7280;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-body {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 14px;
            color: #333;
        }

        .form-group label .required {
            color: #ef4444;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: #667eea;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
        }

        .modal-footer {
            padding: 20px 30px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
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

        .alert-info {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }

            .top-bar {
                flex-direction: column;
                gap: 15px;
            }

            .search-bar {
                flex-direction: column;
            }

            .action-buttons {
                flex-wrap: wrap;
            }
        }
    </style>
</head>

<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-graduation-cap"></i> Admin</h2>
                <p>Web Thi Tiếng Anh</p>
            </div>
            <nav class="sidebar-menu">
                <!-- <a href="#" class="menu-item">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a> -->
                <a href="{{ route('admin.users.index') }}" class="menu-item active">
                    <i class="fas fa-users"></i>
                    <span>Quản lý người dùng</span>
                </a>
                <a href="{{ route('admin.exams.index') }}" class="menu-item">
                    <i class="fas fa-book"></i>
                    <span>Quản lý đề thi</span>
                </a>
                <a href="{{ route('admin.reports.index') }}" class="menu-item">
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

        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Bar -->
            <div class="top-bar">
                <h1>Quản lý người dùng</h1>
                <button class="btn btn-primary" onclick="openCreateModal()">
                    <i class="fas fa-plus"></i> Tạo người dùng mới
                </button>
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

            <!-- Content Card -->
            <div class="content-card">
                <div class="card-header">
                    <h2>Danh sách người dùng</h2>
                </div>

                <div class="search-bar">
                    <input type="text" class="search-input" placeholder="Tìm kiếm theo tên, email..." id="searchInput">
                    <select class="select-filter" id="roleFilter">
                        <option value="">-- Tất cả role -- </option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Họ tên</th>
                                <th>Email</th>
                                <th>Vai trò</th>
                                <th>Ngày tạo</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>#{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="role-badge role-{{ $user->role ? $user->role->name : 'student' }}">
                                            {{ $user->role ? $user->role->name : 'N/A' }}
                                        </span>
                                    </td>
                                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-icon btn-warning" onclick="openEditModal({{ $user->id }})"
                                                title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn-icon btn-success" onclick="sendCredentials({{ $user->id }})"
                                                title="Gửi thông tin đăng nhập">
                                                <i class="fas fa-envelope"></i>
                                            </button>
                                            <button class="btn-icon btn-danger" onclick="deleteUser({{ $user->id }})"
                                                title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 40px;">
                                        <i class="fas fa-inbox"
                                            style="font-size: 48px; color: #d1d5db; margin-bottom: 10px;"></i>
                                        <p style="color: #6b7280;">Chưa có người dùng nào</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="padding: 20px 30px;">
                    {{ $users->links() }}
                </div>
            </div>
        </main>
    </div>

    <!-- Create User Modal -->
    <div class="modal @if($errors->any()) active @endif" id="createModal">
        <div class="modal-content">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h3>Tạo người dùng mới</h3>
                    <button type="button" class="close-modal" onclick="closeModal('createModal')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Họ và tên -->
                    <div class="form-group">
                        <label>Họ và tên <span class="required">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Nhập họ và tên" 
                               value="{{ old('name') }}">
                        @error('name')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label>Email <span class="required">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="Nhập địa chỉ email" 
                               value="{{ old('email') }}">
                        @error('email')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Mật khẩu -->
                    <div class="form-group">
                        <label>Mật khẩu <span class="required">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu">
                        @if($errors->has('password'))
                            @foreach($errors->get('password') as $error)
                                @if(!str_contains($error, 'không khớp'))
                                    <div class="alert alert-danger">{{ $error }}</div>
                                @endif
                            @endforeach
                        @endif
                    </div>

                    <!-- Xác nhận mật khẩu -->
                    <div class="form-group">
                        <label>Xác nhận mật khẩu <span class="required">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Nhập lại mật khẩu">
                        @if($errors->has('password'))
                            @foreach($errors->get('password') as $error)
                                @if(str_contains($error, 'không khớp'))
                                    <div class="alert alert-danger">{{ $error }}</div>
                                @endif
                            @endforeach
                        @endif
                    </div>

                    <!-- Vai trò -->
                    <div class="form-group">
                        <label>Vai trò <span class="required">*</span></label>
                        <select name="role_id" class="form-control">
                            <option value="">-- Chọn vai trò --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Gửi email -->
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" name="send_email" id="sendEmail" value="1" 
                                   {{ old('send_email', 1) ? 'checked' : '' }}>
                            <label for="sendEmail">Gửi thông tin đăng nhập qua email</label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('createModal')">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Tạo người dùng
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- Edit User Modal -->
    <div class="modal  @if($errors->any()) active @endif" id="editModal">
        <div class="modal-content">
            <form action="{{ route('admin.users.update', ['id' => '__ID__']) }}" method="POST" id="editForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h3>Chỉnh sửa người dùng</h3>
                    <button type="button" class="close-modal" onclick="closeModal('editModal')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Họ và tên <span class="required">*</span></label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                        @error('name')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Email <span class="required">*</span></label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                        @error('email')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Vai trò <span class="required">*</span></label>
                        <select name="role_id" id="edit_role" class="form-control" required>
                             @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : ''}}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Mật khẩu mới (để trống nếu không đổi)</label>
                        <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu mới">
                         @if($errors->has('password'))
                            @foreach($errors->get('password') as $error)
                                @if(!str_contains($error, 'không khớp'))
                                    <div class="alert alert-danger">{{ $error }}</div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                    <div class="form-group">
                        <label>Xác nhận mật khẩu mới</label>
                        <input type="password" name="password_confirmation" class="form-control"
                            placeholder="Nhập lại mật khẩu mới">
                        @if($errors->has('password'))
                            @foreach($errors->get('password') as $error)
                                @if(str_contains($error, 'không khớp'))
                                    <div class="alert alert-danger">{{ $error }}</div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCreateModal() {
            document.getElementById('createModal').classList.add('active');
        }

        function openEditModal(userId) {
            // Fetch user data 
            fetch(`/admin/users/${userId}/edit`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('edit_name').value = data.name;
                    document.getElementById('edit_email').value = data.email;
                    document.getElementById('edit_role').value = data.role.id;

                    const form = document.getElementById('editForm');
                    form.action = form.action.replace('__ID__', userId);

                    document.getElementById('editModal').classList.add('active');
                })
                .catch(error => console.error('Error:', error));
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        function sendCredentials(userId) {
            if (confirm('Bạn có chắc muốn gửi thông tin đăng nhập qua email cho người dùng này?')) {
                fetch(`/admin/users/${userId}/send-credentials`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Đã gửi thông tin đăng nhập qua email thành công!');
                        } else {
                            alert('Có lỗi xảy ra: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra khi gửi email!');
                    });
            }
        }

        function deleteUser(userId) {
            if (confirm('Bạn có chắc muốn xóa người dùng này?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/users/${userId}`;

                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';

                form.appendChild(csrfInput);
                form.appendChild(methodInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Close modal when clicking outside
        window.onclick = function (event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('active');
            }
        }

        // Search and filter functionality
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('searchInput').addEventListener('input', filterTable);
            document.getElementById('roleFilter').addEventListener('change', filterTable);
        });

        function filterTable() {
            const searchValue = (document.getElementById('searchInput').value || '').toLowerCase().trim();
            const roleSelect = document.getElementById('roleFilter');
            const roleValue = roleSelect ? roleSelect.value.toLowerCase().trim() : '';
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const name = (row.cells[1]?.textContent || '').toLowerCase().trim();
                const email = (row.cells[2]?.textContent || '').toLowerCase().trim();
                const role = (row.cells[3]?.textContent || '').toLowerCase().trim();
            
                const matchSearch = name.includes(searchValue) || email.includes(searchValue);
                const matchRole = !roleValue || role.includes(roleValue);
            
                row.style.display = matchSearch && matchRole ? '' : 'none';
            });
        }

    </script>
</body>

</html>