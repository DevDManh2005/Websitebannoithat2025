<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVoucherRequest; // THAY ĐỔI: Sử dụng Form Request mới
use App\Models\Voucher;
// use Illuminate\Http\Request; // Không cần dùng trực tiếp trong store/update nữa
// use Illuminate\Support\Facades\Validator; // Bỏ đi vì đã chuyển validation sang Form Request

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

    /**
     * THAY ĐỔI: Sử dụng StoreVoucherRequest để tự động validate
     */
    public function store(StoreVoucherRequest $request)
    {
        // Dữ liệu đã được validate tự động bởi StoreVoucherRequest
        // Nếu validate thất bại, Laravel sẽ tự động redirect về trang trước kèm theo lỗi
        $data = $request->validated();

        Voucher::create($data);

        return redirect()->route('admin.vouchers.index')->with('success', 'Tạo voucher thành công.');
    }

    public function edit(Voucher $voucher)
    {
        return view('admins.vouchers.edit', compact('voucher'));
    }

    /**
     * THAY ĐỔI: Sử dụng StoreVoucherRequest cho cả hàm update
     */
    public function update(StoreVoucherRequest $request, Voucher $voucher)
    {
        // Tương tự hàm store, dữ liệu đã được validate
        $data = $request->validated();

        $voucher->update($data);

        return redirect()->route('admin.vouchers.index')->with('success', 'Cập nhật voucher thành công.');
    }

    public function destroy(Voucher $voucher)
    {
        $voucher->delete();
        return back()->with('success', 'Xóa voucher thành công.');
    }
}