<?php declare(strict_types=1);

namespace Core\Domains\Poll;

use Core\Domains\Poll\Factories\AnswerFactory;
use Core\Domains\Poll\Factories\PollFactory;
use Core\Domains\Poll\Factories\QuestionFactory;
use Core\Domains\Poll\Repositories\AnswerRepository;
use Core\Domains\Poll\Repositories\PollRepository;
use Core\Domains\Poll\Repositories\QuestionRepository;
use Core\Domains\Poll\Services\AnswerService;
use Core\Domains\Poll\Services\PollService;
use Core\Domains\Poll\Services\QuestionService;

class PollLocator
{
    private static PollService        $pollService;
    private static PollFactory        $pollFactory;
    private static PollRepository     $pollRepository;
    private static AnswerService      $answerService;
    private static AnswerFactory      $answerFactory;
    private static AnswerRepository   $answerRepository;
    private static QuestionService    $questionService;
    private static QuestionFactory    $questionFactory;
    private static QuestionRepository $questionRepository;

    public static function PollService(): PollService
    {
        if ( ! isset(self::$pollService)) {
            self::$pollService = new PollService(
                self::PollFactory(),
                self::PollRepository(),
            );
        }

        return self::$pollService;
    }

    public static function PollRepository(): PollRepository
    {
        if ( ! isset(self::$pollRepository)) {
            self::$pollRepository = new PollRepository();
        }

        return self::$pollRepository;
    }

    public static function PollFactory(): PollFactory
    {
        if ( ! isset(self::$pollFactory)) {
            self::$pollFactory = new PollFactory();
        }

        return self::$pollFactory;
    }

    public static function AnswerService(): AnswerService
    {
        if ( ! isset(self::$answerService)) {
            self::$answerService = new AnswerService(
                self::AnswerFactory(),
                self::AnswerRepository(),
            );
        }

        return self::$answerService;
    }

    public static function AnswerRepository(): AnswerRepository
    {
        if ( ! isset(self::$answerRepository)) {
            self::$answerRepository = new AnswerRepository();
        }

        return self::$answerRepository;
    }

    public static function AnswerFactory(): AnswerFactory
    {
        if ( ! isset(self::$answerFactory)) {
            self::$answerFactory = new AnswerFactory();
        }

        return self::$answerFactory;
    }

    public static function QuestionService(): QuestionService
    {
        if ( ! isset(self::$questionService)) {
            self::$questionService = new QuestionService(
                self::QuestionFactory(),
                self::QuestionRepository(),
            );
        }

        return self::$questionService;
    }

    public static function QuestionRepository(): QuestionRepository
    {
        if ( ! isset(self::$questionRepository)) {
            self::$questionRepository = new QuestionRepository();
        }

        return self::$questionRepository;
    }

    public static function QuestionFactory(): QuestionFactory
    {
        if ( ! isset(self::$questionFactory)) {
            self::$questionFactory = new QuestionFactory();
        }

        return self::$questionFactory;
    }
}
