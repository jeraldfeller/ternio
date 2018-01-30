<?php
/**
 * Created by PhpStorm.
 * User: Grabe Grabe
 * Date: 1/29/2018
 * Time: 11:54 PM
 */

namespace AppBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Tools\Pagination\Paginator;

use AppBundle\Entity\Users;
use AppBundle\Entity\UserKeys;
use Symfony\Component\HttpFoundation\Response;

class UsersController extends Controller
{
    /**
     * @Route("/login")
     */
    public function renderLoginForm()
    {
        return $this->render(
            '/user/login.html.twig'
        );
    }

    /**
     * @Route("/register")
     */
    public function registerPage()
    {
        return $this->render(
            '/user/register.html.twig'
        );
    }

    /**
     * @Route("/user/register-account")
     */
    public function registerAccountFunction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($_POST['param'], true);

        // check duplicate email

        $isExist = $em->getRepository('AppBundle:Users')->findOneBy(array('email' => $data['email']));
        if(!$isExist){
            $entity = new Users();
            $entity->setFirstName($data['firstName']);
            $entity->setLastName($data['lastName']);
            $entity->setEmail($data['email']);
            $entity->setPassword($data['password']);
            $entity->setHasShared(false);
            $entity->setConfirmed(false);
            $entity->setPoints(0);
            $entity->setConfirmationToken($this->generateRandomString());
            // generate Private/Public keys
            // check if key exists
            $publicKeyExists = true;
            while($publicKeyExists == true){
                $publicKey = $this->generateRandomString(8);
                $keyEntity = $em->getRepository('AppBundle:Users')->findOneBy(array('publicKey' => $publicKey));
                if(!$keyEntity) {
                    $publicKeyExists = false;
                    $entity->setPublicKey($publicKey);
                }
            }
            $em->persist($entity);
            $em->flush();
            $userId = $entity->getId();
            $token = $entity->getConfirmationToken();


            $privateKeyCount = 1;
            while($privateKeyCount <= 3){
                $privateKey = $this->generateRandomString(8);
                $keyEntity = $em->getRepository('AppBundle:UserKeys')->findOneBy(array('type' => 'private', 'userKey' => $privateKey));
                if(!$keyEntity) {
                    $keyEntity = new UserKeys();
                    $keyEntity->setUser($entity);
                    $keyEntity->setType('private');
                    $keyEntity->setUserKey($privateKey);
                    $em->persist($keyEntity);
                    $privateKeyCount++;
                }
            }

            $em->flush();

            // Send email confirmation
            $from = 'jeraldfeller@gmail.com';
            $subject = 'Email Confirmation';
            $message = 'Please click the link to complete the registration. <a href="'.DOMAIN.'/user/confirm-registration?userId='.$userId.'&token='.$token.'">CLICK HERE</a>';
            //$this->sendEmail($from, $data['email'], $subject, $message);

            $success = true;
            $response = array(
                'message' => 'THANK YOU FOR REGISTERING. <br> WE JUST SENT YOU AN EMAIL TO CONFIRM YOUR ACCOUNT. <br><br> PLEASE CHECK YOUR EMAIL BOX NOW'
            );
        }else{
            $success = false;
            $response = array(
                'message' => 'Email address already exists.'
            );
        }

