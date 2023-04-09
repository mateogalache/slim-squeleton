<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

final class SignUpController
{
    private Twig $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    public function showSignUp(Request $request, Response $response): Response
    {

        return $this->twig->render(
            $response,
            'sign-up.twig'
        );
    }

    public function uploadSignUp(Request $request, Response $response): Response
    {

        $formErrors['email'] = $this->emailAction();

        $email = $_POST['email'];
        $coins = $_POST['coins'];
        $formData = array (
            'email' => $email,
            'coins' => $coins,
        );


        return $this->twig->render(
            $response,
            'sign-up.twig',
            [
                'formAction' => '/sign-up',
                'formData' => $formData,
                'formErrors' => $formErrors
            ]
        );
    }

    private function emailAction(): string|null
    {
        if (!empty($_POST)){
            $email = $_POST['email'];
            $validation = $this->authEmail($email);
            if ($validation === "correct"){
                return null;
            }
            else{
                if ($validation === "notEmail"){
                    return "The email address is not valid";
                }
                else{
                    return "Only emails from the domain @salle.url.edu are accepted";
                }
            }


        }
        else{
            return "nothing";
        }

    }

    private function authEmail(string $email): string
    {
        $validation = "correct";

        if (!filter_var($email,FILTER_VALIDATE_EMAIL)){
            $validation = "notEmail";
        }

        if (!str_ends_with($email, "@salle.url.edu")){
            $validation = "badSalle";
        }


        return $validation;
    }



}