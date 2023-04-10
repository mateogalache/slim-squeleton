<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Controller;

use PDO;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Flash\Messages;
use Slim\Views\Twig;

final class SignInController
{
    private Twig $twig;
    private Messages $flash;

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


        $formErrors['email'] = $this->emailAction();
        $formErrors['password'] = $this->authPassword($password);


        if(!$formErrors['email'] && !$formErrors['password']){
            $formErrors['password'] = $this->comparePassword();
            if (! $formErrors['password']){

                $_SESSION['username'] = substr($_POST['email'], 0, strrpos($_POST['email'], '@'));
                return $this->twig->render(
                    $response,
                    'homepage.twig',
                    [
                        "username" => $_SESSION['username']
                    ]
                );
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

    private function emailAction(): string|null
    {

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

    private function authPassword(string $password): ?string
    {
        if(!preg_match('/^.{7,}$/', $password)){
            return "The password must contain at least 7 characters";
        }

        else if(!preg_match('/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/',$password)){
            return "The password must contain both upper and lower case letters and numbers.";
        }

        else{
            return null;
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
            return "User with this email address does not exist";
        }
        else if (!password_verify($_POST['password'],$user['password'])) {
            return "Your email and/or password are incorrect";
        } else {

            return null;

        }
    }



}