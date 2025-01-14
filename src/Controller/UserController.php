<?php
// src/Controller/UserController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\PurchasedCourse;

class UserController extends AbstractController
{
    #[Route('/purchased-courses', name: 'user_purchased_courses')]
    public function purchasedCourses(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $purchasedCourses = $em->getRepository(PurchasedCourse::class)->findBy(['user' => $user]);

        // Prepare purchased courses data with base64-encoded images
        $purchasedCoursesData = array_map(function($purchasedCourse) {
            $course = $purchasedCourse->getCourse();
            $image = $course->getImage();
            if (is_resource($image)) {
                $image = stream_get_contents($image);
            }

            return [
                'courseName' => $course->getCourseName(),
                'description' => $course->getDescription(),
                'image' => $image ? base64_encode($image) : null,
                'purchaseDate' => $purchasedCourse->getPurchaseDate(),
                'course' => $course,
            ];
        }, $purchasedCourses);

        return $this->render('course/purchasedCourses.html.twig', [
            'purchasedCourses' => $purchasedCoursesData,
        ]);
    }
}