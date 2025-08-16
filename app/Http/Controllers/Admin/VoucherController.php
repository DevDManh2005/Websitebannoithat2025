<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::latest()->paginate(15);
        return view('admins.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        return view('admins.vouchers.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code'             => 'required|string|unique:vouchers,code',
            'type'             => 'required|in:percent,fixed',
            'value'            => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'usage_limit'      => 'nullable|integer|min:1',
            'start_at'         => 'nullable|date',
            'end_at'           => 'nullable|date|after_or_equal:start_at',
            'is_active'        => 'boolean',
        ], [
            'value.max' => 'Mức giảm theo phần trăm chỉ được tối đa 50%.',
        ]);

        $validator->sometimes('value', 'max:50', function ($input) {
            return $input->type === 'percent';
        });

        $data = $validator->validate();
        $data['is_active'] = $request->boolean('is_active');

        Voucher::create($data);

        return redirect()->route('admin.vouchers.index')->with('success', 'Tạo voucher thành công.');
    }

    public function edit(Voucher $voucher)
    {
        return view('admins.vouchers.edit', compact('voucher'));
    }

    public function update(Request $request, Voucher $voucher)
    {
        $validator = Validator::make($request->all(), [
            'code'             => 'required|string|unique:vouchers,code,' . $voucher->id,
            'type'             => 'required|in:percent,fixed',
            'value'            => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'usage_limit'      => 'nullable|integer|min:1',
            'start_at'         => 'nullable|date',
            'end_at'           => 'nullable|date|after_or_equal:start_at',
            'is_active'        => 'boolean',
        ], [
            'value.max' => 'Mức giảm theo phần trăm chỉ được tối đa 50%.',
        ]);

        $validator->sometimes('value', 'max:50', function ($input) {
            return $input->type === 'percent';
        });

        $data = $validator->validate();
        $data['is_active'] = $request->boolean('is_active');
        $voucher->update($data);

        return redirect()->route('admin.vouchers.index')->with('success', 'Cập nhật voucher thành công.');
    }

    public function destroy(Voucher $voucher)
    {
        $voucher->delete();
        return back()->with('success', 'Xóa voucher thành công.');
    }
}