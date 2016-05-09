<?php

namespace App\Http\Controllers\File;

use App\Services\S3Service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\File\UploadFileRequest;

use JWTAuth;

class FileController extends Controller
{

    protected $s3Service;
    protected $user;

    public function __construct(S3Service $s3Service)
    {
        $this->s3Service = $s3Service;
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index(Request $request, $bucket)
    {
        $listResponse = $this->s3Service->listFile($this->user['access_key'], $this->user['secret_key'], $bucket, $request->input('prefix', ''));
        if (!$listResponse) {
            return response()->json(['message' => 'Bucket Error'], 403);
        }
        return response()->json(['files' => $listResponse->get('Contents')], 200);
    }

    public function store(UploadFileRequest $request)
    {
        $uploadResponse = $this->s3Service->uploadFile($this->user['access_key'], $this->user['secret_key'], $request->bucket, $request->file('file')->getPathName(), $request->file('file')->getClientOriginalName(), $request->prefix);
        if (!$uploadResponse) {
            return response()->json(['message' => 'Bucket Error'], 403);
        }
        return response()->json(['message' => 'Upload File Success'], 200);
    }
}