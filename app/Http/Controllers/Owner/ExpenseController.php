<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
    /**
     * Tampilkan data seluruh pengeluaran operasional.
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $properties = $user->properties()->get();
        $selectedPropertyId = $request->query('property_id');
        $categoryFilter = $request->query('category');
        $search = $request->query('search');

        $baseExpensesQuery = $user->expenses()->with('property');

        if ($selectedPropertyId) {
            $baseExpensesQuery->where('expenses.property_id', $selectedPropertyId);
        }

        // Statistik Pengeluaran
        $totalAllTime = (clone $baseExpensesQuery)->sum('amount');
        $totalThisMonth = (clone $baseExpensesQuery)
            ->whereYear('expense_date', now()->year)
            ->whereMonth('expense_date', now()->month)
            ->sum('amount');

        $expensesQuery = clone $baseExpensesQuery;

        if ($categoryFilter) {
            $expensesQuery->where('category', $categoryFilter);
        }

        if ($search) {
            $expensesQuery->where('title', 'like', "%{$search}%");
        }

        $expenses = $expensesQuery->latest('expense_date')->get();

        $categories = ['Utilitas', 'Pemeliharaan', 'Kebersihan', 'Keamanan', 'Operasional', 'Lain-lain'];

        return view('owner.expenses.index', [
            'user' => $user,
            'properties' => $properties,
            'selectedPropertyId' => $selectedPropertyId,
            'categoryFilter' => $categoryFilter,
            'search' => $search,
            'expenses' => $expenses,
            'categories' => $categories,
            'totalThisMonth' => $totalThisMonth,
            'totalAllTime' => $totalAllTime,
        ]);
    }

    /**
     * Simpan catatan pengeluaran baru.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $userPropertyIds = $user->properties()->pluck('id')->toArray();

        $validated = $request->validate([
            'property_id' => ['required', Rule::in($userPropertyIds)],
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:50'],
            'amount' => ['required', 'numeric', 'min:0'],
            'expense_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ], [
            'property_id.required' => 'Pilih properti terkait.',
            'title.required' => 'Judul pengeluaran wajib diisi.',
            'category.required' => 'Kategori pengeluaran wajib dipilih.',
            'amount.required' => 'Nominal biaya wajib diisi.',
            'expense_date.required' => 'Tanggal pengeluaran wajib ditentukan.',
        ]);

        Expense::create($validated);

        return redirect()->route('owner.expenses.index')->with('success', "Pengeluaran '{$validated['title']}' sebesar Rp ".number_format((float) $validated['amount'], 0, ',', '.').' berhasil dicatat!');
    }

    /**
     * Perbarui catatan pengeluaran.
     */
    public function update(Request $request, Expense $expense): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($expense->property->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $userPropertyIds = $user->properties()->pluck('id')->toArray();

        $validated = $request->validate([
            'property_id' => ['required', Rule::in($userPropertyIds)],
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:50'],
            'amount' => ['required', 'numeric', 'min:0'],
            'expense_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $expense->update($validated);

        return redirect()->route('owner.expenses.index')->with('success', "Catatan pengeluaran '{$expense->title}' berhasil diperbarui.");
    }

    /**
     * Hapus catatan pengeluaran.
     */
    public function destroy(Request $request, Expense $expense): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($expense->property->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $title = $expense->title;
        $expense->delete();

        return redirect()->route('owner.expenses.index')->with('success', "Pengeluaran '{$title}' telah dihapus.");
    }
}
