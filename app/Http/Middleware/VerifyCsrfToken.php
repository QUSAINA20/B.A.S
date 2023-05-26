<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [

        'http://127.0.0.1:8000/api/logout/*',
        'http://127.0.0.1:8000/api/upload/*',
        'http://127.0.0.1:8000/api/files/*',
        'http://127.0.0.1:8000/api/soft-delete-files/*',
        'http://127.0.0.1:8000/api/files/restore/*',
        'http://127.0.0.1:8000/api/move-files-to-folder/*',
        'http://127.0.0.1:8000/api/total-file-size/*',
        'http://127.0.0.1:8000/api/empty-trash/*',
        'http://127.0.0.1:8000/api/folders',
        'http://127.0.0.1:8000/api/folders/*',
        'http://127.0.0.1:8000/api/folder/*/edit',
        'http://127.0.0.1:8000/api/folders/delete',
        'http://127.0.0.1:8000/api/folders/*/files',
        'http://127.0.0.1:8000/api/admin/logout/*',
        'http://127.0.0.1:8000/api/admin/register',
        'http://127.0.0.1:8000/api/admin/user/*/upload',
        'http://127.0.0.1:8000/api/admin/user/*/files',
        'http://127.0.0.1:8000/api/admin/messages',
        'http://127.0.0.1:8000/api/admin/messages/*',
        'http://127.0.0.1:8000/api/store-subscribers',
        'http://127.0.0.1:8000/api/save-message',
    ];
}
