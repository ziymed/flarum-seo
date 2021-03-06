<?php
/**
 * Created by Jasper Vriends
 * www.vriends.co - GitHub @jaspervriends
 */
namespace JasperVriends\FlarumSeo\Managers;

use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\Discussion\DiscussionRepository;
use Flarum\Tags\TagRepository;

use JasperVriends\FlarumSeo\Listeners\PageListener;


/**
 * Class ProfileManager
 * @package JasperVriends\FlarumSeo\Managers
 */
class Tag
{
    // Parent and Tag Repository
    protected $parent;
    protected $tagRepository;

    // Current tag
    protected $tag;

    /**
     * Discussion constructor.
     * @param PageListener $parent
     * @param $tag
     */
    public function __construct(PageListener $parent, $tag)
    {
        $this->parent = $parent;
        $this->tagRepository = new TagRepository();

        // I do support it, but it didn't work
        if(!is_numeric($tag))
        {
            $tag = $this->tagRepository->getIdForSlug($tag);
        }

        // Find tag
        $this->tag = $this->tagRepository->findOrFail($tag);

        // Create tags
        $this->createTags();
    }

    /**
     * Create tags
     */
    private function createTags()
    {
        if(!method_exists($this->tag, "getAttribute")) return;

        $lastPostedAt = (new \DateTime($this->tag->getAttribute('last_posted_at')))->format("c");

        $this->parent
            // Add Schema.org metadata: CollectionPage https://schema.org/CollectionPage
            ->setSchemaJson('@type', 'CollectionPage')
            ->setSchemaJson('about', $this->tag->getAttribute('description'))
            ->setUpdatedOn($lastPostedAt)

            // Tag URL
            ->setUrl('/t/' . $this->tag->getAttribute('slug'))

            // Description
            ->setDescription($this->tag->getAttribute('description'))

            // Canonical url
            ->setCanonicalUrl('/t/' . $this->tag->getAttribute('slug'));
    }
}