
@extends('admins.layouts.app')

@section('title', 'Chi tiết Đơn hàng #' . $order->order_code)

@section('content')
<style>
  /* General card styles */
  .card-soft {
    border-radius: 16px;
    border: 1px solid rgba(32, 25, 21, 0.08);
    margin-bottom: 1.5rem;
  }
  .card-soft .card-header {
    background: transparent;
    border-bottom: 1px dashed rgba(32, 25, 21, 0.12);
    padding: 1rem 1.5rem;
  }
  .card-soft .card-body {
    padding: 1rem 1.5rem;
  }
  .card-title {
    font-size: 1.25rem;
    font-weight: 600;
  }

  /* Badge styles */
  .badge.bg-success-soft { background: #e5f7ed; color: #1e6b3a; }
  .badge.bg-danger-soft { background: #fde7e7; color: #992f2f; }
  .badge.bg-info-soft { background: #e6f1ff; color: #0b4a8b; }
  .badge.bg-primary-soft { background: #e8ebff; color: #2a3cff; }
  .badge.bg-warning-soft { background: #fff4d6; color: #8b6b00; }

  /* Responsive container */
  .container-fluid {
    padding: 1rem;
  }

  /* Thông tin giao hàng styles */
  .shipping-info .card-body {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem 1.5rem;
  }
  .shipping-info .mb-3 {
    margin-bottom: 0 !important;
  }
  .shipping-info .form-label {
    font-size: 0.9rem;
    font-weight: 500;
    margin-bottom: 0.25rem;
    color: #333;
  }
  .shipping-info .form-control-static {
    font-size: 0.95rem;
    color: #555;
    line-height: 1.4;
  }

  /* Responsive layout */
  @media (max-width: 992px) {
    .col-lg-8, .col-lg-4 {
      flex: 0 0 100%;
      max-width: 100%;
    }
    .d-flex.justify-content-between.align-items-center.mb-3 {
      flex-direction: column;
      gap: 1rem;
      align-items: flex-start;
    }
    .btn-outline-secondary {
      width: 100%;
      text-align: center;
    }
    .shipping-info .card-body {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 576px) {
    .card-title {
      font-size: 1.1rem;
    }
    .card-body {
      padding: 1rem;
    }
    .fs-5 {
      font-size: 1.1rem !important;
    }
    .shipping-info .form-label {
      font-size: 0.85rem;
    }
    .shipping-info .form-control-static {
      font-size: 0.9rem;
    }
  }

  /* Print styles */
  @media print {
    .no-print { display: none; }
    .invoice {
      width: 100%;
      max-width: 800px;
      margin: 0 auto;
      padding: 20px;
      font-family: Arial, sans-serif;
      font-size: 14px;
      color: #000;
    }
    .invoice-header {
      text-align: center;
      margin-bottom: 20px;
    }
    .invoice-header h2 {
      font-size: 24px;
      margin-bottom: 5px;
    }
    .invoice-header p {
      font-size: 14px;
      color: #555;
    }
    .invoice h4 {
      font-size: 18px;
      margin: 20px 0 10px;
      color: #333;
    }
    .invoice p {
      margin: 5px 0;
      font-size: 14px;
    }
    .invoice-table {
      width: 100%;
      border-collapse: collapse;
      margin: 20px 0;
    }
    .invoice-table th, .invoice-table td {
      border: 1px solid #000;
      padding: 10px;
      text-align: left;
      font-size: 14px;
    }
    .invoice-table th {
      background: #f2f2f2;
      font-weight: bold;
    }
    .invoice-table td small {
      font-size: 12px;
      color: #555;
    }
    /* Adjust table for smaller print sizes */
    @media print and (max-width: 600px) {
      .invoice-table th, .invoice-table td {
        font-size: 12px;
        padding: 8px;
      }
      .invoice-header h2 {
        font-size: 20px;
      }
      .invoice h4 {
        font-size: 16px;
      }
      .invoice p {
        font-size: 12px;
      }
      .shipping-info .card-body {
        grid-template-columns: 1fr;
      }
    }
  }
</style>

<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h5 mb-0 fw-bold">Chi tiết Đơn hàng #{{ $order->order_code }}</h1>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary no-print">
      <i class="bi bi-arrow-left"></i> Quay lại
    </a>
  </div>

  @if(session('success')) <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div> @endif
  @if(session('error'))   <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}</div> @endif

  <div class="row g-3">
    <div class="col-lg-8">
      {{-- SẢN PHẨM --}}
      <div class="card card-soft">
        <div class="card-header"><h5 class="card-title mb-0">Sản phẩm trong đơn</h5></div>
        <div class="card-body">
          @forelse($order->items as $item)
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div class="me-3">
                <strong>{{ $item->variant->product->name }}</strong><br>
                <small class="text-muted">
                  @forelse((array)$item->variant->attributes as $key => $value)
                    {{ ucfirst($key) }}: {{ $value }}@if(!$loop->last), @endif
                  @empty
                    Sản phẩm gốc
                  @endforelse
                </small>
              </div>
              <div class="text-end">
                {{ number_format($item->price) }} ₫ x {{ $item->quantity }}<br>
                <strong>{{ number_format($item->subtotal) }} ₫</strong>
              </div>
            </div>
            @if(!$loop->last) <hr class="my-2"> @endif
          @empty
            <div class="text-muted">Không có sản phẩm.</div>
          @endforelse
        </div>
      </div>

      {{-- TỔNG CỘNG --}}
      <div class="card card-soft mt-3">
        <div class="card-header"><h5 class="card-title mb-0">Tổng cộng đơn hàng</h5></div>
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <span>Tạm tính:</span>
            <span>{{ number_format($order->total_amount) }} ₫</span>
          </div>
          @if($order->discount > 0)
            <div class="d-flex justify-content-between text-success">
              <span>Giảm giá (Voucher):</span>
              <span>-{{ number_format($order->discount) }} ₫</span>
            </div>
          @endif
          <div class="d-flex justify-content-between">
            <span>Phí vận chuyển:</span>
            <span>{{ number_format(optional($order->shipment)->shipping_fee ?? 0) }} ₫</span>
          </div>
          <hr>
          <div class="d-flex justify-content-between fw-bold fs-5">
            <span>Thành tiền:</span>
            <span class="text-danger">{{ number_format($order->final_amount) }} ₫</span>
          </div>
        </div>
      </div>

      {{-- THÔNG TIN GIAO HÀNG --}}
      <div class="card card-soft mt-3 shipping-info">
        <div class="card-header"><h5 class="card-title mb-0">Thông tin giao hàng</h5></div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">Người nhận</label>
            <p class="form-control-static">{{ optional($order->shipment)->receiver_name ?? 'Không có' }}</p>
          </div>
          <div class="mb-3">
            <label class="form-label">Điện thoại</label>
            <p class="form-control-static">{{ optional($order->shipment)->phone ?? 'Không có' }}</p>
          </div>
          <div class="mb-3">
            <label class="form-label">Tỉnh/Thành phố</label>
            <p class="form-control-static">{{ optional($order->shipment)->city ?? 'Không có' }}</p>
          </div>
          <div class="mb-3">
            <label class="form-label">Quận/Huyện</label>
            <p class="form-control-static">{{ optional($order->shipment)->district ?? 'Không có' }}</p>
          </div>
          <div class="mb-3">
            <label class="form-label">Phường/Xã</label>
            <p class="form-control-static">{{ optional($order->shipment)->ward ?? 'Không có' }}</p>
          </div>
          <div class="mb-3">
            <label class="form-label">Địa chỉ chi tiết</label>
            <p class="form-control-static">{{ optional($order->shipment)->address ?? 'Không có' }}</p>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      {{-- KHÁCH HÀNG --}}
      <div class="card card-soft">
        <div class="card-header"><h5 class="card-title mb-0">Thông tin Khách hàng</h5></div>
        <div class="card-body">
          <p class="mb-1"><strong>Tên:</strong> {{ $order->user->name }}</p>
          <p class="mb-1"><strong>Email:</strong> {{ $order->user->email }}</p>
          @if($order->shipment && $order->shipment->tracking_code)
            <p class="mb-0"><strong>Mã vận đơn:</strong> <span class="text-primary fw-bold">{{ $order->shipment->tracking_code }}</span></p>
          @endif
        </div>
      </div>

      {{-- HÀNH ĐỘNG --}}
      <div class="card card-soft mt-3">
        <div class="card-header"><h5 class="card-title mb-0">Hành động</h5></div>
        <div class="card-body">
          <button onclick="printInvoice()" class="btn btn-outline-primary mb-3 no-print">In hóa đơn</button>
          
          @if(in_array($order->status, ['cancelled', 'received']))
            <div class="alert alert-info mb-0">
              Đơn hàng đã ở trạng thái cuối ({{ $order->status == 'cancelled' ? 'Đã hủy' : 'Khách đã nhận' }}) và không thể thay đổi.
            </div>
          @else
            {{-- Cập nhật trạng thái (không có "received") --}}
            <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="mb-3 no-print">
              @csrf @method('PATCH')
              <label for="order_status" class="form-label">Cập nhật trạng thái:</label>
              <div class="input-group">
                <select name="status" id="order_status" class="form-select">
                  <option value="pending"    @selected($order->status == 'pending')>Đang chờ</option>
                  <option value="processing" @selected($order->status == 'processing')>Đang xử lý</option>
                  <option value="shipping"   @selected($order->status == 'shipping')>Đang giao</option>
                  <option value="delivered"  @selected($order->status == 'delivered')>Đã giao</option>
                  <option value="cancelled"  @selected($order->status == 'cancelled')>Đã hủy</option>
                </select>
                <button type="submit" class="btn btn-primary">Cập nhật</button>
              </div>
            </form>

            {{-- Nút Đã thu COD --}}
            @php
              $isCod  = ($order->payment_method ?? 'cod') === 'cod';
              $isPaid = (($order->payment_status ?? 'unpaid') === 'paid') || ($order->is_paid ?? false);
            @endphp
            @if($isCod && !$isPaid)
              <form action="{{ route('admin.orders.cod-paid', $order) }}" method="POST" class="d-grid mb-3 no-print">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-outline-success">Đã thu COD</button>
              </form>
            @endif

            <hr class="no-print">

            {{-- Nội bộ: ready to ship --}}
            @if(!optional($order->shipment)->tracking_code && in_array($order->status, ['pending','processing']))
              <form action="{{ route('admin.orders.ready-to-ship', $order) }}" method="POST" class="d-grid no-print">
                @csrf
                <button type="submit" class="btn btn-success">Sẵn sàng giao (nội bộ)</button>
              </form>
            @endif
          @endif
        </div>
      </div>
    </div>
  </div>

  {{-- Template Hóa đơn để in --}}
  <div id="invoice" class="invoice" style="display: none;">
    <div class="invoice-header">
      <h2>Hóa đơn #{{ $order->order_code }}</h2>
      <p>Ngày đặt hàng: {{ $order->created_at->format('d/m/Y H:i') }}</p>
    </div>
    <hr>
    <h4>Thông tin khách hàng</h4>
    <p><strong>Tên:</strong> {{ $order->user->name }}</p>
    <p><strong>Email:</strong> {{ $order->user->email }}</p>
    @if($order->shipment && $order->shipment->tracking_code)
      <p><strong>Mã vận đơn:</strong> {{ $order->shipment->tracking_code }}</p>
    @endif
    <h4>Thông tin giao hàng</h4>
    <p><strong>Người nhận:</strong> {{ optional($order->shipment)->receiver_name ?? 'Không có' }}</p>
    <p><strong>Điện thoại:</strong> {{ optional($order->shipment)->phone ?? 'Không có' }}</p>
    <p><strong>Địa chỉ:</strong> {{ optional($order->shipment)->address ?? 'Không có' }}, {{ optional($order->shipment)->ward ?? 'Không có' }}, {{ optional($order->shipment)->district ?? 'Không có' }}, {{ optional($order->shipment)->city ?? 'Không có' }}</p>
    <h4>Sản phẩm</h4>
    <table class="invoice-table">
      <thead>
        <tr>
          <th>Sản phẩm</th>
          <th>Số lượng</th>
          <th>Đơn giá</th>
          <th>Tổng</th>
        </tr>
      </thead>
      <tbody>
        @forelse($order->items as $item)
          <tr>
            <td>{{ $item->variant->product->name }}<br>
                <small>
                  @forelse((array)$item->variant->attributes as $key => $value)
                    {{ ucfirst($key) }}: {{ $value }}@if(!$loop->last), @endif
                  @empty
                    Sản phẩm gốc
                  @endforelse
                </small>
            </td>
            <td>{{ $item->quantity }}</td>
            <td>{{ number_format($item->price) }} ₫</td>
            <td>{{ number_format($item->subtotal) }} ₫</td>
          </tr>
        @empty
          <tr><td colspan="4">Không có sản phẩm.</td></tr>
        @endforelse
      </tbody>
    </table>
    <h4>Tổng cộng</h4>
    <p><strong>Tạm tính:</strong> {{ number_format($order->total_amount) }} ₫</p>
    @if($order->discount > 0)
      <p><strong>Giảm giá (Voucher):</strong> -{{ number_format($order->discount) }} ₫</p>
    @endif
    <p><strong>Phí vận chuyển:</strong> {{ number_format(optional($order->shipment)->shipping_fee ?? 0) }} ₫</p>
    <p><strong>Thành tiền:</strong> {{ number_format($order->final_amount) }} ₫</p>
  </div>
</div>
@endsection

@push('scripts')
<script>
function printInvoice() {
  const invoice = document.getElementById('invoice').innerHTML;
  const printWindow = window.open('', '_blank');
  printWindow.document.write(`
    <html>
      <head>
        <title>Hóa đơn #{{ $order->order_code }}</title>
        <style>
          body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            color: #000;
          }
          .invoice { 
            width: 100%; 
            max-width: 800px; 
            margin: auto; 
            padding: 20px; 
          }
          .invoice-header { 
            text-align: center; 
            margin-bottom: 20px; 
          }
          .invoice-header h2 { 
            font-size: 24px; 
            margin-bottom: 5px; 
          }
          .invoice-header p { 
            font-size: 14px; 
            color: #555; 
          }
          .invoice h4 { 
            font-size: 18px; 
            margin: 20px 0 10px; 
            color: #333; 
          }
          .invoice p { 
            margin: 5px 0; 
            font-size: 14px; 
          }
          .invoice-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 20px 0; 
          }
          .invoice-table th, .invoice-table td { 
            border: 1px solid #000; 
            padding: 10px; 
            text-align: left; 
            font-size: 14px; 
          }
          .invoice-table th { 
            background: #f2f2f2; 
            font-weight: bold; 
          }
          .invoice-table td small { 
            font-size: 12px; 
            color: #555; 
          }
          @media print and (max-width: 600px) {
            .invoice-table th, .invoice-table td {
              font-size: 12px;
              padding: 8px;
            }
            .invoice-header h2 {
              font-size: 20px;
            }
            .invoice h4 {
              font-size: 16px;
            }
            .invoice p {
              font-size: 12px;
            }
          }
        </style>
      </head>
      <body>
        ${invoice}
      </body>
    </html>
  `);
  printWindow.document.close();
  printWindow.print();
}
</script>
@endpush
