<?php
  
namespace App\Http\Controllers;
  
use App\Produto;
use App\TipoProduto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdutoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "Produto";
        $Produto = produto::latest()->paginate(5);
  
        return view('produtos.index',compact('Produto'),compact("title"))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }
   
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = "Produto";
        return view('produtos.create',compact("title"));
    }
  
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $title = "Produto";
        $request->validate([
            'nome' => 'required',
            'descricao' => 'required',
            'preco' => 'required',
            'tipoProdutoId' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        ]);
        $imageName = time().'.'.request()->image->getClientOriginalExtension();
        request()->image->move(public_path('ImagensProdutos'), $imageName);        
        $preco = substr(trim(str_replace(",",".", $request->input('preco')),"R$"), 1);
        $request->merge(['preco' => $preco]);    

        $requestData = $request->all();
        $requestData['image'] = "/ImagensProdutos"."/".$imageName;
        produto::create($requestData);
   
        return redirect()->route('produtos.index',compact("title"))
                        ->with('success','Produto criado com sucesso.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Produto  $product
     * @return \Illuminate\Http\Response
     */
    public function show(produto $produto)
    {
        $title = "Produto";
        return view('produtos.show',compact('produto'),compact("title"));
    }
   
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(produto $produto)
    {
        $title = "Produto";
        return view('produtos.edit',compact('produto'),compact("title"));
    }
  
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, produto $produto)
    {
        $title = "Produto";
        $request->validate([
            'nome' => 'required',
            'descricao' => 'required',
            'preco' => 'required',
            'tipoProdutoId' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        ]);
        if(Storage::disk('public')->exists($request->image)){
            Storage::delete($request->image);
        }
        $imageName = time().'.'.request()->image->getClientOriginalExtension();
        request()->image->move(public_path('ImagensProdutos'), $imageName);        
        $preco = str_replace(" ", "", trim(str_replace(",",".", $request->input('preco')),"R$"));
        $request->merge(['preco' => $preco]); 

        $requestData = $request->all();
        $requestData['image'] = "/ImagensProdutos"."/".$imageName;
        $produto->update($requestData);
  
        return redirect()->route('produtos.index',compact("title"))
                        ->with('success','Alterações realizadas com sucesso');
    }
  
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Produto $produto)
    {
        $title = "Produto";
        $produto->delete();
  
        return redirect()->route('produtos.index',compact("title"))
                        ->with('success','Produto apagado com sucesso');
    }
}