<?php

namespace App\Http\Controllers;

use App\Models\Price;
use App\Models\Package;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function home(Request $request)
    {

        // Get featured prices for the slider
        $featuredPrices = Price::active()
            ->whereNotNull('icon_class')
            ->whereNotNull('description')
            ->take(8)
            ->get();

        // Get all prices grouped by category for the full price table
        $pricesByCategory = Price::active()
            ->orderBy('category')
            ->orderBy('item_name')
            ->get()
            ->groupBy('category');

        // Get popular items for the first tab
        $popularItems = Price::active()
            ->whereIn('item_name', [
                'Shirts',
                'Women Blouse',
                'Gowns (long)',
                'Trousers / Joggers',
                'T-shirts / Polo',
                'Suits',
                'Sweater / Sweater Shirt',
                'Blazer',
                'Women Gown (Native)',
                'Jumpsuit'
            ])
            ->get();

        $prices = Price::active()
            ->whereNotNull('icon_class')
            ->whereNotNull('description')
            ->take(8)
            ->get();

        $packages = Package::active()->ordered()->get();

        return view('home', compact('featuredPrices', 'pricesByCategory', 'popularItems', 'prices', 'packages'));
    }
    public function about(Request $request)
    {
        return view('about');
    }
    public function services(Request $request)
    {
        return view('services');
    }
    public function prices(Request $request)
    {
        $prices = Price::active()
            ->whereNotNull('icon_class')
            ->whereNotNull('description')
            ->take(8)
            ->get();

        $packages = Package::active()->ordered()->get();

        return view('prices', compact('prices', 'packages'));
    }
    public function faq(Request $request)
    {
        return view('faq');
    }
    public function contact(Request $request)
    {
        return view('contact');
    }
    public function terms(Request $request)
    {
        return view('terms');
    }
    public function policy(Request $request)
    {
        return view('policy');
    }
}
