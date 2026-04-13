<?php

use Illuminate\Support\Facades\Route;
use ME\Hr\Http\Controllers\HrController;
use ME\Hr\Http\Controllers\HrDashboardController;
use ME\Hr\Http\Controllers\HrEmployeeController;
use ME\Hr\Http\Controllers\HrMasterController;
use ME\Hr\Http\Controllers\HrReportController;

$route = config('hr.route');

Route::middleware(['web'])->get('/hr', [HrController::class, 'index']);
Route::get('/thanas/by-district/{id}', [HrController::class, 'getThanasByDistrict']);

Route::middleware($route['middleware'] ?? ['web'])
	->prefix($route['prefix'] ?? 'admin/hr-center')
	->name($route['as'] ?? 'hr-center.')
	->group(function () {
		Route::get('/', [HrDashboardController::class, 'index'])->name('dashboard');
		Route::get('/employees', [HrEmployeeController::class, 'index'])->name('employees.index');
		Route::post('/employees', [HrEmployeeController::class, 'store'])->name('employees.store');
		Route::put('/employees/{employee}/profile', [HrEmployeeController::class, 'updateProfile'])->name('employees.profile.update');
		Route::put('/employees/{employee}/salary', [HrEmployeeController::class, 'updateSalary'])->name('employees.salary.update');
		Route::put('/employees/{employee}/address', [HrEmployeeController::class, 'updateAddress'])->name('employees.address.update');
		Route::put('/employees/{employee}/nominee', [HrEmployeeController::class, 'updateNominee'])->name('employees.nominee.update');
		Route::put('/employees/{employee}/age-verification', [HrEmployeeController::class, 'updateAgeVerification'])->name('employees.age.update');
		Route::put('/employees/{employee}/resign', [HrEmployeeController::class, 'updateResign'])->name('employees.resign.update');
		Route::put('/employees/{employee}/final-settlement', [HrEmployeeController::class, 'updateFinalSettlement'])->name('employees.final-settlement.update');
		Route::put('/employees/{employee}/final-settlement/print', [HrEmployeeController::class, 'updateFinalSettlement'])->name('employees.final-settlement.print');

		Route::put('/employees/{employee}/basic-info', [HrEmployeeController::class, 'updateBasicInfo'])->name('employees.basic-info.update');
		Route::get('/employees/{employee}/increments', [HrEmployeeController::class, 'incrementsPage'])->name('employees.increments.page');
		Route::post('/employees/{employee}/increments', [HrEmployeeController::class, 'incrementsStore'])->name('employees.increments.store');
		Route::put('/employees/{employee}/increments', [HrEmployeeController::class, 'incrementsUpdate'])->name('employees.increments.update');
		Route::get('/employees/{employee}/earnings-deductions', [HrEmployeeController::class, 'earningsDeductionsPage'])->name('employees.earnings.page');
		Route::post('/employees/{employee}/earnings-deductions', [HrEmployeeController::class, 'earningsDeductionsStore'])->name('employees.earnings.store');
		Route::put('/employees/{employee}/earnings-deductions', [HrEmployeeController::class, 'earningsDeductionsUpdate'])->name('employees.earnings.update');
		Route::delete('/employees/{employee}/earnings-deductions', [HrEmployeeController::class, 'earningsDeductionsDelete'])->name('employees.earnings.delete');
		Route::get('/employees/{employee}/leaves', [HrEmployeeController::class, 'leavesPage'])->name('employees.leaves.page');
		Route::post('/employees/{employee}/leaves', [HrEmployeeController::class, 'leavesStore'])->name('employees.leaves.store');
		Route::put('/employees/{employee}/leaves', [HrEmployeeController::class, 'leavesUpdate'])->name('employees.leaves.update');
		Route::delete('/employees/{employee}/leaves', [HrEmployeeController::class, 'leavesDelete'])->name('employees.leaves.delete');
		Route::delete('/employees/{employee}', [HrEmployeeController::class, 'destroy'])->name('employees.destroy');
		// Route::get('/employees/{employee}/print/{section}', [HrEmployeeController::class, 'printSection'])->name('employees.print.section');
		Route::get('/reports', [HrReportController::class, 'index'])->name('reports.index');
		Route::get('/reports/{report}', [HrReportController::class, 'show'])->name('reports.show');
		Route::post('/reports/monthly/lock-increment', [HrReportController::class, 'lockMonthlyIncrement'])->name('reports.monthly.lock-increment');

		Route::get('/masters/{entity}', [HrMasterController::class, 'index'])->name('masters.index');
		Route::get('/masters/{entity}/create', [HrMasterController::class, 'create'])->name('masters.create');
		Route::post('/masters/{entity}', [HrMasterController::class, 'store'])->name('masters.store');
		Route::get('/masters/{entity}/{id}/edit', [HrMasterController::class, 'edit'])->name('masters.edit');
		Route::put('/masters/{entity}/{id}', [HrMasterController::class, 'update'])->name('masters.update');
		Route::delete('/masters/{entity}/{id}', [HrMasterController::class, 'destroy'])->name('masters.destroy');
	});