        return new Response(
          json_encode(
              array(
                  'success' => $success,
                  'response' => $response
              )
          )
        );

    }

    /**
     * @Route("/user/confirm-registration")
     */

    public function confirmRegistrationFunction()
    {
        if(isset($_GET['userId']) && isset($_GET['token'])){
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Users')->findOneBy(array('id' => $_GET['userId'], 'confirmationToken' => $_GET['token']));
            if($entity){
                $entity->setConfirmed(true);
                $em->flush();
                return $this->redirect('/login');
            }else{
                return $this->redirect('/error');
            }
        }else{
            return $this->redirect('/error');
        }
    }

    /**
     * @Route("/user/login")
     */

    public function userLoginFunction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($_POST['param'], true);

        // check if credential match

        $entity = $em->getRepository('AppBundle:Users')->findOneBy(array('email' => $data['email'], 'password' => $data['password']));
        if($entity){
            if($entity->getConfirmed() == true){
                $keyEntity = $em->getRepository('AppBundle:UserKeys')->findBy(array('user' => $entity));
                if($keyEntity){
                    $privateKeys = array();
                    for($x = 0; $x < count($keyEntity); $x++){
                        if($keyEntity[$x]->getType() == 'private'){
                            $privateKeys[] = $keyEntity[$x]->getUserKey();
                        }
                    }
                    $return = array('success' => true,
                        'id' => $entity->getId(),
                        'firstName' => $entity->getFirstName(),
                        'lastName' => $entity->getLastName(),
                        'email' => $entity->getEmail(),
                        'password' => $entity->getPassword(),
                        'hasShared' => $entity->getHasShared(),
                        'points' => $entity->getPoints(),
                        'keys' => array(
                            'public' => $entity->getPublicKey(),
                            'private' => $privateKeys
                        ),
                        'userLevel' => $entity->getUserLevel()
                    );
                    $this->get('session')->set('isLoggedIn', true);
                    $this->get('session')->set('userData', $return);

                    $success = true;
                    $response = array($return);
                }else{
                    $success = false;
                    $response = array(
                        'message' => 'Email address is not yet confirmed, please check your email box.'
                    );
                }


            }else{
                $success = false;
                $response = array(
                    'message' => 'Email address is not yet confirmed, please check your email box.'
                );
            }
        }else{
            $success = false;
            $response = array(
              'message' => 'Incorrect email or password.'
            );
        }

        return new Response(
            json_encode(
                array(
                    'success' => $success,
                    'response' => $response
                )
            )
        );
    }


    /**
     * @Route("/user/get-info")
     */
    public function getInfoAction(){
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($_POST['param'], true);

        $entity = $em->getRepository('AppBundle:Users')->find($data['id']);
        $info = array();
        if($entity){
            $firstName = $entity->getFirstName();
            $lastName = $entity->getLastName();
            $email = $entity->getEmail();
            $points = $entity->getPoints();
            $publicKey = $entity->getPublicKey();
            $keyEntity = $em->getRepository('AppBundle:UserKeys')->findBy(array('user' => $entity));
            $privateKeys = array();
            if($keyEntity){
                for($x = 0; $x < count($keyEntity); $x++){
                    $privateKeys[] = array(
                        'id' => $keyEntity[$x]->getId(),
                        'key' => $keyEntity[$x]->getUserKey()
                    );
                }
            }

            $info = array(
                'userId' => $data['id'],
                'points' => $points,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email,
                'keys' => array(
                    'public' => $publicKey,
                    'private' => $privateKeys
                )
            );
            $success = true;
        }else{
            $success = false;
        }

        return new Response(
            json_encode(
                array(
                    'success' => $success,
                    'info' => $info
                )
            )
        );
    }

    /**
     * @Route("/user/edit-points")
     */
    public function editPointsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($_POST['param'], true);
        $info = array();
        $entity = $em->getRepository('AppBundle:Users')->find($data['id']);
        if($entity){
            if($data['action'] == 'add'){
                $entity->setPoints($entity->getPoints() + $data['points']);
            }else{
                $entity->setPoints($entity->getPoints() - $data['points']);
            }
            $em->flush();
            $info = array(
              'points' => $entity->getPoints()
            );
            $success = true;
        }else{
            $success = false;
        }

        return new Response(
            json_encode(
                array(
                    'success' => $success,
                    'info' => $info
                )
            )
        );
    }
    /**
     * @Route("/user/delete-key")
     */
    public function deleteKeyAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($_POST['param'], true);
        $entity = $em->getRepository('AppBundle:UserKeys')->find($data['id']);
        if($entity){
            $em->remove($entity);
            $em->flush();
            $success = true;
        }else{
            $success = false;
        }

        return new Response(
            json_encode($success)
        );
    }


    /**
     * @Route("/user/logout")
     */
    public function logout(){
        $this->get('session')->clear();
        return $this->redirectToRoute('homepage', array(), 301);
    }

    public function sendEmail($from, $to, $subject, $message){

        // To send HTML mail, the Content-type header must be set
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

        // Additional headers
        $headers .= 'From: <' . $from . '>' . "\r\n";


        // Mail it
        mail($to, $subject, $message, $headers);



    }

    public function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}