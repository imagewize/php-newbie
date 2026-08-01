<?php

class HomeController {
    private User $user;
    private Post $post;

    public function __construct() {
        $this->user = new User();
        $this->post = new Post();
    }

    public function index(): array {
        $currentUser = $this->user->getCurrentUser();
        $posts = $this->post->getPublished();
        $allUsers = $this->user->getAll();

        return [
            'action' => 'home',
            'currentUser' => $currentUser,
            'posts' => $posts,
            'allUsers' => $allUsers
        ];
    }
}
