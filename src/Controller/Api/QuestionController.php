<?php

namespace App\Controller\Api;

use App\Annotations\ApiResource;
use App\Entity\Question;
use App\Handler\FormHandler;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Exception\InvalidFormException;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation as ApiDoc;

/**
 * Question controller
 *
 * @SWG\Tag(name="Question")
 * @ApiResource("Question")
 */
class QuestionController extends AbstractFOSRestController
{
    /**
     * @var FormHandler $formHandler
     */
    private $formHandler;

    /**
     * QuestionController constructor.
     *
     * @param FormHandler $formHandler
     */
    public function __construct(FormHandler $formHandler)
    {
        $this->formHandler = $formHandler;
    }

    /**
     * Get question by ID
     *
     * @Rest\Get("/questions/{id}", requirements={"id":"\d+"})
     * @Rest\View(serializerGroups={"question.details", "question.ext.details", "default"})
     *
     * @SWG\Parameter(
     *     name="question",
     *     in="path",
     *     type="integer",
     *     description="Question ID"
     * )
     * @SWG\Response(
     *     response=Response::HTTP_OK,
     *     description="Returns an question",
     *     @ApiDoc\Model(type=Question::class)
     * )
     * @SWG\Response(
     *     response=Response::HTTP_NOT_FOUND,
     *     description="When question not found",
     * )
     *
     * @ApiDoc\Security(name="Bearer")
     *
     * @param Question $question
     * @return View
     */
    public function getQuestionAction(Question $question)
    {
        return $this->view($question, !$question ? Response::HTTP_NOT_FOUND : Response::HTTP_OK);
    }

    /**
     * List all questions
     *
     * @Rest\Get("/questions"), name="get_questions"
     *
     * @Rest\QueryParam(name="active",  map=false,  requirements="\d+", nullable=true,  description="Active questions")
     * @Rest\QueryParam(name="limit",  map=false,  requirements="\d+", nullable=true,   description="Number of questions to return")
     * @Rest\View(serializerGroups={"question.details", "question.ext.details", "default"})
     *
     * @SWG\Parameter(
     *     name="active",
     *     in="query",
     *     type="integer",
     *     enum={0,1},
     *     description="Question active status"
     * )
     * @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     type="integer",
     *     description="The field used to limit results"
     * )
     * @SWG\Response(
     *     response=Response::HTTP_OK,
     *     description="Returns a list of questions",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@ApiDoc\Model(type=Question::class))
     *     )
     * )
     * @SWG\Response(
     *     response=Response::HTTP_NOT_FOUND,
     *     description="When question not found",
     * )
     *
     * @ApiDoc\Security(name="Bearer")
     *
     * @param Request $request
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return View
     * @throws NotFoundHttpException
     */
    public function getQuestionsAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        /** @var array $criteria */
        $criteria = [];

        if (null !== $paramFetcher->get('active')) {
            $criteria['active'] = $paramFetcher->get('active');
        }

        $limit = $paramFetcher->get('limit') ?? null;

        /** @var array $questions */
        $questions = $this->formHandler->getAll($criteria, $limit);

