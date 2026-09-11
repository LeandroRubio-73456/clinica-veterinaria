<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OperatingRoomController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SpecialtyController;
use App\Http\Controllers\SpeciesController;
use App\Http\Controllers\SurgeryController;
use App\Http\Controllers\SurgeryTypeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VeterinarianController;
use App\Http\Controllers\OwnerPortalController;
use App\Http\Controllers\RoleController;
use App\Models\OperatingRoom;
use App\Models\Surgery;
use App\Models\SurgeryType;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    $today = now()->toDateString();
    $monthStart = now()->startOfMonth()->toDateString();
    $monthEnd = now()->endOfMonth()->toDateString();

    $operatingRooms = OperatingRoom::query()
        ->where('state', '!=', 'inactive')
        ->with([
            'surgeries' => fn ($query) => $query
                ->whereDate('scheduled_date', $today)
                ->whereIn('state', ['scheduled', 'in_progress'])
                ->with(['surgeryType', 'veterinarian'])
                ->orderBy('start_time'),
        ])
        ->orderBy('name')
        ->get();

    return view('welcome', [
        'operatingRooms' => $operatingRooms,
        'operatingRoomCount' => $operatingRooms->count(),
        'activeSurgeryTypeCount' => SurgeryType::query()
            ->where('state', 'active')
            ->count(),
        'monthlySurgeryCount' => Surgery::query()
            ->whereBetween('scheduled_date', [$monthStart, $monthEnd])
            ->count(),
        'welcomeTimestamp' => now()->format('H:i — d/m/Y'),
    ]);
});

/*
|--------------------------------------------------------------------------
| Rutas autenticadas
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::middleware('role:propietario')->prefix('mi-portal')->name('portal.')->group(function () {
        Route::get('/', [OwnerPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/cirugias/{surgery}', [OwnerPortalController::class, 'surgery'])->name('surgery');
        Route::get('/mi-informacion', [OwnerPortalController::class, 'profile'])->name('profile');
        Route::patch('/mi-informacion', [OwnerPortalController::class, 'updateProfile'])->name('profile.update');
        Route::patch('/contrasena', [OwnerPortalController::class, 'updatePassword'])->name('password.update');
    });

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', DashboardController::class)
        ->name('dashboard');

    Route::get('/dashboard/live', [DashboardController::class, 'live'])
        ->name('dashboard.live');

    /*
    |--------------------------------------------------------------------------
    | Perfil: cualquier usuario autenticado
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::get('/notifications/live', [NotificationController::class, 'live'])
        ->name('notifications.live');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Dueños, mascotas y cirugías administrativas
    |--------------------------------------------------------------------------
    | Admin y administrativo pueden crear y administrar información operativa.
    */

    Route::middleware('role:admin,administrativo')->group(function () {
        Route::resource('owners', OwnerController::class);

        Route::resource('pets', PetController::class);

        Route::patch('/pets/{pet}/deactivate', [PetController::class, 'deactivate'])
            ->name('pets.deactivate');

        Route::patch('/pets/{pet}/activate', [PetController::class, 'activate'])
            ->name('pets.activate');

        Route::resource('surgeries', SurgeryController::class)
            ->only([
                'create',
                'store',
                'edit',
                'update',
                'destroy',
            ]);

        Route::post('/surgeries/{surgery}/cancel', [SurgeryController::class, 'cancel'])
            ->name('surgeries.cancel');

        Route::post('/surgeries/{surgery}/no-show', [SurgeryController::class, 'noShow'])
            ->name('surgeries.no-show');

        Route::get('/reports', [ReportController::class, 'index'])
            ->name('reports.index');

        Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])
            ->name('reports.export.excel');

        Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])
            ->name('reports.export.pdf');
    });

    /*
    |--------------------------------------------------------------------------
    | Consulta de cirugías
    |--------------------------------------------------------------------------
    | Los tres roles pueden consultar la agenda y el detalle.
    */

    Route::middleware('role:admin,administrativo,veterinario')->group(function () {
        Route::get('/surgeries', [SurgeryController::class, 'index'])
            ->name('surgeries.index');

        Route::get('/surgeries/calendar', [SurgeryController::class, 'calendar'])
            ->name('surgeries.calendar');

        Route::get('/surgeries/calendar/events', [SurgeryController::class, 'calendarEvents'])
            ->name('surgeries.calendar.events');

        Route::get('/surgeries/{surgery}', [SurgeryController::class, 'show'])
            ->middleware('can:view,surgery')
            ->name('surgeries.show');
    });

    /*
    |--------------------------------------------------------------------------
    | Acciones del veterinario
    |--------------------------------------------------------------------------
    | El veterinario puede iniciar y finalizar cirugías.
    */

    Route::middleware('role:admin,veterinario')->group(function () {
        Route::post('/surgeries/{surgery}/start', [SurgeryController::class, 'start'])
            ->middleware('can:start,surgery')
            ->name('surgeries.start');

        Route::post('/surgeries/{surgery}/complete', [SurgeryController::class, 'complete'])
            ->middleware('can:complete,surgery')
            ->name('surgeries.complete');
    });

    /*
    |--------------------------------------------------------------------------
    | Administración de usuarios y catálogos
    |--------------------------------------------------------------------------
    | Solo el administrador puede gestionar estas entidades.
    */

    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('roles', RoleController::class)->except('show');

        Route::resource('veterinarians', VeterinarianController::class);

        Route::patch(
            '/veterinarians/{veterinarian}/deactivate',
            [VeterinarianController::class, 'deactivate']
        )->name('veterinarians.deactivate');

        Route::patch(
            '/veterinarians/{veterinarian}/activate',
            [VeterinarianController::class, 'activate']
        )->name('veterinarians.activate');

        Route::resource('operating-rooms', OperatingRoomController::class);

        Route::patch(
            '/operating-rooms/{operatingRoom}/deactivate',
            [OperatingRoomController::class, 'deactivate']
        )->name('operating-rooms.deactivate');

        Route::patch(
            '/operating-rooms/{operatingRoom}/activate',
            [OperatingRoomController::class, 'activate']
        )->name('operating-rooms.activate');

        Route::resource('species', SpeciesController::class);

        Route::patch('/species/{species}/deactivate', [SpeciesController::class, 'deactivate'])
            ->name('species.deactivate');

        Route::patch('/species/{species}/activate', [SpeciesController::class, 'activate'])
            ->name('species.activate');

        Route::resource('specialties', SpecialtyController::class);

        Route::patch('/specialties/{specialty}/deactivate', [SpecialtyController::class, 'deactivate'])
            ->name('specialties.deactivate');

        Route::patch('/specialties/{specialty}/activate', [SpecialtyController::class, 'activate'])
            ->name('specialties.activate');

        Route::resource('surgery-types', SurgeryTypeController::class);

        Route::patch(
            '/surgery-types/{surgeryType}/deactivate',
            [SurgeryTypeController::class, 'deactivate']
        )->name('surgery-types.deactivate');

        Route::patch(
            '/surgery-types/{surgeryType}/activate',
            [SurgeryTypeController::class, 'activate']
        )->name('surgery-types.activate');
    });
});

require __DIR__ . '/auth.php';
