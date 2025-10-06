<?php

namespace App\Http\Controllers;

use App\Support\MegaMenuBuilder;
use Illuminate\Contracts\View\View;

class MegaMenuController extends Controller
{
    public function __invoke(): View
    {
        $categories = MegaMenuBuilder::build();

        return view('pages.mega-menu-preview', [
            'megaMenuCategories' => $categories,
        ]);
    }
}
