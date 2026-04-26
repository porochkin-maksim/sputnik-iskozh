<?php declare(strict_types=1);

namespace Core\App\News;

use Core\Domains\News\NewsCategoryEnum;
use Core\Domains\News\NewsEntity;
use Core\Domains\News\NewsFactory;
use Core\Domains\News\NewsService;
use Core\Exceptions\ValidationException;

readonly class SaveCommand
{
    public function __construct(
        private NewsService $newsService,
        private NewsFactory $newsFactory,
        private SaveValidator $validator,
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function execute(
        ?int    $id,
        string  $title,
        ?string $description,
        ?string $article,
        ?int    $category,
        bool    $isLock,
        ?string $publishedAt,
    ): NewsEntity
    {
        $this->validator->validate($title, $description, $category);

        $news = $this->newsFactory->makeDefault()
            ->setId($id)
            ->setTitle($title)
            ->setDescription($description)
            ->setArticle($article)
            ->setCategory(NewsCategoryEnum::from($category))
            ->setIsLock($isLock)
            ->setPublishedAt($publishedAt);

        return $this->newsService->save($news);
    }
}
