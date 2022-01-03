<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Access\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ClientPostRequest;

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
    public function store(ClientPostRequest $request)
    {
        $data = $request->all();
        // Obtem o Id do usuário logado
        $user_logado = auth()->user()->id;

        try {
            // Verifica se informou o arquivo e se é válido
            if ($request->hasFile('image_url') && $request->file('image_url')->isValid()) {
                // Define um aleatório para o arquivo baseado no timestamps atual
                $fileName = uniqid(date('HisYmd'));
                // Recupera a extensão do arquivo
                $extension = $request->image_url->extension();
                // Define o nome do diretório para salvar o arquivo em: (public/upload/user_id/client_id)
                $diretorio = "public/upload/clients";
                // Define o diretório onde as pastas ficarão
                $uploadfolder = $diretorio;
                // Verifica os tipos suportados
                if ($extension == 'jpg') {
                    $uploadfolder = $diretorio . '/images';
                } elseif ($extension == 'jpeg' || $extension == 'png') {
                    $uploadfolder = $diretorio . '/imagesProfile';
                } else {
                    return "Arquivo não suportado!";
                }
                // Define finalmente o nome
                $nameFile = "{$fileName}.{$extension}";
                // Faz o upload e envia o arquivo para a pasta determinada dentro de storage:
                $upload = $request->image_url->storeAs($uploadfolder, $nameFile);
                // Pega o caminho completo do arquivo e tranforma em link
                $url = Storage::url($upload);

                // Verifica se não deu certo o upload (Redireciona de volta)
                if (!$upload)
                    return redirect()
                        ->back()
                        ->with('error', 'Falha ao fazer upload')
                        ->withInput();
            }
            // Armazena o usuario logado na variável
            $dados = User::find($user_logado);
            // Grava o Id do client para o usuário na tabela de users
            $dados->clients()->attach($this->client)->save();

            Client::create($data);

            return response()->json(!isset($url) ?
                ['data', $data] :
                ['data' => $data, 'url' => $url]);
        } catch (\Exception $e) {
            return response()->json(['data' => [
                'status' => 500,
                'msg' => 'Usuário não cadastrado',
                'method' => $e
            ]]);
        }
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
        $dataRequest = $request->all();

        try {
            $data = Client::findOrFail($id);
            $data->update($dataRequest);

            return response()->json(['data' => [
                'status' => 200,
                'msg' => 'Usuário atualizado com sucesso',
                'data' => $data
            ]]);
        } catch (\Exception $e) {
            return response()->json(['data' => [
                'status' => 500,
                'msg' => 'Usuário não atualizado, tente novamente',
                'method' => $e
            ]]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Client::find($id);

        try {
            $data->delete();

            return response()->json(['data' => [
                'status' => 200,
                'msg' => 'Usuário deletado com sucesso',
                'data' => $data
            ]]);
        } catch (\Exception $e) {
            return response()->json(['data' => [
                'status' => 500,
                'msg' => 'Usuário não deletado, tente novamente',
                'method' => $e
            ]]);
        }
    }
}
