<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Entities\Car;
use App\Domain\Services\CarService;
use App\Domain\ValueObjects\CarData;
use App\Domain\ValueObjects\Credentials;
use App\Exceptions\CarNotFoundException;
use App\Http\Requests\StoreCarRequest;
use App\Http\Requests\UpdateCarRequest;
use App\Http\Responses\ArrayResponse;
use App\Http\Responses\MessageResponse;
use App\Http\Responses\ServerErrorResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CarController extends Controller
{
    public function __construct(private CarService $carService)
    {
    }

    public function index(): Response
    {
        try {
            $output = $this->carService->getCars();
            
            $response = new ArrayResponse($output->getOutput());

            return $response->getResponse();
        } catch (\Throwable $t) {
            $response = new ServerErrorResponse(
                $t->getFile(),
                $t->getLine(),
                $t->getTraceAsString(),
                $t->getMessage()
            );

            return $response->getResponse();
        }
    }

    public function store(StoreCarRequest $request): Response
    {
        try {
            $output = $this->carService->createCar(new CarData(
                $request->input('brand'),
                $request->input('model'),
                $request->input('age'),
                $request->input('price')
            ));
                
            $response = new ArrayResponse($output->getOutput(), Response::HTTP_CREATED);

            return $response->getResponse();
        } catch (\Throwable $t) {
            $response = new ServerErrorResponse(
                $t->getFile(),
                $t->getLine(),
                $t->getTraceAsString(),
                $t->getMessage()
            );

            return $response->getResponse();
        }
    }

    public function update(UpdateCarRequest $request): Response
    {
        try {
            $output = $this->carService->updateCar(new Car(
                $request->input('id'),
                $request->input('brand'),
                $request->input('model'),
                $request->input('age'),
                $request->input('price'),
                null, 
                null                
            ));
                
            $response = new ArrayResponse($output->getOutput());

            return $response->getResponse();
        } catch (CarNotFoundException $exception) {
            $response = new MessageResponse($exception->getMessage(), Response::HTTP_NOT_FOUND);

            return $response->getResponse();
        } catch (\Throwable $t) {
            $response = new ServerErrorResponse(
                $t->getFile(),
                $t->getLine(),
                $t->getTraceAsString(),
                $t->getMessage()
            );

            return $response->getResponse();
        }
    }

    public function show(Request $request): Response
    {
        try {
            $output = $this->carService->getCarById(
                (int) $request->route('id')
            );
            
            $response = new ArrayResponse($output->getOutput());

            return $response->getResponse();
        } catch (CarNotFoundException $exception) {
            $response = new MessageResponse($exception->getMessage(), Response::HTTP_NOT_FOUND);

            return $response->getResponse();
        } catch (\Throwable $t) {
            $response = new ServerErrorResponse(
                $t->getFile(),
                $t->getLine(),
                $t->getTraceAsString(),
                $t->getMessage()
            );

            return $response->getResponse();
        }
    }

    public function destroy(Request $request): Response
    {
        try {
            $output = $this->carService->deleteCar(
                (int) $request->route('id')
            );
            
            $response = new ArrayResponse($output->getOutput());

            return $response->getResponse();
        } catch (CarNotFoundException $exception) {
            $response = new MessageResponse($exception->getMessage(), Response::HTTP_NOT_FOUND);

            return $response->getResponse();
        } catch (\Throwable $t) {
            $response = new ServerErrorResponse(
                $t->getFile(),
                $t->getLine(),
                $t->getTraceAsString(),
                $t->getMessage()
            );

            return $response->getResponse();
        }
    }
}
