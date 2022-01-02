<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClientsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Client::all();

        return response()->json(['data', $data]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        try {
            // Verifica se informou o arquivo e se é válido
            if ($request->hasFile('image_url') && $request->file('image_url')->isValid()) {
                // Define um aleatório para o arquivo baseado no timestamps atual
                $name = uniqid(date('HisYmd'));
                // Recupera a extensão do arquivo
                $extension = $request->image_url->extension();
                // Define o nome do diretório para salvar o arquivo em: (public/upload/user_id/client_id)
                $diretorio = "public/upload/clients";
                // Define o diretório onde as pastas ficarão
                $uploadfolder = $diretorio;
                if ($extension == 'jpg') {
                    $uploadfolder = $diretorio . '/images';
                } elseif ($extension == 'jpeg' || $extension == 'png') {
                    $uploadfolder = $diretorio . '/imagesProfile';
                } else {
                    return "Arquivo não suportado!";
                }
                // Define finalmente o nome
                $nameFile = "{$name}.{$extension}";
                // Faz o upload e envia o arquivo para a pasta determinada dentro de storage:
                $upload = $request->image_url->storeAs($uploadfolder, $nameFile);
                // Pega o caminho completo do arquivo e tranforma em link
                $url = Storage::url($upload);

                // Verifica se NÃO deu certo o upload (Redireciona de volta)
                if (!$upload)
                    return redirect()
                        ->back()
                        ->with('error', 'Falha ao fazer upload')
                        ->withInput();
            }

            Client::create();

            return response()->json(!isset($url) ? ['data', $data] : ['data' => $data, 'url' => $url]);
        } catch (\Exception $e) {
            return response()->json(['data' => ['status' => 500, 'msg' => 'usuário não cadastrado', 'method' => $e]]);
        }
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
