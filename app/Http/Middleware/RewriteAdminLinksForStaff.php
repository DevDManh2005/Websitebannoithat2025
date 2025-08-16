<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class RewriteAdminLinksForStaff
{
    public function handle(Request $request, Closure $next)
    {
        $res = $next($request);

        $u = $request->user();
        if (!$u || optional($u->role)->name !== 'staff') return $res;
        if ($res instanceof BinaryFileResponse) return $res;

        $ct = $res->headers->get('Content-Type', '');
        if (!Str::contains(strtolower($ct), 'text/html')) return $res;

        $html = $res->getContent();
        if (!$html) return $res;

        $hostEsc = preg_quote($request->getSchemeAndHttpHost(), '/');
        $scheme  = $request->getScheme();
        $host    = $request->getHttpHost();

        // href="/admin/..." hoặc action="/admin/..."
        $html = preg_replace('/\b(href|action)=("|\')\/admin\//i', '$1=$2/staff/', $html);
        // href="http(s)://host/admin/..."
        $html = preg_replace('/\b(href|action)=("|\')https?:\/\/'.$hostEsc.'\/admin\//i',
                             '$1=$2'.$scheme.'://'.$host.'/staff/', $html);

        $res->setContent($html);
        $res->headers->remove('Content-Length');
        return $res;
    }
}
