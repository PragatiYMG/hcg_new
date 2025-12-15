<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check if admin is logged in
        if (!session()->get('admin_logged_in')) {
            // Store the current URL to redirect back after login
            $currentUrl = current_url();
            if ($request->getURI()->getQuery()) {
                $currentUrl .= '?' . $request->getURI()->getQuery();
            }

            // Store in session
            session()->set('admin_redirect_url', $currentUrl);

            // Redirect to login
            return redirect()->to(base_url('admin/login'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}