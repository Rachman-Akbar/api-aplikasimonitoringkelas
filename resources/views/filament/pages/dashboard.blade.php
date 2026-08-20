<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Welcome to Aplikasi Monitoring Kelas</h1>
        <p class="mt-2 text-lg text-gray-600">Hello {{ auth()->user()->name }}, you are logged in as {{ auth()->user()->role }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900">Total Kelas</h3>
            <p class="text-3xl font-bold text-indigo-600 mt-2">{{ \App\Models\Kelas::count() }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900">Total Guru</h3>
            <p class="text-3xl font-bold text-indigo-600 mt-2">{{ \App\Models\Guru::count() }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900">Total Siswa</h3>
            <p class="text-3xl font-bold text-indigo-600 mt-2">{{ \App\Models\Siswa::count() }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900">Today's Attendance</h3>
            <p class="text-3xl font-bold text-indigo-600 mt-2">{{ \App\Models\Kehadiran::whereDate('tanggal', now())->count() }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('filament.admin.resources.kelas.index') }}" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                <h3 class="font-medium text-gray-900">Manage Classes</h3>
                <p class="text-sm text-gray-600 mt-1">View and manage class information</p>
            </a>
            
            <a href="{{ route('filament.admin.resources.gurus.index') }}" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                <h3 class="font-medium text-gray-900">Manage Teachers</h3>
                <p class="text-sm text-gray-600 mt-1">View and manage teacher information</p>
            </a>
            
            <a href="{{ route('filament.admin.resources.siswas.index') }}" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                <h3 class="font-medium text-gray-900">Manage Students</h3>
                <p class="text-sm text-gray-600 mt-1">View and manage student information</p>
            </a>
        </div>
    </div>
</div>