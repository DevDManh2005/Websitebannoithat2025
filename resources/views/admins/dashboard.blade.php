@extends('admins.layouts.app')

@section('title', 'Tổng quan')

@section('content')
  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="card p-3">
        <small>Tổng doanh thu</small>
        <h4>2,450,000,000₫</h4>
        <small class="text-success">+12.5% so với tháng trước</small>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card p-3">
        <small>Đơn hàng mới</small>
        <h4>1,234</h4>
        <small class="text-success">+8.2% so với tháng trước</small>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card p-3">
        <small>Người dùng hoạt động</small>
        <h4>8,945</h4>
        <small class="text-success">+3.1% so với tháng trước</small>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card p-3">
        <small>Sản phẩm bán chạy</small>
        <h4>567</h4>
        <small class="text-danger">-2.4% so với tháng trước</small>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-8 mb-4">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <strong>Đơn hàng gần đây</strong>
          <button class="btn btn-sm btn-outline-secondary">Xem tất cả</button>
        </div>
        <div class="table-responsive">
          <table class="table mb-0">
            <thead class="table-light">
              <tr>
                <th>Mã đơn</th>
                <th>Số tiền</th>
                <th>Trạng thái</th>
                <th>Ngày</th>
              </tr>
            </thead>
            <tbody>
              <tr><td>#DH001</td><td>32,990,000₫</td><td><span class="badge bg-dark">Đã giao</span></td><td>15/01/2025</td></tr>
              <tr><td>#DH002</td><td>28,990,000₫</td><td><span class="badge bg-secondary">Đang xử lý</span></td><td>15/01/2025</td></tr>
              <tr><td>#DH003</td><td>25,990,000₫</td><td><span class="badge bg-warning text-dark">Đã xác nhận</span></td><td>14/01/2025</td></tr>
              <tr><td>#DH004</td><td>19,990,000₫</td><td><span class="badge bg-dark">Đã giao</span></td><td>14/01/2025</td></tr>
              <tr><td>#DH005</td><td>6,990,000₫</td><td><span class="badge bg-info text-dark">Đang giao</span></td><td>13/01/2025</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-lg-4 mb-4">
      <div class="card p-3">
        <h5>Hành động nhanh</h5>
        <div class="list-group list-group-flush">
          <a href="#" class="list-group-item list-group-item-action"><i class="bi bi-person-plus me-2"></i> Thêm người dùng mới</a>
          <a href="#" class="list-group-item list-group-item-action"><i class="bi bi-box-seam me-2"></i> Thêm sản phẩm mới</a>
          <a href="#" class="list-group-item list-group-item-action"><i class="bi bi-cart me-2"></i> Xem đơn hàng chờ</a>
          <a href="#" class="list-group-item list-group-item-action"><i class="bi bi-bar-chart me-2"></i> Xem báo cáo</a>
        </div>
      </div>
    </div>
  </div>
@endsection
