<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'image'         => $this->image,
            'author'        => $this->author,
            'writer'        => $this->whenLoaded('writer'), // eager loading
            'news_content'  => $this->news_content,
            'created_at'    => date_format($this->created_at, "d/m/Y H:i:s"),
            'comments'      => $this->whenLoaded('comments', function () {
                return collect($this->comments)->map(function ($comment) {
                    $comment->komentator;
                    return $comment;
                });
            }),
            'comment_total' => $this->whenLoaded('comments', function () {
                return $this->comments->count();
            })
        ];
    }

}
