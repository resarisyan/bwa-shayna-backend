<?php

namespace App\Http\Controllers;

use App\Exports\ProductExport;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Requests\ProductRequest;
use App\Imports\ProductImport;
use App\Models\ProductGallery;
use Illuminate\Support\Str;
use PDF;
use Excel;

use function Ramsey\Uuid\v1;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = Product::all();
        return view('pages.products.index')->with([
            'items' => $items
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.products.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        $data = $request->all();
        $data['slug'] = Str::slug($request->name);
        Product::create($data);
        return redirect()->route('products.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = Product::findOrFail($id);
        return view('pages.products.edit')->with([
            'item' => $item
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, $id)
    {
        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        $item = Product::findOrFail($id);
        $item->update($data);
        return redirect()->route('products.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = Product::findOrFail($id);
        $item->delete();
        ProductGallery::where('products_id', $id)->delete();
        return redirect()->route('products.index');
    }

    public function gallery(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $items = ProductGallery::with('product')
            ->where('products_id', $id)
            ->get();
        return view('pages.products.gallery')->with([
            'product' => $product,
            'items' => $items
        ]);
    }

    public function print_product()
    {
        $data['products'] = Product::all();
        $pdf = PDF::loadView('pages.products.pdf', $data);
        return $pdf->download('products.pdf');
    }

    public function export_product()
    {
        return Excel::download(new ProductExport, 'product.xlsx');
    }

    public function import_product(Request $request)
    {
        // dd($request->file('file'));
        return Excel::import(new ProductImport, $request->file('file'));
    }
}
