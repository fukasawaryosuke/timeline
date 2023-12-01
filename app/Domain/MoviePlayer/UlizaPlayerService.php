<?php

namespace App\Domain\MoviePlayer;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Domain\MoviePlayer\IMoviePlayerService;

class UlizaPlayerService implements IMoviePlayerService
{
    /**
     * Uliza Player用のJavascriptソースを取得
     *
     * @param Request $request
     * @return void
     */
    public function get(string $contentName)
    {
        // uliza playerのjavascriptを取得
        $host = 'https://player-api.p.uliza.jp/v1/players';
        $playerType = '1'; // 1: デフォルト, 2: 自動再生
        $playerId = config('app.uliza_player_id') . $playerType;
        // NOTE: $playerObjectNameを変更する際はglobal.d.tsのフィールド名も変更すること
        $playerObjectName = 'ulizaPlayer';
        $url = "{$host}/{$playerId}/hdc/admin?type=normal&name={$contentName}&playerobjectname={$playerObjectName}&targetid=videoArea";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->getUlizaToken(),
        ]);
        $res = curl_exec($ch);
        curl_close($ch);
        return response($res)->header('Content-Type', 'application/javascript');
    }
    // コンテンツの公開状況取得
    public function getStatus(string $contentName)
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'https://vms-api.p.uliza.jp',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getUlizaToken(),
            ],
        ]);
        $result = $client->get("/v1/contents/{$contentName}")->getBody();
        return json_decode($result);
    }
    public function upload(Request $request)
    {
        $inputFilePath = '';
        $contentName = (string) Str::uuid();
        $file = request()->file('file');
        $contentTile = $file->getClientOriginalName();
        $fileName = $contentTile;
        // TODO: ひらがな、カタカナ、漢字が文字列に含まれる場合はtimestampに置き換え encode errorでも起こすのか?
        if (preg_match('/[ぁ-ん]+|[ァ-ヴー]+|[一-龠]+|()/u', $fileName)) {
            $fileName = date('Ymd_His') . '.mp4';
        }
        // 事前にulizaのftpサーバーにファイルをアップロード
        try {
            $inputFilePath = Storage::disk('ulizaftp')->putFileAs(
                '',
                $file,
                $fileName
            );
        } catch (Exception $e) {
            if (strpos($e, 'Forbidden filename') !== false) {
                return [
                    'result' => false,
                    'error_message' =>
                        'ファイル名に保存出来ない文字が含まれています',
                ];
            }
            return ['result' => false, 'error_message' => $e->getMessage()];
        }
        // ulizaのタスク登録
        $ch = curl_init('https://encluster-api.p.uliza.jp/v1/tasks');
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->getUlizaToken(),
                'Content-Type: application/json',
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode([
                'input_filepath' => $inputFilePath,
                'profile_id' => config('app.uliza_profile_id'),
                'upload_type' => 'external',
                'vms_config' => [
                    'content' => [
                        'name' => $contentName,
                        'title' => $contentTile,
                    ],
                    'config_overrides' => [
                        'category_id' => config('app.uliza_category_id'),
                    ],
                ],
            ]),
            CURLOPT_RETURNTRANSFER => true,
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response);
    }

    public function delete(string $contentName)
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'https://vms-api.p.uliza.jp',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getUlizaToken(),
            ],
        ]);
        $urlPath = "/v1/contents/{$contentName}";
        $result = $client->delete($urlPath);
        return $result->getStatusCode();
    }

    public function getSream(Request $request)
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'https://vms-api.p.uliza.jp',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getUlizaToken(),
            ],
        ]);
        $contentName = $request->input('contentName');
        $contentName = '83f99fdb-bcf8-4321-912e-dab57bc4d3b0';
        // $streamId = $request->input('streamId');
        $streamId = 'c680732f-5d57-47ac-a838-a93b91524b27';
        $path = "/v1/contents/{$contentName}/streams/{$streamId}";
        $response = $client->get($path);
        return $response->getBody();
    }

    public function uploadStream(Request $request)
    {
        $contentName = (string) Str::uuid();
        $file = request()->file('file');
        $contentTile = $file->getClientOriginalName();
        $fileName = $contentTile;
        $path = "/v1/contents/{$contentName}/streams";
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'https://vms-api.p.uliza.jp',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getUlizaToken(),
            ],
        ]);
        // TODO: ひらがな、カタカナ、漢字が文字列に含まれる場合はtimestampに置き換え encode errorでも起こすのか?
        if (preg_match('/[ぁ-ん]+|[ァ-ヴー]+|[一-龠]+|()/u', $fileName)) {
            $fileName = date('Ymd_His') . '.mp4';
        }
        $response = $client->post($path, [
            'json' => [
                'path' => 'VOD/hls/9999/prog-index.m3u8',
                'format' => 'HLS',
                'video_codec' => 'H.264',
                'video_bitrate' => 1500000,
                'framerate' => 30,
                'width' => 1920,
                'height' => 1080,
                'audio_codec' => 'AAC',
                'audio_bitrate' => 192000,
                'sample_rate' => 44100,
                'channels' => 2,
                'audio_track_type' => null,
                'audio_language' => 'ja',
                'audio_label' => '日本語',
                'playback_rate' => 1,
                'filesize' => 18244942,
                'is_original' => false,
            ],
        ]);
        return $response->getBody();
    }
    /** private */

    /**
     * uliza用リクエストヘッダーAuthorizationに設定するトークン取得
     *
     * @return string uliza用トークン
     */
    private function getUlizaToken(): string
    {
        return config('app.uliza_auth_key');
    }
}
