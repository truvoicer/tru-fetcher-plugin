<?php
namespace TruFetcher\Includes\Api\Response;

class Tru_Fetcher_Api_Post_Response extends Tru_Fetcher_Api_Response
{

    public ?\WP_Post $post;
    public ?\WP_Post $postTemplate;

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
    public function getPostTemplate(): ?\WP_Post
    {
        return $this->postTemplate;
    }

    /**
     * @param \WP_Post|null $postTemplate
     */
    public function setPostTemplate(?\WP_Post $postTemplate): void
    {
        $this->postTemplate = $postTemplate;
    }

}