        return $this->view($questions, !$questions ? Response::HTTP_NOT_FOUND : Response::HTTP_OK);
    }

    /**
     * Post question
     *
     * @Rest\Post("/questions"), name="question_create"
     * @Rest\View(serializerGroups={"question.details", "question.ext.details", "default"})
     *
     * @SWG\Parameter(
     * 		name="question",
     * 		in="body",
     * 		required=true,
     * 		@ApiDoc\Model(type=Question::class)
     *	)
     * @SWG\Response(
     *     response=Response::HTTP_CREATED,
     *     description="Returns the created question",
     *     @ApiDoc\Model(type=Question::class)
     * )
     * @SWG\Response(
     *     response=Response::HTTP_BAD_REQUEST,
     *     description="When wrong data is provided"
     * )
     *
     * @ApiDoc\Security(name="Bearer")
     *
     * @param Request $request
     * @return Response|View
     */
    public function postQuestionAction(Request $request)
    {
        try {
            /** @var Question $question */
            $question = $this->formHandler->create(
                $request
            );

            return $this->view($question, Response::HTTP_CREATED);
        } catch (InvalidFormException $exception) {
            $errors = $exception->getErrorMessages($exception->getForm());
            return $this->handleView($this->view($errors, Response::HTTP_BAD_REQUEST));
        }
    }

    /**
     * Put question
     *
     * @Rest\Put("/questions/{id}"), name="question_update"
     * @Rest\View(serializerGroups={"question.details", "question.ext.details", "default"})
     *
     * @SWG\Parameter(
     *     name="question",
     *     in="path",
     *     type="integer",
     *     description="Question ID"
     * )
     * @SWG\Parameter(
     * 		name="question",
     * 		in="body",
     * 		required=true,
     * 		@ApiDoc\Model(type=Question::class)
     *	)
     * @SWG\Response(
     *     response=Response::HTTP_CREATED,
     *     description="Returns the updated question",
     *     @ApiDoc\Model(type=Question::class)
     * )
     * @SWG\Response(
     *     response=Response::HTTP_BAD_REQUEST,
     *     description="When wrong data is provided"
     * )
     *
     * @ApiDoc\Security(name="Bearer")
     *
     * @param Request $request
     * @param Question $question
     * @return Response|View
     */
    public function putQuestionAction(Request $request, Question $question)
    {
        try {
            /** @var Question $question */
            $question = $this->formHandler->update(
                $request,
                $question
            );

            return $this->view($question, Response::HTTP_OK);
        } catch (InvalidFormException $exception) {
            $errors = $exception->getErrorMessages($exception->getForm());
            return $this->handleView($this->view($errors, Response::HTTP_BAD_REQUEST));
        }
    }

    /**
     * Partially update question
     *
     * @Rest\Patch("/questions/{id}"), name="question_update_field"
     * @Rest\View(serializerGroups={"question.details", "question.ext.details", "default"})
     *
     * @SWG\Parameter(
     *     name="question",
     *     in="path",
     *     type="integer",
     *     description="Question ID"
     * )
     * @SWG\Parameter(
     * 		name="question",
     * 		in="body",
     * 		required=true,
     * 		@ApiDoc\Model(type=Question::class)
     *	)
     * @SWG\Response(
     *     response=Response::HTTP_CREATED,
     *     description="Returns the updated question",
     *     @ApiDoc\Model(type=Question::class)
     * )
     * @SWG\Response(
     *     response=Response::HTTP_BAD_REQUEST,
     *     description="When wrong data is provided"
     * )
     *
     * @ApiDoc\Security(name="Bearer")
     *
     * @param Request $request
     * @param Question $question
     * @return Response|View
     */
    public function patchQuestionAction(Request $request, Question $question)
    {
        try {
            /** @var Question $question */
            $question = $this->formHandler->update(
                $request,
                $question
            );

            return $this->view($question, Response::HTTP_OK);
        } catch (InvalidFormException $exception) {
            $errors = $exception->getErrorMessages($exception->getForm());
            return $this->handleView($this->view($errors, Response::HTTP_BAD_REQUEST));
        }
    }

    /**
     * Delete question
     *
     * @Rest\Delete("/questions/{id}"), name="question_delete"
     *
     * @SWG\Parameter(
     *     name="question",
     *     in="path",
     *     type="integer",
     *     description="Question ID"
     * )
     * @SWG\Response(
     *     response=Response::HTTP_NO_CONTENT,
     *     description="Returns an empty message",
     * )
     * @SWG\Response(
     *     response=Response::HTTP_NOT_FOUND,
     *     description="When question not found",
     * )
     *
     * @ApiDoc\Security(name="Bearer")
     *
     * @param Question $question
     * @return Response
     */
    public function deleteQuestionAction(Question $question)
    {
        /** @var Question $question */
        $this->formHandler->delete(
            $question
        );

        return $this->handleView($this->view("Question resource deleted", Response::HTTP_NO_CONTENT));
    }
}
