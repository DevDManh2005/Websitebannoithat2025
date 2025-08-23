<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
class PageController extends Controller
{
    /**
     * Hiển thị trang giới thiệu.
     */
    public function about()
    {
        // Trả về view 'pages.about'
        // Laravel sẽ tự động tìm file tại: resources/views/pages/about.blade.php
        return view('pages.about');
    }

     public function contact()
    {
        return view('pages.contact');
    }

     public function terms(): View
    {
        // Truyền tiêu đề cho trang
        $title = 'Điều khoản & Dịch vụ';

        // Trả về view 'pages.terms' cùng với biến title
        return view('pages.terms', compact('title'));
    }

     public function warranty(): View
    {
        $title = 'Chính sách Bảo hành';
        return view('pages.warranty', compact('title'));
    }

    public function shippingReturns(): View
{
    $title = 'Chính sách Giao hàng & Đổi trả';
    return view('pages.shipping_returns', compact('title'));
}

public function faq(): View
{
    $title = 'Câu hỏi thường gặp (FAQ)';
    return view('pages.faq', compact('title'));
}
}