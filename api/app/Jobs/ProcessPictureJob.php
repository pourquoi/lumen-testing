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

        $flysystem->put($destination_path, file_get_contents(base_path('public/uploads') . $this->path));

        $this->user->avatars()->delete();

        $avatar = new Avatar();
        $avatar->user_id = $this->user->id;
        $avatar->path = $destination_path;
        $avatar->save();

        $msg = 'picture uploaded';
        $topics = [
            "http://example.com/user/{$this->user->id}"
        ];
        $hub->publish(new \Symfony\Component\Mercure\Update($topics, $msg, true));
    }
}
