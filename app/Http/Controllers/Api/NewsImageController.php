<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponder;
use App\News;
use App\NewsImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsImageController extends Controller
{
    use ApiResponder;

    /**
     * Deletes the chosen news image.
     *
     * @param News $news
     * @param NewsImage $news_image
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(News $news, NewsImage $news_image)
    {
        Storage::delete($news_image->src);
        Storage::delete($news_image->thumb_src);
        $news_image->delete();

        return $this->apiRespondSingle($news_image);
    }
}
