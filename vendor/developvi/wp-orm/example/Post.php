<?php

class Post extends WPModel
{
    protected $table = 'posts';
    protected $primaryKey = 'ID';

    public function postMeta()
    {
        return $this->hasOne(PostMeta::class, 'post_id', $this->primaryKey);
    }

}