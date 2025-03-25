<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use AshAllenDesign\ShortURL\Classes\Builder;
use App\Models\ShortenedUrl;

class ShortenedUrlController extends Controller
{
  /**
   * Redirects to the original URL associated with the given shortcode.
   *
   * @param  string  $shortCode
   * @return \Illuminate\Http\RedirectResponse
   *
   * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   */
  public function redirectToOriginalUrl($shortCode)
  {
    $shortenedUrl = ShortenedUrl::where('short_code', $shortCode)->first();

    if (!$shortenedUrl) {
      abort(404); // Short code not found
    }
    \Log::info('Original URL: ' . $shortenedUrl->original_url);
    return redirect($shortenedUrl->original_url);
  }
}
