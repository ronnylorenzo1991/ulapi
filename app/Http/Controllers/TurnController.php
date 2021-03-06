<?php

namespace App\Http\Controllers;

use App\Models\Turns\Entity\Turn;
use App\Models\Turns\Repository\TurnRepository;
use Illuminate\Http\Request;

class TurnController extends Controller
{
    private $turnRepository;

    public function __construct(TurnRepository $turnRepository)
    {
        $this->turnRepository = $turnRepository;
    }

    public function getAll(Request $request)
    {
        $sortData = $request->get('sort') ? preg_split("/[\s|]+/", $request->get('sort')) : [];

        $sortBy = 'id';
        $sortDir = 'desc';

        if (!empty($sortData)) {
            $sortBy = $sortData[0];
            $sortDir = $sortData[1];
        }

        $sortBy =  $sortBy === 'id' ? 'turns.id' : $sortBy;

        $perPage = (int)$request->get('per_page');
        $page = (int)$request->get('page');

        $turns = $this->turnRepository->getAll($sortBy, $sortDir, $perPage, $page, ['status', 'user']);

        return json_encode($turns);
    }

    public function get($id)
    {
        try {
            $turn = $this->turnRepository->findOrFail($id);

            return json_encode([
                'turn' => $turn
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hubo un problema al buscar los datos'
            ], 400);
        }
    }

    public function store(Request $request)
    {
        $data = [
            'number'    => $this->turnRepository->getTurnNumber(),
            'status_id' => $request->get('status') ?? 1,
            'date'      => $request->get('date'),
            'time'      => $request->get('time'),
            'user_id'   => $request->get('user_id'),
        ];

        try {
            $this->turnRepository->store($data);

            return json_encode([
                'message' => 'El turno ha sido añadido correctamente',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hubo un problema al guardar los datos'
            ], 400);
        }
    }

    public function update($id, Request $request)
    {
        $data = [
            'date'      => $request->get('date'),
            'time'      => $request->get('time'),
            'user_id'   => $request->get('user_id'),
            'status_id' => $request->get('status_id', 1),
            'payment'   => $request->get('payment'),
        ];

        try {
            $this->turnRepository->update($data, $id);

            return json_encode([
                'message' => 'El turno ha sido actualizado correctamente',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hubo un problema al guardar los datos'
            ], 400);
        }
    }

    public function delete($id)
    {
        try {
            $this->turnRepository->delete($id);

            return json_encode([
                'message' => 'Turno eliminado satisfactoriamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hubo un problema al guardar los datos'
            ], 400);
        }
    }

    public function getTotalByStatus() {
        $pending = Turn::where('status_id', 1)->get()->count();
        $confirmed = Turn::where('status_id', 2)->get()->count();
        $pay = Turn::where('status_id', 3)->get()->count();

        return json_encode([
            $pending, $confirmed, $pay
        ]);
    }

    public function getIncomeExpenses(Request $request)
    {
        $filterParams = [
            'startDate' => $request->get('startDate'),
            'endDate' => $request->get('endDate'),
        ];

        [$totals, $labels] = $this->turnRepository->getIncomeExpenses($filterParams);

        return json_encode(['totals' => $totals, 'labels' => $labels]);
    }

    public function getTurnsByMonth(Request $request)
    {
        $filterParams = [
            'startDate' => $request->get('startDate'),
            'endDate' => $request->get('endDate'),
        ];

        [$totals, $labels] = $this->turnRepository->getTurnsByMonth($filterParams);

        return json_encode(['totals' => $totals, 'labels' => $labels]);
    }
}
