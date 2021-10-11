<?php

namespace App\Jobs;

use App\Models\Avatar;
use App\Models\User;
use GrahamCampbell\Flysystem\FlysystemManager;
use Symfony\Component\Mercure\Hub;


class ProcessPictureJob extends Job
{
    private $user;
    private $path;

    public function __construct(User $user, $path)
    {
        $this->user = $user;
        $this->path = $path;
    }

    public function handle(FlysystemManager $flysystem, Hub $hub)
    {
        $original_filename_arr = explode('.', $this->path);
        $file_ext = end($original_filename_arr);

        $destination_path = "/users/{$this->user->id}/profile.$file_ext";

        $image = new \Imagick(base_path('public/uploads') . $this->path);

        $w = $image->getImageWidth();
        $h = $image->getImageHeight();

        $new_h = 400;
        $new_w = 400;

        if ($w > $h) {
            $resize_w = $w * $new_h / $h;
            $resize_h = $new_h;
        }
        else {
            $resize_w = $new_w;
            $resize_h = $h * $new_w / $w;
        }

        $image->resizeImage($resize_w, $resize_h, \Imagick::FILTER_LANCZOS, 0.9);
        $image->cropImage($new_w, $new_h, ($resize_w - $new_w) / 2, ($resize_h - $new_h) / 2);
        $image->adaptiveBlurImage(50, 10);

        $flysystem->put($destination_path, (string)$image);

        $this->user->avatars()->delete();

        $avatar = new Avatar();
        $avatar->user_id = $this->user->id;
        $avatar->path = $destination_path;
        $avatar->save();

        $msg = json_encode([
            'event' => 'picture.processed',
            'payload' => $avatar
        ]);
        $topics = [
            "http://example.com/user/{$this->user->id}"
        ];
        $hub->publish(new \Symfony\Component\Mercure\Update($topics, $msg, true));
    }
}
