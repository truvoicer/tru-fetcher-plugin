<?php
namespace TruFetcher\Includes\Api\Response;

class Tru_Fetcher_Api_Post_Response extends Tru_Fetcher_Api_Response
{
    public array $keymap = [];
    public ?\WP_Post $post;
    public ?\WP_Post $template;
    public ?array $navigation;

    public function getKeymap(): array
    {
        return $this->keymap;
    }

    public function setKeymap(array $keymap): void
    {
        $this->keymap = $keymap;
    }

    /**
     * @return \WP_Post|null
     */
    public function getPost(): ?\WP_Post
    {
        return $this->post;
    }

    /**
     * @param \WP_Post|null $post
     */
    public function setPost(?\WP_Post $post): void
    {
        $this->post = $post;
    }

    /**
     * @return \WP_Post|null
     */
    public function getTemplate(): ?\WP_Post
    {
        return $this->template;
    }

    /**
     * @param \WP_Post|null $template
     */
    public function setTemplate(?\WP_Post $template): void
    {
        $this->template = $template;
    }

    public function getNavigation(): ?array
    {
        return $this->navigation;
    }

    public function setNavigation(?array $navigation): void
    {
        $this->navigation = $navigation;
    }

}
