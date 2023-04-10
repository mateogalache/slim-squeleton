<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Controller;

use PDO;
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
        $email = $_POST['email'];
        $coins = $_POST['coins'];
        $password = $_POST['password'];
        $repeatedPassword = $_POST['repeatPassword'];
        $formData = array (
            'email' => $email,
            'coins' => $coins,
        );


        $formErrors['email'] = $this->emailAction();
        $formErrors['password'] = $this->authPassword($password,$repeatedPassword);
        $formErrors['coins'] = $this->authCoins(intval($coins));

        if(!$formErrors['email'] && !$formErrors['password'] && ! $formErrors['coins']){
            $this->insertDataBase();
            return $this->twig->render(
                $response,
                'sign-in.twig'
            );
        }
        else{
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

    private function authPassword(string $password, string $repeatedPassword): ?string
    {
        if(!preg_match('/^.{7,}$/', $password)){
            return "The password must contain at least 7 characters";
        }

        else if(!preg_match('/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/',$password)){
            return "The password must contain both upper and lower case letters and numbers.";
        }

        else if($password !== $repeatedPassword){
            return "Passwords do not match";
        }
        else{
            return null;
        }
    }

    private function authCoins(int $coins): ?string
    {
        if($coins != floor($coins)){
            return "The number of LSCoins is not a valid number";
        }
        else if ($coins < 50 || $coins > 30000){
            return "Sorry, the number of LSCoins is either below or above the limits";
        }
        else{
            return null;
        }
    }

    private function connectDb(): PDO
    {
        return new PDO('mysql:host=db:3306;dbname=lscoins','root','admin');
    }

    private function insertDataBase(): void
    {
        $hashed_password = password_hash($_POST['password'],PASSWORD_DEFAULT);
        $date = date("Y-m-d H:i:s");
        $coins = $_POST['coins'];
        $email = $_POST['email'];


        $connection = $this->connectDb();
        $statement = $connection->prepare("INSERT INTO users(email,password,coins,createdAt,updatedAt) values (:email,:password,:coins,:createdAt,:updatedAt)");
        $statement->bindParam('email',$email,PDO::PARAM_STR);
        $statement->bindParam('password',$hashed_password,PDO::PARAM_STR);
        $statement->bindParam('coins',$coins,PDO::PARAM_STR);
        $statement->bindParam('createdAt',$date,PDO::PARAM_STR);
        $statement->bindParam('updatedAt',$date,PDO::PARAM_STR);
        $statement->execute();
    }



}