<?php

namespace App\Controller\Security;

use App\Entity\User;
use App\Exception\InvalidFormException;
use App\Form\RegistrationType;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation as ApiDoc;
use FOS\RestBundle\View\View;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * AuthController controller
 *
 * @SWG\Tag(name="User")
 */
class AuthController extends AbstractFOSRestController
{
    private $userManager;

    /**
     * AuthController constructor.
     * @param UserManagerInterface $userManager
     */
    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * Register new users.
     *
     * This endpoint register new users.
     *
     * @Rest\Post("/auth/register")
     * @SWG\Response(
     *     response=200,
     *     description="Returned when the register is successful",
     *     @SWG\Schema(
     *         type="array",
     *         @ApiDoc\Model(type=Referential::class)
     *     )
     * ),
     * @SWG\Response(
     *     response="401",
     *     description="Returned when the user has not provided his credentials correctly."
     * )
     * @SWG\Parameter(
     *     name="Content-Type",
     *     in="header",
     *     type="string",
     *     description="Content Type",
     *     required=true
     * ),
     * @SWG\Parameter(
     *     name="Register data",
     *     in="body",
     *     type="string",
     *     description="Register user data",
     *     required=true,
     *     @SWG\Schema(type="object",
     *          @SWG\Property(property="username", description="Username", type="string"),
     *          @SWG\Property(property="password", description="Password", type="string"),
     *          @SWG\Property(property="email", description="Email", type="string"),
     *          required={
     *               "username",
     *               "password",
     *               "email"
     *          }
     *     ),
     * )
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return View|Response
     */
    public function registerAction(Request $request, ValidatorInterface $validator, UserPasswordEncoderInterface $passwordEncoder): ?View
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $data = json_decode(
            $request->getContent(),
            true
        );

        $form->submit($data);

        if ($form->isValid()) {
            $user = $form->getData();

            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user
                ->addRole('ROLE_USER')
                ->setEnabled(true);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->view($user, HttpResponse::HTTP_CREATED);
        }

        throw new InvalidFormException(500, 'Invalid data', $form);
    }
}
