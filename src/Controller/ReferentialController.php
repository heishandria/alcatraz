<?php

namespace App\Controller;

use App\Utils\StringUtil;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use App\Entity\Referential;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation as ApiDoc;

/**
 * QuestionFormat controller
 *
 * @SWG\Tag(name="Referentiel")
 */
class ReferentialController extends AbstractFOSRestController
{
    const ENTITY_PATH = 'App\Entity\\';

    /**
     * Get referential by type
     *
     * @Rest\Get("/referentials/{type}")
     *
     * @SWG\Parameter(
     *     name="referential type",
     *     in="path",
     *     type="integer",
     *     description="Referential type"
     * )
     * @SWG\Response(
     *     response=Response::HTTP_OK,
     *     description="Returns an referential",
     *     @ApiDoc\Model(type=Referential::class)
     * )
     * @SWG\Response(
     *     response=Response::HTTP_NOT_FOUND,
     *     description="When referential not found",
     * )
     *
     * @param Request $request
     * @param string|null $type
     * @param StringUtil $stringUtil
     * @return View
     */
    public function getReferentialAction(Request $request, ?string $type, StringUtil $stringUtil)
    {
        /** @var String $type */
        $type = $stringUtil->convertSnakeToCamel($type);

        if (!class_exists(self::ENTITY_PATH."$type")) {
            return $this->view([], Response::HTTP_NOT_FOUND );
        }

        /** @var array $referential */
        $referential = $this->getDoctrine()->getRepository(self::ENTITY_PATH."$type")->findAll();
        return $this->view($referential, !$referential ? Response::HTTP_NOT_FOUND : Response::HTTP_OK);
    }
}
