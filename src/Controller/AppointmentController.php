<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Repository\AppointmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppointmentController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(): Response
    {
        return $this->render('appointment/index.html.twig', [
            'controller_name' => 'AppointmentController',
        ]);
    }

    /**
     * @Route("/appointment", name="appointment")
     */
    public function appointment()
    {
        return $this->render('appointment/appointment.html.twig', [
            'controller_name' => 'AppointmentController',
        ]);
    }

    /**
     * @Route("/updateAppt", name="updateAppt")
     */
    public function updateAppt(Request $request = null, AppointmentRepository $appointmentRepository)
    {
        $data = $_POST ?? null;

        $newAppt = new Appointment();
        $newAppt->setFirstName($data['first_name']);
        $newAppt->setLastName($data['last_name']);
        $newAppt->setEmail($data['email']);
        $newAppt->setPhone($data['phone']);
        $newAppt->setMessage($data['message']);
        $newAppt->setDate($data['dp']);
        $newAppt->setTime($data['chosenTime']);

        $appointmentRepository->add($newAppt);


        return $this->render('appointment/appointment.html.twig', [
        ]);
    }

    /**
     * @Route("/", name="getAppointmentData")
     */
    public function getAppointmentData(Request $request)
    {
        $queryBuilder = $this->getDoctrine()->getManager()->createQueryBuilder();
        $appDate = $_POST['date'];

        $queryBuilder->select('a')
            ->from(Appointment::class, 'a')
            ->where('a.date = :appDate')
            ->setParameter('appDate',$appDate );


        $appointments = $queryBuilder->getQuery()->getResult();

        $appointmentData = array();

        foreach ($appointments as $appointment) {
            $date = $appointment->getDate();
            $time = $appointment->getTime();
            $appointmentData[] = array('date' => $date, 'time' => $time);
        }

        $response = new JsonResponse($appointmentData);

        return $response;
    }

    /**
     * @Route("/admin/reports", name="getAllAppointmentsData")
     */
    public function getAllAppointmentsData(Request $request)
    {
        $queryBuilder = $this->getDoctrine()->getManager()->createQueryBuilder();

        $queryBuilder->select('a')
            ->from(Appointment::class, 'a')
            ->andWhere('a.date IS NOT NULL')
            ->orderBy('a.date', 'ASC');

        $appointments = $queryBuilder->getQuery()->getResult();

        $apptData = array();

        foreach ($appointments as $appointment) {
            $firstName = $appointment->getFirstName();
            $lastname = $appointment->getLastName();
            $email = $appointment->getEmail();
            $phone = $appointment->getPhone();
            $message = $appointment->getMessage();
            $date = $appointment->getDate();
            $time = $appointment->getTime();
            $apptData[] = array(
                'first_name' => $firstName,
                'last_name' => $lastname,
                'email' => $email,
                'phone' => $phone,
                'message' => $message,
                'date' => $date,
                'time' => $time
            );
        }
        // Convert the data to a JSON response
        $response = new JsonResponse($apptData);

        return $response;
    }

    /**
     * @Route("/admin/reports/filter_results", name="filter_results")
     */
    public function filterResults(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        // Set the base query
        $queryBuilder
            ->select('a')
            ->from(Appointment::class, 'a');

        if (isset($_POST['filter1'])){
            $filter1Values = $_POST['filter1'];

            if (!empty($filter1Values)) {
                $queryBuilder
                    ->andWhere('a.date IN (:filter1Values)')
                    ->setParameter('filter1Values', $filter1Values)
                ->orderBy('a.date', 'ASC');
            }
        };

        if (isset($_POST['filter2'])){
            $filter2Values = $_POST['filter2'];

            if (!empty($filter2Values)) {
                $queryBuilder
                    ->andWhere('a.time IN (:filter2Values)')
                    ->setParameter('filter2Values', $filter2Values)
                    ->orderBy('a.time', 'ASC');
            }
        };

        if (isset($_POST['filter3'])){
            $filter3Values = $_POST['filter3'];

            if (!empty($filter3Values)) {
                $queryBuilder
                    ->andWhere('a.first_name IN (:filter3Values)')
                    ->setParameter('filter3Values', $filter3Values)
                    ->orderBy('a.first_name', 'ASC');
            }
        };

        if (isset($_POST['filter4'])){
            $filter4Values = $_POST['filter4'];

            if (!empty($filter4Values)) {
                $queryBuilder
                    ->andWhere('a.last_name IN (:filter4Values)')
                    ->setParameter('filter4Values', $filter4Values)
                    ->orderBy('a.last_name', 'ASC');
            }
        };

        if (isset($_POST['filter5'])){
            $filter5Values = $_POST['filter5'];

            if (!empty($filter5Values)) {
                $queryBuilder
                    ->andWhere('a.email IN (:filter5Values)')
                    ->setParameter('filter5Values', $filter5Values)
                    ->orderBy('a.email', 'ASC');
            }
        };

        $results = $queryBuilder->getQuery()->getResult();


        $filterResults = array();

        foreach ($results as $filterData) {
            $firstName = $filterData->getFirstName();
            $lastname = $filterData->getLastName();
            $email = $filterData->getEmail();
            $phone = $filterData->getPhone();
            $message = $filterData->getMessage();
            $date = $filterData->getDate();
            $time = $filterData->getTime();
            $filterResults[] = array(
                'first_name' => $firstName,
                'last_name' => $lastname,
                'email' => $email,
                'phone' => $phone,
                'message' => $message,
                'date' => $date,
                'time' => $time
            );
        }

        $response = new JsonResponse($filterResults);

        return $response;
    }

}
