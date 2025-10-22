{{-- resources/views/partials/flash-messages.blade.php --}}
@if(session('success'))
<div id="flashMessage" class="fixed top-4 right-4 z-50 max-w-md">
    <div class="bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3">
        <i class="fas fa-check-circle text-2xl"></i>
        <div>
            <p class="font-semibold">Thành công!</p>
            <p class="text-sm">{{ session('success') }}</p>
        </div>
        <button onclick="closeFlash()" class="ml-4 text-white hover:text-gray-200">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

@if(session('error'))
<div id="flashMessage" class="fixed top-4 right-4 z-50 max-w-md">
    <div class="bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3">
        <i class="fas fa-exclamation-circle text-2xl"></i>
        <div>
            <p class="font-semibold">Lỗi!</p>
            <p class="text-sm">{{ session('error') }}</p>
        </div>
        <button onclick="closeFlash()" class="ml-4 text-white hover:text-gray-200">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

@if(session('info'))
<div id="flashMessage" class="fixed top-4 right-4 z-50 max-w-md">
    <div class="bg-blue-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3">
        <i class="fas fa-info-circle text-2xl"></i>
        <div>
            <p class="font-semibold">Thông báo</p>
            <p class="text-sm">{{ session('info') }}</p>
        </div>
        <button onclick="closeFlash()" class="ml-4 text-white hover:text-gray-200">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

<script>
    function closeFlash() {
        const flash = document.getElementById('flashMessage');
        if (flash) {
            flash.style.opacity = '0';
            setTimeout(() => flash.remove(), 300);
        }
    }

    // Auto close after 5 seconds
    setTimeout(() => {
        closeFlash();
    }, 5000);
</script>