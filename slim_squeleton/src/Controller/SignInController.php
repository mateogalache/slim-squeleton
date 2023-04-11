<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Controller;

use SallePW\SlimApp\Controller\InputsValidationsController;
use PDO;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Flash\Messages;
use Slim\Views\Twig;

final class SignInController
{
    private Twig $twig;
    private Messages $flash;
    private InputsValidationsController $validationsController;

    public function __construct(Twig $twig, Messages $flash)
    {
        $this->twig = $twig;
        $this->flash = $flash;
    }

    public function showSignIn(Request $request, Response $response): Response
    {

        $messages = $this->flash->getMessages();

        $notifications = $messages['notifications'] ?? [];


        return $this->twig->render(
            $response,
            'sign-in.twig',
            [
                "notifs" => $notifications
            ]
        );
    }

    public function uploadSignIn(Request $request, Response $response): Response
    {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $formData = array (
            'email' => $email,
        );

        $validationsController = new InputsValidationsController();

        $formErrors['email'] = $validationsController->emailAction();
        $formErrors['password'] = $validationsController->authPassword($password);


        if(!$formErrors['email'] && !$formErrors['password']){
            if (!$this->comparePassword()){
                $_SESSION['username'] = substr($_POST['email'], 0, strrpos($_POST['email'], '@'));
                return $response->withHeader('Location', '/')->withStatus(302);
            }
            else{
                if($this->comparePassword() === "Your email and/or password are incorrect."){
                    $formErrors['password'] = $this->comparePassword();
                }
                else{
                    $formErrors['email'] = $this->comparePassword();
                }

                return $this->twig->render(
                    $response,
                    'sign-in.twig',
                    [
                        'formAction' => '/sign-in',
                        'formData' => $formData,
                        'formErrors' => $formErrors
                    ]
                );
            }
        }
        else{
            return $this->twig->render(
                $response,
                'sign-in.twig',
                [
                    'formAction' => '/sign-in',
                    'formData' => $formData,
                    'formErrors' => $formErrors
                ]
            );
        }

    }





    private function connectDb(): PDO
    {
        return new PDO('mysql:host=db:3306;dbname=lscoins','root','admin');
    }

    private function comparePassword(): ?string
    {

        $connection = $this->connectDb();
        $statement = $connection->prepare("SELECT * FROM users WHERE email =?");
        $statement->execute([$_POST['email']]);
        $user = $statement->fetch(PDO::FETCH_ASSOC);


        if (!$user) {
            return "User with this email address does not exist.";
        }
        else if (!password_verify($_POST['password'],$user['password'])) {
            return "Your email and/or password are incorrect.";
        } else {

            return null;

        }
    }



}