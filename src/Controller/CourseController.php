<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\CourseRating;
use App\Entity\PurchasedCourse;
use App\Form\CourseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

class CourseController extends AbstractController
{
    #[Route('/', name: 'course_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $courses = $em->getRepository(Course::class)->findAll();
        $user = $this->getUser();
        $role = $user ? $user->getRoles() : [];
        $isAdmin = in_array('admin', $role);
        // Prepare courses data with base64-encoded images
        $courseData = array_map(function($course) use ($em) {
            $ratings = $em->getRepository(CourseRating::class)->findBy(['course' => $course]);
            $averageRating = count($ratings) > 0 ? array_sum(array_map(function($rating) {
                return $rating->getRating();
            }, $ratings)) / count($ratings) : null;
            // Convert BLOB image to base64 if it exists
            $image = $course->getImage();
            if (is_resource($image)) {
                $image = stream_get_contents($image);
            }
            
            // Encode to base64 for display in Twig
            return [
                'id' => $course->getId(),
                'courseName' => $course->getCourseName(),
                'description' => $course->getDescription(),
                'image' => $image ? base64_encode($image) : null,
                'price' => $course->getPrice(),
                'averageRating' => $averageRating,
            ];
        }, $courses);
    
        return $this->render('course/index.html.twig', [
            'courses' => $courseData,
            'isAdmin' => $isAdmin,
        ]);
    }

    #[Route('/add-course', name: 'admin_add_course')]
    public function addCourse(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $user = $this->getUser();
        $role = $user ? $user->getRoles() : [];
        $isAdmin = in_array('admin', $role);

        if (!$isAdmin) {
            throw $this->createAccessDeniedException();
        }

        $course = new Course();
        $form = $this->createForm(CourseType::class, $course);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            if ($file) {
                $imageData = file_get_contents($file->getPathname());
                $course->setImage($imageData);
            }

            $entityManagerInterface->persist($course);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('course_index');
        }

        return $this->render('course/addCourse.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/course/{id}', name: 'course_details', requirements: ['id' => '\d+'])]
    public function courseDetails(int $id, EntityManagerInterface $em): Response
    {   
        $user = $this->getUser();
        $course = $em->getRepository(Course::class)->find($id);

        if (!$course) {
            throw $this->createNotFoundException('Course not found');
        }

        $hasPurchased = false;
        $averageRating = null;

        if ($user) {
            $existingPurchase = $em->getRepository(PurchasedCourse::class)->findOneBy([
                'user' => $user,
                'course' => $course,
            ]);

            $hasPurchased = $existingPurchase ? true : false;
        }

        $courseRatings = $em->getRepository(CourseRating::class)->findBy(['course' => $course]);

        if ($courseRatings) {
            $ratings = array_map(function($rating) {
                return $rating->getRating();
            }, $courseRatings);

            $averageRating = array_sum($ratings) / count($ratings);
        }

        // Convert BLOB image to base64 if it exists
        $image = $course->getImage();
        if (is_resource($image)) {
            $image = stream_get_contents($image);
        }

        // Encode to base64 for display in Twig
        $courseData = [
            'id' => $course->getId(),
            'courseName' => $course->getCourseName(),
            'description' => $course->getDescription(),
            'image' => $image ? base64_encode($image) : null,
            'price' => $course->getPrice(),
            'averageRating' => $averageRating,
        ];

        return $this->render('course/courseDetails.html.twig', [
            'course' => $courseData,
            'hasPurchased' => $hasPurchased,
        ]);
    }

    #[Route('/purchase/{id}', name: 'purchase_course', methods: ['POST'])]
    public function purchaseCourse(int $id, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $course = $em->getRepository(Course::class)->find($id);
        if (!$course) {
            throw $this->createNotFoundException('Course not found');
        }

        $existingPurchase = $em->getRepository(PurchasedCourse::class)->findOneBy([
            'user' => $user,
            'course' => $course,
        ]);

        if ($existingPurchase) {
            $this->addFlash('error', 'You have already purchased this course!');
            return $this->redirectToRoute('course_details', ['id' => $id]);
        }

        $purchasedCourse = new PurchasedCourse();
        $purchasedCourse->setUser($user);
        $purchasedCourse->setCourse($course);
        $purchasedCourse->setPurchaseDate(new \DateTime());

        $em->persist($purchasedCourse);
        $em->flush();

        $this->addFlash('success', 'Course purchased successfully!');

        return $this->redirectToRoute('course_details', ['id' => $id]);
    }

    #[Route('/learn-course/{id}', name: 'learn_course')]
    public function learnCourse(int $id, EntityManagerInterface $em): Response
    {
        $course = $em->getRepository(Course::class)->find($id);

        if (!$course) {
            throw $this->createNotFoundException('Course not found');
        }

        // Convert BLOB image to base64 if it exists
        $image = $course->getImage();
        if (is_resource($image)) {
            $image = stream_get_contents($image);
        }

        $courseData = [
            'id' => $course->getId(),
            'courseName' => $course->getCourseName(),
            'description' => $course->getDescription(),
            'text' => $course->getText(),
            'image' => $image ? base64_encode($image) : null,
        ];

        return $this->render('course/learnCourse.html.twig', [
            'course' => $courseData,
        ]);
    }

    #[Route('/category/{category}', name: 'course_category')]
    public function category(string $category, EntityManagerInterface $em): Response
    {
        $courses = $em->getRepository(Course::class)->findBy(['category' => $category]);
        $user = $this->getUser();
        $role = $user ? $user->getRoles() : [];
        $isAdmin = in_array('ROLE_ADMIN', $role);

        // Prepare courses data with base64-encoded images
        $courseData = array_map(function($course) use ($em) {
            // Convert BLOB image to base64 if it exists
            $image = $course->getImage();
            if (is_resource($image)) {
                $image = stream_get_contents($image);
            }

            // Calculate average rating
            $courseRatings = $em->getRepository(CourseRating::class)->findBy(['course' => $course]);
            $averageRating = null;
            if ($courseRatings) {
                $ratings = array_map(function($rating) {
                    return $rating->getRating();
                }, $courseRatings);
                $averageRating = array_sum($ratings) / count($ratings);
            }

            // Encode to base64 for display in Twig
            return [
                'id' => $course->getId(),
                'courseName' => $course->getCourseName(),
                'description' => $course->getDescription(),
                'image' => $image ? base64_encode($image) : null,
                'averageRating' => $averageRating,
            ];
        }, $courses);

        return $this->render('course/index.html.twig', [
            'courses' => $courseData,
            'isAdmin' => $isAdmin,
        ]);
    }



    #[Route("/course/rate", name:"course_rate", methods: ["POST"], requirements: ["id" => "\d+"])]

    public function rateCourse(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $csrfToken = $request->request->get('_csrf_token');
        if (!$this->isCsrfTokenValid('rate_course', $csrfToken)) {
            throw new InvalidCsrfTokenException('Invalid CSRF token.');
    }

        $courseId = (int) $request->request->get('courseId');
        $ratingValue = (int) $request->request->get('rating');

        // Log the received data
        error_log("Received courseId: $courseId, rating: $ratingValue");

        // Find the course by ID
        $course = $entityManager->getRepository(Course::class)->find($courseId);

        $user = $this->getUser();
        $userId = $user ? $user->getId() : null;

        $existingPurchase = $entityManager->getRepository(PurchasedCourse::class)->findOneBy([
            'user' => $user,
            'course' => $course,
        ]);

        if (!$existingPurchase) {
            return new JsonResponse(['status' => 'error', 'message' => 'You must purchase the course to rate it'], 403);
        }

        $existingRating = $entityManager->getRepository(CourseRating::class)->findOneBy([
            'user' => $user,
            'course' => $course,
        ]);

        try{
            if ($existingRating) {
                // Update the existing rating
                $existingRating->setRating($ratingValue);
                $entityManager->flush();
                return new JsonResponse(['status' => 'success', 'message' => 'Rating updated']);
            } else {
            // Create a new CourseRating entity
            $rating = new CourseRating();
            $rating->setRating((int)$ratingValue);
            $rating->setUser($user);
            $rating->setCourse($course);

            // Save the rating
            $entityManager->persist($rating);
            $entityManager->flush();

            return new JsonResponse(['status' => 'success', 'message' => 'Rating saved']);
        }
        } catch (\Exception $e) {
            // Log the exception
            error_log("Error saving rating: " . $e->getMessage());

            return new JsonResponse(['status' => 'error', 'message' => 'An error occurred while saving the rating'], 500);
        }
    }
}
