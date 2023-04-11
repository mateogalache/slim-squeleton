<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Controller;

use SallePW\SlimApp\Controller\InputsValidationsController;
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

        $validationsController = new InputsValidationsController();

        $formErrors['email'] = $validationsController->emailAction();
        $formErrors['password'] = $validationsController->authPasswordUp($password,$repeatedPassword);
        $formErrors['coins'] = $this->authCoins();

        if(!$formErrors['email'] && !$formErrors['password'] && (!$formErrors['coins'] || !$_POST['coins'])){
            $this->insertDataBase();
            return $response->withHeader('Location', '/sign-in')->withStatus(302);
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



    private function authCoins(): ?string
    {
        if(!filter_var($_POST['coins'], FILTER_VALIDATE_INT)){
            return "The number of LSCoins is not a valid number.";
        }
        else if ($_POST['coins'] < 50 || $_POST['coins'] > 30000){
            return "Sorry, the number of LSCoins is either below or above the limits.";
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
        $statement->bindParam('coins',$coins,PDO::PARAM_INT);
        $statement->bindParam('createdAt',$date,PDO::PARAM_STR);
        $statement->bindParam('updatedAt',$date,PDO::PARAM_STR);
        $statement->execute();
    }



}